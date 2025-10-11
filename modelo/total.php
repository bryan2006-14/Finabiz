<?php
// Inicia sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    echo "0.00";
    exit;
}

// Obtener parámetros de filtro
$mes = isset($_GET['mes']) ? $_GET['mes'] : '';
$anio = isset($_GET['anio']) ? $_GET['anio'] : '';

// Consulta base
$sql = "SELECT SUM(monto) AS total_gastos FROM gastos WHERE id_usuario = :id";

// Agregar filtros solo si se especifican
$params = [':id' => $id_usuario];

if (!empty($mes) && !empty($anio)) {
    $sql .= " AND EXTRACT(MONTH FROM fecha) = :mes AND EXTRACT(YEAR FROM fecha) = :anio";
    $params[':mes'] = $mes;
    $params[':anio'] = $anio;
}

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$totalGastos = $result['total_gastos'] ?? 0;
echo number_format($totalGastos, 2);
?>