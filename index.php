<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icono-ic.png" sizes="96x96" type="image/x-icon">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Finabiz - Control Financiero Inteligente</title>
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
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-primary);
            color: var(--gray-800);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Fondo animado */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
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

        /* Header */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .logo-text {
            font-family: var(--font-secondary);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--gray-700);
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .login-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10rem 2rem 6rem;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-text h1 {
            font-family: var(--font-secondary);
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .hero-text p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .cta-button {
            background: var(--success);
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .secondary-button {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .secondary-button:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .hero-visual {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .phone-mockup {
            width: 280px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 1.5rem 1rem;
            position: relative;
            z-index: 1;
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .app-header {
            text-align: center;
        }

        .app-balance {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .app-status {
            font-size: 0.9rem;
            color: var(--gray-600);
        }

        .app-chart {
            display: flex;
            align-items: end;
            justify-content: space-between;
            height: 80px;
            gap: 0.3rem;
            flex: 1;
        }

        .chart-bar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 3px;
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
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            animation: cardFloat 4s ease-in-out infinite;
            white-space: nowrap;
        }

        .card-1 {
            top: 10%;
            left: -20%;
            animation-delay: 0s;
        }

        .card-2 {
            top: 60%;
            right: -15%;
            animation-delay: -1.5s;
        }

        .card-3 {
            top: 35%;
            left: 70%;
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

        /* Features Section */
        .features {
            padding: 6rem 2rem;
            background: var(--white);
        }

        .section-title {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 4rem;
        }

        .section-title h2 {
            font-family: var(--font-secondary);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.25rem;
            color: var(--gray-600);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2.5rem 2rem;
            text-align: center;
            transition: var(--transition);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-100);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--gray-600);
            line-height: 1.6;
        }

        /* Testimonials Section */
        .testimonials {
            padding: 6rem 2rem;
            background: var(--gray-50);
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .testimonial-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2.5rem;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .testimonial-text {
            font-style: italic;
            color: var(--gray-700);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .author-info h4 {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
        }

        .author-info p {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        /* CTA Section */
        .cta-section {
            padding: 6rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            text-align: center;
        }

        .cta-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-content h2 {
            font-family: var(--font-secondary);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .cta-content p {
            font-size: 1.25rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
        }

        /* Footer */
        footer {
            background: var(--gray-900);
            color: var(--gray-300);
            padding: 4rem 2rem 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto 3rem;
        }

        .footer-column h3 {
            color: var(--white);
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: var(--gray-400);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--white);
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-800);
            color: var(--gray-500);
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }

            .nav-links {
                display: none;
            }

            .hero-content {
                grid-template-columns: 1fr;
                gap: 3rem;
                text-align: center;
            }

            .hero-buttons {
                justify-content: center;
            }

            .features-grid,
            .testimonial-grid {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--gray-700);
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .nav-links.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: var(--white);
                padding: 1rem;
                box-shadow: var(--shadow-lg);
            }
        }
    </style>
</head>
<body>
    <!-- Fondo animado -->
    <div class="background-animation">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>

    <!-- Header -->
    <header>
        <nav class="navbar">
            <div class="logo-container">
                <img src="icono-ic.png" alt="Finabiz Logo" class="logo-img">
                <div class="logo-text">Finabiz</div>
            </div>
            
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
            
            <ul class="nav-links">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#caracteristicas">Características</a></li>
                <li><a href="#testimonios">Testimonios</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ul>
            
            <button class="login-btn" onclick="window.location.href='login.php'">Iniciar Sesión</button>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Control Financiero al Alcance de tu Mano</h1>
                <p>Gestiona tus finanzas de manera inteligente con Finabiz. Toma el control de tu futuro financiero hoy mismo.</p>
                <div class="hero-buttons">
                    <a href="#contacto" class="cta-button">Comenzar Gratis</a>
                    <a href="#caracteristicas" class="secondary-button">Conocer Más</a>
                </div>
            </div>
            <div class="hero-visual">
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
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="caracteristicas">
        <div class="section-title">
            <h2>Todo lo que Necesitas para tu Control Financiero</h2>
            <p>Finabiz te ofrece herramientas poderosas para gestionar tus finanzas de manera eficiente</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-trending-up"></i>
                </div>
                <h3>Análisis Avanzado</h3>
                <p>Obtén informes detallados y análisis predictivos para tomar mejores decisiones financieras basadas en datos reales.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <h3>Ahorro Inteligente</h3>
                <p>Configura metas de ahorro y recibe recomendaciones personalizadas para optimizar tus recursos financieros.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Seguridad Total</h3>
                <p>Tus datos están protegidos con encriptación de grado bancario y autenticación de múltiples factores.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Acceso Multiplataforma</h3>
                <p>Gestiona tus finanzas desde cualquier dispositivo, en cualquier momento y lugar.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <h3>Sincronización Automática</h3>
                <p>Conecta tus cuentas bancarias y tarjetas para una sincronización automática y en tiempo real.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Soporte 24/7</h3>
                <p>Nuestro equipo de soporte está disponible para ayudarte en cualquier momento que lo necesites.</p>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonios">
        <div class="section-title">
            <h2>Lo que Dicen Nuestros Usuarios</h2>
            <p>Descubre cómo Finabiz ha transformado la gestión financiera de miles de personas</p>
        </div>
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <p class="testimonial-text">"Finabiz ha revolucionado la forma en que gestiono mis finanzas personales. Ahora tengo un control total sobre mis ingresos y gastos, y he logrado ahorrar un 30% más que antes."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">MG</div>
                    <div class="author-info">
                        <h4>María González</h4>
                        <p>Usuaria desde 2023</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <p class="testimonial-text">"La facilidad de uso y las funcionalidades de análisis han mejorado significativamente nuestra toma de decisiones financieras en la empresa. ¡Una herramienta imprescindible!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">CR</div>
                    <div class="author-info">
                        <h4>Carlos Rodríguez</h4>
                        <p>CEO de TechSolutions</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <p class="testimonial-text">"Desde que implementamos Finabiz, hemos reducido nuestros costos operativos en un 15%. La plataforma es intuitiva y el soporte siempre está disponible cuando lo necesitamos."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">AM</div>
                    <div class="author-info">
                        <h4>Ana Martínez</h4>
                        <p>Gerente de Finanzas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="contacto">
        <div class="cta-content">
            <h2>Comienza tu Viaje Financiero Hoy</h2>
            <p>Únete a miles de usuarios que ya están tomando el control de sus finanzas con Finabiz</p>
            <a href="login.php" class="cta-button">Crear Cuenta Gratis</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>Finabiz</h3>
                <p>Soluciones financieras inteligentes para personas y empresas. Toma el control de tu futuro financiero.</p>
            </div>
            <div class="footer-column">
                <h3>Enlaces Rápidos</h3>
                <ul class="footer-links">
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#caracteristicas">Características</a></li>
                    <li><a href="#testimonios">Testimonios</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Legal</h3>
                <ul class="footer-links">
                    <li><a href="#">Términos del Servicio</a></li>
                    <li><a href="#">Política de Privacidad</a></li>
                    <li><a href="#">Cookies</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contacto</h3>
                <ul class="footer-links">
                    <li>Email: info@finabiz.com</li>
                    <li>Teléfono: +1 (555) 123-4567</li>
                    <li>Dirección: Av. Principal 123, Ciudad</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2024 Finabiz. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const navLinks = document.querySelector('.nav-links');

            mobileMenuBtn.addEventListener('click', function() {
                navLinks.classList.toggle('active');
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        // Close mobile menu if open
                        navLinks.classList.remove('active');
                    }
                });
            });

            // Header scroll effect
            window.addEventListener('scroll', function() {
                const header = document.querySelector('header');
                if (window.scrollY > 100) {
                    header.style.background = 'rgba(255, 255, 255, 0.98)';
                    header.style.boxShadow = 'var(--shadow-md)';
                } else {
                    header.style.background = 'rgba(255, 255, 255, 0.95)';
                    header.style.boxShadow = 'var(--shadow-sm)';
                }
            });
        });
    </script>
</body>
</html>