<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bcpclinic_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine the date range
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';

switch ($filter) {
    case 'month':
        $currentSql = "SELECT COUNT(*) as count FROM patient_info WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
        $previousSql = "SELECT COUNT(*) as count FROM patient_info WHERE MONTH(date) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(date) = YEAR(CURDATE() - INTERVAL 1 MONTH)";
        break;
    case 'year':
        $currentSql = "SELECT COUNT(*) as count FROM patient_info WHERE YEAR(date) = YEAR(CURDATE())";
        $previousSql = "SELECT COUNT(*) as count FROM patient_info WHERE YEAR(date) = YEAR(CURDATE() - INTERVAL 1 YEAR)";
        break;
    case 'today':
    default:
        $currentSql = "SELECT COUNT(*) as count FROM patient_info WHERE DATE(date) = CURDATE()";
        $previousSql = "SELECT COUNT(*) as count FROM patient_info WHERE DATE(date) = CURDATE() - INTERVAL 1 DAY";
        break;
}

$currentResult = $conn->query($currentSql);
$previousResult = $conn->query($previousSql);

$response = array('count' => 0, 'percentage' => 0, 'increase' => 'text-success');

if ($currentResult->num_rows > 0) {
    $currentRow = $currentResult->fetch_assoc();
    $response['count'] = $currentRow['count'];
}

$previousCount = 0;
if ($previousResult->num_rows > 0) {
    $previousRow = $previousResult->fetch_assoc();
    $previousCount = $previousRow['count'];
}

if ($previousCount > 0) {
    $percentage = (($response['count'] - $previousCount) / $previousCount) * 100;
    $response['percentage'] = round($percentage, 2);

    if ($percentage < 0) {
        $response['percentage'] = abs($response['percentage']);
        $response['increase'] = 'text-danger'; // Use red for decrease
    }
} else if ($response['count'] > 0) {
    $response['percentage'] = 100; // If no previous data but current data exists
}

$conn->close();

// Return the result as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>