<?php
// get_categories.php

// Include your database connection file
include 'connection.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Query to fetch distinct category values from the medical supplies table
$sql = "SELECT DISTINCT category FROM bcp_sms3_medicalsupplies";

// Execute the query
$result = $conn->query($sql);

if ($result) {
    $categories = [];
    // Fetch each distinct category as an associative array
    while ($row = $result->fetch_assoc()) {
        // Each option's value and text will be the category itself
        $categories[] = $row['category'];
    }
    // Output the categories as JSON
    echo json_encode($categories);
} else {
    // Return a JSON error message in case of a query error
    echo json_encode(['error' => 'Error fetching categories: ' . $conn->error]);
}
