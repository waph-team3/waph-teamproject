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
