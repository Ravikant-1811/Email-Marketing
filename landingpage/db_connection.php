<?php
$servername = "localhost";
$username = "root"; // Replace with DB username
$password = ""; // Replace with DB password
$dbname = "bulk_mailer";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
