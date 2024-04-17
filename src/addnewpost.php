<?php
require "database.php";
session_start(); // Ensure session is started before using $_SESSION

$title = $_POST["title"] ?? ''; // Use null coalescing operator to avoid undefined index notices
$content = $_POST["content"] ?? '';
$username = $_SESSION['username'] ?? ''; // Fallback to empty string if not set

// Check if both title and content are provided
if (!empty($title) && !empty($content)) {
    // Assuming addNewPost is a function defined in database.php or another included file
    if (addNewPost($title, $content, $username)) {
        echo "Post added";
    } else {
        echo "Failed";
    }
} else {
    echo "Please fill out all fields.";
}
?>
