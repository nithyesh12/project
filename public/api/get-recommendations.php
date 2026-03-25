<?php
// Handle CORS if needed during dev
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Secure session settings
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

// Disable caching to prevent back-button access
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Normally we would use an autoloader (like Composer)
require_once '../../src_php/Services/PythonApiClient.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array("status" => "error", "message" => "Unauthorized access."));
    exit();
}

use Services\PythonApiClient;

// This endpoint receives JSON from the frontend JS
$data = json_decode(file_get_contents("php://input"));

if (
    $data !== null &&
    !empty($data->soil_ph) &&
    !empty($data->rainfall) &&
    !empty($data->temperature) &&
    !empty($data->state) &&
    isset($data->humidity) &&
    isset($data->nitrogen)
) {
    // Sanitize inputs
    $soil_ph = floatval(htmlspecialchars(strip_tags($data->soil_ph)));
    $rainfall = floatval(htmlspecialchars(strip_tags($data->rainfall)));
    $temperature = floatval(htmlspecialchars(strip_tags($data->temperature)));
    $humidity = floatval(htmlspecialchars(strip_tags($data->humidity)));
    $nitrogen = floatval(htmlspecialchars(strip_tags($data->nitrogen)));
    $state = htmlspecialchars(strip_tags($data->state));

    $pythonClient = new PythonApiClient();
    $recommendations = $pythonClient->getRecommendations($soil_ph, $rainfall, $temperature, $humidity, $nitrogen, $state);

    if ($recommendations !== null) {
        http_response_code(200);
        echo json_encode(array("status" => "success", "recommendations" => $recommendations));
    }
    else {
        http_response_code(503);
        echo json_encode(array("status" => "error", "message" => "Unable to communicate with the Data Engine Microservice. Please ensure the Python server is running on port 5000."));
    }
}
else {
    http_response_code(400);
    echo json_encode(array("status" => "error", "message" => "Incomplete data. Please provide soil pH, rainfall, temperature, humidity, nitrogen, and state."));
}
?>
