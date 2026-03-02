<?php
include 'config/db_config.php';

if ($pdo) {
    echo "<h1 style='color: green;'>✔ Connection Successful!</h1>";
    echo "The Peak LMS is now talking to your database.";
} else {
    echo "<h1 style='color: red;'>✘ Connection Failed!</h1>";
}
?>