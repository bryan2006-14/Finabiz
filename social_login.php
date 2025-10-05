<?php
// social_login.php

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar dependencias de Composer
require_once __DIR__ . '/vendor/autoload.php';

// Cargar variables de entorno (.env local o Render)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad(); // No falla si el .env no existe (Render usa ENV vars)

// Configurar cliente de Google
$google_client = new Google_Client();
$google_client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID'));
$google_client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET'));
$google_client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI'));
$google_client->addScope('email');
$google_client->addScope('profile');

// Verificar la conexión a base de datos (si existe)
if (file_exists(__DIR__ . '/modelo/conexion.php')) {
    require_once __DIR__ . '/modelo/conexion.php';
} else {
    error_log("Advertencia: No se encontró modelo/conexion.php");
}

// Guardar cliente en sesión (si deseas usarlo más adelante)
$_SESSION['google_client'] = $google_client;

// Opcional: mostrar mensaje de éxito si todo cargó bien
echo "✅ Google Client configurado correctamente.";
