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

$sql = "SELECT SUM(monto) AS total_gastos FROM gastos WHERE id_usuario = :id";
$stmt = $connection->prepare($sql);
$stmt->execute([':id' => $id_usuario]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$totalGastos = $result['total_gastos'] ?? 0;
echo number_format($totalGastos, 2);
