<?php
// 1. Connect to the database
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Get data from the HTML form
    $fullname = $_POST["fullname"];
    $enrollment = $_POST["enrollment"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $department = $_POST["department"];

    // 3. Simple check to make sure fields are not empty
    if (empty($fullname) || empty($email) || empty($password)) {
        echo "<h3 style='color:red;'>Error: Please fill all details.</h3> <a href='Register.html'>Go Back</a>";
    } else {
        
        // 4. Create the SQL command to save data into the students table
        $sql = "INSERT INTO students (fullname, enrollment, email, password, department) 
                VALUES ('$fullname', '$enrollment', '$email', '$password', '$department')";

        // 5. Run the SQL command
        if (mysqli_query($conn, $sql)) {
            echo "<h3 style='color:green;'>Success: Data saved into Database successfully!</h3> <a href='Register.html'>Go Back</a>";
        } else {
            echo "<h3 style='color:red;'>Error saving data: " . mysqli_error($conn) . "</h3> <a href='Register.html'>Go Back</a>";
        }
        
    }
}
?>
