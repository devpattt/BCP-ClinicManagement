<?php
// Start session only if one isn't already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['username'])) {
    header("Location: superadmin/mainpage.php");
    exit();
}

include '../fetchfname.php';
include '../connection.php';

// Function to generate a unique ID that doesn't already exist in the database
function generateUniqueId($conn, $length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    do {
        $uniqueId = '';
        for ($i = 0; $i < $length; $i++) {
            $uniqueId .= $characters[random_int(0, $charactersLength - 1)];
        }
        
        // Prepare a query to check if the generated unique id already exists
        $checkQuery = "SELECT COUNT(*) as count FROM bcp_sms3_patients WHERE unique_id = ?";
        $stmt = $conn->prepare($checkQuery);
        if (!$stmt) {
            die("SQL Prepare Error (unique check): " . $conn->error);
        }
        $stmt->bind_param("s", $uniqueId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'];
        $stmt->close();
    } while ($count > 0);
    
    return $uniqueId;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize POST data
    $patient_selection = htmlspecialchars(trim($_POST['patient_selection']));
    
    // Check if the patient selection is "Student"
    if ($patient_selection === "Student") {
        // If Student, grab the student number (if provided)
        if (isset($_POST['student_number']) && !empty(trim($_POST['student_number']))) {
            $student_number = htmlspecialchars(trim($_POST['student_number']));
        } else {
            $student_number = "";
        }
        
        $first_name     = htmlspecialchars(trim($_POST['first_name']));
        $middle_name    = htmlspecialchars(trim($_POST['middle_name']));
        $last_name      = htmlspecialchars(trim($_POST['last_name']));
        // Create fullname as "Lastname, Firstname Middlename"
        $fullname = $last_name . ", " . $first_name . " " . $middle_name;
        $contact_number = htmlspecialchars(trim($_POST['contact_number']));
        
        // Retrieve birthday from the form
        $birthday = htmlspecialchars(trim($_POST['birthday']));
        
        $year_level      = htmlspecialchars(trim($_POST['year_level']));
        $sex             = htmlspecialchars(trim($_POST['sex']));
        $department_code = htmlspecialchars(trim($_POST['department_code']));
        
        // Only allow Male or Female for sex
        $allowed_genders = ['Male', 'Female'];
        if (!in_array($sex, $allowed_genders)) {
            die("Invalid gender selected.");
        }
        
        // Generate a unique ID
        $unique_id = generateUniqueId($conn);
        
        // Prepare the SQL statement for student patients including the patient column
        $stmt = $conn->prepare("INSERT INTO bcp_sms3_patients (unique_id, patient, fullname, student_number, contact_number, sex, birthday, year_level, department_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("SQL Prepare Error: " . $conn->error);
        }
        
        // Bind parameters: patient_selection now included along with other fields
        $stmt->bind_param("sssssssss", $unique_id, $patient_selection, $fullname, $student_number, $contact_number, $sex, $birthday, $year_level, $department_code);
    
        // Execute and check for errors
        if ($stmt->execute()) {
            echo "success";
        } else {
            die("Error inserting record: " . $stmt->error);
        }
        $stmt->close();
    } else {
        // For non-student patients, insert into the bcp_sms3_other_patients table.
        // For these records, we won't have a student number or year level.
        $first_name     = htmlspecialchars(trim($_POST['first_name']));
        $middle_name    = htmlspecialchars(trim($_POST['middle_name']));
        $last_name      = htmlspecialchars(trim($_POST['last_name']));
        // Create fullname as "Lastname, Firstname Middlename"
        $fullname = $last_name . ", " . $first_name . " " . $middle_name;
        $contact_number = htmlspecialchars(trim($_POST['contact_number']));
        
        // Retrieve birthday from the form (mapped to birthdate column)
        $birthday = htmlspecialchars(trim($_POST['birthday']));
        
        $sex             = htmlspecialchars(trim($_POST['sex']));
        $department_code = htmlspecialchars(trim($_POST['department_code']));
        
        // Only allow Male or Female for sex
        $allowed_genders = ['Male', 'Female'];
        if (!in_array($sex, $allowed_genders)) {
            die("Invalid gender selected.");
        }
        
        // Generate a unique ID (you may want to add a separate check here if needed)
        $unique_id = generateUniqueId($conn);
        
        // Set created_at to current date and time
        $created_at = date('Y-m-d H:i:s');
        
        // Prepare the SQL statement for other patients including the patient column
        $stmt = $conn->prepare("INSERT INTO bcp_sms3_other_patients (unique_id, patient, fullname, contact, birthdate, sex, department, create_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("SQL Prepare Error: " . $conn->error);
        }
        
        // Bind parameters: now including patient_selection as the patient value
        $stmt->bind_param("ssssssss", $unique_id, $patient_selection, $fullname, $contact_number, $birthday, $sex, $department_code, $created_at);
    
        // Execute and check for errors
        if ($stmt->execute()) {
            echo "success";
        } else {
            die("Error inserting record: " . $stmt->error);
        }
        $stmt->close();
    }
    
    $conn->close();
    exit();
}
?>
