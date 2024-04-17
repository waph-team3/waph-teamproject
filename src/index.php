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

// Function to check if a post belongs to a specific user
function checkPostOwner($username, $postID) {
    global $mysqli;

    // Debugging statement
    echo "Checking post owner for postID: " . $postID . "<br>";

    // Prepare and execute query to fetch the owner of the post
    $stmt = $mysqli->prepare("SELECT owner FROM posts WHERE postID = ?");
    $stmt->bind_param("i", $postID);
    $stmt->execute();

    // Check for errors
    if ($stmt->error) {
        echo "Error: " . $stmt->error;
        return false;
    }
    
    $stmt->bind_result($owner);
    $stmt->fetch();
    $stmt->close();

    // Debugging statements
    echo "Owner from database: " . $owner . "<br>";
    echo "Provided username: " . $username . "<br>";

    // Check if the post exists and its owner matches the given username
    if ($owner === $username) {
        return true; // Post belongs to the user
    } else {
        return false; // Post does not belong to the user or does not exist
    }
}

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


// Handle post deletion
if (isset($_POST['delete']) && isset($_POST['postID'])) {
    $postID = $_POST['postID'];
    // Check if the post belongs to the current user
    if (checkPostOwner($_SESSION['username'], $postID)) {
        deletePost($postID);
        // Redirect to the current page to reflect changes
        header("Location: editpost.php?post_id=$postID");
        exit();
    } else {
        echo "<script>alert('You are not authorized to delete this post.')</script>";
    }
}

// Handle post editing
if (isset($_POST['edit']) && isset($_POST['postID'])) {
    
    echo "iidsfs: " . $_POST['dee'];
    echo "postID1: " . $_POST['IID'];

    $postID = $_POST['postID'];

    echo "postID: " . $postID;

    // Check if the post belongs to the current user
    if (checkPostOwner($_SESSION['username'], $postID)) {
        // Redirect to the edit post page with the post ID
        header("Location: editpost.php?post_id=$postID");
        exit();
    } else {
        echo "<script>alert('You are not authorized to edit this post.')</script>";
    }
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
                echo "<p>Posted by: " . $post['postID'] . "</p>";
                // Show edit and delete buttons only for the owner of the post
                if ($_SESSION['username'] === $post['owner']) {
                    echo "<form method='post' action='editpost.php'>";
                    echo "<input type='hidden' name='postID' value='" . $post['postID'] . "'>";
                    echo "<button class='btn' type='submit' name='edit'>Edit</button>";
                    echo "</form>";

                    echo "<form method='post' action='deletepost.php'>";
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
