<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    $monto = $_POST['monto'] ?? '';
    $forma_pago = $_POST['forma_pago'] ?? '';
    $fecha = $_POST['fecha'] ?? date('Y-m-d');
    $nota = $_POST['nota'] ?? '';

    if (!$id_usuario) {
        $_SESSION['error'] = "No hay sesión activa";
        header("Location: ../ingreso.php");
        exit();
    }

    if (empty($monto) || empty($forma_pago) || empty($nota)) {
        $_SESSION['error'] = "Todos los campos son obligatorios";
        header("Location: ../ingreso.php");
        exit();
    }

    // Verificar si la conexión existe usando $conn
    if (!isset($conn) || !$conn) {
        $_SESSION['error'] = "Error de conexión a la base de datos";
        header("Location: ../ingreso.php");
        exit();
    }

    try {
        $sql = "INSERT INTO ingresos (usuario_id, monto, forma_pago, fecha, nota) 
                VALUES (:usuario_id, :monto, :forma_pago, :fecha, :nota)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $id_usuario,
            ':monto' => $monto,
            ':forma_pago' => $forma_pago,
            ':fecha' => $fecha,
            ':nota' => $nota
        ]);

        $_SESSION['success'] = "Ingreso registrado correctamente";
        header("Location: ../ingreso.php");
        exit();

    } catch (PDOException $e) {
        error_log("Error al registrar ingreso: " . $e->getMessage());
        $_SESSION['error'] = "Error al registrar el ingreso: " . $e->getMessage();
        header("Location: ../ingreso.php");
        exit();
    }
} else {
    header("Location: ../ingreso.php");
    exit();
}