<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location:index.php");
    exit();
}

$nombre = $_SESSION['nombre'];
$fotoPerfil = $_SESSION['foto_perfil'];
$rutaFotoPerfil = "fotos/" . $fotoPerfil;
$rutaDefault = "recursos/img/default-avatar.png";
$rutaFotoPerfil = (!empty($fotoPerfil) && file_exists("fotos/" . $fotoPerfil))
    ? "fotos/" . $fotoPerfil
    : $rutaDefault;

// Configuración de base de datos PostgreSQL
$host = "dpg-d3cp1eumcj7s73dpm8sg-a.oregon-postgres.render.com"; 
$port = "5432"; 
$dbname = "db_finanzas_fxs9"; 
$user = "db_finanzas_fxs9_user"; 
$password = "MzArnjJx2t87VeEF1Cr03C35Qv3M49CU"; 

// Función para conectar a PostgreSQL usando PDO
function conectarPostgreSQL($host, $port, $dbname, $user, $password) {
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
        $connection = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        return $connection;
    } catch (PDOException $e) {
        error_log("Error de conexión PDO: " . $e->getMessage());
        return false;
    }
}

// Crear una única conexión PDO
$conexion_pdo = conectarPostgreSQL($host, $port, $dbname, $user, $password);

// Incluir y usar el sistema de logros
require_once 'modelo/logros.php';
if ($conexion_pdo) {
    $sistemaLogros = new SistemaLogros($conexion_pdo);
    
    // Verificar logros cada vez que se carga la página
    $sistemaLogros->verificarLogros($_SESSION['id_usuario']);
    
    // Obtener logros del usuario
    $logros_usuario = $sistemaLogros->getLogrosUsuario($_SESSION['id_usuario'], 5);
    
    // Marcar logros como vistos después de mostrarlos
    $sistemaLogros->marcarLogrosComoVistos($_SESSION['id_usuario']);
} else {
    $logros_usuario = [];
}

// Sistema de Alertas Inteligentes
require_once 'modelo/alertas.php';
if ($conexion_pdo) {
    $sistemaAlertas = new AlertasInteligentes($conexion_pdo);
    $alertas = $sistemaAlertas->generarAlertas($_SESSION['id_usuario']);
} else {
    $alertas = [];
}

// Sistema de Análisis de Hábitos
require_once 'modelo/habitos.php';
if ($conexion_pdo) {
    $analisisHabitos = new AnalisisHabitos($conexion_pdo);
    $habitos_semana = $analisisHabitos->getHabitosSemana($_SESSION['id_usuario']);
    $analisis_habitos = $analisisHabitos->getAnalisisHabitos($_SESSION['id_usuario']);
    $resumen_habitos = $analisisHabitos->getResumenHabitos($_SESSION['id_usuario']);
} else {
    $habitos_semana = [];
    $analisis_habitos = [];
    $resumen_habitos = [];
}

// Procesar formularios de metas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if ($conexion_pdo) {
        $id_usuario = $_SESSION['id_usuario'];
        
        // Crear nueva meta
        if (isset($_POST['crear_meta'])) {
            $nombre_meta = $_POST['nombre_meta'];
            $descripcion = $_POST['descripcion'];
            $meta_total = floatval($_POST['meta_total']);
            $icono = $_POST['icono'];
            $fecha_objetivo = !empty($_POST['fecha_objetivo']) ? $_POST['fecha_objetivo'] : null;
            
            $sql = "INSERT INTO metas (id_usuario, nombre_meta, descripcion, meta_total, icono, fecha_objetivo) 
                    VALUES (:id_usuario, :nombre_meta, :descripcion, :meta_total, :icono, :fecha_objetivo)";
            
            $stmt = $conexion_pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':id_usuario' => $id_usuario,
                ':nombre_meta' => $nombre_meta,
                ':descripcion' => $descripcion,
                ':meta_total' => $meta_total,
                ':icono' => $icono,
                ':fecha_objetivo' => $fecha_objetivo
            ]);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Meta creada exitosamente";
            } else {
                $_SESSION['mensaje_error'] = "Error al crear la meta";
            }
        }
        
        // Agregar monto a meta
        if (isset($_POST['agregar_monto'])) {
            $id_meta = intval($_POST['id_meta']);
            $monto_agregar = floatval($_POST['monto_agregar']);
            
            $sql = "UPDATE metas SET monto_actual = monto_actual + :monto WHERE id_meta = :id_meta AND id_usuario = :id_usuario";
            $stmt = $conexion_pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':monto' => $monto_agregar,
                ':id_meta' => $id_meta,
                ':id_usuario' => $id_usuario
            ]);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Monto agregado exitosamente";
                
                // Verificar si la meta se completó
                $sql_check = "SELECT monto_actual, meta_total FROM metas WHERE id_meta = :id_meta";
                $stmt_check = $conexion_pdo->prepare($sql_check);
                $stmt_check->execute([':id_meta' => $id_meta]);
                $meta = $stmt_check->fetch();
                
                if ($meta && $meta['monto_actual'] >= $meta['meta_total']) {
                    $sql_complete = "UPDATE metas SET estado = 'completada' WHERE id_meta = :id_meta";
                    $stmt_complete = $conexion_pdo->prepare($sql_complete);
                    $stmt_complete->execute([':id_meta' => $id_meta]);
                    
                    // Crear logro por meta completada
                    $sql_logro = "INSERT INTO logros (id_usuario, tipo_logro, mensaje, icono) 
                                 VALUES (:id_usuario, 'meta_completada', '¡Felicidades! Completaste una meta de ahorro', '🎯')";
                    $stmt_logro = $conexion_pdo->prepare($sql_logro);
                    $stmt_logro->execute([':id_usuario' => $id_usuario]);
                }
            } else {
                $_SESSION['mensaje_error'] = "Error al agregar monto";
            }
        }
        
        // Editar meta
        if (isset($_POST['editar_meta'])) {
            $id_meta = intval($_POST['id_meta']);
            $nombre_meta = $_POST['nombre_meta'];
            $descripcion = $_POST['descripcion'];
            $meta_total = floatval($_POST['meta_total']);
            $icono = $_POST['icono'];
            $fecha_objetivo = !empty($_POST['fecha_objetivo']) ? $_POST['fecha_objetivo'] : null;
            
            $sql = "UPDATE metas SET nombre_meta = :nombre_meta, descripcion = :descripcion, 
                    meta_total = :meta_total, icono = :icono, fecha_objetivo = :fecha_objetivo 
                    WHERE id_meta = :id_meta AND id_usuario = :id_usuario";
            
            $stmt = $conexion_pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':nombre_meta' => $nombre_meta,
                ':descripcion' => $descripcion,
                ':meta_total' => $meta_total,
                ':icono' => $icono,
                ':fecha_objetivo' => $fecha_objetivo,
                ':id_meta' => $id_meta,
                ':id_usuario' => $id_usuario
            ]);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Meta actualizada exitosamente";
            } else {
                $_SESSION['mensaje_error'] = "Error al actualizar la meta";
            }
        }
        
        // Eliminar meta
        if (isset($_POST['eliminar_meta'])) {
            $id_meta = intval($_POST['id_meta']);
            
            $sql = "DELETE FROM metas WHERE id_meta = :id_meta AND id_usuario = :id_usuario";
            $stmt = $conexion_pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':id_meta' => $id_meta,
                ':id_usuario' => $id_usuario
            ]);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Meta eliminada exitosamente";
            } else {
                $_SESSION['mensaje_error'] = "Error al eliminar la meta";
            }
        }
        
        // Redirigir para evitar reenvío del formulario
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icono-ic.png" sizes="32x32" type="image/x-icon">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/inicio/inicio.css">
    <link rel="stylesheet" href="css/inicio/calculadora.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Inicio - ControlGastos</title>
