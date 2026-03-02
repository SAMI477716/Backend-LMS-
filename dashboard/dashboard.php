<?php
session_start();

// 1. Security Check: If not logged in as instructor, kick back to login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../login.php");
    exit();
}

// 2. Single database connection
include '../config/db_config.php'; 

// 3. Fetch data for the Stats
$res = $pdo->query("SELECT COUNT(*) FROM students");
$total_students = $res->fetchColumn();

// 4. Fetch students for the Overview Table
$query = "SELECT name, batch_id, completion_percentage 
          FROM students 
          LEFT JOIN progress ON students.id = progress.student_id 
          ORDER BY batch_id ASC";
$stmt = $pdo->query($query);
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Dashboard - Peak LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="sidebar">
    <div class="p-4"><h4 class="fw-bold text-primary">LMS Dashboard</h4></div>
    <nav class="nav flex-column px-3">
        <a class="nav-link active" href="#"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link" href="#"><i class="bi bi-people me-2"></i> Students</a>
        <a class="nav-link" href="#"><i class="bi bi-book me-2"></i> Courses</a>
        <a class="nav-link" href="#"><i class="bi bi-journal-check me-2"></i> Grades</a>
        <a class="nav-link" href="#"><i class="bi bi-gear me-2"></i> Settings</a>
    </nav>
    <div class="mt-auto p-4 border-top">
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle p-2 me-2">TB</div>
            <div><small class="d-block fw-bold"><?php echo $_SESSION['username']; ?></small><small class="text-muted">Instructor</small></div>
        </div>
    </div>
</div>

<div class="main-content">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Dashboard</h2>
            <p class="text-muted">Welcome back, <?php echo $_SESSION['username']; ?>! Here's the latest data.</p>
        </div>
        <div class="text-muted"><i class="bi bi-calendar3 me-2"></i> March 2, 2026</div>
    </header>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card stat-card p-4">
                <div class="d-flex justify-content-between">
                    <div><h5 class="fw-bold">Batch 1</h5><p class="text-muted small"><i class="bi bi-people"></i> 24 students</p></div>
                    <div class="text-end"><h2 class="fw-bold mb-0">78%</h2></div>
                </div>
                <div class="progress-circle mt-3" style="border-top-color: #0056b3;"><span class="fw-bold">78%</span></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card p-4">
                <div class="d-flex justify-content-between">
                    <div><h5 class="fw-bold">Batch 2</h5><p class="text-muted small"><i class="bi bi-people"></i> 24 students</p></div>
                    <div class="text-end"><h2 class="fw-bold mb-0">75%</h2></div>
                </div>
                <div class="progress-circle mt-3" style="border-top-color: #28a745;"><span class="fw-bold">75%</span></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4 text-center">
        <div class="col-md-4">
            <div class="card stat-card p-3 d-flex flex-row justify-content-between align-items-center">
                <div><p class="text-muted mb-0">Total Students</p><h3 class="mb-0"><?php echo $total_students; ?></h3></div>
                <i class="bi bi-people fs-2 text-primary"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 d-flex flex-row justify-content-between align-items-center">
                <div><p class="text-muted mb-0">Active Courses</p><h3 class="mb-0">4</h3></div>
                <i class="bi bi-book fs-2 text-success"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 d-flex flex-row justify-content-between align-items-center">
                <div><p class="text-muted mb-0">Pending Grades</p><h3 class="mb-0">12</h3></div>
                <i class="bi bi-clipboard-check fs-2 text-warning"></i>
            </div>
        </div>
    </div>

    <div class="card stat-card p-4 mb-4">
        <h5 class="fw-bold mb-4">Course Completion Progress</h5>
        <div class="mb-4">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold small">GitHub Basics for Beginners</span>
            </div>
            <div class="progress" style="height: 8px; border-radius: 10px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 85%"></div>
            </div>
        </div>
        <div class="mb-2">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold small">LinkedIn Essentials for Beginners</span>
            </div>
            <div class="progress" style="height: 8px; border-radius: 10px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 72%"></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card stat-card p-4">
                <h5 class="fw-bold mb-3">Student Progress Overview</h5>
                <table class="table align-middle">
                    <thead><tr><th>Student</th><th>Batch</th><th>Completion</th><th>Assessments</th></tr></thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-size: 12px;">
                                        <?php echo strtoupper(substr($student['name'], 0, 2)); ?>
                                    </div>
                                    <?php echo htmlspecialchars($student['name']); ?>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-primary">Batch <?php echo $student['batch_id']; ?></span></td>
                            <td><?php echo $student['completion_percentage'] ?? 0; ?>%</td>
                            <td><i class="bi bi-check-circle text-success"></i> 8</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>