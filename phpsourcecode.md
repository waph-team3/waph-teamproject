# Source code to Markdown
This file is automatically created by a script. Please delete this line and replace with the course and your team information accordingly.
## /database.php
```php
<?php
$mysqli = new mysqli('localhost', 'team3', '1234', 'waph_team');
	
if($mysqli->connect_errno) {
    printf("Database connection failed: %s\n", $mysqli->connect_error);
    return FALSE;
}

function addNewUser($username, $password, $fullname, $otheremail) {
    global $mysqli;
    
    // Basic validation checks
    if (empty($username) || empty($password) || empty($fullname) || empty($otheremail)) {
        return false; // Ensures that no field is empty
    }
    
    if (!filter_var($otheremail, FILTER_VALIDATE_EMAIL)) {
        return false; // Ensures the email is in a valid format
    }
    
    // Here you can add additional validation as needed, e.g., minimum lengths
    if (strlen($password) < 8) {
        return false; // Password must be at least 8 characters
    }

    // Hash the password using a more secure method
    $hashedPassword = md5($password);
    
    $preparedSql = "INSERT INTO users (username, password, fullname, otheremail) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($preparedSql);
    if (!$stmt) {
        return false; // Could not prepare the statement
    }
    
    $stmt->bind_param("ssss", $username, $hashedPassword, $fullname, $otheremail); // Bind the variables
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}


function checklogin_mysql($username, $password) {
    global $mysqli;
    
    // Hash the password using md5 for comparison
    $hashed_password = md5($password);
    
    // Check if the user is enabled in the users table
    $prepared_sql_users = "SELECT * FROM users WHERE username=? AND password=?";
    $stmt_users = $mysqli->prepare($prepared_sql_users);
    $stmt_users->bind_param("ss", $username, $hashed_password);
    $stmt_users->execute();
    $result_users = $stmt_users->get_result();
    
    // If the user is found in the users table
    if($result_users->num_rows == 1) {
        $user = $result_users->fetch_assoc();
        if ($user['disabled'] == 1) {
            // User is disabled
            return 'disabled';
        } else {
            // User is enabled, determine user type and return it
            return 'regularuser';
        }
    }
    
    // Check in the super_users table
    $prepared_sql_superusers = "SELECT * FROM super_users WHERE username=? AND password=?";
    $stmt_superusers = $mysqli->prepare($prepared_sql_superusers);
    $stmt_superusers->bind_param("ss", $username, $hashed_password);
    $stmt_superusers->execute();
    $result_superusers = $stmt_superusers->get_result();
    
    // If the user is found in the super_users table
    if($result_superusers->num_rows == 1) {
        return 'superuser'; // Return 'superuser' if the user is a superuser
    }
    
    return FALSE; // Return FALSE if the user is not found in either table
}

// Function to check if a user is disabled
function isUserDisabled($username) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT disabled FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($disabled);
    $stmt->fetch();
    $stmt->close();
    return $disabled; // Returns true if the user is disabled, false otherwise
}


function updatePassword($username, $newPassword) {
    $mysqli = new mysqli('localhost','team3','1234','waph_team');
    
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    $hashed_password = md5($newPassword);

    $prepared_sql = "UPDATE users SET password = ? WHERE username = ?";
    $stmt = $mysqli->prepare($prepared_sql);
    $stmt->bind_param("ss", $hashed_password, $username);
    
    if ($stmt->execute()) {
        $stmt->close();
        $mysqli->close();
        return true;
    } else {
        $stmt->close();
        $mysqli->close();
        return false;
    }
}

// Function to update user profile
function updateUserProfile($username, $fullname, $otheremail, $phone) {
    $mysqli = new mysqli('localhost','team3','1234','waph_team');
    
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    $prepared_sql = "UPDATE users SET fullname = ?, otheremail = ?, phone = ? WHERE username = ?";
    $stmt = $mysqli->prepare($prepared_sql);
    $stmt->bind_param("ssss", $fullname, $otheremail, $phone, $username);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}


// function fetchPosts() {
//     global $mysqli;
//     $posts = array();

//     $sql = "SELECT * FROM posts";
//     $result = $mysqli->query($sql);

//     if ($result->num_rows > 0) {
//         while ($row = $result->fetch_assoc()) {
//             $posts[] = $row;
//         }
//     }

//     return $posts;
// }

function fetchPosts() {
    global $mysqli;
    $posts = array();

    // Prepare the SQL query with a parameter placeholder
    $sql = "SELECT * FROM posts";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        // Execute the prepared statement
        $stmt->execute();

        // Get the result set
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch rows as associative array and add them to $posts array
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
        }

        // Close the statement
        $stmt->close();
    }

    return $posts;
}


// Function to fetch a post by its ID from the database
function fetchPostById($postID) {
    global $mysqli;
    
    // Prepare and execute query to fetch the post
    $stmt = $mysqli->prepare("SELECT title, content, owner FROM posts WHERE postID = ?");
    $stmt->bind_param("s", $postID);
    $stmt->execute();
    $stmt->bind_result($title, $content, $owner);
    
    // Fetch the result
    $stmt->fetch();
    
    // Create an associative array to hold the post details
    $post = array(
        'title' => $title,
        'content' => $content,
        'owner' => $owner
    );
    
    // Close the statement
    $stmt->close();
    
    // Return the post details
    return $post;
}


    function addNewPost($title, $content, $username) {
    // Perform input validation (you can add more validation as needed)
        global $mysqli;
    if (empty($title) || empty($content)) {
        return "Please fill out all fields.";
    } else {
        // Insert the new post into the database
        $postID = uniqid(); // Generate a unique postID
        $owner = $username; // Assuming the username is passed to the function
        $sql = "INSERT INTO posts (postID, title, content, owner) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ssss", $postID, $title, $content, $owner);
            if ($stmt->execute()) {
                $stmt->close();
                return "Post added successfully!";
            } else {
                $stmt->close();
                return "Error adding post: " . $mysqli->error;
            }
        } else {
            return "Error preparing statement: " . $mysqli->error;
        }
    }
}

// Function to update a post in the database
function updatePost($postID, $title, $content) {
    global $mysqli;
    
    // Prepare and execute query to update the post
    $stmt = $mysqli->prepare("UPDATE posts SET title = ?, content = ? WHERE postID = ?");
    $stmt->bind_param("sss", $title, $content, $postID);
    $stmt->execute();
    $stmt->close();
}

function deletePost($postID) {
    global $mysqli;
    
    // Prepare and execute query to delete the post
    $stmt = $mysqli->prepare("DELETE FROM posts WHERE postID = ?");
    $stmt->bind_param("s", $postID);
    $stmt->execute();

    // Check for errors
    if ($stmt->error) {
        echo "Error deleting post: " . $stmt->error;
    } else {
        echo "Post deleted successfully.";
    }
    
    $stmt->close();
}

function fetchComments($mysqli, $postID) {
    // Prepare SQL query to select comments for the specified postID
    $query = "SELECT content, commenter FROM comments WHERE postID = ?";
    
    // Prepare the SQL statement
    $stmt = $mysqli->prepare($query);
    
    // Bind the postID parameter to the prepared statement
    $stmt->bind_param("s", $postID);
    
    // Execute the prepared statement
    $stmt->execute();
    
    // Get result set from the executed statement
    $result = $stmt->get_result();
    
    // Initialize an empty array to store comments
    $comments = [];
    
    // Fetch each row from the result set
    while ($row = $result->fetch_assoc()) {
        // Add the fetched comment to the comments array
        $comments[] = $row;
    }
    
    // Close the prepared statement
    $stmt->close();
    
    // Return the array of comments
    return $comments;
}

?>

```
## /addnewpost.php
```php
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

```
## /deletepost.php
```php
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

```
## /addnewuser.php
```php
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
        <a href="form.php" class="login-link">Login here</a>
    </div>
</body>
</html>

```
## /registrationform.php
```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>WAPH-Registration page</title>
   <link rel="stylesheet" href="style.css">
  <script type="text/javascript">
      function displayTime() {
        document.getElementById('digit-clock').innerHTML = "Current time:" + new Date();
      }
      setInterval(displayTime, 500);

      function validateForm() {
      var fullname = document.getElementById('fullname').value.trim();
      var email = document.getElementById('email').value.trim();
      var username = document.getElementById('username').value.trim();
      var password = document.getElementById('password').value;
      var confirmPassword = document.getElementById('confirmPassword').value;
      var errorElement = document.getElementById('error-message');

      // Client-side validation for password match
      if (password !== confirmPassword) {
        errorElement.textContent = "Passwords do not match";
        return false;
      }

      // Client-side validation for preventing XSS (Cross-Site Scripting)
      var htmlPattern = /<\/?[a-z][\s\S]*>/i; // Regex to detect HTML tags
      if (htmlPattern.test(fullname) || htmlPattern.test(email) || htmlPattern.test(username)) {
        errorElement.textContent = "Invalid characters detected";
        return false;
      }

      // Client-side validation for email format
      var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(email)) {
        errorElement.textContent = "Invalid email format";
        return false;
      }-

      // Clear error message if all validations pass
      errorElement.textContent = "";
      return true;
    }
  </script>
</head>
<body>
  <div class="container">
    <h1>Registration form, WAPH</h1>
    <h2>Team-3</h2>
    <div id="digit-clock"></div>
    <?php
      // PHP code to display visited time
      echo "Visited time: " . date("Y-m-d h:i:sa");
    ?>
    <form action="addnewuser.php" method="POST" class="form login" onsubmit="return validateForm()">
      <input type="text" class="text_field" id="fullname" name="fullname" placeholder="Full Name" required><br>
      <input type="text" class="text_field" id="email" name="email" placeholder="Email" required><br>
      <input type="text" class="text_field" id="username" name="username" placeholder="Username" required><br>
      <input type="password" class="text_field" id="password" name="password" placeholder="Password" required><br>
      <input type="password" class="text_field" id="confirmPassword" placeholder="Confirm Password" required><br>
      <span class="error-message" id="error-message"></span>
      <button class="button" type="submit">Register</button>
    </form>
  </div>
  <div class="container">
        <br>
        <a href="login.php" class="login-link">Already a user? Login here</a>
  </div>

</body>
</html>

```
## /profile.php
```php
<?php


session_start();  


if (!isset($_SESSION['authenticated']) or $_SESSION['authenticated'] != TRUE) {
        session_destroy();
        echo "<script>alert('Nice try!! You have to login first!')</script>";
        header("Refresh: 0; url=login.php");
        die();
    }

    if ($_SESSION['browser'] != $_SERVER["HTTP_USER_AGENT"]) {
    session_destroy();
    echo "<script>alert('Alert! Alert ! Session hijacking is detected')</script>";
    header("Refresh: 0; url=login.php");
    die();
}
require "database.php";

if (!isset($_SESSION['nocsrftoken'])) {
    $_SESSION['nocsrftoken'] = bin2hex(openssl_random_pseudo_bytes(32)); // Generate a random token
}

// Validate CSRF token on form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tokenFromForm = $_POST['nocsrftoken'] ?? '';
    if (!hash_equals($_SESSION['nocsrftoken'], $tokenFromForm)) {
        die("CSRF Token Validation Failed.");
    }
}


// Retrieve user's current profile data
$username = ""; // Initialize variables to store current user's data
$fullname = "";
$otheremail = "";
$phone = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['fullname']) && isset($_POST['otheremail']) && isset($_POST['phone'])) {
        $username = $_POST['username'];
        $fullname = $_POST['fullname'];
        $otheremail = $_POST['otheremail'];
        $phone = $_POST['phone'];

        // Update user's profile
        if (updateUserProfile($username, $fullname, $otheremail, $phone)) {
            echo "Profile updated successfully.";
        } else {
            echo "Failed to update profile.";
        }
    } else {
        echo "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Edit Profile</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label>Username:</label><br>
        <input type="text" name="username" value="<?php echo $username; ?>" ><br>
        <label>Full Name:</label><br>
        <input type="text" name="fullname" value="<?php echo $fullname; ?>"><br>
        <label>Additional Email:</label><br>
        <input type="text" name="otheremail" value="<?php echo $otheremail; ?>"><br>
        <label>Phone:</label><br>
        <input type="text" name="phone" value="<?php echo $phone; ?>"><br><br>
        <input type="hidden" name="nocsrftoken" value="<?php echo htmlspecialchars($_SESSION['nocsrftoken']); ?>">
        <input type="submit" value="Update Profile">
    </form>
</body>
</html>

```
## /form.php
```php
<?php
session_start();
if (!isset($_SESSION['nocsrftoken'])) {
    $_SESSION['nocsrftoken'] = bin2hex(openssl_random_pseudo_bytes(32)); // Generate a random token
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>WAPH-Login page</title>
  <link rel="stylesheet" href="style.css">
  
  <script type="text/javascript">
      function displayTime() {
        document.getElementById('digit-clock').innerHTML = "Current time:" + new Date();
      }
      setInterval(displayTime, 500);


      function validateForm() {
      
      var username = document.getElementById('username').value.trim();
      var password = document.getElementById('password').value;
      var errorElement = document.getElementById('error-message');

      // Check if username is empty
        if (username === "") {
            errorElement.textContent = "Please enter your username.";
            return false; // Prevent form from submitting
        }

        // Check if password is empty
        if (password === "") {
            errorElement.textContent += (errorElement.textContent.length > 0 ? "\n" : "") + "Please enter your password.";
            return false; // Prevent form from submitting
        }
        
          // Clear error message if all validations pass
          errorElement.textContent = "";
          return true;
        }
  </script>
</head>
<body>
  <div class="container">
    <h1>A Simple login form, WAPH</h1>
    <h2>Team-3</h2>
    <div id="digit-clock"></div>

    <div class="visited-time">
    <?php echo "Visited time: " . date("Y-m-d h:i:sa"); ?>
    </div>

    <form action="index.php" method="POST" class="form login" onsubmit="return validateForm()">
      Username: <input type="text" class="text_field" name="username" /> <br>
      Password: <input type="password" class="text_field" name="password" /> <br>
      <span class="error-message" id="error-message"></span>
      <input type="hidden" name="nocsrftoken" value="<?php echo $_SESSION['nocsrftoken']; ?>">
      <button class="button" type="submit">Login</button>
    </form>
  </div>
</body>
</html>

```
## /chat.php
```php
<?php

session_start();
if (!isset($_SESSION['authenticated']) or $_SESSION['authenticated'] != TRUE) {
        session_destroy();
        echo "<script>alert('Nice try!! You have to login first!')</script>";
        header("Refresh: 0; url=login.php");
        die();
    }

    if ($_SESSION['browser'] != $_SERVER["HTTP_USER_AGENT"]) {
    session_destroy();
    echo "<script>alert('Alert! Alert ! Session hijacking is detected')</script>";
    header("Refresh: 0; url=login.php");
    die();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Group Live Chat</h1>
<pre id="messages" style="height: 400px; overflow: scroll"></pre>
<input type="text" id="messageBox" placeholder="Type your message here" style="display: block; width: 100%; margin-bottom: 10px; padding: 10px;" />
<button id="send" title="Send Message!" style="width: 100%; height: 30px;">Send Message</button>

<script>
  (function() {
    const sendBtn = document.querySelector('#send');
    const messages = document.querySelector('#messages');
    const messageBox = document.querySelector('#messageBox');

    let ws;

    function showMessage(message) {
      messages.textContent += `\n\n${message}`;
      messages.scrollTop = messages.scrollHeight;
      messageBox.value = '';
    }

    function init() {
      if (ws) {
        ws.onerror = ws.onopen = ws.onclose = null;
        ws.close();
      }

      ws = new WebSocket('ws://localhost:6969');
      ws.onopen = () => {
        console.log('Connection opened!');
      }
      ws.onmessage = (event) => {
        const messageData = JSON.parse(event.data);
        showMessage(`${messageData.sender}: ${messageData.message}`);
      };
      ws.onclose = function() {
        ws = null;
      }
    }

    sendBtn.onclick = function() {
      if (!ws) {
        showMessage("No WebSocket connection :(");
        return;
      }

      const message = messageBox.value;
      if (message.trim() === '') return;

      const messageData = {
        sender: '<?php echo $_SESSION['username']; ?>',
        message: message
      };

      ws.send(JSON.stringify(messageData));
      showMessage(`${messageData.sender}: ${messageData.message}`);
    }

    init();
  })();
</script>
</body>
</html>
```
## /changepasswordform.php
```php
<?php

session_start();

if (!isset($_SESSION['authenticated']) or $_SESSION['authenticated'] != TRUE) {
        session_destroy();
        echo "<script>alert('Nice try!! You have to login first!')</script>";
        header("Refresh: 0; url=login.php");
        die();
    }

    if ($_SESSION['browser'] != $_SERVER["HTTP_USER_AGENT"]) {
    session_destroy();
    echo "<script>alert('Alert! Alert ! Session hijacking is detected')</script>";
    header("Refresh: 0; url=login.php");
    die();
}


// Generate and store CSRF token in the session if it doesn't exist
if (!isset($_SESSION['nocsrftoken'])) {
    $_SESSION['nocsrftoken'] = bin2hex(openssl_random_pseudo_bytes(32)); // Generate a random token
}

// Validate CSRF token on form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tokenFromForm = $_POST['nocsrftoken'] ?? '';
    if (!hash_equals($_SESSION['nocsrftoken'], $tokenFromForm)) {
        die("CSRF Token Validation Failed.");
    }
}


require "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all fields are filled
    if (isset($_POST['username']) && isset($_POST['current_password']) && isset($_POST['new_password'])) {
        // Fetch the user's current password from the database
        $stmt = $mysqli->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $_POST['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $current_password_from_database = $row['password'];

            // Check if the entered current password matches the one in the database
            if (md5($_POST['current_password']) == $current_password_from_database) {
                // Update password
                if (updatePassword($_POST['username'], $_POST['new_password'])) {
                    echo "Password updated successfully.";
                } else {
                    echo "Failed to update password.";
                }
            } else {
                echo "Current password is incorrect.";
            }
        } else {
            echo "User not found.";
        }
        $stmt->close();
    } else {
        echo "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Change Password</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label>Username:</label><br>
        <input type="text" name="username"><br>
        <label>Current Password:</label><br>
        <input type="password" name="current_password"><br>
        <label>New Password:</label><br>
        <input type="password" name="new_password"><br><br>
        <input type="hidden" name="nocsrftoken" value="<?php echo htmlspecialchars($_SESSION['nocsrftoken']); ?>">
        <input type="submit" value="Change Password">
    </form>
</body>
</html>

```
## /editpost.php
```php
<?php
session_start();


if (!isset($_SESSION['authenticated']) or $_SESSION['authenticated'] != TRUE) {
        session_destroy();
        echo "<script>alert('Nice try!! You have to login first!')</script>";
        header("Refresh: 0; url=login.php");
        die();
    }

    if ($_SESSION['browser'] != $_SERVER["HTTP_USER_AGENT"]) {
    session_destroy();
    echo "<script>alert('Alert! Alert ! Session hijacking is detected')</script>";
    header("Refresh: 0; url=login.php");
    die();
}


if (!isset($_SESSION['nocsrftoken'])) {
    $_SESSION['nocsrftoken'] = bin2hex(openssl_random_pseudo_bytes(32)); // Generate a random token
}

// Validate CSRF token on form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tokenFromForm = $_POST['nocsrftoken'] ?? '';
    if (!hash_equals($_SESSION['nocsrftoken'], $tokenFromForm)) {
        die("CSRF Token Validation Failed.");
    }
}

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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Edit Post</h1>
    <form method="post">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>"><br>
        <label for="content">Content:</label><br>
        <textarea id="content" name="content"><?php echo htmlspecialchars($post['content']); ?></textarea><br>
        <input type="hidden" name="postID" value="<?php echo $postID; ?>">
          <input type="hidden" name="nocsrftoken" value="<?php echo htmlspecialchars($_SESSION['nocsrftoken']); ?>">
        <button type="submit" name="update">Update</button>
    </form>
</body>
</html>

```
## /index.php
```php
<?php
// Start session
session_start();
if (!isset($_SESSION['nocsrftoken'])) {
    $_SESSION['nocsrftoken'] = bin2hex(openssl_random_pseudo_bytes(32)); // Generate a random token
}

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 15 * 60,
    'path' => '/',
    'domain' => 'waph-team3.minifacebook.com',
    'secure' => true,
    'httponly' => true
]);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tokenFromForm = $_POST['nocsrftoken'] ?? '';
    if (!hash_equals($_SESSION['nocsrftoken'], $tokenFromForm)) {
        die("CSRF Token Validation Failed.");
    }
}



require "database.php";



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

if (isset($_POST["username"]) and isset($_POST["password"])){
        $username = htmlspecialchars($_POST["username"]); // Sanitize input
        $password = htmlspecialchars($_POST["password"]); // Sanitize input

        if (checklogin_mysql($username,$password)) {
            $_SESSION['authenticated'] = TRUE;
            $_SESSION['username'] = $_POST["username"];
            $_SESSION['browser'] = $_SERVER["HTTP_USER_AGENT"];
        }else{
            session_destroy();
            echo "<script>alert('Invalid password/username');window.location='form.php';</script>";
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



function fetchUsers() {
    global $mysqli;
    $users = [];

    // Prepare the SQL query with a parameter placeholder
    $sql = "SELECT username FROM users";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        // Execute the prepared statement
        $stmt->execute();

        // Bind result variables
        $stmt->bind_result($username);

        // Fetch rows and store usernames in $users array
        while ($stmt->fetch()) {
            $users[] = $username;
        }

        // Close the statement
        $stmt->close();
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
    <link rel="stylesheet" href="style.css">
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
                    echo '<input type="hidden" name="nocsrftoken" value="' . htmlspecialchars($_SESSION['nocsrftoken']) . '">';
                    echo "<input type='hidden' name='postID' value='" . $post['postID'] . "'>";
                    echo "<button class='btn' type='submit' name='edit'>Edit</button>";
                    echo "</form>";

                    echo "<form method='post' action='deletepost.php' onsubmit='return confirm(\"Are you sure you want to delete this post?\")'>";
                    echo '<input type="hidden" name="nocsrftoken" value="' . htmlspecialchars($_SESSION['nocsrftoken']) . '">';
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
                echo '<input type="hidden" name="nocsrftoken" value="' . htmlspecialchars($_SESSION['nocsrftoken']) . '">';
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
        <a class="btn" href="chat.php">Chat Room</a>

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

```
## /newpost.php
```php
<?php


session_start();  


if (!isset($_SESSION['authenticated']) or $_SESSION['authenticated'] != TRUE) {
        session_destroy();
        echo "<script>alert('Nice try!! You have to login first!')</script>";
        header("Refresh: 0; url=login.php");
        die();
    }

    if ($_SESSION['browser'] != $_SERVER["HTTP_USER_AGENT"]) {
    session_destroy();
    echo "<script>alert('Alert! Alert ! Session hijacking is detected')</script>";
    header("Refresh: 0; url=login.php");
    die();
}
require "database.php";

if (!isset($_SESSION['nocsrftoken'])) {
    $_SESSION['nocsrftoken'] = bin2hex(openssl_random_pseudo_bytes(32)); // Generate a random token
}

// Validate CSRF token on form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tokenFromForm = $_POST['nocsrftoken'] ?? '';
    if (!hash_equals($_SESSION['nocsrftoken'], $tokenFromForm)) {
        die("CSRF Token Validation Failed.");
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>New Post</h1>
    <form action="addnewpost.php" method="POST" onsubmit="return validateForm()">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br>
        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="4" required></textarea><br>
        <input type="hidden" name="nocsrftoken" value="<?php echo htmlspecialchars($_SESSION['nocsrftoken']); ?>">
        <input type="submit" value="Submit">
    </form>

    <script>
        function validateForm() {
            var title = document.getElementById("title").value;
            var content = document.getElementById("content").value;
            if (title.trim() === "" || content.trim() === "") {
                alert("Please fill out all fields.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
```
## /logout.php
```php
<?php
session_start();
session_destroy();
?>
<p> You are logout! </p>

<a href="form.php">Login again</a>
```
