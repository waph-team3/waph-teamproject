<?php
// Include database configuration
require "database.php";

// Fetch chat history for a specific user
if(isset($_GET['receiver'])) {
    $receiver = $_GET['receiver'];
    $sender = $_SESSION['username']; // Get current user dynamically

    // Fetch chat history from the database
    $stmt = $mysqli->prepare("SELECT sender, message FROM chat WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?) ORDER BY timestamp");
    $stmt->bind_param("ssss", $sender, $receiver, $receiver, $sender);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode($messages);
} else {
    echo json_encode([]);
}
?>
