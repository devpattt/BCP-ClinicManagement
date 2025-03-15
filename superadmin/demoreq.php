<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: superadmin/mainpage.php");
  exit(); 
}

include '../fetchfname.php';
include '../connection.php';

// Function to generate a random alphanumeric string of given length
function generateUniqueID($length = 8) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for($i = 0; $i < $length; $i++){
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Define allowed file types
$allowedTypes = [
  'application/pdf', 
  'application/msword', 
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Ensure a file was uploaded and there's no error.
  if (isset($_FILES['request_file']) && $_FILES['request_file']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['request_file']['tmp_name'];
    $fileName = $_FILES['request_file']['name'];
    $fileSize = $_FILES['request_file']['size'];
    $fileType = $_FILES['request_file']['type'];
    
    // Validate file type
    if (!in_array($fileType, $allowedTypes)) {
      $message = "Only PDF, DOC, and DOCX files are allowed.";
    } else {
      // Sanitize file name (optional but recommended)
      $fileNameCmps = pathinfo($fileName);
      $newFileName = preg_replace('/[^a-zA-Z0-9_-]/', '', $fileNameCmps['filename']) . '.' . $fileNameCmps['extension'];
      
      // Directory in which the uploaded file will be moved
      $uploadFileDir = '../uploads/';
      if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0777, true);
      }
      $dest_path = $uploadFileDir . $newFileName;
      
      if(move_uploaded_file($fileTmpPath, $dest_path)) {
        // Get the reason from the form input
        $reason = htmlspecialchars(trim($_POST['reason']));
        // Generate a unique id (random alphanumeric string)
        $unique_id = generateUniqueID();
        
        // Prepare the INSERT statement using the new table and field names.
        $sql = "INSERT INTO bcp_sms3_reqmedreqcord (unique_id, request, reason, action, process) VALUES (?, ?, ?, 'Pending', 'Pending')";
        if ($stmt = $conn->prepare($sql)) {
          // Bind parameters: unique_id, file name, and reason.
          $stmt->bind_param("sss", $unique_id, $newFileName, $reason);
          if ($stmt->execute()) {
            $message = "File is successfully uploaded and record saved.";
          } else {
            $message = "Database error: " . $stmt->error;
          }
          $stmt->close();
        } else {
          $message = "Error preparing statement: " . $conn->error;
        }
      } else {
        $message = "There was an error moving the uploaded file.";
      }
    }
  } else {
    $message = "No file uploaded or there was an upload error.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Add Request - Clinic Management System</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container mt-5">
    <h2 class="mb-4">Add New Request</h2>
    
    <?php if(isset($message)): ?>
      <div class="alert alert-info">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>
    
    <form action="" method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="request_file" class="form-label">Upload Request (PDF or DOC/DOCX):</label>
        <input type="file" name="request_file" id="request_file" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="reason" class="form-label">Reason:</label>
        <input type="text" name="reason" id="reason" class="form-control" placeholder="Enter reason" required>
      </div>
      <!-- The request_at field is automatically set to the current timestamp in the database -->
      <!-- The action and process fields default to 'Pending' -->
      <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
