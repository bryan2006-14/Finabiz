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

$sql = "SELECT SUM(monto) AS total_ingresos FROM ingresos WHERE id_usuario = :id";
$stmt = $connection->prepare($sql);
$stmt->execute([':id' => $id_usuario]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$totalIngresos = $result['total_ingresos'] ?? 0;
echo number_format($totalIngresos, 2);
