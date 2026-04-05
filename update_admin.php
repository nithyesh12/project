<?php
require_once 'src_php/Config/database.php';

$database = new \Config\Database();
$db = $database->getConnection();

$hash = password_hash('admin123', PASSWORD_BCRYPT);
$query = "UPDATE admins SET password_hash = :hash WHERE email = 'admin@growyourcrops.com'";
$stmt = $db->prepare($query);
$stmt->bindParam(':hash', $hash);
if ($stmt->execute()) {
    echo "Admin password updated successfully.";
} else {
    echo "Failed to update admin password.";
}
?>