</head>

<body>
    <!-- Barra de navegación lateral -->
    <nav class="sidebar">
        <div class="brand-logo">
            <img src="logo_Finabiz.png" alt="Finabiz Logo" class="brand-logo-img">
        </div>

        <div class="nav-links">
            <div class="nav-section">
                <div class="nav-title">Home</div>
                <a href="./inicio.php" class="nav-link active">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-title">Finanzas</div>
                <a href="./ingreso.php" class="nav-link">
                    <i class="fas fa-coins"></i>
                    <span>Ingresos</span>
                </a>
                <a href="./gasto.php" class="nav-link">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Gastos</span>
                </a>
                <a href="./balance.php" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Balance</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-title">Herramientas</div>
                <a href="calculadora.php" class="nav-link">
                    <i class="fas fa-calculator"></i>
                    <span>Calculadora</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-title">Otros</div>
                <a href="configuracion.php" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="main-content">
        <!-- Header -->
        <div class="header">
            <h1 class="page-title">Panel de Control</h1>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($nombre); ?></span>
                <div class="user-avatar">
                    <img src="<?php echo htmlspecialchars($rutaFotoPerfil); ?>" alt="Foto de perfil">
                </div>
                <a href="modelo/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <!-- Mensajes de éxito/error -->
        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 1rem 0;">
                <?php echo $_SESSION['mensaje_exito']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['mensaje_exito']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 1rem 0;">
                <?php echo $_SESSION['mensaje_error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['mensaje_error']); ?>
        <?php endif; ?>

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <i class="fas fa-hand-wave"></i>
            <div class="welcome-text">¡Bienvenido de nuevo! <span><?php echo htmlspecialchars($nombre); ?></span></div>
        </div>

        <!-- Sección de Gráfico - CORREGIDA -->
        <div class="chart-section" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">Resumen Financiero</h2>
                <div class="chart-controls" style="display: flex; gap: 0.5rem;">
                    <button class="chart-btn active" onclick="changeChartType('doughnut')" style="background: #4f46e5; color: white; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-chart-pie"></i>
                    </button>
                    <button class="chart-btn" onclick="changeChartType('bar')" style="background: #f3f4f6; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-chart-bar"></i>
                    </button>
                    <button class="chart-btn" onclick="changeChartType('line')" style="background: #f3f4f6; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-chart-line"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container" style="height: 400px; position: relative;">
                <canvas id="financialChart"></canvas>
            </div>
        </div>

        <!-- Sección de Alertas Inteligentes -->
        <?php if (!empty($alertas)): ?>
        <div class="alertas-section" style="margin-bottom: 2rem;">
            <div class="section-header">
                <h3 style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-bell"></i>
                    Alertas Inteligentes
                </h3>
            </div>
            
            <div class="alertas-container">
                <?php foreach($alertas as $alerta): ?>
                    <div class="alerta-item 
                        <?php 
                        switch($alerta['tipo']) {
                            case 'peligro': echo 'alerta-peligro'; break;
                            case 'advertencia': echo 'alerta-advertencia'; break;
                            case 'exito': echo 'alerta-exito'; break;
                            default: echo 'alerta-info';
                        }
                        ?>" 
                        style="display: flex; align-items: center; gap: 1rem; padding: 1rem; margin-bottom: 0.5rem; border-radius: 8px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        
                        <div class="alerta-icono" style="font-size: 1.5rem;">
                            <?php echo $alerta['icono']; ?>
                        </div>
                        
                        <div class="alerta-mensaje" style="flex: 1;">
                            <?php echo htmlspecialchars($alerta['mensaje']); ?>
                        </div>
                        
                        <button class="alerta-cerrar" onclick="this.parentElement.remove()" 
                                style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #6b7280;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Resto de tu contenido... -->
        <!-- Sección de Análisis de Hábitos -->
        <div class="habitos-section" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div class="section-header" style="margin-bottom: 1.5rem;">
                <h3 style="display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                    <i class="fas fa-chart-line"></i>
                    Análisis de Hábitos Semanales
                </h3>
                <small style="color: #6b7280;">Patrones y tendencias de tu comportamiento financiero</small>
            </div>
            
            <?php if (!empty($habitos_semana)): ?>
            <!-- Gráfico de hábitos semanales -->
            <div class="habitos-chart" style="margin-bottom: 2rem;">
                <h4 style="margin-bottom: 1rem; color: #374151;">📊 Gastos por Día de la Semana</h4>
                <div class="chart-bars" style="display: flex; align-items: end; gap: 0.5rem; height: 200px; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                    <?php foreach($habitos_semana as $dia => $datos): 
                        $max_gasto = max(array_column($habitos_semana, 'gastos'));
                        $altura = $max_gasto > 0 ? ($datos['gastos'] / $max_gasto) * 150 : 10;
                        $color = $datos['tendencia'] > 0 ? '#ef4444' : '#10b981';
                    ?>
                    <div class="bar-container" style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                        <div class="bar" style="width: 80%; background: <?php echo $color; ?>; height: <?php echo $altura; ?>px; border-radius: 4px 4px 0 0; position: relative;">
                            <div class="bar-value" style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); font-size: 0.75rem; font-weight: 600; color: #374151;">
                                S/<?php echo number_format($datos['gastos'], 0); ?>
                            </div>
                        </div>
                        <div class="bar-label" style="margin-top: 0.5rem; font-weight: 600; color: #374151;">
                            <?php echo $dia; ?>
                        </div>
                        <div class="bar-tendencia" style="font-size: 0.75rem; color: <?php echo $color; ?>;">
                            <?php if ($datos['tendencia'] != 0): ?>
                                <?php echo $datos['tendencia'] > 0 ? '↗' : '↘'; ?>
                                <?php echo number_format(abs($datos['tendencia']), 1); ?>%
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Análisis de hábitos -->
            <?php if (!empty($analisis_habitos)): ?>
            <div class="analisis-habitos" style="margin-bottom: 2rem;">
                <h4 style="margin-bottom: 1rem; color: #374151;">🔍 Patrones Detectados</h4>
                <div class="analisis-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                    <?php foreach($analisis_habitos as $analisis): ?>
                        <div class="analisis-item" style="padding: 1rem; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #3b82f6;">
                            <?php echo $analisis; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Resumen semanal -->
            <?php if (!empty($resumen_habitos)): ?>
            <div class="resumen-habitos">
                <h4 style="margin-bottom: 1rem; color: #374151;">📈 Resumen Semanal</h4>
                <div class="resumen-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="resumen-item" style="text-align: center; padding: 1rem; background: #f0fdf4; border-radius: 8px;">
                        <div style="font-size: 2rem; color: #10b981; margin-bottom: 0.5rem;">💰</div>
                        <div style="font-weight: 600; color: #374151;">Total Ingresos</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #10b981;">
                            S/<?php echo number_format($resumen_habitos['total_ingresos'], 2); ?>
                        </div>
                    </div>
                    
                    <div class="resumen-item" style="text-align: center; padding: 1rem; background: #fef2f2; border-radius: 8px;">
                        <div style="font-size: 2rem; color: #ef4444; margin-bottom: 0.5rem;">💸</div>
                        <div style="font-weight: 600; color: #374151;">Total Gastos</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #ef4444;">
                            S/<?php echo number_format($resumen_habitos['total_gastos'], 2); ?>
                        </div>
                    </div>
                    
                    <div class="resumen-item" style="text-align: center; padding: 1rem; background: #f0f9ff; border-radius: 8px;">
                        <div style="font-size: 2rem; color: #3b82f6; margin-bottom: 0.5rem;">⚖️</div>
                        <div style="font-weight: 600; color: #374151;">Balance Semanal</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #3b82f6;">
                            S/<?php echo number_format($resumen_habitos['balance_semanal'], 2); ?>
                        </div>
                    </div>
                    
                    <div class="resumen-item" style="text-align: center; padding: 1rem; background: #faf5ff; border-radius: 8px;">
                        <div style="font-size: 2rem; color: #8b5cf6; margin-bottom: 0.5rem;">📅</div>
                        <div style="font-weight: 600; color: #374151;">Días Activos</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #8b5cf6;">
                            <?php echo $resumen_habitos['dias_con_movimientos']; ?>/7
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
                <div style="text-align: center; padding: 2rem; color: #6b7280;">
                    <i class="fas fa-chart-line" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>No hay suficientes datos para el análisis de hábitos</p>
                    <p style="font-size: 0.875rem;">Registra ingresos y gastos para ver tus patrones</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sección de Logros y Metas -->
        <div class="goals-achievements-section" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            
            <!-- Logros -->
            <div class="achievements-card" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="display: flex; align-items: center; gap: 0.5rem; margin: 0;">🏆 Tus Logros</h3>
                </div>
                <div id="logros-container">
                    <?php
                    if (!empty($logros_usuario)) {
                        foreach($logros_usuario as $logro) {
                            $fecha = date('d/m/Y', strtotime($logro['fecha_obtenido']));
                            $esNuevo = !$logro['visto'];
                            $bgColor = $esNuevo ? '#fef3c7' : '#f3f4f6';
                            $borderColor = $esNuevo ? '#f59e0b' : '#4f46e5';
                            
                            echo "
                            <div style='background: {$bgColor}; padding: 1rem; border-radius: 8px; margin-bottom: 0.5rem; border-left: 4px solid {$borderColor};'>
                                <div style='font-weight: 600; display: flex; align-items: center; gap: 0.5rem;'>
                                    <span>{$logro['icono']}</span>
                                    <span>{$logro['mensaje']}</span>
                                </div>
                                <div style='font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;'>
                                    Obtenido: {$fecha}
                                </div>
                            </div>";
                        }
                    } else {
                        echo "
                        <div style='text-align: center; padding: 3rem 1rem; color: #9ca3af;'>
                            <i class='fas fa-trophy' style='font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;'></i>
                            <p>Comienza a usar la app<br>para desbloquear logros</p>
                        </div>";
                    }
                    ?>
                </div>
            </div>
            
            <!-- Metas -->
            <div class="goals-card" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="display: flex; align-items: center; gap: 0.5rem; margin: 0;">🎯 Tus Metas</h3>
                    <button class="add-btn" onclick="abrirModalMeta()" style="background: #4f46e5; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; transition: all 0.3s ease;">
                        <i class="fas fa-plus"></i> Nueva Meta
                    </button>
                </div>
                <div id="metas-container">
                    <?php
                    if ($conexion_pdo) {
                        $id_usuario = $_SESSION['id_usuario'];
                        $sql = "SELECT * FROM metas WHERE id_usuario = :id_usuario AND estado = 'activa' ORDER BY fecha_creacion DESC LIMIT 5";
                        $stmt = $conexion_pdo->prepare($sql);
                        $stmt->execute([':id_usuario' => $id_usuario]);
                        $metas = $stmt->fetchAll();
                        
                        if (!empty($metas)) {
                            foreach ($metas as $meta) {
                                $porcentaje = $meta['meta_total'] > 0 ? min(round(($meta['monto_actual'] / $meta['meta_total']) * 100), 100) : 0;
                                $monto_actual = number_format($meta['monto_actual'], 2);
                                $meta_total = number_format($meta['meta_total'], 2);
                                $restante = $meta['meta_total'] - $meta['monto_actual'];
                                $restante_formatted = number_format(max($restante, 0), 2);
                                
                                echo "
                                <div class='meta-item' style='margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 8px; transition: all 0.3s ease;'>
                                    <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;'>
                                        <span style='font-weight: 600; display: flex; align-items: center; gap: 0.5rem;'>
                                            " . htmlspecialchars($meta['icono']) . " " . htmlspecialchars($meta['nombre_meta']) . "
                                        </span>
                                        <div style='display: flex; gap: 0.5rem;'>
                                            <button class='meta-action-btn' onclick='abrirModalAgregarMonto({$meta['id_meta']})' title='Agregar monto' style='background: none; border: none; color: #10b981; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 4px;'>
                                                <i class='fas fa-plus-circle'></i>
                                            </button>
                                            <button class='meta-action-btn' onclick='abrirModalEditarMeta({$meta['id_meta']})' title='Editar' style='background: none; border: none; color: #3b82f6; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 4px;'>
                                                <i class='fas fa-edit'></i>
                                            </button>
                                            <button class='meta-action-btn' onclick='confirmarEliminarMeta({$meta['id_meta']})' title='Eliminar' style='background: none; border: none; color: #ef4444; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 4px;'>
                                                <i class='fas fa-trash'></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div style='font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;'>
                                        S/{$monto_actual} / S/{$meta_total} (Restante: S/{$restante_formatted})
                                    </div>
                                    <div style='background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;'>
                                        <div style='background: linear-gradient(90deg, #10b981, #34d399); height: 100%; width: {$porcentaje}%; transition: width 0.6s ease;'></div>
                                    </div>
                                    <div style='display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;'>
                                        <span style='font-size: 0.875rem; font-weight: 600; color: #10b981;'>{$porcentaje}%</span>
                                        " . ($meta['fecha_objetivo'] ? "<span style='font-size: 0.75rem; color: #9ca3af;'>Meta: " . date('d/m/Y', strtotime($meta['fecha_objetivo'])) . "</span>" : "") . "
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "
                            <div style='text-align: center; padding: 3rem 1rem; color: #9ca3af;'>
                                <i class='fas fa-bullseye' style='font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;'></i>
                                <p>No tienes metas aún<br>¡Crea tu primera meta!</p>
                            </div>";
                        }
                    } else {
                        echo "
                        <div style='text-align: center; padding: 2rem; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;'>
                            <i class='fas fa-exclamation-triangle' style='font-size: 2rem; color: #f59e0b; margin-bottom: 0.5rem;'></i>
                            <p style='color: #92400e; margin: 0; font-weight: 500;'>No se pudo conectar a la base de datos</p>
                        </div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="cards-container">
            <div class="card card-income">
                <div class="card-content">
                    <h3>Ingreso Total</h3>
                    <div class="amount">S/<?php include 'modelo/totalIngreso.php'; ?></div>
                    <div class="card-trend">
                        <span class="trend-up">
                            <i class="fas fa-arrow-up"></i>
                            +8.2%
                        </span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>

            <div class="card card-expense">
                <div class="card-content">
                    <h3>Gasto Total</h3>
                    <div class="amount">S/<?php include 'modelo/total.php'; ?></div>
                    <div class="card-trend">
                        <span class="trend-down">
                            <i class="fas fa-arrow-down"></i>
                            -3.5%
                        </span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
            </div>

            <div class="card card-budget">
                <div class="card-content">
                    <h3>Balance Actual</h3>
                    <div class="amount">S/<?php
                                            $ingresos = file_get_contents('modelo/totalIngreso.php');
                                            $gastos = file_get_contents('modelo/total.php');
                                            echo number_format(floatval($ingresos) - floatval($gastos), 2);
                                            ?></div>
                    <div class="card-trend">
                        <span class="trend-up">
                            <i class="fas fa-arrow-up"></i>
                            +12.7%
                        </span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            <div class="card card-savings">
                <div class="card-content">
                    <h3>Ahorros del Mes</h3>
                    <div class="amount">S/1,245.50</div>
                    <div class="card-trend">
                        <span class="trend-up">
                            <i class="fas fa-arrow-up"></i>
                            +15.3%
                        </span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
            </div>
        </div>

        <!-- Análisis de gastos -->
        <div class="expenses-section">
            <div class="section-header">
                <h3>Gastos por Categoría</h3>
            </div>
            <div class="expenses-grid">
                <div class="expense-item">
                    <div class="expense-icon food">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="expense-info">
                        <span class="expense-category">Alimentación</span>
                        <span class="expense-amount">S/425.80</span>
                        <div class="expense-bar">
                            <div class="expense-fill" style="width: 35%"></div>
                        </div>
                    </div>
                    <div class="expense-percentage">35%</div>
                </div>
                
                <div class="expense-item">
                    <div class="expense-icon transport">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="expense-info">
                        <span class="expense-category">Transporte</span>
                        <span class="expense-amount">S/180.50</span>
                        <div class="expense-bar">
                            <div class="expense-fill" style="width: 22%"></div>
                        </div>
                    </div>
                    <div class="expense-percentage">22%</div>
                </div>

                <div class="expense-item">
                    <div class="expense-icon entertainment">
                        <i class="fas fa-film"></i>
                    </div>
                    <div class="expense-info">
                        <span class="expense-category">Entretenimiento</span>
                        <span class="expense-amount">S/120.00</span>
                        <div class="expense-bar">
                            <div class="expense-fill" style="width: 15%"></div>
                        </div>
                    </div>
                    <div class="expense-percentage">15%</div>
                </div>

                <div class="expense-item">
                    <div class="expense-icon health">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="expense-info">
                        <span class="expense-category">Salud</span>
                        <span class="expense-amount">S/95.30</span>
                        <div class="expense-bar">
                            <div class="expense-fill" style="width: 12%"></div>
                        </div>
                    </div>
                    <div class="expense-percentage">12%</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Crear Meta -->
    <div class="modal fade" id="modalMeta" tabindex="-1" aria-labelledby="modalMetaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMetaLabel">Crear Nueva Meta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="crear_meta" value="1">
                        <div class="mb-3">
                            <label for="nombre_meta" class="form-label">Nombre de la Meta</label>
                            <input type="text" class="form-control" id="nombre_meta" name="nombre_meta" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="meta_total" class="form-label">Monto Total (S/)</label>
                            <input type="number" class="form-control" id="meta_total" name="meta_total" step="0.01" min="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="icono" class="form-label">Icono</label>
                            <select class="form-select" id="icono" name="icono">
                                <option value="🎯">🎯 Objetivo</option>
                                <option value="💰">💰 Dinero</option>
                                <option value="🏠">🏠 Casa</option>
                                <option value="🚗">🚗 Auto</option>
                                <option value="✈️">✈️ Viaje</option>
                                <option value="🎓">🎓 Educación</option>
                                <option value="💼">💼 Negocio</option>
                                <option value="❤️">❤️ Salud</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_objetivo" class="form-label">Fecha Objetivo (Opcional)</label>
                            <input type="date" class="form-control" id="fecha_objetivo" name="fecha_objetivo">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Meta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar Monto -->
    <div class="modal fade" id="modalAgregarMonto" tabindex="-1" aria-labelledby="modalAgregarMontoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarMontoLabel">Agregar Monto a Meta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="agregar_monto" value="1">
                        <input type="hidden" id="id_meta_monto" name="id_meta">
                        <div class="mb-3">
                            <label for="monto_agregar" class="form-label">Monto a Agregar (S/)</label>
                            <input type="number" class="form-control" id="monto_agregar" name="monto_agregar" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Agregar Monto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Meta -->
    <div class="modal fade" id="modalEditarMeta" tabindex="-1" aria-labelledby="modalEditarMetaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarMetaLabel">Editar Meta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="editar_meta" value="1">
                        <input type="hidden" id="id_meta_editar" name="id_meta">
                        <div class="mb-3">
                            <label for="nombre_meta_editar" class="form-label">Nombre de la Meta</label>
                            <input type="text" class="form-control" id="nombre_meta_editar" name="nombre_meta" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion_editar" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_editar" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="meta_total_editar" class="form-label">Monto Total (S/)</label>
                            <input type="number" class="form-control" id="meta_total_editar" name="meta_total" step="0.01" min="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="icono_editar" class="form-label">Icono</label>
                            <select class="form-select" id="icono_editar" name="icono">
                                <option value="🎯">🎯 Objetivo</option>
                                <option value="💰">💰 Dinero</option>
                                <option value="🏠">🏠 Casa</option>
                                <option value="🚗">🚗 Auto</option>
                                <option value="✈️">✈️ Viaje</option>
                                <option value="🎓">🎓 Educación</option>
                                <option value="💼">💼 Negocio</option>
                                <option value="❤️">❤️ Salud</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_objetivo_editar" class="form-label">Fecha Objetivo (Opcional)</label>
                            <input type="date" class="form-control" id="fecha_objetivo_editar" name="fecha_objetivo">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Meta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para eliminar meta -->
    <form id="formEliminarMeta" method="POST" action="" style="display: none;">
        <input type="hidden" name="eliminar_meta" value="1">
        <input type="hidden" id="id_meta_eliminar" name="id_meta">
    </form>

    <!-- ChatBot -->
    <div id="chatbot-container" class="chatbot-container">
        <div class="chatbot-header">
            <div class="bot-info">
                <div class="bot-avatar">
                    <i class="fas fa-robot"></i>
                    <div class="avatar-status"></div>
                </div>
                <div class="bot-details">
                    <span class="bot-name">Asistente Financiero</span>
                    <span class="bot-status">En línea</span>
                </div>
            </div>
            <div class="chatbot-controls">
                <button id="expand-chat" class="control-btn" title="Expandir">
                    <i class="fas fa-expand"></i>
                </button>
                <button id="minimize-chat" class="control-btn" title="Minimizar">
                    <i class="fas fa-minus"></i>
                </button>
                <button id="close-chat" class="control-btn" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="chatbot-body">
            <div class="chat-suggestions">
                <div class="suggestion" onclick="sendQuickMessage('¿Cómo puedo ahorrar más dinero?')">
                    <i class="fas fa-piggy-bank"></i>
                    <span>Consejos de ahorro</span>
                </div>
                <div class="suggestion" onclick="sendQuickMessage('Analiza mis gastos')">
                    <i class="fas fa-chart-bar"></i>
                    <span>Análisis de gastos</span>
                </div>
                <div class="suggestion" onclick="sendQuickMessage('¿Cómo hacer un presupuesto?')">
                    <i class="fas fa-calculator"></i>
                    <span>Crear presupuesto</span>
                </div>
            </div>

            <div id="chat-messages" class="chat-messages"></div>

            <div class="chat-input-area">
                <div class="input-container">
                    <!-- CORREGIDO: Input habilitado -->
                    <input type="text" id="chat-input" placeholder="Escribe tu pregunta..." maxlength="500">
                    <button id="stop-btn" class="control-button stop-button" title="Detener respuesta" style="display: none;">
                        <i class="fas fa-stop"></i>
                    </button>
                    <button id="send-btn" class="send-button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat FAB -->
    <div id="chat-fab" class="chat-fab">
        <i class="fas fa-comments"></i>
        <span class="notification">1</span>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let chatVisible = false;
        let isTyping = false;
        let isExpanded = false;
        let typingInterval = null;
        let currentChart = null;

        // Esperar a que todo el DOM esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM completamente cargado - Inicializando componentes');
            
            // Inicializar componentes con un pequeño retraso para asegurar que todo esté listo
            setTimeout(() => {
                initializeChart();
                initializeChat();
                showWelcomeMessage();
            }, 100);
        });

        function initializeChart() {
            console.log('Inicializando gráfico...');
            const canvas = document.getElementById('financialChart');
            
            if (canvas) {
                console.log('Canvas encontrado:', canvas);
                const ctx = canvas.getContext('2d');
                
                // Destruir gráfico existente si hay uno
                if (currentChart) {
                    currentChart.destroy();
                }
                
                currentChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Ingresos', 'Gastos', 'Ahorros'],
                        datasets: [{
                            data: [3500, 1200, 800],
                            backgroundColor: ['#10b981', '#ef4444', '#3b82f6'],
                            borderWidth: 0,
                            cutout: '65%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { 
                                display: false 
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: S/${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Gráfico inicializado correctamente');
            } else {
                console.error('No se encontró el canvas para el gráfico con id "financialChart"');
                console.log('Elementos canvas en la página:', document.querySelectorAll('canvas'));
            }
        }

        function initializeChat() {
            console.log('Inicializando chat...');
            
            const chatFab = document.getElementById('chat-fab');
            const minimizeBtn = document.getElementById('minimize-chat');
            const expandBtn = document.getElementById('expand-chat');
            const closeBtn = document.getElementById('close-chat');
            const sendBtn = document.getElementById('send-btn');
            const stopBtn = document.getElementById('stop-btn');
            const chatInput = document.getElementById('chat-input');

            if (chatFab) {
                chatFab.addEventListener('click', toggleChat);
                console.log('Chat FAB inicializado');
            } else {
                console.error('No se encontró el chat FAB');
            }

            if (minimizeBtn) minimizeBtn.addEventListener('click', minimizeChat);
            if (expandBtn) expandBtn.addEventListener('click', expandChat);
            if (closeBtn) closeBtn.addEventListener('click', closeChat);
            if (sendBtn) sendBtn.addEventListener('click', sendMessage);
            if (stopBtn) stopBtn.addEventListener('click', stopTyping);
            
            if (chatInput) {
                chatInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && !isTyping) {
                        sendMessage();
                    }
                });
                console.log('Input del chat inicializado - ESTÁ HABILITADO');
            } else {
                console.error('No se encontró el input del chat');
            }

            console.log('Chat inicializado completamente');
        }

        function showWelcomeMessage() {
            setTimeout(() => {
                addBotMessage("¡Hola! 👋 Soy tu asistente financiero. Puedo ayudarte con análisis de gastos, consejos de ahorro y presupuestos. ¿En qué puedo ayudarte hoy?");
            }, 1500);
        }

        function toggleChat() {
            const container = document.getElementById('chatbot-container');
            const fab = document.getElementById('chat-fab');
            
            chatVisible = !chatVisible;
            
            if (chatVisible) {
                container.style.display = 'flex';
                fab.style.display = 'none';
                setTimeout(() => {
                    container.style.opacity = '1';
                    container.style.transform = 'translateY(0)';
                    // Enfocar el input cuando se abre el chat
                    document.getElementById('chat-input').focus();
                }, 10);
            } else {
                container.style.opacity = '0';
                container.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    container.style.display = 'none';
                    fab.style.display = 'flex';
                }, 300);
            }
        }

        function minimizeChat() {
            const container = document.getElementById('chatbot-container');
            const fab = document.getElementById('chat-fab');
            
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            setTimeout(() => {
                container.style.display = 'none';
                fab.style.display = 'flex';
            }, 300);
            
            chatVisible = false;
        }

        function expandChat() {
            const container = document.getElementById('chatbot-container');
            isExpanded = !isExpanded;
            
            if (isExpanded) {
                container.style.width = '80%';
                container.style.height = '80%';
                container.style.maxWidth = '1000px';
                container.style.maxHeight = '700px';
                document.getElementById('expand-chat').innerHTML = '<i class="fas fa-compress"></i>';
                document.getElementById('expand-chat').title = 'Contraer';
            } else {
                container.style.width = '350px';
                container.style.height = '500px';
                container.style.maxWidth = 'none';
                container.style.maxHeight = 'none';
                document.getElementById('expand-chat').innerHTML = '<i class="fas fa-expand"></i>';
                document.getElementById('expand-chat').title = 'Expandir';
            }
        }

        function closeChat() {
            minimizeChat();
        }

        function sendQuickMessage(message) {
            document.getElementById('chat-input').value = message;
            sendMessage();
        }

        function sendMessage() {
            if (isTyping) {
                console.log('El bot está escribiendo, espera...');
                return;
            }
            
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            
            if (!message) {
                console.log('Mensaje vacío');
                return;
            }
            
            document.querySelector('.chat-suggestions').style.display = 'none';
            
            addUserMessage(message);
            input.value = '';
            
            input.disabled = true;
            isTyping = true;
            
            document.getElementById('stop-btn').style.display = 'block';
            
            showTypingIndicator();
            
            setTimeout(() => {
                hideTypingIndicator();
                const response = getBotResponse(message);
                simulateTyping(response);
            }, 1000);
        }

        function stopTyping() {
            if (typingInterval) {
                clearInterval(typingInterval);
                typingInterval = null;
            }
            
            hideTypingIndicator();
            
            document.getElementById('chat-input').disabled = false;
            isTyping = false;
            
            document.getElementById('stop-btn').style.display = 'none';
            
            const messagesContainer = document.getElementById('chat-messages');
            const interruptedElement = document.createElement('div');
            interruptedElement.className = 'message bot-message interrupted';
            interruptedElement.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <p>Respuesta interrumpida. ¿En qué más puedo ayudarte?</p>
                    <span class="message-time">${getCurrentTime()}</span>
                </div>
            `;
            messagesContainer.appendChild(interruptedElement);
            scrollToBottom();
        }

        function simulateTyping(message) {
            const messagesContainer = document.getElementById('chat-messages');
            const messageElement = document.createElement('div');
            messageElement.className = 'message bot-message';
            messageElement.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <p></p>
                    <span class="message-time">${getCurrentTime()}</span>
                </div>
            `;
            messagesContainer.appendChild(messageElement);
            
            const textElement = messageElement.querySelector('p');
            let index = 0;
            const typingSpeed = 30;
            
            if (typingInterval) {
                clearInterval(typingInterval);
            }
            
            typingInterval = setInterval(() => {
                if (index < message.length) {
                    textElement.textContent += message[index];
                    index++;
                    scrollToBottom();
                } else {
                    clearInterval(typingInterval);
                    typingInterval = null;
                    
                    document.getElementById('chat-input').disabled = false;
                    isTyping = false;
                    
                    document.getElementById('stop-btn').style.display = 'none';
                }
            }, typingSpeed);
        }

        function addUserMessage(message) {
            const messagesContainer = document.getElementById('chat-messages');
            const messageElement = document.createElement('div');
            messageElement.className = 'message user-message';
            messageElement.innerHTML = `
                <div class="message-content">
                    <p>${message}</p>
                    <span class="message-time">${getCurrentTime()}</span>
                </div>
                <div class="message-avatar">
                    <img src="<?php echo $rutaFotoPerfil; ?>" alt="Usuario">
                </div>
            `;
            messagesContainer.appendChild(messageElement);
            scrollToBottom();
        }

        function addBotMessage(message) {
            const messagesContainer = document.getElementById('chat-messages');
            const messageElement = document.createElement('div');
            messageElement.className = 'message bot-message';
            messageElement.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <p>${message}</p>
                    <span class="message-time">${getCurrentTime()}</span>
                </div>
            `;
            messagesContainer.appendChild(messageElement);
            scrollToBottom();
        }

        function showTypingIndicator() {
            const messagesContainer = document.getElementById('chat-messages');
            const typingElement = document.createElement('div');
            typingElement.className = 'typing-indicator';
            typingElement.id = 'typing-indicator';
            typingElement.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="typing-content">
                    <div class="typing-dots">
                        <span></span><span></span><span></span>
                    </div>
                    <span class="typing-text">Escribiendo...</span>
                </div>
            `;
            messagesContainer.appendChild(typingElement);
            scrollToBottom();
        }

        function hideTypingIndicator() {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) {
                indicator.remove();
            }
        }

        function getBotResponse(message) {
            const lowerMessage = message.toLowerCase();
            
            if (lowerMessage.includes('ahorro') || lowerMessage.includes('ahorrar')) {
                return "Te recomiendo seguir la regla 50/30/20: 50% para gastos necesarios, 30% para gastos personales y 20% para ahorros. Automatiza tus ahorros para que sea más fácil y consistente. También considera establecer metas de ahorro específicas para mantenerte motivado.";
            } else if (lowerMessage.includes('gasto') || lowerMessage.includes('analiza')) {
                return "Tus principales categorías de gasto son: Alimentación (35%), Transporte (22%), Entretenimiento (15%) y Salud (12%). Te sugiero revisar la categoría de entretenimiento, ya que representa un 15% de tus gastos. Considera establecer un límite mensual para esta categoría.";
            } else if (lowerMessage.includes('presupuesto')) {
                return "Para crear un presupuesto efectivo: 1) Registra todos tus ingresos, 2) Clasifica tus gastos en categorías, 3) Establece límites realistas para cada categoría, 4) Haz un seguimiento regular y ajusta según sea necesario. La clave es ser constante y revisar tu presupuesto semanalmente.";
            } else if (lowerMessage.includes('balance') || lowerMessage.includes('resumen')) {
                return "Tu situación financiera actual es positiva. Tienes un balance favorable con una tendencia de crecimiento del 12.7% respecto al mes anterior. Tus ahorros han aumentado un 15.3% este mes, lo que indica que vas por buen camino. Sigue así!";
            } else if (lowerMessage.includes('inversión') || lowerMessage.includes('invertir')) {
                return "Para comenzar a invertir, te recomiendo: 1) Establecer un fondo de emergencia primero, 2) Definir tus objetivos financieros, 3) Comprender tu tolerancia al riesgo, 4) Diversificar tus inversiones. Considera opciones como fondos indexados o cuentas de ahorro de alto rendimiento para empezar.";
            } else {
                return "Puedo ayudarte con: análisis de gastos, consejos de ahorro, creación de presupuestos, estrategias de inversión y seguimiento de metas financieras. ¿Hay algo específico sobre lo que te gustaría hablar?";
            }
        }

        function getCurrentTime() {
            return new Date().toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }

        function scrollToBottom() {
            const messagesContainer = document.getElementById('chat-messages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function changeChartType(type) {
            document.querySelectorAll('.chart-btn').forEach(btn => {
                btn.style.background = '#f3f4f6';
                btn.style.color = '#6b7280';
            });
            event.target.closest('.chart-btn').style.background = '#4f46e5';
            event.target.closest('.chart-btn').style.color = 'white';
            
            if (currentChart) {
                currentChart.destroy();
            }
            
            const ctx = document.getElementById('financialChart').getContext('2d');
            currentChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: ['Ingresos', 'Gastos', 'Ahorros'],
                    datasets: [{
                        data: [3500, 1200, 800],
                        backgroundColor: ['#10b981', '#ef4444', '#3b82f6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        // Funciones para metas
        function abrirModalMeta() {
            const modal = new bootstrap.Modal(document.getElementById('modalMeta'));
            modal.show();
        }

        function abrirModalAgregarMonto(idMeta) {
            document.getElementById('id_meta_monto').value = idMeta;
            const modal = new bootstrap.Modal(document.getElementById('modalAgregarMonto'));
            modal.show();
        }

        function abrirModalEditarMeta(idMeta) {
            document.getElementById('id_meta_editar').value = idMeta;
            const modal = new bootstrap.Modal(document.getElementById('modalEditarMeta'));
            modal.show();
        }

        function confirmarEliminarMeta(idMeta) {
            if (confirm('¿Estás seguro de que quieres eliminar esta meta? Esta acción no se puede deshacer.')) {
                document.getElementById('id_meta_eliminar').value = idMeta;
                document.getElementById('formEliminarMeta').submit();
            }
        }

        // Función de debug para verificar que todo funciona
        function debugChat() {
            console.log('=== DEBUG CHAT ===');
            console.log('Chat FAB:', document.getElementById('chat-fab'));
            console.log('Chat Container:', document.getElementById('chatbot-container'));
            console.log('Chat Input:', document.getElementById('chat-input'));
            console.log('Send Button:', document.getElementById('send-btn'));
            console.log('Typing state:', isTyping);
            console.log('Chat visible:', chatVisible);
            console.log('Canvas found:', document.getElementById('financialChart'));
            console.log('==================');
        }

        // Ejecutar debug al cargar
        window.addEventListener('load', function() {
            setTimeout(debugChat, 2000);
        });
    </script>

    <!-- Los estilos CSS se mantienen igual que en tu código original -->
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #06b6d4;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--gray-200);
            padding: 2rem 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .brand-logo {
            width: 260px;
            height: 110px;
            border: none;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: logoFloat 4s ease-in-out infinite;
            padding: 12px 18px;
            overflow: hidden;
        }

        .brand-logo-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        .nav-section {
            margin-bottom: 1.5rem;
            padding: 0 1rem;
        }

        .nav-title {
            color: var(--gray-500);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.5rem;
            padding: 0 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 1rem;
            margin: 2px 0;
            color: var(--gray-600);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            background: var(--gray-100);
            color: var(--gray-800);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .nav-link i {
            width: 20px;
            font-size: 18px;
        }

        .main-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-800);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            font-weight: 600;
            color: var(--gray-700);
        }

        .user-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .logout-btn {
            color: var(--gray-500);
            text-decoration: none;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: var(--gray-100);
            color: var(--danger);
        }

        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .welcome-banner i {
            font-size: 2rem;
        }

        .welcome-text {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .welcome-text span {
            color: #fbbf24;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .card-income::before { background: var(--success); }
        .card-expense::before { background: var(--danger); }
        .card-budget::before { background: var(--primary); }
        .card-savings::before { background: var(--warning); }

        .card-content h3 {
            color: var(--gray-500);
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .card-trend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .trend-up {
            color: var(--success);
            font-weight: 600;
        }

        .trend-down {
            color: var(--danger);
            font-weight: 600;
        }

        .trend-text {
            color: var(--gray-500);
        }

        .card-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            opacity: 0.8;
        }

        .card-income .card-icon { background: var(--success); }
        .card-expense .card-icon { background: var(--danger); }
        .card-budget .card-icon { background: var(--primary); }
        .card-savings .card-icon { background: var(--warning); }

        .expenses-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .expenses-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .expense-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .expense-item:hover {
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .expense-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        .expense-icon.food { background: var(--warning); }
        .expense-icon.transport { background: var(--info); }
        .expense-icon.entertainment { background: #8b5cf6; }
        .expense-icon.health { background: var(--danger); }

        .expense-info {
            flex: 1;
        }

        .expense-category {
            font-weight: 600;
            color: var(--gray-800);
            display: block;
            margin-bottom: 0.25rem;
        }

        .expense-amount {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .expense-bar {
            background: var(--gray-200);
            height: 4px;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .expense-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 2px;
            transition: width 0.6s ease;
        }

        .expense-percentage {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.875rem;
        }

        .chatbot-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            z-index: 1001;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .chatbot-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 1rem 1.25rem;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bot-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .bot-avatar {
            position: relative;
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .avatar-status {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background: #10b981;
            border: 2px solid #4f46e5;
            border-radius: 50%;
        }

        .bot-details {
            display: flex;
            flex-direction: column;
        }

        .bot-name {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .bot-status {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        .chatbot-controls {
            display: flex;
            gap: 0.25rem;
        }

        .control-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 0.75rem;
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .chatbot-body {
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }

        .chat-suggestions {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            background: var(--gray-50);
        }

        .suggestion {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }

        .suggestion:hover {
            background: var(--primary);
            color: white;
            transform: translateX(2px);
            border-color: var(--primary);
        }

        .suggestion i {
            width: 16px;
            font-size: 0.875rem;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            background: var(--gray-50);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 3px;
        }

        .message {
            display: flex;
            gap: 0.5rem;
            align-items: flex-end;
            animation: messageSlide 0.3s ease;
        }

        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-message {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-message .message-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
        }

        .bot-message .message-avatar {
            background: var(--primary);
            color: white;
            font-size: 0.875rem;
        }

        .message-content {
            max-width: 75%;
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .user-message .message-content {
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .bot-message .message-content {
            border-bottom-left-radius: 4px;
        }

        .message-content p {
            margin: 0;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .message-time {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.25rem;
            display: block;
        }

        .bot-message .message-time {
            color: var(--gray-500);
        }

        .typing-indicator {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .typing-content {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .typing-dots {
            display: flex;
            gap: 0.25rem;
            padding: 0.75rem 1rem;
            background: white;
            border-radius: 12px 12px 12px 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .typing-dots span {
            width: 6px;
            height: 6px;
            background: var(--gray-400);
            border-radius: 50%;
            animation: typingDot 1.4s infinite;
        }

        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typingDot {
            0%, 60%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            30% {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        .typing-text {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-left: 0.5rem;
        }

        .chat-input-area {
            border-top: 1px solid var(--gray-200);
            padding: 1rem;
            background: white;
            border-radius: 0 0 16px 16px;
        }

        .input-container {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            background: var(--gray-100);
            border-radius: 20px;
            padding: 0.5rem;
        }

        #chat-input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            outline: none;
            color: var(--gray-800);
        }

        #chat-input:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        #chat-input::placeholder {
            color: var(--gray-500);
        }

        .control-button {
            background: transparent;
            border: none;
            color: var(--gray-600);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .stop-button {
            color: var(--danger);
        }

        .control-button:hover {
            background: var(--gray-200);
        }

        .send-button {
            background: var(--primary);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .send-button:hover {
            background: var(--primary-light);
            transform: scale(1.05);
        }

        .send-button:disabled {
            background: var(--gray-400);
            cursor: not-allowed;
            transform: none;
        }

        .chat-fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .chat-fab:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.5);
        }

        .notification {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.125rem 0.375rem;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        .interrupted .message-content {
            background: #fef3c7;
            color: #92400e;
        }

        .meta-action-btn:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .cards-container {
                grid-template-columns: 1fr;
            }

            .chatbot-container {
                width: calc(100vw - 40px);
                height: calc(100vh - 100px);
                bottom: 20px;
                right: 20px;
                left: 20px;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .goals-achievements-section {
                grid-template-columns: 1fr !important;
            }
        }

        @keyframes logoFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }
    </style>
    <style>
    .alerta-peligro {
        border-left: 4px solid #ef4444;
        background: linear-gradient(90deg, #fef2f2, white) !important;
    }

    .alerta-advertencia {
        border-left: 4px solid #f59e0b;
        background: linear-gradient(90deg, #fffbeb, white) !important;
    }

    .alerta-exito {
        border-left: 4px solid #10b981;
        background: linear-gradient(90deg, #ecfdf5, white) !important;
    }

    .alerta-info {
        border-left: 4px solid #3b82f6;
        background: linear-gradient(90deg, #eff6ff, white) !important;
    }

    .alerta-item {
        transition: all 0.3s ease;
    }

    .alerta-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }

    .alerta-cerrar:hover {
        color: #374151 !important;
    }
    </style>
</body>
</html>