<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>WAPH-Login page</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 400px;
      margin: 50px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h1, h2 {
      text-align: center;
    }
    #digit-clock {
      text-align: center;
      margin-bottom: 20px;
    }
    .form {
      text-align: center;
    }
    .text_field, .button {
      padding: 10px;
      margin: 10px;
      width: 80%;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .button {
      background-color: #007bff;
      color: #fff;
      border: none;
      cursor: pointer;
    }
    .button:hover {
      background-color: #0056b3;
    }
  </style>
  <script type="text/javascript">
      function displayTime() {
        document.getElementById('digit-clock').innerHTML = "Current time:" + new Date();
      }
      setInterval(displayTime, 500);
  </script>
</head>
<body>
  <div class="container">
    <h1>A Simple login form, WAPH</h1>
    <h2>Team-3</h2>
    <div id="digit-clock"></div>
    <?php
      // PHP code to display visited time
      echo "Visited time: " . date("Y-m-d h:i:sa");
    ?>
    <form action="index.php" method="POST" class="form login">
      Username: <input type="text" class="text_field" name="username" /> <br>
      Password: <input type="password" class="text_field" name="password" /> <br>
      <button class="button" type="submit">Login</button>
    </form>
  </div>
</body>
</html>
