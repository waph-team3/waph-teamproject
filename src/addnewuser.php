<?php
require "database.php";

// Initialize variables to null
$username = $password = $fullname = $primaryemail = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic validation
    $username = test_input($_POST["username"]);
    $password = test_input($_POST["password"]);
    $fullname = test_input($_POST["fullname"] ?? ''); // Using null coalescing operator
    $primaryemail = test_input($_POST["email"] ?? '');

    // Validate username and password
    if (empty($username) || empty($password)) {
        echo "No username/password provided";
    } elseif (strlen($password) < 8) {
        echo "Password must be at least 8 characters long";
    } elseif (!filter_var($primaryemail, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
    } else {
        // Attempt to register the user
        $success = addNewUser($username, $password, $fullname, $primaryemail);
        echo $success ? "Registration Succeed" : "Registration Failed";
    }
} else {
    // Form not submitted
    echo "Please submit the form";
}

// Function to sanitize input data
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <br>
        <a href="login.php" class="login-link">Login here</a>
    </div>
</body>
</html>
