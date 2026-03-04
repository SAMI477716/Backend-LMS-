<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../student_login.php");
    exit();
}

include_once '../config/db_config.php'; 

$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['username'];

/** 2. Fetch Student Progress & Grades **/
try {
    // Fetch General Progress
    $query = "SELECT id, completion_percentage FROM progress WHERE student_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$student_id]);
    $my_progress = $stmt->fetchAll();

    // NEW: Fetch Detailed Grades from the grades table
    $grade_query = "SELECT course_name, grade, created_at FROM grades WHERE student_id = ? ORDER BY created_at DESC";
    $grade_stmt = $pdo->prepare($grade_query);
    $grade_stmt->execute([$student_id]);
    $my_grades = $grade_stmt->fetchAll();

} catch (PDOException $e) {
    $my_progress = [];
    $my_grades = [];
}

// 3. Calculate Average Progress
$avg_progress = 0;
if (count($my_progress) > 0) {
    $total = array_sum(array_column($my_progress, 'completion_percentage'));
    $avg_progress = round($total / count($my_progress));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - Peak LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: #fff;
            border-right: 1px solid #e9ecef;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #6c757d;
            padding: 12px 25px;
            font-weight: 500;
        }
        .sidebar .nav-link.active {
            color: #0d6efd;
            background: #f8f9fa;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .profile-icon {
            width: 45px;
            height: 45px;
            background-color: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
        }
        .card { border-radius: 12px; }
        .table thead { background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="px-4 mb-4">
        <h4 class="fw-bold text-primary">Peak LMS</h4>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link active" href="#"><i class="bi bi-house-door me-2"></i> My Dashboard</a>
        <a class="nav-link" href="#"><i class="bi bi-book me-2"></i> My Courses</a>
        <a class="nav-link" href="#"><i class="bi bi-award me-2"></i> Certificates</a>
        <a class="nav-link text-danger mt-4" href="../logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">My Learning</h2>
            <p class="text-muted small">Welcome back, <?php echo ucfirst(htmlspecialchars($student_name)); ?>!</p>
        </div>
        <div class="profile-icon shadow-sm">
            <?php echo strtoupper(substr($student_name, 0, 2)); ?>
        </div>
    </header>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 d-flex flex-row justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0 small fw-bold">Enrolled Courses</p>
                    <h3 class="mb-0"><?php echo count($my_progress); ?></h3>
                </div>
                <i class="bi bi-journal-text fs-3 text-primary"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 d-flex flex-row justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0 small fw-bold">Overall Progress</p>
                    <h3 class="mb-0"><?php echo $avg_progress; ?>%</h3>
                </div>
                <i class="bi bi-star fs-3 text-warning"></i>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4 mb-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-bar-chart-line me-2"></i>Course Progress</h5>
        <?php if (empty($my_progress)): ?>
            <div class="alert alert-info">You haven't been assigned any courses yet.</div>
        <?php else: ?>
            <?php foreach ($my_progress as $course): ?>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">Batch Progress Overview</span>
                        <span class="text-primary fw-bold"><?php echo $course['completion_percentage']; ?>%</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 10px;">
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: <?php echo $course['completion_percentage']; ?>%" 
                             aria-valuenow="<?php echo $course['completion_percentage']; ?>" 
                             aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-journal-check me-2"></i>My Exam Results</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-muted small uppercase">
                        <th>Course Name</th>
                        <th>Grade</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($my_grades)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted small">No grades have been posted for your account yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($my_grades as $row): ?>
                            <tr>
                                <td class="fw-semibold text-dark"><?php echo htmlspecialchars($row['course_name']); ?></td>
                                <td class="fw-bold text-primary"><?php echo $row['grade']; ?>%</td>
                                <td>
                                    <?php if($row['grade'] >= 50): ?>
                                        <span class="badge bg-success-subtle text-success border border-success">Passed</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger">Retake Needed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>