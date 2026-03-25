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

// Prevent CORS issues during development
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../src_php/Config/database.php';
require_once '../../src_php/Models/User.php';

$database = new \Config\Database();
$db = $database->getConnection();
$user = new \Models\User($db);

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->action)) {
    // Also support GET action
    $action = isset($_GET['action']) ? $_GET['action'] : '';
} else {
    $action = $data->action;
}

if ($action === 'register') {
    if (!empty($data->first_name) && !empty($data->last_name) && !empty($data->email) && !empty($data->password)) {
        $user->first_name = $data->first_name;
        $user->last_name = $data->last_name;
        $user->email = $data->email;
        $user->password = password_hash($data->password, PASSWORD_BCRYPT);

        if ($user->create()) {
            http_response_code(201);
            echo json_encode(array("status" => "success", "message" => "User was created."));
        } else {
            http_response_code(400);
            echo json_encode(array("status" => "error", "message" => "Unable to create user. Email may exist."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("status" => "error", "message" => "Unable to create user. Data is incomplete."));
    }
} elseif ($action === 'login') {
    if (!empty($data->email) && !empty($data->password)) {
        $user->email = $data->email;
        $email_exists = $user->emailExists();

        if ($email_exists && password_verify($data->password, $user->password)) {
            // Prevent Session Fixation by generating a new session ID
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->id;
            $_SESSION['first_name'] = $user->first_name;
            
            http_response_code(200);
            echo json_encode(array(
                "status" => "success",
                "message" => "Successful login.",
                "user" => array(
                    "id" => $user->id,
                    "first_name" => $user->first_name,
                    "last_name" => $user->last_name
                )
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("status" => "error", "message" => "Login failed."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("status" => "error", "message" => "Login failed. Data is incomplete."));
    }
} elseif ($action === 'logout') {
    // Unset all session variables
    $_SESSION = array();

    // Kill the session cookie completely
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    
    // Attempt to explicitly clear browser cache to ensure back button doesn't leak info
    header("Clear-Site-Data: \"cache\", \"cookies\", \"storage\"");
    
    http_response_code(200);
    echo json_encode(array("status" => "success", "message" => "Logged out."));
} elseif ($action === 'session') {
    if (isset($_SESSION['user_id'])) {
        http_response_code(200);
        echo json_encode(array(
            "status" => "success", 
            "user" => array("id" => $_SESSION['user_id'], "first_name" => $_SESSION['first_name'])
        ));
    } else {
        http_response_code(401);
        echo json_encode(array("status" => "error", "message" => "Not logged in."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("status" => "error", "message" => "Invalid action."));
}
?>
