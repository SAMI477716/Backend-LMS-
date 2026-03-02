<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LMS Tracker - Peak Members</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .selection-card { width: 450px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .btn-role { border: 2px solid #001f3f; color: #001f3f; padding: 15px; margin-bottom: 15px; transition: 0.3s; font-weight: bold; }
        .btn-student { background-color: #001f3f; color: white; }
    </style>
</head>
<body>
    <div class="selection-card text-center">
        <h2 class="fw-bold">LMS Tracker for Peak Members</h2>
        <p class="text-muted mb-4">Peak Performance Learning</p>
        
        <a href="student_login.php" class="btn btn-dark w-100 mb-3" style="background-color: #001f3f;">
    <i class="bi bi-mortarboard me-2"></i> I'm a Student
</a>
        
        <a href="login.php?role=instructor" class="btn btn-role w-100 d-flex align-items-center justify-content-center">
             <i class="bi bi-briefcase me-2"></i> I'm an Instructor
         </a>
        
        <div class="mt-4 small text-muted">
            Peak Members Support &bull; Privacy
        </div>
    </div>
</body>
</html>