<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recuperar datos del formulario
    $nombreUsuario = $_POST["username"] ?? '';
    $correo        = $_POST["email"] ?? '';
    $password      = $_POST["password"] ?? '';
    
    // Validaciones básicas
    if (empty($nombreUsuario) || empty($correo) || empty($password)) {
        die("Error: Todos los campos son obligatorios.");
    }

    // Preparar datos para Django
    $data = [
        'nombre' => $nombreUsuario,
        'correo' => $correo,
        'password' => $password
    ];
    
    // Configurar llamada a API de Django
    $url = 'http://localhost:8000/api/registrar/';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    // Verificar si hubo error en cURL
    if ($curl_error) {
        die("Error de conexión: " . $curl_error);
    }
    
    // Decodificar respuesta
    $result = json_decode($response, true);
    
    // Verificar respuesta
    if ($http_code === 200 && isset($result['success']) && $result['success']) {
        // Registro exitoso
        header("Location: registroExitoso.php");
        exit;
    } else {
        // Mostrar error específico
        $error_msg = $result['message'] ?? 'Error desconocido del servidor';
        die("Error: " . $error_msg . " (Código: $http_code)");
    }
} else {
    // Si no es POST, redirigir
    header("Location: ../registrarse.php");
    exit;
}
?>