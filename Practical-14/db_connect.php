<?php
// ==================================================================
// Practical 14: Database Connection for Events & Students JSON API
// ==================================================================

// 1. Database Connection Details
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "studenthub";

// 2. Connect to MySQL Server
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// 3. Create & Select Database
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db");
mysqli_select_db($conn, $db);

// 4. Create uploads folder for event posters
$upload_dir = __DIR__ . '/uploads';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 5. Create 'events' table
$table_events = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    poster VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $table_events);

// Pre-load sample events if empty
$check_events = mysqli_query($conn, "SELECT COUNT(*) as count FROM events");
$event_row = mysqli_fetch_assoc($check_events);
if ($event_row['count'] == 0) {
    mysqli_query($conn, "INSERT INTO events (title, event_date, category, description, poster) VALUES
        ('Annual TechFest 2026', '2026-11-15', 'Technical', 'State-level coding hackathon and robotics exhibition.', 'sample_poster1.png'),
        ('Cultural Beats 2026', '2026-12-05', 'Cultural', 'Annual music, classical dance, and drama performances.', 'sample_poster2.png'),
        ('IoT Robotics Workshop', '2026-11-28', 'Workshop', 'Hands-on practical training on Arduino and IoT sensors.', 'sample_poster3.png')
    ");
}

// 6. Create 'students' table
$table_students = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    course VARCHAR(100) NOT NULL,
    year VARCHAR(50) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $table_students);

// Pre-load sample students if empty
$check_students = mysqli_query($conn, "SELECT COUNT(*) as count FROM students");
$student_row = mysqli_fetch_assoc($check_students);
if ($student_row['count'] == 0) {
    mysqli_query($conn, "INSERT INTO students (fullname, email, mobile, course, year, gender) VALUES
        ('Rahul Sharma', 'rahul@gmail.com', '9876543210', 'Computer Engineering', '3rd Year', 'Male'),
        ('Priya Patel', 'priya@gmail.com', '9876543211', 'Information Technology', '2nd Year', 'Female'),
        ('Amit Verma', 'amit@gmail.com', '9876543212', 'Mechanical Engineering', '4th Year', 'Male')
    ");
}

// 7. Users table
$table_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    mobile VARCHAR(15),
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'student'
)";
mysqli_query($conn, $table_users);
?>
