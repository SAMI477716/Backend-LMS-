<?php
include 'config/db_config.php';

// Encrypt Sami's password
$new_password = password_hash('sami123', PASSWORD_DEFAULT);

// Update the database specifically for 'sami'
$sql = "UPDATE users SET password_hash = ? WHERE username = 'sami'";
$stmt = $pdo->prepare($sql);

if($stmt->execute([$new_password])) {
    echo "Password updated successfully for Sami! You can now log in as a student.";
} else {
    echo "Error updating student password.";
}
?>