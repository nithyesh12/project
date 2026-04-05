<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

require_once '../../src_php/Config/database.php';

$database = new \Config\Database();
$db = $database->getConnection();

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'list') {
    $query = "SELECT id, first_name, last_name, email, created_at FROM users ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    echo json_encode(["status" => "success", "data" => $users]);
} elseif ($action === 'delete') {
    $data = json_decode(file_get_contents("php://input"));
    if (!empty($data->id)) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $data->id);
        if ($stmt->execute()) {
            // Further logic could clean up farm records for this user
            echo json_encode(["status" => "success", "message" => "User deleted."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to delete user."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID is required."]);
    }
} elseif ($action === 'stats') {
    $stmt1 = $db->prepare("SELECT COUNT(*) as count FROM users");
    $stmt1->execute();
    $users_count = $stmt1->fetch(\PDO::FETCH_ASSOC)['count'];

    $stmt2 = $db->prepare("SELECT COUNT(*) as count FROM crops");
    $stmt2->execute();
    $crops_count = $stmt2->fetch(\PDO::FETCH_ASSOC)['count'];

    echo json_encode(["status" => "success", "users" => $users_count, "crops" => $crops_count]);
}
?>
