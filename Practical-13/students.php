<?php
// ==================================================================
// Practical 11: Student Management Module (Add, View, Edit, Delete, Search & Filter)
// ==================================================================

// 1. Connect to Database
include 'db_connect.php';

$message = "";
$error = "";

// 2. Handle ADD Student Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $mobile   = trim($_POST['mobile']);
    $course   = trim($_POST['course']);
    $year     = trim($_POST['year']);
    $gender   = trim($_POST['gender']);

    // Check empty fields
    if (empty($fullname) || empty($email) || empty($mobile) || empty($course) || empty($year) || empty($gender)) {
        $error = "Please fill in all fields.";
    } else {
        // Insert new student into MySQL
        $insert_sql = "INSERT INTO students (fullname, email, mobile, course, year, gender) 
                       VALUES ('$fullname', '$email', '$mobile', '$course', '$year', '$gender')";
        
        if (mysqli_query($conn, $insert_sql)) {
            header("Location: students.php?msg=added");
            exit();
        } else {
            $error = "Error adding student: " . mysqli_error($conn);
        }
    }
}

// 3. Handle Status Messages from URL (Success / Error)
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added')   $message = "Student added successfully!";
    if ($_GET['msg'] == 'updated') $message = "Student details updated successfully!";
    if ($_GET['msg'] == 'deleted') $message = "Student record deleted successfully!";
}
if (isset($_GET['err'])) {
    $error = $_GET['err'];
}

// 4. Handle SEARCH and FILTER
$search_query   = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_course  = isset($_GET['course']) ? trim($_GET['course']) : '';

// Build SQL query dynamically based on search & filter
$sql = "SELECT * FROM students WHERE 1=1";

if (!empty($search_query)) {
    $sql .= " AND (fullname LIKE '%$search_query%' OR email LIKE '%$search_query%' OR mobile LIKE '%$search_query%')";
}

if (!empty($filter_course)) {
    $sql .= " AND course = '$filter_course'";
}

$sql .= " ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$student_count = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Management Module - Practical 11</title>

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
    background: #e6ecf2;
}

nav a {
    text-decoration: none;
    margin: 12px;
    color: #003366;
    font-weight: bold;
}

main {
    width: 90%;
    max-width: 1100px;
    margin: 30px auto;
}

.box {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

h2, h3 {
    color: #003366;
    margin-bottom: 15px;
}

/* Alert Messages */
.alert-success {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
    border: 1px solid #c8e6c9;
    font-weight: bold;
    text-align: center;
}

.alert-error {
    background: #ffebee;
    color: #c62828;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
    border: 1px solid #ffcdd2;
    font-weight: bold;
    text-align: center;
}

/* Form Styling */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
    font-size: 13px;
}

input[type="text"],
input[type="email"],
input[type="tel"],
select {
    width: 100%;
    padding: 9px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}

.btn-add {
    background: #003366;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

.btn-add:hover {
    background: #002244;
}

/* Search & Filter Bar */
.filter-bar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
    background: #eef3f8;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.filter-bar input[type="text"] {
    flex: 2;
    min-width: 200px;
}

.filter-bar select {
    flex: 1;
    min-width: 180px;
}

.btn-search {
    background: #2e7d32;
    color: white;
    padding: 9px 18px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

.btn-reset {
    background: #666;
    color: white;
    padding: 9px 15px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}

/* Student Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    padding: 11px;
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

.btn-edit {
    background: #1976d2;
    color: white;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
    margin-right: 5px;
}

.btn-delete {
    background: #d32f2f;
    color: white;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
}

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
    <p>Practical 11: Student Records Management (CRUD + Search &amp; Filter)</p>
</header>

<nav>
    <a href="index.html">Home</a>
    <a href="students.php" style="color: #003366; text-decoration: underline;">Students</a>
    <a href="register.html">Register</a>
    <a href="Login.html">Login</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="admin.php">Admin Panel</a>
</nav>

<main>

    <!-- Success / Error Feedback Messages -->
    <?php if (!empty($message)): ?>
        <div class="alert-success">&#10004; <?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert-error">&#10008; <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- 1. ADD STUDENT MODULE -->
    <div class="box">
        <h3>➕ Add New Student Record</h3>
        <form action="students.php" method="POST">
            <div class="form-grid">
                <div>
                    <label>Full Name</label>
                    <input type="text" name="fullname" placeholder="Student name" required>
                </div>
                <div>
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="student@gmail.com" required>
                </div>
                <div>
                    <label>Mobile Number</label>
                    <input type="tel" name="mobile" maxlength="10" placeholder="10 digits" required>
                </div>
                <div>
                    <label>Course / Department</label>
                    <select name="course" required>
                        <option value="">-- Select Course --</option>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                        <option value="Civil Engineering">Civil Engineering</option>
                    </select>
                </div>
                <div>
                    <label>Year</label>
                    <select name="year" required>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>
                </div>
                <div>
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="add_student" class="btn-add">Save Student</button>
        </form>
    </div>

    <!-- 2. SEARCH & FILTER MODULE -->
    <div class="box">
        <h3>🔍 Search &amp; Filter Students</h3>
        <form action="students.php" method="GET" class="filter-bar">
            
            <input type="text" name="search" placeholder="Search by name, email or mobile..." value="<?php echo htmlspecialchars($search_query); ?>">
            
            <select name="course">
                <option value="">All Departments</option>
                <option value="Computer Engineering" <?php if ($filter_course == 'Computer Engineering') echo 'selected'; ?>>Computer Engineering</option>
                <option value="Information Technology" <?php if ($filter_course == 'Information Technology') echo 'selected'; ?>>Information Technology</option>
                <option value="Mechanical Engineering" <?php if ($filter_course == 'Mechanical Engineering') echo 'selected'; ?>>Mechanical Engineering</option>
                <option value="Civil Engineering" <?php if ($filter_course == 'Civil Engineering') echo 'selected'; ?>>Civil Engineering</option>
            </select>
            
            <button type="submit" class="btn-search">Search / Filter</button>
            <a href="students.php" class="btn-reset">Reset</a>
        </form>

        <!-- 3. VIEW STUDENTS TABLE -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h4>Student Records (Total: <?php echo $student_count; ?>)</h4>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($student_count > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><b><?php echo htmlspecialchars($row['fullname']); ?></b></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                            <td><?php echo htmlspecialchars($row['course']); ?></td>
                            <td><?php echo htmlspecialchars($row['year']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td>
                                <!-- EDIT button -->
                                <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                <!-- DELETE button -->
                                <a href="delete_student.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this student record?');" class="btn-delete">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #888; padding: 25px;">
                            No student records found matching your search.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<footer>
    <p>&copy; 2026 StudentHub Portal | Practical 11</p>
</footer>

</body>
</html>
