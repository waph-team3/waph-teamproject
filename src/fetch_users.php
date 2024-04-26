<?php
// Include database configuration
require "database.php";

// Function to fetch users
function fetchUsers() {
    global $mysqli;
    $users = [];
    $result = $mysqli->query("SELECT username FROM users");
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['username'];
    }
    echo json_encode($users);
}

// Fetch users and output JSON
fetchUsers();
?>
