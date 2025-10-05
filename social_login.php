<?php
// social_login.php

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar dependencias de Composer
require_once __DIR__ . '/vendor/autoload.php';

// Cargar variables de entorno (.env local o Render)
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Configurar cliente de Google
$google_client = new Google_Client();
$google_client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID'));
$google_client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET'));
$google_client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI'));
$google_client->addScope('email');
$google_client->addScope('profile');

// --- Si Google devuelve el "code" (tras iniciar sesión) ---
if (isset($_GET['code'])) {
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        // Guardar solo el token en sesión (no el objeto completo)
        $_SESSION['access_token'] = $token;

        // Configurar cliente con el token
        $google_client->setAccessToken($token['access_token']);

        // Obtener datos del usuario
        $google_service = new Google_Service_Oauth2($google_client);
        $user_info = $google_service->userinfo->get();

        // Guardar los datos que necesites en sesión
        $_SESSION['user_email'] = $user_info->email;
        $_SESSION['user_name'] = $user_info->name;

        echo "✅ Sesión iniciada con Google: {$_SESSION['user_name']} ({$_SESSION['user_email']})";
        exit;
    } else {
        echo "❌ Error obteniendo token: " . htmlspecialchars($token['error']);
        exit;
    }
}

// --- Si no hay sesión iniciada, generar el link de login ---
if (!isset($_SESSION['access_token'])) {
    $login_url = $google_client->createAuthUrl();
    echo "<a href='" . htmlspecialchars($login_url) . "'>Iniciar sesión con Google</a>";
} else {
    echo "✅ Google Client configurado correctamente.";
}
?>
