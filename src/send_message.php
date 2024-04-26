<?php
// Include database configuration
require "database.php";

// Function to send a message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message']) && isset($_POST['sender']) && isset($_POST['receiver'])) {
    $message = $_POST['message'];
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    
    // Insert the message into the database
    $stmt = $mysqli->prepare("INSERT INTO chat (sender, receiver, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $sender, $receiver, $message);
    $stmt->execute();
    $stmt->close();
}
?>
