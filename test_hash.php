<?php
var_dump(password_verify('admin123', '$2y$10$wTfH.rW6YnB6Jm/9g1f3c.BfMpHX76b9uK/tY41D45HtbvO.S0e0m'));
$new_hash = password_hash('admin123', PASSWORD_BCRYPT);
echo "\nNew hash: $new_hash\n";
?>
