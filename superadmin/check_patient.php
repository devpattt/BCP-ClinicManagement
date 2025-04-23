<?php
header('Content-Type: application/json');
include '../connection.php';

$stu = $_POST['student_number'] ?? '';
if (!$stu) {
  echo json_encode(['exists'=>false]);
  exit;
}

$stmt = $conn->prepare("
  SELECT 1
  FROM bcp_sms3_patients
  WHERE student_number = ? 
  LIMIT 1
");
$stmt->bind_param('s', $stu);
$stmt->execute();
$res = $stmt->get_result();
echo json_encode(['exists' => $res->num_rows > 0]);
