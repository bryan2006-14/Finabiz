<?php
// config/oauth_config.php

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    // Cargar variables de entorno
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();
} catch (Exception $e) {
    die('Error cargando configuración: ' . $e->getMessage());
}

// Leer variables desde entorno (.env o Render)
$clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET');
$redirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI');

// DEBUG opcional
// error_log("CLIENT_ID: " . $clientId);
// error_log("REDIRECT_URI: " . $redirectUri);

// Verificar que no falte nada
if (!$clientId || !$clientSecret || !$redirectUri) {
    die('❌ Faltan variables de Google OAuth. Verifica el archivo .env o las variables en Render.');
}

// Configurar Google Client
$google_client = new Google_Client();
$google_client->setClientId($clientId);
$google_client->setClientSecret($clientSecret);
$google_client->setRedirectUri($redirectUri);
$google_client->addScope("email");
$google_client->addScope("profile");

// Devolver el cliente configurado
return $google_client;
