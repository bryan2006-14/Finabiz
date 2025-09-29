<?php
// Inicia sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    exit;
}

$sql = "SELECT g.monto, g.forma_pago, g.fecha, c.categoria, g.nota
        FROM gastos g
        JOIN categorias c ON g.id_categoria = c.id_categoria
        WHERE g.id_usuario = :id
        ORDER BY g.fecha DESC";
$stmt = $connection->prepare($sql);
$stmt->execute([':id' => $id_usuario]);
$gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($gastos) {
    // Establecer idioma español para la fecha
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');

    foreach ($gastos as $gasto) {
        $fecha = strftime("%d de %B de %Y", strtotime($gasto['fecha']));

        echo "<tr>";
        echo "<td>S/." . number_format($gasto['monto'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($gasto['forma_pago']) . "</td>";
        echo "<td>" . ucfirst($fecha) . "</td>";
        echo "<td>" . htmlspecialchars($gasto['categoria']) . "</td>";
        echo "<td>" . htmlspecialchars($gasto['nota']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No hay gastos registrados</td></tr>";
}
