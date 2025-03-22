<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../superadmin/mainpage.php");
    exit();
}

include '../../fetchfname.php';
include '../../connection.php';
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$uniqueId = isset($_GET['unique_id']) ? htmlspecialchars($_GET['unique_id']) : "";

// Allowed file extensions (TXT, DOC, and PDF)
$allowedTypes = ['txt', 'doc', 'pdf'];

// Directory to store uploaded files (ensure this directory exists and is writable)
$uploadDir = '../uploads/';

// If the form is submitted via POST, process the file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if a file was selected
    if (!isset($_FILES['document']) || $_FILES['document']['error'] === UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('No file selected. Please choose a file to upload.'); window.location.href='';</script>";
        exit();
    }
    
    // Check for any upload error.
    if ($_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('An upload error occurred.'); window.location.href='';</script>";
        exit();
    }
    
    $fileName    = trim($_FILES['document']['name']);
    $fileTmpPath = $_FILES['document']['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedTypes)) {
        echo "<script>alert('Invalid file type. Only TXT, DOC, and PDF allowed.'); window.location.href='';</script>";
        exit();
    }
    
    // Create a new unique file name to avoid conflicts
    $newFileName = $uniqueId . '_' . time() . '.' . $fileExtension;
    $destination = $uploadDir . $newFileName;
    
    // Move the uploaded file to the target directory
    if (!move_uploaded_file($fileTmpPath, $destination)) {
        echo "<script>alert('Failed to move uploaded file.'); window.location.href='';</script>";
        exit();
    }
    
    // Prepare SQL to insert file details into the database.
    // Make sure your table (e.g., file_uploads) exists with the columns: unique_id, file_name, file_type, file_path, created_at.
    $sql = "INSERT INTO file_uploads (unique_id, file_name, file_type, file_path, created_at) VALUES (?, ?, ?, ?, NOW())";
    if ($stmt = $conn->prepare($sql)) {
        // Bind the unique id, original file name, file type, and the destination path.
        if ($stmt->bind_param("ssss", $uniqueId, $fileName, $fileExtension, $destination)) {
            if ($stmt->execute()) {
                echo "<script>alert('File uploaded and stored successfully!'); window.location.href='integ.php';</script>";
            } else {
                echo "<script>alert('Error executing statement: " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('Error binding parameters: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
         echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clinic Management System / Upload Patient Record</title>
  <link href="../../assets/img/bcp logo.png" rel="icon">
  <link href="../../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../../assets/css/style.css" rel="stylesheet">
  <style>
    .pagetitle { margin: 80px 0 20px; }
    .sidebar .logo-container { text-align: center; margin-bottom: 10px; }
    .card-header { background: #f7f7f7; border-bottom: 1px solid #e3e3e3; }
    .card-header .back-button { text-decoration: none; }
  </style>
</head>
<body>
  <!-- Header and Sidebar (unchanged) -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../../assets/img/default profile.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($fullname); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header"></li>
            <li><hr class="dropdown-divider"></li>
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
  
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <div class="logo-container">
        <img src="../../assets/img/bcp logo.png" alt="Logo" style="width: 100px; height: auto;">
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
  
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Upload Patient Record</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Upload Record</li>
        </ol>
      </nav>
      <p class="text-muted">Reference: <?php echo $uniqueId; ?></p>
    </div>
    
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <a href="receive.php?unique_id=<?php echo $uniqueId; ?>" class="btn btn-secondary back-button">
                <i class="bi bi-arrow-left"></i> Back
              </a>
              <h5 class="mb-0">Upload TXT, DOC, or PDF</h5>
              <div></div>
            </div>
            <div class="card-body">
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="document" class="form-label">Select Document:</label>
                  <input type="file" name="document" id="document" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
              </form>
              <!-- This form uploads a TXT, DOC, or PDF file to be stored in the database. -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  
  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/js/main.js"></script>
</body>
</html>
