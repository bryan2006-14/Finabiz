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

// Obtener parámetros de filtro directamente de $_GET
$mes = isset($_GET['mes']) ? $_GET['mes'] : '';
$anio = isset($_GET['anio']) ? $_GET['anio'] : '';

// Construir la consulta base - MOSTRAR TODOS si no hay filtros
$sql = "SELECT SUM(monto) AS total_ingresos FROM ingresos WHERE id_usuario = :id";
$params = [':id' => $id_usuario];

// Agregar filtros SOLO si ambos están presentes y no están vacíos
if (!empty($mes) && !empty($anio)) {
    $sql .= " AND EXTRACT(MONTH FROM fecha) = :mes AND EXTRACT(YEAR FROM fecha) = :anio";
    $params[':mes'] = $mes;
    $params[':anio'] = $anio;
}

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$totalIngresos = $result['total_ingresos'] ?? 0;
echo number_format($totalIngresos, 2);
?>