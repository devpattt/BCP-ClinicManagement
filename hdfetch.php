<?php
include 'connection.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';

switch ($filter) {
    case 'month':
        $currentSql = "SELECT COUNT(*) as count FROM bcp_sms3_patients WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $previousSql = "SELECT COUNT(*) as count FROM bcp_sms3_patients WHERE MONTH(created_at) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(CURDATE() - INTERVAL 1 MONTH)";
        break;
    case 'year':
        $currentSql = "SELECT COUNT(*) as count FROM bcp_sms3_patients WHERE YEAR(created_at) = YEAR(CURDATE())";
        $previousSql = "SELECT COUNT(*) as count FROM bcp_sms3_patients WHERE YEAR(created_at) = YEAR(CURDATE() - INTERVAL 1 YEAR)";
        break;
    case 'today':
    default:
        $currentSql = "SELECT COUNT(*) as count FROM bcp_sms3_patients WHERE DATE(created_at) = CURDATE()";
        $previousSql = "SELECT COUNT(*) as count FROM bcp_sms3_patients WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY";
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
        $response['increase'] = 'text-danger'; 
    }
} else if ($response['count'] > 0) {
    $response['percentage'] = 100; 
}

$conn->close();

// Return the result as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>