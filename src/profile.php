<?php
require "database.php";


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
        <input type="submit" value="Update Profile">
    </form>
</body>
</html>
