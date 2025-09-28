<?php
require 'conexion.php';

try {
    // Consulta para obtener las categorías
    $sql = "SELECT id_categoria, categoria FROM categorias";
    $stmt = $connection->query($sql);

    // Obtener todas las categorías como arreglo asociativo
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver como JSON
    header('Content-Type: application/json');
    echo json_encode($categorias);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
