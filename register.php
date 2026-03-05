<?php
// 1. Include database connection
include_once 'config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Capture form data
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];
    $batch    = $_POST['batch']; // e.g., "Batch 1"

    try {
        // Start a transaction to ensure both tables are updated or none at all
        $pdo->beginTransaction();

        // 3. Insert into 'users' table
        $sql_user = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([$username, $email, $password, $role]);
        
        // Get the ID of the user we just inserted
        $user_id = $pdo->lastInsertId();

        // 4. If the role is 'student', automatically create the student profile
        if ($role === 'student') {
            // We use $user_id to link the tables
            $sql_student = "INSERT INTO students (user_id, name, batch_id) VALUES (?, ?, ?)";
            $stmt_student = $pdo->prepare($sql_student);
            
            // Note: batch_id in your students table seems to store strings like "Batch 1" 
            // based on your HTML select values.
            $stmt_student->execute([$user_id, $username, $batch]);
        }

        // Commit the changes to the database
        $pdo->commit();

        // Redirect based on role
        if ($role === 'student') {
            header("Location: student_login.php?registration=success");
        } else {
            header("Location: login.php?registration=success");
        }
        exit();

    } catch (Exception $e) {
        // If anything goes wrong, undo everything
        $pdo->rollBack();
        error_log("Registration Error: " . $e->getMessage());
        die("An error occurred during registration. Please try again.");
    }
}