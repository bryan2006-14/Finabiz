<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location:index.php");
}
$nombre = $_SESSION['nombre'];
$fotoPerfil = $_SESSION['foto_perfil'];
$rutaDefault = "recursos/img/default-avatar.png";
$rutaFotoPerfil = (!empty($fotoPerfil) && file_exists("fotos/" . $fotoPerfil))
    ? "fotos/" . $fotoPerfil
    : $rutaDefault;

// Después de insertar un ingreso o gasto exitosamente
require_once 'modelo/logros.php';
function conectarPostgreSQL($host, $port, $dbname, $user, $password) {
    try {
        $conexion = @pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
        return $conexion;
    } catch (Exception $e) {
        return false;
    }
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
    <link rel="stylesheet" href="css/reset.css" media="print" onload="this.media='all'">
    
    <!-- Fonts con display=swap -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <title>Ingresos - ControlGastos</title>
    
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
.btn-primary-custom{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);border:none;padding:0.75rem 1.5rem;border-radius:8px;color:white;font-weight:600;display:flex;align-items:center;gap:0.5rem;transition:all 0.3s ease;text-decoration:none}
.btn-primary-custom:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(79,70,229,0.3);color:white}

/* TARJETA RESUMEN */
.summary-card{background:linear-gradient(135deg,var(--success) 0%,#34d399 100%);color:white;padding:1.5rem;border-radius:12px;margin-bottom:2rem;display:flex;align-items:center;gap:1rem}
.summary-icon{width:60px;height:60px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem}
.summary-content{flex:1}
.summary-title{font-size:0.875rem;opacity:0.9;margin-bottom:0.25rem}
.summary-amount{font-size:2rem;font-weight:700}

/* FILTROS */
.filters-container{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;padding:1rem;background:var(--gray-50);border-radius:8px;flex-wrap:wrap;gap:1rem}
.filter-select{background:white;border:1px solid var(--gray-200);padding:0.5rem 1rem;border-radius:6px;color:var(--gray-700);outline:none}
.filter-select:focus{border-color:var(--primary)}

/* TABLA */
.table-container{background:white;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);overflow-x:auto}
.table-custom{margin:0;min-width:600px}
.table-custom thead th{background:var(--gray-50);color:var(--gray-700);font-weight:600;padding:1rem;border-bottom:2px solid var(--gray-200)}
.table-custom tbody td{padding:1rem;vertical-align:middle;border-bottom:1px solid var(--gray-100)}
.amount-positive{color:var(--success);font-weight:600}
.payment-method{display:inline-flex;align-items:center;gap:0.5rem;padding:0.25rem 0.75rem;background:var(--gray-100);border-radius:20px;font-size:0.875rem}

/* MODAL */
.modal-custom .modal-content{border:none;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.15)}
.modal-custom .modal-header{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);color:white;border-radius:12px 12px 0 0;border:none;padding:1.5rem}
.modal-custom .modal-title{font-weight:600}
.modal-custom .btn-close{filter:invert(1)}
.modal-custom .modal-body{padding:1.5rem}
.form-group{margin-bottom:1.5rem}
.form-label{font-weight:600;color:var(--gray-700);margin-bottom:0.5rem}
.input-with-icon{position:relative}
.input-with-icon span{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--gray-500)}
.input-with-icon input,.input-with-icon select{padding-left:2.5rem}
.form-control-custom{border:1px solid var(--gray-200);border-radius:6px;padding:0.75rem 1rem;transition:all 0.3s ease}
.form-control-custom:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(79,70,229,0.1)}
.btn-submit{background:linear-gradient(135deg,var(--success) 0%,#34d399 100%);border:none;padding:0.75rem 2rem;border-radius:6px;color:white;font-weight:600;width:100%;transition:all 0.3s ease}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(16,185,129,0.3)}

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
    .content-section{padding:1.5rem}
    .summary-card{padding:1.25rem;flex-direction:column;text-align:center;gap:0.75rem}
    .summary-icon{width:50px;height:50px;font-size:1.25rem}
    .summary-amount{font-size:1.75rem}
    .table-container{overflow-x:auto}
    .modal-custom .modal-body{padding:1.25rem}
}
@media (max-width:480px){
    .main-content{padding:0.75rem}
    .header{padding:0.875rem;border-radius:8px;margin-bottom:1rem}
    .page-title{font-size:1.25rem}
    .user-avatar img{width:36px;height:36px}
    .content-section{padding:1rem;border-radius:8px;margin-bottom:1rem}
    .section-title{font-size:1.1rem}
    .summary-card{padding:1rem;border-radius:8px}
    .summary-amount{font-size:1.5rem}
    .btn-primary-custom{padding:0.625rem 1.25rem;font-size:0.875rem;width:100%;justify-content:center}
    .filters-container{padding:0.875rem}
    .filter-select{width:100%}
    .table-custom thead th,.table-custom tbody td{padding:0.75rem;font-size:0.875rem}
    .mobile-menu-btn{width:45px;height:45px;font-size:1.1rem;top:0.75rem;left:0.75rem}
    .sidebar{width:260px}
}
@media (max-width:374px){
    .page-title{font-size:1.1rem}
    .section-title{font-size:1rem}
    .summary-amount{font-size:1.35rem}
    .content-section{padding:0.875rem}
}
@media (min-width:1440px){.main-content{max-width:1600px;margin-left:auto;margin-right:auto;padding-left:calc(var(--sidebar-width) + 3rem)}}
@media (max-width:1024px) and (orientation:landscape){.summary-card{flex-direction:row;text-align:left}}
@media (max-width:767px) and (orientation:landscape){
    .sidebar{width:220px}
    .main-content{margin-left:220px;padding:1rem}
    .brand-logo{height:70px}
    .nav-link{padding:8px 0.75rem;font-size:0.75rem}
}
@media print{
    .sidebar,.mobile-menu-btn,.sidebar-overlay,.sidebar-close-btn,.logout-btn,.btn-primary-custom{display:none!important}
    .main-content{margin-left:0;padding:0;margin-top:0}
    .header{margin-top:0}
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
                <a href="./ingreso.php" class="nav-link active" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-coins"></i>
                    <span>Ingresos</span>
                </a>
                <a href="./gasto.php" class="nav-link" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Gastos</span>
                </a>
                <a href="./balance.php" class="nav-link" onclick="closeSidebarOnMobile()">
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
            </div>

            <div class="nav-section">
                <div class="nav-title">Otros</div>
                <a href="configuracion.php" class="nav-link" onclick="closeSidebarOnMobile()">
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
            <h1 class="page-title">Gestión de Ingresos</h1>
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

        <!-- Sección de contenido -->
        <section class="content-section">
            <div class="section-header">
                <h2 class="section-title">Registro de Ingresos</h2>
                <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#ingresoModal">
                    <i class="fas fa-plus"></i>
                    Agregar Nuevo Ingreso
                </button>
            </div>

            <!-- Tarjeta de resumen -->
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-title">TOTAL DE INGRESOS</div>
                    <div class="summary-amount">+ S/<?php include 'modelo/totalIngreso.php'; ?></div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="filters-container">
                <div>
                    <span>Filtrar por mes:</span>
                </div>
                <select class="filter-select" id="monthFilter">
                    <option selected disabled>Seleccionar mes</option>
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

            <!-- Tabla de ingresos -->
            <div class="table-container">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Monto</th>
                            <th>Forma de Pago</th>
                            <th>Fecha</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include 'modelo/tableIngreso.php'; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal para agregar ingreso -->
    <div class="modal fade modal-custom" id="ingresoModal" tabindex="-1" aria-labelledby="ingresoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ingresoModalLabel">Agregar Nuevo Ingreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="ingresoForm" action="./modelo/registroIngreso.php" method="POST">
                        <div class="form-group">
                            <label class="form-label">Monto</label>
                            <div class="input-with-icon">
                                <span>S/</span>
                                <input type="number" step="0.01" class="form-control form-control-custom" id="montoInput" name="monto" placeholder="0.00" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Forma de Pago</label>
                            <div class="input-with-icon">
                                <span><i class="fas fa-credit-card"></i></span>
                                <select class="form-control form-control-custom" name="forma_pago" required>
                                    <option value="">Seleccione Forma de Pago</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Yape">Yape</option>
                                    <option value="Plin">Plin</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Nota</label>
                            <textarea class="form-control form-control-custom" id="note" name="nota" rows="3" placeholder="Descripción del ingreso..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-plus"></i>
                            Agregar Ingreso
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts - Cargar al final para mejor rendimiento -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <script>
        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            initializeMobileMenu();
        });

        function initializeApp() {
            // Validación del formulario
            const ingresoForm = document.getElementById('ingresoForm');
            if (ingresoForm) {
                ingresoForm.addEventListener('submit', function(e) {
                    const montoInput = document.getElementById('montoInput');
                    const montoValue = parseFloat(montoInput.value);
                    
                    if (isNaN(montoValue) || montoValue <= 0) {
                        e.preventDefault();
                        alert('Por favor ingrese un monto válido mayor a 0');
                        montoInput.focus();
                        return false;
                    }
                    return true;
                });
            }

            // Filtrar por mes
            const monthFilter = document.getElementById('monthFilter');
            if (monthFilter) {
                monthFilter.addEventListener('change', function(e) {
                    console.log('Filtrar por mes:', e.target.value);
                    // Implementar lógica de filtrado según tu estructura de datos
                });
            }

            // Inicializar tooltips de Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
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
</body>
</html>