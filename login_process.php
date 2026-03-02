<?php
session_start();
include 'config/db_config.php'; // Using your working database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Find the user in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // 2. Check if user exists and password is correct
    if ($user && password_verify($password, $user['password_hash'])) {
        // Store user info in a Session so the dashboard knows who logged in
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // 3. Redirect to the instructor dashboard
        header("Location: dashboard/dashboard.php");
        exit();
    } else {
        // If wrong, go back to login with an error
        header("Location: login.php?error=invalid");
        exit();
    }
}