<?php
// update_send.php
include '../connection.php';

// Retrieve POST data (consider adding sanitization/validation in production)
$reference = $_POST['reference'];
$pdfFile   = $_POST['pdfFile'];
$remarks   = $_POST['remarks'];

// Update status to "Done" in bcp_sms3_send_integ table
$updateQuery = "UPDATE bcp_sms3_send_integ SET status = 'Done' WHERE unique_id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("s", $reference);
if (!$stmt->execute()) {
    http_response_code(500);
    echo "Error updating status: " . $stmt->error;
    exit;
}
$stmt->close();

// Insert new record into bcp_sms3_aff table
$insertQuery = "INSERT INTO bcp_sms3_aff (unique_id, file, remarks, sent_at) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("sss", $reference, $pdfFile, $remarks);
if (!$stmt->execute()) {
    http_response_code(500);
    echo "Error inserting record: " . $stmt->error;
    exit;
}
$stmt->close();

$conn->close();
echo "Success";
?>
