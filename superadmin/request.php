<?php
session_start();


if (!isset($_SESSION['username'])) {
    header("Location: superadmin/mainpage.php");
    exit();
}

include '../connection.php';
include '../fetchfname.php'; // Ensures $fullname is set
include 'fetch_medicine.php'; // Fetches medicine names for autocompletes



if ($_SERVER["REQUEST_METHOD"] == "POST") {
  error_log("Request received: " . json_encode($_POST)); // Debugging line

  $item = htmlspecialchars(trim($_POST['item']));
  $quantity = intval($_POST['quantity']);
  $category = htmlspecialchars(trim($_POST['category']));
  $action = 'Pending'; // Based on your database schema

  if (empty($fullname) || empty($item) || empty($category) || $quantity <= 0) {
      echo json_encode(['status' => 'error', 'message' => 'All fields are required and quantity must be greater than 0.']);
      exit();
  }

  $query = "INSERT INTO bcp_sms3_reqsupplies (name, item, category, quantity, action) VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($query);

  if (!$stmt) {
      echo json_encode(['status' => 'error', 'message' => 'SQL Prepare failed: ' . $conn->error]);
      exit();
  }

  $stmt->bind_param("sssis", $fullname, $item, $category, $quantity, $action);

  if ($stmt->execute()) {
      echo json_encode(['status' => 'success', 'message' => 'Record inserted successfully.']);
  } else {
      echo json_encode(['status' => 'error', 'message' => 'Error inserting record: ' . $stmt->error]);
  }

  $stmt->close();
  $conn->close();
  exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System / Add Records</title>
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
  <link href="../https://maxcdn.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/forms.css" rel="stylesheet">

<!-- RECOMMENDATION FOR ITEM NAME -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">

<style>
 
 #itemName {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

.dropdown-content {
    position: absolute;
    background: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    max-height: 200px;
    overflow-y: auto;
    width: 100%;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

.dropdown-content div {
    padding: 10px;
    cursor: pointer;
    font-size: 16px;
}

.dropdown-content div:hover {
    background: #f0f0f0;
}


  </style>

</head>
<body>
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
      <ul id="system-nav" class="nav-content collapse show " data-bs-parent="#sidebar-nav">
      <li>
          <a href="clinic-dashboard.php">
            <i class="bi bi-circle" ></i><span>Report and Analytics</span>
          </a>
        </li>
        <li>
          <a href="request.php" class="active">
            <i class="bi bi-circle" ></i><span>Request Supply</span>
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
        <a href="medical-supplies.php">
            <i class="bi bi-circle" ></i><span>Medical Supplies</span>
          </a>
        </li>
          <li>
            <a href="blankanomaly.php">
              <i class="bi bi-circle" ></i><span>A.I Anomaly</span>
            </a>
          </li>
          <li>
          <a href="integ.php" >
            <i class="bi bi-circle" ></i><span>Patient Reports</span>
          </a>
        </li>
          <li></li>
      </ul>
    </li>
  <hr class="sidebar-divider">
</aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Request Page</h1>
      <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Request Supplies</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

  
    <section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Patient Basic Information</h5>

          <!-- Request Modal -->
          <div class="container mt-5">
        <button class="btn btn-primary mb-3" style="background-color: #1e3a8a; border-color: #1e3a8a;" data-bs-toggle="modal" data-bs-target="#addSupplyModal">Request</button>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Request Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
              <?php
              
              include 'viewsupplies.php';

              ?>
            </tbody>
        </table>
    </div>

  <!-- Add Supply Modal -->
  <div class="modal fade" id="addSupplyModal" tabindex="-1" aria-labelledby="addSupplyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addSupplyForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" id="itemName" name="item" placeholder="Search Medicine..." autocomplete="off">
                            <div id="itemNameDropdown" class="dropdown-content"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Medicine">Medicine</option>
                                <option value="Equipment">Equipment</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="requestSupplyButton">Request Supply</button>

                    </div>
                </div>
            </form>
        </div>
    </div>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
  <script src="../assets/js/main.js"></script>


  <script>
$(document).ready(function () {
    $("#addSupplyForm").submit(function (event) {
        event.preventDefault(); // Prevent default form submission

        let formData = {
            item: $("#itemName").val(),
            category: $("#category").val(),
            quantity: $("#quantity").val()
        };

        $.ajax({
            url: "request.php", // Ensure this points to the correct PHP script
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    alert("Request submitted successfully!");
                    $("#addSupplyModal").modal("hide"); // Hide modal
                    $("#addSupplyForm")[0].reset(); // Reset form fields

                    // Prepend the new request at the top of the table
                    $("#supplyTableBody").prepend(`
                        <tr>
                            <td>${response.data.item}</td>
                            <td>${response.data.category}</td>
                            <td>${response.data.quantity}</td>
                            <td>${response.data.request_date}</td>
                            <td>Pending</td>
                        </tr>
                    `);
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
                alert("Something went wrong. Please try again.");
            }
        });
    });
});

</script>

  

<!-- RECOMMENDATION FOR MEDICINE NAMES -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $("#itemName").on("input", function () {
        let query = $(this).val();

        if (query.length > 1) {
            $.ajax({
                url: "fetch_medicine.php",
                method: "GET",
                data: { query: query },
                dataType: "json",
                success: function (data) {
                    let dropdown = $("#itemNameDropdown");
                    dropdown.empty().show();

                    if (data.length > 0) {
                        data.forEach(function (medicine) {
                            dropdown.append(`<div class="dropdown-item">${medicine}</div>`);
                        });
                    } else {
                        dropdown.append('<div class="dropdown-item">No medicine found</div>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", error);
                }
            });
        } else {
            $("#itemNameDropdown").hide();
        }
    });

    // Select item from dropdown
    $(document).on("click", ".dropdown-item", function () {
        $("#itemName").val($(this).text());
        $("#itemNameDropdown").hide();
    });

    // Hide dropdown when clicking outside
    $(document).click(function (e) {
        if (!$(e.target).closest("#itemName, #itemNameDropdown").length) {
            $("#itemNameDropdown").hide();
        }
    });
});
</script>





</script>

</body>



</html>