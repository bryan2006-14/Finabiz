<?php
// config/session_config.php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 1800,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => false, // Cambia a true en producción con HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    session_start();
    
    // Regenerar ID de sesión periódicamente
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 300) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}
?>