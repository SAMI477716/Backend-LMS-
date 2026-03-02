<?php
include 'config/db_config.php';

$new_password = password_hash('peak123', PASSWORD_DEFAULT);
$sql = "UPDATE users SET password_hash = ? WHERE username = 'tebarek'";
$stmt = $pdo->prepare($sql);

if($stmt->execute([$new_password])) {
    echo "Password updated successfully for Tebarek! You can now log in.";
} else {
    echo "Error updating password.";
}
?>