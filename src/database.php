<?php
$mysqli = new mysqli('localhost', 'team3', '1234', 'waph_team');
	
if($mysqli->connect_errno) {
    printf("Database connection failed: %s\n", $mysqli->connect_error);
    return FALSE;
}

function addnewuser($username, $password) {
    global $mysqli;
    // Hash the password
    $hashed_password = md5($password);
    
    $prepared_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $mysqli->prepare($prepared_sql);
    $stmt->bind_param("ss", $username, $hashed_password); // Bind the hashed password
    if ($stmt->execute()) return TRUE;
    return FALSE;
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

?>
