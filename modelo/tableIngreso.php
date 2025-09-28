<?php
// Inicia sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    exit; // no mostrar nada si no hay usuario
}

$sql = "SELECT monto, forma_pago, fecha, nota 
        FROM ingresos 
        WHERE id_usuario = :id 
        ORDER BY fecha DESC";
$stmt = $connection->prepare($sql);
$stmt->execute([':id' => $id_usuario]);
$ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($ingresos) {
    foreach ($ingresos as $ingreso) {
        echo "<tr>";
        echo "<td> S/." . number_format($ingreso['monto'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($ingreso['forma_pago']) . "</td>";
        echo "<td>" . date("d \d\e F \d\e Y", strtotime($ingreso['fecha'])) . "</td>";
        echo "<td>" . htmlspecialchars($ingreso['nota']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No hay ingresos registrados</td></tr>";
}
