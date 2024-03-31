<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>WAPH-Login page</title>
  <script type="text/javascript">
      function displayTime() {
        document.getElementById('digit-clock').innerHTML = "Current time:" + new Date();
      }
      setInterval(displayTime,500);
  </script>
</head>
<body>
  <h1>A Simple login form, WAPH</h1>
  <h2>Team-3</h2>
  <div id="digit-clock"></div>  
<?php
  //some code here
  echo "Visited time: " . date("Y-m-d h:i:sa")
?>
    <form action="index.php" method="POST" class="form login">
    Username: <input type="email" class="text_field" name="username" required pattern="^[\w.-]+@[\w-]+(.[\w-]+)*$" title="Email address is required as username" placeholder="Username in email" onchange="this.setCustomValidity(this.validity.patternMismatch ? this.title : '');"> <br>
    Password: <input type="password" class="text_field" name="password"> <br>
    <button class="button" type="submit">Login</button>
  </form>
</body>
</html>