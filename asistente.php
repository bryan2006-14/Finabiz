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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente Financiero IA - Finabiz</title>
        <link rel="shortcut icon" href="icono-ic.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
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
            --white: #ffffff;
            --black: #000000;
            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-primary);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--gray-800);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--gray-200);
            padding: 2rem 0;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .brand-logo {
            width: 100%;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            padding: 12px 18px;
        }

        .brand-logo-img {
            max-width: 90%;
            max-height: 100%;
            object-fit: contain;
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

        /* CONTENIDO PRINCIPAL */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
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

        /* CHATBOT PAGE */
        .chatbot-page {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 2rem;
            height: calc(100vh - 280px);
        }

        .chat-info-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            padding: 0;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            height: 100%;
        }

        #ad-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        #ad-container > div {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            position: relative;
        }

        #ad-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }

        .ad-content {
            position: absolute;
            bottom: 30px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        .ad-button {
            display: inline-block;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }

        .ad-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,123,255,0.4);
        }

        /* Chatbot Container */
        .chatbot-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-xl);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .chatbot-container.expanded {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            bottom: 0;
            width: calc(100vw - var(--sidebar-width));
            height: 100vh;
            border-radius: 0;
            z-index: 999;
            box-shadow: -5px 0 20px rgba(0,0,0,0.2);
        }

        .chatbot-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bot-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .bot-avatar {
            position: relative;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .avatar-status {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            background: var(--success);
            border: 2px solid var(--primary);
            border-radius: 50%;
        }

        .bot-details {
            display: flex;
            flex-direction: column;
        }

        .bot-name {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .bot-status {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .chatbot-controls {
            display: flex;
            gap: 0.5rem;
        }

        .control-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            font-size: 1rem;
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .chatbot-body {
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }

        .chat-suggestions {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            background: var(--gray-50);
        }

        .suggestion {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: white;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }

        .suggestion:hover {
            background: var(--primary);
            color: white;
            transform: translateX(5px);
            box-shadow: var(--shadow-md);
        }

        .suggestion i {
            font-size: 1.1rem;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background: var(--gray-50);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message {
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
            animation: messageSlide 0.3s ease-out;
        }

        .user-message {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .bot-message .message-avatar {
            background: var(--primary);
            color: white;
        }

        .user-message .message-avatar {
            background: var(--gray-300);
            color: var(--gray-700);
        }

        .message-content {
            max-width: 70%;
            padding: 1rem 1.25rem;
            border-radius: 18px;
            box-shadow: var(--shadow-sm);
            position: relative;
        }

        .bot-message .message-content {
            background: white;
            color: var(--gray-800);
            border-bottom-left-radius: 4px;
        }

        .user-message .message-content {
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-content p {
            margin: 0;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.5rem;
            display: block;
        }

        .chat-input-area {
            border-top: 1px solid var(--gray-200);
            padding: 1.5rem;
            background: white;
        }

        .input-container {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            background: var(--gray-100);
            border-radius: 25px;
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }

        .input-container:focus-within {
            background: white;
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        #chat-input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.5rem;
            font-size: 0.95rem;
            outline: none;
            resize: none;
            max-height: 120px;
            font-family: inherit;
        }

        .send-button {
            background: var(--primary);
            border: none;
            color: white;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .send-button:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        .send-button:disabled {
            background: var(--gray-400);
            cursor: not-allowed;
            transform: none;
        }

        .stop-button {
            background: var(--danger);
        }

        .stop-button:hover {
            background: #dc2626;
        }

        .typing-indicator {
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
            margin-bottom: 1rem;
        }

        .typing-dots {
            display: flex;
            gap: 0.25rem;
            padding: 1rem 1.25rem;
            background: white;
            border-radius: 18px;
            border-bottom-left-radius: 4px;
            box-shadow: var(--shadow-sm);
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: var(--gray-400);
            border-radius: 50%;
            animation: typingDot 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* MENÚ MÓVIL */
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
            background: rgba(239,68,68,0.1);
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
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
        }

        /* Animaciones */
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

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                width: 220px;
            }
            .main-content {
                margin-left: 220px;
                padding: 1.5rem;
            }
            .chatbot-page {
                grid-template-columns: 1fr;
                height: auto;
                min-height: calc(100vh - 200px);
            }
            .chat-info-panel {
                order: 2;
            }
            .chatbot-container {
                order: 1;
                height: 60vh;
            }
            .chatbot-container.expanded {
                left: 220px;
                width: calc(100vw - 220px);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -100%;
                width: 280px;
                transition: left 0.3s ease;
                z-index: 1100;
                box-shadow: 2px 0 20px rgba(0,0,0,0.3);
            }
            .sidebar.active {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            .mobile-menu-btn, .sidebar-close-btn {
                display: flex;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem;
                margin-top: 4rem;
            }
            .page-title {
                font-size: 1.5rem;
                width: 100%;
            }
            .user-info {
                width: 100%;
                justify-content: space-between;
            }
            .chatbot-page {
                padding: 0;
            }
            .chat-info-panel {
                padding: 1.5rem;
            }
            .chatbot-header {
                padding: 1.25rem;
            }
            .bot-avatar {
                width: 44px;
                height: 44px;
                font-size: 1.3rem;
            }
            .chat-suggestions,
            .chat-messages,
            .chat-input-area {
                padding: 1.25rem;
            }
            .message-content {
                max-width: 85%;
            }
            .chatbot-container.expanded {
                left: 0;
                width: 100vw;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 0.75rem;
            }
            .header {
                padding: 0.875rem;
                border-radius: 8px;
                margin-bottom: 1rem;
            }
            .page-title {
                font-size: 1.25rem;
            }
            .user-avatar img {
                width: 36px;
                height: 36px;
            }
            .chat-info-panel {
                padding: 1rem;
            }
            .chatbot-header {
                padding: 1rem;
            }
            .bot-avatar {
                width: 40px;
                height: 40px;
            }
            .chat-suggestions,
            .chat-messages,
            .chat-input-area {
                padding: 1rem;
            }
            .message-content {
                max-width: 90%;
                padding: 0.875rem 1rem;
            }
            .input-container {
                padding: 0.625rem 0.875rem;
            }
            .send-button {
                width: 40px;
                height: 40px;
            }
            .mobile-menu-btn {
                width: 45px;
                height: 45px;
                font-size: 1.1rem;
                top: 0.75rem;
                left: 0.75rem;
            }
            .sidebar {
                width: 260px;
            }
        }

        /* Scrollbar personalizado */
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: var(--gray-400);
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: var(--gray-500);
        }
    </style>
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
                <a href="calculadora.php" class="nav-link" onclick="closeSidebarOnMobile()">
                    <i class="fas fa-calculator"></i>
                    <span>Calculadora</span>
                </a>
                <a href="asistente.php" class="nav-link active" onclick="closeSidebarOnMobile()">
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
            <h1 class="page-title">Asistente Financiero IA</h1>
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

        <!-- Chatbot Page -->
        <div class="chatbot-page">
            <!-- Panel de información -->
            <div class="chat-info-panel">
                <div id="ad-container">
                  <div >
                   <img src="trii.gif" alt="Trii Gif" loading="lazy">
                    <div class="ad-content">
                      <a href="https://trii.pe/" target="_blank" class="ad-button">Conoce más</a>
                    </div>
                  </div>
                </div>
            </div>

            <!-- Chatbot Container -->
            <div class="chatbot-container" id="chatbot-container" >
                <div class="chatbot-header">
                    <div class="bot-info">
                        <div class="bot-avatar">
                            <i class="fas fa-robot"></i>
                            <div class="avatar-status"></div>
                        </div>
                        <div class="bot-details">
                            <span class="bot-name">Asistente Financiero</span>
                            <span class="bot-status">En línea - Listo para ayudarte</span>
                        </div>
                    </div>
                    <div class="chatbot-controls">
                        <button id="expand-chat" class="control-btn" title="Expandir">
                            <i class="fas fa-expand"></i>
                        </button>
                        <button id="minimize-chat" class="control-btn" title="Minimizar" style="display: none;">
                            <i class="fas fa-compress"></i>
                        </button>
                    </div>
                </div>

                <div class="chatbot-body">
                    <div class="chat-suggestions">
                        <div class="suggestion" onclick="sendQuickMessage('¿Cómo puedo ahorrar más dinero?')">
                            <i class="fas fa-piggy-bank"></i>
                            <span>Consejos de ahorro</span>
                        </div>
                        <div class="suggestion" onclick="sendQuickMessage('Analiza mis gastos y dame recomendaciones')">
                            <i class="fas fa-chart-bar"></i>
                            <span>Análisis de gastos</span>
                        </div>

                    </div>

                    <div id="chat-messages" class="chat-messages">
                        <!-- Los mensajes se insertarán aquí dinámicamente -->
                    </div>

                    <div class="chat-input-area">
                        <div class="input-container">
                            <textarea id="chat-input" placeholder="Escribe tu pregunta sobre finanzas..." rows="1" maxlength="500"></textarea>
                            <button id="stop-btn" class="send-button stop-button" title="Detener respuesta" style="display: none;">
                                <i class="fas fa-stop"></i>
                            </button>
                            <button id="send-btn" class="send-button" title="Enviar mensaje">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Variables globales
        let isTyping = false;
        let typingInterval = null;
        let isExpanded = false;

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            initializeMobileMenu();
            showWelcomeMessage();
            
            // Autoajustar altura del textarea
            const chatInput = document.getElementById('chat-input');
            chatInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });

        function initializeApp() {
            initializeChat();
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

        function initializeChat() {
            const expandBtn = document.getElementById('expand-chat');
            const minimizeBtn = document.getElementById('minimize-chat');
            const sendBtn = document.getElementById('send-btn');
            const stopBtn = document.getElementById('stop-btn');
            const chatInput = document.getElementById('chat-input');

            // Event listeners
            if (expandBtn) expandBtn.addEventListener('click', expandChat);
            if (minimizeBtn) minimizeBtn.addEventListener('click', minimizeChat);
            if (sendBtn) sendBtn.addEventListener('click', sendMessage);
            if (stopBtn) stopBtn.addEventListener('click', stopTyping);
            
            if (chatInput) {
                chatInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey && !isTyping) {
                        e.preventDefault();
                        sendMessage();
                    }
                });
            }
        }

        function showWelcomeMessage() {
            setTimeout(() => {
                addBotMessage("¡Hola! 👋 Soy tu asistente financiero personal. Puedo ayudarte con:");
                
                setTimeout(() => {
                    addBotMessage("💡 <strong>Consejos de ahorro</strong> - Estrategias para maximizar tus ahorros<br>📊 <strong>Análisis de gastos</strong> - Identificar patrones y oportunidades<br>💰 <strong>Presupuestos</strong> - Crear y seguir planes financieros<br>🎯 <strong>Metas financieras</strong> - Planificar y alcanzar tus objetivos<br>📈 <strong>Inversiones</strong> - Orientación básica sobre inversiones");
                    
                    setTimeout(() => {
                        addBotMessage("¿En qué puedo ayudarte hoy? Puedes escribir tu pregunta o elegir una de las opciones sugeridas.");
                    }, 800);
                }, 800);
            }, 1000);
        }

        function expandChat() {
            const container = document.getElementById('chatbot-container');
            const expandBtn = document.getElementById('expand-chat');
            const minimizeBtn = document.getElementById('minimize-chat');
            
            if (!isExpanded) {
                container.classList.add('expanded');
                expandBtn.style.display = 'none';
                minimizeBtn.style.display = 'flex';
                isExpanded = true;
            }
        }

        function minimizeChat() {
            const container = document.getElementById('chatbot-container');
            const expandBtn = document.getElementById('expand-chat');
            const minimizeBtn = document.getElementById('minimize-chat');
            
            if (isExpanded) {
                container.classList.remove('expanded');
                expandBtn.style.display = 'flex';
                minimizeBtn.style.display = 'none';
                isExpanded = false;
            }
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
            
            // Ocultar sugerencias después del primer mensaje
            document.querySelector('.chat-suggestions').style.display = 'none';
            
            addUserMessage(message);
            input.value = '';
            input.style.height = 'auto';
            
            // Deshabilitar entrada mientras el bot responde
            input.disabled = true;
            document.getElementById('send-btn').disabled = true;
            isTyping = true;
            
            // Mostrar botón de detener
            document.getElementById('stop-btn').style.display = 'flex';
            
            showTypingIndicator();
            
            // Simular procesamiento y obtener respuesta
            setTimeout(() => {
                hideTypingIndicator();
                const response = getBotResponse(message);
                simulateTyping(response);
            }, 1000 + Math.random() * 1000); // Tiempo variable para parecer más natural
        }

        function stopTyping() {
            if (typingInterval) {
                clearInterval(typingInterval);
                typingInterval = null;
            }
            hideTypingIndicator();
            
            const input = document.getElementById('chat-input');
            input.disabled = false;
            document.getElementById('send-btn').disabled = false;
            isTyping = false;
            document.getElementById('stop-btn').style.display = 'none';
            
            addBotMessage("He detenido mi respuesta. ¿Hay algo más en lo que pueda ayudarte?");
        }

        function simulateTyping(message) {
            const messagesContainer = document.getElementById('chat-messages');
            const messageElement = document.createElement('div');
            messageElement.className = 'message bot-message';
            messageElement.innerHTML = `
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    <p></p>
                    <span class="message-time">${getCurrentTime()}</span>
                </div>
            `;
            messagesContainer.appendChild(messageElement);
            
            const textElement = messageElement.querySelector('p');
            let index = 0;
            const typingSpeed = 20; // Velocidad de tipeo (ms por caracter)
            
            if (typingInterval) clearInterval(typingInterval);
            
            typingInterval = setInterval(() => {
                if (index < message.length) {
                    // Manejar etiquetas HTML básicas
                    if (message.substring(index, index + 4) === '<br>') {
                        textElement.innerHTML += '<br>';
                        index += 4;
                    } else if (message.substring(index, index + 8) === '<strong>') {
                        textElement.innerHTML += '<strong>';
                        index += 8;
                    } else if (message.substring(index, index + 9) === '</strong>') {
                        textElement.innerHTML += '</strong>';
                        index += 9;
                    } else {
                        textElement.innerHTML += message[index];
                        index++;
                    }
                    scrollToBottom();
                } else {
                    clearInterval(typingInterval);
                    typingInterval = null;
                    
                    const input = document.getElementById('chat-input');
                    input.disabled = false;
                    document.getElementById('send-btn').disabled = false;
                    isTyping = false;
                    document.getElementById('stop-btn').style.display = 'none';
                    
                    // Enfocar el input nuevamente
                    input.focus();
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
                    <i class="fas fa-user"></i>
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
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
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
            
            // Respuestas basadas en palabras clave
            if (lowerMessage.includes('ahorro') || lowerMessage.includes('ahorrar') || lowerMessage.includes('guardar dinero')) {
                return `Te recomiendo seguir estas estrategias de ahorro:<br><br>
                💰 <strong>Regla 50/30/20:</strong> 50% para necesidades, 30% para deseos, 20% para ahorros<br>
                🏦 <strong>Automatiza tus ahorros:</strong> Configura transferencias automáticas a tu cuenta de ahorros<br>
                📱 <strong>Apps de ahorro:</strong> Usa apps que redondeen tus compras y ahorren el cambio<br>
                🎯 <strong>Metas específicas:</strong> Establece metas claras con fechas y montos definidos<br><br>
                ¿Te gustaría que profundice en alguna de estas estrategias?`;
                
            } else if (lowerMessage.includes('gasto') || lowerMessage.includes('analiza') || lowerMessage.includes('donde gasto')) {
                return `Basado en patrones comunes, te sugiero:<br><br>
                📊 <strong>Analiza tus últimos 3 meses:</strong> Identifica categorías donde puedes reducir<br>
                🛒 <strong>Compras impulsivas:</strong> Espera 24 horas antes de compras no esenciales<br>
                🍽️ <strong>Comida fuera:</strong> Cocinar en casa puede ahorrarte hasta 50% en alimentación<br>
                📱 <strong>Suscripciones:</strong> Revisa y cancela suscripciones que no uses regularmente<br><br>
                ¿Quieres que te ayude a crear un plan específico para reducir gastos?`;
                
            } else if (lowerMessage.includes('presupuesto') || lowerMessage.includes('presupuestar')) {
                return `Para crear un presupuesto efectivo:<br><br>
                1️⃣ <strong>Registra todos tus ingresos</strong> (salarios, inversiones, otros)<br>
                2️⃣ <strong>Clasifica tus gastos</strong> en categorías fijas y variables<br>
                3️⃣ <strong>Establece límites realistas</strong> para cada categoría<br>
                4️⃣ <strong>Haz seguimiento semanal</strong> y ajusta según necesidad<br>
                5️⃣ <strong>Incluye ahorros e inversiones</strong> como gasto fijo<br><br>
                ¿Te gustaría que te ayude a estructurar tu presupuesto mensual?`;
                
            } else if (lowerMessage.includes('deuda') || lowerMessage.includes('tarjeta') || lowerMessage.includes('préstamo')) {
                return `Para manejar deudas efectivamente:<br><br>
                🎯 <strong>Método bola de nieve:</strong> Paga la deuda más pequeña primero para ganar momentum<br>
                💳 <strong>Intereses altos primero:</strong> Enfócate en deudas con mayores tasas de interés<br>
                🔄 <strong>Consolidación:</strong> Considera unificar deudas en un solo préstamo con menor interés<br>
                🚫 <strong>Evita nuevas deudas:</strong> Congela el uso de tarjetas de crédito mientras pagas<br><br>
                ¿Tienes deudas específicas que te gustaría analizar?`;
                
            } else if (lowerMessage.includes('inversión') || lowerMessage.includes('invertir') || lowerMessage.includes('inversiones')) {
                return `Conceptos básicos de inversión:<br><br>
                📈 <strong>Diversificación:</strong> No pongas todos tus huevos en una canasta<br>
                ⏳ <strong>Largo plazo:</strong> Las inversiones generalmente dan mejores resultados con tiempo<br>
                🎯 <strong>Define tu perfil de riesgo:</strong> Conservador, moderado o agresivo<br>
                💼 <strong>Opciones comunes:</strong> Fondos indexados, acciones, bienes raíces, criptomonedas<br><br>
                <em>Nota: Esto es orientación general. Consulta con un asesor financiero para recomendaciones personalizadas.</em>`;
                
            } else if (lowerMessage.includes('meta') || lowerMessage.includes('objetivo') || lowerMessage.includes('compra')) {
                return `Para establecer metas financieras efectivas:<br><br>
                🎯 <strong>Usa el método SMART:</strong> Específicas, Medibles, Alcanzables, Relevantes, con Tiempo definido<br>
                💰 <strong>Divide en hitos:</strong> Metas grandes en pasos pequeños y alcanzables<br>
                📅 <strong>Plazos realistas:</strong> Corto (1-12 meses), mediano (1-5 años), largo plazo (5+ años)<br>
                📊 <strong>Seguimiento regular:</strong> Revisa progreso mensual y ajusta según necesidad<br><br>
                ¿Tienes alguna meta específica en mente?`;
                
            } else if (lowerMessage.includes('hola') || lowerMessage.includes('buenas') || lowerMessage.includes('saludos')) {
                return `¡Hola! 👋 Me alegra verte por aquí. Como tu asistente financiero, puedo ayudarte con:<br><br>
                • Consejos de ahorro e inversión<br>
                • Análisis de gastos y presupuestos<br>
                • Estrategias para reducir deudas<br>
                • Planificación de metas financieras<br>
                • Educación financiera básica<br><br>
                ¿En qué aspecto de tus finanzas te gustaría trabajar hoy?`;
                
            } else {
                return `Interesante pregunta. Como asistente financiero, me especializo en:<br><br>
                💰 <strong>Gestión de ahorros</strong> - Estrategias para maximizar tu dinero<br>
                📊 <strong>Análisis de gastos</strong> - Identificar patrones y oportunidades<br>
                🎯 <strong>Planificación de metas</strong> - Alcanzar objetivos financieros<br>
                📈 <strong>Educación financiera</strong> - Conceptos y mejores prácticas<br><br>
                ¿Podrías reformular tu pregunta en alguno de estos temas, o prefieres que te cuente más sobre alguno en específico?`;
            }
        }

        function getCurrentTime() {
            return new Date().toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
        }

        function scrollToBottom() {
            const messagesContainer = document.getElementById('chat-messages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
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