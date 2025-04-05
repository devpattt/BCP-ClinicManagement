<?php
// delete_file.php
include '../connection.php';

if(isset($_POST['unique_id'])){
  $uniqueId = $_POST['unique_id'];
  $stmt = $conn->prepare("DELETE FROM bcp_sms3_send_integ WHERE unique_id = ?");
  $stmt->bind_param("s", $uniqueId);
  if($stmt->execute()){
    echo "Success";
  } else {
    http_response_code(500);
    echo "Error: " . $stmt->error;
  }
  $stmt->close();
} else {
  http_response_code(400);
  echo "Invalid Request";
}

$conn->close();
?>
