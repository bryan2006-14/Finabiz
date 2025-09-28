<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recuperar los datos del formulario
    $nombreUsuario = $_POST["username"] ?? '';
    $correo        = $_POST["email"] ?? '';
    $password      = $_POST["password"] ?? '';
    $fotoPerfil    = '';

    // Manejo de foto de perfil
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
        $tempFile   = $_FILES["file"]["tmp_name"];
        $fotoPerfil = "users" . time() . ".jpg"; // Nombre único
        move_uploaded_file($tempFile, "../fotos/" . $fotoPerfil);
    } else {
        $fotoPerfil = "fotos/usuario1.jpg"; // Imagen por defecto
    }

    try {
        // Consulta preparada para insertar usuario
        $sql = "INSERT INTO usuarios (nombre, correo, password, foto_perfil)
                VALUES (:nombre, :correo, :password, :foto_perfil)";
        
        $stmt = $connection->prepare($sql);
        $resultado = $stmt->execute([
            ':nombre'      => $nombreUsuario,
            ':correo'      => $correo,
            ':password'    => $password, // ⚠️ ideal usar password_hash aquí
            ':foto_perfil' => $fotoPerfil
        ]);

        if ($resultado) {
            header("Location: registroExitoso.php");
            exit;
        } else {
            echo "Error al crear el usuario.";
        }

    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
}
?>
