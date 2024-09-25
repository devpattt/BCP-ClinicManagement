<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accountId = filter_input(INPUT_POST, 'AccountId', FILTER_SANITIZE_STRING);
    
    if ($accountId) {
        $stmt = $conn->prepare("SELECT account_id FROM heads_db WHERE account_id = ?");
        $stmt->bind_param("s", $accountId);
        $stmt->execute();
        $stmt->store_result();

        $exists = $stmt->num_rows > 0;
        $stmt->close();
    } else {
        $exists = true; // Invalid account ID considered as existing
    }

    $conn->close();
    echo json_encode(['exists' => $exists]);
}
?>
