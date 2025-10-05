<?php
// social_login.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/oauth_config.php';
require_once __DIR__ . '/modelo/conexion.php';

// Asegurar conexión a BD
if (!isset($connection)) {
    die("Error: No se pudo establecer conexión a la base de datos.");
}

// 🚀 1. INICIAR LOGIN CON GOOGLE
if (isset($_GET['provider']) && $_GET['provider'] === 'google' && !isset($_GET['code'])) {
    $auth_url = $google_client->createAuthUrl();
    header('Location: ' . $auth_url);
    exit;
}

// 🚀 2. CALLBACK DESDE GOOGLE
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
        $picture = $userInfo->picture;

        // 🚨 Verificar si el usuario ya existe
        $stmt = $connection->prepare("SELECT id_usuario, foto_perfil FROM usuarios WHERE correo = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ✅ Usuario existente
            $user_id = (int)$user['id_usuario'];

            // Actualizar foto si está vacía
            if (empty($user['foto_perfil']) && $picture) {
                $update = $connection->prepare("UPDATE usuarios SET foto_perfil = :foto WHERE id_usuario = :id");
                $update->bindParam(':foto', $picture);
                $update->bindParam(':id', $user_id);
                $update->execute();
            }
        } else {
            // 🆕 Crear nuevo usuario
            $insert = $connection->prepare("INSERT INTO usuarios (nombre, correo, foto_perfil, created_via_social) VALUES (:nombre, :email, :foto, TRUE)");
            $insert->bindParam(':nombre', $name);
            $insert->bindParam(':email', $email);
            $insert->bindParam(':foto', $picture);
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

// 🚨 Si no aplica ninguna condición, redirigir al index
header('Location: index.php');
exit;
?>
