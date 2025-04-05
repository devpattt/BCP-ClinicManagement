<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: superadmin/mainpage.php");
  exit();
}

include '../fetchfname.php';
include '../connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System / Patient Reports</title>

  <!-- Favicon and Apple Touch Icon -->
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Main CSS Files -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/forms.css" rel="stylesheet">

  <style>
    /* Table container: smaller width & centered */
    .table-container {
      max-width: 90%;
      margin: 0 auto 1rem auto;
      overflow-x: auto;
    }
    /* Slightly reduce overall table font size */
    .table-container table.table-sm {
      font-size: 0.85rem;
    }
    /* Reduce cell padding */
    .table-sm td, .table-sm th {
      padding: 0.3rem;
      vertical-align: middle;
    }
    /* Prevent wrapping in the Manage column */
    #patientTable td:last-child {
      white-space: nowrap;
    }
    /* Make buttons smaller and inline */
    .btn-sm {
      font-size: 0.75rem;
      padding: 0.25rem 0.4rem;
    }
    /* Search container aligned to the right */
    .search-container {
      text-align: right;
    }
    /* Make the Remarks column wider */
    th.remarks-col, td.remarks-col {
      width: 30%;
    }
  </style>
</head>
<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="header-left d-flex align-items-center">
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <!-- Profile Dropdown -->
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../assets/img/default profile.jpg" alt="Profile" class="rounded-circle">
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
        </li><!-- End Profile Nav -->
      </ul>
    </nav>
  </header>
  <!-- ======= End Header ======= -->

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
          <i class="bi bi-hospital"></i>
          <span>Clinic Management</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="system-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li><a href="clinic-dashboard.php"><i class="bi bi-circle"></i><span>Report and Analytics</span></a></li>
          <li><a href="forms-elements.php"><i class="bi bi-circle"></i><span>Patient Registration</span></a></li>
          <li><a href="tables-data.php" ><i class="bi bi-circle"></i><span>Patient Medical Records</span></a></li>
          <li><a href="medical-supplies.php" class="active"><i class="bi bi-circle"></i><span>Medical Supplies</span></a></li>
          <li><a href="request.php"><i class="bi bi-circle"></i><span>Request Supply</span></a></li>
          <li><a href="SDforecastingai.php"><i class="bi bi-circle"></i><span>ForecastingAI</span></a></li>
          <li><a href="admission.php"><i class="bi bi-circle"></i><span>Student Data</span></a></li>
          <li><a href="integ.php"><i class="bi bi-circle"></i><span>Medical Requests</span></a></li>
        </ul>
      </li>
      <hr class="sidebar-divider">
    </ul>
  </aside>
  <!-- ======= End Sidebar ======= -->

  <main id="main" class="main mt-5 pt-5">
    <div class="container">
      <div class="pagetitle">
        <h1>Medical Equipment Management</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Medical Equipment Management</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <!-- New controls row with buttons and search bar -->
      <div class="row mb-3 align-items-center">
        <div class="col-md-6">
          <!-- Add Equipment button now opens the modal -->
          <a href="#" class="btn btn-primary btn-sm" id="addEquipmentBtn">Add Equipment</a>
          <!-- Generate Report button placed between Add Equipment and Supply -->
          <a href="#" class="btn btn-primary btn-sm mx-2" id="generateReportBtn">Generate Report</a>
          <a href="medical-supplies.php" class="btn btn-primary btn-sm">Supply</a>
        </div>
        <div class="col-md-6 text-end">
          <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search records..." style="width: 200px; display: inline-block;">
        </div>
      </div>

      <section class="section">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title fw-bold mb-3" style="color: #012970;"></h4>
                <div class="table-container">
                  <table class="table table-sm table-hover table-striped table-bordered align-middle text-center" id="patientTable">
                    <thead class="table-primary">
                      <tr>
                        <th>Item</th>
                        <th>Brand</th>
                        <th>Quantity</th>
                        <th class="remarks-col">Remarks</th>
                        <th>Manage</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $stmt = $conn->prepare("SELECT id, item, brand, quantity, remarks FROM bcp_sms3_equipments");
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                            $id = htmlspecialchars($row['id']);
                            $item = htmlspecialchars($row['item']);
                            $brand = htmlspecialchars($row['brand']);
                            $quantity = htmlspecialchars($row['quantity']);
                            $remarks = htmlspecialchars($row['remarks']);

                            echo "<tr id='row-{$id}'>";
                            echo "<td>{$item}</td>";
                            echo "<td>{$brand}</td>";
                            echo "<td>{$quantity}</td>";
                            echo "<td class='remarks-col'>{$remarks}</td>";
                            echo "<td>";
                            echo "<a href='#' data-id='{$id}' class='edit-btn btn btn-primary btn-sm me-1'>Edit</a>";
                            echo "<a href='#' data-id='{$id}' class='delete-btn btn btn-danger btn-sm '>Delete</a>";

                            echo "</td>";
                            echo "</tr>";
                          }
                        } else {
                          echo "<tr><td colspan='5' class='text-center'>No equipment found</td></tr>";
                        }
                        $stmt->close();
                      ?>
                    </tbody>
                  </table>
                </div><!-- End table-container -->
              </div><!-- End card-body -->
            </div><!-- End card -->
          </div><!-- End col -->
        </div><!-- End row -->
      </section>
    </div><!-- End container -->
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- =================== ADD EQUIPMENT MODAL =================== -->
  <div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #012970; color: #fff;">
          <h5 class="modal-title" id="addEquipmentModalLabel">Add Equipment</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Equipment form -->
          <form id="addEquipmentForm">
            <div class="mb-3">
              <label for="itemName" class="form-label">Item Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="itemName" name="item" required>
            </div>
            <div class="mb-3">
              <label for="brand" class="form-label">Brand</label>
              <input type="text" class="form-control" id="brand" name="brand">
            </div>
            <div class="mb-3">
              <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="mb-3">
              <label for="remarks" class="form-label">Remarks</label>
              <textarea class="form-control" id="remarks" name="remarks" rows="4"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" id="addSubmitBtn" class="btn btn-primary btn-sm">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <!-- =================== SUCCESS MODAL =================== -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #012970; color: #fff;">
          <h5 class="modal-title" id="successModalLabel">Success</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="successModalBody">
          Equipment added successfully!
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- =================== ALERT MODAL (Upper Center) =================== -->
  <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="margin-top: 20px; margin-left: auto; margin-right: auto; max-width: 400px;">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #012970; color: #fff;">
          <h5 class="modal-title" id="alertModalLabel">Message</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="alertModalBody">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- =================== EDIT & DELETE MODALS (Unchanged) =================== -->
  <!-- Edit Confirmation Modal -->
  <div class="modal fade" id="editConfirmModal" tabindex="-1" aria-labelledby="editConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="editConfirmModalLabel">Confirm Edit</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to edit this data?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" id="editConfirmBtn" class="btn btn-primary btn-sm">Confirm</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this data?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" id="deleteConfirmBtn" class="btn btn-primary btn-sm">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>

  <!-- =================== EDIT EQUIPMENT MODAL =================== -->
  <div class="modal fade" id="editEquipmentModal" tabindex="-1" aria-labelledby="editEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #012970; color: #fff;">
          <h5 class="modal-title" id="editEquipmentModalLabel">Edit Equipment</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Edit Equipment form -->
          <form id="editEquipmentForm">
            <!-- Hidden input to store the record ID -->
            <input type="hidden" id="editEquipId">
            <div class="mb-3">
              <label for="editItemName" class="form-label">Item Name</label>
              <input type="text" class="form-control" id="editItemName" name="item">
            </div>
            <div class="mb-3">
              <label for="editBrand" class="form-label">Brand</label>
              <input type="text" class="form-control" id="editBrand" name="brand">
            </div>
            <div class="mb-3">
              <label for="editQuantity" class="form-label">Quantity</label>
              <input type="number" class="form-control" id="editQuantity" name="quantity">
            </div>
            <div class="mb-3">
              <label for="editRemarks" class="form-label">Remarks</label>
              <textarea class="form-control" id="editRemarks" name="remarks" rows="4"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" id="editSubmitBtn" class="btn btn-primary btn-sm">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <!-- =================== DELETE CONFIRMATION MODAL =================== -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #012970; color: #fff;">
          <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to Delete this row?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" id="deleteSubmitBtn" class="btn btn-primary btn-sm">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <!-- =================== jsPDF and AutoTable for Report Generation =================== -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
  <script>
    const fullname = "<?php echo $fullname; ?>";
    function generateReport() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();
      const logo = new Image();
      logo.src = "../assets/img/bcp logo.png";
      logo.onload = function () {
        doc.addImage(logo, "PNG", 30, 10, 20, 24);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(14);
        doc.text("Bestlink College of the Philippines", 105, 15, { align: "center" });
        doc.setFontSize(12);
        doc.text("Kaligayahan, Quirino Highway, Novaliches,", 105, 22, { align: "center" });
        doc.text("Quezon City, Philippines, 1123.", 105, 29, { align: "center" });
        doc.setFontSize(16);
        doc.text("Medical Supplies Report", 105, 40, { align: "center" });
        
        // Get the table data (only the relevant columns: Item, Brand, Quantity, Remarks)
        const table = document.querySelector("table");
        const rows = Array.from(table.querySelectorAll("tbody tr")).map((row, index) => {
          const cells = row.querySelectorAll("td");
          return [
            index + 1,
            cells[0].innerText, 
            cells[1].innerText,
            cells[2].innerText,
            cells[3].innerText
          ];
        });
        
        doc.autoTable({
          head: [["#", "Item", "Brand", "Quantity", "Remarks"]],
          body: rows,
          startY: 45,
          theme: "grid",
        });
        const finalY = doc.lastAutoTable.finalY || 50;
        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        // Bottom left: Generated By full name
        doc.text(`Generated By: ${fullname}`, 20, finalY + 10);
        // Bottom right: Current Date/Time
        doc.text(`Generated: ${new Date().toLocaleString()}`, 140, finalY + 10);
        doc.save("Medical_Supplies_Report.pdf");
      };
    }

    // Attach event listener to the Generate Report button
    document.getElementById("generateReportBtn").addEventListener("click", function(e) {
      e.preventDefault();
      generateReport();
    });
  </script>

  <!-- =================== Main JS Code for Equipment Management =================== -->
  <script>
    // Helper function to show an alert using the Alert Modal
    function showAlert(message) {
      document.getElementById('alertModalBody').innerText = message;
      new bootstrap.Modal(document.getElementById('alertModal')).show();
    }

    // Search filter functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll('#patientTable tbody tr');
      rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.indexOf(filter) > -1 ? '' : 'none';
      });
    });

    // Global modal instances for Add, Success, Edit, and Delete
    const addEquipmentModal = new bootstrap.Modal(document.getElementById('addEquipmentModal'));
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    const editEquipmentModal = new bootstrap.Modal(document.getElementById('editEquipmentModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    // Refresh page when the success modal is hidden
    document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
      window.location.reload();
    });

    // Open Add Equipment Modal when clicking the button
    document.getElementById('addEquipmentBtn').addEventListener('click', function(e) {
      e.preventDefault();
      addEquipmentModal.show();
    });

    // Handle Add Equipment submission (existing code)
    document.getElementById('addSubmitBtn').addEventListener('click', function() {
      const item = document.getElementById('itemName').value.trim();
      const brand = document.getElementById('brand').value.trim();
      const quantity = document.getElementById('quantity').value.trim();
      const remarks = document.getElementById('remarks').value.trim();

      if (!item || !quantity) {
        showAlert("Please fill in all required fields.");
        return;
      }

      const formData = { item, brand, quantity, remarks };

      fetch('add_equip.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      })
      .then(response => response.text())
      .then(text => {
        try {
          const data = JSON.parse(text);
          if (data.success) {
            addEquipmentModal.hide();
            successModal.show();
          } else {
            showAlert("Error: " + data.error);
          }
        } catch (e) {
          showAlert("An error occurred: " + text);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        showAlert("An error occurred: " + error.message);
      });
    });

    // ---------- EDIT FUNCTIONALITY ----------
    // Global variable to store original values of the record for edit comparison
    let originalEditData = {};

    // When an Edit button is clicked, populate and show the Edit modal.
    // Assumes that each edit button has a class "edit-btn" and a data attribute "data-id".
    document.querySelectorAll('.edit-btn').forEach(button => {
      button.addEventListener('click', function() {
        const recordId = this.getAttribute('data-id');
        // Retrieve existing values from the row.
        const row = document.getElementById('row-' + recordId);
        const cells = row.getElementsByTagName('td');

        // Save original values for comparison
        originalEditData = {
          item: cells[0].innerText.trim(),
          brand: cells[1].innerText.trim(),
          quantity: cells[2].innerText.trim(),
          remarks: cells[3].innerText.trim()
        };

        // Populate the edit modal form with the current values
        document.getElementById('editEquipId').value = recordId;
        document.getElementById('editItemName').value = originalEditData.item;
        document.getElementById('editBrand').value = originalEditData.brand;
        document.getElementById('editQuantity').value = originalEditData.quantity;
        document.getElementById('editRemarks').value = originalEditData.remarks;

        editEquipmentModal.show();
      });
    });

    // Handle Edit Equipment submission.
    document.getElementById('editSubmitBtn').addEventListener('click', function() {
      const recordId = document.getElementById('editEquipId').value;
      const item = document.getElementById('editItemName').value.trim();
      const brand = document.getElementById('editBrand').value.trim();
      const quantity = document.getElementById('editQuantity').value.trim();
      const remarks = document.getElementById('editRemarks').value.trim();

      // Check if nothing was changed
      if (
        item === originalEditData.item &&
        brand === originalEditData.brand &&
        quantity === originalEditData.quantity &&
        remarks === originalEditData.remarks
      ) {
        // Hide the Edit modal first so the alert won't appear behind it
        editEquipmentModal.hide();
        showAlert("You didn't change anything.");
        return;
      }

      // Prepare data to send for editing
      const formData = { id: recordId, item, brand, quantity, remarks };

      fetch('edit_equip.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      })
      .then(response => response.text())
      .then(text => {
        try {
          const data = JSON.parse(text);
          if (data.success) {
            editEquipmentModal.hide();
            successModal.show();
            // Optionally update the table row with new values.
            const row = document.getElementById('row-' + recordId);
            if (row) {
              const cells = row.getElementsByTagName('td');
              cells[0].innerText = item;
              cells[1].innerText = brand;
              cells[2].innerText = quantity;
              cells[3].innerText = remarks;
            }
          } else {
            editEquipmentModal.hide(); // Hide modal if there's an error too
            showAlert("Error: " + data.error);
          }
        } catch (e) {
          editEquipmentModal.hide(); // Hide modal if there's an error too
          showAlert("An error occurred: " + text);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        editEquipmentModal.hide(); // Hide modal if there's an error too
        showAlert("An error occurred: " + error.message);
      });
    });

    // ---------- DELETE FUNCTIONALITY ----------
    let currentRecordId = '';

    // When a Delete button is clicked, store the record ID and show the Delete modal.
    // Assumes that each delete button has a class "delete-btn" and a data attribute "data-id".
    document.querySelectorAll('.delete-btn').forEach(button => {
      button.addEventListener('click', function() {
        currentRecordId = this.getAttribute('data-id');
        deleteModal.show();
      });
    });

    // Handle Delete confirmation.
    document.getElementById('deleteSubmitBtn').addEventListener('click', function() {
      // For deletion, send only the ID (without the 'item' field) to trigger deletion logic.
      fetch('edit_equip.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: currentRecordId })
      })
      .then(response => response.text())
      .then(text => {
        try {
          const data = JSON.parse(text);
          if (data.success) {
            deleteModal.hide();
            // Optionally remove the row from the table.
            const row = document.getElementById('row-' + currentRecordId);
            if (row) {
              row.remove();
            }
            successModal.show();
          } else {
            showAlert("Error deleting record: " + data.error);
          }
        } catch (e) {
          showAlert("An error occurred: " + text);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        showAlert("An error occurred: " + error.message);
      });
    });
  </script>
</body>
</html>
