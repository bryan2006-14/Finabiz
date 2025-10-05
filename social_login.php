<?php
// social_login.php
// Si no hay 'code' mostramos el link para iniciar login
$client_id     = getenv('GOOGLE_CLIENT_ID');
$client_secret = getenv('GOOGLE_CLIENT_SECRET');
$redirect_uri  = getenv('GOOGLE_REDIRECT_URI');

if (!$client_id || !$client_secret || !$redirect_uri) {
    http_response_code(500);
    echo "Faltan variables de entorno. Revisa GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET y GOOGLE_REDIRECT_URI.";
    exit;
}

if (!isset($_GET['code'])) {
    // Generar URL de autorización
    $scope = urlencode('email profile');
    $auth_url = "https://accounts.google.com/o/oauth2/v2/auth"
        . "?response_type=code"
        . "&client_id=" . urlencode($client_id)
        . "&redirect_uri=" . urlencode($redirect_uri)
        . "&scope=" . $scope
        . "&access_type=online"
        . "&prompt=consent";
    echo "<a href='$auth_url'>Iniciar sesión con Google</a>";
    exit;
}

// Si llegó 'code' -> intercambiar por token
$code = $_GET['code'];
$token_endpoint = "https://oauth2.googleapis.com/token";

$post = http_build_query([
    'code' => $code,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code'
]);

$ch = curl_init($token_endpoint);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
$res = curl_exec($ch);
if ($res === false) {
    echo "Curl error: " . curl_error($ch);
    exit;
}
curl_close($ch);

$token = json_decode($res, true);
if (isset($token['error'])) {
    echo "Token error: " . htmlspecialchars(json_encode($token));
    exit;
}

$access_token = $token['access_token'] ?? null;
if (!$access_token) {
    echo "No se obtuvo access_token.";
    exit;
}

// Obtener info del usuario
$userinfo = file_get_contents("https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . urlencode($access_token));
$user = json_decode($userinfo, true);

// Aquí ya tienes datos del usuario: id, email, name, picture...
echo "<h3>Usuario</h3>";
echo "<pre>" . htmlspecialchars(print_r($user, true)) . "</pre>";

// TODO: iniciar sesión en tu app, buscar/crear usuario en DB, etc.
