<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$id_usu    = $_SESSION['id_usuario'];
$monto     = $_POST['monto'] ?? 0;
$forma_pago = $_POST['forma_pago'] ?? '';
$categoria  = $_POST['categoria'] ?? '';
$nota       = $_POST['nota'] ?? '';
$fecha      = date("Y-m-d");

try {
    // Insertar con consulta preparada (más seguro)
    $sql = "INSERT INTO gastos (id_usuario, monto, forma_pago, fecha, id_categoria, nota) 
            VALUES (:id_usuario, :monto, :forma_pago, :fecha, :id_categoria, :nota)";
    
    $stmt = $connection->prepare($sql);
    $resultado = $stmt->execute([
        ':id_usuario'  => $id_usu,
        ':monto'       => $monto,
        ':forma_pago'  => $forma_pago,
        ':fecha'       => $fecha,
        ':id_categoria'=> $categoria,
        ':nota'        => $nota
    ]);

    if ($resultado) {
        header('Location: ../gasto.php');
        exit;
    } else {
        echo "Error al insertar el registro.";
    }

} catch (PDOException $e) {
    echo "Error en la base de datos: " . $e->getMessage();
}
?>
