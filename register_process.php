<?php
include_once 'config/db_config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role']; 
    $batch = $_POST['batch'];

    // Securely hash the password
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO users (username, email, password_hash, role, batch) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$username, $email, $hashed_pass, $role, $batch])) {
            $user_id = $pdo->lastInsertId();

            // Auto-login session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // --- REDIRECT LOGIC FIX ---
            if ($role === 'instructor') {
                // If your instructor file is in the 'dashboard' folder:
                header("Location: dashboard/dashboard.php"); 
            } else {
                // For students:
                header("Location: student/student_dashboard.php");
            }
            exit();
        }

    } catch (PDOException $e) {
        // This will catch if the username already exists
        echo "Registration Error: " . $e->getMessage();
    }
}
?>