<?php
// 1. Start Session
session_start();

// 2. Restrict Access: Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html");
    exit();
}

// 3. Role-Based Check: Must be 'admin'
if ($_SESSION['user_role'] != 'admin') {
    echo "<div style='text-align:center; margin-top:50px; font-family:Arial;'>";
    echo "<h2 style='color:red;'>Access Denied!</h2>";
    echo "<p>You are a Student. Only Administrators can view this page.</p>";
    echo "<a href='dashboard.php'>Go to Student Dashboard</a> | <a href='logout.php'>Logout</a>";
    echo "</div>";
    exit();
}

// 4. Session Timeout: 10 minutes
if (time() - $_SESSION['last_time'] > 600) {
    session_destroy();
    header("Location: Login.html");
    exit();
}
$_SESSION['last_time'] = time();

// Fetch users from database
include 'db_connect.php';
$result = mysqli_query($conn, "SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentHub - Admin Panel</title>

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

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background: #003366;
    color: white;
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
    <p>Admin Control Panel</p>
</header>

<nav>
    <a href="Home.html">Home</a>
    <a href="admin.php">Admin Panel</a>
    <a href="manage_events.php" style="color: #003366; font-weight: bold;">Events Manager</a>
    <a href="students.php">Students Module</a>
    <a href="logout.php" style="color: red;">Logout</a>
</nav>

<main>

<section>

    <div class="top-bar">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (Admin)</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <p style="color: green; margin-bottom: 15px;">
        &bull; Role-Based Redirection: <b>Admin Access Granted</b>
    </p>

    <div style="margin: 20px 0; display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="manage_events.php" style="background:#003366; color:white; padding:10px 18px; text-decoration:none; border-radius:5px; font-weight:bold; display:inline-block;">
            📅 Open Event Management &amp; Poster Upload &rarr;
        </a>
        <a href="students.php" style="background:#2e7d32; color:white; padding:10px 18px; text-decoration:none; border-radius:5px; font-weight:bold; display:inline-block;">
            👨‍🎓 Open Student Records Management &rarr;
        </a>
    </div>

    <h3>Registered Users List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><b><?php echo htmlspecialchars($row['role']); ?></b></td>
        </tr>
        <?php endwhile; ?>
    </table>

</section>

</main>

<footer>
    <p>&copy; 2026 StudentHub Portal</p>
</footer>

</body>
</html>
