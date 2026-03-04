<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Account - Peak LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" style="width: 420px; border-radius: 15px;">
            <h4 class="text-center fw-bold mb-4">Create Account</h4>
            <form action="register_process.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="e.g. Samuel Z" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Role</label>
                    <select name="role" class="form-select">
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Batch</label>
                    <select name="batch" class="form-select">
                        <option value="Batch 1">Batch 1</option>
                        <option value="Batch 2">Batch 2</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold mb-3">Register Now</button>
            </form>

            <div class="text-center border-top pt-3">
    <p class="small text-muted mb-1">Already have an account?</p>
    <div class="d-grid gap-2">
        <a href="student_login.php" class="btn btn-outline-secondary btn-sm">Click here for Student Login</a>
        <a href="login.php" class="btn btn-outline-dark btn-sm">Click here for Instructor Login</a>
       </div>
        </div>
        </div>
    </div>
</body>
</html>