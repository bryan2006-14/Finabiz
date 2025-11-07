<?php
session_start();
// Verificar si debemos mostrar el modal
$showAd = isset($_SESSION['show_ad']) && $_SESSION['show_ad'];
if ($showAd) {
    unset($_SESSION['show_ad']);
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location:index.php");
    exit();
}

$nombre = $_SESSION['nombre'];
$fotoPerfil = $_SESSION['foto_perfil'];
$rutaDefault = "recursos/img/default-avatar.png";
$rutaFotoPerfil = (!empty($fotoPerfil) && file_exists("fotos/" . $fotoPerfil))
    ? "fotos/" . $fotoPerfil
    : $rutaDefault;

// Configuración de base de datos PostgreSQL
$host = 'dpg-d421923ipnbc73buvavg-a.oregon-postgres.render.com';
$port = "5432"; 
$dbname = "db_finabiz"; 
$user = "db_finabiz_user"; 
$password = "AkwKCIh1aJYNAqd687v8a6WZWgun5Axm"; 

function conectarPostgreSQL($host, $port, $dbname, $user, $password) {
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        $connection = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 10
        ]);
        return $connection;
    } catch (PDOException $e) {
        error_log("Error de conexión PDO: " . $e->getMessage());
        return false;
    }
}

$conexion_pdo = conectarPostgreSQL($host, $port, $dbname, $user, $password);

// Cargar módulos solo si la conexión existe
$logros_usuario = [];
$alertas = [];
$habitos_semana = [];
$analisis_habitos = [];
$resumen_habitos = [];
$metas_usuario = [];

if ($conexion_pdo) {
    // Cargar logros con manejo de errores
    if (file_exists('modelo/logros.php')) {
        require_once 'modelo/logros.php';
        try {
            $sistemaLogros = new SistemaLogros($conexion_pdo);
            $sistemaLogros->verificarLogros($_SESSION['id_usuario']);
            $logros_usuario = $sistemaLogros->getLogrosUsuario($_SESSION['id_usuario'], 5);
            $sistemaLogros->marcarLogrosComoVistos($_SESSION['id_usuario']);
        } catch (Exception $e) {
            error_log("Error cargando logros: " . $e->getMessage());
            $logros_usuario = [];
        }
    }
    
    // Cargar alertas con manejo de errores
    if (file_exists('modelo/alertas.php')) {
        require_once 'modelo/alertas.php';
        try {
            $sistemaAlertas = new AlertasInteligentes($conexion_pdo);
            $alertas = $sistemaAlertas->generarAlertas($_SESSION['id_usuario']);
        } catch (Exception $e) {
            error_log("Error cargando alertas: " . $e->getMessage());
            $alertas = [];
        }
    }
    
    // Cargar hábitos con manejo de errores
    if (file_exists('modelo/habitos.php')) {
        require_once 'modelo/habitos.php';
        try {
            $analisisHabitos = new AnalisisHabitos($conexion_pdo);
            $habitos_semana = $analisisHabitos->getHabitosSemana($_SESSION['id_usuario']);
            $analisis_habitos = $analisisHabitos->getAnalisisHabitos($_SESSION['id_usuario']);
            $resumen_habitos = $analisisHabitos->getResumenHabitos($_SESSION['id_usuario']);
        } catch (Exception $e) {
            error_log("Error cargando hábitos: " . $e->getMessage());
            $habitos_semana = [];
            $analisis_habitos = [];
            $resumen_habitos = [];
        }
    }
    
    // Cargar metas del usuario - CORREGIDO: usar usuario_id en lugar de id_usuario
    try {
        $sql_metas = "SELECT * FROM metas WHERE usuario_id = :usuario_id AND estado != 'completada' ORDER BY fecha_creacion DESC";
        $stmt_metas = $conexion_pdo->prepare($sql_metas);
        $stmt_metas->execute([':usuario_id' => $_SESSION['id_usuario']]);
        $metas_usuario = $stmt_metas->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error cargando metas: " . $e->getMessage());
        $metas_usuario = [];
    }
}

