<?php
include 'config/db_config.php';

echo "<h2>Database Diagnostic</h2>";

try {
    // Check if the user exists and what their role is
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE username = 'sami'");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        echo "✅ User 'sami' found!<br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Role in DB: <strong>" . $user['role'] . "</strong><br>";
        
        if ($user['role'] !== 'student') {
            echo "❌ ERROR: Role must be 'student' for the login to work.";
        }
    } else {
        echo "❌ ERROR: User 'sami' does not exist in the 'users' table.";
    }

    // Check if there is progress data for this user
    if ($user) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM progress WHERE student_id = ?");
        $stmt->execute([$user['id']]);
        $count = $stmt->fetchColumn();
        echo "<br>Progress Records: " . $count;
    }

} catch (Exception $e) {
    echo "Connection Error: " . $e->getMessage();
}
?>