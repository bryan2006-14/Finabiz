<?php
// social_login.php

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$google_client = require_once __DIR__ . '/config/oauth_config.php';
require_once __DIR__ . '/modelo/conexion.php';

// DEBUG: Verificar si $connection existe
if (!isset($connection)) {
    die("Error: La conexión PDO no se estableció. Revisa modelo/conexion.php");
}

// 🚀 Inicio del flujo de autenticación con Google
if (isset($_GET['provider']) && $_GET['provider'] === 'google' && !isset($_GET['code'])) {
    $auth_url = $google_client->createAuthUrl();
    header('Location: ' . $auth_url);
    exit;
}

// 🚀 Callback de Google
if (isset($_GET['code'])) {
    
    // Reabrir sesión
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    try {
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            die('Error en token: ' . $token['error']);
        }
        
        $google_client->setAccessToken($token);
        $oauth2 = new Google_Service_Oauth2($google_client);
        $userInfo = $oauth2->userinfo->get();
        
        $email = $userInfo->email;
        $name = $userInfo->name;
        $picture = $userInfo->picture;
        $google_id = $userInfo->id;
        
        echo "<h4>Datos de Google obtenidos correctamente:</h4>";
        echo "Email: " . $email . "<br>";
        echo "Nombre: " . $name . "<br>";
        echo "Foto: " . $picture . "<br>";
        
        // --- BUSCAR O CREAR USUARIO CON PDO ---
        
        // Buscar usuario por email
        $stmt = $connection->prepare("SELECT id_usuario, foto_perfil FROM usuarios WHERE correo = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Usuario existe
            $user_id = (int)$user['id_usuario'];
            echo "Usuario existente encontrado. ID: " . $user_id . "<br>";
            
            // Actualizar foto si no tiene
            if (empty($user['foto_perfil']) && $picture) {
                $update_stmt = $connection->prepare("UPDATE usuarios SET foto_perfil = :foto, created_via_social = TRUE WHERE id_usuario = :id");
                $update_stmt->bindParam(':foto', $picture);
                $update_stmt->bindParam(':id', $user_id);
                $update_stmt->execute();
                echo "Foto de perfil actualizada.<br>";
            }
            
        } else {
            // Crear nuevo usuario
            echo "Creando nuevo usuario...<br>";
            $insert_stmt = $connection->prepare("INSERT INTO usuarios (nombre, correo, foto_perfil, created_via_social) VALUES (:nombre, :email, :foto, TRUE)");
            $insert_stmt->bindParam(':nombre', $name);
            $insert_stmt->bindParam(':email', $email);
            $insert_stmt->bindParam(':foto', $picture);
            $insert_stmt->execute();
            $user_id = (int)$connection->lastInsertId();
            echo "Nuevo usuario creado. ID: " . $user_id . "<br>";
        }
        
        // Crear sesión
        $_SESSION['id_usuario'] = $user_id;
        $_SESSION['nombre'] = $name;
        $_SESSION['foto_perfil'] = $picture;
        $_SESSION['google_logged_in'] = true;
        
        // Actualizar último acceso
        $update_access = $connection->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = :id");
        $update_access->bindParam(':id', $user_id);
        $update_access->execute();
        
        echo "<h4 style='color: green;'>¡Login exitoso! Redirigiendo en 3 segundos...</h4>";
        
        // Redirigir después de 3 segundos
        echo "<script>
            setTimeout(function() { 
                window.location.href = 'inicio.php'; 
            }, 3000);
        </script>";
        
        exit;
        
    } catch (Exception $e) {
        die('Error en el proceso: ' . $e->getMessage());
    }
}

// Si no es ninguno de los casos anteriores
header('Location: index.php');
exit;
?>