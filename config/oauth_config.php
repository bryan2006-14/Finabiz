<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    // Cargar variables de entorno (si existe el archivo .env)
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad(); // No lanza error si no existe el archivo (útil en Render)

    // Verificar que las variables existan, desde $_ENV o getenv()
    $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
    $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET');
    $redirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI');

    if (!$clientId || !$clientSecret || !$redirectUri) {
        die('❌ Error: Faltan variables de entorno de Google OAuth (GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET o GOOGLE_REDIRECT_URI)');
    }

} catch (Exception $e) {
    die('❌ Error cargando configuración OAuth: ' . $e->getMessage());
}

// Crear y configurar el cliente de Google
$google_client = new Google_Client();
$google_client->setClientId($clientId);
$google_client->setClientSecret($clientSecret);
$google_client->setRedirectUri($redirectUri);
$google_client->addScope("email");
$google_client->addScope("profile");

// ✅ Devolver cliente configurado
return $google_client;
