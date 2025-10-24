<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location:index.php");
}
$nombre = $_SESSION['nombre'];
require_once 'modelo/config.php';
$fotoPerfil = $_SESSION['foto_perfil'];
$rutaDefault = "recursos/img/default-avatar.png";
$rutaFotoPerfil = (!empty($fotoPerfil) && file_exists("fotos/" . $fotoPerfil))
    ? "fotos/" . $fotoPerfil
    : $rutaDefault;

// Obtener datos del usuario (simulado - debes reemplazar con tu lógica real)
$datosUsuario = [
    'nombre' => $nombre,
    'correo' => 'usuario@ejemplo.com', // Reemplaza con datos reales de tu BD
    'password' => '********' // No mostrar contraseña real
];
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
    
    <title>Configuración - ControlGastos</title>
    
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

/* SECCIÓN CONFIGURACIÓN */
.configuration-section{background:white;padding:2rem;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:2rem}
.profile-header{text-align:center;margin-bottom:2rem;padding-bottom:2rem;border-bottom:1px solid var(--gray-200)}
.profile-title{font-size:1.5rem;font-weight:700;color:var(--gray-800);margin-bottom:1.5rem}
.profile-avatar{position:relative;width:120px;height:120px;margin:0 auto 1rem}
.profile-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;border:4px solid var(--primary)}
.avatar-edit{position:absolute;bottom:0;right:0;width:36px;height:36px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;cursor:pointer;transition:all 0.3s ease;border:2px solid white}
.avatar-edit:hover{background:var(--primary-light);transform:scale(1.1)}

/* FORMULARIOS */
.form-grid{display:grid;gap:1.5rem;margin-bottom:2rem}
.form-group{display:flex;flex-direction:column}
.form-label{font-weight:600;color:var(--gray-700);margin-bottom:0.5rem;display:flex;align-items:center;gap:0.5rem}
.form-label i{color:var(--primary);font-size:0.875rem}
.form-control{padding:0.75rem 1rem;border:1px solid var(--gray-200);border-radius:6px;font-size:1rem;transition:all 0.3s ease;background:var(--gray-50)}
.form-control:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(79,70,229,0.1);background:white}
.form-control:read-only{background:var(--gray-100);color:var(--gray-500);cursor:not-allowed}
.form-control:read-only:focus{border-color:var(--gray-200);box-shadow:none}
.password-section{background:var(--gray-50);padding:1.5rem;border-radius:8px;margin-bottom:2rem}
.password-section .form-grid{margin-bottom:1rem}
.checkbox-group{display:flex;align-items:center;gap:0.5rem;margin-top:1rem}
.checkbox-group input[type="checkbox"]{width:18px;height:18px;accent-color:var(--primary)}
.checkbox-group label{color:var(--gray-600);font-size:0.875rem;cursor:pointer}
.error-message{color:var(--danger);font-size:0.875rem;margin-top:0.5rem;display:none}
.error-message.show{display:block}

