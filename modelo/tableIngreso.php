<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

// Usar $conn en lugar de $connection
if (!$conn) {
    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Error de conexión a la base de datos</td></tr>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No hay sesión activa</td></tr>";
    exit;
}

$mes = $_GET['mes'] ?? '';
$anio = $_GET['anio'] ?? '';

try {
    $sql = "SELECT * FROM ingresos WHERE usuario_id = :usuario_id";
    $params = [':usuario_id' => $id_usuario];

    if (!empty($mes) && !empty($anio)) {
        $sql .= " AND EXTRACT(MONTH FROM fecha) = :mes AND EXTRACT(YEAR FROM fecha) = :anio";
        $params[':mes'] = $mes;
        $params[':anio'] = $anio;
    }

    $sql .= " ORDER BY fecha DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result && count($result) > 0) {
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td class='amount-positive'>+ S/" . number_format($row['monto'], 2) . "</td>";
            echo "<td><span class='payment-method'>" . htmlspecialchars($row['forma_pago']) . "</span></td>";
            echo "<td>" . date('d/m/Y', strtotime($row['fecha'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['nota']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No se encontraron ingresos" . 
             (!empty($mes) && !empty($anio) ? " para el período seleccionado" : "") . "</td></tr>";
    }
} catch (PDOException $e) {
    error_log("Error en tableIngreso: " . $e->getMessage());
    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Error al cargar los datos</td></tr>";
}
?>