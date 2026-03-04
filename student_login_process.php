<?php
session_start();
include_once 'config/db_config.php'; // Corrected path

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Find the user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verify password against hash
    if ($user && password_verify($password, $user['password_hash'])) {
        // Log them in immediately (no verification column exists)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: student/student_dashboard.php");
        exit();
    } else {
        echo "Invalid username or password.";
    }
}
?>