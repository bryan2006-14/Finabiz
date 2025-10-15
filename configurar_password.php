<?php
// configurar_password.php
session_start();
require 'conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['id_usuario'];
    $new_password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirm_password"] ?? '';
    
    if ($new_password === $confirm_password) {
        // Actualizar password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = :password WHERE id_usuario = :id";
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':password' => $hashed_password,
            ':id' => $user_id
        ]);
        
        $_SESSION['success'] = "Password configurado correctamente. Ahora puedes iniciar sesión con email y password.";
        header("Location: perfil.php");
        exit;
    } else {
        $error = "Las contraseñas no coinciden";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Configurar Password</title>
</head>
<body>
    <h2>Configurar tu Password</h2>
    <p>Para poder iniciar sesión con email y contraseña, configura tu password:</p>
    
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    
    <form method="POST">
        <input type="password" name="password" placeholder="Nueva contraseña" required>
        <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
        <button type="submit">Configurar Password</button>
    </form>
</body>
</html>