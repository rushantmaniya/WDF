<?php
// 1. Start session
session_start();

// 2. Destroy session completely
session_destroy();

// 3. Delete remember-me cookie if present
if (isset($_COOKIE['user_email'])) {
    setcookie("user_email", "", time() - 3600);
}

// 4. Redirect back to login page
header("Location: Login.html");
exit();
?>
