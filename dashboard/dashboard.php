<?php
session_start();

// 1. Security Check: If not logged in as instructor, kick back to login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../login.php");
    exit();
}

// 2. Single database connection
include '../config/db_config.php'; 

// --- NEW: GRADE SUBMISSION LOGIC ---
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_grade'])) {
    $student_id = $_POST['student_id'];
    $course_name = $_POST['course_name'];
    $grade_value = $_POST['grade'];

    try {
        // Insert into the grades table
        $sql = "INSERT INTO grades (student_id, course_name, grade) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$student_id, $course_name, $grade_value])) {
            $message = "<div class='alert alert-success py-2 small shadow-sm'><i class='bi bi-check-circle me-2'></i>Grade submitted successfully!</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger py-2 small shadow-sm'>Error: " . $e->getMessage() . "</div>";
    }
}

// 3. Fetch data for the Stats
$res = $pdo->query("SELECT COUNT(*) FROM students");
$total_students = $res->fetchColumn();

// 4. Fetch students for the Overview Table and Form Dropdown
$query = "SELECT students.id, name, batch_id, completion_percentage 
          FROM students 
          LEFT JOIN progress ON students.id = progress.student_id 
          ORDER BY batch_id ASC";
$stmt = $pdo->query($query);
$students = $stmt->fetchAll();

