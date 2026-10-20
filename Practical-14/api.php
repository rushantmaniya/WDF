<?php
// ==================================================================
// Practical 14: PHP JSON API Endpoint for Events & Students
// ==================================================================

// 1. Tell browser this is a JSON API
header('Content-Type: application/json');

// 2. Connect to MySQL Database
include 'db_connect.php';

// 3. Get requested action and data type ('events' or 'students')
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$type   = $_GET['type'] ?? $_POST['type'] ?? 'events'; // default is events for Practical 14

// ==================================================================
// SECTION A: EVENTS API ENDPOINTS
// ==================================================================
if ($type == 'events') {

    // 1. LIST EVENTS (GET)
    if ($action == 'list') {
        $result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
        $events = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $events[] = $row;
        }
        echo json_encode($events);
        exit;
    }

    // 2. ADD EVENT (POST with optional file upload)
    if ($action == 'add') {
        $title       = trim($_POST['title'] ?? '');
        $event_date  = $_POST['event_date'] ?? '';
        $category    = $_POST['category'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if (empty($title) || empty($event_date) || empty($category)) {
            echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields!']);
            exit;
        }

        // Handle poster upload (or use default placeholder)
        $poster_name = 'sample_poster1.png';
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $poster_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES['poster']['name']);
                move_uploaded_file($_FILES['poster']['tmp_name'], 'uploads/' . $poster_name);
            }
        }

        $sql = "INSERT INTO events (title, event_date, category, description, poster) 
                VALUES ('$title', '$event_date', '$category', '$description', '$poster_name')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Event published successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }

    // 3. UPDATE EVENT (POST)
    if ($action == 'update') {
        $id          = intval($_POST['id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $event_date  = $_POST['event_date'] ?? '';
        $category    = $_POST['category'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if ($id <= 0 || empty($title) || empty($event_date) || empty($category)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data or empty fields!']);
            exit;
        }

        $poster_sql = "";
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $new_poster = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES['poster']['name']);
                move_uploaded_file($_FILES['poster']['tmp_name'], 'uploads/' . $new_poster);
                $poster_sql = ", poster='$new_poster'";
            }
        }

        $sql = "UPDATE events SET 
                title       = '$title',
                event_date  = '$event_date',
                category    = '$category',
                description = '$description' 
                $poster_sql 
                WHERE id    = $id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Event updated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }

    // 4. DELETE EVENT (GET or POST)
    if ($action == 'delete') {
        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Event ID!']);
            exit;
        }

        $sql = "DELETE FROM events WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }
}

// ==================================================================
// SECTION B: STUDENTS API ENDPOINTS (Also supported!)
// ==================================================================
if ($type == 'students') {
    if ($action == 'list') {
        $result = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        echo json_encode($students);
        exit;
    }

    if ($action == 'add') {
        $fullname = trim($_POST['fullname'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $mobile   = trim($_POST['mobile'] ?? '');
        $course   = trim($_POST['course'] ?? '');
        $year     = trim($_POST['year'] ?? '');
        $gender   = trim($_POST['gender'] ?? '');

        $sql = "INSERT INTO students (fullname, email, mobile, course, year, gender) VALUES ('$fullname', '$email', '$mobile', '$course', '$year', '$gender')";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Student added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }

    if ($action == 'update') {
        $id       = intval($_POST['id'] ?? 0);
        $fullname = trim($_POST['fullname'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $mobile   = trim($_POST['mobile'] ?? '');
        $course   = trim($_POST['course'] ?? '');
        $year     = trim($_POST['year'] ?? '');
        $gender   = trim($_POST['gender'] ?? '');

        $sql = "UPDATE students SET fullname='$fullname', email='$email', mobile='$mobile', course='$course', year='$year', gender='$gender' WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Student updated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }

    if ($action == 'delete') {
        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
        if (mysqli_query($conn, "DELETE FROM students WHERE id=$id")) {
            echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action or type!']);
exit;
?>
