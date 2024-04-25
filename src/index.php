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

// Function to fetch comments
// function fetchComments($mysqli, $postID) {
//     $stmt = $mysqli->prepare("SELECT content, commenter FROM comments WHERE postID = ?");
//     $stmt->bind_param("s", $postID);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $comments = [];
//     while ($row = $result->fetch_assoc()) {
//         $comments[] = $row;
//     }
//     $stmt->close();
//     return $comments;
// }

// Function to add a comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && isset($_POST['postID'])) {
    $comment = $_POST['comment'];
    $postID = $_POST['postID'];
    $commenter = $_SESSION['username']; // Username from session
    $stmt = $mysqli->prepare("INSERT INTO comments (commentID, content, postID, commenter) VALUES (UUID(), ?, ?, ?)");
    $stmt->bind_param("sss", $comment, $postID, $commenter);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php"); // Redirect to avoid resubmission
    exit;
}

// Check if login credentials are provided
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $login_type = checklogin_mysql($_POST["username"], $_POST["password"]);
    if ($login_type === 'regularuser' || $login_type === 'superuser') {
        // If login is successful, set session variables
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $_POST["username"];
        $_SESSION['browser'] = $_SERVER["HTTP_USER_AGENT"];
        $_SESSION['usertype'] = $login_type; // Set session variable for user type
    } elseif ($login_type === 'disabled') {
        // If the user is disabled, show a message and redirect to login form
        echo "<script>alert('Your account is disabled. Please contact the administrator.');</script>";
        header("Refresh: 0; url=form.php");
        exit();
    } else {
        // If login fails (user not found or incorrect password), destroy session and show error message
        session_destroy();
        echo "<script>alert('Invalid username/password');</script>";
        header("Refresh: 0; url=form.php");
        exit();
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

// Function to disable a user
function disableUser($username) {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE users SET disabled = 1 WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

// Function to enable a user
function enableUser($username) {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE users SET disabled = 0 WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

// Check if action is requested and the user is a superuser
if ($_SESSION['usertype'] === 'superuser' && isset($_GET['action']) && isset($_GET['username'])) {
    $action = $_GET['action'];
    $username = $_GET['username'];
    if ($action === 'disable') {
        disableUser($username);
    } elseif ($action === 'enable') {
        enableUser($username);
    }
}

// Function to fetch users
function fetchUsers() {
    global $mysqli;
    $users = [];
    $result = $mysqli->query("SELECT username FROM users");
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['username'];
    }
    return $users;
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
        .comment {
            margin-left: 20px;
            font-size: 0.9em;
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
        form.comment-form {
            margin-top: 10px;
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

                // Display comments
                $comments = fetchComments($mysqli, $post['postID']);
                if ($comments) {
                    echo "<div>Comments:</div>";
                    foreach ($comments as $comment) {
                        echo "<div class='comment'><strong>" . htmlentities($comment['commenter']) . ":</strong> " . htmlentities($comment['content']) . "</div>";
                    }
                }

                // Add comment form
                echo "<form class='comment-form' method='post' action=''>";
                echo "<input type='hidden' name='postID' value='" . $post['postID'] . "'>";
                echo "<textarea name='comment' rows='2' cols='50' placeholder='Write a comment...' required></textarea><br>";
                echo "<button class='btn' type='submit'>Add Comment</button>";
                echo "</form>";

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

        <!-- Additional functionality for superuser -->
        <?php if ($_SESSION['usertype'] === 'superuser'): ?>
            <h2>Manage Users</h2>
            <h3>User List</h3>
            <ul>
                <?php $users = fetchUsers();
                foreach ($users as $user) {
                    $disabled = isUserDisabled($user); // Check if the user is disabled
                    echo "<li>$user ";
                    if ($user !== $_SESSION['username']) { // Exclude the current user from actions
                        if ($disabled) {
                            // If the user is disabled, display the enable option
                            echo "<a href='?action=enable&username=$user'>Enable</a>";
                        } else {
                            // If the user is enabled, display the disable option
                            echo "<a href='?action=disable&username=$user'>Disable</a>";
                        }
                    }
                    echo "</li>";
                } ?>
            </ul>
        <?php endif; ?>

    </div>
</body>
</html>
