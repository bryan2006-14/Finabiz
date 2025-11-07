<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location:index.php");
    exit;
}

require_once 'modelo/conexion.php';

$nombre = $_SESSION['nombre'];
$fotoPerfil = $_SESSION['foto_perfil'];
$rutaDefault = "recursos/img/default-avatar.png";
$rutaFotoPerfil = (!empty($fotoPerfil) && file_exists("fotos/" . $fotoPerfil))
    ? "fotos/" . $fotoPerfil
    : $rutaDefault;

$idUsuario = $_SESSION['id_usuario'];

// Obtener parámetros de filtro
$mesSeleccionado = isset($_GET['mes']) && $_GET['mes'] != '' ? $_GET['mes'] : '';
$anioSeleccionado = isset($_GET['anio']) && $_GET['anio'] != '' ? $_GET['anio'] : '';

// 🔹 Funciones con PDO y filtros - CORREGIDO: usar $conn en lugar de $connection
function getTotalIngreso($conn, $idUsuario, $mes = '', $anio = '') {
    $query = "SELECT SUM(monto) as total FROM ingresos WHERE usuario_id = :id_usuario";
    $params = [':id_usuario' => $idUsuario];
    
    if (!empty($mes) && !empty($anio)) {
        $query .= " AND EXTRACT(MONTH FROM fecha) = :mes AND EXTRACT(YEAR FROM fecha) = :anio";
        $params[':mes'] = $mes;
        $params[':anio'] = $anio;
    }
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function getTotalGasto($conn, $idUsuario, $mes = '', $anio = '') {
    $query = "SELECT SUM(monto) as total FROM gastos WHERE usuario_id = :id_usuario";
    $params = [':id_usuario' => $idUsuario];
    
    if (!empty($mes) && !empty($anio)) {
        $query .= " AND EXTRACT(MONTH FROM fecha) = :mes AND EXTRACT(YEAR FROM fecha) = :anio";
        $params[':mes'] = $mes;
        $params[':anio'] = $anio;
    }
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

// 🔹 Obtener totales con filtros - CORREGIDO: usar $conn en lugar de $connection
$ingresoTotal = getTotalIngreso($conn, $idUsuario, $mesSeleccionado, $anioSeleccionado);
$gastoTotal   = getTotalGasto($conn, $idUsuario, $mesSeleccionado, $anioSeleccionado);
$balance      = floatval($ingresoTotal) - floatval($gastoTotal);

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
    <link rel="stylesheet" href="css/reset.css" media="print" onload="this.media='all'">
    
    <!-- Fonts con display=swap -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    
    <title>Balance - ControlGastos</title>
    
    <!-- CSS Responsivo Completo -->
    <link rel="stylesheet" href="data:text/css;base64,<?php echo base64_encode('
/* VARIABLES Y ESTILOS BASE */
:root{--primary:#4f46e5;--primary-light:#6366f1;--success:#10b981;--danger:#ef4444;--warning:#f59e0b;--info:#06b6d4;--gray-50:#f9fafb;--gray-100:#f3f4f6;--gray-200:#e5e7eb;--gray-300:#d1d5db;--gray-400:#9ca3af;--gray-500:#6b7280;--gray-600:#4b5563;--gray-700:#374151;--gray-800:#1f2937;--gray-900:#111827;--sidebar-width:260px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:\'Inter\',sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;color:var(--gray-800);overflow-x:hidden}

/* SIDEBAR RESPONSIVO */
.sidebar{position:fixed;top:0;left:0;width:var(--sidebar-width);height:100vh;background:rgba(255,255,255,0.95);backdrop-filter:blur(20px);border-right:1px solid var(--gray-200);padding:2rem 0;z-index:1000;overflow-y:auto;transition:all 0.3s ease}
.brand-logo{width:100%;height:110px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;padding:12px 18px}
.brand-logo-img{max-width:90%;max-height:100%;object-fit:contain}
.nav-section{margin-bottom:1.5rem;padding:0 1rem}
.nav-title{color:var(--gray-500);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.5rem;padding:0 1rem}
.nav-link{display:flex;align-items:center;gap:12px;padding:12px 1rem;margin:2px 0;color:var(--gray-600);text-decoration:none;border-radius:8px;transition:all 0.3s ease;font-weight:500}
.nav-link:hover{background:var(--gray-100);color:var(--gray-800)}
.nav-link.active{background:var(--primary);color:white}
.nav-link i{width:20px;font-size:18px}

/* CONTENIDO PRINCIPAL */
.main-content{margin-left:var(--sidebar-width);padding:2rem;min-height:100vh;transition:margin-left 0.3s ease}
.header{display:flex;justify-content:space-between;align-items:center;background:white;padding:1.5rem 2rem;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
.page-title{font-size:2rem;font-weight:700;color:var(--gray-800)}
.user-info{display:flex;align-items:center;gap:1rem}
.user-name{font-weight:600;color:var(--gray-700)}
.user-avatar img{width:40px;height:40px;border-radius:50%;object-fit:cover}
.logout-btn{color:var(--gray-500);text-decoration:none;padding:8px;border-radius:50%;transition:all 0.3s ease}
.logout-btn:hover{background:var(--gray-100);color:var(--danger)}

/* SECCIÓN DE CONTENIDO */
.content-section{background:white;padding:2rem;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:2rem}
.section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
.section-title{font-size:1.5rem;font-weight:700;color:var(--gray-800)}

/* FILTROS */
.filters-container{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;padding:1rem;background:var(--gray-50);border-radius:8px;flex-wrap:wrap;gap:1rem}
.filter-group{display:flex;align-items:center;gap:1rem;flex-wrap:wrap}
.filter-label{font-weight:500;color:var(--gray-700);white-space:nowrap}
.filter-select{background:white;border:1px solid var(--gray-200);padding:0.75rem 1rem;border-radius:6px;color:var(--gray-700);outline:none;min-width:150px}
.filter-select:focus{border-color:var(--primary)}
.btn-filter{background:var(--primary);color:white;border:none;padding:0.5rem 1rem;border-radius:6px;cursor:pointer;transition:all 0.3s ease;display:flex;align-items:center;gap:0.5rem}
.btn-filter:hover{background:var(--primary-light);transform:translateY(-1px)}

/* TARJETAS DE BALANCE */
.balance-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;margin-bottom:2rem}
.balance-card{padding:1.5rem;border-radius:12px;color:white;display:flex;align-items:center;gap:1rem}
.balance-card.income{background:linear-gradient(135deg,var(--success) 0%,#34d399 100%)}
.balance-card.expense{background:linear-gradient(135deg,var(--danger) 0%,#f87171 100%)}
.balance-card.total{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%)}
.balance-icon{width:60px;height:60px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem}
.balance-content{flex:1}
.balance-title{font-size:0.875rem;opacity:0.9;margin-bottom:0.25rem}
.balance-amount{font-size:1.75rem;font-weight:700}

/* SECCIÓN GRÁFICO */
.chart-section{background:white;padding:2rem;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:2rem}
.chart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem}
.chart-title{font-size:1.25rem;font-weight:600;color:var(--gray-800)}
.chart-controls{display:flex;gap:0.5rem;flex-wrap:wrap}
.chart-btn{background:var(--gray-100);border:none;padding:0.5rem 1rem;border-radius:6px;color:var(--gray-600);cursor:pointer;transition:all 0.3s ease;font-size:0.875rem;display:flex;align-items:center;gap:0.5rem}
.chart-btn:hover{background:var(--gray-200)}
.chart-btn.active{background:var(--primary);color:white}
.chart-container{height:400px;position:relative}

/* ESTADÍSTICAS */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1.5rem;margin-top:2rem}
.stat-card{background:var(--gray-50);padding:1.5rem;border-radius:8px;text-align:center}
.stat-value{font-size:2rem;font-weight:700;margin-bottom:0.5rem}
.stat-value.positive{color:var(--success)}
.stat-value.negative{color:var(--danger)}
.stat-label{color:var(--gray-600);font-size:0.875rem}

/* MENÚ MÓVIL */
.mobile-menu-btn{display:none;position:fixed;top:1rem;left:1rem;z-index:1001;background:white;border:none;width:50px;height:50px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.15);cursor:pointer;align-items:center;justify-content:center;font-size:1.25rem;color:var(--primary);transition:all 0.3s ease}
.mobile-menu-btn:hover{background:var(--primary);color:white;transform:scale(1.05)}
.mobile-menu-btn:active{transform:scale(0.95)}
.sidebar-close-btn{display:none;position:absolute;top:1rem;right:1rem;background:rgba(239,68,68,0.1);border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;align-items:center;justify-content:center;font-size:1.1rem;color:#ef4444;transition:all 0.3s ease;z-index:1001}
.sidebar-close-btn:hover{background:#ef4444;color:white}
.sidebar-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:999;display:none;opacity:0;transition:opacity 0.3s ease}
.sidebar-overlay.active{opacity:1}

/* RESPONSIVE */
@media (max-width:1024px){.sidebar{width:220px}.main-content{margin-left:220px;padding:1.5rem}}
@media (max-width:768px){
    .sidebar{position:fixed;left:-100%;width:280px;transition:left 0.3s ease;z-index:1100;box-shadow:2px 0 20px rgba(0,0,0,0.3)}
    .sidebar.active{left:0}
    .main-content{margin-left:0;padding:1rem}
    .mobile-menu-btn,.sidebar-close-btn{display:flex}
    .header{flex-direction:column;align-items:flex-start;padding:1rem;margin-top:4rem}
    .page-title{font-size:1.5rem;width:100%}
    .user-info{width:100%;justify-content:space-between}
    .section-header{flex-direction:column;align-items:flex-start}
    .filters-container{flex-direction:column;align-items:flex-start}
    .filter-group{width:100%;justify-content:space-between}
    .content-section{padding:1.5rem}
    .balance-cards{grid-template-columns:1fr}
    .balance-card{padding:1.25rem;flex-direction:column;text-align:center;gap:0.75rem}
    .balance-icon{width:50px;height:50px;font-size:1.25rem}
    .balance-amount{font-size:1.5rem}
    .chart-section{padding:1.5rem}
    .chart-header{flex-direction:column;align-items:flex-start}
    .chart-controls{width:100%;justify-content:center}
    .chart-container{height:300px}
    .stats-grid{grid-template-columns:1fr}
}
@media (max-width:480px){
    .main-content{padding:0.75rem}
    .header{padding:0.875rem;border-radius:8px;margin-bottom:1rem}
    .page-title{font-size:1.25rem}
    .user-avatar img{width:36px;height:36px}
    .content-section{padding:1rem;border-radius:8px;margin-bottom:1rem}
    .section-title{font-size:1.1rem}
    .filters-container{padding:0.875rem}
    .filter-select{width:100%}
    .balance-card{padding:1rem;border-radius:8px}
    .balance-amount{font-size:1.35rem}
    .chart-section{padding:1rem;border-radius:8px}
    .chart-title{font-size:1.1rem}
    .chart-btn{padding:0.375rem 0.75rem;font-size:0.8rem}
    .chart-container{height:250px}
    .stat-card{padding:1rem}
    .stat-value{font-size:1.5rem}
    .mobile-menu-btn{width:45px;height:45px;font-size:1.1rem;top:0.75rem;left:0.75rem}
    .sidebar{width:260px}
}
@media (max-width:374px){
    .page-title{font-size:1.1rem}
    .section-title{font-size:1rem}
    .balance-amount{font-size:1.25rem}
    .content-section{padding:0.875rem}
    .chart-container{height:220px}
}
@media (min-width:1440px){.main-content{max-width:1600px;margin-left:auto;margin-right:auto;padding-left:calc(var(--sidebar-width) + 3rem)}}
@media (max-width:1024px) and (orientation:landscape){.balance-card{flex-direction:row;text-align:left}.chart-container{height:300px}}
@media (max-width:767px) and (orientation:landscape){
    .sidebar{width:220px}
    .main-content{margin-left:220px;padding:1rem}
    .brand-logo{height:70px}
    .nav-link{padding:8px 0.75rem;font-size:0.75rem}
    .balance-cards{grid-template-columns:repeat(3,1fr);gap:0.75rem}
}
@media print{
    .sidebar,.mobile-menu-btn,.sidebar-overlay,.sidebar-close-btn,.logout-btn,.chart-controls{display:none!important}
    .main-content{margin-left:0;padding:0;margin-top:0}
    .header{margin-top:0}
    .chart-container{height:300px}
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
    
    <!-- Barra de navegación lateral -->
    <nav class="sidebar" id="sidebar">
        <!-- Botón cerrar en el sidebar (solo móvil) -->
        <button class="sidebar-close-btn" id="sidebar-close-btn" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="brand-logo">
            <img src="logo_Finabiz.png" alt="Finabiz Logo" class="brand-logo-img" loading="lazy">
        </div>

        <div class="nav-links">
            <div class="nav-section">
                <div class="nav-title">Home</div>
                <a href="./inicio.php" class="nav-link" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-title">Finanzas</div>
                <a href="./ingreso.php" class="nav-link" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-coins"></i>
                    <span>Ingresos</span>
                </a>
                <a href="./gasto.php" class="nav-link" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Gastos</span>
                </a>
                <a href="./balance.php" class="nav-link active" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-chart-line"></i>
                    <span>Balance</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-title">Herramientas</div>
                <a href="calculadora.php" class="nav-link" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-calculator"></i>
                    <span>Calculadora</span>
                </a>
                <a href="asistente.php" class="nav-link" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-robot"></i>
                    <span>Asistente IA</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-title">Otros</div>
                <a href="configuracion.php" class="nav-link" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
                <a href="modelo/logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="main-content">
        <!-- Header -->
        <div class="header">
            <h1 class="page-title">Balance Financiero</h1>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($nombre); ?></span>
                <div class="user-avatar">
                    <img src="<?php echo htmlspecialchars($rutaFotoPerfil); ?>" alt="Foto de perfil" loading="lazy">
                </div>
                <a href="modelo/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </div>
        </div>

        <!-- Sección de contenido -->
        <section class="content-section">
            <div class="section-header">
                <h2 class="section-title">Resumen <?php 
                    if (!empty($mesSeleccionado) && !empty($anioSeleccionado)) {
                        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                        echo 'de ' . ucfirst($meses[intval($mesSeleccionado) - 1]) . ' ' . $anioSeleccionado;
                    } else {
                        echo 'General';
                    }
                ?></h2>
            </div>

            <!-- Filtros -->
            <div class="filters-container">
                <div class="filter-group">
                    <span class="filter-label">Filtrar por período:</span>
                    <form method="GET" action="" id="filterForm" class="filter-group">
                        <select class="filter-select" name="mes" id="mesFilter">
                            <option value="">Todos los meses</option>
                            <option value="01" <?php echo $mesSeleccionado == '01' ? 'selected' : ''; ?>>Enero</option>
                            <option value="02" <?php echo $mesSeleccionado == '02' ? 'selected' : ''; ?>>Febrero</option>
                            <option value="03" <?php echo $mesSeleccionado == '03' ? 'selected' : ''; ?>>Marzo</option>
                            <option value="04" <?php echo $mesSeleccionado == '04' ? 'selected' : ''; ?>>Abril</option>
                            <option value="05" <?php echo $mesSeleccionado == '05' ? 'selected' : ''; ?>>Mayo</option>
                            <option value="06" <?php echo $mesSeleccionado == '06' ? 'selected' : ''; ?>>Junio</option>
                            <option value="07" <?php echo $mesSeleccionado == '07' ? 'selected' : ''; ?>>Julio</option>
                            <option value="08" <?php echo $mesSeleccionado == '08' ? 'selected' : ''; ?>>Agosto</option>
                            <option value="09" <?php echo $mesSeleccionado == '09' ? 'selected' : ''; ?>>Septiembre</option>
                            <option value="10" <?php echo $mesSeleccionado == '10' ? 'selected' : ''; ?>>Octubre</option>
                            <option value="11" <?php echo $mesSeleccionado == '11' ? 'selected' : ''; ?>>Noviembre</option>
                            <option value="12" <?php echo $mesSeleccionado == '12' ? 'selected' : ''; ?>>Diciembre</option>
                        </select>
                        
                        <select class="filter-select" name="anio" id="anioFilter">
                            <option value="">Todos los años</option>
                            <?php
                            $currentYear = date('Y');
                            for ($year = $currentYear; $year >= 2020; $year--) {
                                $selected = $anioSeleccionado == $year ? 'selected' : '';
                                echo "<option value='$year' $selected>$year</option>";
                            }
                            ?>
                        </select>
                        
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-filter"></i>
                            Filtrar
                        </button>
                        
                        <button type="button" class="btn-filter" onclick="resetFilters()" style="background: var(--gray-500);">
                            <i class="fas fa-redo"></i>
                            Limpiar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tarjetas de balance -->
            <div class="balance-cards">
                <div class="balance-card income">
                    <div class="balance-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="balance-content">
                        <div class="balance-title">INGRESOS <?php echo !empty($mesSeleccionado) && !empty($anioSeleccionado) ? 'DEL PERÍODO' : 'TOTALES'; ?></div>
                        <div class="balance-amount">+ S/<?php echo number_format(floatval($ingresoTotal), 2); ?></div>
                    </div>
                </div>

                <div class="balance-card expense">
                    <div class="balance-icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="balance-content">
                        <div class="balance-title">GASTOS <?php echo !empty($mesSeleccionado) && !empty($anioSeleccionado) ? 'DEL PERÍODO' : 'TOTALES'; ?></div>
                        <div class="balance-amount">- S/<?php echo number_format(floatval($gastoTotal), 2); ?></div>
                    </div>
                </div>

                <div class="balance-card total">
                    <div class="balance-icon">
                        <i class="fas fa-scale-balanced"></i>
                    </div>
                    <div class="balance-content">
                        <div class="balance-title">BALANCE <?php echo !empty($mesSeleccionado) && !empty($anioSeleccionado) ? 'DEL PERÍODO' : 'FINAL'; ?></div>
                        <div class="balance-amount">
                            <?php 
                            $balanceFormatted = number_format(abs($balance), 2);
                            if ($balance >= 0) {
                                echo '+ S/' . $balanceFormatted;
                            } else {
                                echo '- S/' . $balanceFormatted;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico principal -->
            <div class="chart-section">
                <div class="chart-header">
                    <h3 class="chart-title">Distribución Financiera</h3>
                    <div class="chart-controls">
                        <button class="chart-btn active" onclick="changeChartType('doughnut')">
                            <i class="fas fa-chart-pie"></i> Circular
                        </button>
                        <button class="chart-btn" onclick="changeChartType('bar')">
                            <i class="fas fa-chart-bar"></i> Barras
                        </button>
                        <button class="chart-btn" onclick="changeChartType('line')">
                            <i class="fas fa-chart-line"></i> Líneas
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="balanceChart"></canvas>
                </div>
            </div>

            <!-- Estadísticas adicionales -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value <?php echo $balance >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $balance >= 0 ? '+' : '-'; ?> S/<?php echo number_format(abs($balance), 2); ?>
                    </div>
                    <div class="stat-label">Balance Neto</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value positive">
                        <?php echo $ingresoTotal > 0 ? number_format((floatval($ingresoTotal) - floatval($gastoTotal)) / floatval($ingresoTotal) * 100, 1) : '0'; ?>%
                    </div>
                    <div class="stat-label">Tasa de Ahorro</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value">
                        S/<?php echo number_format(floatval($ingresoTotal) / 30, 2); ?>
                    </div>
                    <div class="stat-label">Promedio Diario</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value">
                        <?php echo $ingresoTotal > 0 ? number_format((floatval($gastoTotal) / floatval($ingresoTotal)) * 100, 1) : '0'; ?>%
                    </div>
                    <div class="stat-label">Gasto/Ingreso</div>
                </div>
            </div>
        </section>
    </main>

    <!-- Scripts - Cargar al final para mejor rendimiento -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <script>
        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            initializeMobileMenu();
            initializeChart();
        });

        let balanceChart;
        let currentChartType = 'doughnut';

        // Datos para el gráfico
        const chartData = {
            ingresos: <?php echo floatval($ingresoTotal); ?>,
            gastos: <?php echo floatval($gastoTotal); ?>,
            balance: <?php echo abs($balance); ?>
        };

        function initializeApp() {
            // Auto-submit del formulario de filtros cuando cambian mes o año
            const mesFilter = document.getElementById('mesFilter');
            const anioFilter = document.getElementById('anioFilter');
            
            if (mesFilter && anioFilter) {
                mesFilter.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
                
                anioFilter.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }

            // Inicializar tooltips de Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        function initializeChart() {
            const ctx = document.getElementById('balanceChart').getContext('2d');
            
            const data = {
                labels: ['Ingresos', 'Gastos', 'Balance'],
                datasets: [{
                    data: [chartData.ingresos, chartData.gastos, chartData.balance],
                    backgroundColor: ['#10b981', '#ef4444', '#4f46e5'],
                    borderWidth: 0
                }]
            };

            const options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: S/${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                }
            };

            // Configuración específica para cada tipo de gráfico
            if (currentChartType === 'doughnut') {
                options.cutout = '50%';
            }

            balanceChart = new Chart(ctx, {
                type: currentChartType,
                data: data,
                options: options
            });
        }

        // Cambiar tipo de gráfico
        function changeChartType(type) {
            currentChartType = type;
            
            // Actualizar botones activos
            document.querySelectorAll('.chart-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Destruir gráfico anterior y crear nuevo
            if (balanceChart) {
                balanceChart.destroy();
            }
            initializeChart();
        }

        function resetFilters() {
            window.location.href = 'balance.php';
        }

        function initializeMobileMenu() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            
            function updateMenuVisibility() {
                if (window.innerWidth <= 768) {
                    mobileMenuBtn.style.display = 'flex';
                    sidebar.classList.remove('active');
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
                sidebar.classList.remove('active');
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            } else {
                sidebar.classList.add('active');
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
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
    </script>
    <style>        .logout-btn {
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
</style>
</body>
</html>