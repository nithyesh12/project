<?php
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

header("Content-Type: application/json; charset=UTF-8");

require_once '../../src_php/Config/database.php';
require_once '../../src_php/Models/Admin.php';

$database = new \Config\Database();
$db = $database->getConnection();
$admin = new \Models\Admin($db);

$data = json_decode(file_get_contents("php://input"));
$action = isset($data->action) ? $data->action : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action === 'login') {
    if (!empty($data->email) && !empty($data->password)) {
        $admin->email = $data->email;
        $email_exists = $admin->emailExists();

        if ($email_exists && password_verify($data->password, $admin->password_hash)) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin->id;
            
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Admin login successful."]);
        } else {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Invalid admin credentials."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Incomplete data."]);
    }
} elseif ($action === 'logout') {
    $_SESSION = array();
    session_destroy();
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Admin logged out."]);
} elseif ($action === 'check') {
    if (isset($_SESSION['admin_id'])) {
        http_response_code(200);
        echo json_encode(["status" => "success", "admin_id" => $_SESSION['admin_id']]);
    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Not logged in as admin."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid action."]);
}
?>
