<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

require_once '../../src_php/Config/database.php';
require_once '../../src_php/Models/Crop.php';

$database = new \Config\Database();
$db = $database->getConnection();
$crop = new \Models\Crop($db);

$data = json_decode(file_get_contents("php://input"));
$action = isset($_GET['action']) ? $_GET['action'] : (isset($data->action) ? $data->action : '');

if ($action === 'list') {
    $stmt = $crop->read();
    $crops = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    echo json_encode(["status" => "success", "data" => $crops]);
} elseif ($action === 'create' || $action === 'update') {
    if (!empty($data->crop_name)) {
        if ($action === 'update' && empty($data->id)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID is required for update."]);
            exit();
        }

        $crop->crop_name = $data->crop_name;
        $crop->scientific_name = $data->scientific_name ?? null;
        $crop->short_desc = $data->short_desc ?? null;
        $crop->image_url = $data->image_url ?? null;
        $crop->ph_min = $data->ph_min !== '' ? $data->ph_min : null;
        $crop->ph_max = $data->ph_max !== '' ? $data->ph_max : null;
        $crop->temp_min = $data->temp_min !== '' ? $data->temp_min : null;
        $crop->temp_max = $data->temp_max !== '' ? $data->temp_max : null;
        $crop->rain_min = $data->rain_min !== '' ? $data->rain_min : null;
        $crop->rain_max = $data->rain_max !== '' ? $data->rain_max : null;
        $crop->n_min = $data->n_min !== '' ? $data->n_min : null;
        $crop->n_max = $data->n_max !== '' ? $data->n_max : null;
        $crop->seasons = $data->seasons ?? null;
        $crop->states = $data->states ?? null;
        $crop->water_req = $data->water_req ?? null;

        if ($action === 'create') {
            if ($crop->create()) {
                echo json_encode(["status" => "success", "message" => "Crop created."]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to create crop."]);
            }
        } else {
            $crop->id = $data->id;
            if ($crop->update()) {
                echo json_encode(["status" => "success", "message" => "Crop updated."]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to update crop."]);
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Crop name is required."]);
    }
} elseif ($action === 'delete') {
    if (!empty($data->id)) {
        $crop->id = $data->id;
        if ($crop->delete()) {
            echo json_encode(["status" => "success", "message" => "Crop deleted."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to delete crop."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID is required."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid action."]);
}
?>
