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

// Obtener parámetros de filtro
$mes = isset($_GET['mes']) ? $_GET['mes'] : '';
$anio = isset($_GET['anio']) ? $_GET['anio'] : '';

// Consulta base
$sql = "SELECT g.monto, g.forma_pago, g.fecha, c.categoria, g.nota
        FROM gastos g
        JOIN categorias c ON g.id_categoria = c.id_categoria
        WHERE g.id_usuario = :id";

$params = [':id' => $id_usuario];

// Agregar filtros solo si se especifican
if (!empty($mes) && !empty($anio)) {
    $sql .= " AND EXTRACT(MONTH FROM g.fecha) = :mes AND EXTRACT(YEAR FROM g.fecha) = :anio";
    $params[':mes'] = $mes;
    $params[':anio'] = $anio;
}

$sql .= " ORDER BY g.fecha DESC";

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($gastos) {
    // Traducción de meses al español
    $meses = [
        'January' => 'enero',
        'February' => 'febrero',
        'March' => 'marzo',
        'April' => 'abril',
        'May' => 'mayo',
        'June' => 'junio',
        'July' => 'julio',
        'August' => 'agosto',
        'September' => 'septiembre',
        'October' => 'octubre',
        'November' => 'noviembre',
        'December' => 'diciembre'
    ];

    foreach ($gastos as $gasto) {
        $date = new DateTime($gasto['fecha']);
        $fecha = $date->format("d \d\e F \d\e Y");
        $fecha = strtr($fecha, $meses); // traducir al español

        echo "<tr>";
        echo "<td>S/." . number_format($gasto['monto'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($gasto['forma_pago']) . "</td>";
        echo "<td>" . ucfirst($fecha) . "</td>";
        echo "<td>" . htmlspecialchars($gasto['categoria']) . "</td>";
        echo "<td>" . htmlspecialchars($gasto['nota']) . "</td>";
        echo "</tr>";
    }
} else {
    if (!empty($mes) && !empty($anio)) {
        // Mostrar mensaje específico del período si se filtró
        $mesesNombres = [
            '01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril',
            '05' => 'mayo', '06' => 'junio', '07' => 'julio', '08' => 'agosto',
            '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'
        ];
        
        $mesEspanol = $mesesNombres[$mes] ?? 'este mes';
        
        echo "<tr><td colspan='5' class='text-center'>No hay gastos registrados para " . 
             ucfirst($mesEspanol) . " de $anio</td></tr>";
    } else {
        // Mensaje general si no hay gastos
        echo "<tr><td colspan='5' class='text-center'>No hay gastos registrados</td></tr>";
    }
}
?>