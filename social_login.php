<?php
// social_login.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar dependencias
require_once __DIR__ . '/vendor/autoload.php';

// Cargar variables de entorno (local o Render)
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

// Configurar cliente de Google
$google_client = new Google_Client();
$google_client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID'));
$google_client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET'));
$google_client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI'));
$google_client->addScope('email');
$google_client->addScope('profile');

// Si viene el "code" de Google
if (isset($_GET['code'])) {
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (!isset($token['error'])) {
        $google_client->setAccessToken($token['access_token']);
        $google_service = new Google_Service_Oauth2($google_client);
        $data = $google_service->userinfo->get();
        
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit;
    } else {
        echo "❌ Error al autenticar con Google.";
    }
    exit;
}

// Si no hay "code", redirige al login
$authUrl = $google_client->createAuthUrl();
echo "<a href='" . htmlspecialchars($authUrl) . "'>Iniciar sesión con Google</a>";
