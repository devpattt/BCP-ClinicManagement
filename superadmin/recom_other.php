<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: superadmin/mainpage.php");
  exit();
}

include '../connection.php';
include '../fetchfname.php';

// ---------- Math Logic Functions for Stock Adjustments ----------

function updateStock($conn) {
    $query = "SELECT id, unit, quantity FROM bcp_sms3_medicalsupplies";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $unit = (int)$row['unit'];
        $quantity = (int)$row['quantity'];

        if ($quantity <= 0 && $unit > 0) {
            $newUnit = $unit - 1;
            $newQuantity = $quantity + 10; // Add 10 to quantity.
            if ($newQuantity < 0) {
                $newQuantity = 0;
            }
            $updateQuery = "UPDATE bcp_sms3_medicalsupplies 
                            SET unit = $newUnit, 
                                quantity = $newQuantity
                            WHERE id = $id";
            $conn->query($updateQuery);
        }
    }
}

function adjustExcessQuantity($conn) {
    $updateQuery = "UPDATE bcp_sms3_medicalsupplies 
                    SET unit = unit + (quantity DIV 10), 
                        quantity = quantity % 10
                    WHERE quantity >= 10";
    $conn->query($updateQuery);
}
// ---------- End Math Logic Functions ----------

// Fetch the list of medicines from the bcp_sms3_medicalsupplies table
$query = "SELECT item_name FROM bcp_sms3_medicalsupplies";
$result = $conn->query($query);
$itemsOptions = "<option value=''>Select a medicine</option>";
if ($result) {
    while ($row = $result->fetch_assoc()) {
         $item_name = htmlspecialchars($row['item_name']);
         $itemsOptions .= "<option value='$item_name'>$item_name</option>";
    }
}

// Get record ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
  die("No record ID provided.");
}
$id = intval($_GET['id']);

