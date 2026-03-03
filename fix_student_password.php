<?php
include 'config/db_config.php';

// 1. Create the new password hash
$new_password = password_hash('sami123', PASSWORD_DEFAULT);

// 2. IMPORTANT: Update BOTH the password and the role to 'student'
$sql = "UPDATE users SET password_hash = ?, role = 'student' WHERE username = 'sami'";
$stmt = $pdo->prepare($sql);

if($stmt->execute([$new_password])) {
    echo "SUCCESS: Sami's role is now 'student' and password is 'sami123'.<br>";
    echo "You can now login at student_login.php";
} else {
    echo "ERROR: Failed to update the database.";
}
?>