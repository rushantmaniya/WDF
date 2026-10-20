<?php
// ==================================================================
// Practical 14: Restricted Admin Dashboard with Statistics & Role-Based Menu
// ==================================================================

// 1. Start Session
session_start();

// 2. RESTRICT ACCESS: Must be logged in!
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html");
    exit();
}

// 3. RESTRICT ACCESS: Must be an ADMIN!
$user_role = $_SESSION['user_role'] ?? 'student';
if ($user_role !== 'admin') {
    echo "<div style='text-align:center; margin-top:50px; font-family:Arial;'>";
    echo "<h2 style='color:red;'>Access Denied: Administrator Only!</h2>";
    echo "<p>You are logged in as a <b>Student</b>. You do not have permission to view the Admin Dashboard.</p>";
    echo "<a href='dashboard.php' style='color:#003366; font-weight:bold;'>Go to Student Dashboard</a> | <a href='logout.php'>Logout</a>";
    echo "</div>";
    exit();
}

// 4. Session Timeout: 10 minutes of inactivity
if (isset($_SESSION['last_time']) && (time() - $_SESSION['last_time'] > 600)) {
    session_destroy();
    header("Location: Login.html");
    exit();
}
$_SESSION['last_time'] = time();

// 5. Connect to Database
include 'db_connect.php';

// 6. FETCH USER STATISTICS, STUDENT COUNT, & EVENT COUNT
// A. Total Registered Users
$res_users = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$total_users = mysqli_fetch_assoc($res_users)['count'];

// B. Total Student Records
$res_students = mysqli_query($conn, "SELECT COUNT(*) as count FROM students");
$total_students = mysqli_fetch_assoc($res_students)['count'];

// C. Total Campus Events
$res_events = mysqli_query($conn, "SELECT COUNT(*) as count FROM events");
$total_events = mysqli_fetch_assoc($res_events)['count'];

// D. Total Admin Users
$res_admins = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
$total_admins = mysqli_fetch_assoc($res_admins)['count'];

// 7. FETCH LATEST REGISTRATIONS (Last 5 users)
$latest_sql = "SELECT id, fullname, email, mobile, role, created_at FROM users ORDER BY id DESC LIMIT 5";
$latest_users = mysqli_query($conn, $latest_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Practical 14</title>

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

/* DYNAMIC ROLE-BASED NAVIGATION BAR */
nav {
    text-align: center;
    padding: 12px;
    background: #e6ecf2;
    border-bottom: 1px solid #ccc;
}

nav a {
    text-decoration: none;
    margin: 0 10px;
    color: #003366;
    font-weight: bold;
    font-size: 14px;
}

nav a.active {
    text-decoration: underline;
}

main {
    width: 85%;
    max-width: 1100px;
    margin: 30px auto;
}

.box {
    background: white;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 25px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 12px;
}

.badge-admin {
    background: #d32f2f;
    color: white;
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 12px;
    vertical-align: middle;
    margin-left: 8px;
}

.btn-logout {
    background: #c62828;
    color: white;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    font-size: 14px;
}

/* STATISTICS CARDS GRID */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    border-top: 4px solid #003366;
    text-align: center;
}

