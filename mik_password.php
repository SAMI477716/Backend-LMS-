<?php
include '../config/db_config.php';

// The password you want mik to use
$plain_password = 'mik123'; 
$new_hash = password_hash($plain_password, PASSWORD_DEFAULT);

// We use a placeholder (?) for the hash and specify the exact username
$sql = "UPDATE users SET password_hash = ? WHERE username = 'mik'";
$stmt = $pdo->prepare($sql);

if($stmt->execute([$new_hash])) {
    echo "Success! mik's password is now encrypted. Try logging in with: mik123";
} else {
    echo "Error updating database.";
}
?>