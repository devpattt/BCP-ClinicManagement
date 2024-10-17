<?php
$servername = "localhost";
$username = "clin_clinicb1";
$password = "50keHz!cflu9wkwk";
$dbname = "clin_bcp_sms3";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

