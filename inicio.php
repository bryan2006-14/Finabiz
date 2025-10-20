<?php
session_start();
// Verificar si debemos mostrar el modal
$showAd = isset($_SESSION['show_ad']) && $_SESSION['show_ad'];
if ($showAd) {
    // Eliminar la flag para que no se muestre en cada recarga
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
$host = "dpg-d3cp1eumcj7s73dpm8sg-a.oregon-postgres.render.com"; 
$port = "5432"; 
$dbname = "db_finanzas_fxs9"; 
$user = "db_finanzas_fxs9_user"; 
$password = "MzArnjJx2t87VeEF1Cr03C35Qv3M49CU"; 

// Función optimizada para conectar a PostgreSQL
function conectarPostgreSQL($host, $port, $dbname, $user, $password) {
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
        $connection = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true // Conexión persistente para mejor rendimiento
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

if ($conexion_pdo) {
    // Sistema de logros
    require_once 'modelo/logros.php';
    $sistemaLogros = new SistemaLogros($conexion_pdo);
    $sistemaLogros->verificarLogros($_SESSION['id_usuario']);
    $logros_usuario = $sistemaLogros->getLogrosUsuario($_SESSION['id_usuario'], 5);
    $sistemaLogros->marcarLogrosComoVistos($_SESSION['id_usuario']);
    
    // Sistema de alertas
    require_once 'modelo/alertas.php';
    $sistemaAlertas = new AlertasInteligentes($conexion_pdo);
    $alertas = $sistemaAlertas->generarAlertas($_SESSION['id_usuario']);
    
    // Sistema de hábitos
    require_once 'modelo/habitos.php';
    $analisisHabitos = new AnalisisHabitos($conexion_pdo);
    $habitos_semana = $analisisHabitos->getHabitosSemana($_SESSION['id_usuario']);
    $analisis_habitos = $analisisHabitos->getAnalisisHabitos($_SESSION['id_usuario']);
    $resumen_habitos = $analisisHabitos->getResumenHabitos($_SESSION['id_usuario']);
}

// Procesar formularios de metas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conexion_pdo) {
    $id_usuario = $_SESSION['id_usuario'];
    
    if (isset($_POST['crear_meta'])) {
        $sql = "INSERT INTO metas (id_usuario, nombre_meta, descripcion, meta_total, icono, fecha_objetivo) 
                VALUES (:id_usuario, :nombre_meta, :descripcion, :meta_total, :icono, :fecha_objetivo)";
        $stmt = $conexion_pdo->prepare($sql);
        $resultado = $stmt->execute([
            ':id_usuario' => $id_usuario,
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
        
        $sql = "UPDATE metas SET monto_actual = monto_actual + :monto WHERE id_meta = :id_meta AND id_usuario = :id_usuario";
        $stmt = $conexion_pdo->prepare($sql);
        $stmt->execute([':monto' => $monto, ':id_meta' => $id_meta, ':id_usuario' => $id_usuario]);
        
        // Verificar si se completó
        $sql_check = "SELECT monto_actual, meta_total FROM metas WHERE id_meta = :id_meta";
        $stmt_check = $conexion_pdo->prepare($sql_check);
        $stmt_check->execute([':id_meta' => $id_meta]);
        $meta = $stmt_check->fetch();
        
        if ($meta && $meta['monto_actual'] >= $meta['meta_total']) {
            $conexion_pdo->prepare("UPDATE metas SET estado = 'completada' WHERE id_meta = :id_meta")
                ->execute([':id_meta' => $id_meta]);
            $conexion_pdo->prepare("INSERT INTO logros (id_usuario, tipo_logro, mensaje, icono) VALUES (:id_usuario, 'meta_completada', '¡Felicidades! Completaste una meta de ahorro', '🎯')")
                ->execute([':id_usuario' => $id_usuario]);
        }
        $_SESSION['mensaje_exito'] = "Monto agregado exitosamente";
    }
    
    if (isset($_POST['editar_meta'])) {
        $sql = "UPDATE metas SET nombre_meta = :nombre_meta, descripcion = :descripcion, meta_total = :meta_total, icono = :icono, fecha_objetivo = :fecha_objetivo WHERE id_meta = :id_meta AND id_usuario = :id_usuario";
        $stmt = $conexion_pdo->prepare($sql);
        $stmt->execute([
            ':nombre_meta' => $_POST['nombre_meta'],
            ':descripcion' => $_POST['descripcion'],
            ':meta_total' => floatval($_POST['meta_total']),
            ':icono' => $_POST['icono'],
            ':fecha_objetivo' => !empty($_POST['fecha_objetivo']) ? $_POST['fecha_objetivo'] : null,
            ':id_meta' => intval($_POST['id_meta']),
            ':id_usuario' => $id_usuario
        ]);
        $_SESSION['mensaje_exito'] = "Meta actualizada exitosamente";
    }
    
    if (isset($_POST['eliminar_meta'])) {
        $conexion_pdo->prepare("DELETE FROM metas WHERE id_meta = :id_meta AND id_usuario = :id_usuario")
            ->execute([':id_meta' => intval($_POST['id_meta']), ':id_usuario' => $id_usuario]);
        $_SESSION['mensaje_exito'] = "Meta eliminada exitosamente";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#4f46e5">
    <link rel="shortcut icon" href="icono-ic.png" type="image/x-icon">
    
    <!-- Preconnect para mejorar velocidad -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- CSS Crítico inline -->
    <style>
        :root{--primary:#4f46e5;--primary-light:#6366f1;--success:#10b981;--danger:#ef4444;--warning:#f59e0b;--info:#06b6d4;--gray-50:#f9fafb;--gray-100:#f3f4f6;--gray-200:#e5e7eb;--gray-300:#d1d5db;--gray-400:#9ca3af;--gray-500:#6b7280;--gray-600:#4b5563;--gray-700:#374151;--gray-800:#1f2937;--gray-900:#111827;--sidebar-width:260px}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;color:var(--gray-800);overflow-x:hidden}
    </style>
    
    <!-- CSS externo con defer -->
    <link rel="stylesheet" href="css/inicio/inicio.css" media="print" onload="this.media='all'">
    
    <!-- Fonts con display=swap para mejor rendimiento -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <title>Inicio - ControlGastos</title>
    
    <!-- CSS Responsivo Completo -->
    <link rel="stylesheet" href="data:text/css;base64,<?php echo base64_encode('
/* CSS COMPLETO RESPONSIVO OPTIMIZADO */
.sidebar{position:fixed;top:0;left:0;width:var(--sidebar-width);height:100vh;background:rgba(255,255,255,0.95);backdrop-filter:blur(20px);border-right:1px solid var(--gray-200);padding:2rem 0;z-index:1000;overflow-y:auto;transition:all 0.3s ease}
.brand-logo{width:100%;height:110px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;padding:12px 18px}
.brand-logo-img{max-width:90%;max-height:100%;object-fit:contain}
.nav-section{margin-bottom:1.5rem;padding:0 1rem}
.nav-title{color:var(--gray-500);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.5rem;padding:0 1rem}
.nav-link{display:flex;align-items:center;gap:12px;padding:12px 1rem;margin:2px 0;color:var(--gray-600);text-decoration:none;border-radius:8px;transition:all 0.3s ease;font-weight:500}
.nav-link:hover{background:var(--gray-100);color:var(--gray-800)}
.nav-link.active{background:var(--primary);color:white}
.nav-link i{width:20px;font-size:18px}
.main-content{margin-left:var(--sidebar-width);padding:2rem;min-height:100vh;transition:margin-left 0.3s ease}
.header{display:flex;justify-content:space-between;align-items:center;background:white;padding:1.5rem 2rem;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
.page-title{font-size:2rem;font-weight:700;color:var(--gray-800)}
.user-info{display:flex;align-items:center;gap:1rem}
.user-name{font-weight:600;color:var(--gray-700)}
.user-avatar img{width:40px;height:40px;border-radius:50%;object-fit:cover}
.logout-btn{color:var(--gray-500);text-decoration:none;padding:8px;border-radius:50%;transition:all 0.3s ease}
.logout-btn:hover{background:var(--gray-100);color:var(--danger)}
.welcome-banner{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);color:white;padding:2rem;border-radius:12px;margin-bottom:2rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap}
.welcome-banner i{font-size:2rem}
.welcome-text{font-size:1.25rem;font-weight:600}
.welcome-text span{color:#fbbf24}
.chart-section{background:white;padding:2rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:2rem}
.section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem}
.section-header h2,.section-header h3{font-size:1.5rem;font-weight:700;color:#1f2937;margin:0}
.chart-controls{display:flex;gap:0.5rem}
.chart-btn{background:#f3f4f6;border:none;padding:0.5rem;border-radius:8px;cursor:pointer;transition:all 0.3s ease;min-width:40px;height:40px;display:flex;align-items:center;justify-content:center}
.chart-btn:hover{transform:scale(1.05)}
.chart-btn.active{background:#4f46e5;color:white}
.chart-container{height:400px;position:relative}
.alertas-section{margin-bottom:2rem}
.alertas-container{display:flex;flex-direction:column;gap:0.5rem}
.alerta-item{display:flex;align-items:center;gap:1rem;padding:1rem;border-radius:8px;background:white;box-shadow:0 2px 4px rgba(0,0,0,0.1);transition:all 0.3s ease}
.alerta-item:hover{transform:translateX(5px);box-shadow:0 4px 8px rgba(0,0,0,0.15)}
.alerta-icono{font-size:1.5rem;flex-shrink:0}
.alerta-mensaje{flex:1;font-size:0.95rem}
.alerta-cerrar{background:none;border:none;font-size:1.2rem;cursor:pointer;color:#6b7280;flex-shrink:0}
.alerta-peligro{border-left:4px solid #ef4444;background:linear-gradient(90deg,#fef2f2,white)}
.alerta-advertencia{border-left:4px solid #f59e0b;background:linear-gradient(90deg,#fffbeb,white)}
.alerta-exito{border-left:4px solid #10b981;background:linear-gradient(90deg,#ecfdf5,white)}
.alerta-info{border-left:4px solid #3b82f6;background:linear-gradient(90deg,#eff6ff,white)}
.habitos-section{background:white;padding:2rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:2rem}
.chart-bars{display:flex;align-items:end;gap:0.5rem;height:200px;padding:1rem;background:#f8fafc;border-radius:8px;overflow-x:auto}
.bar-container{display:flex;flex-direction:column;align-items:center;flex:1;min-width:60px}
.bar{width:80%;border-radius:4px 4px 0 0;position:relative;transition:all 0.3s ease}
.bar-value{position:absolute;top:-25px;left:50%;transform:translateX(-50%);font-size:0.75rem;font-weight:600;color:#374151;white-space:nowrap}
.bar-label{margin-top:0.5rem;font-weight:600;color:#374151;font-size:0.875rem}
.bar-tendencia{font-size:0.75rem}
.analisis-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1rem}
.analisis-item{padding:1rem;background:#f0f9ff;border-radius:8px;border-left:4px solid #3b82f6;font-size:0.95rem}
.resumen-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem}
.resumen-item{text-align:center;padding:1rem;border-radius:8px}
.goals-achievements-section{display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:2rem}
.achievements-card,.goals-card{background:white;padding:1.5rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
.add-btn{background:#4f46e5;color:white;border:none;padding:0.5rem 1rem;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;transition:all 0.3s ease}
.add-btn:hover{background:#4338ca;transform:translateY(-2px)}
.meta-item{margin-bottom:1.5rem;padding:1rem;background:#f9fafb;border-radius:8px;transition:all 0.3s ease}
.meta-action-btn{background:none;border:none;cursor:pointer;padding:0.25rem 0.5rem;border-radius:4px;transition:all 0.3s ease}
.meta-action-btn:hover{transform:scale(1.1)}
.cards-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1.5rem;margin-bottom:2rem}
.card{background:white;padding:1.5rem;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);position:relative;overflow:hidden;transition:transform 0.3s ease}
.card:hover{transform:translateY(-2px)}
.card::before{content:"";position:absolute;top:0;left:0;right:0;height:4px}
.card-income::before{background:var(--success)}
.card-expense::before{background:var(--danger)}
.card-budget::before{background:var(--primary)}
.card-savings::before{background:var(--warning)}
.card-content h3{color:var(--gray-500);font-size:0.875rem;font-weight:600;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.05em}
.amount{font-size:2rem;font-weight:700;color:var(--gray-800);margin-bottom:0.5rem}
.card-trend{display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;flex-wrap:wrap}
.trend-up{color:var(--success);font-weight:600}
.trend-down{color:var(--danger);font-weight:600}
.trend-text{color:var(--gray-500)}
.card-icon{position:absolute;top:1.5rem;right:1.5rem;width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:white;opacity:0.8}
.card-income .card-icon{background:var(--success)}
.card-expense .card-icon{background:var(--danger)}
.card-budget .card-icon{background:var(--primary)}
.card-savings .card-icon{background:var(--warning)}
.expenses-section{background:white;padding:2rem;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:2rem}
.expenses-grid{display:grid;gap:1rem;margin-top:1.5rem}
.expense-item{display:flex;align-items:center;gap:1rem;padding:1rem;background:var(--gray-50);border-radius:8px;transition:all 0.3s ease}
.expense-item:hover{background:white;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
.expense-icon{width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;flex-shrink:0}
.expense-icon.food{background:var(--warning)}
.expense-icon.transport{background:var(--info)}
.expense-icon.entertainment{background:#8b5cf6}
.expense-icon.health{background:var(--danger)}
.expense-info{flex:1;min-width:0}
.expense-category{font-weight:600;color:var(--gray-800);display:block;margin-bottom:0.25rem}
.expense-amount{color:var(--gray-600);font-size:0.875rem}
.expense-bar{background:var(--gray-200);height:4px;border-radius:2px;margin-top:0.5rem;overflow:hidden}
.expense-fill{height:100%;background:var(--primary);border-radius:2px;transition:width 0.6s ease}
.expense-percentage{font-weight:600;color:var(--primary);font-size:0.875rem;flex-shrink:0}
.chatbot-container{position:fixed;bottom:20px;right:20px;width:350px;height:500px;background:white;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.15);display:none;flex-direction:column;z-index:1001;opacity:0;transform:translateY(20px);transition:all 0.3s ease}
.chatbot-header{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);color:white;padding:1rem 1.25rem;border-radius:16px 16px 0 0;display:flex;justify-content:space-between;align-items:center}
.bot-info{display:flex;align-items:center;gap:0.75rem}
.bot-avatar{position:relative;width:36px;height:36px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center}
.avatar-status{position:absolute;bottom:0;right:0;width:8px;height:8px;background:#10b981;border:2px solid #4f46e5;border-radius:50%}
.bot-name{font-weight:600;font-size:0.875rem}
.bot-status{font-size:0.75rem;opacity:0.9}
.chatbot-controls{display:flex;gap:0.25rem}
.control-btn{background:rgba(255,255,255,0.2);border:none;color:white;width:28px;height:28px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.3s ease}
.chatbot-body{display:flex;flex-direction:column;flex:1;overflow:hidden}
.chat-suggestions{padding:1rem;border-bottom:1px solid var(--gray-200);display:flex;flex-direction:column;gap:0.5rem;background:var(--gray-50)}
.suggestion{display:flex;align-items:center;gap:0.5rem;padding:0.75rem;background:white;border-radius:8px;cursor:pointer;transition:all 0.3s ease;font-size:0.875rem;color:var(--gray-700);border:1px solid var(--gray-200)}
.suggestion:hover{background:var(--primary);color:white;transform:translateX(2px)}
.chat-messages{flex:1;overflow-y:auto;padding:1rem;background:var(--gray-50);display:flex;flex-direction:column;gap:1rem}
.message{display:flex;gap:0.5rem;align-items:flex-end}
.user-message{flex-direction:row-reverse}
.message-avatar{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.bot-message .message-avatar{background:var(--primary);color:white}
.message-content{max-width:75%;background:white;padding:0.75rem 1rem;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
.user-message .message-content{background:var(--primary);color:white}
.message-content p{margin:0;font-size:0.875rem;line-height:1.5;word-wrap:break-word}
.chat-input-area{border-top:1px solid var(--gray-200);padding:1rem;background:white;border-radius:0 0 16px 16px}
.input-container{display:flex;gap:0.5rem;align-items:center;background:var(--gray-100);border-radius:20px;padding:0.5rem}
#chat-input{flex:1;border:none;background:transparent;padding:0.5rem 0.75rem;font-size:0.875rem;outline:none}
.send-button{background:var(--primary);border:none;color:white;width:36px;height:36px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.3s ease;flex-shrink:0}
.chat-fab{position:fixed;bottom:20px;right:20px;width:60px;height:60px;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);color:white;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.5rem;box-shadow:0 4px 15px rgba(79,70,229,0.4);transition:all 0.3s ease;z-index:1000}
.notification{position:absolute;top:-4px;right:-4px;background:var(--danger);color:white;font-size:0.75rem;font-weight:600;padding:0.125rem 0.375rem;border-radius:10px;min-width:18px;height:18px;display:flex;align-items:center;justify-content:center;border:2px solid white}

/* MEDIA QUERIES RESPONSIVO */
@media (max-width: 1024px){
  .sidebar{width:220px}
  .main-content{margin-left:220px;padding:1.5rem}
  .cards-container{grid-template-columns:repeat(2,1fr)}
  .goals-achievements-section{grid-template-columns:1fr}
  .chart-container{height:350px}
  .analisis-grid{grid-template-columns:1fr}
}

@media (max-width: 768px){
  .sidebar{position:fixed;left:-100%;width:220px;transition:left 0.3s ease;z-index:1100;box-shadow:2px 0 10px rgba(0,0,0,0.3)}
  .sidebar.active{left:0}
  .main-content{margin-left:0;padding:1rem}
  .header{flex-direction:column;align-items:flex-start;padding:1rem}
  .page-title{font-size:1.5rem;width:100%}
  .user-info{width:100%;justify-content:space-between}
  .welcome-banner{padding:1.25rem;flex-direction:column;text-align:center}
  .chart-section{padding:1rem}
  .section-header{flex-direction:column;align-items:flex-start}
  .chart-controls{width:100%;justify-content:center}
  .chart-container{height:300px}
  .alerta-item{flex-direction:column;align-items:flex-start;gap:0.75rem;padding:0.875rem}
  .alerta-cerrar{align-self:flex-end;position:absolute;top:0.5rem;right:0.5rem}
  .chart-bars{height:180px;padding:0.75rem;gap:0.25rem}
  .bar-container{min-width:50px}
  .bar-value{font-size:0.65rem;top:-20px}
  .bar-label{font-size:0.75rem}
  .analisis-grid{grid-template-columns:1fr;gap:0.75rem}
  .resumen-grid{grid-template-columns:1fr;gap:0.75rem}
  .goals-achievements-section{grid-template-columns:1fr;gap:1rem}
  .add-btn{width:100%;justify-content:center}
  .cards-container{grid-template-columns:1fr;gap:1rem}
  .expenses-grid{gap:0.75rem}
  .expense-item{padding:0.875rem;flex-wrap:wrap}
  .expense-info{order:2;width:100%;margin-top:0.5rem}
  .expense-percentage{order:3}
  .chatbot-container{width:calc(100vw - 20px);height:calc(100vh - 80px);max-height:600px;bottom:10px;right:10px;left:10px}
  .chat-fab{width:56px;height:56px;font-size:1.35rem;bottom:15px;right:15px}
}

@media (max-width: 480px){
  .main-content{padding:0.75rem}
  .header{padding:0.875rem;border-radius:8px;margin-bottom:1rem}
  .page-title{font-size:1.25rem}
  .user-avatar img{width:36px;height:36px}
  .welcome-banner{padding:1rem;border-radius:8px}
  .welcome-text{font-size:0.9rem}
  .chart-section,.habitos-section,.expenses-section{padding:0.875rem;border-radius:8px;margin-bottom:1rem}
  .section-header h2,.section-header h3{font-size:1.1rem}
  .chart-container{height:250px}
  .chart-btn{min-width:36px;height:36px;padding:0.375rem}
  .amount{font-size:1.5rem}
  .card-content h3{font-size:0.8rem}
  .card-icon{width:40px;height:40px;font-size:1.1rem;top:1rem;right:1rem}
  .expense-icon{width:36px;height:36px;font-size:0.9rem}
  .expense-category{font-size:0.875rem}
  .expense-amount,.expense-percentage{font-size:0.8rem}
  .chat-fab{width:52px;height:52px;font-size:1.25rem}
}

@media (max-width: 374px){
  .page-title{font-size:1.1rem}
  .welcome-text{font-size:0.85rem}
  .section-header h2,.section-header h3{font-size:1rem}
  .amount{font-size:1.35rem}
  .chart-container{height:220px}
  .bar-container{min-width:45px}
}

@media (min-width: 1440px){
  .main-content{max-width:1600px;margin-left:auto;margin-right:auto;padding-left:calc(var(--sidebar-width) + 3rem)}
  .cards-container{grid-template-columns:repeat(4,1fr)}
  .chart-container{height:450px}
}

@media (max-width: 1024px) and (orientation: landscape){
  .chart-container{height:300px}
  .chart-bars{height:180px}
  .chatbot-container{width:400px;height:450px}
}

@media (max-width: 767px) and (orientation: landscape){
  .sidebar{width:180px}
  .main-content{margin-left:180px;padding:1rem}
  .brand-logo{height:70px}
  .nav-link{padding:8px 0.75rem;font-size:0.75rem}
  .cards-container{grid-template-columns:repeat(4,1fr);gap:0.75rem}
  .goals-achievements-section{grid-template-columns:1fr 1fr;gap:1rem}
}
'); ?>">
</head>

<body>
    <!-- Overlay para sidebar en móvil -->
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <!-- Botón menú móvil (hamburguesa) -->
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
            <h1 class="page-title">Panel de Control</h1>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($nombre); ?></span>
                <div class="user-avatar">
                    <img src="<?php echo htmlspecialchars($rutaFotoPerfil); ?>" alt="Foto de perfil" loading="lazy">
                </div>
                <a href="modelo/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <i class="fas fa-hand-wave"></i>
            <div class="welcome-text">¡Bienvenido de nuevo! <span><?php echo htmlspecialchars($nombre); ?></span></div>
        </div>

        <!-- Alertas -->
        <?php if (!empty($alertas)): ?>
        <div class="alertas-section">
            <div class="section-header">
                <h3><i class="fas fa-bell"></i> Alertas Inteligentes</h3>
            </div>
            <div class="alertas-container">
                <?php foreach($alertas as $alerta): ?>
                    <div class="alerta-item alerta-<?php echo $alerta['tipo']; ?>">
                        <div class="alerta-icono"><?php echo $alerta['icono']; ?></div>
                        <div class="alerta-mensaje"><?php echo htmlspecialchars($alerta['mensaje']); ?></div>
                        <button class="alerta-cerrar" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Hábitos -->
        <div class="habitos-section">
            <div class="section-header">
                <h3><i class="fas fa-chart-line"></i> Análisis de Hábitos Semanales</h3>
                <small style="color:#6b7280;">Patrones y tendencias de tu comportamiento financiero</small>
            </div>
            
            <?php if (!empty($habitos_semana)): ?>
            <div class="habitos-chart" style="margin-bottom:2rem;">
                <h4 style="margin-bottom:1rem;color:#374151;">📊 Gastos por Día de la Semana</h4>
                <div class="chart-bars">
                    <?php 
                    $max_gasto = max(array_column($habitos_semana, 'gastos'));
                    foreach($habitos_semana as $dia => $datos): 
                        $altura = $max_gasto > 0 ? ($datos['gastos'] / $max_gasto) * 150 : 10;
                        $color = $datos['tendencia'] > 0 ? '#ef4444' : '#10b981';
                    ?>
                    <div class="bar-container">
                        <div class="bar" style="background:<?php echo $color; ?>;height:<?php echo $altura; ?>px;">
                            <div class="bar-value">S/<?php echo number_format($datos['gastos'], 0); ?></div>
                        </div>
                        <div class="bar-label"><?php echo $dia; ?></div>
                        <div class="bar-tendencia" style="color:<?php echo $color; ?>;">
                            <?php if ($datos['tendencia'] != 0): ?>
                                <?php echo $datos['tendencia'] > 0 ? '↗' : '↘'; ?>
                                <?php echo number_format(abs($datos['tendencia']), 1); ?>%
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php if (!empty($analisis_habitos)): ?>
            <div style="margin-bottom:2rem;">
                <h4 style="margin-bottom:1rem;color:#374151;">🔍 Patrones Detectados</h4>
                <div class="analisis-grid">
                    <?php foreach($analisis_habitos as $analisis): ?>
                        <div class="analisis-item"><?php echo $analisis; ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($resumen_habitos)): ?>
            <div>
                <h4 style="margin-bottom:1rem;color:#374151;">📈 Resumen Semanal</h4>
                <div class="resumen-grid">
                    <div class="resumen-item" style="background:#f0fdf4;">
                        <div style="font-size:2rem;color:#10b981;margin-bottom:0.5rem;">💰</div>
                        <div style="font-weight:600;color:#374151;">Total Ingresos</div>
                        <div style="font-size:1.25rem;font-weight:700;color:#10b981;">
                            S/<?php echo number_format($resumen_habitos['total_ingresos'], 2); ?>
                        </div>
                    </div>
                    <div class="resumen-item" style="background:#fef2f2;">
                        <div style="font-size:2rem;color:#ef4444;margin-bottom:0.5rem;">💸</div>
                        <div style="font-weight:600;color:#374151;">Total Gastos</div>
                        <div style="font-size:1.25rem;font-weight:700;color:#ef4444;">
                            S/<?php echo number_format($resumen_habitos['total_gastos'], 2); ?>
                        </div>
                    </div>
                    <div class="resumen-item" style="background:#f0f9ff;">
                        <div style="font-size:2rem;color:#3b82f6;margin-bottom:0.5rem;">⚖️</div>
                        <div style="font-weight:600;color:#374151;">Balance Semanal</div>
                        <div style="font-size:1.25rem;font-weight:700;color:#3b82f6;">
                            S/<?php echo number_format($resumen_habitos['balance_semanal'], 2); ?>
                        </div>
                    </div>
                    <div class="resumen-item" style="background:#faf5ff;">
                        <div style="font-size:2rem;color:#8b5cf6;margin-bottom:0.5rem;">📅</div>
                        <div style="font-weight:600;color:#374151;">Días Activos</div>
                        <div style="font-size:1.25rem;font-weight:700;color:#8b5cf6;">
                            <?php echo $resumen_habitos['dias_con_movimientos']; ?>/7
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
                <div style="text-align:center;padding:2rem;color:#6b7280;">
                    <i class="fas fa-chart-line" style="font-size:3rem;margin-bottom:1rem;opacity:0.5;"></i>
                    <p>No hay suficientes datos para el análisis de hábitos</p>
                    <p style="font-size:0.875rem;">Registra ingresos y gastos para ver tus patrones</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Logros y Metas -->
        <div class="goals-achievements-section">
            <div class="achievements-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                    <h3 style="display:flex;align-items:center;gap:0.5rem;margin:0;">🏆 Tus Logros</h3>
                </div>
                <div>
                    <?php if (!empty($logros_usuario)): ?>
                        <?php foreach($logros_usuario as $logro): 
                            $fecha = date('d/m/Y', strtotime($logro['fecha_obtenido']));
                            $bgColor = !$logro['visto'] ? '#fef3c7' : '#f3f4f6';
                            $borderColor = !$logro['visto'] ? '#f59e0b' : '#4f46e5';
                        ?>
                        <div style="background:<?php echo $bgColor; ?>;padding:1rem;border-radius:8px;margin-bottom:0.5rem;border-left:4px solid <?php echo $borderColor; ?>;">
                            <div style="font-weight:600;display:flex;align-items:center;gap:0.5rem;">
                                <span><?php echo $logro['icono']; ?></span>
                                <span><?php echo $logro['mensaje']; ?></span>
                            </div>
                            <div style="font-size:0.875rem;color:#6b7280;margin-top:0.25rem;">
                                Obtenido: <?php echo $fecha; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align:center;padding:3rem 1rem;color:#9ca3af;">
                            <i class="fas fa-trophy" style="font-size:3rem;margin-bottom:1rem;opacity:0.5;"></i>
                            <p>Comienza a usar la app<br>para desbloquear logros</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="goals-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem;">
                    <h3 style="display:flex;align-items:center;gap:0.5rem;margin:0;">🎯 Tus Metas</h3>
                    <button class="add-btn" onclick="abrirModalMeta()">
                        <i class="fas fa-plus"></i> Nueva Meta
                    </button>
                </div>
                <div>
                    <?php
                    if ($conexion_pdo) {
                        $stmt = $conexion_pdo->prepare("SELECT * FROM metas WHERE id_usuario = :id_usuario AND estado = 'activa' ORDER BY fecha_creacion DESC LIMIT 5");
                        $stmt->execute([':id_usuario' => $_SESSION['id_usuario']]);
                        $metas = $stmt->fetchAll();
                        
                        if (!empty($metas)):
                            foreach ($metas as $meta):
                                $porcentaje = $meta['meta_total'] > 0 ? min(round(($meta['monto_actual'] / $meta['meta_total']) * 100), 100) : 0;
                    ?>
                    <div class="meta-item">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;flex-wrap:wrap;gap:0.5rem;">
                            <span style="font-weight:600;display:flex;align-items:center;gap:0.5rem;">
                                <?php echo htmlspecialchars($meta['icono']); ?> <?php echo htmlspecialchars($meta['nombre_meta']); ?>
                            </span>
                            <div style="display:flex;gap:0.5rem;">
                                <button class="meta-action-btn" onclick="abrirModalAgregarMonto(<?php echo $meta['id_meta']; ?>)" style="color:#10b981;" title="Agregar monto">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button class="meta-action-btn" onclick="abrirModalEditarMeta(<?php echo $meta['id_meta']; ?>)" style="color:#3b82f6;" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="meta-action-btn" onclick="confirmarEliminarMeta(<?php echo $meta['id_meta']; ?>)" style="color:#ef4444;" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div style="font-size:0.875rem;color:#6b7280;margin-bottom:0.5rem;">
                            S/<?php echo number_format($meta['monto_actual'], 2); ?> / S/<?php echo number_format($meta['meta_total'], 2); ?>
                            (Restante: S/<?php echo number_format(max($meta['meta_total'] - $meta['monto_actual'], 0), 2); ?>)
                        </div>
                        <div style="background:#e5e7eb;height:8px;border-radius:4px;overflow:hidden;">
                            <div style="background:linear-gradient(90deg,#10b981,#34d399);height:100%;width:<?php echo $porcentaje; ?>%;transition:width 0.6s ease;"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:0.25rem;">
                            <span style="font-size:0.875rem;font-weight:600;color:#10b981;"><?php echo $porcentaje; ?>%</span>
                            <?php if ($meta['fecha_objetivo']): ?>
                            <span style="font-size:0.75rem;color:#9ca3af;">Meta: <?php echo date('d/m/Y', strtotime($meta['fecha_objetivo'])); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php 
                            endforeach;
                        else:
                    ?>
                        <div style="text-align:center;padding:3rem 1rem;color:#9ca3af;">
                            <i class="fas fa-bullseye" style="font-size:3rem;margin-bottom:1rem;opacity:0.5;"></i>
                            <p>No tienes metas aún<br>¡Crea tu primera meta!</p>
                        </div>
                    <?php 
                        endif;
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Tarjetas Financieras -->
        <div class="cards-container">
            <div class="card card-income">
                <div class="card-content">
                    <h3>Ingreso Total</h3>
                    <div class="amount">S/<?php include 'modelo/totalIngreso.php'; ?></div>
                    <div class="card-trend">
                        <span class="trend-up"><i class="fas fa-arrow-up"></i> +8.2%</span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
            </div>

            <div class="card card-expense">
                <div class="card-content">
                    <h3>Gasto Total</h3>
                    <div class="amount">S/<?php include 'modelo/total.php'; ?></div>
                    <div class="card-trend">
                        <span class="trend-down"><i class="fas fa-arrow-down"></i> -3.5%</span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon"><i class="fas fa-piggy-bank"></i></div>
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
                        <span class="trend-up"><i class="fas fa-arrow-up"></i> +12.7%</span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon"><i class="fas fa-wallet"></i></div>
            </div>

            <div class="card card-savings">
                <div class="card-content">
                    <h3>Ahorros del Mes</h3>
                    <div class="amount">S/1,245.50</div>
                    <div class="card-trend">
                        <span class="trend-up"><i class="fas fa-arrow-up"></i> +15.3%</span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon"><i class="fas fa-piggy-bank"></i></div>
            </div>
        </div>

        <!-- Gastos por Categoría -->
        <div class="expenses-section">
            <div class="section-header">
                <h3>Gastos por Categoría</h3>
            </div>
            <div class="expenses-grid">
                <div class="expense-item">
                    <div class="expense-icon food"><i class="fas fa-utensils"></i></div>
                    <div class="expense-info">
                        <span class="expense-category">Alimentación</span>
                        <span class="expense-amount">S/425.80</span>
                        <div class="expense-bar"><div class="expense-fill" style="width:35%"></div></div>
                    </div>
                    <div class="expense-percentage">35%</div>
                </div>
                
                <div class="expense-item">
                    <div class="expense-icon transport"><i class="fas fa-car"></i></div>
                    <div class="expense-info">
                        <span class="expense-category">Transporte</span>
                        <span class="expense-amount">S/180.50</span>
                        <div class="expense-bar"><div class="expense-fill" style="width:22%"></div></div>
                    </div>
                    <div class="expense-percentage">22%</div>
                </div>

                <div class="expense-item">
                    <div class="expense-icon entertainment"><i class="fas fa-film"></i></div>
                    <div class="expense-info">
                        <span class="expense-category">Entretenimiento</span>
                        <span class="expense-amount">S/120.00</span>
                        <div class="expense-bar"><div class="expense-fill" style="width:15%"></div></div>
                    </div>
                    <div class="expense-percentage">15%</div>
                </div>

                <div class="expense-item">
                    <div class="expense-icon health"><i class="fas fa-heartbeat"></i></div>
                    <div class="expense-info">
                        <span class="expense-category">Salud</span>
                        <span class="expense-amount">S/95.30</span>
                        <div class="expense-bar"><div class="expense-fill" style="width:12%"></div></div>
                    </div>
                    <div class="expense-percentage">12%</div>
                </div>
            </div>
        </div>
    </main>

    <!-- MODALES (Bootstrap) -->
    <!-- Modal Crear Meta -->
    <div class="modal fade" id="modalMeta" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nueva Meta</h5>
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
                    <h5 class="modal-title">Agregar Monto a Meta</h5>
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
                    <h5 class="modal-title">Editar Meta</h5>
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
                    <input type="text" id="chat-input" placeholder="Escribe tu pregunta..." maxlength="500">
                    <button id="stop-btn" class="control-button stop-button" title="Detener respuesta" style="display:none;">
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

    <!-- Scripts - Cargar al final para mejor rendimiento -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>

    <script>
        // Variables globales
        let chatVisible = false;
        let isTyping = false;
        let isExpanded = false;
        let typingInterval = null;
        let currentChart = null;

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            initializeMobileMenu();
        });

        function initializeApp() {
            setTimeout(() => {
                initializeChart();
                initializeChat();
                showWelcomeMessage();
            }, 100);
        }

        function initializeMobileMenu() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            
            // Mostrar/ocultar botón hamburguesa según tamaño de pantalla
            function updateMenuVisibility() {
                if (window.innerWidth <= 768) {
                    mobileMenuBtn.style.display = 'flex';
                    sidebar.classList.remove('active'); // Ocultar sidebar en móvil por defecto
                } else {
                    mobileMenuBtn.style.display = 'none';
                    sidebar.classList.remove('active');
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
                // Cerrar sidebar
                sidebar.classList.remove('active');
                overlay.style.display = 'none';
                document.body.style.overflow = ''; // Restaurar scroll
            } else {
                // Abrir sidebar
                sidebar.classList.add('active');
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Prevenir scroll del body
            }
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.remove('active');
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        function initializeChart() {
            const canvas = document.getElementById('financialChart');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            if (currentChart) currentChart.destroy();
            
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
                        legend: { display: false },
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
        }

        function initializeChat() {
            const chatFab = document.getElementById('chat-fab');
            const minimizeBtn = document.getElementById('minimize-chat');
            const expandBtn = document.getElementById('expand-chat');
            const closeBtn = document.getElementById('close-chat');
            const sendBtn = document.getElementById('send-btn');
            const stopBtn = document.getElementById('stop-btn');
            const chatInput = document.getElementById('chat-input');

            if (chatFab) chatFab.addEventListener('click', toggleChat);
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
            }
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
                container.style.width = window.innerWidth <= 768 ? '95vw' : '80%';
                container.style.height = window.innerWidth <= 768 ? '85vh' : '80%';
                container.style.maxWidth = '1000px';
                container.style.maxHeight = '700px';
                document.getElementById('expand-chat').innerHTML = '<i class="fas fa-compress"></i>';
            } else {
                container.style.width = window.innerWidth <= 768 ? 'calc(100vw - 20px)' : '350px';
                container.style.height = window.innerWidth <= 768 ? 'calc(100vh - 80px)' : '500px';
                container.style.maxWidth = 'none';
                container.style.maxHeight = window.innerWidth <= 768 ? '600px' : 'none';
                document.getElementById('expand-chat').innerHTML = '<i class="fas fa-expand"></i>';
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
            if (isTyping) return;
            
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            if (!message) return;
            
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
        }

        function simulateTyping(message) {
            const messagesContainer = document.getElementById('chat-messages');
            const messageElement = document.createElement('div');
            messageElement.className = 'message bot-message';
            messageElement.innerHTML = `
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    <p></p>
                    <span class="message-time" style="font-size:0.75rem;color:#6b7280;margin-top:0.25rem;display:block;">${getCurrentTime()}</span>
                </div>
            `;
            messagesContainer.appendChild(messageElement);
            
            const textElement = messageElement.querySelector('p');
            let index = 0;
            const typingSpeed = 30;
            
            if (typingInterval) clearInterval(typingInterval);
            
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
                    <span class="message-time" style="font-size:0.75rem;color:rgba(255,255,255,0.7);margin-top:0.25rem;display:block;">${getCurrentTime()}</span>
                </div>
                <div class="message-avatar">
                    <img src="<?php echo $rutaFotoPerfil; ?>" alt="Usuario" style="width:100%;height:100%;border-radius:50%;object-fit:cover;border:2px solid var(--primary);">
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
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    <p>${message}</p>
                    <span class="message-time" style="font-size:0.75rem;color:#6b7280;margin-top:0.25rem;display:block;">${getCurrentTime()}</span>
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
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div style="display:flex;flex-direction:column;gap:0.25rem;">
                    <div style="display:flex;gap:0.25rem;padding:0.75rem 1rem;background:white;border-radius:12px 12px 12px 4px;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                        <span style="width:6px;height:6px;background:#9ca3af;border-radius:50%;animation:typingDot 1.4s infinite;"></span>
                        <span style="width:6px;height:6px;background:#9ca3af;border-radius:50%;animation:typingDot 1.4s infinite 0.2s;"></span>
                        <span style="width:6px;height:6px;background:#9ca3af;border-radius:50%;animation:typingDot 1.4s infinite 0.4s;"></span>
                    </div>
                </div>
            `;
            messagesContainer.appendChild(typingElement);
            scrollToBottom();
        }

        function hideTypingIndicator() {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) indicator.remove();
        }

        function getBotResponse(message) {
            const lowerMessage = message.toLowerCase();
            
            if (lowerMessage.includes('ahorro') || lowerMessage.includes('ahorrar')) {
                return "Te recomiendo seguir la regla 50/30/20: 50% para gastos necesarios, 30% para gastos personales y 20% para ahorros. Automatiza tus ahorros para que sea más fácil y consistente.";
            } else if (lowerMessage.includes('gasto') || lowerMessage.includes('analiza')) {
                return "Tus principales categorías de gasto son: Alimentación (35%), Transporte (22%), Entretenimiento (15%) y Salud (12%). Te sugiero revisar la categoría de entretenimiento.";
            } else if (lowerMessage.includes('presupuesto')) {
                return "Para crear un presupuesto efectivo: 1) Registra todos tus ingresos, 2) Clasifica tus gastos en categorías, 3) Establece límites realistas, 4) Haz seguimiento regular.";
            } else if (lowerMessage.includes('balance') || lowerMessage.includes('resumen')) {
                return "Tu situación financiera actual es positiva. Tienes un balance favorable con una tendencia de crecimiento del 12.7% respecto al mes anterior.";
            } else {
                return "Puedo ayudarte con: análisis de gastos, consejos de ahorro, creación de presupuestos, estrategias de inversión y seguimiento de metas financieras. ¿Hay algo específico sobre lo que te gustaría hablar?";
            }
        }

        function getCurrentTime() {
            return new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
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
            
            if (currentChart) currentChart.destroy();
            
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
                    plugins: { legend: { display: false } }
                }
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

        // Animación de tipeo para el chat
        const style = document.createElement('style');
        style.textContent = `
            @keyframes typingDot {
                0%, 60%, 100% { transform: scale(1); opacity: 0.5; }
                30% { transform: scale(1.2); opacity: 1; }
            }
            
            /* ESTILOS PARA MENÚ MÓVIL */
            .mobile-menu-btn {
                display: none;
                position: fixed;
                top: 1rem;
                left: 1rem;
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
            
            .mobile-menu-btn:active {
                transform: scale(0.95);
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
                opacity: 1;
            }
            
            /* Estilos móvil para sidebar */
            @media (max-width: 768px) {
                .sidebar {
                    position: fixed;
                    left: -100%;
                    width: 280px;
                    transition: left 0.3s ease;
                    z-index: 1100;
                    box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
                }
                
                .sidebar.active {
                    left: 0;
                }
                
                .sidebar-close-btn {
                    display: flex;
                }
                
                .main-content {
                    margin-left: 0;
                }
                
                /* Ajustar header cuando hay menú hamburguesa */
                .header {
                    margin-top: 4rem;
                }
            }
            
            @media (max-width: 480px) {
                .mobile-menu-btn {
                    width: 45px;
                    height: 45px;
                    font-size: 1.1rem;
                }
                
                .sidebar {
                    width: 260px;
                }
            }
            
            @media print {
                .sidebar, .chat-fab, .chatbot-container, .logout-btn, .add-btn, 
                .meta-action-btn, .alerta-cerrar, .mobile-menu-btn, .sidebar-overlay,
                .sidebar-close-btn { display: none !important; }
                .main-content { margin-left: 0; padding: 0; margin-top: 0; }
                .header { margin-top: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>