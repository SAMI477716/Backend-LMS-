<?php
include_once 'config/db_config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role']; 
    $batch = $_POST['batch'];

    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO users (username, email, password_hash, role, batch) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$username, $email, $hashed_pass, $role, $batch])) {
            $user_id = $pdo->lastInsertId();

            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // --- REDIRECT FIX BASED ON YOUR IMAGES ---
            if ($role === 'instructor') {
                // Verified path from image_e7f4c0.png
                header("Location: dashboard/dashboard.php"); 
            } else {
                // Verified path from image_e7f49f.png
                header("Location: student/student_dashboard.php");
            }
            exit();
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>