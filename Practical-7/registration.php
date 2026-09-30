<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $fullname = htmlspecialchars(trim($_POST["fullname"] ?? ''));
    $enrollment = htmlspecialchars(trim($_POST["enrollment"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars(trim($_POST["password"] ?? ''));
    $department = htmlspecialchars(trim($_POST["department"] ?? ''));

    // Validate inputs
    if (empty($fullname) || empty($enrollment) || empty($email) || empty($password) || empty($department)) {
        echo "<h3 style='color:red;'>Error: All fields are required.</h3> <a href='Register.html'>Go Back</a>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<h3 style='color:red;'>Error: Invalid email format.</h3> <a href='Register.html'>Go Back</a>";
    } else {
        // Store data in CSV format
        $file = fopen('registrations.csv', 'a');
        if ($file) {
            fputcsv($file, [$fullname, $enrollment, $email, password_hash($password, PASSWORD_DEFAULT), $department]);
            fclose($file);
            echo "<h3 style='color:green;'>Success: Registration complete.</h3> <a href='Register.html'>Go Back</a>";
        } else {
            echo "<h3 style='color:red;'>Error: Could not open file for writing.</h3> <a href='Register.html'>Go Back</a>";
        }
    }
} else {
    echo "<h3 style='color:red;'>Invalid request method.</h3> <a href='Register.html'>Go Back</a>";
}
?>
