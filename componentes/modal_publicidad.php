<?php
// Verificar si la sesión está activa
if (!isset($_SESSION['id_usuario'])) {
    return;
}
?>
<style>
    /* Variables de colores basados en la imagen */
    :root {
        --primary-dark: #0A2463;
        --primary: #1E3A8A;
        --primary-light: #3B82F6;
        --accent: #10B981;
        --accent-light: #34D399;
        --text-light: #F8FAFC;
        --text-gray: #94A3B8;
        --background: #0F172A;
        --card-bg: #1E293B;
        --border: #334155;
    }
    
    /* Modal de Publicidad */
    .ad-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.95);
        animation: fadeIn 0.6s ease-out;
    }

    .ad-modal-content {
        position: relative;
        margin: 2% auto;
        width: 90%;
        height: 96%;
        max-width: 1200px;
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--background) 50%, var(--card-bg) 100%);
        border-radius: 20px;
        box-shadow: 0 15px 60px rgba(0, 0, 0, 0.9);
        overflow: hidden;
        border: 1px solid var(--border);
        animation: modalSlideIn 0.7s ease-out;
    }

    .ad-close-btn {
        position: absolute;
        top: 25px;
        right: 30px;
        font-size: 40px;
        font-weight: bold;
        color: var(--text-light);
        cursor: pointer;
        z-index: 10000;
        background: rgba(239, 68, 68, 0.7);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .ad-close-btn:hover {
        background: rgba(239, 68, 68, 0.9);
        transform: scale(1.15) rotate(90deg);
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.6);
    }

    .ad-container {
        width: 100%;
        height: 100%;
        display: flex;
        padding: 0;
    }

    .ad-left-panel {
        flex: 1;
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: var(--text-light);
    }

    .ad-right-panel {
        flex: 1;
        background: var(--card-bg);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
        border-left: 1px solid var(--border);
    }

    .ad-content h1 {
        font-size: 2.8em;
        margin-bottom: 25px;
        color: var(--text-light);
        line-height: 1.2;
        font-weight: 700;
    }

    .ad-content h1 span {
        color: var(--accent);
    }

    .ad-content p {
        font-size: 1.3em;
        margin-bottom: 35px;
        color: var(--text-gray);
        line-height: 1.6;
    }

    .ad-features {
        margin-bottom: 40px;
    }

    .ad-feature {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        color: var(--text-light);
        font-size: 1.1em;
    }

    .ad-feature::before {
        content: "✓";
        color: var(--accent);
        font-weight: bold;
        margin-right: 10px;
        font-size: 1.3em;
    }

    /* Estilos para los botones de descarga */
    .download-buttons {
        display: flex;
        gap: 20px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .download-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 16px 24px;
        background: var(--card-bg);
        color: var(--text-light);
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1em;
        border: 2px solid var(--border);
        transition: all 0.3s ease;
        min-width: 200px;
        position: relative;
        overflow: hidden;
    }

    .download-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        background: var(--primary);
        border-color: var(--primary-light);
    }

    .download-btn .btn-icon {
        font-size: 1.8em;
        transition: transform 0.3s ease;
    }

    .download-btn:hover .btn-icon {
        transform: scale(1.1);
    }

    .btn-txt {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.2;
    }

    .btn-main-text {
        font-size: 0.9em;
        font-weight: 500;
        opacity: 0.9;
    }

    .btn-store-name {
        font-size: 1.2em;
        font-weight: 700;
    }

    .btn-apple {
        background: linear-gradient(135deg, #000000 0%, #333333 100%);
        border-color: #555555;
    }

    .btn-apple:hover {
        background: linear-gradient(135deg, #333333 0%, #555555 100%);
        border-color: #777777;
    }

    .btn-google {
        background: linear-gradient(135deg, #4285F4 0%, #34A853 100%);
        border-color: #4285F4;
    }

    .btn-google:hover {
        background: linear-gradient(135deg, #34A853 0%, #4285F4 100%);
        border-color: #34A853;
    }

    .ad-button {
        display: inline-block;
        padding: 18px 50px;
        background: linear-gradient(45deg, var(--accent), var(--accent-light));
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-size: 1.3em;
        font-weight: bold;
        transition: all 0.4s ease;
        box-shadow: 0 8px 30px rgba(16, 185, 129, 0.4);
        border: none;
        cursor: pointer;
        margin-top: 20px;
    }

    .ad-button:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(16, 185, 129, 0.6);
    }

    .app-preview {
        width: 100%;
        height: 100%;
        background: var(--primary-dark);
        border-radius: 20px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border);
    }

    .app-image-container {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        overflow: hidden;
        background: var(--card-bg);
    }

    .app-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 15px;
    }

    /* Animaciones */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes modalSlideIn {
        from { 
            opacity: 0;
            transform: scale(0.9) translateY(-50px);
        }
        to { 
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .ad-container {
            flex-direction: column;
        }
        
        .ad-right-panel {
            border-left: none;
            border-top: 1px solid var(--border);
        }
        
        .app-preview {
            height: 400px;
        }
        
        .download-buttons {
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .ad-modal-content {
            width: 95%;
            height: 98%;
            margin: 1% auto;
        }
        
        .ad-left-panel, .ad-right-panel {
            padding: 30px 20px;
        }
        
        .ad-content h1 {
            font-size: 2.2em;
        }
        
        .app-preview {
            height: 300px;
        }
        
        .download-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .download-btn {
            min-width: 250px;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .ad-content h1 {
            font-size: 1.8em;
        }
        
        .ad-content p {
            font-size: 1.1em;
        }
        
        .download-btn {
            min-width: 100%;
            padding: 14px 20px;
        }
        
        .ad-button {
            padding: 15px 30px;
            font-size: 1.1em;
        }
    }
</style>

<!-- Modal de Publicidad -->
<div id="ad-modal" class="ad-modal">
    <div class="ad-modal-content">
        <span class="ad-close-btn" id="ad-close-btn">&times;</span>
        <div class="ad-container">
            <div class="ad-left-panel">
                <div class="ad-content">
                    <h1>INVERTIR EN ACCIONES <span>NUNCA FUE TAN SEGURO</span></h1>
                    <p>Empieza a invertir en acciones en la Bolsa de Valores de Lima. Invierte en acciones de +90 de las empresas más grandes del Perú y el mundo. Compra y vende acciones de forma fácil, rápida y segura en una aplicación respaldada por Kalipa SAB, sociedad agente de bolsa supervisada por la SMV.</p>
                    
                    <div class="ad-features">
                        <div class="ad-feature">Inversiones desde $10</div>
                        <div class="ad-feature">Más de 90 empresas disponibles</div>
                        <div class="ad-feature">Plataforma regulada y segura</div>
                        <div class="ad-feature">Interfaz intuitiva y fácil de usar</div>
                    </div>
                    
                    <div class="download-buttons">
                        <a href="https://apps.apple.com/gb/app/trii/id1513826307" class="download-btn btn-apple" target="_blank">
                            <span class="btn-icon">
                                <i class="fab fa-apple"></i>
                            </span>
                            <span class="btn-txt">
                                <span class="btn-main-text">Descargar en</span>
                                <span class="btn-store-name">App Store</span>
                            </span>
                        </a>
                        
                        <a href="https://play.google.com/store/apps/details?id=com.triico.app" class="download-btn btn-google" target="_blank">
                            <span class="btn-icon">
                                <i class="fab fa-google-play"></i>
                            </span>
                            <span class="btn-txt">
                                <span class="btn-main-text">Disponible en</span>
                                <span class="btn-store-name">Google Play</span>
                            </span>
                        </a>
                    </div>
                    
                    <a href="#" class="ad-button">COMENZAR A INVERTIR</a>
                </div>
            </div>
            
            <div class="ad-right-panel">
                <div class="app-preview">
                    <div class="app-image-container">
                        <img src="https://trii.pe/wp-content/uploads/2022/07/Mockup01.png" alt="Trii App" class="app-image" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal de publicidad permanente
    document.addEventListener('DOMContentLoaded', function() {
        const adModal = document.getElementById('ad-modal');
        const closeBtn = document.getElementById('ad-close-btn');

        // Mostrar modal inmediatamente al cargar la página
        function showAdModal() {
            adModal.style.display = 'block';
            
            // Opcional: Guardar en sessionStorage para no mostrar muy frecuentemente
            sessionStorage.setItem('adLastShown', new Date().getTime());
        }

        function closeAdModal() {
            // Animación de salida
            adModal.style.opacity = '0';
            adModal.style.transform = 'scale(1.1)';
            
            setTimeout(() => {
                adModal.style.display = 'none';
            }, 400);
        }

        // Mostrar el modal (puedes controlar cuándo mostrarlo)
        showAdModal();

        // Event listeners
        closeBtn.addEventListener('click', closeAdModal);

        // Cerrar al hacer click fuera del contenido
        adModal.addEventListener('click', function(e) {
            if (e.target === adModal) {
                closeAdModal();
            }
        });

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && adModal.style.display === 'block') {
                closeAdModal();
            }
        });
    });
</script>