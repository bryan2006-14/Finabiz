<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$id_usu     = $_SESSION['id_usuario'];
$monto      = $_POST['monto'] ?? 0;
$forma_pago = $_POST['forma_pago'] ?? '';
$nota       = $_POST['nota'] ?? '';
$fecha      = date("Y-m-d");

try {
    // Insertar ingreso con consulta preparada
    $sql = "INSERT INTO ingresos (id_usuario, monto, forma_pago, fecha, nota) 
            VALUES (:id_usuario, :monto, :forma_pago, :fecha, :nota)";
    
    $stmt = $connection->prepare($sql);
    $resultado = $stmt->execute([
        ':id_usuario'  => $id_usu,
        ':monto'       => $monto,
        ':forma_pago'  => $forma_pago,
        ':fecha'       => $fecha,
        ':nota'        => $nota
    ]);

    if ($resultado) {
        header('Location: ../ingreso.php');
        exit;
    } else {
        echo "Error al insertar el registro.";
    }

} catch (PDOException $e) {
    echo "Error en la base de datos: " . $e->getMessage();
}
?>
