<?php
// ==================================================================
// Practical 12: Database Connection & Event Table Setup
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

// 4. Create 'uploads' folder for posters if it doesn't exist
$upload_dir = __DIR__ . '/uploads';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 5. Create 'events' table for Event Management (Admin Module)
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

// 6. Pre-load sample events if empty (so testing is easy!)
$check_events = mysqli_query($conn, "SELECT COUNT(*) as count FROM events");
$event_row = mysqli_fetch_assoc($check_events);
if ($event_row['count'] == 0) {
    mysqli_query($conn, "INSERT INTO events (title, event_date, category, description, poster) VALUES
        ('Annual TechFest 2026', '2026-11-15', 'Technical', 'State-level college coding hackathon, web design competition, and robotics exhibition.', 'sample_poster1.png'),
        ('Cultural Beats 2026', '2026-12-05', 'Cultural', 'Annual music, classical dance, and drama performances by university students.', 'sample_poster2.png'),
        ('IoT Robotics Workshop', '2026-11-28', 'Workshop', 'Hands-on practical training on Arduino, Raspberry Pi, and Smart Automation sensors.', 'sample_poster3.png')
    ");
}

// 7. Keep 'users' table for Admin authentication
$table_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    mobile VARCHAR(15),
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'student'
)";
mysqli_query($conn, $table_users);

// Ensure default Admin user exists
$check_admin = mysqli_query($conn, "SELECT * FROM users WHERE email = 'admin@gmail.com'");
if (mysqli_num_rows($check_admin) == 0) {
    $admin_pass = password_hash("admin123", PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (fullname, email, password, role) VALUES ('Admin User', 'admin@gmail.com', '$admin_pass', 'admin')");
}
?>
