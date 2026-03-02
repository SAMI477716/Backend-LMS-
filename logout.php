<?php
session_start();

// 1. Clear all session data (username, role, user_id)
session_unset();

// 2. Destroy the session entirely
session_destroy();

// 3. Send the user back to the starting role selection page
header("Location: index.php");
exit();
?>