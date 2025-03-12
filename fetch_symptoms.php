<?php
include 'connection.php'; 

if (isset($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";
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