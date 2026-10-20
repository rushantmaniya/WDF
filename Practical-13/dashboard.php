<?php
// 1. Start Session
session_start();

// 2. Restrict Access: User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html");
    exit();
}

// 3. Session Timeout: Auto logout after 10 minutes of inactivity
if (time() - $_SESSION['last_time'] > 600) {
    session_destroy();
    header("Location: Login.html");
    exit();
}
$_SESSION['last_time'] = time(); // Reset active time
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentHub - Student Dashboard</title>

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
    width: 80%;
    margin: 30px auto;
}

section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 0 10px #ccc;
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.logout-btn {
    background: red;
    color: white;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}

.cards {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.card {
    width: 30%;
    padding: 20px;
    margin: 10px 0;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 0 5px gray;
    background: white;
}

footer {
    text-align: center;
    padding: 15px;
    margin-top: 30px;
}
</style>
</head>

<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Student Dashboard</p>
</header>

<nav>
    <a href="Home.html">Home</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="logout.php" style="color: red;">Logout</a>
</nav>

<main>

<section>

    <div class="top-bar">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (Student)</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <p style="color: green; margin-bottom: 20px;">
        &bull; You are logged in securely using PHP Sessions.<br>
        &bull; Session ID: <code><?php echo session_id(); ?></code>
    </p>

    <div class="cards">
        <div class="card">
            <h3>Attendance</h3>
            <p>View your attendance record.</p>
        </div>
        <div class="card">
            <h3>Assignments</h3>
            <p>Check pending assignments.</p>
        </div>
        <div class="card">
            <h3>Results</h3>
            <p>View examination results.</p>
        </div>
    </div>

</section>

</main>

<footer>
    <p>&copy; 2026 StudentHub Portal</p>
</footer>

</body>
</html>
