<?php
// Start session
session_start();

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 15 * 60,
    'path' => '/',
    'domain' => 'waph-team3.minifacebook.com',
    'secure' => true,
    'httponly' => true
]);

// Include database configuration
require "database.php";


// Check if login credentials are provided
if (isset($_POST["username"]) && isset($_POST["password"])) {
    if (checklogin_mysql($_POST["username"], $_POST["password"])) {
        // If login is successful, set session variables
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $_POST["username"];
        $_SESSION['browser'] = $_SERVER["HTTP_USER_AGENT"];
    } else {
        // If login fails, destroy session and show error message
        session_destroy();
        echo "<script>alert('Invalid username/password');window.location='form.php';</script>";
        die();
    }
}

// Check if user is logged in
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // If not logged in, destroy session and redirect to login form
    session_destroy();
    echo "<script>alert('You have not logged in, please login first!')</script>";
    header("Refresh: 0; url=form.php");
    die();
}

// Check for session hijacking
if ($_SESSION['browser'] != $_SERVER["HTTP_USER_AGENT"]) {
    // If session hijacking is detected, destroy session and redirect to login form
    session_destroy();
    echo "<script>alert('Session hijacking is detected')</script>";
    header("Refresh: 0; url=form.php");
    die();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        .post {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .post h3 {
            color: #333;
        }
        .post p {
            color: #666;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome <?php echo htmlentities($_SESSION['username']); ?>!</h2>

        <h2>Posts</h2>
        <hr>
        <?php
        // Fetch and display posts
        $posts = fetchPosts($mysqli);
        if (!empty($posts)) {
            foreach ($posts as $post) {
                echo "<div class='post'>";
                echo "<h3>Title: " . $post['title'] . "</h3>";
                echo "<p>Content: " . $post['content'] . "</p>";
                echo "<p>Posted by: " . $post['owner'] . "</p>";
                // Show edit and delete buttons only for the owner of the post
                if ($_SESSION['username'] === $post['owner']) {
                    echo "<form method='post' action='editpost.php'>";
                    echo "<input type='hidden' name='postID' value='" . $post['postID'] . "'>";
                    echo "<button class='btn' type='submit' name='edit'>Edit</button>";
                    echo "</form>";

                    echo "<form method='post' action='deletepost.php' onsubmit='return confirm(\"Are you sure you want to delete this post?\")'>";
                    echo "<input type='hidden' name='postID' value='" . $post['postID'] . "'>";
                    echo "<button class='btn' type='submit' name='delete'>Delete</button>";
                    echo "</form>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No posts found.</p>";
        }
        ?>
        <hr> 
        <a class="btn" href="changepasswordform.php">Change Password</a> 
        <a class="btn" href="profile.php">Edit Profile</a> 
        <a class="btn" href="logout.php">Logout</a>
        <a class="btn" href="newpost.php">Add Post</a>
    </div>
</body>
</html>
