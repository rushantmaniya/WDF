<?php
// Simple Database Connection for Practical 9
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "studenthub";

// 1. Connect to MySQL Server
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// 2. Automatically create the database if it doesn't exist
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db");

// 3. Select the database
mysqli_select_db($conn, $db);

// 4. Automatically create the 'users' table
// Note: password is VARCHAR(255) to store the secure hash from password_hash()
$table_setup = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    course VARCHAR(100) NOT NULL,
    year VARCHAR(50) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $table_setup);
?>
