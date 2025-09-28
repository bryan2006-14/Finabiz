<?php
require 'conexion.php'; // reutilizamos la conexión con PDO

// Obtener el valor seleccionado del formulario (siempre validar)
$categoriaSeleccionada = isset($_POST['categoria']) ? (int)$_POST['categoria'] : 0;

try {
    // Consulta segura con parámetro preparado
    $sql = "SELECT id_gasto, id_usuario, monto, forma_pago, fecha, id_categoria, nota 
            FROM gastos 
            WHERE id_categoria = :categoria";

    $stmt = $connection->prepare($sql);
    $stmt->execute([':categoria' => $categoriaSeleccionada]);

    // Mostrar los resultados en una tabla
    echo '<table border="1" cellpadding="5">';
    echo '<tr>
            <th>ID Gasto</th>
            <th>ID Usuario</th>
            <th>Monto</th>
            <th>Forma de Pago</th>
            <th>Fecha</th>
            <th>ID Categoría</th>
            <th>Nota</th>
          </tr>';

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($fila['id_gasto']) . '</td>';
        echo '<td>' . htmlspecialchars($fila['id_usuario']) . '</td>';
        echo '<td>' . htmlspecialchars($fila['monto']) . '</td>';
        echo '<td>' . htmlspecialchars($fila['forma_pago']) . '</td>';
        echo '<td>' . htmlspecialchars($fila['fecha']) . '</td>';
        echo '<td>' . htmlspecialchars($fila['id_categoria']) . '</td>';
        echo '<td>' . htmlspecialchars($fila['nota']) . '</td>';
        echo '</tr>';
    }

    echo '</table>';

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
?>
