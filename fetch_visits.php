<?php
header('Content-Type: application/json');

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

// Set the start and end date for your range
$startDate = new DateTime('2024-01-01'); // Start date
$endDate = new DateTime(); // Current date

// Query to get visit data based on the created_at month
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as visit_month, COUNT(*) as visit_count FROM bcp_sms3_patients GROUP BY visit_month ORDER BY visit_month";
$result = $conn->query($sql);

$visitData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $visitData[$row['visit_month']] = (int)$row['visit_count'];
    }
}

// Prepare data for all months
$allMonthsData = [];
$monthNames = [
    '01' => 'January', '02' => 'February', '03' => 'March',
    '04' => 'April', '05' => 'May', '06' => 'June',
    '07' => 'July', '08' => 'August', '09' => 'September',
    '10' => 'October', '11' => 'November', '12' => 'December'
];

for ($date = clone $startDate; $date <= $endDate; $date->modify('+1 month')) {
    $formattedMonth = $date->format('Y-m');
    $year = $date->format('Y');
    $monthNumber = $date->format('m'); // Get the month number

    $allMonthsData[] = [
        'month' => $monthNames[$monthNumber] . " $year", // Use month name with the year
        'count' => isset($visitData[$formattedMonth]) ? $visitData[$formattedMonth] : 0 // Default to 0 if no visits
    ];
}

$conn->close();

// Return JSON response
echo json_encode($allMonthsData);
?>
