<?php
session_start();
// 1. Database Connection - Path adjusted for your structure
include_once('../config/db_config.php');

// 2. Security: Ensure only instructors can post grades
if ($_SESSION['role'] !== 'instructor') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_grade'])) {
    // Collect data from the form
    $student_id = $_POST['student_id'];
    $course_name = $_POST['course_name'];
    //$grade_value = $_POST['grade'];
    $grade = intval($_POST['grade']); // This captures the full number 85

    try {
        // Prepare SQL based on your updated schema
        $sql = "INSERT INTO grades (student_id, course_name, grade) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$student_id, $course_name, $grade_value])) {
            // Redirect back with success status
            header("Location: instructor_dashboard.php?status=success");
            exit();
        }
    } catch (PDOException $e) {
        // Redirect back with error message
        header("Location: instructor_dashboard.php?status=error&msg=" . urlencode($e->getMessage()));
        exit();
    }
}
?>