// Procesar formularios de metas con manejo de errores
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conexion_pdo) {
    $usuario_id = $_SESSION['id_usuario']; // Cambiado a usuario_id para consistencia
    
    try {
        if (isset($_POST['crear_meta'])) {
            // CORREGIDO: usar usuario_id en lugar de id_usuario
            $sql = "INSERT INTO metas (usuario_id, nombre_meta, descripcion, meta_total, icono, fecha_objetivo) 
                    VALUES (:usuario_id, :nombre_meta, :descripcion, :meta_total, :icono, :fecha_objetivo)";
            $stmt = $conexion_pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':usuario_id' => $usuario_id, // Cambiado aquí
                ':nombre_meta' => $_POST['nombre_meta'],
                ':descripcion' => $_POST['descripcion'],
                ':meta_total' => floatval($_POST['meta_total']),
                ':icono' => $_POST['icono'],
                ':fecha_objetivo' => !empty($_POST['fecha_objetivo']) ? $_POST['fecha_objetivo'] : null
            ]);
            $_SESSION['mensaje_exito'] = $resultado ? "Meta creada exitosamente" : "Error al crear la meta";
        }
        
        if (isset($_POST['agregar_monto'])) {
            $id_meta = intval($_POST['id_meta']);
            $monto = floatval($_POST['monto_agregar']);
            
            // CORREGIDO: usar usuario_id
            $sql = "UPDATE metas SET monto_actual = monto_actual + :monto WHERE id = :id_meta AND usuario_id = :usuario_id";
            $stmt = $conexion_pdo->prepare($sql);
            $stmt->execute([
                ':monto' => $monto, 
                ':id_meta' => $id_meta, 
                ':usuario_id' => $usuario_id // Cambiado aquí
            ]);
            
            $sql_check = "SELECT monto_actual, meta_total FROM metas WHERE id = :id_meta";
            $stmt_check = $conexion_pdo->prepare($sql_check);
            $stmt_check->execute([':id_meta' => $id_meta]);
            $meta = $stmt_check->fetch();
            
            if ($meta && $meta['monto_actual'] >= $meta['meta_total']) {
                $conexion_pdo->prepare("UPDATE metas SET estado = 'completada' WHERE id = :id_meta")
                    ->execute([':id_meta' => $id_meta]);
                $conexion_pdo->prepare("INSERT INTO logros (usuario_id, tipo_logro, mensaje, icono) VALUES (:usuario_id, 'meta_completada', '¡Felicidades! Completaste una meta de ahorro', '🎯')")
                    ->execute([':usuario_id' => $usuario_id]); // Cambiado aquí
            }
            $_SESSION['mensaje_exito'] = "Monto agregado exitosamente";
        }
        
        if (isset($_POST['editar_meta'])) {
            // CORREGIDO: usar usuario_id
            $sql = "UPDATE metas SET nombre_meta = :nombre_meta, descripcion = :descripcion, meta_total = :meta_total, icono = :icono, fecha_objetivo = :fecha_objetivo WHERE id = :id_meta AND usuario_id = :usuario_id";
            $stmt = $conexion_pdo->prepare($sql);
            $stmt->execute([
                ':nombre_meta' => $_POST['nombre_meta'],
                ':descripcion' => $_POST['descripcion'],
                ':meta_total' => floatval($_POST['meta_total']),
                ':icono' => $_POST['icono'],
                ':fecha_objetivo' => !empty($_POST['fecha_objetivo']) ? $_POST['fecha_objetivo'] : null,
                ':id_meta' => intval($_POST['id_meta']),
                ':usuario_id' => $usuario_id // Cambiado aquí
            ]);
            $_SESSION['mensaje_exito'] = "Meta actualizada exitosamente";
        }
        
        if (isset($_POST['eliminar_meta'])) {
            // CORREGIDO: usar usuario_id
            $conexion_pdo->prepare("DELETE FROM metas WHERE id = :id_meta AND usuario_id = :usuario_id")
                ->execute([
                    ':id_meta' => intval($_POST['id_meta']), 
                    ':usuario_id' => $usuario_id // Cambiado aquí
                ]);
            $_SESSION['mensaje_exito'] = "Meta eliminada exitosamente";
        }
        
    } catch (PDOException $e) {
        error_log("Error en operación POST: " . $e->getMessage());
        $_SESSION['mensaje_error'] = "Error en la operación: " . $e->getMessage();
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Obtener datos para las estadísticas
$total_ingresos = 0;
$total_gastos = 0;
$balance = 0;
$ahorros_mes = 0;

if ($conexion_pdo) {
    try {
        // Obtener total de ingresos del mes actual - CORREGIDO: usar usuario_id
        $sql_ingresos = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos WHERE usuario_id = :usuario_id AND EXTRACT(MONTH FROM fecha) = EXTRACT(MONTH FROM CURRENT_DATE)";
        $stmt_ingresos = $conexion_pdo->prepare($sql_ingresos);
        $stmt_ingresos->execute([':usuario_id' => $_SESSION['id_usuario']]);
        $result_ingresos = $stmt_ingresos->fetch();
        $total_ingresos = $result_ingresos['total'];

        // Obtener total de gastos del mes actual - CORREGIDO: usar usuario_id
        $sql_gastos = "SELECT COALESCE(SUM(monto), 0) as total FROM gastos WHERE usuario_id = :usuario_id AND EXTRACT(MONTH FROM fecha) = EXTRACT(MONTH FROM CURRENT_DATE)";
        $stmt_gastos = $conexion_pdo->prepare($sql_gastos);
        $stmt_gastos->execute([':usuario_id' => $_SESSION['id_usuario']]);
        $result_gastos = $stmt_gastos->fetch();
        $total_gastos = $result_gastos['total'];

        $balance = $total_ingresos - $total_gastos;
        $ahorros_mes = max(0, $total_ingresos - $total_gastos);
        
    } catch (PDOException $e) {
        error_log("Error calculando estadísticas: " . $e->getMessage());
    }
}

// Obtener gastos por categoría del mes actual
$gastos_por_categoria = [];
$total_gastos_categorias = 0;
if ($conexion_pdo) {
    try {
        $sql_categorias = "SELECT categoria, COALESCE(SUM(monto), 0) as total 
                          FROM gastos 
                          WHERE usuario_id = :usuario_id 
                          AND EXTRACT(MONTH FROM fecha) = EXTRACT(MONTH FROM CURRENT_DATE)
                          GROUP BY categoria
                          ORDER BY total DESC";
        $stmt_categorias = $conexion_pdo->prepare($sql_categorias);
        $stmt_categorias->execute([':usuario_id' => $_SESSION['id_usuario']]);
        $gastos_por_categoria = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular total para porcentajes
        foreach ($gastos_por_categoria as $gasto) {
            $total_gastos_categorias += $gasto['total'];
        }
        
    } catch (PDOException $e) {
        error_log("Error cargando gastos por categoría: " . $e->getMessage());
    }
}

// Iconos por categoría
$iconos_categorias = [
    'Alimentación' => ['icon' => 'utensils', 'class' => 'food'],
    'Transporte' => ['icon' => 'car', 'class' => 'transport'],
    'Entretenimiento' => ['icon' => 'film', 'class' => 'entertainment'],
    'Salud' => ['icon' => 'heartbeat', 'class' => 'health'],
    'Educación' => ['icon' => 'graduation-cap', 'class' => 'education'],
    'Ropa' => ['icon' => 'tshirt', 'class' => 'clothing'],
    'Hogar' => ['icon' => 'home', 'class' => 'home'],
    'Otros' => ['icon' => 'shopping-cart', 'class' => 'other']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#6366f1">
    <link rel="shortcut icon" href="icono-ic.png" type="image/x-icon">
    <!-- CSS Crítico inline -->
    <style>
        :root{--primary:#4f46e5;--primary-light:#6366f1;--success:#10b981;--danger:#ef4444;--warning:#f59e0b;--info:#06b6d4;--gray-50:#f9fafb;--gray-100:#f3f4f6;--gray-200:#e5e7eb;--gray-300:#d1d5db;--gray-400:#9ca3af;--gray-500:#6b7280;--gray-600:#4b5563;--gray-700:#374151;--gray-800:#1f2937;--gray-900:#111827;--sidebar-width:260px}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;color:var(--gray-800);overflow-x:hidden}
    </style>
    
    <!-- CSS externo con defer -->
    <link rel="stylesheet" href="css/inicio/inicio.css" media="print" onload="this.media='all'">
    
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <title>Panel de Control - ControlGastos</title>
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #ec4899;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --gray: #64748b;
            --light: #f1f5f9;
            --white: #ffffff;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--dark);
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            padding: 2rem 0;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
        }

        .brand-logo {
            width: 100%;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            padding: 12px 18px;
        }

        .brand-logo-img {
            max-width: 85%;
            max-height: 100%;
            object-fit: contain;
        }

        .nav-section {
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .nav-title {
            color: var(--gray);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.75rem;
            padding: 0 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 1.25rem;
            margin: 4px 0;
            color: var(--gray);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            background: var(--light);
            color: var(--primary);
            transform: translateX(5px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }

        .nav-link i {
            width: 22px;
            font-size: 18px;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* HEADER */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.6s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            margin: 0;
        }

        .header-subtitle {
            color: var(--gray);
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--light);
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--primary);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .logout-btn {
            background: var(--danger);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            background: #dc2626;
            color: white;
        }

        /* WELCOME BANNER */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 20px 60px rgba(99, 102, 241, 0.3);
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-banner i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
        }

        .welcome-text {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .welcome-text span {
            color: #fbbf24;
        }

        /* STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease forwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
        }

        .stat-card.income { --gradient-start: #10b981; --gradient-end: #34d399; }
        .stat-card.expense { --gradient-start: #ef4444; --gradient-end: #f87171; }
        .stat-card.balance { --gradient-start: #6366f1; --gradient-end: #818cf8; }
        .stat-card.savings { --gradient-start: #f59e0b; --gradient-end: #fbbf24; }

        .stat-icon {
            width: 65px;
            height: 65px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.25rem;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.75rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
            line-height: 1;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .trend-positive {
            color: var(--success);
        }

        .trend-negative {
            color: var(--danger);
        }

        /* ALERTS SECTION */
        .alerts-section {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-item {
            display: flex;
            align-items: start;
            gap: 1.25rem;
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            border-left: 5px solid;
            transition: all 0.3s ease;
        }

        .alert-item:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .alert-danger {
            background: linear-gradient(90deg, #fef2f2, white);
            border-color: var(--danger);
        }

        .alert-warning {
            background: linear-gradient(90deg, #fffbeb, white);
            border-color: var(--warning);
        }

        .alert-success {
            background: linear-gradient(90deg, #f0fdf4, white);
            border-color: var(--success);
        }

        .alert-info {
            background: linear-gradient(90deg, #eff6ff, white);
            border-color: #3b82f6;
        }

        .alert-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .alert-danger .alert-icon {
            background: var(--danger);
            color: white;
        }

        .alert-warning .alert-icon {
            background: var(--warning);
            color: white;
        }

        .alert-success .alert-icon {
            background: var(--success);
            color: white;
        }

        .alert-info .alert-icon {
            background: #3b82f6;
            color: white;
        }

        .alert-content {
            flex: 1;
        }

        .alert-message {
            color: var(--dark);
            line-height: 1.6;
            font-weight: 500;
        }

        .alert-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray);
            font-size: 1.2rem;
            padding: 0.25rem;
            transition: all 0.3s ease;
        }

        .alert-close:hover {
            color: var(--danger);
            transform: scale(1.2);
        }

        /* CONTENT GRID */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        /* CHART SECTION */
        .chart-section {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .chart-controls {
            display: flex;
            gap: 0.5rem;
            background: var(--light);
            padding: 0.5rem;
            border-radius: 12px;
        }

        .chart-btn {
            background: transparent;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            color: var(--gray);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-btn:hover {
            background: white;
            color: var(--primary);
        }

        .chart-btn.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .chart-container {
            height: 400px;
            position: relative;
            margin-top: 1.5rem;
        }

        /* GOALS & ACHIEVEMENTS */
        .goals-achievements-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .goals-card, .achievements-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .add-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .add-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.3);
        }

        .goal-item {
            background: var(--light);
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }

        .goal-item:hover {
            transform: translateX(5px);
            background: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .progress-bar-custom {
            background: #e2e8f0;
            height: 10px;
            border-radius: 10px;
            overflow: hidden;
            margin: 1rem 0 0.5rem 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            border-radius: 10px;
            transition: width 1s ease;
        }

        /* EXPENSES SECTION */
        .expenses-section {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .expense-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            background: var(--light);
            border-radius: 16px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .expense-item:hover {
            transform: translateX(5px);
            background: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .expense-icon {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
            flex-shrink: 0;
        }

        .expense-icon.food {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
        }

        .expense-icon.transport {
            background: linear-gradient(135deg, #06b6d4, #22d3ee);
        }

        .expense-icon.entertainment {
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        }

        .expense-icon.health {
            background: linear-gradient(135deg, #ef4444, #f87171);
        }

        .expense-icon.education {
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        }

        .expense-icon.clothing {
            background: linear-gradient(135deg, #ec4899, #f472b6);
        }

        .expense-icon.home {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
        }

        .expense-icon.other {
            background: linear-gradient(135deg, #6b7280, #9ca3af);
        }

        .expense-details {
            flex: 1;
        }

        .expense-category {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.05rem;
            margin-bottom: 0.25rem;
        }

        .expense-amount {
            color: var(--gray);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .expense-percentage {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--primary);
        }

        /* MENÚ HAMBURGUESA - ESTILOS CORREGIDOS */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 1.5rem;
            left: 1.5rem;
            z-index: 1001;
            background: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: var(--primary);
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.05);
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        .sidebar-close-btn {
            display: none;
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(239, 68, 68, 0.1);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #ef4444;
            transition: all 0.3s ease;
            z-index: 1001;
        }

        .sidebar-close-btn:hover {
            background: #ef4444;
            color: white;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .goals-achievements-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
            }

            .sidebar-close-btn {
                display: flex;
            }

            .sidebar {
                left: -100%;
                z-index: 1100;
                width: 280px;
                box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1.5rem;
                margin-top: 4rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .user-info {
                width: 100%;
                justify-content: space-between;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 300px;
            }

            .expense-item {
                flex-wrap: wrap;
            }

            .welcome-banner {
                padding: 1.5rem;
            }

            .welcome-text {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 480px) {
            .mobile-menu-btn {
                width: 45px;
                height: 45px;
                font-size: 1.1rem;
                top: 1rem;
                left: 1rem;
            }

            .sidebar {
                width: 260px;
            }

            .header {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .welcome-banner {
                padding: 1.25rem;
            }

            .welcome-text {
                font-size: 1.1rem;
            }

            .stat-card {
                padding: 1.5rem;
            }

            .stat-value {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Overlay para sidebar en móvil -->
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <!-- Botón menú móvil -->
    <button class="mobile-menu-btn" id="mobile-menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Incluir Navbar -->
    <?php include 'componentes/navbar.php'; ?>
    
    <!-- Incluir Modal de Publicidad -->
    <?php include 'componentes/modal_publicidad.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <!-- Header -->
        <div class="header">
            <div>
                <h1 class="page-title">Panel de Control</h1>
                <p class="header-subtitle">Gestiona tus finanzas de manera inteligente</p>
            </div>
            <div class="user-info">
                <div class="user-badge">
                    <div class="user-avatar">
                        <img src="<?php echo htmlspecialchars($rutaFotoPerfil); ?>" alt="Foto de perfil" loading="lazy">
                    </div>
                    <span class="user-name"><?php echo htmlspecialchars($nombre); ?></span>
                </div>
                <a href="modelo/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <i class="fas fa-hand-wave"></i>
            <div class="welcome-text">¡Bienvenido de nuevo, <span><?php echo htmlspecialchars($nombre); ?></span>!</div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card income">
                <div class="stat-icon">
                    <i class="fas fa-arrow-trend-up"></i>
                </div>
                <div class="stat-label">Ingresos Totales</div>
                <div class="stat-value">S/ <?php echo number_format($total_ingresos, 2); ?></div>
                <div class="stat-trend <?php echo $total_ingresos > 0 ? 'trend-positive' : 'trend-negative'; ?>">
                    <i class="fas fa-<?php echo $total_ingresos > 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                    <span><?php echo $total_ingresos > 0 ? '8.2% vs mes anterior' : 'Sin datos del mes anterior'; ?></span>
                </div>
            </div>

            <div class="stat-card expense">
                <div class="stat-icon">
                    <i class="fas fa-arrow-trend-down"></i>
                </div>
                <div class="stat-label">Gastos Totales</div>
                <div class="stat-value">S/ <?php echo number_format($total_gastos, 2); ?></div>
                <div class="stat-trend <?php echo $total_gastos > 0 ? 'trend-positive' : 'trend-negative'; ?>">
                    <i class="fas fa-<?php echo $total_gastos > 0 ? 'arrow-down' : 'arrow-up'; ?>"></i>
                    <span><?php echo $total_gastos > 0 ? '3.5% vs mes anterior' : 'Sin datos del mes anterior'; ?></span>
                </div>
            </div>

            <div class="stat-card balance">
                <div class="stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-label">Balance Actual</div>
                <div class="stat-value">S/ <?php echo number_format($balance, 2); ?></div>
                <div class="stat-trend <?php echo $balance > 0 ? 'trend-positive' : 'trend-negative'; ?>">
                    <i class="fas fa-<?php echo $balance > 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                    <span><?php echo $balance != 0 ? '12.7% vs mes anterior' : 'Sin movimientos'; ?></span>
                </div>
            </div>

            <div class="stat-card savings">
                <div class="stat-icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <div class="stat-label">Ahorros del Mes</div>
                <div class="stat-value">
                    S/ <?php echo number_format($ahorros_mes, 2); ?>
                </div>
                <div class="stat-trend <?php echo $ahorros_mes > 0 ? 'trend-positive' : 'trend-negative'; ?>">
                    <i class="fas fa-<?php echo $ahorros_mes > 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                    <span>
                        <?php 
                        if ($ahorros_mes > 0) {
                            echo 'Ahorro activo';
                        } else if ($total_ingresos > 0 && $total_gastos > 0) {
                            echo 'Sin ahorro este mes';
                        } else {
                            echo 'Sin datos disponibles';
                        }
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        <?php if (!empty($alertas)): ?>
        <div class="alerts-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-bell"></i> Alertas Inteligentes
                </h3>
            </div>
            <div>
                <?php foreach($alertas as $alerta): ?>
                    <div class="alert-item alert-<?php echo $alerta['tipo']; ?>">
                        <div class="alert-icon">
                            <i class="fas fa-<?php 
                                echo $alerta['tipo'] == 'peligro' ? 'exclamation-triangle' : 
                                     ($alerta['tipo'] == 'advertencia' ? 'exclamation-circle' : 
                                     ($alerta['tipo'] == 'exito' ? 'check-circle' : 'info-circle')); 
                            ?>"></i>
                        </div>
                        <div class="alert-content">
                            <p class="alert-message"><?php echo htmlspecialchars($alerta['mensaje']); ?></p>
                        </div>
                        <button class="alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Chart Section -->
            <div class="chart-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-chart-pie"></i> Resumen Financiero
                    </h3>
                    <div class="chart-controls">
                        <button class="chart-btn active" onclick="changeChartType('doughnut')">
                            <i class="fas fa-chart-pie"></i> Circular
                        </button>
                        <button class="chart-btn" onclick="changeChartType('bar')">
                            <i class="fas fa-chart-bar"></i> Barras
                        </button>
                        <button class="chart-btn" onclick="changeChartType('line')">
                            <i class="fas fa-chart-line"></i> Línea
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>

            <!-- Goals Section -->
            <div class="goals-card">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-bullseye"></i> Mis Metas
                    </h3>
                </div>
                <button class="add-btn" onclick="abrirModalMeta()">
                    <i class="fas fa-plus"></i> Nueva Meta de Ahorro
                </button>

                <div>
                    <?php
                    if (!empty($metas_usuario)):
                        foreach ($metas_usuario as $meta):
                            // CORREGIDO: usar 'id' en lugar de 'id_meta' si es necesario
                            $meta_id = isset($meta['id']) ? $meta['id'] : $meta['id_meta'];
                            $porcentaje = $meta['meta_total'] > 0 ? min(round(($meta['monto_actual'] / $meta['meta_total']) * 100), 100) : 0;
                    ?>
                    <div class="goal-item">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
                            <span style="font-weight:700;font-size:1.05rem;color:var(--dark);">
                                <?php echo htmlspecialchars($meta['nombre_meta']); ?>
                            </span>
                            <div style="display:flex;gap:0.5rem;">
                                <button style="background:none;border:none;color:var(--success);cursor:pointer;padding:0.25rem;font-size:1.1rem;" 
                                        onclick="abrirModalAgregarMonto(<?php echo $meta_id; ?>)" title="Agregar monto">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button style="background:none;border:none;color:var(--primary);cursor:pointer;padding:0.25rem;font-size:1.1rem;" 
                                        onclick="abrirModalEditarMeta(<?php echo $meta_id; ?>)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button style="background:none;border:none;color:var(--danger);cursor:pointer;padding:0.25rem;font-size:1.1rem;" 
                                        onclick="confirmarEliminarMeta(<?php echo $meta_id; ?>)" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div style="font-size:0.9rem;color:var(--gray);margin-bottom:0.5rem;font-weight:500;">
                            S/ <?php echo number_format($meta['monto_actual'], 2); ?> / S/ <?php echo number_format($meta['meta_total'], 2); ?>
                        </div>
                        <div class="progress-bar-custom">
                            <div class="progress-fill" style="width: <?php echo $porcentaje; ?>%"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:0.9rem;font-weight:700;color:var(--primary);"><?php echo $porcentaje; ?>% completado</span>
                            <?php if ($meta['fecha_objetivo']): ?>
                            <span style="font-size:0.8rem;color:var(--gray);">
                                <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($meta['fecha_objetivo'])); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php 
                        endforeach;
                    else:
                    ?>
                        <div style="text-align:center;padding:3rem 1rem;color:var(--gray);">
                            <i class="fas fa-bullseye" style="font-size:3.5rem;margin-bottom:1rem;opacity:0.3;"></i>
                            <p style="font-weight:600;">No tienes metas aún</p>
                            <p style="font-size:0.9rem;">¡Crea tu primera meta de ahorro!</p>
                        </div>
                    <?php 
                    endif;
                    ?>
                </div>
            </div>
        </div>

        <!-- Logros -->
        <?php if (!empty($logros_usuario)): ?>
        <div class="achievements-card" style="margin-bottom:2rem;">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-trophy"></i> Tus Logros Recientes
                </h3>
            </div>
            <div style="display:grid;gap:1rem;">
                <?php foreach($logros_usuario as $logro): 
                    $fecha = date('d/m/Y', strtotime($logro['fecha_obtenido']));
                    $bgColor = !$logro['visto'] ? 'linear-gradient(90deg, #fef3c7, white)' : 'var(--light)';
                    $borderColor = !$logro['visto'] ? 'var(--warning)' : 'var(--primary)';
                ?>
                <div style="background:<?php echo $bgColor; ?>;padding:1.25rem;border-radius:16px;border-left:5px solid <?php echo $borderColor; ?>;transition:all 0.3s ease;" 
                     onmouseover="this.style.transform='translateX(5px)'" 
                     onmouseout="this.style.transform='translateX(0)'">
                    <div style="font-weight:600;font-size:1.05rem;color:var(--dark);margin-bottom:0.25rem;">
                        <?php echo $logro['mensaje']; ?>
                    </div>
                    <div style="font-size:0.85rem;color:var(--gray);">
                        <i class="fas fa-calendar"></i> Obtenido el <?php echo $fecha; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Gastos por Categoría -->
        <div class="expenses-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-chart-bar"></i> Gastos por Categoría
                </h3>
            </div>

            <?php if (!empty($gastos_por_categoria)): ?>
                <?php foreach ($gastos_por_categoria as $gasto): 
                    $categoria = $gasto['categoria'];
                    $monto = $gasto['total'];
                    $porcentaje = $total_gastos_categorias > 0 ? round(($monto / $total_gastos_categorias) * 100) : 0;
                    
                    // Obtener icono y clase CSS para la categoría
                    $icono_info = $iconos_categorias[$categoria] ?? ['icon' => 'shopping-cart', 'class' => 'other'];
                ?>
                <div class="expense-item">
                    <div class="expense-icon <?php echo $icono_info['class']; ?>">
                        <i class="fas fa-<?php echo $icono_info['icon']; ?>"></i>
                    </div>
                    <div class="expense-details">
                        <div class="expense-category"><?php echo htmlspecialchars($categoria); ?></div>
                        <div class="expense-amount">S/ <?php echo number_format($monto, 2); ?></div>
                    </div>
                    <div class="expense-percentage"><?php echo $porcentaje; ?>%</div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center;padding:3rem 1rem;color:var(--gray);">
                    <i class="fas fa-chart-pie" style="font-size:3.5rem;margin-bottom:1rem;opacity:0.3;"></i>
                    <p style="font-weight:600;">No hay gastos registrados este mes</p>
                    <p style="font-size:0.9rem;">¡Comienza a registrar tus gastos para ver estadísticas!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- MODALES (Bootstrap) -->
    <!-- Modal Crear Meta -->
    <div class="modal fade" id="modalMeta" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-bullseye"></i> Crear Nueva Meta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="crear_meta" value="1">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Meta</label>
                            <input type="text" class="form-control" name="nombre_meta" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Monto Total (S/)</label>
                            <input type="number" class="form-control" name="meta_total" step="0.01" min="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icono</label>
                            <select class="form-select" name="icono">
                                <option value="target">🎯 Objetivo</option>
                                <option value="money">💰 Dinero</option>
                                <option value="home">🏠 Casa</option>
                                <option value="car">🚗 Auto</option>
                                <option value="plane">✈️ Viaje</option>
                                <option value="education">🎓 Educación</option>
                                <option value="business">💼 Negocio</option>
                                <option value="health">❤️ Salud</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha Objetivo (Opcional)</label>
                            <input type="date" class="form-control" name="fecha_objetivo">
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

    <!-- Modal Agregar Monto -->
    <div class="modal fade" id="modalAgregarMonto" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Agregar Monto a Meta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="agregar_monto" value="1">
                        <input type="hidden" id="id_meta_monto" name="id_meta">
                        <div class="mb-3">
                            <label class="form-label">Monto a Agregar (S/)</label>
                            <input type="number" class="form-control" name="monto_agregar" step="0.01" min="0.01" required>
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

    <!-- Modal Editar Meta -->
    <div class="modal fade" id="modalEditarMeta" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Meta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="editar_meta" value="1">
                        <input type="hidden" id="id_meta_editar" name="id_meta">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Meta</label>
                            <input type="text" class="form-control" id="nombre_meta_editar" name="nombre_meta" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_editar" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Monto Total (S/)</label>
                            <input type="number" class="form-control" id="meta_total_editar" name="meta_total" step="0.01" min="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icono</label>
                            <select class="form-select" id="icono_editar" name="icono">
                                <option value="target">🎯 Objetivo</option>
                                <option value="money">💰 Dinero</option>
                                <option value="home">🏠 Casa</option>
                                <option value="car">🚗 Auto</option>
                                <option value="plane">✈️ Viaje</option>
                                <option value="education">🎓 Educación</option>
                                <option value="business">💼 Negocio</option>
                                <option value="health">❤️ Salud</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha Objetivo (Opcional)</label>
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

    <!-- Formulario oculto eliminar meta -->
    <form id="formEliminarMeta" method="POST" style="display:none;">
        <input type="hidden" name="eliminar_meta" value="1">
        <input type="hidden" id="id_meta_eliminar" name="id_meta">
    </form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let currentChart = null;

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            initializeChart();
            initializeMobileMenu();
            animateProgressBars();
        });

        function initializeMobileMenu() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            
            function updateMenuVisibility() {
                if (window.innerWidth <= 768) {
                    if (mobileMenuBtn) mobileMenuBtn.style.display = 'flex';
                    if (sidebar) sidebar.classList.remove('active');
                } else {
                    if (mobileMenuBtn) mobileMenuBtn.style.display = 'none';
                    if (sidebar) sidebar.classList.remove('active');
                    document.getElementById('sidebar-overlay').style.display = 'none';
                }
            }
            
            updateMenuVisibility();
            window.addEventListener('resize', updateMenuVisibility);
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isActive = sidebar.classList.contains('active');
            
            if (isActive) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            } else {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Cerrar sidebar al hacer clic en un enlace (para móviles)
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });
        });

        function initializeChart() {
            const canvas = document.getElementById('financialChart');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            if (currentChart) currentChart.destroy();
            
            // Usar datos reales de PHP
            const ingresos = <?php echo $total_ingresos; ?>;
            const gastos = <?php echo $total_gastos; ?>;
            const ahorros = <?php echo $ahorros_mes; ?>;
            
            currentChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Ingresos', 'Gastos', 'Ahorros'],
                    datasets: [{
                        data: [ingresos, gastos, ahorros],
                        backgroundColor: ['#10b981', '#ef4444', '#6366f1'],
                        borderWidth: 0,
                        cutout: '70%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: { size: 14, weight: '600' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: S/ ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function changeChartType(type) {
            document.querySelectorAll('.chart-btn').forEach(btn => btn.classList.remove('active'));
            event.target.closest('.chart-btn').classList.add('active');
            
            if (currentChart) currentChart.destroy();
            
            const ctx = document.getElementById('financialChart').getContext('2d');
            
            // Usar datos reales de PHP
            const ingresos = <?php echo $total_ingresos; ?>;
            const gastos = <?php echo $total_gastos; ?>;
            const ahorros = <?php echo $ahorros_mes; ?>;
            
            currentChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: ['Ingresos', 'Gastos', 'Ahorros'],
                    datasets: [{
                        data: [ingresos, gastos, ahorros],
                        backgroundColor: ['#10b981', '#ef4444', '#6366f1'],
                        borderWidth: type === 'doughnut' ? 0 : 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            position: 'bottom',
                            labels: { padding: 20, font: { size: 14, weight: '600' } }
                        } 
                    }
                }
            });
        }

        function animateProgressBars() {
            document.querySelectorAll('.progress-fill').forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        }

        // Funciones para metas
        function abrirModalMeta() {
            new bootstrap.Modal(document.getElementById('modalMeta')).show();
        }

        function abrirModalAgregarMonto(idMeta) {
            document.getElementById('id_meta_monto').value = idMeta;
            new bootstrap.Modal(document.getElementById('modalAgregarMonto')).show();
        }

        function abrirModalEditarMeta(idMeta) {
            document.getElementById('id_meta_editar').value = idMeta;
            new bootstrap.Modal(document.getElementById('modalEditarMeta')).show();
        }

        function confirmarEliminarMeta(idMeta) {
            if (confirm('¿Estás seguro de que quieres eliminar esta meta? Esta acción no se puede deshacer.')) {
                document.getElementById('id_meta_eliminar').value = idMeta;
                document.getElementById('formEliminarMeta').submit();
            }
        }
    </script>
</body>
</html>