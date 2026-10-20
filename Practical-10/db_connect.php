<?php
// 1. Database Connection Details
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "studenthub";

// 2. Connect to MySQL
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// 3. Create and Select Database
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db");
mysqli_select_db($conn, $db);

// 4. Create 'users' table
$table_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    mobile VARCHAR(15),
    password VARCHAR(255),
    course VARCHAR(100),
    year VARCHAR(50),
    gender VARCHAR(10),
    role VARCHAR(20) DEFAULT 'student'
)";
mysqli_query($conn, $table_sql);

// Ensure 'role' column exists
$check_role = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'role'");
if (mysqli_num_rows($check_role) == 0) {
    mysqli_query($conn, "ALTER TABLE users ADD role VARCHAR(20) DEFAULT 'student'");
}

// 5. Create default Admin user for easy testing
$check_admin = mysqli_query($conn, "SELECT * FROM users WHERE email = 'admin@gmail.com'");
if (mysqli_num_rows($check_admin) == 0) {
    $admin_pass = password_hash("admin123", PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (fullname, email, password, role) VALUES ('Admin User', 'admin@gmail.com', '$admin_pass', 'admin')");
}
?>
