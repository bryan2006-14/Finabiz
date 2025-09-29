<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay usuario logueado redirige
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

// Valores seguros por defecto (evita "undefined variable")
$nombre = isset($_SESSION['nombre']) && $_SESSION['nombre'] !== '' ? $_SESSION['nombre'] : 'Usuario';
$fotoPerfil = isset($_SESSION['foto_perfil']) && $_SESSION['foto_perfil'] !== '' ? $_SESSION['foto_perfil'] : null;

$rutaDefault = 'recursos/img/default-avatar.png';
$rutaFotoPerfil = ($fotoPerfil && file_exists(__DIR__ . '/fotos/' . $fotoPerfil))
    ? 'fotos/' . $fotoPerfil
    : $rutaDefault;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icono-ic.png" sizes="96x96" type="image/x-icon">
    <link rel="stylesheet" href="css/reset.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Calculadora Financiera - ControlGastos</title>
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

        /* CALCULATOR SECTION */
        .calculator-section {
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

        /* TABS */
        .tabs-nav {
            display: flex;
            background: var(--gray-50);
            border-radius: 8px;
            padding: 0.5rem;
            margin-bottom: 2rem;
        }

        .tab-button {
            flex: 1;
            background: transparent;
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 6px;
            color: var(--gray-600);
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .tab-button:hover {
            background: var(--gray-200);
            color: var(--gray-800);
        }

        .tab-button.active {
            background: var(--primary);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* BASIC CALCULATOR */
        .calculator-basic {
            max-width: 400px;
            margin: 0 auto;
            background: var(--gray-50);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .calculator-display {
            background: var(--gray-900);
            padding: 1.5rem;
        }

        .calculator-display input {
            width: 100%;
            background: transparent;
            border: none;
            color: white;
            font-size: 2rem;
            font-weight: 600;
            text-align: right;
            outline: none;
        }

        .calculator-buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--gray-300);
        }

        .btn {
            border: none;
            padding: 1.5rem;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .btn:hover {
            background: var(--gray-50);
            transform: scale(1.02);
        }

        .btn-number {
            color: var(--gray-800);
        }

        .btn-operation {
            background: var(--gray-100);
            color: var(--primary);
        }

        .btn-operation:hover {
            background: var(--gray-200);
        }

        .btn-equals {
            background: var(--primary);
            color: white;
        }

        .btn-equals:hover {
            background: var(--primary-light);
        }

        .btn-clear {
            background: var(--danger);
            color: white;
        }

        .btn-clear:hover {
            background: #dc2626;
        }

        .btn-zero {
            grid-column: span 2;
        }

        /* FINANCIAL CALCULATORS */
        .financial-calculator {
            max-width: 500px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray-200);
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .btn-calculate {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none;
            padding: 1rem 2rem;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .btn-calculate:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .result-container {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
        }

        .result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-label {
            color: var(--gray-600);
            font-weight: 500;
        }

        .result-value {
            color: var(--gray-800);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .result-value.positive {
            color: var(--success);
        }

        .result-value.negative {
            color: var(--danger);
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

            .tabs-nav {
                flex-direction: column;
            }

            .calculator-buttons {
                grid-template-columns: repeat(4, 1fr);
            }

            .btn {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .calculator-buttons {
                grid-template-columns: repeat(4, 1fr);
            }

            .btn {
                padding: 0.75rem;
                font-size: 1rem;
            }
        }
    </style>
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
                <a href="./balance.php" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Balance</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-title">Herramientas</div>
                <a href="./calculadora.php" class="nav-link active">
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
            <h1 class="page-title">Calculadora Financiera</h1>
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

        <!-- Sección de calculadora -->
        <section class="calculator-section">
            <div class="section-header">
                <h2 class="section-title">Herramientas de Cálculo</h2>
            </div>

            <!-- Tabs Navigation -->
            <div class="tabs-nav">
                <button class="tab-button active" onclick="switchTab('basica')">
                    <i class="fas fa-calculator"></i>
                    Calculadora Básica
                </button>
                <button class="tab-button" onclick="switchTab('prestamo')">
                    <i class="fas fa-money-bill-wave"></i>
                    Simulador de Préstamo
                </button>
                <button class="tab-button" onclick="switchTab('inversion')">
                    <i class="fas fa-chart-line"></i>
                    Simulador de Inversión
                </button>
            </div>

            <!-- Calculadora Básica -->
            <div class="tab-content active" id="basica">
                <div class="calculator-basic">
                    <div class="calculator-display">
                        <input type="text" id="display" readonly value="0">
                    </div>
                    <div class="calculator-buttons">
                        <button class="btn btn-clear" onclick="clearDisplay()">C</button>
                        <button class="btn btn-clear" onclick="clearEntry()">CE</button>
                        <button class="btn btn-operation" onclick="deleteLast()">⌫</button>
                        <button class="btn btn-operation" onclick="appendOperation('/')">/</button>

                        <button class="btn btn-number" onclick="appendNumber('7')">7</button>
                        <button class="btn btn-number" onclick="appendNumber('8')">8</button>
                        <button class="btn btn-number" onclick="appendNumber('9')">9</button>
                        <button class="btn btn-operation" onclick="appendOperation('*')">×</button>

                        <button class="btn btn-number" onclick="appendNumber('4')">4</button>
                        <button class="btn btn-number" onclick="appendNumber('5')">5</button>
                        <button class="btn btn-number" onclick="appendNumber('6')">6</button>
                        <button class="btn btn-operation" onclick="appendOperation('-')">-</button>

                        <button class="btn btn-number" onclick="appendNumber('1')">1</button>
                        <button class="btn btn-number" onclick="appendNumber('2')">2</button>
                        <button class="btn btn-number" onclick="appendNumber('3')">3</button>
                        <button class="btn btn-operation" onclick="appendOperation('+')">+</button>

                        <button class="btn btn-number btn-zero" onclick="appendNumber('0')">0</button>
                        <button class="btn btn-number" onclick="appendNumber('.')">.</button>
                        <button class="btn btn-equals" onclick="calculate()">=</button>
                    </div>
                </div>
            </div>

            <!-- Calculadora de Préstamo -->
            <div class="tab-content" id="prestamo">
                <div class="financial-calculator">
                    <h3>Simulador de Préstamo</h3>
                    <div class="form-group">
                        <label class="form-label">Monto del Préstamo (S/)</label>
                        <input type="number" id="loanAmount" class="form-control" placeholder="Ej: 100000" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tasa de Interés Anual (%)</label>
                        <input type="number" id="loanRate" class="form-control" step="0.01" placeholder="Ej: 12.5" min="0" max="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Plazo (años)</label>
                        <input type="number" id="loanTerm" class="form-control" placeholder="Ej: 5" min="1" max="30">
                    </div>
                    <button class="btn-calculate" onclick="calculateLoan()">
                        <i class="fas fa-calculator"></i>
                        Calcular Préstamo
                    </button>
                    <div class="result-container" id="loanResult" style="display: none;"></div>
                </div>
            </div>

            <!-- Calculadora de Inversión -->
            <div class="tab-content" id="inversion">
                <div class="financial-calculator">
                    <h3>Simulador de Inversión</h3>
                    <div class="form-group">
                        <label class="form-label">Monto Inicial (S/)</label>
                        <input type="number" id="initialAmount" class="form-control" placeholder="Ej: 50000" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Aporte Mensual (S/)</label>
                        <input type="number" id="monthlyContribution" class="form-control" placeholder="Ej: 1000" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tasa de Interés Anual (%)</label>
                        <input type="number" id="interestRate" class="form-control" step="0.01" placeholder="Ej: 8.5" min="0" max="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Plazo (años)</label>
                        <input type="number" id="investmentTerm" class="form-control" placeholder="Ej: 10" min="1" max="50">
                    </div>
                    <button class="btn-calculate" onclick="calculateInvestment()">
                        <i class="fas fa-chart-line"></i>
                        Calcular Inversión
                    </button>
                    <div class="result-container" id="investmentResult" style="display: none;"></div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentInput = '0';
        let previousInput = '';
        let operation = null;
        let resetScreen = false;

        // Sistema de tabs
        function switchTab(tabName) {
            // Ocultar todos los tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remover active de todos los botones
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Mostrar tab seleccionado
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        // Calculadora básica
        function updateDisplay() {
            const display = document.getElementById('display');
            display.value = currentInput;
        }

        function appendNumber(number) {
            if (currentInput === '0' || resetScreen) {
                currentInput = number;
                resetScreen = false;
            } else {
                currentInput += number;
            }
            updateDisplay();
        }

        function appendOperation(op) {
            if (operation !== null) calculate();
            previousInput = currentInput;
            operation = op;
            resetScreen = true;
        }

        function calculate() {
            let computation;
            const prev = parseFloat(previousInput);
            const current = parseFloat(currentInput);
            
            if (isNaN(prev) || isNaN(current)) return;
            
            switch (operation) {
                case '+':
                    computation = prev + current;
                    break;
                case '-':
                    computation = prev - current;
                    break;
                case '*':
                    computation = prev * current;
                    break;
                case '/':
                    computation = prev / current;
                    break;
                default:
                    return;
            }
            
            currentInput = computation.toString();
            operation = null;
            previousInput = '';
            updateDisplay();
        }

        function clearDisplay() {
            currentInput = '0';
            previousInput = '';
            operation = null;
            updateDisplay();
        }

        function clearEntry() {
            currentInput = '0';
            updateDisplay();
        }

        function deleteLast() {
            if (currentInput.length === 1) {
                currentInput = '0';
            } else {
                currentInput = currentInput.slice(0, -1);
            }
            updateDisplay();
        }

        // Calculadora de préstamo
        function calculateLoan() {
            const amount = parseFloat(document.getElementById('loanAmount').value);
            const rate = parseFloat(document.getElementById('loanRate').value);
            const term = parseFloat(document.getElementById('loanTerm').value);
            
            if (!amount || !rate || !term) {
                alert('Por favor complete todos los campos');
                return;
            }
            
            const monthlyRate = rate / 100 / 12;
            const numberOfPayments = term * 12;
            
            const monthlyPayment = amount * 
                (monthlyRate * Math.pow(1 + monthlyRate, numberOfPayments)) / 
                (Math.pow(1 + monthlyRate, numberOfPayments) - 1);
            
            const totalPayment = monthlyPayment * numberOfPayments;
            const totalInterest = totalPayment - amount;
            
            const result = document.getElementById('loanResult');
            result.innerHTML = `
                <div class="result-item">
                    <span class="result-label">Cuota Mensual:</span>
                    <span class="result-value">S/${monthlyPayment.toFixed(2)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Total a Pagar:</span>
                    <span class="result-value">S/${totalPayment.toFixed(2)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Interés Total:</span>
                    <span class="result-value negative">S/${totalInterest.toFixed(2)}</span>
                </div>
            `;
            result.style.display = 'block';
        }

        // Calculadora de inversión
        function calculateInvestment() {
            const initial = parseFloat(document.getElementById('initialAmount').value);
            const monthly = parseFloat(document.getElementById('monthlyContribution').value);
            const rate = parseFloat(document.getElementById('interestRate').value);
            const term = parseFloat(document.getElementById('investmentTerm').value);
            
            if (!initial && !monthly) {
                alert('Por favor ingrese al menos un monto inicial o aporte mensual');
                return;
            }
            if (!rate || !term) {
                alert('Por favor complete la tasa de interés y el plazo');
                return;
            }
            
            const monthlyRate = rate / 100 / 12;
            const months = term * 12;
            
            let futureValue = initial * Math.pow(1 + monthlyRate, months);
            
            if (monthly > 0) {
                futureValue += monthly * ((Math.pow(1 + monthlyRate, months) - 1) / monthlyRate) * (1 + monthlyRate);
            }
            
            const totalContributions = initial + (monthly * months);
            const interestEarned = futureValue - totalContributions;
            
            const result = document.getElementById('investmentResult');
            result.innerHTML = `
                <div class="result-item">
                    <span class="result-label">Valor Futuro:</span>
                    <span class="result-value positive">S/${futureValue.toFixed(2)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Total Aportado:</span>
                    <span class="result-value">S/${totalContributions.toFixed(2)}</span>
                </div>
                <div class="result-item">
                    <span class="result-label">Interés Ganado:</span>
                    <span class="result-value positive">S/${interestEarned.toFixed(2)}</span>
                </div>
            `;
            result.style.display = 'block';
        }

        // Inicializar calculadora básica
        document.addEventListener('DOMContentLoaded', function() {
            updateDisplay();
            
            // Agregar soporte para teclado
            document.addEventListener('keydown', function(event) {
                if (event.key >= '0' && event.key <= '9') {
                    appendNumber(event.key);
                } else if (event.key === '.') {
                    appendNumber('.');
                } else if (event.key === '+' || event.key === '-' || event.key === '*' || event.key === '/') {
                    appendOperation(event.key);
                } else if (event.key === 'Enter' || event.key === '=') {
                    calculate();
                } else if (event.key === 'Escape') {
                    clearDisplay();
                } else if (event.key === 'Backspace') {
                    deleteLast();
                }
            });
        });
    </script>
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