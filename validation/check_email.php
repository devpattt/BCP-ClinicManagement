<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    
    if ($email) {
        $stmt = $conn->prepare("SELECT email FROM heads_db WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        $exists = $stmt->num_rows > 0;
        $stmt->close();
    } else {
        $exists = true; // Invalid email considered as existing
    }

    $conn->close();
    echo json_encode(['exists' => $exists]);
}
?>
