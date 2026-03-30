<?php
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

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../src_php/Services/PythonApiClient.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array("status" => "error", "message" => "Unauthorized access."));
    exit();
}

// Decode incoming POST data from frontend
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->state) && isset($data->soil_ph) && isset($data->temperature) && isset($data->rainfall)) {
    
    $client = new \Services\PythonApiClient();
    
    // Call Python MS
    $recommendations = $client->getRecommendations(
        $data->soil_ph, 
        $data->rainfall, 
        $data->temperature, 
        $data->humidity ?? 60, 
        $data->nitrogen ?? 50, 
        $data->state,
        $data->season ?? null
    );

    if ($recommendations) {
        http_response_code(200);
        echo json_encode(array("status" => "success", "data" => $recommendations));
    } else {
        http_response_code(500);
        echo json_encode(array("status" => "error", "message" => "Failed to get recommendations from ML Engine."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("status" => "error", "message" => "Missing required farm parameters."));
}
?>
