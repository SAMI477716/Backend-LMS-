<?php
session_start();
// Use two dots (../) because this file is inside the 'student' folder
include '../config/db_config.php'; 

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['username'];

// 2. Fetch Sami's Data
$query = "SELECT course_name, completion_percentage FROM progress WHERE student_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$student_id]);
$my_progress = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - Peak LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <header class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-0">Welcome back, <?php echo ucfirst($student_name); ?>!</h2>
            <p class="text-muted">Tracking your path to Peak Performance.</p>
        </div>
        <a href="../logout.php" class="btn btn-danger shadow-sm">
            <i class="bi bi-box-arrow-left me-2"></i> Logout
        </a>
    </header>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px;">
                <h5 class="fw-bold mb-4">My Courses</h5>
                <?php foreach ($my_progress as $course): ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold"><?php echo htmlspecialchars($course['course_name']); ?></span>
                            <span class="text-primary fw-bold"><?php echo $course['completion_percentage']; ?>%</span>
                        </div>
                        <div class="progress" style="height: 12px; border-radius: 10px;">
                            <div class="progress-bar bg-primary" style="width: <?php echo $course['completion_percentage']; ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px;">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 28px; margin: 0 auto;">
                    <?php echo strtoupper(substr($student_name, 0, 1)); ?>
                </div>
                <h5 class="fw-bold"><?php echo ucfirst($student_name); ?></h5>
                <hr>
                <p class="small text-muted text-start"><i class="bi bi-envelope me-2"></i> sami@peak.com</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>