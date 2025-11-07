<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

$total = 0;

// Usar $conn en lugar de $connection
if ($conn) {
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    if ($id_usuario) {
        $mes = $_GET['mes'] ?? '';
        $anio = $_GET['anio'] ?? '';
        
        try {
            $sql = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos WHERE usuario_id = :usuario_id";
            $params = [':usuario_id' => $id_usuario];

            if (!empty($mes) && !empty($anio)) {
                $sql .= " AND EXTRACT(MONTH FROM fecha) = :mes AND EXTRACT(YEAR FROM fecha) = :anio";
                $params[':mes'] = $mes;
                $params[':anio'] = $anio;
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $total = $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error en totalIngreso: " . $e->getMessage());
            $total = 0;
        }
    }
} else {
    error_log("Error: No hay conexión a la base de datos en totalIngreso.php");
}

echo number_format($total, 2);
?>