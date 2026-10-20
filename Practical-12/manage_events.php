<?php
// ==================================================================
// Practical 12: Admin Event Management Module with File Upload Validation
// ==================================================================

session_start();
include 'db_connect.php';

$message = "";
$error = "";

// 1. Handle Messages from URL
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added')   $message = "Event and poster uploaded successfully!";
    if ($_GET['msg'] == 'updated') $message = "Event details updated successfully!";
    if ($_GET['msg'] == 'deleted') $message = "Event deleted successfully!";
}
if (isset($_GET['err'])) {
    $error = $_GET['err'];
}

// 2. Handle ADD EVENT Form Submission (POST with file upload)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    $title       = trim($_POST['title']);
    $event_date  = $_POST['event_date'];
    $category    = $_POST['category'];
    $description = trim($_POST['description']);

    // Check empty fields
    if (empty($title) || empty($event_date) || empty($category)) {
        $error = "Please fill in all required fields.";
    } elseif (!isset($_FILES['poster']) || $_FILES['poster']['error'] != 0) {
        $error = "Please select an event poster image.";
    } else {
        // Get File Upload Details
        $file_name = $_FILES['poster']['name'];
        $file_size = $_FILES['poster']['size'];
        $file_tmp  = $_FILES['poster']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validation Rules:
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp']; // Allowed extensions
        $max_file_size = 2 * 1024 * 1024;                // Maximum 2MB

        // 1. FILE TYPE VALIDATION
        if (!in_array($file_ext, $allowed_types)) {
            $error = "Invalid file type! Only JPG, JPEG, PNG, and WEBP images are allowed.";
        } 
        // 2. FILE SIZE VALIDATION
        elseif ($file_size > $max_file_size) {
            $error = "File size too large! Poster image must be less than 2MB.";
        } 
        // 3. UPLOAD FILE & SAVE TO DATABASE
        else {
            // Generate a unique file name to prevent overwriting
            $new_poster_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $file_name);
            $target_path = "uploads/" . $new_poster_name;

            // Move the file from temp folder to uploads folder
            if (move_uploaded_file($file_tmp, $target_path)) {
                
                // Insert into MySQL
                $insert_sql = "INSERT INTO events (title, event_date, category, description, poster) 
                               VALUES ('$title', '$event_date', '$category', '$description', '$new_poster_name')";
                
                if (mysqli_query($conn, $insert_sql)) {
                    header("Location: manage_events.php?msg=added");
                    exit();
                } else {
                    $error = "Database Error: " . mysqli_error($conn);
                }

            } else {
                $error = "Failed to upload image file to the server.";
            }
        }
    }
}

// 3. Fetch all events for viewing
$result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
$event_count = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Event Management - Practical 12</title>

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
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 25px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

h2, h3 {
    color: #003366;
    margin-bottom: 15px;
}

/* Alert Feedback */
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
input[type="date"],
select,
textarea {
    width: 100%;
    padding: 9px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}

input[type="file"] {
    width: 100%;
    padding: 7px;
    background: #f9f9f9;
    border: 1px dashed #999;
    border-radius: 5px;
}

.help-text {
    font-size: 11px;
    color: #666;
    margin-top: 3px;
}

.btn-submit {
    background: #003366;
    color: white;
    padding: 10px 22px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
}

.btn-submit:hover {
    background: #002244;
}

/* Table Styling */
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
    vertical-align: middle;
}

th {
    background: #003366;
    color: white;
}

tr:hover {
    background: #f9f9f9;
}

.poster-img {
    width: 80px;
    height: 55px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #ddd;
    display: block;
}

.badge {
    background: #e3f2fd;
    color: #0d47a1;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: bold;
}

.btn-edit {
    background: #1976d2;
    color: white;
    padding: 6px 12px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
    margin-right: 5px;
}

.btn-delete {
    background: #d32f2f;
    color: white;
    padding: 6px 12px;
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
    <p>Practical 12: Admin Event Management &amp; Poster Upload</p>
</header>

<nav>
    <a href="Home.html">Home</a>
    <a href="manage_events.php" style="color: #003366; text-decoration: underline;">Event Manager</a>
    <a href="Events.html">Student Events View</a>
    <a href="admin.php">Admin Panel</a>
    <a href="dashboard.php">Dashboard</a>
</nav>

<main>

    <!-- Feedback Alerts -->
    <?php if (!empty($message)): ?>
        <div class="alert-success">&#10004; <?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert-error">&#10008; <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- 1. ADD EVENT FORM WITH FILE UPLOAD -->
    <div class="box">
        <h3>➕ Add New Campus Event</h3>

        <!-- Notice enctype="multipart/form-data" for file upload -->
        <form action="manage_events.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-grid">
                <div>
                    <label>Event Title</label>
                    <input type="text" name="title" placeholder="e.g. Hackathon 2026" required>
                </div>

                <div>
                    <label>Event Date</label>
                    <input type="date" name="event_date" required>
                </div>

                <div>
                    <label>Category</label>
                    <select name="category" required>
                        <option value="Technical">Technical</option>
                        <option value="Cultural">Cultural</option>
                        <option value="Sports">Sports</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Seminar">Seminar</option>
                    </select>
                </div>

                <div>
                    <label>Event Poster (Image File)</label>
                    <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp" required>
                    <p class="help-text">Max 2MB &bull; Allowed: JPG, PNG, WEBP</p>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label>Event Description</label>
                <textarea name="description" rows="3" placeholder="Enter brief details about this event..."></textarea>
            </div>

            <button type="submit" name="add_event" class="btn-submit">Upload &amp; Publish Event</button>
        </form>
    </div>

    <!-- 2. VIEW ALL EVENTS TABLE -->
    <div class="box">
        <h3>📅 Campus Events List (Total: <?php echo $event_count; ?>)</h3>

        <table>
            <thead>
                <tr>
                    <th>Poster</th>
                    <th>Event Title</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($event_count > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['poster']) && file_exists('uploads/' . $row['poster'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($row['poster']); ?>" alt="Poster" class="poster-img">
                                <?php else: ?>
                                    <span style="font-size: 11px; color: #888;">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><b><?php echo htmlspecialchars($row['title']); ?></b></td>
                            <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                            <td><span class="badge"><?php echo htmlspecialchars($row['category']); ?></span></td>
                            <td style="color: #555; max-width: 250px;"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <!-- EDIT button -->
                                <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                <!-- DELETE button -->
                                <a href="delete_event.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this event and its poster?');" class="btn-delete">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #888; padding: 25px;">
                            No events found. Add your first event using the form above!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<footer>
    <p>&copy; 2026 StudentHub Portal | Practical 12</p>
</footer>

</body>
</html>
