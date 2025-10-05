<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Google\Client as Google_Client;
use Dotenv\Dotenv;

try {
    // Cargar variables de entorno desde el archivo .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
} catch (Exception $e) {
    die('⚠️ Error cargando configuración: ' . htmlspecialchars($e->getMessage()));
}

// Crear cliente de Google
$google_client = new Google_Client();

// Cargar credenciales desde variables de entorno (compatibles con Render)
$clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET');
$redirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI');

// Validar que existan las tres
if (!$clientId || !$clientSecret || !$redirectUri) {
    die('❌ Error: Variables de entorno GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET o GOOGLE_REDIRECT_URI no configuradas correctamente.');
}

// Configurar el cliente
$google_client->setClientId($clientId);
$google_client->setClientSecret($clientSecret);
$google_client->setRedirectUri($redirectUri);

// Permisos necesarios
$google_client->addScope('email');
$google_client->addScope('profile');

// Retornar instancia (si se usa require_once)
return $google_client;
