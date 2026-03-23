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
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../src_php/Config/database.php';
require_once '../../src_php/Models/FarmRecord.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array("status" => "error", "message" => "Unauthorized access."));
    exit();
}

$database = new \Config\Database();
$db = $database->getConnection();
$record = new \Models\FarmRecord($db);

$record->user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $record->readAllByUser();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $records_arr = array();
            $records_arr["records"] = array();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                extract($row);
                $record_item = array(
                    "id" => $id,
                    "state" => $state,
                    "soil_ph" => $soil_ph,
                    "temperature" => $temperature,
                    "rainfall" => $rainfall,
                    "recommended_crop" => $recommended_crop,
                    "status" => $status,
                    "created_at" => $created_at
                );
                array_push($records_arr["records"], $record_item);
            }
            http_response_code(200);
            echo json_encode($records_arr);
        } else {
            http_response_code(200);
            echo json_encode(array("records" => array(), "message" => "No records found."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->state) && !empty($data->soil_ph) && !empty($data->recommended_crop)) {
            $record->state = $data->state;
            $record->soil_ph = $data->soil_ph;
            $record->temperature = $data->temperature ?? 0;
            $record->rainfall = $data->rainfall ?? 0;
            $record->humidity = $data->humidity ?? 0;
            $record->nitrogen = $data->nitrogen ?? 0;
            $record->recommended_crop = $data->recommended_crop;

            if ($record->create()) {
                http_response_code(201);
                echo json_encode(array("status" => "success", "message" => "Record saved."));
            } else {
                http_response_code(503);
                echo json_encode(array("status" => "error", "message" => "Unable to create record."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("status" => "error", "message" => "Incomplete data."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->status)) {
            $record->id = $data->id;
            $record->status = $data->status;

            if ($record->updateStatus()) {
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => "Record updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("status" => "error", "message" => "Unable to update record."));
            }
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            $record->id = $data->id;
            if ($record->delete()) {
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => "Record deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("status" => "error", "message" => "Unable to delete record."));
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("status" => "error", "message" => "Method not allowed."));
        break;
}
?>
