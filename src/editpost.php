<?php
session_start();

// Include database configuration
require "database.php";

// Check if user is logged in
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: form.php");
    exit;
}

// Check if post ID is provided
if (!isset($_POST['postID'])) {
    header("Location: index.php");
    exit;
}

// Get post ID from the form submission
$postID = $_POST['postID'];

// Fetch post details from the database
$post = fetchPostById($postID);

// Check if the post exists
if (!$post) {
    echo "Post not found.";
    exit;
}

// Check if the current user is the owner of the post
if ($_SESSION['username'] !== $post['owner']) {
    echo "You are not authorized to edit this post.";
    exit;
}

// Handle update action if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    updatePost($postID, $title, $content);
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
</head>
<body>
    <h1>Edit Post</h1>
    <form method="post">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>"><br>
        <label for="content">Content:</label><br>
        <textarea id="content" name="content"><?php echo htmlspecialchars($post['content']); ?></textarea><br>
        <input type="hidden" name="postID" value="<?php echo $postID; ?>">
        <button type="submit" name="update">Update</button>
    </form>
</body>
</html>
