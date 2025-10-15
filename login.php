<?php
require 'modelo/conexion.php';
session_start();

$error_message = '';

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Preparamos la consulta con parámetros
        $stmt = $connection->prepare("SELECT id_usuario, nombre, password, foto_perfil FROM usuarios WHERE correo = :correo");
        $stmt->execute([':correo' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $password_bd = $row['password'];

            // ✅ VERIFICACIÓN MEJORADA - Permite login a usuarios de Google
            $login_valid = false;
            
            if ($password_bd === '') {
                // Usuario de Google con password vacío - permitir configurar
                $error_message = "Usuario registrado con Google. Si quieres usar email/password, primero configura una contraseña en tu perfil.";
            } 
            // Primero verificar si coincide en texto plano (para usuarios existentes)
            else if ($password_bd === $password) {
                $login_valid = true;
            }
            // Luego verificar con password_verify (para usuarios nuevos/encriptados)
            else if (password_verify($password, $password_bd)) {
                $login_valid = true;
            }

            if ($login_valid) {
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['id_usuario'] = $row['id_usuario'];
                $_SESSION['foto_perfil'] = $row['foto_perfil'];

                header('Location: inicio.php');
                exit;
            } else {
                $error_message = "La contraseña no coincide";
            }
        } else {
            $error_message = "No existe un usuario con ese correo";
        }
    } catch (PDOException $e) {
        $error_message = "Error en la base de datos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icono-ic.png" sizes="96x96" type="image/x-icon">
    <link rel="stylesheet" href="css/reset.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Iniciar Sesión - ControlGastos</title>
</head>

<body>
    <!-- Fondo animado -->
    <div class="background-animation">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>

    <main class="login-container">
        <!-- Panel izquierdo con imagen y diseño moderno -->
        <div class="info-panel">
            <div class="panel-overlay"></div>
            <div class="panel-content">
                <div class="brand-section">
                    <div class="brand-logo">
                        <!-- Logo con fallback en caso de error -->
                        <img src="logo_finabiz.png" alt="ControlGastos Logo" class="logo-img" onerror="this.src='icono-ic.png'; this.onerror=null;">
                    </div>
                    <h1 class="brand-title">Finabiz</h1>
                    <p class="brand-subtitle">Tu asistente financiero personal</p>
                </div>

                <div class="visual-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-trending-up"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">95%</span>
                            <span class="stat-label">Usuarios satisfechos</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">$2.5M</span>
                            <span class="stat-label">Ahorros generados</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">10K+</span>
                            <span class="stat-label">Usuarios activos</span>
                        </div>
                    </div>
                </div>

                <div class="hero-illustration">
                    <div class="floating-cards">
                        <div class="money-card card-1">
                            <i class="fas fa-dollar-sign"></i>
                            <span>+$1,250</span>
                        </div>
                        <div class="money-card card-2">
                            <i class="fas fa-chart-line"></i>
                            <span>+15%</span>
                        </div>
                        <div class="money-card card-3">
                            <i class="fas fa-target"></i>
                            <span>Meta: 80%</span>
                        </div>
                    </div>
                    
                    <div class="phone-mockup">
                        <div class="phone-screen">
                            <div class="app-header">
                                <div class="app-balance">$4,250.00</div>
                                <div class="app-status">Balance disponible</div>
                            </div>
                            <div class="app-chart">
                                <div class="chart-bar" style="height: 40%"></div>
                                <div class="chart-bar" style="height: 70%"></div>
                                <div class="chart-bar" style="height: 55%"></div>
                                <div class="chart-bar" style="height: 80%"></div>
                                <div class="chart-bar" style="height: 60%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="trust-indicators">
                    <div class="trust-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>100% Seguro</span>
                    </div>
                    <div class="trust-item">
                        <i class="fas fa-lock"></i>
                        <span>Datos Encriptados</span>
                    </div>
                    <div class="trust-item">
                        <i class="fas fa-headset"></i>
                        <span>Soporte 24/7</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel derecho con formulario -->
        <div class="form-panel">
            <div class="form-container">
                <!-- Header del formulario -->
                <div class="form-header">
                    <!-- Logo también en el formulario -->
                    <div class="form-logo">
                        <img src="icono-ic.png" alt="ControlGastos" class="form-logo-img">
                    </div>
                    <h2>¡Bienvenido de nuevo!</h2>
                    <p>Inicia sesión en tu cuenta para continuar</p>
                </div>

                <!-- Mensaje de error -->
                <?php if (!empty($error_message)): ?>
                <div class="error-message" id="errorMessage">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?php echo $error_message; ?></span>
                    <button onclick="hideError()" class="error-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="login-form" id="loginForm">
                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
                            <div class="input-line"></div>
                        </div>
                        <span class="error-text" id="emailError"></span>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" id="password" placeholder="Contraseña" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                            </button>
                            <div class="input-line"></div>
                        </div>
                        <span class="error-text" id="passwordError"></span>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" id="rememberMe">
                            <span class="checkmark"></span>
                            <span class="label-text">Recordarme</span>
                        </label>
                        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="login-btn" id="loginBtn">
                        <span class="btn-content">
                            <span class="btn-text">Iniciar Sesión</span>
                            <span class="btn-loading">
                                <span class="spinner"></span>
                                <span class="loading-text">Iniciando sesión...</span>
                            </span>
                        </span>
                    </button>

                    <div class="form-divider">
                        <span>o continúa con</span>
                    </div>

                    <div class="social-login">
                        <!-- BOTÓN DE GOOGLE ORIGINAL FUNCIONANDO -->
                        <a href="social_login.php?provider=google" class="social-btn google-btn" role="button">
                            <i class="fab fa-google"></i>
                            <span>Continuar con Google</span>
                        </a>
                    </div>

                    <div class="signup-link">
                        <p>¿No tienes una cuenta? <a href="registrarse.php">Regístrate aquí</a></p>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="form-footer">
                <p>&copy; 2024 ControlGastos. Todos los derechos reservados.</p>
                <div class="footer-links">
                    <a href="#">Términos</a>
                    <a href="#">Privacidad</a>
                    <a href="#">Soporte</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Prevenir múltiples envíos del formulario
        let isSubmitting = false;

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            initializeForm();
            addAnimations();
            checkLogoError();
        });

        // Verificar error del logo
        function checkLogoError() {
            const logo = document.querySelector('.logo-img');
            if (logo && logo.naturalWidth === 0) {
                logo.src = 'icono-ic.png';
            }
        }

        // Inicializar formulario
        function initializeForm() {
            const inputs = document.querySelectorAll('.input-container input');
            const form = document.getElementById('loginForm');
            
            inputs.forEach(input => {
                // Efecto de focus en inputs
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    if (this.value === '') {
                        this.parentElement.classList.remove('focused');
                    }
                    validateField(this);
                });

                input.addEventListener('input', function() {
                    if (this.value !== '') {
                        this.parentElement.classList.add('filled');
                    } else {
                        this.parentElement.classList.remove('filled');
                    }
                });
            });

            // Manejar envío del formulario
            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }

                const email = document.getElementById('email');
                const password = document.getElementById('password');
                
                const emailValid = validateField(email);
                const passwordValid = validateField(password);
                
                if (!emailValid || !passwordValid || email.value === '' || password.value === '') {
                    e.preventDefault();
                    return false;
                }

                // Mostrar estado de carga
                isSubmitting = true;
                showLoading();
            });
        }

        // Validar campo individual
        function validateField(field) {
            const fieldType = field.type;
            const fieldValue = field.value;
            const errorElement = document.getElementById(field.id + 'Error');
            let isValid = true;
            let errorMessage = '';

            if (fieldType === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(fieldValue)) {
                    isValid = false;
                    errorMessage = 'Por favor ingresa un correo válido';
                }
            } else if (fieldType === 'password') {
                if (fieldValue.length < 6) {
                    isValid = false;
                    errorMessage = 'La contraseña debe tener al menos 6 caracteres';
                }
            }

            // Mostrar/ocultar error
            if (!isValid && fieldValue !== '') {
                errorElement.textContent = errorMessage;
                errorElement.style.display = 'block';
                field.parentElement.classList.add('error');
            } else {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
                field.parentElement.classList.remove('error');
            }

            return isValid;
        }

        // Toggle password visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Mostrar loading en botón - MEJORADO
        function showLoading() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        }

        // Ocultar mensaje de error
        function hideError() {
            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 300);
            }
        }

        // Agregar animaciones
        function addAnimations() {
            // Animación de entrada para elementos
            const animatedElements = document.querySelectorAll('.form-header, .login-form, .form-footer');
            animatedElements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.6s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, 200 * (index + 1));
            });

            // Animación de las formas del fondo
            const shapes = document.querySelectorAll('.shape');
            shapes.forEach(shape => {
                const duration = Math.random() * 10 + 10;
                const delay = Math.random() * 5;
                shape.style.animationDuration = `${duration}s`;
                shape.style.animationDelay = `${delay}s`;
            });
        }

        // Efecto de ripple en botones
        function createRipple(event) {
            const button = event.currentTarget;
            
            // No crear ripple si el botón está en loading
            if (button.classList.contains('loading')) {
                return;
            }

            const circle = document.createElement('span');
            const diameter = Math.max(button.clientWidth, button.clientHeight);
            const radius = diameter / 2;

            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
            circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
            circle.classList.add('ripple');

            const ripple = button.getElementsByClassName('ripple')[0];
            if (ripple) {
                ripple.remove();
            }

            button.appendChild(circle);
        }

        // Agregar efecto ripple a botones
        document.querySelectorAll('.login-btn, .social-btn').forEach(btn => {
            btn.addEventListener('click', createRipple);
        });

        // Auto-ocultar mensaje de error después de 5 segundos
        <?php if (!empty($error_message)): ?>
        setTimeout(() => {
            hideError();
        }, 5000);
        <?php endif; ?>
    </script>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            
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
            
            --white: #ffffff;
            --black: #000000;
            
            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-secondary: 'Poppins', sans-serif;
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            font-family: var(--font-primary);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Fondo animado */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            border-radius: 50%;
            animation: float infinite ease-in-out;
        }

        .shape-1 {
            width: 200px;
            height: 200px;
            background: var(--primary);
            top: 10%;
            left: 10%;
            animation-duration: 15s;
        }

        .shape-2 {
            width: 150px;
            height: 150px;
            background: var(--secondary);
            top: 70%;
            right: 15%;
            animation-duration: 12s;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 100px;
            height: 100px;
            background: var(--success);
            bottom: 20%;
            left: 20%;
            animation-duration: 18s;
            animation-delay: -10s;
        }

        .shape-4 {
            width: 120px;
            height: 120px;
            background: var(--warning);
            top: 30%;
            right: 30%;
            animation-duration: 14s;
            animation-delay: -7s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg) scale(1);
            }
            25% {
                transform: translate(30px, -30px) rotate(90deg) scale(1.1);
            }
            50% {
                transform: translate(-20px, 20px) rotate(180deg) scale(0.9);
            }
            75% {
                transform: translate(-30px, -20px) rotate(270deg) scale(1.05);
            }
        }

        /* Layout principal */
        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* Panel izquierdo rediseñado */
        .info-panel {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .panel-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="4"/></g></svg>');
            opacity: 0.3;
        }

        .panel-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            text-align: center;
        }

        .brand-section {
            margin-bottom: 3rem;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: logoFloat 3s ease-in-out infinite;
            overflow: hidden;
        }

        /* Estilos para el logo */
        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 10px;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .brand-title {
            font-family: var(--font-secondary);
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .brand-subtitle {
            font-size: clamp(0.9rem, 2vw, 1.1rem);
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
            margin-bottom: 2rem;
        }

        /* Estadísticas visuales */
        .visual-stats {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 1rem 0.5rem;
            text-align: center;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 90px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.2);
        }

        .stat-icon {
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            color: white;
            font-size: 0.9rem;
        }

        .stat-number {
            display: block;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            line-height: 1.2;
        }

        /* Ilustración hero */
        .hero-illustration {
            position: relative;
            height: 200px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .floating-cards {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .money-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 0.5rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--primary);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            animation: cardFloat 4s ease-in-out infinite;
            white-space: nowrap;
        }

        .card-1 {
            top: 15%;
            left: 5%;
            animation-delay: 0s;
        }

        .card-2 {
            top: 65%;
            right: 10%;
            animation-delay: -1.5s;
        }

        .card-3 {
            top: 40%;
            left: 50%;
            transform: translateX(-50%);
            animation-delay: -3s;
        }

        @keyframes cardFloat {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-10px) rotate(2deg);
            }
        }

        .phone-mockup {
            width: 120px;
            height: 170px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 0.75rem 0.5rem;
            position: relative;
            z-index: 1;
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .app-header {
            text-align: center;
        }

        .app-balance {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .app-status {
            font-size: 0.65rem;
            color: var(--gray-600);
        }

        .app-chart {
            display: flex;
            align-items: end;
            justify-content: space-between;
            height: 50px;
            gap: 0.2rem;
            flex: 1;
        }

        .chart-bar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 2px;
            flex: 1;
            animation: barGrow 2s ease-in-out infinite;
        }

        .chart-bar:nth-child(1) { animation-delay: 0s; }
        .chart-bar:nth-child(2) { animation-delay: 0.2s; }
        .chart-bar:nth-child(3) { animation-delay: 0.4s; }
        .chart-bar:nth-child(4) { animation-delay: 0.6s; }
        .chart-bar:nth-child(5) { animation-delay: 0.8s; }

        @keyframes barGrow {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(1.1); }
        }

        /* Indicadores de confianza */
        .trust-indicators {
            display: flex;
            justify-content: space-around;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .trust-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.4rem;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.75rem;
            font-weight: 500;
            flex: 1;
            min-width: 70px;
        }

        .trust-item i {
            font-size: 1.2rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .trust-item:hover i {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        /* Panel derecho */
        .form-panel {
            background: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow-y: auto;
        }

        .form-container {
            flex: 1;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Logo en el formulario */
        .form-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 12px;
            padding: 5px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .form-header h2 {
            font-family: var(--font-secondary);
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--gray-600);
            font-size: clamp(0.9rem, 2vw, 1rem);
        }

        /* Mensaje de error */
        .error-message {
            background: linear-gradient(135deg, #fef2f2 0%, #fce7e6 100%);
            border: 1px solid #fecaca;
            color: var(--danger);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
            position: relative;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        .error-close {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            margin-left: auto;
            padding: 4px;
            border-radius: 4px;
            transition: var(--transition);
        }

        .error-close:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        /* Formulario */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            color: var(--gray-400);
            font-size: 1rem;
            z-index: 2;
            transition: var(--transition);
        }

        .input-container.focused .input-icon {
            color: var(--primary);
        }

        .input-container input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius);
            font-size: 1rem;
            background: var(--gray-50);
            color: var(--gray-800);
            transition: var(--transition);
            outline: none;
        }

        .input-container input:focus {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .input-container.error input {
            border-color: var(--danger);
            background: #fef2f2;
        }

        .input-line {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
            transition: var(--transition);
        }

        .input-container.focused .input-line {
            width: 100%;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: var(--transition);
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--gray-600);
            background: var(--gray-100);
        }

        .error-text {
            color: var(--danger);
            font-size: 0.875rem;
            display: none;
            margin-top: 0.25rem;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0.5rem 0;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.9rem;
            color: var(--gray-700);
        }

        .remember-me input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid var(--gray-300);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .remember-me input:checked + .checkmark {
            background: var(--primary);
            border-color: var(--primary);
        }

        .remember-me input:checked + .checkmark::after {
            content: '✓';
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .forgot-password:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Botón de login MEJORADO */
        .login-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .login-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .login-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .login-btn:disabled {
            cursor: not-allowed;
            opacity: 0.9;
        }

        .btn-content {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-text {
            transition: opacity 0.3s ease;
        }

        .btn-loading {
            position: absolute;
            display: none;
            align-items: center;
            gap: 0.5rem;
        }

        .login-btn.loading .btn-text {
            opacity: 0;
        }

        .login-btn.loading .btn-loading {
            display: flex;
        }

        /* Spinner mejorado */
        .spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            font-size: 0.95rem;
            font-weight: 600;
        }

        /* Ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .form-divider {
            position: relative;
            text-align: center;
            margin: 1rem 0;
        }

        .form-divider::before,
        .form-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: var(--gray-200);
        }

        .form-divider::before {
            left: 0;
        }

        .form-divider::after {
            right: 0;
        }

        .form-divider span {
            background: white;
            padding: 0 1rem;
            color: var(--gray-500);
            font-size: 0.875rem;
        }

        /* Social login - RESTAURADO AL ORIGINAL */
        .social-login {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.875rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius);
            background: white;
            color: var(--gray-700);
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .social-btn:hover {
            border-color: var(--gray-300);
            background: var(--gray-50);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .google-btn i {
            font-size: 1.25rem;
        }

        .signup-link {
            text-align: center;
            margin-top: 1rem;
        }

        .signup-link p {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        .signup-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .signup-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Footer */
        .form-footer {
            padding: 1.5rem 1rem;
            border-top: 1px solid var(--gray-200);
            text-align: center;
            background: var(--gray-50);
        }

        .form-footer p {
            color: var(--gray-500);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        /* ========== RESPONSIVE MEJORADO ========== */

        /* Tablets grandes y laptops pequeñas */
        @media (max-width: 1200px) {
            .panel-content {
                max-width: 350px;
                padding: 1.5rem;
            }
            
            .hero-illustration {
                height: 180px;
            }
            
            .phone-mockup {
                width: 110px;
                height: 160px;
            }
        }

        /* Tablets */
        @media (max-width: 1024px) {
            .login-container {
                grid-template-columns: 1fr;
                min-height: 100vh;
            }

            .info-panel {
                display: none;
            }

            .form-panel {
                overflow-y: auto;
                justify-content: flex-start;
            }
            
            .form-container {
                padding: 3rem 2rem;
                max-width: 450px;
            }
        }

        /* Tablets pequeñas */
        @media (max-width: 768px) {
            .form-container {
                padding: 2rem 1.5rem;
            }
            
            .form-header h2 {
                font-size: 1.75rem;
            }
            
            .visual-stats {
                gap: 0.5rem;
            }
            
            .stat-card {
                min-width: 80px;
                padding: 0.75rem 0.25rem;
            }
            
            .stat-number {
                font-size: 1.1rem;
            }
            
            .stat-label {
                font-size: 0.65rem;
            }
        }

        /* Móviles grandes */
        @media (max-width: 480px) {
            .form-container {
                padding: 1.5rem 1rem;
            }
            
            .form-header {
                margin-bottom: 1.5rem;
            }
            
            .form-logo {
                width: 70px;
                height: 70px;
            }
            
            .form-header h2 {
                font-size: 1.5rem;
            }
            
            .form-header p {
                font-size: 0.9rem;
            }
            
            .login-form {
                gap: 1rem;
            }
            
            .input-container input {
                padding: 0.875rem 0.875rem 0.875rem 2.5rem;
                font-size: 0.9rem;
            }
            
            .input-icon {
                left: 0.875rem;
                font-size: 0.9rem;
            }
            
            .password-toggle {
                right: 0.875rem;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            
            .forgot-password {
                align-self: flex-end;
            }
            
            .login-btn {
                padding: 0.875rem;
                font-size: 0.95rem;
            }
            
            .social-btn {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
            
            .form-footer {
                padding: 1rem;
            }
            
            .footer-links {
                gap: 0.75rem;
            }
            
            .footer-links a {
                font-size: 0.8rem;
            }
        }

        /* Móviles muy pequeños */
        @media (max-width: 360px) {
            .form-container {
                padding: 1rem 0.75rem;
            }
            
            .form-header h2 {
                font-size: 1.3rem;
            }
            
            .form-header p {
                font-size: 0.85rem;
            }
            
            .input-container input {
                padding: 0.75rem 0.75rem 0.75rem 2.25rem;
            }
            
            .input-icon {
                left: 0.75rem;
            }
            
            .password-toggle {
                right: 0.75rem;
            }
            
            .login-btn {
                padding: 0.75rem;
            }
            
            .form-divider span {
                font-size: 0.8rem;
            }
        }

        /* Alturas pequeñas */
        @media (max-height: 700px) {
            .form-container {
                padding: 1.5rem 1rem;
                justify-content: flex-start;
            }
            
            .form-header {
                margin-bottom: 1.5rem;
            }
            
            .login-form {
                gap: 1rem;
            }
        }

        /* Orientación landscape en móviles */
        @media (max-height: 600px) and (orientation: landscape) {
            .form-panel {
                overflow-y: auto;
            }
            
            .form-container {
                padding: 1rem;
            }
            
            .form-header {
                margin-bottom: 1rem;
            }
            
            .form-logo {
                width: 50px;
                height: 50px;
                margin-bottom: 0.5rem;
            }
            
            .form-header h2 {
                font-size: 1.2rem;
                margin-bottom: 0.25rem;
            }
            
            .form-header p {
                font-size: 0.8rem;
                margin-bottom: 0.5rem;
            }
            
            .login-form {
                gap: 0.75rem;
            }
            
            .input-container input {
                padding: 0.6rem 0.6rem 0.6rem 2.2rem;
                font-size: 0.85rem;
            }
            
            .form-options {
                margin: 0.25rem 0;
            }
        }

        /* Pantallas muy grandes */
        @media (min-width: 1600px) {
            .form-container {
                max-width: 450px;
                padding: 4rem 2rem;
            }
            
            .panel-content {
                max-width: 500px;
                padding: 3rem;
            }
        }

        /* Mejoras de accesibilidad */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Enfoque visible para accesibilidad */
        button:focus-visible,
        input:focus-visible,
        a:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>

</body>
</html>