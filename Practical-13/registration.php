<?php
// ==================================================================
// Practical 9: Secure User Registration
// ==================================================================

// 1. Include database connection
include 'db_connect.php';

// Variable to store error or success messages
$error_message = "";
$success_message = "";

// 2. Only process when the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Receive and sanitize inputs from the HTML form
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $mobile   = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $course   = trim($_POST['course'] ?? '');
    $year     = trim($_POST['year'] ?? '');
    $gender   = trim($_POST['gender'] ?? '');

    // 4. Backend Validation (Never trust frontend alone!)
    if (empty($fullname) || empty($email) || empty($mobile) || empty($password) || empty($confirm) || empty($course) || empty($year) || empty($gender)) {
        $error_message = "All fields are required. Please fill in all details.";
    } elseif (!preg_match("/^[A-Za-z ]+$/", $fullname)) {
        $error_message = "Full Name should contain letters and spaces only.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $error_message = "Mobile number must be exactly 10 digits.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm) {
        $error_message = "Passwords do not match.";
    } else {

        // 5. Check for Duplicate Email/Username (Security check)
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error_message = "This email is already registered! Please use a different email or log in.";
        } else {

            // 6. Securely Hash the Password using password_hash()
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 7. Insert user details using a Prepared Statement (Prevents SQL Injection)
            $insert_sql = "INSERT INTO users (fullname, email, mobile, password, course, year, gender) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "sssssss", $fullname, $email, $mobile, $hashed_password, $course, $year, $gender);

            if (mysqli_stmt_execute($insert_stmt)) {
                $success_message = "Registration successful! Your details have been securely saved.";
            } else {
                $error_message = "Database error: Unable to complete registration. " . mysqli_error($conn);
            }

            mysqli_stmt_close($insert_stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
} else {
    $error_message = "Invalid request method.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status - StudentHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: #f2f2f2;
        }
        header {
            background: #003366;
            color: white;
            text-align: center;
            padding: 20px;
        }
        nav {
            text-align: center;
            padding: 12px;
        }
        nav a {
            text-decoration: none;
            margin: 12px;
            color: #003366;
            font-weight: bold;
        }
        main {
            width: 45%;
            margin: 40px auto;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success {
            color: #2e7d32;
        }
        .error {
            color: #c62828;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            margin-right: 10px;
            padding: 10px 20px;
            background: #003366;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-secondary {
            background: #666;
        }
        footer {
            text-align: center;
            padding: 15px;
            margin-top: 30px;
            color: #555;
        }
    </style>
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Registration Status</p>
</header>

<nav>
    <a href="Home.html">Home</a>
    <a href="register.html">Register</a>
    <a href="Login.html">Login</a>
</nav>

<main>
    <div class="card">
        <?php if (!empty($success_message)): ?>
            <h2 class="success">&#10004; Success</h2>
            <p style="margin-top: 15px; font-size: 16px;"><?php echo htmlspecialchars($success_message); ?></p>
            <div style="margin-top: 25px;">
                <a href="Login.html" class="btn">Go to Login</a>
                <a href="Home.html" class="btn btn-secondary">Go to Home</a>
            </div>
        <?php else: ?>
            <h2 class="error">&#10008; Registration Failed</h2>
            <p style="margin-top: 15px; font-size: 16px; color: #555;"><?php echo htmlspecialchars($error_message); ?></p>
            <div style="margin-top: 25px;">
                <a href="register.html" class="btn">Try Again</a>
                <a href="Login.html" class="btn btn-secondary">Login</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2026 StudentHub Portal</p>
</footer>

</body>
</html>
