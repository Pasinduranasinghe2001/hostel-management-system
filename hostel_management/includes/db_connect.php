<?php
$host = "localhost";
$dbname = "hostel_management";
$username = "root";
$password = ""; // Update if you have a DB password

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
