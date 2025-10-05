<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/vendor/autoload.php';

// Cargar variables de entorno (solo localmente)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad(); // no fallará si no hay .env en Render

// Verificar conexión
require_once __DIR__ . '/modelo/conexion.php';

// Configurar Google Client
$google_client = new Google_Client();
$google_client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID'));
$google_client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET'));
$google_client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI'));
$google_client->addScope('email');
$google_client->addScope('profile');
