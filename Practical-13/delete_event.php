<?php
// ==================================================================
// Practical 12: Delete Event Record & Remove Uploaded Poster
// ==================================================================

include 'db_connect.php';

// 1. Get Event ID from URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 2. Find poster filename to delete the file from 'uploads/' folder
    $res = mysqli_query($conn, "SELECT poster FROM events WHERE id = $id");
    if ($row = mysqli_fetch_assoc($res)) {
        $poster_file = "uploads/" . $row['poster'];
        // Remove file if exists (skip sample default posters)
        if (file_exists($poster_file) && !str_starts_with($row['poster'], 'sample_')) {
            unlink($poster_file);
        }
    }

    // 3. Delete event row from MySQL
    $delete_sql = "DELETE FROM events WHERE id = $id";
    if (mysqli_query($conn, $delete_sql)) {
        header("Location: manage_events.php?msg=deleted");
    } else {
        header("Location: manage_events.php?err=" . urlencode("Failed to delete event: " . mysqli_error($conn)));
    }
} else {
    header("Location: manage_events.php");
}
exit();
?>
