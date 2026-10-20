<?php
// ==================================================================
// Practical 13: PHP JSON API Endpoint for Students Module
// ==================================================================

// 1. Tell the browser we are returning JSON data
header('Content-Type: application/json');

// 2. Connect to MySQL Database
include 'db_connect.php';

// 3. Get requested action ('list', 'add', 'update', 'delete')
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

// ------------------------------------------------------------------
// ENDPOINT 1: LIST ALL STUDENTS (GET)
// ------------------------------------------------------------------
if ($action == 'list') {
    $result = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
    $students = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }

    // Return students as a JSON array
    echo json_encode($students);
    exit;
}

// ------------------------------------------------------------------
// ENDPOINT 2: ADD NEW STUDENT (POST)
// ------------------------------------------------------------------
if ($action == 'add') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $mobile   = trim($_POST['mobile'] ?? '');
    $course   = trim($_POST['course'] ?? '');
    $year     = trim($_POST['year'] ?? '');
    $gender   = trim($_POST['gender'] ?? '');

    // Validation
    if (empty($fullname) || empty($email) || empty($mobile) || empty($course)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
        exit;
    }

    $sql = "INSERT INTO students (fullname, email, mobile, course, year, gender) 
            VALUES ('$fullname', '$email', '$mobile', '$course', '$year', '$gender')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Student added successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit;
}

// ------------------------------------------------------------------
// ENDPOINT 3: UPDATE STUDENT (POST)
// ------------------------------------------------------------------
if ($action == 'update') {
    $id       = intval($_POST['id'] ?? 0);
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $mobile   = trim($_POST['mobile'] ?? '');
    $course   = trim($_POST['course'] ?? '');
    $year     = trim($_POST['year'] ?? '');
    $gender   = trim($_POST['gender'] ?? '');

    if ($id <= 0 || empty($fullname) || empty($email) || empty($mobile) || empty($course)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data or empty fields!']);
        exit;
    }

    $sql = "UPDATE students SET 
            fullname = '$fullname',
            email    = '$email',
            mobile   = '$mobile',
            course   = '$course',
            year     = '$year',
            gender   = '$gender'
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Student details updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit;
}

// ------------------------------------------------------------------
// ENDPOINT 4: DELETE STUDENT (GET or POST)
// ------------------------------------------------------------------
if ($action == 'delete') {
    $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid student ID!']);
        exit;
    }

    $sql = "DELETE FROM students WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Student record deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit;
}

// Default response if unknown action
echo json_encode(['status' => 'error', 'message' => 'Unknown action requested']);
exit;
?>
