<?php
	
	//session_set_cookie_params(15*60,"/","192.168.33.128"TRUE,TRUE);
	session_set_cookie_params([
	    'lifetime' => 15*60,
	    'path' => '/',
	    'domain' => 'waph-team3.minifacebook.com',
	    'secure' => TRUE,
	    'httponly' => TRUE
	]);

	session_start();  

	require "database.php";
	if (isset($_POST["username"]) and isset($_POST["password"])){
		if (checklogin_mysql($_POST["username"],$_POST["password"])) {
			$_SESSION['authenticated'] = TRUE;
			$_SESSION['username'] = $_POST["username"];
			$_SESSION['browser'] = $_SERVER["HTTP_USER_AGENT"];
		}else{
			session_destroy();
			echo "<script>alert('Invalid username/password');window.location='form.php';</script>";
			die();
		}
	}
	if (!isset($_SESSION['authenticated']) or $_SESSION['authenticated'] != TRUE) {
		session_destroy();
		echo "<script>alert('You have not loggedin,please login first!')</script>";
		header("Refresh: 0; url=form.php");
		die();
	}

	if ($_SESSION['browser'] != $_SERVER["HTTP_USER_AGENT"]) {
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
        $posts = fetchPosts();
        if (!empty($posts)) {
            foreach ($posts as $post) {
                echo "<div class='post'>";
                echo "<h3>Title: " . $post['title'] . "</h3>";
                echo "<p>Content: " . $post['content'] . "</p>";
                echo "<p>Posted by: " . $post['owner'] . "</p>";
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
    </div>
</body>
</html>