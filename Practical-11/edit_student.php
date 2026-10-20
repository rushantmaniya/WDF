<?php
// ==================================================================
// Practical 11: Edit Student Record
// ==================================================================

include 'db_connect.php';

$error = "";

// 1. Handle Form Update Submission (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id       = intval($_POST['id']);
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $mobile   = trim($_POST['mobile']);
    $course   = trim($_POST['course']);
    $year     = trim($_POST['year']);
    $gender   = trim($_POST['gender']);

    if (empty($fullname) || empty($email) || empty($mobile) || empty($course)) {
        $error = "All fields are required.";
    } else {
        // Update MySQL record
        $update_sql = "UPDATE students SET 
            fullname = '$fullname',
            email    = '$email',
            mobile   = '$mobile',
            course   = '$course',
            year     = '$year',
            gender   = '$gender'
            WHERE id = $id";

        if (mysqli_query($conn, $update_sql)) {
            header("Location: students.php?msg=updated");
            exit();
        } else {
            $error = "Error updating record: " . mysqli_error($conn);
        }
    }
}

// 2. Fetch existing student details by ID (GET)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM students WHERE id = $id");
    $student = mysqli_fetch_assoc($result);

    if (!$student) {
        die("Student record not found! <a href='students.php'>Go Back</a>");
    }
} else if (!isset($_POST['id'])) {
    header("Location: students.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Student - Practical 11</title>

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

main {
    width: 45%;
    min-width: 320px;
    margin: 30px auto;
}

.box {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

h2 {
    color: #003366;
    margin-bottom: 20px;
    text-align: center;
}

label {
    font-weight: bold;
    display: block;
    margin-top: 12px;
    margin-bottom: 5px;
}

input[type="text"],
input[type="email"],
input[type="tel"],
select {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}

.btn-save {
    background: #003366;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 15px;
    margin-top: 20px;
}

.btn-cancel {
    background: #777;
    color: white;
    padding: 10px 18px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
    margin-left: 10px;
}

.alert-error {
    background: #ffebee;
    color: #c62828;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}
</style>
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Edit Student Record</p>
</header>

<main>

    <div class="box">
        <h2>✏️ Edit Student Details</h2>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="edit_student.php" method="POST">
            <!-- Hidden ID field -->
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

            <label>Full Name</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($student['fullname']); ?>" required>

            <label>Email Address</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>

            <label>Mobile Number</label>
            <input type="tel" name="mobile" maxlength="10" value="<?php echo htmlspecialchars($student['mobile']); ?>" required>

            <label>Course / Department</label>
            <select name="course" required>
                <option value="Computer Engineering" <?php if ($student['course'] == 'Computer Engineering') echo 'selected'; ?>>Computer Engineering</option>
                <option value="Information Technology" <?php if ($student['course'] == 'Information Technology') echo 'selected'; ?>>Information Technology</option>
                <option value="Mechanical Engineering" <?php if ($student['course'] == 'Mechanical Engineering') echo 'selected'; ?>>Mechanical Engineering</option>
                <option value="Civil Engineering" <?php if ($student['course'] == 'Civil Engineering') echo 'selected'; ?>>Civil Engineering</option>
            </select>

            <label>Year</label>
            <select name="year" required>
                <option value="1st Year" <?php if ($student['year'] == '1st Year') echo 'selected'; ?>>1st Year</option>
                <option value="2nd Year" <?php if ($student['year'] == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                <option value="3rd Year" <?php if ($student['year'] == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                <option value="4th Year" <?php if ($student['year'] == '4th Year') echo 'selected'; ?>>4th Year</option>
            </select>

            <label>Gender</label>
            <select name="gender" required>
                <option value="Male" <?php if ($student['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($student['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if ($student['gender'] == 'Other') echo 'selected'; ?>>Other</option>
            </select>

            <div style="margin-top: 25px;">
                <button type="submit" class="btn-save">Update Record</button>
                <a href="students.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

</main>

</body>
</html>
