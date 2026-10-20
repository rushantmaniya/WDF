<?php
// ==================================================================
// Practical 12: Edit Event Record & Optional Poster Update
// ==================================================================

include 'db_connect.php';

$error = "";

// 1. Handle Event Update Submission (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id          = intval($_POST['id']);
    $title       = trim($_POST['title']);
    $event_date  = $_POST['event_date'];
    $category    = $_POST['category'];
    $description = trim($_POST['description']);
    $old_poster  = $_POST['old_poster'];

    if (empty($title) || empty($event_date) || empty($category)) {
        $error = "Please fill in all required fields.";
    } else {
        $final_poster = $old_poster;

        // Check if user uploaded a NEW poster
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
            $file_name = $_FILES['poster']['name'];
            $file_size = $_FILES['poster']['size'];
            $file_tmp  = $_FILES['poster']['tmp_name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
            $max_file_size = 2 * 1024 * 1024; // 2MB

            // 1. File Type Validation
            if (!in_array($file_ext, $allowed_types)) {
                $error = "Invalid file type! Only JPG, JPEG, PNG, and WEBP allowed.";
            } 
            // 2. File Size Validation
            elseif ($file_size > $max_file_size) {
                $error = "File size exceeds 2MB! Please upload a smaller image.";
            } else {
                $new_file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $file_name);
                if (move_uploaded_file($file_tmp, "uploads/" . $new_file_name)) {
                    $final_poster = $new_file_name;
                } else {
                    $error = "Failed to upload new poster.";
                }
            }
        }

        // If no error, update MySQL record
        if (empty($error)) {
            $update_sql = "UPDATE events SET 
                title       = '$title',
                event_date  = '$event_date',
                category    = '$category',
                description = '$description',
                poster      = '$final_poster'
                WHERE id    = $id";

            if (mysqli_query($conn, $update_sql)) {
                header("Location: manage_events.php?msg=updated");
                exit();
            } else {
                $error = "Database Error: " . mysqli_error($conn);
            }
        }
    }
}

// 2. Fetch existing event record by ID (GET)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($conn, "SELECT * FROM events WHERE id = $id");
    $event = mysqli_fetch_assoc($res);

    if (!$event) {
        die("Event not found! <a href='manage_events.php'>Go Back</a>");
    }
} else if (!isset($_POST['id'])) {
    header("Location: manage_events.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Event - Practical 12</title>

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
    font-size: 13px;
}

input[type="text"],
input[type="date"],
select,
textarea {
    width: 100%;
    padding: 9px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}

.current-poster-preview {
    display: flex;
    align-items: center;
    gap: 15px;
    background: #f9f9f9;
    padding: 10px;
    border-radius: 5px;
    margin-top: 5px;
    border: 1px solid #eee;
}

.current-poster-preview img {
    width: 90px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #ccc;
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
    <p>Edit Event Record</p>
</header>

<main>

    <div class="box">
        <h2>✏️ Edit Event Details</h2>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="edit_event.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
            <input type="hidden" name="old_poster" value="<?php echo htmlspecialchars($event['poster']); ?>">

            <label>Event Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>

            <label>Event Date</label>
            <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>

            <label>Category</label>
            <select name="category" required>
                <option value="Technical" <?php if ($event['category'] == 'Technical') echo 'selected'; ?>>Technical</option>
                <option value="Cultural" <?php if ($event['category'] == 'Cultural') echo 'selected'; ?>>Cultural</option>
                <option value="Sports" <?php if ($event['category'] == 'Sports') echo 'selected'; ?>>Sports</option>
                <option value="Workshop" <?php if ($event['category'] == 'Workshop') echo 'selected'; ?>>Workshop</option>
                <option value="Seminar" <?php if ($event['category'] == 'Seminar') echo 'selected'; ?>>Seminar</option>
            </select>

            <label>Current Event Poster</label>
            <div class="current-poster-preview">
                <?php if (file_exists('uploads/' . $event['poster'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($event['poster']); ?>" alt="Poster">
                <?php endif; ?>
                <span style="font-size: 13px; color: #555;">File: <?php echo htmlspecialchars($event['poster']); ?></span>
            </div>

            <label>Change Poster (Optional)</label>
            <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp">
            <p style="font-size: 11px; color: #666; margin-top: 3px;">Leave blank to keep existing poster. Max 2MB (JPG, PNG, WEBP).</p>

            <label>Event Description</label>
            <textarea name="description" rows="3"><?php echo htmlspecialchars($event['description']); ?></textarea>

            <div style="margin-top: 25px;">
                <button type="submit" class="btn-save">Update Event</button>
                <a href="manage_events.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

</main>

</body>
</html>
