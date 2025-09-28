<?php
require 'conexion.php';

// Iniciar sesión y obtener el id_usuario
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

// Función para obtener la cantidad total de ingreso del usuario
function getTotalIngreso($connection, $id_usuario) {
    $query = "SELECT SUM(monto) as total FROM ingresos WHERE id_usuario = :id_usuario";
    $stmt = $connection->prepare($query);
    $stmt->execute([':id_usuario' => $id_usuario]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return $data['total'] ?? 0;
}

// Función para obtener la cantidad total de gasto del usuario
function getTotalGasto($connection, $id_usuario) {
    $query = "SELECT SUM(monto) as total FROM gastos WHERE id_usuario = :id_usuario";
    $stmt = $connection->prepare($query);
    $stmt->execute([':id_usuario' => $id_usuario]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return $data['total'] ?? 0;
}

// Presupuesto fijo (puedes luego moverlo a DB si quieres)
function getPresupuesto() {
    return 500;
}

// Obtener los totales
$totalIngreso = getTotalIngreso($connection, $id_usuario);
$totalGasto   = getTotalGasto($connection, $id_usuario);
$presupuesto  = getPresupuesto();

// Crear array con los datos
$data = [
    'totalIngreso' => $totalIngreso,
    'totalGasto'   => $totalGasto,
    'presupuesto'  => $presupuesto
];

// Respuesta JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
