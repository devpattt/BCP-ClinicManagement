<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bcp_sms3_cms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Now you can use $conn to prepare statements
?>
