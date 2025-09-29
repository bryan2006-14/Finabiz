<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location:index.php");
}
$nombre = $_SESSION['nombre'];
$fotoPerfil = $_SESSION['foto_perfil'];
$rutaFotoPerfil = "fotos/" . $fotoPerfil;
// Verificar si la imagen existe, usar una por defecto si no
$rutaDefault = "recursos/img/default-avatar.png";
$rutaFotoPerfil = (!empty($fotoPerfil) && file_exists("fotos/" . $fotoPerfil))
    ? "fotos/" . $fotoPerfil
    : $rutaDefault;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icono-ic.png" sizes="32x32" type="image/x-icon">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/inicio/inicio.css">
    <link rel="stylesheet" href="css/inicio/calculadora.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Inicio - ControlGastos</title>
</head>

<body>
    <!-- Barra de navegación lateral -->
    <nav class="sidebar">
<div class="brand-logo">
    <img src="logo_Finabiz.png" alt="Finabiz Logo" class="brand-logo-img">
</div>

        <div class="nav-links">
            <div class="nav-section">
                <div class="nav-title">Home</div>
                <a href="./inicio.php" class="nav-link active">
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
                <a href="./balance.php" class="nav-link">
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
            <h1 class="page-title">Panel de Control</h1>
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

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <i class="fas fa-hand-wave"></i>
            <div class="welcome-text">¡Bienvenido de nuevo! <span><?php echo $nombre; ?></span></div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="cards-container">
            <div class="card card-income">
                <div class="card-content">
                    <h3>Ingreso Total</h3>
                    <div class="amount">S/<?php include 'modelo/totalIngreso.php'; ?></div>
                    <div class="card-trend">
                        <span class="trend-up">
                            <i class="fas fa-arrow-up"></i>
                            +8.2%
                        </span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>

            <div class="card card-expense">
                <div class="card-content">
                    <h3>Gasto Total</h3>
                    <div class="amount">S/<?php include 'modelo/total.php'; ?></div>
                    <div class="card-trend">
                        <span class="trend-down">
                            <i class="fas fa-arrow-down"></i>
                            -3.5%
                        </span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
            </div>

            <div class="card card-budget">
                <div class="card-content">
                    <h3>Balance Actual</h3>
                    <div class="amount">S/<?php
                                            $ingresos = file_get_contents('modelo/totalIngreso.php');
                                            $gastos = file_get_contents('modelo/total.php');
                                            echo number_format(floatval($ingresos) - floatval($gastos), 2);
                                            ?></div>
                    <div class="card-trend">
                        <span class="trend-up">
                            <i class="fas fa-arrow-up"></i>
                            +12.7%
                        </span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            <div class="card card-savings">
                <div class="card-content">
                    <h3>Ahorros del Mes</h3>
                    <div class="amount">S/1,245.50</div>
                    <div class="card-trend">
                        <span class="trend-up">
                            <i class="fas fa-arrow-up"></i>
                            +15.3%
                        </span>
                        <span class="trend-text">vs mes anterior</span>
                    </div>
                </div>
                <div class="card-icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
            </div>
        </div>

        <!-- Gráfico principal -->
        <div class="chart-section">
            <div class="section-header">
                <h2>Resumen Financiero</h2>
                <div class="chart-controls">
                    <button class="chart-btn active" onclick="changeChartType('doughnut')">
                        <i class="fas fa-chart-pie"></i>
                    </button>
                    <button class="chart-btn" onclick="changeChartType('bar')">
                        <i class="fas fa-chart-bar"></i>
                    </button>
                    <button class="chart-btn" onclick="changeChartType('line')">
                        <i class="fas fa-chart-line"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="financialChart"></canvas>
            </div>
        </div>

        <!-- Análisis de gastos -->
        <div class="expenses-section">
            <div class="section-header">
                <h3>Gastos por Categoría</h3>
            </div>
            <div class="expenses-grid">
                <div class="expense-item">
                    <div class="expense-icon food">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="expense-info">
                        <span class="expense-category">Alimentación</span>
                        <span class="expense-amount">S/425.80</span>
                        <div class="expense-bar">
                            <div class="expense-fill" style="width: 35%"></div>
                        </div>
                    </div>
                    <div class="expense-percentage">35%</div>
                </div>
                
                <div class="expense-item">
                    <div class="expense-icon transport">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="expense-info">
                        <span class="expense-category">Transporte</span>
                        <span class="expense-amount">S/180.50</span>
                        <div class="expense-bar">
                            <div class="expense-fill" style="width: 22%"></div>
                        </div>
                    </div>
                    <div class="expense-percentage">22%</div>
                </div>

                <div class="expense-item">
                    <div class="expense-icon entertainment">
                        <i class="fas fa-film"></i>
                    </div>
                    <div class="expense-info">
                        <span class="expense-category">Entretenimiento</span>
                        <span class="expense-amount">S/120.00</span>
                        <div class="expense-bar">
                            <div class="expense-fill" style="width: 15%"></div>
                        </div>
                    </div>
                    <div class="expense-percentage">15%</div>
                </div>

                <div class="expense-item">
                    <div class="expense-icon health">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="expense-info">
                        <span class="expense-category">Salud</span>
                        <span class="expense-amount">S/95.30</span>
                        <div class="expense-bar">
                            <div class="expense-fill" style="width: 12%"></div>
                        </div>
                    </div>
                    <div class="expense-percentage">12%</div>
                </div>
            </div>
        </div>
    </main>

