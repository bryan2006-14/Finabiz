<?php
// No iniciar sesión si ya está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Incluir conexión
require_once 'conexion.php';

$id_usuario = $_SESSION['id_usuario'];

// Obtener parámetros de filtro
$mesSeleccionado = isset($_GET['mes']) ? $_GET['mes'] : '';
$anioSeleccionado = isset($_GET['anio']) ? $_GET['anio'] : '';

// Determinar qué página nos está incluyendo
$current_page = basename($_SERVER['PHP_SELF']);

try {
    if ($current_page == 'gasto.php') {
        // Consulta para gastos
        $sql = "SELECT 
            g.monto,
            g.forma_pago,
            g.fecha,
            g.nota,
            c.categoria as nombre_categoria
        FROM gastos g 
        LEFT JOIN categorias c ON g.categoria_id = c.id 
        WHERE g.usuario_id = ?";
        
        if (!empty($mesSeleccionado) && !empty($anioSeleccionado)) {
            $sql .= " AND MONTH(g.fecha) = ? AND YEAR(g.fecha) = ?";
        }
        
        $sql .= " ORDER BY g.fecha DESC";
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($mesSeleccionado) && !empty($anioSeleccionado)) {
            $stmt->bind_param("iss", $id_usuario, $mesSeleccionado, $anioSeleccionado);
        } else {
            $stmt->bind_param("i", $id_usuario);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categoria_nombre = $row['nombre_categoria'] ?? 'Sin categoría';
                $categoria_class = 'category-other';
                
                if (strpos(strtolower($categoria_nombre), 'comida') !== false) $categoria_class = 'category-food';
                elseif (strpos(strtolower($categoria_nombre), 'transporte') !== false) $categoria_class = 'category-transport';
                elseif (strpos(strtolower($categoria_nombre), 'vivienda') !== false) $categoria_class = 'category-housing';
                elseif (strpos(strtolower($categoria_nombre), 'entretenimiento') !== false) $categoria_class = 'category-entertainment';
                
                echo "<tr>";
                echo "<td class='amount-negative'>- S/ " . number_format($row['monto'], 2) . "</td>";
                echo "<td><span class='payment-method'>" . htmlspecialchars($row['forma_pago']) . "</span></td>";
                echo "<td>" . date('d/m/Y', strtotime($row['fecha'])) . "</td>";
                echo "<td><span class='category-badge $categoria_class'>" . htmlspecialchars($categoria_nombre) . "</span></td>";
                echo "<td>" . htmlspecialchars($row['nota']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align: center; padding: 2rem; color: var(--gray-500);'>No hay gastos registrados</td></tr>";
        }
        
        $stmt->close();
        
    } elseif ($current_page == 'ingreso.php') {
        // Consulta para ingresos
        $sql = "SELECT 
            monto,
            forma_pago,
            fecha,
            nota
        FROM ingresos 
        WHERE usuario_id = ?";
        
        if (!empty($mesSeleccionado) && !empty($anioSeleccionado)) {
            $sql .= " AND MONTH(fecha) = ? AND YEAR(fecha) = ?";
        }
        
        $sql .= " ORDER BY fecha DESC";
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($mesSeleccionado) && !empty($anioSeleccionado)) {
            $stmt->bind_param("iss", $id_usuario, $mesSeleccionado, $anioSeleccionado);
        } else {
            $stmt->bind_param("i", $id_usuario);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td class='amount-positive'>+ S/ " . number_format($row['monto'], 2) . "</td>";
                echo "<td><span class='payment-method'>" . htmlspecialchars($row['forma_pago']) . "</span></td>";
                echo "<td>" . date('d/m/Y', strtotime($row['fecha'])) . "</td>";
                echo "<td>" . htmlspecialchars($row['nota']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' style='text-align: center; padding: 2rem; color: var(--gray-500);'>No hay ingresos registrados</td></tr>";
        }
        
        $stmt->close();
    }
    
} catch (Exception $e) {
    echo "<tr><td colspan='5' style='text-align: center; padding: 2rem; color: var(--danger);'>Error al cargar los datos: " . $e->getMessage() . "</td></tr>";
}

// No cerrar la conexión aquí para que la página principal pueda usarla
?>