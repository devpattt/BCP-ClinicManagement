<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../superadmin/mainpage.php");
    exit();
}

include '../connection.php';
include '../fetchfname.php';

$uniqueId = isset($_GET['unique_id']) ? htmlspecialchars($_GET['unique_id']) : "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process the uploaded file.
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $fileName    = trim($_FILES['document']['name']);
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        // Only allow TXT files.
        if ($fileExtension !== 'txt') {
            header("Location: send.php?unique_id=" . urlencode($uniqueId) . "&error=" . urlencode("Invalid file type. Only TXT files are allowed."));
            exit();
        }
        $fileContent = file_get_contents($fileTmpPath);
    } else {
        header("Location: send.php?unique_id=" . urlencode($uniqueId) . "&error=" . urlencode("No file uploaded or an upload error occurred."));
        exit();
    }
    
    // Get the Reason input.
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : "";
    if (empty($reason)) {
        header("Location: send.php?unique_id=" . urlencode($uniqueId) . "&error=" . urlencode("Please enter a reason."));
        exit();
    }
    
    // Insert the file content and reason into the database.
    $sql = "INSERT INTO bcp_sms3_send_integ (unique_id, request, reason, sent_date)
            VALUES (?, ?, ?, NOW())";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $uniqueId, $fileContent, $reason);
        if ($stmt->execute()) {
            // After successful insertion, update process status to 'Done' in the bcp_sms3_reqmedreqcord table.
            $updateSQL = "UPDATE bcp_sms3_reqmedreqcord 
                          SET process='Done' 
                          WHERE unique_id = '" . $uniqueId . "'";
            if ($conn->query($updateSQL)) {
                header("Location: integ.php?success=" . urlencode("File and reason stored; process updated to Sent."));
                exit();
            } else {
                header("Location: send.php?unique_id=" . urlencode($uniqueId) . "&error=" . urlencode("File stored but update failed: " . $conn->error));
                exit();
            }
        } else {
            header("Location: send.php?unique_id=" . urlencode($uniqueId) . "&error=" . urlencode("Insertion error: " . $stmt->error));
            exit();
        }
        $stmt->close();
    } else {
         header("Location: send.php?unique_id=" . urlencode($uniqueId) . "&error=" . urlencode("Error preparing statement: " . $conn->error));
         exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System / Send TXT with Reason</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="../https://fonts.gstatic.com" rel="preconnect">
  <link href="../https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <script type="javascript" src="../assets/js/darkmode.js" defer></script>
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
      <h1>Send TXT File with Reason</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Send TXT</li>
        </ol>
      </nav>
      <p class="text-muted">Reference: <?php echo $uniqueId; ?></p>
      <!-- Back Button placed immediately below the breadcrumb -->
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
              <form action="send.php?unique_id=<?php echo $uniqueId; ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="document" class="form-label">Select TXT File:</label>
                  <input type="file" name="document" id="document" class="form-control" accept=".txt" required>
                </div>
                <div class="mb-3">
                  <label for="reason" class="form-label">Reason:</label>
                  <input type="text" name="reason" id="reason" class="form-control" placeholder="Enter reason here" required>
                </div>
                <button type="submit" class="btn btn-primary">Send</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Modal for displaying messages -->
  <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="messageModalLabel">Message</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="messageModalBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    // When the document loads, check for error or success messages in the URL.
    document.addEventListener("DOMContentLoaded", function() {
      const urlParams = new URLSearchParams(window.location.search);
      const error = urlParams.get('error');
      const success = urlParams.get('success');
      if (error || success) {
          const messageModalBody = document.getElementById('messageModalBody');
          messageModalBody.textContent = error ? error : success;
          const messageModalLabel = document.getElementById('messageModalLabel');
          messageModalLabel.textContent = error ? "Error" : "Success";
          var messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
          messageModal.show();
      }
    });
  </script>
</body>
</html>
