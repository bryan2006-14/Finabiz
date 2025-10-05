<?php
// social_login.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/oauth_config.php';
require_once __DIR__ . '/modelo/conexion.php';

// Asegurar conexión
if (!isset($connection)) {
    die("Error: No se pudo establecer conexión a la base de datos.");
}

// 🚀 INICIO DEL LOGIN CON GOOGLE
if (isset($_GET['provider']) && $_GET['provider'] === 'google' && !isset($_GET['code'])) {
    $auth_url = $google_client->createAuthUrl();

    // 🔍 DEBUG: Ver qué URL se está enviando a Google (para revisar redirect_uri)
    echo "<h3>DEBUG Auth URL:</h3>";
    echo "<pre>" . htmlspecialchars($auth_url) . "</pre>";
    echo "<h4>redirect_uri que se está enviando:</h4>";
    echo "<pre>" . htmlspecialchars($google_client->getRedirectUri()) . "</pre>";
    echo "<p>Si esto no coincide EXACTAMENTE con lo que está en tu consola de Google, causará error 400.</p>";
    exit;

    // Cuando todo funcione, reemplaza lo anterior por:
    // header('Location: ' . $auth_url);
    // exit;
}

// 🚀 CALLBACK DESDE GOOGLE (después del login)
if (isset($_GET['code'])) {
    try {
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
        if (isset($token['error'])) {
            die('Error obteniendo token: ' . $token['error']);
        }

        $google_client->setAccessToken($token);
        $oauth2 = new Google_Service_Oauth2($google_client);
        $userInfo = $oauth2->userinfo->get();

        $email = $userInfo->email;
        $name = $userInfo->name;
        $picture = $userInfo->picture;
        $google_id = $userInfo->id;

        echo "<h3>✅ Datos de Google obtenidos correctamente</h3>";
        echo "Email: $email <br>Nombre: $name <br><img src='$picture' width='80'><br>";

        // Buscar usuario
        $stmt = $connection->prepare("SELECT id_usuario, foto_perfil FROM usuarios WHERE correo = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_id = (int)$user['id_usuario'];
            echo "Usuario existente encontrado (ID: $user_id)<br>";

            if (empty($user['foto_perfil']) && $picture) {
                $update = $connection->prepare("UPDATE usuarios SET foto_perfil = :foto WHERE id_usuario = :id");
                $update->bindParam(':foto', $picture);
                $update->bindParam(':id', $user_id);
                $update->execute();
            }
        } else {
            echo "Creando nuevo usuario...<br>";
            $insert = $connection->prepare("INSERT INTO usuarios (nombre, correo, foto_perfil, created_via_social) VALUES (:nombre, :email, :foto, TRUE)");
            $insert->bindParam(':nombre', $name);
            $insert->bindParam(':email', $email);
            $insert->bindParam(':foto', $picture);
            $insert->execute();
            $user_id = (int)$connection->lastInsertId();
            echo "Nuevo usuario creado (ID: $user_id)<br>";
        }

        $_SESSION['id_usuario'] = $user_id;
        $_SESSION['nombre'] = $name;
        $_SESSION['foto_perfil'] = $picture;
        $_SESSION['google_logged_in'] = true;

        echo "<h4 style='color: green;'>¡Login exitoso! Redirigiendo en 3 segundos...</h4>";
        echo "<script>
            setTimeout(function() { window.location.href = 'inicio.php'; }, 3000);
        </script>";
        exit;

    } catch (Exception $e) {
        die('Error en el proceso: ' . $e->getMessage());
    }
}

// Si no es ninguno de los casos anteriores
header('Location: index.php');
exit;
