<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $monto = floatval($_POST['monto']);
    $forma_pago = $_POST['forma_pago'];
    $fecha = $_POST['fecha'];
    $categoria_id = $_POST['categoria_id'];
    $nota = $_POST['nota'];

    // Validar datos
    if ($monto <= 0 || empty($forma_pago) || empty($fecha) || empty($categoria_id) || empty($nota)) {
        $_SESSION['error'] = "Todos los campos son obligatorios y el monto debe ser mayor a 0";
        header("Location: ../gasto.php");
        exit();
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO gastos (usuario_id, monto, forma_pago, fecha, categoria_id, nota) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idssis", $id_usuario, $monto, $forma_pago, $fecha, $categoria_id, $nota);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Gasto registrado correctamente";
        
        // Verificar logros
        require_once 'logros.php';
        verificarLogroPrimerGasto($id_usuario);
        
    } else {
        $_SESSION['error'] = "Error al registrar el gasto: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: ../gasto.php");
    exit();
}
?>