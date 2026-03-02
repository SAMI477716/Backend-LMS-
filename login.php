<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Peak LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 400px; border-radius: 15px;">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Instructor Login</h3>
                <p class="text-muted">Enter your credentials to access the dashboard</p>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
    <div class="alert alert-danger py-2 text-center" style="font-size: 14px; border-radius: 10px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> 
        Invalid username or password.
    </div>
  <?php endif; ?>
            
            <form action="login_process.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold" style="background-color: #001f3f;">Login</button>
            </form>
            
            <div class="mt-3 text-center">
                <a href="index.php" class="text-decoration-none small">← Back to Role Selection</a>
            </div>
        </div>
    </div>
</body>
</html>