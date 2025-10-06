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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Finabiz - Tu Asistente Financiero Personal con IA</title>
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
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            
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

/* Contenedor para los dos botones */
.buttons-container {
    display: flex;
    justify-content: center; /* centra los botones */
    align-items: center;
    gap: 15px; /* espacio entre ellos */
    margin-top: 20px;
}

/* Estilo base para ambos botones */
.login-btn,
.register-btn {
    font-family: 'Poppins', sans-serif;
    padding: 10px 20px;
    border-radius: 30px;
    cursor: pointer;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

/* Botón de iniciar sesión */
.login-btn {
    background-color: #007bff;
    color: white;
    border: none;
}

.login-btn:hover {
    background-color: #0056b3;
}

/* Botón de registrarse (transparente y gris) */
.register-btn {
    background-color: transparent;
    color: #555;
    border: 2px solid #ccc;
}

.register-btn:hover {
    color: #333;
    border-color: #888;
    background-color: rgba(0, 0, 0, 0.05);
}

        
        /* Fondo animado mejorado */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
        }

        .shape {
            position: absolute;
            opacity: 0.08;
            border-radius: 50%;
            animation: float infinite ease-in-out;
            filter: blur(40px);
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            top: 10%;
            left: 10%;
            animation-duration: 20s;
        }

        .shape-2 {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--info) 100%);
            top: 60%;
            right: 10%;
            animation-duration: 18s;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);
            bottom: 15%;
            left: 25%;
            animation-duration: 22s;
            animation-delay: -10s;
        }

        .shape-4 {
            width: 180px;
            height: 180px;
            background: linear-gradient(135deg, var(--warning) 0%, #fbbf24 100%);
            top: 40%;
            right: 35%;
            animation-duration: 16s;
            animation-delay: -7s;
        }

        .shape-5 {
            width: 220px;
            height: 220px;
            background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%);
            bottom: 30%;
            right: 20%;
            animation-duration: 19s;
            animation-delay: -12s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg) scale(1);
            }
            25% {
                transform: translate(40px, -40px) rotate(90deg) scale(1.15);
            }
            50% {
                transform: translate(-30px, 30px) rotate(180deg) scale(0.85);
            }
            75% {
                transform: translate(-40px, -30px) rotate(270deg) scale(1.1);
            }
        }

        /* Header mejorado */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            border-bottom: 1px solid rgba(79, 70, 229, 0.1);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-img {
            height: 45px;
            width: auto;
            object-fit: contain;
            transition: var(--transition);
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2.5rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--gray-700);
            font-weight: 500;
            font-size: 0.95rem;
            transition: var(--transition);
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .login-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            font-size: 0.95rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(79, 70, 229, 0.4);
        }

        /* Hero Section mejorado */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 11rem 2rem 8rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-text h1 {
            font-family: var(--font-secondary);
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .hero-text .highlight {
            background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-text p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
            line-height: 1.7;
        }

        .hero-features {
            display: flex;
            gap: 2rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
        }

        .hero-feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
        }

        .hero-feature-item i {
            color: var(--success);
            font-size: 1.2rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .cta-button {
            background: var(--success);
            color: white;
            padding: 1.1rem 2.5rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: var(--transition);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.4);
        }

        .secondary-button {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.4);
            padding: 1.1rem 2.5rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition);
            backdrop-filter: blur(10px);
        }

        .secondary-button:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.6);
            transform: translateY(-2px);
        }

        .hero-visual {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 550px;
        }

        .phone-mockup {
            width: 300px;
            height: 550px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(25px);
            border: 2px solid rgba(255, 255, 255, 0.25);
            border-radius: 30px;
            padding: 1.5rem 1rem;
            position: relative;
            z-index: 2;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 22px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            overflow: hidden;
        }

        .app-header {
            text-align: center;
        }

        .app-greeting {
            font-size: 0.85rem;
            color: var(--gray-600);
            margin-bottom: 0.25rem;
        }

        .app-balance {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.25rem;
        }

        .app-status {
            font-size: 0.85rem;
            color: var(--success);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
        }

        .app-chart {
            display: flex;
            align-items: end;
            justify-content: space-between;
            height: 120px;
            gap: 0.4rem;
            flex: 1;
            padding: 0.5rem;
            background: var(--gray-50);
            border-radius: 12px;
        }

        .chart-bar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 4px;
            flex: 1;
            animation: barGrow 2.5s ease-in-out infinite;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.2);
        }

        .chart-bar:nth-child(1) { animation-delay: 0s; }
        .chart-bar:nth-child(2) { animation-delay: 0.2s; }
        .chart-bar:nth-child(3) { animation-delay: 0.4s; }
        .chart-bar:nth-child(4) { animation-delay: 0.6s; }
        .chart-bar:nth-child(5) { animation-delay: 0.8s; }
        .chart-bar:nth-child(6) { animation-delay: 1s; }

        @keyframes barGrow {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(1.15); }
        }

        .app-quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }

        .quick-action {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            padding: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gray-700);
            transition: var(--transition);
        }

        .quick-action i {
            font-size: 1rem;
            color: var(--primary);
        }

        .floating-cards {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .money-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 14px;
            padding: 0.9rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--primary);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            animation: cardFloat 5s ease-in-out infinite;
            white-space: nowrap;
        }

        .money-card i {
            font-size: 1.2rem;
        }

        .card-1 {
            top: 8%;
            left: -25%;
            animation-delay: 0s;
        }

        .card-2 {
            top: 55%;
            right: -20%;
            animation-delay: -2s;
        }

        .card-3 {
            top: 30%;
            left: 75%;
            animation-delay: -4s;
        }

        @keyframes cardFloat {
            0%, 100% {
                transform: translateY(0px) rotate(-2deg);
            }
            50% {
                transform: translateY(-15px) rotate(2deg);
            }
        }

        /* AI Assistant Section - NUEVA */
        .ai-assistant {
            padding: 7rem 2rem;
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            position: relative;
            overflow: hidden;
        }

        .ai-assistant::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .ai-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
            position: relative;
        }

        .ai-visual {
            position: relative;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ai-chat-mockup {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-2xl);
            border: 1px solid var(--gray-200);
        }

        .chat-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--gray-100);
        }

        .ai-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .chat-info h3 {
            font-size: 1.1rem;
            color: var(--gray-800);
            font-weight: 700;
        }

        .chat-info p {
            font-size: 0.85rem;
            color: var(--success);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .chat-messages {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.5rem 0;
        }

        .chat-message {
            display: flex;
            gap: 0.75rem;
            animation: messageSlide 0.5s ease-out;
        }

        .chat-message.user {
            flex-direction: row-reverse;
        }

        .message-bubble {
            max-width: 75%;
            padding: 0.9rem 1.2rem;
            border-radius: 16px;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .message-bubble.ai {
            background: var(--gray-100);
            color: var(--gray-800);
            border-bottom-left-radius: 4px;
        }

        .message-bubble.user {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border-bottom-right-radius: 4px;
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

        .ai-text h2 {
            font-family: var(--font-secondary);
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .ai-text .highlight {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .ai-text p {
            font-size: 1.2rem;
            color: var(--gray-600);
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .ai-features-list {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            margin-bottom: 2.5rem;
        }

        .ai-feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .ai-feature-item:hover {
            transform: translateX(10px);
            box-shadow: var(--shadow-md);
        }

        .ai-feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .ai-feature-text {
            flex: 1;
        }

        .ai-feature-text h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
        }

        .ai-feature-text p {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin: 0;
        }

        /* Features Section mejorado */
        .features {
            padding: 7rem 2rem;
            background: var(--white);
        }

        .section-title {
            text-align: center;
            max-width: 900px;
            margin: 0 auto 5rem;
        }

        .section-title h2 {
            font-family: var(--font-secondary);
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .section-title p {
            font-size: 1.25rem;
            color: var(--gray-600);
            line-height: 1.7;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--white);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            text-align: center;
            transition: var(--transition);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-100);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
            transform: scaleX(0);
            transition: var(--transition);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-12px);
            box-shadow: var(--shadow-2xl);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--gray-600);
            line-height: 1.7;
            font-size: 1rem;
        }

        /* Testimonials Section mejorado */
        .testimonials {
            padding: 7rem 2rem;
            background: var(--gray-50);
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .testimonial-card {
            background: var(--white);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border: 1px solid var(--gray-100);
            position: relative;
        }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 1.5rem;
            left: 2rem;
            font-size: 5rem;
            color: var(--primary);
            opacity: 0.1;
            font-family: Georgia, serif;
        }

        .testimonial-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .testimonial-rating {
            display: flex;
            gap: 0.25rem;
            margin-bottom: 1rem;
        }

        .testimonial-rating i {
            color: var(--warning);
            font-size: 1.1rem;
        }

        .testimonial-text {
            font-style: italic;
            color: var(--gray-700);
            margin-bottom: 2rem;
            line-height: 1.7;
            font-size: 1.05rem;
            position: relative;
            z-index: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .author-info h4 {
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
            font-size: 1.1rem;
        }

        .author-info p {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        /* Stats Section - NUEVA */
        .stats-section {
            padding: 7rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid2" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid2)"/></svg>');
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            font-family: var(--font-secondary);
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* CTA Section mejorado */
        .cta-section {
            padding: 7rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .cta-content {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .cta-content h2 {
            font-family: var(--font-secondary);
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .cta-content p {
            font-size: 1.3rem;
            margin-bottom: 3rem;
            opacity: 0.95;
            line-height: 1.7;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Footer mejorado */
        footer {
            background: var(--gray-900);
            color: var(--gray-300);
            padding: 5rem 2rem 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 4rem;
            max-width: 1400px;
            margin: 0 auto 3rem;
        }

        .footer-column h3 {
            color: var(--white);
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .footer-column p {
            line-height: 1.7;
            color: var(--gray-400);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.9rem;
        }

        .footer-links a {
            color: var(--gray-400);
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-links a:hover {
            color: var(--primary-light);
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-link {
            width: 45px;
            height: 45px;
            background: var(--gray-800);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            transition: var(--transition);
            font-size: 1.2rem;
        }

        .social-link:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-800);
            color: var(--gray-500);
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Mobile Menu mejorado */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--gray-700);
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .mobile-menu-btn:hover {
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-content,
            .ai-content {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .ai-content {
                grid-template-columns: 1fr;
            }

            .ai-text {
                order: 1;
                text-align: center;
            }

            .ai-visual {
                order: 2;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: white;
                flex-direction: column;
                padding: 1.5rem;
                box-shadow: var(--shadow-lg);
                gap: 1rem;
            }

            .nav-links.active {
                display: flex;
            }

            .mobile-menu-btn {
                display: block;
            }

            .hero {
                padding: 9rem 1.5rem 5rem;
            }

            .hero-content {
                grid-template-columns: 1fr;
                gap: 3rem;
                text-align: center;
            }

            .hero-buttons,
            .cta-buttons {
                justify-content: center;
            }

            .hero-features {
                justify-content: center;
            }

            .hero-visual {
                height: 450px;
            }

            .phone-mockup {
                width: 260px;
                height: 450px;
            }

            .money-card {
                font-size: 0.85rem;
                padding: 0.7rem 1rem;
            }

            .features-grid,
            .testimonial-grid {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }

            .ai-text {
                text-align: center;
            }

            .ai-feature-item:hover {
                transform: translateX(0);
            }

            .stats-grid {
                gap: 2rem;
            }
        }

        /* Animaciones adicionales */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
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
        <div class="shape shape-5"></div>
    </div>

    <!-- Header -->
    <header>
        <nav class="navbar">
            <div class="logo-container">
                <img src="logo_Finabiz.png" alt="Finabiz Logo" class="logo-img">
            </div>
            
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
            
            <ul class="nav-links">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#asistente">Asistente IA</a></li>
                <li><a href="#caracteristicas">Características</a></li>
                <li><a href="#testimonios">Testimonios</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ul>
            
<!-- Botón Iniciar Sesión -->
<div class="buttons-container">
    <button class="login-btn" onclick="window.location.href='login.php'">
        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
    </button>

    <button class="register-btn" onclick="window.location.href='registrarse.php'">
        <i class="fas fa-user-plus"></i> Registrarse
    </button>
</div>


        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Tu <span class="highlight">Asistente Financiero Personal</span> con Inteligencia Artificial</h1>
                <p>Toma el control total de tus finanzas personales con IA avanzada. Finabiz te ayuda a ahorrar más, gastar inteligentemente y alcanzar tus metas financieras.</p>
                
                <div class="hero-features">
                    <div class="hero-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>100% Gratis</span>
                    </div>
                    <div class="hero-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Asistente IA 24/7</span>
                    </div>
                    <div class="hero-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Seguro y Privado</span>
                    </div>
                </div>
                
                <div class="hero-buttons">
                    <a href="login.php" class="cta-button">
                        <i class="fas fa-rocket"></i>
                        Comenzar Ahora
                    </a>
                    <a href="#asistente" class="secondary-button">
                        <i class="fas fa-robot"></i>
                        Ver Asistente IA
                    </a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="floating-cards">
                    <div class="money-card card-1">
                        <i class="fas fa-arrow-trend-up"></i>
                        <span>+S/1,250</span>
                    </div>
                    <div class="money-card card-2">
                        <i class="fas fa-chart-line"></i>
                        <span>+23% Este mes</span>
                    </div>
                    <div class="money-card card-3">
                        <i class="fas fa-piggy-bank"></i>
                        <span>Meta: 85%</span>
                    </div>
                </div>
                
                <div class="phone-mockup">
                    <div class="phone-screen">
                        <div class="app-header">
                            <div class="app-greeting">Hola, Usuario 👋</div>
                            <div class="app-balance">S/4,250.00</div>
                            <div class="app-status">
                                <i class="fas fa-circle"></i>
                                +S/320 este mes
                            </div>
                        </div>
                        <div class="app-chart">
                            <div class="chart-bar" style="height: 45%"></div>
                            <div class="chart-bar" style="height: 75%"></div>
                            <div class="chart-bar" style="height: 60%"></div>
                            <div class="chart-bar" style="height: 85%"></div>
                            <div class="chart-bar" style="height: 55%"></div>
                            <div class="chart-bar" style="height: 70%"></div>
                        </div>
                        <div class="app-quick-actions">
                            <div class="quick-action">
                                <i class="fas fa-plus"></i>
                                Ingreso
                            </div>
                            <div class="quick-action">
                                <i class="fas fa-minus"></i>
                                Gasto
                            </div>
                            <div class="quick-action">
                                <i class="fas fa-chart-pie"></i>
                                Reportes
                            </div>
                            <div class="quick-action">
                                <i class="fas fa-robot"></i>
                                Asistente
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- AI Assistant Section -->
    <section class="ai-assistant" id="asistente">
        <div class="ai-content">
            <div class="ai-visual">
                <div class="ai-chat-mockup">
                    <div class="chat-header">
                        <div class="ai-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="chat-info">
                            <h3>Asistente Financiero IA</h3>
                            <p><i class="fas fa-circle"></i> En línea</p>
                        </div>
                    </div>
                    <div class="chat-messages">
                        <div class="chat-message">
                            <div class="message-bubble ai">
                                ¡Hola! 👋 Soy tu asistente financiero personal. ¿En qué puedo ayudarte hoy?
                            </div>
                        </div>
                        <div class="chat-message user">
                            <div class="message-bubble user">
                                ¿Cómo puedo ahorrar más dinero este mes?
                            </div>
                        </div>
                        <div class="chat-message">
                            <div class="message-bubble ai">
                                Analicé tus gastos y encontré que puedes ahorrar S/450 reduciendo gastos en entretenimiento y comida fuera. ¿Quieres que te cree un plan de ahorro personalizado?
                            </div>
                        </div>
                        <div class="chat-message user">
                            <div class="message-bubble user">
                                ¡Sí, por favor! 🎯
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ai-text">
                <h2>Tu <span class="highlight">Asistente Financiero Inteligente</span> Siempre Disponible</h2>
                <p>Nuestra IA analiza tus patrones de gasto, te da consejos personalizados y te ayuda a tomar mejores decisiones financieras en tiempo real.</p>
                
                <div class="ai-features-list">
                    <div class="ai-feature-item">
                        <div class="ai-feature-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="ai-feature-text">
                            <h4>Análisis Inteligente</h4>
                            <p>Identifica patrones en tus gastos y te sugiere oportunidades de ahorro</p>
                        </div>
                    </div>
                    <div class="ai-feature-item">
                        <div class="ai-feature-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="ai-feature-text">
                            <h4>Conversación Natural</h4>
                            <p>Pregunta cualquier cosa sobre tus finanzas como si hablaras con un experto</p>
                        </div>
                    </div>
                    <div class="ai-feature-item">
                        <div class="ai-feature-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="ai-feature-text">
                            <h4>Recomendaciones Personalizadas</h4>
                            <p>Recibe consejos adaptados a tu situación financiera única</p>
                        </div>
                    </div>
                    <div class="ai-feature-item">
                        <div class="ai-feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="ai-feature-text">
                            <h4>Alertas Proactivas</h4>
                            <p>Te avisa cuando detecta gastos inusuales o oportunidades de ahorro</p>
                        </div>
                    </div>
                </div>
                
                <a href="login.php" class="cta-button">
                    <i class="fas fa-robot"></i>
                    Probar Asistente IA Gratis
                </a>
            </div>
        </div>
    </section>



    <!-- Features Section -->
    <section class="features" id="caracteristicas">
        <div class="section-title">
            <h2>Todo lo que Necesitas para Dominar tus Finanzas Personales</h2>
            <p>Herramientas poderosas y fáciles de usar diseñadas específicamente para ayudarte a alcanzar tus metas financieras</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3>Control de Gastos</h3>
                <p>Registra y categoriza tus gastos automáticamente. Visualiza en qué gastas tu dinero con gráficos intuitivos y reportes detallados.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Análisis Predictivo</h3>
                <p>Nuestra IA predice tus gastos futuros basándose en tus patrones históricos y te ayuda a planificar mejor tu presupuesto mensual.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <h3>Metas de Ahorro</h3>
                <p>Define objetivos de ahorro personalizados y recibe recordatorios y consejos para alcanzarlos más rápido de lo que imaginas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3>Gestión de Ingresos</h3>
                <p>Registra todas tus fuentes de ingresos y obtén una visión clara de tu flujo de efectivo mensual y proyecciones anuales.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3>Reportes Detallados</h3>
                <p>Genera reportes personalizados por período, categoría o tipo de transacción. Exporta tus datos cuando los necesites.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h3>Privacidad Total</h3>
                <p>Tus datos financieros están protegidos con encriptación de nivel bancario. Tu información nunca se comparte con terceros.</p>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonios">
        <div class="section-title">
            <h2>Lo que Dicen Nuestros Usuarios</h2>
            <p>Miles de personas ya están transformando su relación con el dinero gracias a Finabiz</p>
        </div>
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"Finabiz cambió completamente mi vida financiera. En solo 3 meses logré ahorrar S/2,500 siguiendo los consejos del asistente IA. ¡Es increíble cómo una app puede hacer tanta diferencia!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">MG</div>
                    <div class="author-info">
                        <h4>María González</h4>
                        <p>Profesora • Lima</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"El asistente de IA es como tener un asesor financiero personal disponible 24/7. Me ayuda a tomar mejores decisiones y me motiva a seguir ahorrando. La mejor app financiera que he usado."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">CR</div>
                    <div class="author-info">
                        <h4>Carlos Rodríguez</h4>
                        <p>Ingeniero • Arequipa</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"Finalmente entiendo a dónde va mi dinero cada mes. Los reportes son súper claros y el asistente me da tips prácticos que realmente funcionan. Ya alcancé mi primera meta de ahorro."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">AM</div>
                    <div class="author-info">
                        <h4>Ana Martínez</h4>
                        <p>Diseñadora • Cusco</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="contacto">
        <div class="cta-content">
            <h2>Comienza tu Transformación Financiera Hoy</h2>
            <p>Únete a miles de usuarios que ya están tomando el control de sus finanzas personales con la ayuda de inteligencia artificial</p>
            <div class="cta-buttons">
                <a href="login.php" class="cta-button">
                    <i class="fas fa-rocket"></i>
                    Crear Cuenta Gratis
                </a>
                <a href="#caracteristicas" class="secondary-button">
                    <i class="fas fa-circle-info"></i>
                    Conocer Más
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>Finabiz</h3>
                <p>Tu asistente financiero personal con inteligencia artificial. Toma el control de tus finanzas y alcanza tus metas con ayuda de la IA más avanzada.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Enlaces Rápidos</h3>
                <ul class="footer-links">
                    <li><a href="#inicio"><i class="fas fa-chevron-right"></i> Inicio</a></li>
                    <li><a href="#asistente"><i class="fas fa-chevron-right"></i> Asistente IA</a></li>
                    <li><a href="#caracteristicas"><i class="fas fa-chevron-right"></i> Características</a></li>
                    <li><a href="#testimonios"><i class="fas fa-chevron-right"></i> Testimonios</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Legal</h3>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Términos del Servicio</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Política de Privacidad</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Política de Cookies</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Aviso Legal</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contacto</h3>
                <ul class="footer-links">
                    <li><i class="fas fa-envelope"></i> info@finabiz.com</li>
                    <li><i class="fas fa-phone"></i> +51 999 888 777</li>
                    <li><i class="fas fa-map-marker-alt"></i> Lima, Perú</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2024 Finabiz - Tu Asistente Financiero con IA. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const navLinks = document.querySelector('.nav-links');

            mobileMenuBtn.addEventListener('click', function() {
                navLinks.classList.toggle('active');
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
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
                        const icon = mobileMenuBtn.querySelector('i');
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            });

            // Header scroll effect
            let lastScroll = 0;
            window.addEventListener('scroll', function() {
                const header = document.querySelector('header');
                const currentScroll = window.pageYOffset;

                if (currentScroll > 100) {
                    header.style.background = 'rgba(255, 255, 255, 0.98)';
                    header.style.boxShadow = 'var(--shadow-md)';
                } else {
                    header.style.background = 'rgba(255, 255, 255, 0.95)';
                    header.style.boxShadow = 'var(--shadow-sm)';
                }

                lastScroll = currentScroll;
            });

            // Animación de números en Stats Section
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px'
            };

            const statsObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const statCards = entry.target.querySelectorAll('.stat-card');
                        statCards.forEach((card, index) => {
                            setTimeout(() => {
                                card.style.opacity = '0';
                                card.style.transform = 'translateY(20px)';
                                setTimeout(() => {
                                    card.style.transition = 'all 0.6s ease-out';
                                    card.style.opacity = '1';
                                    card.style.transform = 'translateY(0)';
                                }, 50);
                            }, index * 100);
                        });
                        statsObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            const statsSection = document.querySelector('.stats-section');
            if (statsSection) {
                statsObserver.observe(statsSection);
            }

            // Animación de features al hacer scroll
            const featureObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '0';
                        entry.target.style.transform = 'translateY(30px)';
                        setTimeout(() => {
                            entry.target.style.transition = 'all 0.6s ease-out';
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, 100);
                        featureObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            document.querySelectorAll('.feature-card').forEach(card => {
                featureObserver.observe(card);
            });

            // Animación de testimonios
            document.querySelectorAll('.testimonial-card').forEach(card => {
                featureObserver.observe(card);
            });

            // Simulación de mensajes del chat (efecto de escritura)
            const chatMessages = document.querySelectorAll('.chat-message');
            let delay = 0;
            
            const chatObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        chatMessages.forEach((message, index) => {
                            message.style.opacity = '0';
                            message.style.transform = 'translateY(10px)';
                            setTimeout(() => {
                                message.style.transition = 'all 0.4s ease-out';
                                message.style.opacity = '1';
                                message.style.transform = 'translateY(0)';
                            }, index * 600);
                        });
                        chatObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });

            const aiSection = document.querySelector('.ai-assistant');
            if (aiSection) {
                chatObserver.observe(aiSection);
            }

            // Efecto parallax suave en hero
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const hero = document.querySelector('.hero');
                const heroContent = document.querySelector('.hero-content');
                
                if (hero && scrolled < hero.offsetHeight) {
                    heroContent.style.transform = `translateY(${scrolled * 0.3}px)`;
                    hero.style.opacity = 1 - (scrolled / hero.offsetHeight) * 0.5;
                }
            });

            // Animación de las barras del gráfico
            const chartBars = document.querySelectorAll('.chart-bar');
            chartBars.forEach((bar, index) => {
                bar.style.opacity = '0';
                bar.style.transform = 'scaleY(0)';
                setTimeout(() => {
                    bar.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    bar.style.opacity = '1';
                    bar.style.transform = 'scaleY(1)';
                }, 1500 + (index * 100));
            });

            // Animación de floating cards
            setTimeout(() => {
                document.querySelectorAll('.money-card').forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'all 0.6s ease-out';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 2000 + (index * 200));
                });
            }, 500);

            // Efecto hover para las feature cards
            document.querySelectorAll('.feature-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-12px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Contador animado para stats
            function animateCounter(element, target, duration = 2000) {
                const start = 0;
                const increment = target / (duration / 16);
                let current = start;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    
                    if (element.textContent.includes('K')) {
                        element.textContent = Math.floor(current) + 'K+';
                    } else if (element.textContent.includes('M')) {
                        element.textContent = 'S/' + Math.floor(current) + 'M+';
                    } else if (element.textContent.includes('%')) {
                        element.textContent = Math.floor(current) + '%';
                    } else {
                        element.textContent = current.toFixed(0);
                    }
                }, 16);
            }

            // Activar contador cuando la sección sea visible
            const counterObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const statNumbers = entry.target.querySelectorAll('.stat-number');
                        statNumbers.forEach(stat => {
                            const text = stat.textContent;
                            let target = parseInt(text.replace(/\D/g, ''));
                            
                            if (text.includes('K')) {
                                animateCounter(stat, 10, 2000);
                            } else if (text.includes('M')) {
                                animateCounter(stat, 5, 2000);
                            } else if (text.includes('%')) {
                                animateCounter(stat, 98, 2000);
                            }
                        });
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            if (statsSection) {
                counterObserver.observe(statsSection);
            }

            // Agregar clase para animaciones de entrada
            const fadeElements = document.querySelectorAll('.section-title, .ai-text');
            const fadeObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-up');
                        fadeObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            fadeElements.forEach(element => {
                fadeObserver.observe(element);
            });

            // Efecto de typing en el hero
            const heroTitle = document.querySelector('.hero-text h1');
            if (heroTitle) {
                heroTitle.style.opacity = '0';
                setTimeout(() => {
                    heroTitle.style.transition = 'opacity 1s ease-out';
                    heroTitle.style.opacity = '1';
                }, 300);
            }

            // Prevenir FOUC (Flash of Unstyled Content)
            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.5s ease-in';
                document.body.style.opacity = '1';
            }, 100);
        });

        // Lazy loading para imágenes
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>
</body>
</html>