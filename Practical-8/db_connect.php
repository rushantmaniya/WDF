<?php
// Very simple database connection
$host = "127.0.0.1"; // Changed to 127.0.0.1 for Mac compatibility
$user = "root"; // Default username for XAMPP/MAMP
$pass = "";     // Default password is usually empty (or "root" on MAMP)
$db   = "studenthub"; // The name of your database

// 1. Connect to MySQL FIRST (without selecting a database yet)
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// 2. Magic trick: Automatically create the database if you haven't yet!
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db");

// 3. Now select that database
mysqli_select_db($conn, $db);

// 4. Automatically create the 'students' table so you don't get errors!
$table_setup = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    enrollment VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(100),
    department VARCHAR(100)
)";
mysqli_query($conn, $table_setup);
?>
