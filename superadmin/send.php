<?php
ob_start(); // Start output buffering
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../superadmin/mainpage.php");
    exit();
}

include '../connection.php';
include '../fetchfname.php';

// Get the unique_id from the URL
$uniqueId = isset($_GET['unique_id']) ? htmlspecialchars($_GET['unique_id']) : "";

// Check if the request is made via AJAX.
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("No file uploaded or an upload error occurred.");
        }
        
        $fileName    = trim($_FILES['document']['name']);
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Allowed file extensions.
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Invalid file type. Only PDF, DOC, and DOCX files are allowed.");
        }
        
        // Read file content (for the sake of reference check only; we won't store it in the DB)
        // (You could remove this if you don't need to validate file content.)
        $fileContent = file_get_contents($fileTmpPath);
        if ($fileContent === false) {
            throw new Exception("Failed to read file content.");
        }
        
        // --- Reference Check ---
        // Extract the reference from the file name.
        // Example: "MyDocument (ABC123).pdf" yields reference "ABC123"
        if (strpos($fileName, '(') === false || strpos($fileName, ')') === false) {
            throw new Exception("No reference found in the file name. Please include the reference in parentheses.");
        }
        $startPos = strpos($fileName, '(');
        $endPos   = strpos($fileName, ')', $startPos);
        $fileReference = trim(substr($fileName, $startPos + 1, $endPos - $startPos - 1));
        if ($fileReference !== $uniqueId) {
            throw new Exception("Invalid file. The reference in the file name does not match.");
        }
        
        // Get the Reason input.
        $reason = isset($_POST['reason']) ? trim($_POST['reason']) : "";
        if (empty($reason)) {
            throw new Exception("Please enter a reason.");
        }
        
        // Optional: Sanitize file name for disk storage.
        $fileNameCmps = pathinfo($fileName);
        $newFileName = preg_replace('/[^a-zA-Z0-9_-]/', '', $fileNameCmps['filename']) . '.' . $fileNameCmps['extension'];
        
        // Set the upload folder.
        $uploadFileDir = '../uploads/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }
        $dest_path = $uploadFileDir . $newFileName;
        if (!move_uploaded_file($fileTmpPath, $dest_path)) {
            throw new Exception("There was an error moving the uploaded file.");
        }
        
        // Insert only the unique_id, file name, and reason into the database.
        $sql = "INSERT INTO bcp_sms3_send_integ (unique_id, request, reason, sent_date)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    request = VALUES(request), 
                    reason = VALUES(reason), 
                    sent_date = NOW()";
                    
        if (!($stmt = $conn->prepare($sql))) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        // Bind parameters: unique_id (s), request (s), reason (s)
        $stmt->bind_param("sss", $uniqueId, $newFileName, $reason);
        
        if (!$stmt->execute()) {
            throw new Exception("Insertion error: " . $stmt->error);
        }
        $stmt->close();
        
        // Update process status in the related table.
        $updateSQL = "UPDATE bcp_sms3_reqmedreqcord SET process='Done' WHERE unique_id = '" . $uniqueId . "'";
        if (!$conn->query($updateSQL)) {
            throw new Exception("File stored but update failed: " . $conn->error);
        }
        
        ob_clean();
        $response = ['status' => 'success', 'message' => 'File Sent Successfully!.'];
    } catch (Exception $e) {
        ob_clean();
        $response = ['status' => 'error', 'message' => $e->getMessage()];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System / Send Document with Reason</title>
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <script type="text/javascript" src="../assets/js/darkmode.js" defer></script>
  <style>
    .pagetitle { margin: 80px 0 20px; }
  </style>
</head>
<body>
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../assets/img/default profile.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($fullname); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>Administrator</h6>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="../logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <div class="logo-container" style="text-align: center; margin-bottom: 10px;">
        <img src="../assets/img/bcp logo.png" alt="Logo" style="width: 100px; height: auto;">
      </div>
      <hr class="sidebar-divider">
      <li class="nav-heading">Clinic Management System</li>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#system-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-hospital"></i><span>Clinic Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="system-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li><a href="clinic-dashboard.php"><i class="bi bi-circle"></i><span>Report and Analytics</span></a></li>
          <li><a href="request.php"><i class="bi bi-circle"></i><span>Request Supply</span></a></li>
          <li><a href="forms-elements.php"><i class="bi bi-circle"></i><span>Patient Registration</span></a></li>
          <li><a href="tables-data.php"><i class="bi bi-circle"></i><span>Patient Medical Records</span></a></li>
          <li><a href="medical-supplies.php"><i class="bi bi-circle"></i><span>Medical Supplies</span></a></li>
          <li><a href="blankanomaly.php"><i class="bi bi-circle"></i><span>A.I Anomaly</span></a></li>
          <li><a href="integ.php"><i class="bi bi-circle"></i><span>Patient Reports</span></a></li>
        </ul>
      </li>
      <hr class="sidebar-divider">
    </ul>
  </aside>

  <!-- ======= Main Content ======= -->
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Send Document with Reason</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Send Document</li>
        </ol>
      </nav>
      <p class="text-muted">Reference: <?php echo $uniqueId; ?></p>
      <!-- Back Button -->
      <div class="mb-3">
        <a href="generatePDF.php?unique_id=<?php echo $uniqueId; ?>" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Back
        </a>
      </div>
    </div>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <!-- The form will be submitted via AJAX -->
              <form id="sendForm" action="send.php?unique_id=<?php echo $uniqueId; ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="document" class="form-label">Select Document (PDF, DOC, or DOCX):</label>
                  <input type="file" name="document" id="document" class="form-control" accept=".pdf,.doc,.docx" required>
                </div>
                <div class="mb-3">
                  <label for="reason" class="form-label">Remarks:</label>
                  <input type="text" name="reason" id="reason" class="form-control" placeholder="Enter reason here" required>
                </div>
                <!-- Open the confirmation modal instead of submitting directly -->
                <button type="button" class="btn btn-primary" id="openConfirmModal">Send</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Confirmation Modal -->
  <div class="modal fade" id="confirmSendModal" tabindex="-1" aria-labelledby="confirmSendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmSendModalLabel">Confirm Transaction</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to send the document and reason?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <!-- This button triggers the AJAX submission -->
          <button type="button" class="btn btn-primary" id="confirmSendButton">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Result Modal: displays success or error message -->
  <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="resultModalLabel">Result</h5>
          <button type="button" class="btn-close" id="resultCloseButton" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="resultModalBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="redirectButton">Close</button>
        </div>
      </div>
    </div>
  </div>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
  
  <script>
    // Open the confirmation modal when "Send" is clicked.
    document.getElementById('openConfirmModal').addEventListener('click', function() {
      var confirmModal = new bootstrap.Modal(document.getElementById('confirmSendModal'));
      confirmModal.show();
    });

    // When the user clicks "Confirm", submit the form via AJAX.
    document.getElementById('confirmSendButton').addEventListener('click', function() {
      var form = document.getElementById('sendForm');
      var formData = new FormData(form);

      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(response => response.text())
      .then(text => {
        console.log("Raw response:", text);
        let data = JSON.parse(text);
        var confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmSendModal'));
        confirmModal.hide();
        document.getElementById('resultModalBody').textContent = data.message;
        document.getElementById('resultModalLabel').textContent = (data.status === 'success') ? "Success" : "Error";
        var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
        resultModal.show();
        if (data.status === 'success') {
          // Delay redirection for 2 seconds to allow the modal to be seen.
          setTimeout(function(){
            window.location.href = "integ.php";
          }, 2000);
        }
      })
      .catch(error => {
        console.error("AJAX error:", error);
        var confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmSendModal'));
        confirmModal.hide();
        document.getElementById('resultModalBody').textContent = "An unexpected error occurred: " + error.message;
        document.getElementById('resultModalLabel').textContent = "Error";
        var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
        resultModal.show();
      });
    });

    document.getElementById('redirectButton').addEventListener('click', function() {
      window.location.href = "integ.php";
    });
    document.getElementById('resultCloseButton').addEventListener('click', function() {
      window.location.href = "integ.php";
    });
  </script>
</body>
</html>
