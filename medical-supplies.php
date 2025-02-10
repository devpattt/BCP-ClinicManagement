<?php

session_start();

if (!isset($_SESSION['accountId'])) {
  header("Location: index.php");
  exit(); 
}
include 'connection.php';
include 'fetchfname.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));
    $quantity = htmlspecialchars(trim($_POST['quantity']));
    $unit = htmlspecialchars(trim($_POST['unit']));
    $stmt = $conn->prepare("INSERT INTO bcp_sms3_supplies (name, description, quantity, unit) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $description, $quantity, $unit);

      if ($stmt->execute()) {
          echo json_encode(['status' => 'success', 'message' => 'Record inserted successfully.']);
      } else {
          echo json_encode(['status' => 'error', 'message' => 'Error inserting record: ' . $stmt->error]);
      }

    $stmt->close();
    $conn->close();
    exit; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="assets/img/bcp logo.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <script type="javascript" src="assets/js/darkmode.js" defer></script>
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
          <img src="assets/img/default profile.jpg" alt="Profile" class="rounded-circle">
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
              <a class="dropdown-item d-flex align-items-center" href="logout.php">
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

      
    <ul class="sidebar-nav" id="sidebar-nav">
    <div class="logo-container" style="text-align: center; margin-bottom: 10px;">
    <img src="assets/img/bcp logo.png" alt="Logo" style="width: 100px; height: auto;">
    </div>

      <hr class="sidebar-divider">


      <li class="nav-heading">Clinic Management System</li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#system-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-hospital"></i><span>Clinic Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="system-nav" class="nav-content collapse show " data-bs-parent="#sidebar-nav">
          <li>
            <a href="clinic-dashboard.php">
              <i class="bi bi-circle"></i><span>Report and Analytics</span>
            </a>
          </li>
          <li>
            <a href="forms-elements.php">
              <i class="bi bi-circle"></i><span>Patient Registration</span>
            </a>
          </li>
          <li>
            <a href="tables-data.php">
              <i class="bi bi-circle"></i><span>Patient Medical Records</span>
            </a>
          </li>
          <li>
            <a href="medical-supplies.php" class="active">
              <i class="bi bi-circle"></i><span>Medical Supplies</span>
            </a>
          </li>
          <li>
            <a href="blankanomaly.php">
              <i class="bi bi-circle"></i><span>A.I Anomaly</span>
            </a>
          </li>
        </ul>
      </li>

  <hr class="sidebar-divider">
  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Medical Supplies Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Medical Supplies Management</li>
        </ol>
      </nav>
    </div>

  <section class="section dashboard">
  <div class="row">
    <div class="col-lg-24">
      <div class="row d-flex justify-content-between">

        <!-- Card 1: Patient Today -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card sales-card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>
                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">Out of stocks products</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-exclamation-triangle text-danger"></i>
                </div>
                <div class="ps-3">
                  <h6 id="today-count"></h6>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 2: Patient This Month -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card revenue-card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>
                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">Supplies on low stocks</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-exclamation-triangle text-primary"></i>
                </div>
                <div class="ps-3">
                  <h6 id="month-count"></h6>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 3: Patient This Year -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card customers-card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>
                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">Numbers of Products to be arrived</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-truck text-success"></i>
                </div>
                <div class="ps-3">
                  <h6 id="year-count"></h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

<div class="container mt-5">
        <button class="btn btn-primary mb-3" style="background-color: #1e3a8a; border-color: #1e3a8a;" data-bs-toggle="modal" data-bs-target="#addSupplyModal">Add Supply</button>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Minimum Stock</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="supplyTableBody"></tbody>
        </table>
    </div>

<!-- Add Supply Modal -->
<div class="modal fade" id="addSupplyModal" tabindex="-1" aria-labelledby="addSupplyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addSupplyForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplyModalLabel">Add Supply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="itemName" class="form-label">Item Name</label>
                        <input type="text" class="form-control custom-border" id="itemName" name="item_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select custom-border" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Medicine">Medicine</option>
                            <option value="Equipment">Equipment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control custom-border" id="quantity" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="minimumStock" class="form-label">Minimum Stock</label>
                        <input type="number" class="form-control custom-border" id="minimumStock" name="minimum_stock" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" style="background-color: #1e3a8a; border-color: #1e3a8a;" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #1e3a8a; border-color: #1e3a8a;">Add Supply</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Update Supply Modal -->
<div class="modal fade" id="updateSupplyModal" tabindex="-1" aria-labelledby="updateSupplyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="updateSupplyForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateSupplyModalLabel">Update Supply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="updateId" name="id">
                    <div class="mb-3">
                        <label for="updateItemName" class="form-label">Item Name</label>
                        <input type="text" class="form-control custom-border" id="updateItemName" name="item_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateCategory" class="form-label">Category</label>
                        <select class="form-select custom-border" id="updateCategory" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Medicine">Medicine</option>
                            <option value="Equipment">Equipment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="updateQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control custom-border" id="updateQuantity" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateMinimumStock" class="form-label">Minimum Stock</label>
                        <input type="number" class="form-control custom-border" id="updateMinimumStock" name="minimum_stock" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" style="background-color: #1e3a8a; border-color: #1e3a8a;" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #1e3a8a; border-color: #1e3a8a;">Update Supply</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-border {
        border: 2px solid black; 
        border-radius: 4px; 
    }

    .custom-border:focus {
        border-color: #333; 
        box-shadow: 0 0 5px rgba(51, 51, 51, 0.5); 
    }
</style>


  </main>


  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/supplies.js"></script>
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>

  <script> 
document.addEventListener("DOMContentLoaded", () => {
    const updateLowStockCounts = () => {
        fetch('fetchlowstocks.php')
            .then(response => response.json())
            .then(data => {
                const { lowStockCount } = data;

                document.getElementById('month-count').textContent = lowStockCount;

                if (lowStockCount === 0) {
                    document.getElementById('today-count').textContent = "0";
                } else {
                    document.getElementById('today-count').textContent = `${lowStockCount}`;
                }
            })
            .catch(error => console.error('Error fetching low-stock data:', error));
    };

    updateLowStockCounts();

    setInterval(updateLowStockCounts, 30000); 
});
document.addEventListener('DOMContentLoaded', function() {
    function updateProfileName() {
        fetch('fetch_uname.php') 
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const nameSpan = document.getElementById('fname');
                if (data.fname) {
                    nameSpan.textContent = data.fname; 
                } else if (data.error) {
                    console.error('Error from PHP script:', data.error);
                } else {
                    console.error('Unexpected response format');
                }
            })
            .catch(error => console.error('Error fetching name:', error));
    }

    updateProfileName(); 
});


  </script>

</body>

</html>