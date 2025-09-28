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

// 🔹 Funciones con PDO
function getTotalIngreso($connection, $idUsuario) {
    $query = "SELECT SUM(monto) as total FROM ingresos WHERE id_usuario = :id_usuario";
    $stmt = $connection->prepare($query);
    $stmt->execute([':id_usuario' => $idUsuario]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function getTotalGasto($connection, $idUsuario) {
    $query = "SELECT SUM(monto) as total FROM gastos WHERE id_usuario = :id_usuario";
    $stmt = $connection->prepare($query);
    $stmt->execute([':id_usuario' => $idUsuario]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

// 🔹 Obtener totales
$ingresoTotal = getTotalIngreso($connection, $idUsuario);
$gastoTotal   = getTotalGasto($connection, $idUsuario);
$balance      = floatval($ingresoTotal) - floatval($gastoTotal);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/reset.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Balance - ControlGastos</title>
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

        /* SIDEBAR */
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

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 2rem;
            margin-bottom: 2rem;
        }

        .logo i {
            font-size: 2rem;
            color: var(--primary);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
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

        /* MAIN CONTENT */
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

        /* CONTENT SECTION */
        .content-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
        }

        /* FILTERS */
        .filters-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
        }

        .filter-select {
            background: white;
            border: 1px solid var(--gray-200);
            padding: 0.75rem 1rem;
            border-radius: 6px;
            color: var(--gray-700);
            outline: none;
            min-width: 200px;
        }

        .filter-select:focus {
            border-color: var(--primary);
        }

        /* BALANCE CARDS */
        .balance-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .balance-card {
            padding: 1.5rem;
            border-radius: 12px;
            color: white;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .balance-card.income {
            background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);
        }

        .balance-card.expense {
            background: linear-gradient(135deg, var(--danger) 0%, #f87171 100%);
        }

        .balance-card.total {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        }

        .balance-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .balance-content {
            flex: 1;
        }

        .balance-title {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.25rem;
        }

        .balance-amount {
            font-size: 1.75rem;
            font-weight: 700;
        }

        /* CHART SECTION */
        .chart-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .chart-controls {
            display: flex;
            gap: 0.5rem;
        }

        .chart-btn {
            background: var(--gray-100);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: var(--gray-600);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .chart-btn:hover {
            background: var(--gray-200);
        }

        .chart-btn.active {
            background: var(--primary);
            color: white;
        }

        .chart-container {
            height: 400px;
            position: relative;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-value.positive {
            color: var(--success);
        }

        .stat-value.negative {
            color: var(--danger);
        }

        .stat-label {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .balance-cards {
                grid-template-columns: 1fr;
            }

            .filters-container {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .chart-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Barra de navegación lateral -->
    <nav class="sidebar">
        <div class="logo">
            <i class="fas fa-wallet"></i>
            <span class="logo-text">ControlGastos</span>
        </div>

        <div class="nav-links">
            <div class="nav-section">
                <div class="nav-title">Home</div>
                <a href="./inicio.php" class="nav-link">
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
                <a href="./balance.php" class="nav-link active">
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
            <h1 class="page-title">Balance Financiero</h1>
            <div class="user-info">
                <span class="user-name"><?php echo $nombre; ?></span>
                <div class="user-avatar">
                    <img src="<?php echo $rutaFotoPerfil; ?>" alt="Foto de perfil">
                </div>
                <a href="modelo/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <!-- Sección de contenido -->
        <section class="content-section">
            <div class="section-header">
                <h2 class="section-title">Resumen del Mes</h2>
            </div>

            <!-- Filtros -->
            <div class="filters-container">
                <div>
                    <span>Seleccionar período:</span>
                </div>
                <select class="filter-select" id="periodFilter">
                    <option selected value="current">Mes Actual</option>
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </div>

            <!-- Tarjetas de balance -->
            <div class="balance-cards">
                <div class="balance-card income">
                    <div class="balance-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="balance-content">
                        <div class="balance-title">INGRESOS TOTALES</div>
                        <div class="balance-amount">+ S/<?php echo number_format(floatval($ingresoTotal), 2); ?></div>
                    </div>
                </div>

                <div class="balance-card expense">
                    <div class="balance-icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="balance-content">
                        <div class="balance-title">GASTOS TOTALES</div>
                        <div class="balance-amount">- S/<?php echo number_format(floatval($gastoTotal), 2); ?></div>
                    </div>
                </div>

                <div class="balance-card total">
                    <div class="balance-icon">
                        <i class="fas fa-scale-balanced"></i>
                    </div>
                    <div class="balance-content">
                        <div class="balance-title">BALANCE FINAL</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let balanceChart;
        let currentChartType = 'doughnut';

        // Datos para el gráfico
        const chartData = {
            ingresos: <?php echo floatval($ingresoTotal); ?>,
            gastos: <?php echo floatval($gastoTotal); ?>,
            balance: <?php echo abs($balance); ?>
        };

        // Inicializar gráfico
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

        // Filtrar por período
        document.getElementById('periodFilter').addEventListener('change', function(e) {
            console.log('Cambiar período:', e.target.value);
            // Aquí iría la lógica para cargar datos del período seleccionado
        });

        // Inicializar cuando carga la página
        document.addEventListener('DOMContentLoaded', function() {
            initializeChart();
        });

        // Actualizar estadísticas en tiempo real
        function updateStats() {
            // Aquí podrías agregar lógica para actualizar estadísticas
            // cuando cambien los filtros
        }
    </script>
</body>

</html>