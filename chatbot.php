<?php
// ================== CONFIGURACIÓN Y LOGS ==================
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/chatbot_errors.log'); // Guardar log en el mismo directorio

require 'config.php'; // Aquí defines tu GEMINI_API_KEY
header("Content-Type: application/json");

// ================== VALIDACIÓN DE API KEY ==================
if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) {
    error_log("ERROR: API key de Gemini no configurada");
    echo json_encode(["reply" => "Error de configuración del sistema. Contacta al administrador."]);
    exit;
}

// ================== LECTURA DE LA PREGUNTA ==================
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data || !isset($data["message"])) {
    error_log("ERROR: No se recibió mensaje en la solicitud");
    echo json_encode(["reply" => "Por favor escribe una consulta válida."]);
    exit;
}

$question = trim($data["message"]);
if (empty($question)) {
    echo json_encode(["reply" => "Por favor escribe una consulta."]);
    exit;
}

error_log("Pregunta recibida: " . $question);

// ================== CONFIGURACIÓN DE LA SOLICITUD ==================
// Endpoint de Gemini
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . GEMINI_API_KEY;

$payload = [
    "contents" => [
        [
            "role" => "user",
            "parts" => [
                ["text" => "Eres un asistente experto en finanzas para pequeñas empresas. 
                Solo respondes preguntas relacionadas con ingresos, gastos, presupuestos y estrategias financieras. 
                Si la pregunta no es sobre finanzas, responde amablemente que solo puedes hablar de ese tema."],
                ["text" => $question]
            ]
        ]
    ]
];

// ================== EJECUCIÓN ==================
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// ================== LOGS ==================
error_log("Código HTTP: " . $httpCode);
if ($error) error_log("Error cURL: " . $error);
if ($response) error_log("Respuesta API: " . substr($response, 0, 300));

// ================== MANEJO DE ERRORES ==================
if ($response === false) {
    echo json_encode(["reply" => "Error de conexión con el servicio. Intenta nuevamente."]);
    exit;
}

$result = json_decode($response, true);

if ($httpCode !== 200) {
    error_log("Error HTTP $httpCode: " . print_r($result, true));
    switch ($httpCode) {
        case 401:
            echo json_encode(["reply" => "Error de autenticación con la API de Gemini. Verifica tu clave."]);
            break;
        case 429:
            echo json_encode(["reply" => "El servicio está ocupado o alcanzaste el límite. Intenta más tarde."]);
            break;
        default:
            echo json_encode(["reply" => "Ocurrió un error en el servicio de Gemini. Intenta más tarde."]);
            break;
    }
    exit;
}

if (!$result || isset($result['error'])) {
    error_log("Error de API: " . print_r($result, true));
    echo json_encode(["reply" => "Error procesando tu consulta. Intenta reformular tu pregunta."]);
    exit;
}

// ================== RESPUESTA ==================
$reply = $result["candidates"][0]["content"]["parts"][0]["text"] ?? "";
$reply = trim($reply);

if (empty($reply)) {
    $reply = "Revisa tus ingresos y gastos. Una buena práctica es que tus ingresos sean al menos un 20% mayores a tus gastos.";
}

echo json_encode(["reply" => $reply]);
?>
