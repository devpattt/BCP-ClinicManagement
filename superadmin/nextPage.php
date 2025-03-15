<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../superadmin/mainpage.php");
    exit();
}

include '../fetchfname.php';
include '../connection.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Function to generate a unique ID
function generateUniqueID($length = 8) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
}

// Function to extract text from DOCX
function extractTextFromDocx($filePath) {
    $content = '';
    if (file_exists($filePath)) {
        $zip = new ZipArchive;
        if ($zip->open($filePath) === TRUE) {
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();
            if ($xml) {
                $xml = str_replace('</w:p>', "\n", $xml);
                $content = strip_tags($xml);
            }
        }
    }
    return trim($content);
}

// Function to extract text from PDF using pdftotext
function extractTextFromPdf($filePath) {
    $output = shell_exec("pdftotext " . escapeshellarg($filePath) . " -");
    return trim($output);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $fileName = $_FILES['document']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedTypes = ['docx', 'pdf'];
        if (!in_array($fileExtension, $allowedTypes)) {
            echo "<script>alert('Invalid file type. Only DOCX and PDF allowed.');</script>";
        } else {
            $textContent = ($fileExtension == 'docx') ? extractTextFromDocx($fileTmpPath) : extractTextFromPdf($fileTmpPath);
            
            $data = array_map('trim', explode("\n", $textContent));
            if (count($data) >= 8) {
                $unique_id = generateUniqueID();
                list($fullname, $student_number, $contact, $s_gender, $age, $year_level, $conditions, $treatment) = array_slice($data, 0, 8);
                
                $age = intval($age);
                $sql = "INSERT INTO bcp_sms3_inter_patient (unique_id, fullname, student_number, contact, s_gender, age, year_level, conditions, treatment, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("sssssis", $unique_id, $fullname, $student_number, $contact, $s_gender, $age, $year_level, $conditions, $treatment);
                    if ($stmt->execute()) {
                        echo "<script>alert('File uploaded and data successfully inserted!'); window.location.href='integ.php';</script>";
                    } else {
                        echo "<script>alert('Database error: " . $stmt->error . "');</script>";
                    }
                    $stmt->close();
                } else {
                    echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
                }
            } else {
                echo "<script>alert('Invalid file format. Ensure all fields are present.');</script>";
            }
        }
    } else {
        echo "<script>alert('No file uploaded or an upload error occurred.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Patient Record</title>
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Upload Patient Record</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="document" class="form-label">Upload DOCX or PDF:</label>
                <input type="file" name="document" id="document" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
