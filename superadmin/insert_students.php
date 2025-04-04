<?php
include '../connection.php'; // Adjust this according to your setup

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['students']) || !is_array($data['students'])) {
    echo json_encode(["success" => false, "message" => "Invalid data format"]);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO bcp_sms3_students (student_number, first_name, middle_name, last_name,birthday ,contact_number, year_level, sex, department_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($data['students'] as $student) {
    $stmt->bind_param("sssssssss", 
        $student['student_number'], 
        $student['first_name'], 
        $student['middle_name'], 
        $student['last_name'], 
        $student['birthday'],
        $student['contact_number'], 
        $student['year_level'], 
        $student['sex'], 
        $student['department_code']
    );
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo json_encode(["success" => true, "message" => "Students inserted successfully"]);
?>