$error = "";
$recommendation = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recommendation = trim($_POST['recommendation']);
    $meds_given_arr = $_POST['meds'];
    $quantity_arr = $_POST['quantity'];
    
    // Validate recommendation exists
    if (empty($recommendation)) {
        $error = "Please fill out the recommendation.";
    } else {
        $meds_combined = [];
        $valid = true; // Flag to check if all inputs are valid
        
        // Loop through each medicine pair and validate against inventory
        foreach ($meds_given_arr as $index => $med_val) {
            $med_val = trim($med_val);
            // Check that quantity is greater than 0 (not just non-empty)
            $quantity_val = trim($quantity_arr[$index]);
            if (!empty($med_val) && $quantity_val > 0) {
                // Query the available quantity for the given medicine
                $stmt = $conn->prepare("SELECT quantity FROM bcp_sms3_medicalsupplies WHERE item_name = ?");
                $stmt->bind_param("s", $med_val);
                $stmt->execute();
                $resultMed = $stmt->get_result();
                if ($row = $resultMed->fetch_assoc()) {
                    $available_quantity = $row['quantity'];
                    // Check if the input quantity exceeds the available quantity
                    if ($quantity_val > $available_quantity) {
                        $error = "Quantity is invalid; the remaining of {$med_val} is {$available_quantity}.";
                        $valid = false;
                        $stmt->close();
                        break;
                    } else {
                        $meds_combined[] = "$med_val = $quantity_val";
                    }
                } else {
                    $error = "Medicine {$med_val} not found.";
                    $valid = false;
                    $stmt->close();
                    break;
                }
                $stmt->close();
            }
        }
        
        // Ensure at least one valid pair exists
        if (empty($meds_combined)) {
            $error = "Please provide at least one valid Meds Given and Quantity pair.";
            $valid = false;
        }
        
        // If everything is valid, update the patient's record and adjust the supplies quantities
        if ($valid) {
            // Combine pairs with commas
            $meds_given = implode(", ", $meds_combined);
    
            // Update recommendation and meds in the patients table
            $stmt = $conn->prepare("UPDATE bcp_sms3_other_patients SET recommendation = ?, meds = ? WHERE id = ?");
            $stmt->bind_param("ssi", $recommendation, $meds_given, $id);
            if ($stmt->execute()) {
                // Update each medicine's available quantity using the total stock logic
                foreach ($meds_given_arr as $index => $med_val) {
                    $med_val = trim($med_val);
                    // Use a numeric conversion to ensure proper comparison
                    $quantity_val = (int)trim($quantity_arr[$index]);
                    if (!empty($med_val) && $quantity_val > 0) {
                        // Retrieve current unit and quantity
                        $stmt2 = $conn->prepare("SELECT unit, quantity FROM bcp_sms3_medicalsupplies WHERE item_name = ?");
                        $stmt2->bind_param("s", $med_val);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        if ($row2 = $result2->fetch_assoc()) {
                            $currentUnit = (int)$row2['unit'];
                            $currentQuantity = (int)$row2['quantity'];
                            // Calculate total stock as (unit * 10) + quantity
                            $totalStock = ($currentUnit * 10) + $currentQuantity;
                            // Deduct the prescribed quantity
                            $newTotal = $totalStock - $quantity_val;
                            if ($newTotal < 0) {
                                $newTotal = 0;
                            }
                            // Recalculate new unit and quantity values
                            $newUnit = floor($newTotal / 10);
                            $newQuantity = $newTotal % 10;
                            
                            // Update the medicine's record
                            $stmt3 = $conn->prepare("UPDATE bcp_sms3_medicalsupplies SET unit = ?, quantity = ? WHERE item_name = ?");
                            $stmt3->bind_param("iis", $newUnit, $newQuantity, $med_val);
                            $stmt3->execute();
                            $stmt3->close();
                        }
                        $stmt2->close();
                    }
                }
                
                // Optionally, call additional math logic functions if needed.
                // updateStock($conn);
                // adjustExcessQuantity($conn);
                
                $success = "Recommendation updated successfully!";
            } else {
                $error = "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Recommendation Entry - Clinic Management System</title>
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    .remove-btn {
      cursor: pointer;
      font-size: 1.2rem;
      color: red;
      border: none;
      background: none;
      padding: 0;
      margin-top: 0.8rem;
    }
    .remove-container {
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>
  <!-- Header -->
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
  
  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <div class="logo-container" style="text-align: center; margin-bottom: 10px;">
        <img src="../assets/img/bcp logo.png" alt="Logo" style="width: 100px; height: auto;">
      </div>
      <hr class="sidebar-divider">
      <li class="nav-heading">Clinic Management System</li>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#system-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-hospital"></i>
          <span>Clinic Management</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="system-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
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
            <a href="tables-data.php" class="active">
              <i class="bi bi-circle"></i><span>Patient Medical Records</span>
            </a>
          </li>
          <li>
            <a href="medical-supplies.php">
              <i class="bi bi-circle"></i><span>Medical Supplies</span>
            </a>
          </li>
          <li>
            <a href="request.php">
              <i class="bi bi-circle"></i><span>Request Supply</span>
            </a>
          </li>
          <li>
            <a href="SDforecastingai.php">
              <i class="bi bi-circle"></i><span>ForecastingAI</span>
            </a>
          </li>
          <li>
            <a href="admission.php">
              <i class="bi bi-circle"></i><span>Student Data</span>
            </a>
          </li>
          <li>
            <a href="integ.php">
              <i class="bi bi-circle"></i><span>Medical Requests</span>
            </a>
          </li>
        </ul>
      </li>
      <hr class="sidebar-divider">
    </ul>
  </aside>
  
  <!-- Main Content -->
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Recommendation Entry</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="mainpage.php">Home</a></li>
          <li class="breadcrumb-item"><a href="tables-data.php">Patient Medical Records</a></li>
          <li class="breadcrumb-item active">Recommendation Entry</li>
        </ol>
      </nav>
    </div>
    
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Enter Recommendation Message</h5>
              <?php
              if (!empty($error)) {
                  echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>";
              }
              ?>
              <form method="POST" action="">
                <div class="form-group mb-3">
                  <label for="recommendation">Recommendation Message</label>
                  <textarea name="recommendation" id="recommendation" class="form-control" rows="8" required><?php echo htmlspecialchars($recommendation); ?></textarea>
                </div>
                <!-- Container for Meds Given and Quantity sets -->
                <div id="medsContainer">
                  <div class="row mb-3 meds-row">
                    <div class="col-md-5">
                      <label for="meds_0">Meds Given</label>
                      <select name="meds[]" id="meds_0" class="form-select" required>
                        <?php echo $itemsOptions; ?>
                      </select>
                    </div>
                    <div class="col-md-5">
                      <label for="quantity_0">Quantity</label>
                      <input type="number" name="quantity[]" id="quantity_0" class="form-control" required>
                    </div>
                    <!-- No remove button for the initial row -->
                  </div>
                </div>
                <!-- Add Button -->
                <button type="button" id="addButton" class="btn btn-secondary mb-3">Add</button>
                <br>
                <button type="submit" class="btn btn-success mt-3">Submit Recommendation</button>
                <a href="other_patient.php" class="btn btn-secondary mt-3">Back to Records</a>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  
  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="successModalLabel">Success</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php echo htmlspecialchars($success); ?>
        </div>
        <div class="modal-footer">
          <!-- Redirect on OK -->
          <button type="button" class="btn btn-success" id="okButton">OK</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Error Modal -->
  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php echo htmlspecialchars($error); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/js/main.js"></script>
  <script>
    // Maximum of 3 sets allowed
    const maxSets = 3;
    let currentSets = 1;
    const medsContainer = document.getElementById("medsContainer");
    const addButton = document.getElementById("addButton");

    addButton.addEventListener("click", function() {
      if (currentSets < maxSets) {
        const newIndex = currentSets;
        const newRow = document.createElement("div");
        newRow.className = "row mb-3 meds-row";
        newRow.innerHTML = `
          <div class="col-md-5">
            <label for="meds_${newIndex}">Meds Given</label>
            <select name="meds[]" id="meds_${newIndex}" class="form-select" required>
              <?php echo $itemsOptions; ?>
            </select>
          </div>
          <div class="col-md-5">
            <label for="quantity_${newIndex}">Quantity</label>
            <input type="number" name="quantity[]" id="quantity_${newIndex}" class="form-control" required>
          </div>
          <div class="col-md-2 remove-container">
            <button type="button" class="remove-btn" title="Remove">&times;</button>
          </div>
        `;
        medsContainer.appendChild(newRow);
        currentSets++;
        if(currentSets === maxSets){
          addButton.style.display = "none";
        }
        
        newRow.querySelector(".remove-btn").addEventListener("click", function() {
          medsContainer.removeChild(newRow);
          currentSets--;
          if(currentSets < maxSets){
            addButton.style.display = "inline-block";
          }
        });
      }
    });

    // If success message exists, show success modal.
    <?php if (!empty($success)): ?>
      var successModal = new bootstrap.Modal(document.getElementById('successModal'));
      successModal.show();
    <?php elseif (!empty($error) && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
      var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
      errorModal.show();
    <?php endif; ?>

    // Redirect when OK is clicked on the success modal.
    document.getElementById('okButton')?.addEventListener("click", function() {
      window.location.href = "other_patient.php";
    });
  </script>
</body>
</html>
