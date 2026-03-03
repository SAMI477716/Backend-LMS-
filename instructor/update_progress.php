<?php
session_start();
include_once '../config/db_config.php';

// Security: Only Instructors allowed
if ($_SESSION['role'] !== 'instructor') {
    die("Access Denied");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $new_progress = $_POST['progress'];

    // Update the progress table for that specific student
    $sql = "UPDATE progress SET completion_percentage = ? WHERE student_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$new_progress, $student_id])) {
        echo "Successfully updated progress to $new_progress%";
    }
}
?>

<form method="POST">
    <label>Student ID (e.g., 2 for Sami):</label>
    <input type="number" name="student_id" required>
    
    <label>New Progress %:</label>
    <input type="number" name="progress" min="0" max="100" required>
    
    <button type="submit">Update Student</button>
</form>