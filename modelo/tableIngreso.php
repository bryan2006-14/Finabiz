<?php
// Inicia sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No hay sesión activa</td></tr>";
    exit;
}

// Obtener parámetros de filtro directamente de $_GET
$mes = isset($_GET['mes']) ? $_GET['mes'] : '';
$anio = isset($_GET['anio']) ? $_GET['anio'] : '';

// Construir la consulta base - MOSTRAR TODOS si no hay filtros
$sql = "SELECT * FROM ingresos WHERE id_usuario = :id";
$params = [':id' => $id_usuario];

// Agregar filtros SOLO si ambos están presentes y no están vacíos
if (!empty($mes) && !empty($anio)) {
    $sql .= " AND EXTRACT(MONTH FROM fecha) = :mes AND EXTRACT(YEAR FROM fecha) = :anio";
    $params[':mes'] = $mes;
    $params[':anio'] = $anio;
}

$sql .= " ORDER BY fecha DESC";

$stmt = $connection->prepare($sql);
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
?>