<?php
// social_login.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/oauth_config.php';
require_once __DIR__ . '/modelo/conexion.php';

if (!isset($connection)) {
    die("Error: No se pudo establecer conexión a la base de datos.");
}

// 🚀 1. INICIAR LOGIN CON GOOGLE
if (isset($_GET['provider']) && $_GET['provider'] === 'google' && !isset($_GET['code'])) {
    $auth_url = $google_client->createAuthUrl();
    header('Location: ' . $auth_url);
    exit;
}

// 🚀 2. CALLBACK DESDE GOOGLE (después del login)
if (isset($_GET['code'])) {
    try {
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($token['error'])) {
            die('Error obteniendo token: ' . htmlspecialchars($token['error']));
        }

        $google_client->setAccessToken($token);
        $oauth2 = new Google_Service_Oauth2($google_client);
        $userInfo = $oauth2->userinfo->get();

        $email = $userInfo->email;
        $name = $userInfo->name;

        // 📸 Si no tiene imagen, generamos un avatar con inicial
        $picture = $userInfo->picture ?? '';
        if (empty($picture)) {
            $nameEncoded = urlencode($name ?: 'Usuario');
            $picture = "https://ui-avatars.com/api/?name={$nameEncoded}&background=0D8ABC&color=fff&size=128";
        }

        // 🚨 Verificar si el usuario ya existe
        $stmt = $connection->prepare("SELECT id_usuario, foto_perfil, password FROM usuarios WHERE correo = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ✅ Usuario existente
            $user_id = (int)$user['id_usuario'];

            // Actualizar foto y nombre si es necesario
            $update = $connection->prepare("UPDATE usuarios SET nombre = :nombre, foto_perfil = :foto WHERE id_usuario = :id");
            $update->bindParam(':nombre', $name);
            $update->bindParam(':foto', $picture);
            $update->bindParam(':id', $user_id);
            $update->execute();
            
        } else {
    // 🆕 Crear nuevo usuario CON PASSWORD TEMPORAL
    $temp_password = password_hash("google_temp_" . bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
    
    $insert = $connection->prepare("
        INSERT INTO usuarios (nombre, correo, foto_perfil, password) 
        VALUES (:nombre, :email, :foto, :password)
    ");
    $insert->bindParam(':nombre', $name);
    $insert->bindParam(':email', $email);
    $insert->bindParam(':foto', $picture);
    $insert->bindParam(':password', $temp_password);
    $insert->execute();
    $user_id = (int)$connection->lastInsertId();
}

        // ✅ Guardar sesión
        $_SESSION['id_usuario'] = $user_id;
        $_SESSION['nombre'] = $name;
        $_SESSION['foto_perfil'] = $picture;
        $_SESSION['google_logged_in'] = true;

        // 🔁 Redirigir a inicio.php
        header('Location: inicio.php');
        exit;

    } catch (Exception $e) {
        die('Error en el proceso: ' . htmlspecialchars($e->getMessage()));
    }
}

header('Location: index.php');
exit;
?>