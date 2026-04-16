<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

require_once '../../src_php/Config/database.php';

try {
    $database = new \Config\Database();
    $db = $database->getConnection();

    $query = "SELECT c.crop_name, c.image_url, cc.category, cc.benefits, cc.usage_method 
              FROM crop_cosmetics cc
              JOIN crops c ON cc.crop_id = c.id
              ORDER BY c.crop_name ASC";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $cosmetics = array();
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $cosmetics[] = array(
            "crop_name" => $row['crop_name'],
            "image_url" => $row['image_url'],
            "category" => $row['category'],
            "benefits" => $row['benefits'],
            "usage_method" => $row['usage_method']
        );
    }

    if(empty($cosmetics)) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "No cosmetic data found."]);
    } else {
        http_response_code(200);
        echo json_encode(["status" => "success", "data" => $cosmetics]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to fetch cosmetic data: " . $e->getMessage()]);
}
?>
