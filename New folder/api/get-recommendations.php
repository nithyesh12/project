<?php
// Handle CORS if needed during dev
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Normally we would use an autoloader (like Composer)
require_once '../../src_php/Services/PythonApiClient.php';

use Services\PythonApiClient;

// This endpoint receives JSON from the frontend JS
$data = json_decode(file_get_contents("php://input"));

if (
!empty($data->soil_ph) &&
!empty($data->rainfall) &&
!empty($data->temperature) &&
!empty($data->state)
) {
    // Sanitize inputs
    $soil_ph = floatval(htmlspecialchars(strip_tags($data->soil_ph)));
    $rainfall = floatval(htmlspecialchars(strip_tags($data->rainfall)));
    $temperature = floatval(htmlspecialchars(strip_tags($data->temperature)));
    $state = htmlspecialchars(strip_tags($data->state));

    $pythonClient = new PythonApiClient();
    $recommendations = $pythonClient->getRecommendations($soil_ph, $rainfall, $temperature, $state);

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
    echo json_encode(array("status" => "error", "message" => "Incomplete data. Please provide soil pH, rainfall, temperature, and state."));
}
?>