// 5. Fetch unique courses for the dropdown
$course_query = "SELECT DISTINCT course_name FROM courses ORDER BY course_name ASC";
$courses = $pdo->query($course_query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Dashboard - Peak LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Sidebar Styling */
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
        .sidebar .nav-link:hover {
            color: #0d6efd;
        }

        /* Circular Progress Styling */
        .progress-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: radial-gradient(closest-side, white 79%, transparent 80% 100%),
                        conic-gradient(var(--progress-color) calc(var(--progress-value) * 1%), #e9ecef 0);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
        }
        .progress-circle::before {
            content: attr(data-value) '%';
            font-weight: bold;
            font-size: 1.2rem;
        }
        .progress-circle span {
            position: absolute;
            bottom: -20px;
            font-size: 0.75rem;
            color: #6c757d;
        }

        /* Header Profile Icon Styling */
        .profile-header-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            background-color: #004a99;
            color: white;
            border-radius: 50%;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        /* Search Input Styling */
        .search-container {
            position: relative;
            max-width: 300px;
        }
        .search-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .search-container input {
            padding-left: 35px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="px-4 mb-4">
        <h4 class="fw-bold text-primary">LMS Dashboard</h4>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link active" href="#"><i class="bi bi-grid-fill me-2"></i> dashboard</a>
        <a class="nav-link" href="#"><i class="bi bi-people me-2"></i> student</a>
        <a class="nav-link" href="#"><i class="bi bi-book me-2"></i> course</a>
        <a class="nav-link" href="#"><i class="bi bi-journal-check me-2"></i> grade</a>
        <a class="nav-link" href="#"><i class="bi bi-gear me-2"></i> setting</a>
        <a class="nav-link text-danger mt-3" href="../logout.php"><i class="bi bi-box-arrow-left me-2"></i> logout</a>
    </nav>
</div>

<div class="main-content">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Dashboard</h2>
            <p class="text-muted small">Welcome back, <?php echo $_SESSION['username']; ?>!</p>
        </div>
        
        <div class="d-flex align-items-center">
            <div class="text-muted me-3 small"><i class="bi bi-calendar3 me-1"></i> <?php echo date("F j, Y"); ?></div>
            <i class="bi bi-bell me-3 text-muted"></i>
            <div class="profile-header-icon shadow-sm" title="Instructor Profile">
                <?php echo strtoupper(substr($_SESSION['username'], 0, 2)); ?>
            </div>
        </div>
    </header>

    <div class="card border-0 shadow-sm p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2 text-primary"></i> Quick Grade Entry</h5>
        <?php echo $message; ?>
        <form action="" method="POST" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Select Student</label>
                <select name="student_id" class="form-select" required>
                    <option value="">Choose Student...</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['name']); ?> (Batch <?php echo $student['batch_id']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Course</label>
                <select name="course_name" class="form-select" required>
                    <option value="">Choose Course...</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo htmlspecialchars($course['course_name']); ?>">
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Grade (%)</label>
                <input type="number" name="grade" class="form-control" min="0" max="100" placeholder="0-100" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" name="submit_grade" class="btn btn-primary w-100 fw-bold shadow-sm">Submit Grade</button>
            </div>
        </form>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="fw-bold mb-0">Batch 1</h5>
                        <p class="text-muted small mb-0">24 students</p>
                    </div>
                    <div class="text-end">
                        <h2 class="fw-bold mb-0">78%</h2>
                        <span class="text-success small"><i class="bi bi-graph-up"></i> 5% vs last week</span>
                    </div>
                </div>
                <div class="progress-circle" data-value="78" style="--progress-color: #0d6efd; --progress-value: 78;">
                    <span>Complete</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="fw-bold mb-0">Batch 2</h5>
                        <p class="text-muted small mb-0">24 students</p>
                    </div>
                    <div class="text-end">
                        <h2 class="fw-bold mb-0">75%</h2>
                        <span class="text-danger small"><i class="bi bi-graph-down"></i> 2% vs last week</span>
                    </div>
                </div>
                <div class="progress-circle" data-value="75" style="--progress-color: #198754; --progress-value: 75;">
                    <span>Complete</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 d-flex flex-row justify-content-between align-items-center">
                <div><p class="text-muted mb-0 small fw-bold">Total Students</p><h3 class="mb-0"><?php echo $total_students; ?></h3></div>
                <i class="bi bi-people fs-3 text-primary"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 d-flex flex-row justify-content-between align-items-center">
                <div><p class="text-muted mb-0 small fw-bold">Active Courses</p><h3 class="mb-0"><?php echo count($courses); ?></h3></div>
                <i class="bi bi-book fs-3 text-success"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 d-flex flex-row justify-content-between align-items-center">
                <div><p class="text-muted mb-0 small fw-bold">Pending Grades</p><h3 class="mb-0">12</h3></div>
                <i class="bi bi-clipboard-check fs-3 text-warning"></i>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4 mb-4">
        <h5 class="fw-bold mb-4">Course Completion Progress</h5>
        <?php foreach (array_slice($courses, 0, 2) as $c): ?>
        <div class="mb-4">
            <div class="d-flex justify-content-between mb-2 small fw-semibold"><?php echo $c['course_name']; ?></div>
            <div class="progress" style="height: 8px; border-radius: 10px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 80%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card border-0 shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Student Progress Overview</h5>
            <div class="search-container">
                <i class="bi bi-search"></i>
                <input type="text" id="studentSearch" class="form-control" placeholder="Search by name or batch...">
            </div>
        </div>
        <table class="table align-middle" id="studentTable">
            <thead class="table-light">
                <tr><th>Student</th><th>Batch</th><th>Completion</th><th>Assessments</th></tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 11px;">
                                <?php echo strtoupper(substr($student['name'] ?? 'ST', 0, 2)); ?>
                            </div>
                            <span class="student-name small fw-semibold"><?php echo htmlspecialchars($student['name'] ?? 'Unknown'); ?></span>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-primary batch-label">Batch <?php echo $student['batch_id'] ?? 'N/A'; ?></span></td>
                    <td class="fw-bold"><?php echo $student['completion_percentage'] ?? 0; ?>%</td>
                    <td><i class="bi bi-check-circle text-success"></i> 8</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Live Search Logic
    document.getElementById('studentSearch').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#studentTable tbody tr');

        tableRows.forEach(row => {
            const studentName = row.querySelector('.student-name').textContent.toLowerCase();
            const batchLabel = row.querySelector('.batch-label').textContent.toLowerCase();
            
            if (studentName.includes(searchValue) || batchLabel.includes(searchValue)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>

</body>
</html>