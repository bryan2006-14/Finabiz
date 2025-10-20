<?php
// Verificar si la sesión está activa
if (!isset($_SESSION['id_usuario'])) {
    return;
}
?>
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
            <a href="./inicio.php" class="nav-link active" onclick="closeSidebarOnMobile()">
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

            <a href="asistente.php" class="nav-link" onclick="closeSidebarOnMobile()">
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