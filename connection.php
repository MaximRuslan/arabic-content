<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "tehmina_ibad";

// $servername = "mysql1008.mochahost.com";
// $username = "tehmina_ibad";
// $password = "2S]_+&6Lb5CF";
// $database = "tehmina_ibad";

// Create connection
$conn = mysqli_connect($servername, $username, $password,$database);
mysqli_set_charset($conn, 'utf8');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
else{
	session_start();
}
?>