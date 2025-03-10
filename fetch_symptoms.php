<?php
include 'connection.php'; // Ensure this connects to your database

if (isset($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%"; // Use wildcard for partial matching
    $stmt = $conn->prepare("SELECT symptoms FROM bcp_sms3_symptoms WHERE symptoms LIKE ? LIMIT 5");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $symptoms = [];
    while ($row = $result->fetch_assoc()) {
        $symptoms[] = $row['symptoms'];
    }

    echo json_encode($symptoms);
    $stmt->close();
    $conn->close();
}
?>
