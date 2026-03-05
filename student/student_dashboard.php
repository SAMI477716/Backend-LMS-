<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../student_login.php");
    exit();
}

include_once '../config/db_config.php'; 

$user_id = $_SESSION['user_id']; 
$student_name = $_SESSION['username'];

try {
    // 1. Get the student_id
    $stmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $student_row = $stmt->fetch(PDO::FETCH_ASSOC);
    $student_id = $student_row ? $student_row['id'] : 0;

    // 2. Fetch Detailed Grades for the Table
    $grade_query = "SELECT course_name, grade, created_at FROM grades WHERE student_id = ? ORDER BY created_at DESC";
    $grade_stmt = $pdo->prepare($grade_query);
    $grade_stmt->execute([$student_id]);
    $my_grades = $grade_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. ENROLLMENT PROGRESS CALCULATION (Real-Time Sync)
    
    // A. Count total available courses in the curriculum
    $total_courses_res = $pdo->query("SELECT COUNT(*) FROM courses");
    $total_system_courses = (int)$total_courses_res->fetchColumn();

    // B. Count how many courses the student is actually enrolled in (exists in grades table)
    $enrolled_stmt = $pdo->prepare("SELECT COUNT(DISTINCT course_name) FROM grades WHERE student_id = ?");
    $enrolled_stmt->execute([$student_id]);
    $enrolled_count = (int)$enrolled_stmt->fetchColumn();

    // C. Calculate Enrollment Percentage
    if ($total_system_courses > 0) {
        $overall_val = round(($enrolled_count / $total_system_courses) * 100);
    } else {
        $overall_val = 0;
    }

    // D. Keeping passed_count just for the status text
    $passed_stmt = $pdo->prepare("SELECT COUNT(DISTINCT course_name) FROM grades WHERE student_id = ? AND grade >= 50");
    $passed_stmt->execute([$student_id]);
    $passed_count = (int)$passed_stmt->fetchColumn();

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $my_grades = [];
    $overall_val = 0;
}

/** Helper Function for Status Requirements **/
/*function getStatusDetails($percent) {
    if ($percent < 25) return ['text' => 'Just Started', 'color' => 'bg-secondary'];
    if ($percent < 50) return ['text' => 'In Progress', 'color' => 'bg-warning text-dark'];
    if ($percent <= 99) return ['text' => 'Almost There', 'color' => 'bg-info text-white'];
    return ['text' => 'Completed Curriculum', 'color' => 'bg-success'];
}*/
/** Helper Function for Status Requirements **/
function getStatusDetails($percent) {
    if ($percent < 20) {
        return ['text' => 'Critical Warning', 'color' => 'bg-danger'];
    } elseif ($percent >= 20 && $percent < 50) {
        return ['text' => 'Needs Help', 'color' => 'bg-warning text-dark'];
    } elseif ($percent >= 50 && $percent <= 75) {
        return ['text' => 'On Track', 'color' => 'bg-info text-white'];
    } else {
        // This covers everything > 75
        return ['text' => 'Excellent', 'color' => 'bg-success'];
    }
}

$status = getStatusDetails($overall_val);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - Peak LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar { width: 250px; height: 100vh; position: fixed; background: #fff; border-right: 1px solid #e9ecef; padding-top: 20px; }
        .sidebar .nav-link { color: #6c757d; padding: 12px 25px; font-weight: 500; text-decoration: none; display: block; }
        .sidebar .nav-link.active { color: #0d6efd; background: #f8f9fa; }
        .main-content { margin-left: 250px; padding: 30px; background-color: #f8f9fa; min-height: 100vh; }
        .profile-icon { width: 45px; height: 45px; background-color: #0d6efd; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; }
        .card { border-radius: 12px; border: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="px-4 mb-4"><h4 class="fw-bold text-primary">Peak LMS</h4></div>
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
            <div class="card shadow-sm p-3 d-flex flex-row justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0 small fw-bold">Enrolled Courses</p>
                    <h3 class="mb-0"><?php echo $enrolled_count; ?></h3>
                </div>
                <i class="bi bi-journal-text fs-3 text-primary"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3 d-flex flex-row justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0 small fw-bold">Enrollment Progress</p>
                    <h3 class="mb-0"><?php echo $overall_val; ?>%</h3>
                </div>
                <i class="bi bi-percent fs-3 text-success"></i>
            </div>
        </div>
    </div>

    <div class="card shadow-sm p-4 mb-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-bar-chart-line me-2"></i>Course Completion Progress</h5>
        <div class="mb-4">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold">Enrollment vs Total Curriculum</span>
                <span class="badge <?php echo $status['color']; ?>"><?php echo $status['text']; ?></span>
            </div>
            <div class="progress" style="height: 18px; border-radius: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $status['color']; ?>" 
                     role="progressbar" 
                     style="width: <?php echo $overall_val; ?>%">
                     <?php echo $overall_val; ?>%
                </div>
            </div>
            <p class="text-muted small mt-2">
                You have enrolled in <strong><?php echo $enrolled_count; ?></strong> out of <strong><?php echo $total_system_courses; ?></strong> available courses.
            </p>
        </div>
    </div>

    <div class="card shadow-sm p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-journal-check me-2"></i>My Exam Results</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>Course Name</th>
                        <th>Grade</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($my_grades)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No results found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($my_grades as $row): ?>
                            <tr>
                                <td class="fw-semibold"><?php echo htmlspecialchars($row['course_name']); ?></td>
                                <td class="fw-bold text-primary"><?php echo $row['grade']; ?>%</td>
                                <td>
                                    <span class="badge <?php echo $row['grade'] >= 50 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?>">
                                        <?php echo $row['grade'] >= 50 ? 'Passed' : 'Retake Needed'; ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <?php echo !empty($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : 'N/A'; ?>
                                </td>
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