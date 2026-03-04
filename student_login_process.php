<?php
session_start();
include_once 'config/db_config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Find the user in the users table
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // 2. Verify password
    if ($user && password_verify($password, $user['password_hash'])) {
        
        // 3. IMPORTANT: Fetch the student_id from the students table
        $stmt_student = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
        $stmt_student->execute([$user['id']]);
        $student = $stmt_student->fetch();

        // 4. Set Session Variables
        $_SESSION['user_id'] = $user['id'];      // The login ID (2)
        $_SESSION['student_id'] = $student['id']; // The data ID (1)
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: student/student_dashboard.php");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: student_login.php?error=1");
        exit();
    }
}
?>