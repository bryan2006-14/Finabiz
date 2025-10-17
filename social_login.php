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

        // 📸 Manejo robusto de imagen de perfil
        $picture = $userInfo->picture ?? '';
        if (empty($picture)) {
            // Generar avatar con iniciales si no hay imagen de Google
            $initials = 'U'; // Por defecto 'U' de Usuario
            if (!empty($name)) {
                $nameParts = explode(' ', $name);
                $initials = '';
                foreach ($nameParts as $part) {
                    if (!empty($part)) {
                        $initials .= strtoupper(substr($part, 0, 1));
                        if (strlen($initials) >= 2) break;
                    }
                }
            }
            $picture = "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=0D8ABC&color=fff&size=128&length=2";
        }

        // 🚨 Verificar si el usuario ya existe
        $stmt = $connection->prepare("SELECT id_usuario, foto_perfil, password FROM usuarios WHERE correo = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ✅ Usuario existente - actualizar datos
            $user_id = (int)$user['id_usuario'];

            // Solo actualizar si la foto actual está vacía o es la por defecto
            $current_photo = $user['foto_perfil'] ?? '';
            $should_update_photo = empty($current_photo) || 
                                 strpos($current_photo, 'ui-avatars.com') !== false ||
                                 strpos($current_photo, 'default-avatar.png') !== false;

            if ($should_update_photo) {
                $update = $connection->prepare("UPDATE usuarios SET nombre = :nombre, foto_perfil = :foto WHERE id_usuario = :id");
                $update->bindParam(':nombre', $name);
                $update->bindParam(':foto', $picture);
                $update->bindParam(':id', $user_id);
                $update->execute();
            } else {
                // Solo actualizar el nombre
                $update = $connection->prepare("UPDATE usuarios SET nombre = :nombre WHERE id_usuario = :id");
                $update->bindParam(':nombre', $name);
                $update->bindParam(':id', $user_id);
                $update->execute();
                // Usar la foto existente
                $picture = $current_photo;
            }
            
        } else {
            // 🆕 Crear nuevo usuario CON PASSWORD ENCRIPTADO
            $auto_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            
            $insert = $connection->prepare("
                INSERT INTO usuarios (nombre, correo, foto_perfil, password) 
                VALUES (:nombre, :email, :foto, :password)
            ");
            $insert->bindParam(':nombre', $name);
            $insert->bindParam(':email', $email);
            $insert->bindParam(':foto', $picture);
            $insert->bindParam(':password', $auto_password);
            
            if ($insert->execute()) {
                $user_id = (int)$connection->lastInsertId();
            } else {
                throw new Exception("Error al crear nuevo usuario: " . implode(", ", $insert->errorInfo()));
            }
        }

        // ✅ Guardar sesión
        $_SESSION['id_usuario'] = $user_id;
        $_SESSION['nombre'] = $name;
        $_SESSION['foto_perfil'] = $picture;
        $_SESSION['google_logged_in'] = true;

        // 🎯 Marcar para mostrar publicidad en la próxima carga
        $_SESSION['show_ad'] = true;

        // 🔁 Redirigir a inicio.php (SOLO UNA VEZ)
        header('Location: inicio.php');
        exit;

    } catch (Exception $e) {
        error_log("Error en social login: " . $e->getMessage());
        // Redirigir a página de error o login con mensaje
        header('Location: index.php?error=social_login_failed');
        exit;
    }
}

// Si no es un callback de Google, redirigir al inicio
header('Location: index.php');
exit;
?>