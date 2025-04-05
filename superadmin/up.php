<?php
// up.php
include '../connection.php';

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];

    if ($type === 'edit') {
        // Update the record's 'action' field to an empty string
        $stmt = $conn->prepare("UPDATE bcp_sms3_req SET action = '' WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo "success";
        } else {
            http_response_code(500);
            echo "error";
        }
        $stmt->close();
    }
} else {
    http_response_code(400);
    echo "Missing parameters";
}
?>
