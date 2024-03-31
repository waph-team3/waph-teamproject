
<?php
$mysqli = new mysqli('localhost', 'waph_team0’ /*Database username*/, 'waph@UC!2024' /*Database password*/, 'waph_team' /*
Database name*/);
if($mysqli->connect_errno) {
printf("Database connection failed: %s\n", $mysqli->connect_error);
return FALSE;
}
function addnewuser ($username, $password) {
global $mysqli;
$prepared_sql = "INSERT INTO users (username, password) VALUES (?, md5(?));";
$stmt = $mysqli->prepare($prepared_
$stmt->bind_param("ss", $username, $password);
if ($stmt->execute()) return TRUE;
return FALSE;
}

function checklogin_mysql($username, $password) {
global $mysqli;
$prepared_sql = "SELECT *FROM users WHERE username= ? AND password=md5(?);";
$stmt = $mysqli->prepare($prepared_
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result-$stmt->get_result();
if ($result->num_rows ==1) return TRUE;
return FALSE;
}
