<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>New Post</h1>
    <form action="addnewpost.php" method="POST" onsubmit="return validateForm()">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br>
        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="4" required></textarea><br>
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