<?php
// social_login.php

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/modelo/conexion.php';

// Configuración de Google Client (sin archivo externo)
$google_client = new Google_Client();
$google_client->setClientId(getenv('GOOGLE_CLIENT_ID'));
$google_client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
$google_client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));
$google_client->addScope('email');
$google_client->addScope('profile');

// DEBUG: Verificar si $connection existe
if (!isset($connection)) {
    die("Error: La conexión PDO no se estableció. Revisa modelo/conexion.php");
}
