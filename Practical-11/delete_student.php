<?php
// ==================================================================
// Practical 11: Delete Student Record
// ==================================================================

include 'db_connect.php';

// 1. Get the ID from the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 2. Run DELETE query
    $delete_sql = "DELETE FROM students WHERE id = $id";

    if (mysqli_query($conn, $delete_sql)) {
        // Redirect with success message
        header("Location: students.php?msg=deleted");
    } else {
        // Redirect with error message
        header("Location: students.php?err=" . urlencode("Could not delete record: " . mysqli_error($conn)));
    }
} else {
    header("Location: students.php");
}
exit();
?>
