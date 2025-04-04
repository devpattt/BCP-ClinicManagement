<?php
// get_student_suggestions.php

include '../connection.php'; // adjust if needed

if (isset($_GET['student_number'])) {
    $searchTerm = $_GET['student_number'];

    // Query your table for partial matches on student_number
    $stmt = $conn->prepare("SELECT student_number, first_name, middle_name, last_name, birthday,contact_number, year_level, sex, department_code 
                            FROM bcp_sms3_students 
                            WHERE student_number LIKE CONCAT('%', ?, '%') 
                            LIMIT 10");
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    // Return JSON
    header('Content-Type: application/json');
    echo json_encode($students);
} else {
    // Return empty JSON if no parameter
    echo json_encode([]);
}
?>
