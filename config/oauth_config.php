<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
} catch (Exception $e) {
    die('Error cargando configuración: ' . $e->getMessage());
}

$google_client = new Google_Client();

$clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET');
$redirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI');

if (!$clientId || !$clientSecret || !$redirectUri) {
    die('❌ Variables de entorno no configuradas correctamente.');
}

$google_client->setClientId($clientId);
$google_client->setClientSecret($clientSecret);
$google_client->setRedirectUri($redirectUri);
$google_client->addScope('email');
$google_client->addScope('profile');

return $google_client;