/* BOTONES */
.form-buttons{display:flex;gap:1rem;justify-content:flex-end;flex-wrap:wrap}
.btn-edit{background:var(--gray-200);border:none;padding:0.75rem 2rem;border-radius:6px;color:var(--gray-700);font-weight:600;cursor:pointer;transition:all 0.3s ease;display:flex;align-items:center;gap:0.5rem}
.btn-edit:hover{background:var(--gray-300)}
.btn-save{background:linear-gradient(135deg,var(--success) 0%,#34d399 100%);border:none;padding:0.75rem 2rem;border-radius:6px;color:white;font-weight:600;cursor:pointer;transition:all 0.3s ease;display:none;align-items:center;gap:0.5rem}
.btn-save:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(16,185,129,0.3)}
.btn-save.show{display:flex}
.btn-edit.hide{display:none}

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
    .configuration-section{padding:1.5rem}
    .profile-header{padding-bottom:1.5rem;margin-bottom:1.5rem}
    .profile-avatar{width:100px;height:100px}
    .form-buttons{flex-direction:column}
    .btn-edit,.btn-save{width:100%;justify-content:center}
}
@media (min-width:768px){.form-grid{grid-template-columns:1fr 1fr;gap:2rem}.password-section .form-grid{grid-template-columns:1fr}}
@media (max-width:480px){
    .main-content{padding:0.75rem}
    .header{padding:0.875rem;border-radius:8px;margin-bottom:1rem}
    .page-title{font-size:1.25rem}
    .user-avatar img{width:36px;height:36px}
    .configuration-section{padding:1rem;border-radius:8px;margin-bottom:1rem}
    .profile-title{font-size:1.1rem}
    .profile-avatar{width:80px;height:80px}
    .avatar-edit{width:30px;height:30px;font-size:0.875rem}
    .form-control{padding:0.625rem 0.875rem;font-size:0.875rem}
    .password-section{padding:1rem}
    .btn-edit,.btn-save{padding:0.625rem 1.25rem;font-size:0.875rem}
    .mobile-menu-btn{width:45px;height:45px;font-size:1.1rem;top:0.75rem;left:0.75rem}
    .sidebar{width:260px}
}
@media (max-width:374px){
    .page-title{font-size:1.1rem}
    .profile-title{font-size:1rem}
    .configuration-section{padding:0.875rem}
    .form-grid{gap:1rem}
}
@media (min-width:1440px){.main-content{max-width:1600px;margin-left:auto;margin-right:auto;padding-left:calc(var(--sidebar-width) + 3rem)}}
@media (max-width:1024px) and (orientation:landscape){.profile-avatar{width:90px;height:90px}}
@media (max-width:767px) and (orientation:landscape){
    .sidebar{width:220px}
    .main-content{margin-left:220px;padding:1rem}
    .brand-logo{height:70px}
    .nav-link{padding:8px 0.75rem;font-size:0.75rem}
}
@media print{
    .sidebar,.mobile-menu-btn,.sidebar-overlay,.sidebar-close-btn,.logout-btn,.btn-edit,.avatar-edit{display:none!important}
    .main-content{margin-left:0;padding:0;margin-top:0}
    .header{margin-top:0}
    .configuration-section{box-shadow:none;border:1px solid var(--gray-200)}
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
                <a href="./balance.php" class="nav-link" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-chart-line"></i>
                    <span>Balance</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-title">Herramientas</div>
                <a href="./calculadora.php" class="nav-link" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-calculator"></i>
                    <span>Calculadora</span>
                </a>
                <a href="asistente.php" class="nav-link" onclick="closeSidebarOnMobile()">
                <i class="fas fa-robot"></i>
                <span>Asistente IA</span>
            </div>

            <div class="nav-section">
                <div class="nav-title">Otros</div>
                <a href="configuracion.php" class="nav-link active" onclick="closeSidebarOnMobile()">
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
            <h1 class="page-title">Configuración de Perfil</h1>
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

        <!-- Sección de configuración -->
        <section class="configuration-section">
            <div class="profile-header">
                <h2 class="profile-title">Editar Perfil</h2>
                <div class="profile-avatar">
                    <img src="<?php echo htmlspecialchars($rutaFotoPerfil); ?>" alt="Foto de perfil" loading="lazy">
                    <div class="avatar-edit" title="Cambiar foto de perfil">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
            </div>

            <form class="configuration-form" method="POST" id="profileForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="nombre">
                            <i class="fas fa-user"></i>
                            Nombre de usuario
                        </label>
                        <input type="text" class="form-control" name="nombre" id="nombre" 
                               value="<?php echo htmlspecialchars($datosUsuario['nombre']); ?>" 
                               required readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="correo">
                            <i class="fas fa-envelope"></i>
                            Correo electrónico
                        </label>
                        <input type="email" class="form-control" name="correo" id="correo" 
                               value="<?php echo htmlspecialchars($datosUsuario['correo']); ?>" 
                               required readonly>
                    </div>
                </div>

                <div class="password-section">
                    <h3 style="color: var(--gray-800); margin-bottom: 1rem; font-size: 1.1rem;">
                        <i class="fas fa-lock"></i> Cambiar Contraseña
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="password">
                                Nueva contraseña
                            </label>
                            <input type="password" class="form-control" name="password" id="password" 
                                   value="<?php echo htmlspecialchars($datosUsuario['password']); ?>" 
                                   required readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="confirm_password">
                                Confirmar contraseña
                            </label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" 
                                   required readonly>
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="showPassword">
                        <label for="showPassword">Mostrar contraseñas</label>
                    </div>

                    <div class="error-message" id="error-message"></div>
                </div>

                <div class="form-buttons">
                    <button type="button" class="btn-edit" id="editButton">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </button>
                    <button type="submit" class="btn-save" id="submitButton">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </section>
    </main>

    <!-- Scripts - Cargar al final para mejor rendimiento -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <script>
        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            initializeMobileMenu();
        });

        function initializeApp() {
            // Elementos del DOM
            const editButton = document.getElementById('editButton');
            const submitButton = document.getElementById('submitButton');
            const showPasswordCheckbox = document.getElementById('showPassword');
            const errorMessage = document.getElementById('error-message');
            const formControls = document.querySelectorAll('.form-control');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');

            // Modo edición
            let editMode = false;

            // Habilitar/deshabilitar edición
            editButton.addEventListener('click', function() {
                editMode = !editMode;
                
                if (editMode) {
                    // Entrar en modo edición
                    formControls.forEach(control => {
                        control.readOnly = false;
                        control.style.background = 'white';
                    });
                    
                    editButton.classList.add('hide');
                    submitButton.classList.add('show');
                    errorMessage.textContent = '';
                    errorMessage.classList.remove('show');
                    
                    // Enfocar el primer campo
                    document.getElementById('nombre').focus();
                } else {
                    // Salir del modo edición
                    formControls.forEach(control => {
                        control.readOnly = true;
                        control.style.background = 'var(--gray-50)';
                    });
                    
                    editButton.classList.remove('hide');
                    submitButton.classList.remove('show');
                }
            });

            // Mostrar/ocultar contraseñas
            showPasswordCheckbox.addEventListener('change', function() {
                const type = this.checked ? 'text' : 'password';
                passwordInput.type = type;
                confirmPasswordInput.type = type;
            });

            // Validación del formulario
            document.getElementById('profileForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validar contraseñas
                if (passwordInput.value !== confirmPasswordInput.value) {
                    errorMessage.textContent = 'Las contraseñas no coinciden';
                    errorMessage.classList.add('show');
                    confirmPasswordInput.focus();
                    return;
                }
                
                // Validar longitud de contraseña
                if (passwordInput.value.length > 0 && passwordInput.value.length < 6) {
                    errorMessage.textContent = 'La contraseña debe tener al menos 6 caracteres';
                    errorMessage.classList.add('show');
                    passwordInput.focus();
                    return;
                }
                
                errorMessage.classList.remove('show');
                
                // Simular envío del formulario (aquí iría tu lógica real)
                alert('Cambios guardados correctamente');
                
                // Salir del modo edición
                editMode = false;
                formControls.forEach(control => {
                    control.readOnly = true;
                    control.style.background = 'var(--gray-50)';
                });
                
                editButton.classList.remove('hide');
                submitButton.classList.remove('show');
                
                // Ocultar contraseñas después de guardar
                showPasswordCheckbox.checked = false;
                passwordInput.type = 'password';
                confirmPasswordInput.type = 'password';
            });

            // Validación en tiempo real
            confirmPasswordInput.addEventListener('input', function() {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    errorMessage.textContent = 'Las contraseñas no coinciden';
                    errorMessage.classList.add('show');
                } else {
                    errorMessage.classList.remove('show');
                }
            });

            // Prevenir que se envíe el formulario con Enter en modo no edición
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !editMode) {
                    e.preventDefault();
                }
            });

            // Manejar clic en el botón de editar avatar
            document.querySelector('.avatar-edit').addEventListener('click', function() {
                alert('Funcionalidad para cambiar foto de perfil - En desarrollo');
                // Aquí iría la lógica para subir una nueva foto de perfil
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