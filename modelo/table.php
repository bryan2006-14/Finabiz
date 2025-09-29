<?php
// Inicia buffer de salida
ob_start();

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
    foreach ($gastos as $gasto) {
        // Usar IntlDateFormatter para formato en español (solución moderna)
        try {
            $formatter = new IntlDateFormatter(
                'es_ES',
                IntlDateFormatter::LONG,
                IntlDateFormatter::NONE,
                'America/Lima',
                IntlDateFormatter::GREGORIAN
            );
            $timestamp = strtotime($gasto['fecha']);
            $fecha = $formatter->format($timestamp);
        } catch (Exception $e) {
            // Fallback: usar formato manual con traducción
            $meses = [
                1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
            ];
            
            $timestamp = strtotime($gasto['fecha']);
            $dia = date('d', $timestamp);
            $mes = $meses[(int)date('n', $timestamp)];
            $anio = date('Y', $timestamp);
            $fecha = "$dia de $mes de $anio";
        }

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