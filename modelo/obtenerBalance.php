<?php
require 'conexion.php'; // Incluir el archivo de conexión a la base de datos

session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("Error: No se ha iniciado sesión.");
}

$idUsuario = $_SESSION['id_usuario'];

try {
    // Consulta SQL para obtener la cantidad total de gasto por categoría
    $sql = "SELECT c.categoria, SUM(g.monto) AS total_gasto
            FROM gastos g
            INNER JOIN categorias c ON g.id_categoria = c.id_categoria
            WHERE g.id_usuario = :id_usuario
            GROUP BY c.categoria";

    $stmt = $connection->prepare($sql);
    $stmt->execute([':id_usuario' => $idUsuario]);

    $data = [];

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[$fila['categoria']] = $fila['total_gasto'];
    }

    // Devolver los datos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($data);

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
