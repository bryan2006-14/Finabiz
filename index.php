<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#4f46e5">
    <link rel="shortcut icon" href="icono-ic.png" type="image/x-icon">
    
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <title>Finabiz - Tu Asistente Financiero Personal con IA</title>
    
    <style>
        :root{--primary:#4f46e5;--primary-light:#6366f1;--primary-dark:#4338ca;--secondary:#06b6d4;--success:#10b981;--warning:#f59e0b;--danger:#ef4444;--info:#3b82f6;--gray-50:#f9fafb;--gray-100:#f3f4f6;--gray-200:#e5e7eb;--gray-300:#d1d5db;--gray-400:#9ca3af;--gray-500:#6b7280;--gray-600:#4b5563;--gray-700:#374151;--gray-800:#1f2937;--gray-900:#111827;--white:#ffffff;--black:#000000;--font-primary:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;--font-secondary:'Poppins',sans-serif;--shadow-sm:0 1px 2px 0 rgba(0,0,0,0.05);--shadow-md:0 4px 6px -1px rgba(0,0,0,0.1);--shadow-lg:0 10px 15px -3px rgba(0,0,0,0.1);--shadow-xl:0 20px 25px -5px rgba(0,0,0,0.1);--shadow-2xl:0 25px 50px -12px rgba(0,0,0,0.25);--border-radius:12px;--transition:all 0.3s cubic-bezier(0.4,0,0.2,1)}
        *{margin:0;padding:0;box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{font-family:var(--font-primary);color:var(--gray-800);line-height:1.6;overflow-x:hidden}

        /* BOTÓN WHATSAPP FLOTANTE */
        .whatsapp-button{position:fixed;bottom:30px;right:30px;width:70px;height:70px;background:linear-gradient(135deg,#25d366 0%,#20ba58 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 20px rgba(37,211,102,0.4);transition:all 0.3s ease;z-index:999;text-decoration:none;color:white;font-size:2.5rem;animation:whatsappPulse 2s ease-in-out infinite}
        .whatsapp-button:hover{transform:scale(1.1);box-shadow:0 6px 30px rgba(37,211,102,0.6)}
        .whatsapp-button:active{transform:scale(0.95)}
        @keyframes whatsappPulse{0%,100%{box-shadow:0 4px 20px rgba(37,211,102,0.4)}50%{box-shadow:0 4px 20px rgba(37,211,102,0.8),0 0 0 10px rgba(37,211,102,0.2)}}

        /* CONTENEDOR BOTONES HEADER */
        .buttons-container{display:flex;gap:15px;align-items:center;flex-wrap:wrap}
        .login-btn,.register-btn{font-family:'Poppins',sans-serif;padding:10px 20px;border-radius:30px;cursor:pointer;font-size:16px;display:inline-flex;align-items:center;gap:8px;transition:all 0.3s ease;border:none;text-decoration:none}
        .login-btn{background-color:#007bff;color:white}
        .login-btn:hover{background-color:#0056b3}
        .register-btn{background-color:transparent;color:#555;border:2px solid #ccc}
        .register-btn:hover{color:#333;border-color:#888;background-color:rgba(0,0,0,0.05)}

        /* FONDO ANIMADO */
        .background-animation{position:fixed;top:0;left:0;width:100%;height:100%;z-index:-2;overflow:hidden;background:linear-gradient(135deg,#f5f7fa 0%,#e9ecef 100%)}
        .shape{position:absolute;opacity:0.08;border-radius:50%;animation:float infinite ease-in-out;filter:blur(40px)}
        .shape-1{width:300px;height:300px;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);top:10%;left:10%;animation-duration:20s}
        .shape-2{width:250px;height:250px;background:linear-gradient(135deg,var(--secondary) 0%,var(--info) 100%);top:60%;right:10%;animation-duration:18s;animation-delay:-5s}
        .shape-3{width:200px;height:200px;background:linear-gradient(135deg,var(--success) 0%,#34d399 100%);bottom:15%;left:25%;animation-duration:22s;animation-delay:-10s}
        .shape-4{width:180px;height:180px;background:linear-gradient(135deg,var(--warning) 0%,#fbbf24 100%);top:40%;right:35%;animation-duration:16s;animation-delay:-7s}
        .shape-5{width:220px;height:220px;background:linear-gradient(135deg,#a78bfa 0%,#c084fc 100%);bottom:30%;right:20%;animation-duration:19s;animation-delay:-12s}
        @keyframes float{0%,100%{transform:translate(0,0) rotate(0deg) scale(1)}25%{transform:translate(40px,-40px) rotate(90deg) scale(1.15)}50%{transform:translate(-30px,30px) rotate(180deg) scale(0.85)}75%{transform:translate(-40px,-30px) rotate(270deg) scale(1.1)}}

        /* HEADER MEJORADO CON MENÚ HAMBURGUESA */
        header{background:rgba(255,255,255,0.95);backdrop-filter:blur(15px);position:fixed;top:0;left:0;width:100%;z-index:1000;box-shadow:var(--shadow-sm);transition:var(--transition);border-bottom:1px solid rgba(79,70,229,0.1)}
        .navbar{display:flex;justify-content:space-between;align-items:center;padding:1rem 2rem;max-width:1400px;margin:0 auto}
        .logo-container{display:flex;align-items:center;gap:0.75rem}
        .logo-img{height:45px;width:auto;object-fit:contain;transition:var(--transition)}
        .logo-img:hover{transform:scale(1.05)}
        .nav-links{display:flex;list-style:none;gap:2.5rem;align-items:center}
        .nav-links a{text-decoration:none;color:var(--gray-700);font-weight:500;font-size:0.95rem;transition:var(--transition);position:relative;padding:0.5rem 0}
        .nav-links a:hover{color:var(--primary)}
        .nav-links a::after{content:'';position:absolute;bottom:0;left:0;width:0;height:2px;background:linear-gradient(90deg,var(--primary) 0%,var(--primary-light) 100%);transition:var(--transition)}
        .nav-links a:hover::after{width:100%}

        /* MENÚ MÓVIL HAMBURGUESA */
        .mobile-menu-btn{display:none;background:none;border:none;color:var(--gray-700);font-size:1.5rem;cursor:pointer;transition:var(--transition);width:40px;height:40px;border-radius:8px;align-items:center;justify-content:center}
        .mobile-menu-btn:hover{background:var(--gray-100);color:var(--primary)}
        .sidebar-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:999;display:none;opacity:0;transition:opacity 0.3s ease}
        .sidebar-overlay.active{display:block;opacity:1}

        /* HERO SECTION */
        .hero{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:11rem 2rem 8rem;position:relative;overflow:hidden}
        .hero::before{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background:url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');opacity:0.3}
        .hero-content{max-width:1400px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:center;position:relative;z-index:1}
        .hero-text h1{font-family:var(--font-secondary);font-size:clamp(2.5rem,5vw,4.5rem);font-weight:800;line-height:1.1;margin-bottom:1.5rem;text-shadow:0 2px 10px rgba(0,0,0,0.1)}
        .hero-text .highlight{background:linear-gradient(90deg,#fbbf24 0%,#f59e0b 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .hero-text p{font-size:1.3rem;margin-bottom:2.5rem;opacity:0.95;line-height:1.7}
        .hero-features{display:flex;gap:2rem;margin-bottom:2.5rem;flex-wrap:wrap}
        .hero-feature-item{display:flex;align-items:center;gap:0.5rem;font-size:1rem}
        .hero-feature-item i{color:var(--success);font-size:1.2rem}
        .hero-buttons{display:flex;gap:1rem;flex-wrap:wrap}
        .cta-button{background:var(--success);color:white;padding:1.1rem 2.5rem;border-radius:var(--border-radius);text-decoration:none;font-weight:700;font-size:1.1rem;transition:var(--transition);box-shadow:0 4px 20px rgba(16,185,129,0.3);display:inline-flex;align-items:center;gap:0.5rem}
        .cta-button:hover{transform:translateY(-3px);box-shadow:0 8px 30px rgba(16,185,129,0.4)}
        .secondary-button{background:rgba(255,255,255,0.15);color:white;border:2px solid rgba(255,255,255,0.4);padding:1.1rem 2.5rem;border-radius:var(--border-radius);text-decoration:none;font-weight:600;font-size:1.1rem;transition:var(--transition);backdrop-filter:blur(10px)}
        .secondary-button:hover{background:rgba(255,255,255,0.25);border-color:rgba(255,255,255,0.6);transform:translateY(-2px)}

        /* HERO VISUAL */
        .hero-visual{position:relative;display:flex;justify-content:center;align-items:center;height:550px}
        .phone-mockup{width:300px;height:550px;background:rgba(255,255,255,0.12);backdrop-filter:blur(25px);border:2px solid rgba(255,255,255,0.25);border-radius:30px;padding:1.5rem 1rem;position:relative;z-index:2;box-shadow:0 25px 50px rgba(0,0,0,0.3)}
        .phone-screen{width:100%;height:100%;background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);border-radius:22px;padding:1.5rem;display:flex;flex-direction:column;gap:1rem;overflow:hidden}
        .app-header{text-align:center}
        .app-greeting{font-size:0.85rem;color:var(--gray-600);margin-bottom:0.25rem}
        .app-balance{font-size:2rem;font-weight:800;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:0.25rem}
        .app-status{font-size:0.85rem;color:var(--success);font-weight:600;display:flex;align-items:center;justify-content:center;gap:0.3rem}
        .app-chart{display:flex;align-items:end;justify-content:space-between;height:120px;gap:0.4rem;flex:1;padding:0.5rem;background:var(--gray-50);border-radius:12px}
        .chart-bar{background:linear-gradient(180deg,var(--primary) 0%,var(--primary-light) 100%);border-radius:4px;flex:1;animation:barGrow 2.5s ease-in-out infinite;box-shadow:0 2px 8px rgba(79,70,229,0.2)}
        .chart-bar:nth-child(1){animation-delay:0s}.chart-bar:nth-child(2){animation-delay:0.2s}.chart-bar:nth-child(3){animation-delay:0.4s}.chart-bar:nth-child(4){animation-delay:0.6s}.chart-bar:nth-child(5){animation-delay:0.8s}.chart-bar:nth-child(6){animation-delay:1s}
        @keyframes barGrow{0%,100%{transform:scaleY(1)}50%{transform:scaleY(1.15)}}
        .app-quick-actions{display:grid;grid-template-columns:1fr 1fr;gap:0.5rem}
        .quick-action{background:white;border:1px solid var(--gray-200);border-radius:8px;padding:0.75rem;display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;font-weight:600;color:var(--gray-700);transition:var(--transition)}
        .quick-action i{font-size:1rem;color:var(--primary)}
        .floating-cards{position:absolute;width:100%;height:100%}
        .money-card{position:absolute;background:rgba(255,255,255,0.95);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.4);border-radius:14px;padding:0.9rem 1.2rem;display:flex;align-items:center;gap:0.6rem;font-size:0.95rem;font-weight:700;color:var(--primary);box-shadow:0 8px 30px rgba(0,0,0,0.2);animation:cardFloat 5s ease-in-out infinite;white-space:nowrap}
        .money-card i{font-size:1.2rem}
        .card-1{top:8%;left:-25%;animation-delay:0s}.card-2{top:55%;right:-20%;animation-delay:-2s}.card-3{top:30%;left:75%;animation-delay:-4s}
        @keyframes cardFloat{0%,100%{transform:translateY(0px) rotate(-2deg)}50%{transform:translateY(-15px) rotate(2deg)}}

        /* AI ASSISTANT SECTION */
        .ai-assistant{padding:7rem 2rem;background:linear-gradient(135deg,#f8fafc 0%,#e0e7ff 100%);position:relative;overflow:hidden}
        .ai-assistant::before{content:'';position:absolute;top:0;right:0;width:500px;height:500px;background:radial-gradient(circle,rgba(79,70,229,0.1) 0%,transparent 70%);border-radius:50%}
        .ai-content{max-width:1400px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:center;position:relative}
        .ai-visual{position:relative;height:500px;display:flex;align-items:center;justify-content:center}
        .ai-chat-mockup{width:100%;max-width:450px;background:white;border-radius:20px;padding:2rem;box-shadow:var(--shadow-2xl);border:1px solid var(--gray-200)}
        .chat-header{display:flex;align-items:center;gap:1rem;padding-bottom:1.5rem;border-bottom:2px solid var(--gray-100)}
        .ai-avatar{width:50px;height:50px;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:1.5rem}
        .chat-info h3{font-size:1.1rem;color:var(--gray-800);font-weight:700}
        .chat-info p{font-size:0.85rem;color:var(--success);display:flex;align-items:center;gap:0.3rem}
        .chat-messages{display:flex;flex-direction:column;gap:1rem;padding:1.5rem 0}
        .chat-message{display:flex;gap:0.75rem;animation:messageSlide 0.5s ease-out}
        .chat-message.user{flex-direction:row-reverse}
        .message-bubble{max-width:75%;padding:0.9rem 1.2rem;border-radius:16px;font-size:0.95rem;line-height:1.5}
        .message-bubble.ai{background:var(--gray-100);color:var(--gray-800);border-bottom-left-radius:4px}
        .message-bubble.user{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);color:white;border-bottom-right-radius:4px}
        @keyframes messageSlide{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .ai-text h2{font-family:var(--font-secondary);font-size:clamp(2rem,4vw,3.5rem);font-weight:800;color:var(--gray-900);margin-bottom:1.5rem;line-height:1.2}
        .ai-text .highlight{background:linear-gradient(90deg,var(--primary) 0%,var(--primary-light) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .ai-text p{font-size:1.2rem;color:var(--gray-600);margin-bottom:2rem;line-height:1.7}
        .ai-features-list{display:flex;flex-direction:column;gap:1.2rem;margin-bottom:2.5rem}
        .ai-feature-item{display:flex;align-items:center;gap:1rem;padding:1rem;background:white;border-radius:12px;box-shadow:var(--shadow-sm);transition:var(--transition)}
        .ai-feature-item:hover{transform:translateX(10px);box-shadow:var(--shadow-md)}
        .ai-feature-icon{width:50px;height:50px;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:1.3rem;flex-shrink:0}
        .ai-feature-text{flex:1}
        .ai-feature-text h4{font-size:1.1rem;font-weight:700;color:var(--gray-800);margin-bottom:0.25rem}
        .ai-feature-text p{font-size:0.9rem;color:var(--gray-600);margin:0}

        /* FEATURES SECTION */
        .features{padding:7rem 2rem;background:var(--white)}
        .section-title{text-align:center;max-width:900px;margin:0 auto 5rem}
        .section-title h2{font-family:var(--font-secondary);font-size:clamp(2rem,4vw,3.5rem);font-weight:800;color:var(--gray-900);margin-bottom:1.5rem;line-height:1.2}
        .section-title p{font-size:1.25rem;color:var(--gray-600);line-height:1.7}
        .features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:2.5rem;max-width:1400px;margin:0 auto}
        .feature-card{background:var(--white);border-radius:20px;padding:3rem 2.5rem;text-align:center;transition:var(--transition);box-shadow:var(--shadow-md);border:1px solid var(--gray-100);position:relative;overflow:hidden}
        .feature-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--primary) 0%,var(--primary-light) 100%);transform:scaleX(0);transition:var(--transition)}
        .feature-card:hover::before{transform:scaleX(1)}
        .feature-card:hover{transform:translateY(-12px);box-shadow:var(--shadow-2xl)}
        .feature-icon{width:80px;height:80px;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;color:white;font-size:2rem;box-shadow:0 8px 20px rgba(79,70,229,0.3);transition:var(--transition)}
        .feature-card:hover .feature-icon{transform:scale(1.1) rotate(5deg)}
        .feature-card h3{font-size:1.5rem;font-weight:700;color:var(--gray-800);margin-bottom:1rem}
        .feature-card p{color:var(--gray-600);line-height:1.7;font-size:1rem}

        /* TESTIMONIALS SECTION */
        .testimonials{padding:7rem 2rem;background:var(--gray-50)}
        .testimonial-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(350px,1fr));gap:2.5rem;max-width:1400px;margin:0 auto}
        .testimonial-card{background:var(--white);border-radius:20px;padding:3rem;box-shadow:var(--shadow-md);transition:var(--transition);border:1px solid var(--gray-100);position:relative}
        .testimonial-card::before{content:'"';position:absolute;top:1.5rem;left:2rem;font-size:5rem;color:var(--primary);opacity:0.1;font-family:Georgia,serif}
        .testimonial-card:hover{transform:translateY(-8px);box-shadow:var(--shadow-xl)}
        .testimonial-rating{display:flex;gap:0.25rem;margin-bottom:1rem}
        .testimonial-rating i{color:var(--warning);font-size:1.1rem}
        .testimonial-text{font-style:italic;color:var(--gray-700);margin-bottom:2rem;line-height:1.7;font-size:1.05rem;position:relative;z-index:1}
        .testimonial-author{display:flex;align-items:center;gap:1rem}
        .author-avatar{width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1.3rem;flex-shrink:0}
        .author-info h4{font-weight:700;color:var(--gray-800);margin-bottom:0.25rem;font-size:1.1rem}
        .author-info p{color:var(--gray-600);font-size:0.9rem}

        /* CTA SECTION */
        .cta-section{padding:7rem 2rem;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;text-align:center;position:relative;overflow:hidden}
        .cta-section::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle,rgba(255,255,255,0.1) 0%,transparent 70%);animation:pulse 8s ease-in-out infinite}
        @keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.1)}}
        .cta-content{max-width:900px;margin:0 auto;position:relative;z-index:1}
        .cta-content h2{font-family:var(--font-secondary);font-size:clamp(2.5rem,5vw,4rem);font-weight:800;margin-bottom:1.5rem;line-height:1.2}
        .cta-content p{font-size:1.3rem;margin-bottom:3rem;opacity:0.95;line-height:1.7}
        .cta-buttons{display:flex;gap:1rem;justify-content:center;flex-wrap:wrap}

        /* FOOTER */
        footer{background:var(--gray-900);color:var(--gray-300);padding:5rem 2rem 2rem}
        .footer-content{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:4rem;max-width:1400px;margin:0 auto 3rem}
        .footer-column h3{color:var(--white);font-size:1.3rem;margin-bottom:1.5rem;font-weight:700}
        .footer-column p{line-height:1.7;color:var(--gray-400)}
        .footer-links{list-style:none}
        .footer-links li{margin-bottom:0.9rem}
        .footer-links a{color:var(--gray-400);text-decoration:none;transition:var(--transition);display:inline-flex;align-items:center;gap:0.5rem}
        .footer-links a:hover{color:var(--primary-light);transform:translateX(5px)}
        .social-links{display:flex;gap:1rem;margin-top:1.5rem}
        .social-link{width:45px;height:45px;background:var(--gray-800);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--white);transition:var(--transition);font-size:1.2rem}
        .social-link:hover{background:var(--primary);transform:translateY(-3px)}
        .copyright{text-align:center;padding-top:2rem;border-top:1px solid var(--gray-800);color:var(--gray-500);max-width:1400px;margin:0 auto}

        /* ANIMACIONES */
        @keyframes fadeInUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
        .fade-in-up{animation:fadeInUp 0.8s ease-out}

        /* ============================================
           RESPONSIVE - SECCIÓN MEJORADA Y CORREGIDA
           ============================================ */
        @media (max-width:1024px){
            .hero-content,.ai-content{grid-template-columns:1fr;gap:3rem}
            .ai-text{order:1;text-align:center}
            .ai-visual{order:2}
        }

        /* TABLET Y MÓVIL GRANDE (768px) */
        @media (max-width:768px){
            /* NAVBAR CORREGIDO */
            .navbar{
                padding:0.75rem 1rem;
                flex-wrap:wrap;
                gap:0.75rem;
            }
            
            /* Logo y menú hamburguesa en la misma línea */
            .logo-container{
                flex:1;
            }
            
            .mobile-menu-btn{
                display:flex !important;
                order:2;
            }
            
            /* Botones de autenticación en su propia línea */
            .buttons-container{
                order:3;
                width:100%;
                display:flex;
                gap:8px;
                margin-top:0.25rem;
            }
            
            .login-btn,
            .register-btn{
                flex:1;
                padding:9px 14px;
                font-size:14px;
                justify-content:center;
            }
            
            /* Menú de navegación lateral */
            .nav-links{
                display:none;
                position:fixed;
                top:0;
                right:-100%;
                width:280px;
                height:100vh;
                background:white;
                flex-direction:column;
                padding:5rem 2rem 2rem;
                box-shadow:-4px 0 15px rgba(0,0,0,0.15);
                gap:1.5rem;
                z-index:1001;
                transition:right 0.3s cubic-bezier(0.4,0,0.2,1);
                overflow-y:auto;
            }
            
            .nav-links.active{
                display:flex;
                right:0;
            }
            
            .nav-links a{
                padding:0.75rem 0;
                font-size:1.05rem;
                border-bottom:1px solid var(--gray-100);
            }
            
            /* Overlay */
            .sidebar-overlay.active{
                display:block;
            }
            
            /* HERO */
            .hero{
                padding:10rem 1.5rem 5rem;
            }
            
            .hero-content{
                grid-template-columns:1fr;
                gap:3rem;
                text-align:center;
            }
            
            .hero-buttons,
            .cta-buttons{
                justify-content:center;
            }
            
            .hero-features{
                justify-content:center;
            }
            
            .hero-visual{
                height:450px;
            }
            
            .phone-mockup{
                width:260px;
                height:450px;
            }
            
            /* GRIDS */
            .features-grid,
            .testimonial-grid{
                grid-template-columns:1fr;
            }
            
            .footer-content{
                grid-template-columns:1fr;
                gap:2.5rem;
            }
            
            /* AI SECTION */
            .ai-text{
                text-align:center;
            }
            
            .ai-feature-item:hover{
                transform:translateX(0);
            }
            
            /* WHATSAPP */
            .whatsapp-button{
                bottom:20px;
                right:20px;
                width:60px;
                height:60px;
                font-size:2rem;
            }
        }

        /* MÓVIL PEQUEÑO (480px) */
        @media (max-width:480px){
            .navbar{
                padding:0.75rem;
            }
            
            .logo-img{
                height:35px;
            }
            
            .buttons-container{
                gap:6px;
            }
            
            .login-btn,
            .register-btn{
                padding:8px 12px;
                font-size:13px;
            }
            
            .login-btn i,
            .register-btn i{
                font-size:12px;
            }
            
            /* Menu lateral más estrecho */
            .nav-links{
                width:250px;
                padding:4rem 1.5rem 2rem;
            }
            
            /* HERO */
            .hero{
                padding:9rem 1rem 4rem;
            }
            
            .hero-visual{
                height:400px;
            }
            
            .phone-mockup{
                width:240px;
                height:420px;
            }
            
            /* BOTONES */
            .cta-button,
            .secondary-button{
                padding:1rem 2rem;
                font-size:1rem;
                width:100%;
                justify-content:center;
            }
            
            .hero-buttons{
                width:100%;
            }
            
            /* CARDS */
            .feature-card{
                padding:2rem 1.5rem;
            }
            
            .testimonial-card{
                padding:2rem;
            }
            
            /* WHATSAPP */
            .whatsapp-button{
                bottom:15px;
                right:15px;
                width:55px;
                height:55px;
                font-size:1.8rem;
            }
        }

        /* MÓVIL EXTRA PEQUEÑO (360px) */
        @media (max-width:360px){
            .buttons-container{
                flex-direction:column;
                gap:6px;
            }
            
            .login-btn,
            .register-btn{
                width:100%;
                padding:9px 12px;
            }
            
            .nav-links{
                width:85%;
                max-width:250px;
            }
        }

        /* PANTALLAS MUY PEQUEÑAS (374px) */
        @media (max-width:374px){
            .hero-text h1{
                font-size:2rem;
            }
            
            .hero-text p{
                font-size:1.1rem;
            }
            
            .phone-mockup{
                width:220px;
                height:380px;
            }
            
            .feature-card{
                padding:1.5rem 1rem;
            }
        }

        /* PANTALLAS GRANDES */
        @media (min-width:1440px){
            .navbar,
            .hero-content,
            .ai-content,
            .features-grid,
            .testimonial-grid,
            .footer-content{
                max-width:1200px;
                margin-left:auto;
                margin-right:auto;
            }
        }

        /* PRINT */
        @media print{
            .whatsapp-button,
            .mobile-menu-btn{
                display:none !important;
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
        <div class="shape shape-5"></div>
    </div>

    <!-- Botón WhatsApp Flotante -->
    <a href="https://wa.me/51917590605" target="_blank" class="whatsapp-button" title="Contactar por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Header -->
    <header>
        <nav class="navbar">
            <div class="logo-container">
                <img src="logo_Finabiz.png" alt="Finabiz Logo" class="logo-img">
            </div>
            
            <!-- Botón menú hamburguesa -->
            <button class="mobile-menu-btn" id="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Overlay para móvil -->
            <div class="sidebar-overlay" id="sidebar-overlay"></div>
            
            <!-- Menú de navegación -->
            <ul class="nav-links" id="nav-links">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#asistente">Asistente IA</a></li>
                <li><a href="#caracteristicas">Características</a></li>
                <li><a href="#testimonios">Testimonios</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ul>
            
            <!-- Botones de autenticación -->
            <div class="buttons-container">
                <a href="login.php" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
                <a href="registrarse.php" class="register-btn">
                    <i class="fas fa-user-plus"></i> Registrarse
                </a>
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
                    <li><i class="fas fa-phone"></i> +51 917 590 605</li>
                    <li><i class="fas fa-map-marker-alt"></i> Trujillo, Perú</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2024 Finabiz - Tu Asistente Financiero con IA. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
        });

        function initializeApp() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const navLinks = document.getElementById('nav-links');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            function toggleMobileMenu() {
                navLinks.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
                
                if (navLinks.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }

            if (mobileMenuBtn && navLinks && sidebarOverlay) {
                mobileMenuBtn.addEventListener('click', toggleMobileMenu);
                sidebarOverlay.addEventListener('click', toggleMobileMenu);
            }

            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        if (navLinks && navLinks.classList.contains('active')) {
                            toggleMobileMenu();
                        }
                    }
                });
            });

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

            document.querySelectorAll('.feature-card, .testimonial-card').forEach(card => {
                featureObserver.observe(card);
            });

            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const hero = document.querySelector('.hero');
                const heroContent = document.querySelector('.hero-content');
                
                if (hero && scrolled < hero.offsetHeight) {
                    heroContent.style.transform = `translateY(${scrolled * 0.3}px)`;
                    hero.style.opacity = 1 - (scrolled / hero.offsetHeight) * 0.5;
                }
            });

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

            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.5s ease-in';
                document.body.style.opacity = '1';
            }, 100);
        }
    </script>
</body>
</html>