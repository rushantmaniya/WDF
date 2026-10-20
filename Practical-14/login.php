<?php
// 1. Start Session
session_start();

// 2. Connect to Database
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Get input from form
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 4. Find user by email in database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {

        // 5. Verify password using password_verify()
        if (password_verify($password, $row['password'])) {

            // 6. Security: Regenerate Session ID
            session_regenerate_id(true);

            // 7. Store user details in session
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $row['fullname'];
            $_SESSION['user_role'] = $row['role'];   // 'student' or 'admin'
            $_SESSION['last_time'] = time();         // For session timeout

            // 8. Optional: Remember-Me cookie (7 days)
            if (isset($_POST['remember'])) {
                setcookie("user_email", $email, time() + (7 * 24 * 60 * 60));
            }

            // 9. Role-Based Redirection
            if ($row['role'] == 'admin') {
                header("Location: admin.php");       // Send Admin to Admin page
            } else {
                header("Location: dashboard.php");   // Send Student to Dashboard
            }
            exit();

        } else {
            echo "<div style='text-align:center; margin-top:50px;'>";
            echo "<h3 style='color:red;'>Error: Wrong Password!</h3>";
            echo "<a href='Login.html'>Go Back to Login</a>";
            echo "</div>";
        }

    } else {
        echo "<div style='text-align:center; margin-top:50px;'>";
        echo "<h3 style='color:red;'>Error: User not found with this email!</h3>";
        echo "<a href='Login.html'>Go Back to Login</a>";
        echo "</div>";
    }

} else {
    header("Location: Login.html");
    exit();
}
?>
