<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno (.env si existe, Render usa ENV vars)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

$google_client = new Google_Client();

// Configuración segura: usa .env o variables del entorno
$google_client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID'));
$google_client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET'));
$google_client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI'));
$google_client->addScope('email');
$google_client->addScope('profile');

return $google_client;