<!-- ChatBot Mejorado -->
<div id="chatbot-container" class="chatbot-container">
    <div class="chatbot-header">
        <div class="bot-info">
            <div class="bot-avatar">
                <i class="fas fa-robot"></i>
                <div class="avatar-status"></div>
            </div>
            <div class="bot-details">
                <span class="bot-name">Asistente Financiero</span>
                <span class="bot-status">En línea</span>
            </div>
        </div>
        <div class="chatbot-controls">
            <button id="expand-chat" class="control-btn" title="Expandir">
                <i class="fas fa-expand"></i>
            </button>
            <button id="minimize-chat" class="control-btn" title="Minimizar">
                <i class="fas fa-minus"></i>
            </button>
            <button id="close-chat" class="control-btn" title="Cerrar">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div class="chatbot-body">
        <div class="chat-suggestions">
            <div class="suggestion" onclick="sendQuickMessage('¿Cómo puedo ahorrar más dinero?')">
                <i class="fas fa-piggy-bank"></i>
                <span>Consejos de ahorro</span>
            </div>
            <div class="suggestion" onclick="sendQuickMessage('Analiza mis gastos')">
                <i class="fas fa-chart-bar"></i>
                <span>Análisis de gastos</span>
            </div>
            <div class="suggestion" onclick="sendQuickMessage('¿Cómo hacer un presupuesto?')">
                <i class="fas fa-calculator"></i>
                <span>Crear presupuesto</span>
            </div>
        </div>

        <div id="chat-messages" class="chat-messages"></div>

        <div class="chat-input-area">
            <div class="input-container">
                <input type="text" id="chat-input" placeholder="Escribe tu pregunta..." maxlength="500" disabled>
                <button id="stop-btn" class="control-button stop-button" title="Detener respuesta" style="display: none;">
                    <i class="fas fa-stop"></i>
                </button>
                <button id="send-btn" class="send-button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Chat FAB -->
<div id="chat-fab" class="chat-fab">
    <i class="fas fa-comments"></i>
    <span class="notification">1</span>
</div>

