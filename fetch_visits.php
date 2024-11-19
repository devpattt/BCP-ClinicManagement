<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bcp_sms3_cms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$startDate = new DateTime('2024-01-01'); 
$endDate = new DateTime(); 
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as visit_month, COUNT(*) as visit_count FROM bcp_sms3_patients GROUP BY visit_month ORDER BY visit_month";
$result = $conn->query($sql);

$visitData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $visitData[$row['visit_month']] = (int)$row['visit_count'];
    }
}

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
    $monthNumber = $date->format('m'); 

    $allMonthsData[] = [
        'month' => $monthNames[$monthNumber] . " $year", 
        'count' => isset($visitData[$formattedMonth]) ? $visitData[$formattedMonth] : 0
    ];
}

$conn->close();

echo json_encode($allMonthsData);
?>
