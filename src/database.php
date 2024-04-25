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
		$mysqli = new mysqli('localhost','team3','1234','waph_team');
		if($mysqli->connect_errno){
			printf("Database connection failed: %s\n", $mysqli->connect_errno);
			exit();
		}
		$prepared_sql = "SELECT * FROM users WHERE username=? AND password = md5(?)";
		$stmt = $mysqli->prepare($prepared_sql);
		$stmt->bind_param("ss", $username, $password);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows ==1)
			return TRUE;
		return FALSE;
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


function fetchPosts() {
    global $mysqli;
    $posts = array();

    $sql = "SELECT * FROM posts";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
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