<script>
    // Variables globales
    let chatVisible = false;
    let isTyping = false;
    let isExpanded = false;
    let typingInterval = null;
    let currentChart = null;

    // Inicializar cuando carga la página
    document.addEventListener('DOMContentLoaded', function() {
        initializeChart();
        initializeChat();
        showWelcomeMessage();
    });

    // Inicializar gráfico
    function initializeChart() {
        const ctx = document.getElementById('financialChart').getContext('2d');
        currentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Ingresos', 'Gastos', 'Ahorros'],
                datasets: [{
                    data: [3500, 1200, 800],
                    backgroundColor: ['#10b981', '#ef4444', '#3b82f6'],
                    borderWidth: 0,
                    cutout: '65%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Inicializar chat
    function initializeChat() {
        // Event listeners
        document.getElementById('chat-fab').addEventListener('click', toggleChat);
        document.getElementById('minimize-chat').addEventListener('click', minimizeChat);
        document.getElementById('expand-chat').addEventListener('click', expandChat);
        document.getElementById('close-chat').addEventListener('click', closeChat);
        document.getElementById('send-btn').addEventListener('click', sendMessage);
        document.getElementById('stop-btn').addEventListener('click', stopTyping);
        document.getElementById('chat-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !isTyping) {
                sendMessage();
            }
        });
    }

    function showWelcomeMessage() {
        setTimeout(() => {
            addBotMessage("¡Hola! 👋 Soy tu asistente financiero. Puedo ayudarte con análisis de gastos, consejos de ahorro y presupuestos. ¿En qué puedo ayudarte hoy?");
        }, 1000);
    }

    function toggleChat() {
        const container = document.getElementById('chatbot-container');
        const fab = document.getElementById('chat-fab');
        
        chatVisible = !chatVisible;
        
        if (chatVisible) {
            container.style.display = 'flex';
            fab.style.display = 'none';
            setTimeout(() => {
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 10);
        } else {
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            setTimeout(() => {
                container.style.display = 'none';
                fab.style.display = 'flex';
            }, 300);
        }
    }

    function minimizeChat() {
        const container = document.getElementById('chatbot-container');
        const fab = document.getElementById('chat-fab');
        
        container.style.opacity = '0';
        container.style.transform = 'translateY(20px)';
        setTimeout(() => {
            container.style.display = 'none';
            fab.style.display = 'flex';
        }, 300);
        
        chatVisible = false;
    }

    function expandChat() {
        const container = document.getElementById('chatbot-container');
        isExpanded = !isExpanded;
        
        if (isExpanded) {
            container.style.width = '80%';
            container.style.height = '80%';
            container.style.maxWidth = '1000px';
            container.style.maxHeight = '700px';
            document.getElementById('expand-chat').innerHTML = '<i class="fas fa-compress"></i>';
            document.getElementById('expand-chat').title = 'Contraer';
        } else {
            container.style.width = '350px';
            container.style.height = '500px';
            container.style.maxWidth = 'none';
            container.style.maxHeight = 'none';
            document.getElementById('expand-chat').innerHTML = '<i class="fas fa-expand"></i>';
            document.getElementById('expand-chat').title = 'Expandir';
        }
    }

    function closeChat() {
        minimizeChat();
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
        
        // Ocultar sugerencias
        document.querySelector('.chat-suggestions').style.display = 'none';
        
        addUserMessage(message);
        input.value = '';
        
        // Deshabilitar entrada mientras el bot responde
        input.disabled = true;
        isTyping = true;
        
        // Mostrar botón de detener
        document.getElementById('stop-btn').style.display = 'block';
        
        showTypingIndicator();
        
        // Simular respuesta del bot después de un breve retraso
        setTimeout(() => {
            hideTypingIndicator();
            const response = getBotResponse(message);
            simulateTyping(response);
        }, 1000);
    }

    function stopTyping() {
        if (typingInterval) {
            clearInterval(typingInterval);
            typingInterval = null;
        }
        
        // Limpiar cualquier indicador de escritura
        hideTypingIndicator();
        
        // Habilitar entrada
        document.getElementById('chat-input').disabled = false;
        isTyping = false;
        
        // Ocultar botón de detener
        document.getElementById('stop-btn').style.display = 'none';
        
        // Mostrar mensaje de interrupción
        const messagesContainer = document.getElementById('chat-messages');
        const interruptedElement = document.createElement('div');
        interruptedElement.className = 'message bot-message interrupted';
        interruptedElement.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <p>Respuesta interrumpida. ¿En qué más puedo ayudarte?</p>
                <span class="message-time">${getCurrentTime()}</span>
            </div>
        `;
        messagesContainer.appendChild(interruptedElement);
        scrollToBottom();
    }

    function simulateTyping(message) {
        const messagesContainer = document.getElementById('chat-messages');
        const messageElement = document.createElement('div');
        messageElement.className = 'message bot-message';
        messageElement.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <p></p>
                <span class="message-time">${getCurrentTime()}</span>
            </div>
        `;
        messagesContainer.appendChild(messageElement);
        
        const textElement = messageElement.querySelector('p');
        let index = 0;
        const typingSpeed = 30; // ms por caracter
        
        // Limpiar intervalo previo si existe
        if (typingInterval) {
            clearInterval(typingInterval);
        }
        
        typingInterval = setInterval(() => {
            if (index < message.length) {
                textElement.textContent += message[index];
                index++;
                scrollToBottom();
            } else {
                clearInterval(typingInterval);
                typingInterval = null;
                
                // Habilitar entrada
                document.getElementById('chat-input').disabled = false;
                isTyping = false;
                
                // Ocultar botón de detener
                document.getElementById('stop-btn').style.display = 'none';
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
                <img src="<?php echo $rutaFotoPerfil; ?>" alt="Usuario">
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
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
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
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="typing-content">
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                </div>
                <span class="typing-text">Escribiendo...</span>
            </div>
        `;
        messagesContainer.appendChild(typingElement);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    function getBotResponse(message) {
        const lowerMessage = message.toLowerCase();
        
        if (lowerMessage.includes('ahorro') || lowerMessage.includes('ahorrar')) {
            return "Te recomiendo seguir la regla 50/30/20: 50% para gastos necesarios, 30% para gastos personales y 20% para ahorros. Automatiza tus ahorros para que sea más fácil y consistente. También considera establecer metas de ahorro específicas para mantenerte motivado.";
        } else if (lowerMessage.includes('gasto') || lowerMessage.includes('analiza')) {
            return "Tus principales categorías de gasto son: Alimentación (35%), Transporte (22%), Entretenimiento (15%) y Salud (12%). Te sugiero revisar la categoría de entretenimiento, ya que representa un 15% de tus gastos. Considera establecer un límite mensual para esta categoría.";
        } else if (lowerMessage.includes('presupuesto')) {
            return "Para crear un presupuesto efectivo: 1) Registra todos tus ingresos, 2) Clasifica tus gastos en categorías, 3) Establece límites realistas para cada categoría, 4) Haz un seguimiento regular y ajusta según sea necesario. La clave es ser constante y revisar tu presupuesto semanalmente.";
        } else if (lowerMessage.includes('balance') || lowerMessage.includes('resumen')) {
            return "Tu situación financiera actual es positiva. Tienes un balance favorable con una tendencia de crecimiento del 12.7% respecto al mes anterior. Tus ahorros han aumentado un 15.3% este mes, lo que indica que vas por buen camino. Sigue así!";
        } else if (lowerMessage.includes('inversión') || lowerMessage.includes('invertir')) {
            return "Para comenzar a invertir, te recomiendo: 1) Establecer un fondo de emergencia primero, 2) Definir tus objetivos financieros, 3) Comprender tu tolerancia al riesgo, 4) Diversificar tus inversiones. Considera opciones como fondos indexados o cuentas de ahorro de alto rendimiento para empezar.";
        } else {
            return "Puedo ayudarte con: análisis de gastos, consejos de ahorro, creación de presupuestos, estrategias de inversión y seguimiento de metas financieras. ¿Hay algo específico sobre lo que te gustaría hablar?";
        }
    }

    function getCurrentTime() {
        return new Date().toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    function scrollToBottom() {
        const messagesContainer = document.getElementById('chat-messages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
</script>

<style>
    /* Estilos mejorados para el chatbot */
    .chatbot-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 350px;
        height: 500px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        display: none;
        flex-direction: column;
        z-index: 1001;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }

    .chatbot-header {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: white;
        padding: 1rem 1.25rem;
        border-radius: 16px 16px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .bot-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .bot-avatar {
        position: relative;
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .avatar-status {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 8px;
        height: 8px;
        background: #10b981;
        border: 2px solid #4f46e5;
        border-radius: 50%;
    }

    .bot-details {
        display: flex;
        flex-direction: column;
    }

    .bot-name {
        font-weight: 600;
        font-size: 0.875rem;
    }

    .bot-status {
        font-size: 0.75rem;
        opacity: 0.9;
    }

    .chatbot-controls {
        display: flex;
        gap: 0.25rem;
    }

    .control-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 0.75rem;
    }

    .control-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .chatbot-body {
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
    }

    .chat-suggestions {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        background: #f9fafb;
    }

    .suggestion {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.875rem;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .suggestion:hover {
        background: #4f46e5;
        color: white;
        transform: translateX(2px);
        border-color: #4f46e5;
    }

    .suggestion i {
        width: 16px;
        font-size: 0.875rem;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 3px;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    .message {
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
        animation: messageSlide 0.3s ease;
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

    .user-message {
        flex-direction: row-reverse;
    }

    .message-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .user-message .message-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #4f46e5;
    }

    .bot-message .message-avatar {
        background: #4f46e5;
        color: white;
        font-size: 0.875rem;
    }

    .message-content {
        max-width: 75%;
        background: white;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .user-message .message-content {
        background: #4f46e5;
        color: white;
        border-bottom-right-radius: 4px;
    }

    .bot-message .message-content {
        border-bottom-left-radius: 4px;
    }

    .message-content p {
        margin: 0;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .message-time {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.7);
        margin-top: 0.25rem;
        display: block;
    }

    .bot-message .message-time {
        color: #6b7280;
    }

    .typing-indicator {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .typing-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .typing-dots {
        display: flex;
        gap: 0.25rem;
        padding: 0.75rem 1rem;
        background: white;
        border-radius: 12px 12px 12px 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .typing-dots span {
        width: 6px;
        height: 6px;
        background: #9ca3af;
        border-radius: 50%;
        animation: typingDot 1.4s infinite;
    }

    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; }

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

    .typing-text {
        font-size: 0.75rem;
        color: #6b7280;
        margin-left: 0.5rem;
    }

    .chat-input-area {
        border-top: 1px solid #e5e7eb;
        padding: 1rem;
        background: white;
        border-radius: 0 0 16px 16px;
    }

    .input-container {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        background: #f3f4f6;
        border-radius: 20px;
        padding: 0.5rem;
    }

    #chat-input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        outline: none;
        color: #374151;
    }

    #chat-input:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    #chat-input::placeholder {
        color: #9ca3af;
    }

    .control-button {
        background: transparent;
        border: none;
        color: #6b7280;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .stop-button {
        color: #ef4444;
    }

    .control-button:hover {
        background: #e5e7eb;
    }

    .send-button {
        background: #4f46e5;
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .send-button:hover {
        background: #6366f1;
        transform: scale(1.05);
    }

    .send-button:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    /* CHAT FAB */
    .chat-fab {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .chat-fab:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.5);
    }

    .notification {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #ef4444;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.125rem 0.375rem;
        border-radius: 10px;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }

    /* Mensaje de interrupción */
    .interrupted .message-content {
        background: #fef3c7;
        color: #92400e;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .chatbot-container {
            width: calc(100vw - 40px);
            height: calc(100vh - 100px);
            bottom: 20px;
            right: 20px;
            left: 20px;
        }
        
        .chatbot-container.expanded {
            width: calc(100vw - 40px);
            height: calc(100vh - 100px);
        }
    }
</style>

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

        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .welcome-banner i {
            font-size: 2rem;
        }

        .welcome-text {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .welcome-text span {
            color: #fbbf24;
        }

        /* CARDS */
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .card-income::before { background: var(--success); }
        .card-expense::before { background: var(--danger); }
        .card-budget::before { background: var(--primary); }
        .card-savings::before { background: var(--warning); }

        .card-content h3 {
            color: var(--gray-500);
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .card-trend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .trend-up {
            color: var(--success);
            font-weight: 600;
        }

        .trend-down {
            color: var(--danger);
            font-weight: 600;
        }

        .trend-text {
            color: var(--gray-500);
        }

        .card-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            opacity: 0.8;
        }

        .card-income .card-icon { background: var(--success); }
        .card-expense .card-icon { background: var(--danger); }
        .card-budget .card-icon { background: var(--primary); }
        .card-savings .card-icon { background: var(--warning); }

        /* CHART SECTION */
        .chart-section {
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
            margin-bottom: 1.5rem;
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
        }

        .chart-controls {
            display: flex;
            gap: 0.5rem;
        }

        .chart-btn {
            background: var(--gray-100);
            border: none;
            padding: 0.5rem;
            border-radius: 8px;
            color: var(--gray-600);
            cursor: pointer;
            transition: all 0.3s ease;
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

        /* EXPENSES SECTION */
        .expenses-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .section-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .expenses-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .expense-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
        }

        .expense-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        .expense-icon.food { background: var(--warning); }
        .expense-icon.transport { background: var(--info); }
        .expense-icon.entertainment { background: #8b5cf6; }
        .expense-icon.health { background: var(--danger); }

        .expense-info {
            flex: 1;
        }

        .expense-category {
            font-weight: 600;
            color: var(--gray-800);
            display: block;
            margin-bottom: 0.25rem;
        }

        .expense-amount {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .expense-bar {
            background: var(--gray-200);
            height: 4px;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .expense-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 2px;
            transition: width 0.6s ease;
        }

        .expense-percentage {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.875rem;
        }

        /* CHATBOT STYLES */
        .chatbot-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            z-index: 1001;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .chatbot-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 1rem 1.25rem;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bot-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .bot-avatar {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .bot-details {
            display: flex;
            flex-direction: column;
        }

        .bot-name {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .bot-status {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        .chatbot-controls {
            display: flex;
            gap: 0.25rem;
        }

        .control-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 0.75rem;
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .chatbot-body {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .chat-suggestions {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .suggestion {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            color: var(--gray-700);
        }

        .suggestion:hover {
            background: var(--primary);
            color: white;
            transform: translateX(2px);
        }

        .suggestion i {
            width: 16px;
            font-size: 0.875rem;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            background: var(--gray-50);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .chat-messages::-webkit-scrollbar {
            width: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 2px;
        }

        .message {
            display: flex;
            gap: 0.5rem;
            align-items: flex-end;
            animation: messageSlide 0.3s ease;
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

        .user-message {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-message .message-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .bot-message .message-avatar {
            background: var(--primary);
            color: white;
            font-size: 0.75rem;
        }

        .message-content {
            max-width: 75%;
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .user-message .message-content {
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .bot-message .message-content {
            border-bottom-left-radius: 4px;
        }

        .message-content p {
            margin: 0;
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .message-time {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.25rem;
            display: block;
        }

        .bot-message .message-time {
            color: var(--gray-500);
        }

        .typing-indicator {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .typing-dots {
            display: flex;
            gap: 0.25rem;
            padding: 0.75rem 1rem;
            background: white;
            border-radius: 12px 12px 12px 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .typing-dots span {
            width: 6px;
            height: 6px;
            background: var(--gray-400);
            border-radius: 50%;
            animation: typingDot 1.4s infinite;
        }

        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }

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

        .chat-input-area {
            border-top: 1px solid var(--gray-200);
            padding: 1rem;
            background: white;
            border-radius: 0 0 16px 16px;
        }

        .input-container {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            background: var(--gray-100);
            border-radius: 20px;
            padding: 0.5rem;
        }

        #chat-input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            outline: none;
            color: var(--gray-800);
        }

        #chat-input::placeholder {
            color: var(--gray-500);
        }

        .send-button {
            background: var(--primary);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .send-button:hover {
            background: var(--primary-light);
            transform: scale(1.05);
        }

        .send-button:disabled {
            background: var(--gray-400);
            cursor: not-allowed;
            transform: none;
        }

        /* CHAT FAB */
        .chat-fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .chat-fab:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.5);
        }

        .notification {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.125rem 0.375rem;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
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

            .cards-container {
                grid-template-columns: 1fr;
            }

            .chatbot-container {
                width: calc(100vw - 40px);
                height: calc(100vh - 100px);
                bottom: 20px;
                right: 20px;
                left: 20px;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }

        /* ANIMATIONS */
        .card {
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .expense-item:hover {
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
    <style>
   /* Contenedor del logo */
.brand-logo {
    width: 260px; /* ancho para logos horizontales */
    height: 110px; /* altura rectangular */
    border: none; /* sin borde */
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    animation: logoFloat 4s ease-in-out infinite;
    padding: 12px 18px;
    overflow: hidden;
}

/* Imagen del logo */
.brand-logo-img {
    max-width: 100%;   /* ocupa todo el ancho disponible */
    max-height: 100%;  /* no se pasa de la altura del contenedor */
    object-fit: contain; /* mantiene proporciones */
    display: block;
}


</style>
</body>

</html>