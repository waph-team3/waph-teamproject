<?php
// Start session
session_start();

// Include database configuration
require "database.php";

// Function to check if a post belongs to a specific user
function checkPostOwner($username, $postID) {
    global $mysqli;

    // Prepare and execute query to fetch the owner of the post
    $stmt = $mysqli->prepare("SELECT owner FROM posts WHERE postID = ?");
    $stmt->bind_param("s", $postID);
    $stmt->execute();

    // Check for errors
    if ($stmt->error) {
        echo "Error: " . $stmt->error;
        return false;
    }
    
    $stmt->bind_result($owner);
    $stmt->fetch();
    $stmt->close();

    // Check if the post exists and its owner matches the given username
    if ($owner === $username) {
        return true; // Post belongs to the user
    } else {
        return false; // Post does not belong to the user or does not exist
    }
}

// Check if user is logged in
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // If not logged in, destroy session and redirect to login form
    session_destroy();
    echo "<script>alert('You have not logged in, please login first!')</script>";
    header("Refresh: 0; url=form.php");
    exit();
}

// Check for session hijacking
if ($_SESSION['browser'] != $_SERVER["HTTP_USER_AGENT"]) {
    // If session hijacking is detected, destroy session and redirect to login form
    session_destroy();
    echo "<script>alert('Session hijacking is detected')</script>";
    header("Refresh: 0; url=form.php");
    exit();
}

// Handle post deletion
if (isset($_POST['delete']) && isset($_POST['postID'])) {
    $postID = $_POST['postID'];
    // Check if the post belongs to the current user
    if (checkPostOwner($_SESSION['username'], $postID)) {
        // Delete the post
        deletePost($postID);
        echo "<script>alert('Post deleted successfully.')</script>";
    } else {
        echo "<script>alert('You are not authorized to delete this post.')</script>";
    }
}

// Redirect to the main page
header("Location: index.php");
exit();
?>
