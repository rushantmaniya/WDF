<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST["name"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST["message"] ?? ''));

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        echo "<h3 style='color:red;'>Error: All fields are required.</h3> <a href='Contact.html'>Go Back</a>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<h3 style='color:red;'>Error: Invalid email format.</h3> <a href='Contact.html'>Go Back</a>";
    } else {
        // Store data in JSON format
        $data = ["name" => $name, "email" => $email, "message" => $message];
        $file = 'contacts.json';
        
        // Read old data, add new data, and save
        $current_data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
        if (!is_array($current_data)) {
            $current_data = [];
        }
        $current_data[] = $data;
        file_put_contents($file, json_encode($current_data, JSON_PRETTY_PRINT));
        
        echo "<h3 style='color:green;'>Success: Your message has been sent successfully.</h3> <a href='Contact.html'>Go Back</a>";
    }
} else {
    echo "<h3 style='color:red;'>Invalid request method.</h3> <a href='Contact.html'>Go Back</a>";
}
?>
