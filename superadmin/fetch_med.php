<?php
include '../connection.php'; // Adjust if needed

header('Content-Type: application/json');

if (isset($_GET['brand'])) {
    $searchTerm = $_GET['brand'];

    // Prepared statement for partial matching in brand
    $stmt = $conn->prepare("
        SELECT brand, generic 
        FROM bcp_sms3_medicine 
        WHERE brand LIKE CONCAT('%', ?, '%') 
        LIMIT 10
    ");
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $medicines = [];
    while ($row = $result->fetch_assoc()) {
        $medicines[] = $row;
    }

    echo json_encode($medicines);
} else {
    // Return empty JSON if no parameter
    echo json_encode([]);
}
?>