.stat-card.green  { border-top-color: #2e7d32; }
.stat-card.orange { border-top-color: #f57c00; }
.stat-card.purple { border-top-color: #7b1fa2; }

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #003366;
    margin: 8px 0;
}

.stat-card.green .stat-number  { color: #2e7d32; }
.stat-card.orange .stat-number { color: #f57c00; }
.stat-card.purple .stat-number { color: #7b1fa2; }

.stat-label {
    font-size: 14px;
    color: #666;
    font-weight: bold;
}

/* QUICK ACTION BUTTONS */
.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 15px;
}

.btn-action {
    background: #003366;
    color: white;
    padding: 10px 18px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    font-size: 13px;
    display: inline-block;
}

.btn-action.green { background: #2e7d32; }
.btn-action.purple { background: #512da8; }

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    font-size: 14px;
}

th {
    background: #003366;
    color: white;
}

tr:hover {
    background: #f9f9f9;
}

.role-tag {
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: bold;
}

.role-admin { background: #ffebee; color: #c62828; }
.role-student { background: #e3f2fd; color: #0d47a1; }

footer {
    text-align: center;
    padding: 20px;
    margin-top: 30px;
    color: #666;
}
</style>
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Practical 14: Administrator Statistics &amp; Role-Based Dashboard</p>
</header>

<!-- 8. DYNAMIC MENU BASED ON ROLE -->
<nav>
    <a href="Home.html">Home</a>

    <?php if ($user_role === 'admin'): ?>
        <!-- DYNAMIC ADMIN MENU OPTIONS -->
        <a href="admin.php" class="active">Admin Dashboard</a>
        <a href="students.php">Manage Students</a>
        <a href="manage_events.php">Manage Events</a>
        <a href="dashboard.php">Switch to Student View</a>
        <a href="logout.php" style="color: red;">Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a>
    <?php else: ?>
        <!-- DYNAMIC STUDENT MENU OPTIONS -->
        <a href="dashboard.php">My Dashboard</a>
        <a href="Profile.html">Profile</a>
        <a href="Events.html">Events</a>
        <a href="logout.php" style="color: red;">Logout</a>
    <?php endif; ?>
</nav>

<main>

    <!-- Top Banner with Admin Info -->
    <div class="box">
        <div class="top-bar">
            <div>
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> <span class="badge-admin">Admin Privileges</span></h2>
                <p style="color: #666; font-size: 13px; margin-top: 4px;">
                    Role: <b><?php echo strtoupper($user_role); ?></b> &bull; Restricted Access Verified
                </p>
            </div>
            <div>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>

        <p style="color: green; font-size: 14px;">
            &bull; Access Restricted: Only users with <code>role = 'admin'</code> can access this page.<br>
            &bull; Navigation Bar above shows <b>Dynamic Menu Options</b> based on your logged-in role.
        </p>
    </div>

    <!-- 9. USER STATISTICS, STUDENT COUNT, & EVENT COUNT -->
    <h3 style="color: #003366; margin-bottom: 12px;">📊 System Statistics Overview</h3>
    <div class="stats-grid">
        
        <div class="stat-card">
            <div class="stat-label">TOTAL REGISTERED USERS</div>
            <div class="stat-number"><?php echo $total_users; ?></div>
            <small style="color: #777;">Accounts in database</small>
        </div>

        <div class="stat-card green">
            <div class="stat-label">TOTAL STUDENTS COUNT</div>
            <div class="stat-number"><?php echo $total_students; ?></div>
            <small style="color: #777;">Active student records</small>
        </div>

        <div class="stat-card orange">
            <div class="stat-label">TOTAL EVENTS COUNT</div>
            <div class="stat-number"><?php echo $total_events; ?></div>
            <small style="color: #777;">Published campus events</small>
        </div>

        <div class="stat-card purple">
            <div class="stat-label">ADMINISTRATORS</div>
            <div class="stat-number"><?php echo $total_admins; ?></div>
            <small style="color: #777;">Admin role accounts</small>
        </div>

    </div>

    <!-- 10. QUICK ADMIN ACTIONS -->
    <div class="box">
        <h3>⚡ Quick Admin Controls</h3>
        <div class="action-buttons">
            <a href="students.php" class="btn-action green">👨‍🎓 Manage Student Records &rarr;</a>
            <a href="manage_events.php" class="btn-action">📅 Manage Campus Events &rarr;</a>
            <a href="students_api.html" class="btn-action purple">⚡ Fetch API Live Manager &rarr;</a>
        </div>
    </div>

    <!-- 11. LATEST REGISTRATIONS TABLE -->
    <div class="box">
        <h3>🕒 Latest User Registrations</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Role</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($latest_users) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($latest_users)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><b><?php echo htmlspecialchars($row['fullname']); ?></b></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                            <td>
                                <span class="role-tag <?php echo $row['role'] === 'admin' ? 'role-admin' : 'role-student'; ?>">
                                    <?php echo strtoupper($row['role']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #888; padding: 20px;">
                            No recent registrations found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<footer>
    <p>&copy; 2026 StudentHub Portal | Practical 14 - Admin Dashboard &amp; Role-Based Menu</p>
</footer>

</body>
</html>
