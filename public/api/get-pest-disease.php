<?php
require_once '../../src_php/Config/database.php';
use Config\Database;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_GET['crop_name'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing crop_name parameter."]);
    exit();
}

$crop_name = $_GET['crop_name'];

$database = new Database();
$db = $database->getConnection();

$query = "
    SELECT pd.*, c.crop_name 
    FROM pest_disease pd
    JOIN crops c ON pd.crop_id = c.id
    WHERE c.crop_name = ?
";

$stmt = $db->prepare($query);
$stmt->execute([$crop_name]);

$results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

if ($results) {
    http_response_code(200);
    echo json_encode(["status" => "success", "data" => $results]);
} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "No pest or disease data found for this crop."]);
}
?>
