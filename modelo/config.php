<?php
// Inicia sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php'; // Conexión PDO

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['id_usuario'];

/**
 * Obtener datos del usuario
 */
function obtenerDatosUsuario($connection, $user_id) {
    $sql = "SELECT nombre, correo, password FROM usuarios WHERE id_usuario = :id";
    $stmt = $connection->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $data ?: false;
}

/**
 * Actualizar datos del usuario
 */
function actualizarUsuario($connection, $user_id, $nombre, $correo, $password) {
    if (empty($password)) {
        // Si no cambia la contraseña
        $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo WHERE id_usuario = :id";
        $stmt = $connection->prepare($sql);
        $success = $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':id'     => $user_id
        ]);
    } else {
        // Si cambia la contraseña → la encriptamos
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, password = :password WHERE id_usuario = :id";
        $stmt = $connection->prepare($sql);
        $success = $stmt->execute([
            ':nombre'   => $nombre,
            ':correo'   => $correo,
            ':password' => $hashedPassword,
            ':id'       => $user_id
        ]);
    }

    if ($success) {
        $_SESSION['nombre'] = $nombre; // actualizar sesión
        return true;
    }
    return false;
}

// Obtener datos del usuario
$datosUsuario = obtenerDatosUsuario($connection, $user_id);

if (!$datosUsuario) {
    echo "Error: Usuario no encontrado.";
    exit;
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = trim($_POST["nombre"]);
    $correo   = trim($_POST["correo"]);
    $password = trim($_POST["password"]);

    if (actualizarUsuario($connection, $user_id, $nombre, $correo, $password)) {
        header("Location: ./inicio.php");
        exit;
    } else {
        echo "Error al actualizar el usuario.";
    }
}
