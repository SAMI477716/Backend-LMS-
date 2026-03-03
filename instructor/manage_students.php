<?php
session_start();
include_once '../config/db_config.php';

// Security: RBAC check - Only instructors can access this governance tool
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../login.php");
    exit();
}

// Handle Grade/Progress Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_progress'])) {
    $student_id = $_POST['student_id'];
    $new_perc = $_POST['progress'];
    
    $update = $pdo->prepare("UPDATE progress SET completion_percentage = ? WHERE student_id = ?");
    $update->execute([$new_perc, $student_id]);
    $msg = "Progress updated successfully!";
}

// Fetch all students and their progress (Student Tracking Objective)
$query = "SELECT u.id, u.username, u.batch, p.completion_percentage 
          FROM users u 
          LEFT JOIN progress p ON u.id = p.student_id 
          WHERE u.role = 'student'";
$students = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Skill Developer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="fw-bold">Centralized Student Governance</h2>
    <p class="text-muted">Manage batches and track academic performance.</p>

    <?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Batch</th>
                        <th>Current Progress</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?php echo $s['id']; ?></td>
                        <td><?php echo htmlspecialchars($s['username']); ?></td>
                        <td><span class="badge bg-info"><?php echo $s['batch'] ?? 'Batch 1'; ?></span></td>
                        <td><?php echo $s['completion_percentage'] ?? 0; ?>%</td>
                        <td>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="student_id" value="<?php echo $s['id']; ?>">
                                <input type="number" name="progress" class="form-control form-control-sm" style="width: 80px;" min="0" max="100">
                                <button type="submit" name="update_progress" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>