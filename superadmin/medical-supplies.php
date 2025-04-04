<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: superadmin/mainpage.php");
    exit();
}

include '../connection.php';
include '../fetchfname.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System</title>
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <!-- Fixed external URLs -->
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
    .card-icon i {
        cursor: pointer;
    }
    .custom-border {
      border: 2px solid black;
      border-radius: 4px;
    }
    .custom-border:focus {
      border-color: #333;
      box-shadow: 0 0 5px rgba(51, 51, 51, 0.5);
    }
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

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Medical Supplies Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="mainpage.php">Home</a></li>
          <li class="breadcrumb-item active">Medical Supplies Management</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="row">
        <div class="col-lg-24">
          <div class="row d-flex justify-content-between">
            <!-- Card 1: Out of Stocks Products -->
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="card info-card sales-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i></i></a>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Out of stocks products</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i id="outOfStockIcon" class="bi bi-exclamation-triangle text-danger" style="cursor: pointer;"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="today-count"></h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 2: Supplies on Low Stocks -->
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="card info-card revenue-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i></i></a>
                </div>
                <div class="card-body">
                  <h5 class="card-title">Supplies on low stocks</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i id="lowStockIcon" class="bi bi-exclamation-triangle text-primary" style="cursor: pointer;"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="month-count"></h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 3: Numbers of Products to be Arrived -->
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="card info-card customers-card">
                <div class="filter">
                  <a class="icon" href="logic.php" data-bs-toggle="dropdown"><i></i></a>
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
      </div>
    </section>

    <!-- Modal for Notifications -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="alertModalLabel">Notification</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="alertModalBody">
            <!-- Dynamic message will be inserted here -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Supplies Table + Buttons + Search Bar in one row -->
    <div class="container mt-4">
      <div class="row mb-3 align-items-center">
        <div class="col-auto">
          <button class="btn btn-primary" style="background-color: #1e3a8a; border-color: #1e3a8a;" data-bs-toggle="modal" data-bs-target="#addSupplyModal">
            Add Supply
          </button>
          <button class="btn btn-primary" style="background-color: #1e3a8a; border-color: #1e3a8a;" onclick="generateReport()">
            Generate Report
          </button>
          <button class="btn btn-primary" style="background-color: #1e3a8a; border-color: #1e3a8a;" onclick="window.location.href='equipment.php';">
            Equipment
           </button>

        </div>
        <div class="col-auto ms-auto">
          <input type="text" id="searchInput" class="form-control" placeholder="Search...">
        </div>
      </div>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Code</th>
            <th>Brand Name</th>
            <th>Generic</th>
            <th>Strip</th>
            <th>Quantity</th>
            <th>Date Added</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="supplyTableBody">
          <!-- Supplies data will be injected here -->
        </tbody>
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
          <!-- Brand Name Input with suggestion list -->
          <div class="mb-3 position-relative">
            <label for="itemName" class="form-label">Brand Name</label>
            <input type="text" class="form-control custom-border" id="itemName" name="item_name" autocomplete="off" required>
            <!-- Datalist (optional, for browser autocomplete) -->
            <datalist id="brandDatalist">
              <!-- Options will be populated by JS -->
            </datalist>
            <!-- Suggestion list for detailed recommendations -->
            <ul id="brandSuggestionList" style="display:none; list-style: none; padding: 0; margin: 0; border: 1px solid #ccc; position: absolute; width: 100%; background: #fff; z-index: 1000;"></ul>
            <!-- Recommendation message if no match is found -->
            <div id="recDiv" style="display:none;">
              <div class="alert alert-info mt-2" role="alert">
                No matching medicine found. Please verify your brand name or create a new record.
              </div>
            </div>
          </div>
          <!-- Generic Combo Box -->
          <div class="mb-3">
            <label for="category" class="form-label">Generic</label>
            <select class="form-select" id="category" name="category" required>
              <option value="">Select Generic</option>
            </select>
          </div>
          <!-- Strip Input -->
          <div class="mb-3">
            <label for="unit" class="form-label">Strip</label>
            <input type="number" class="form-control custom-border" id="unit" name="unit" required>
          </div>
          <!-- Quantity Input -->
          <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control custom-border" id="quantity" name="quantity" required>
          </div>
          <!-- Hidden field if additional data is needed -->
          <input type="hidden" name="description" value="">
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
              <input type="hidden" id="updateCode" name="code">
              <div class="mb-3">
                <label for="updateItemName" class="form-label">Brand Name</label>
                <input type="text" class="form-control custom-border" id="updateItemName" name="item_name" required>
              </div>
              <div class="mb-3">
                <label for="updateCategory" class="form-label">Generic</label>
                <select class="form-select" id="updateCategory" name="category" required>
                  <option value="">Select Generic</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="updateUnit" class="form-label">Strip</label>
                <input type="number" class="form-control custom-border" id="updateUnit" name="unit" required>
              </div>
              <div class="mb-3">
                <label for="updateQuantity" class="form-label">Quantity</label>
                <input type="number" class="form-control custom-border" id="updateQuantity" name="quantity" required>
              </div>
              <input type="hidden" name="description" value="">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" style="background-color: #1e3a8a; border-color: #1e3a8a;" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" style="background-color: #1e3a8a; border-color: #1e3a8a;">Update Supply</button>
            </div>
          </div>
        </form>
      </div>
    </div>

  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="../assets/vendor/chart.js/chart.umd.js"></script>
<script src="../assets/vendor/echarts/echarts.min.js"></script>
<script src="../assets/vendor/quill/quill.js"></script>
<script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="../assets/vendor/tinymce/tinymce.min.js"></script>
<script src="../assets/vendor/php-email-form/validate.js"></script>
<script src="../assets/js/main.js"></script>

<!-- Override default alert to use modal -->
<script>
  window.alert = function(message) {
    showAlertModal(message);
  };
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: blue;">
        <h5 class="modal-title" id="deleteConfirmModalLabel" style="color: white;">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this row?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteButton">Confirm</button>
      </div>
    </div>
  </div>
</div>

<!-- Client-Side Script to Handle Fetching, Search, Modal Alerts, Auto-Fill, and Deletion -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  // --- Alert Modal Function ---
  function showAlertModal(message) {
    const alertModalBody = document.getElementById("alertModalBody");
    alertModalBody.textContent = message;
    const alertModalElem = document.getElementById("alertModal");
    const alertModal = new bootstrap.Modal(alertModalElem);
    alertModal.show();
    alertModalElem.addEventListener("hidden.bs.modal", () => {
      location.reload();
    }, { once: true });
  }

  // Variable to hold the code of the supply to be deleted
  let deleteSupplyCode = null;

  // --- Fetch Supplies & Attach Edit/Delete Button Listeners ---
  const supplyTableBody = document.getElementById("supplyTableBody");
  
  function attachDeleteListeners() {
    document.querySelectorAll(".delete-btn").forEach(button => {
      button.addEventListener("click", function() {
        deleteSupplyCode = this.getAttribute("data-code");
        // Show the delete confirmation modal
        const deleteModalElem = document.getElementById("deleteConfirmModal");
        const deleteModal = new bootstrap.Modal(deleteModalElem);
        deleteModal.show();
      });
    });
  }
  
  function fetchSupplies() {
    fetch("../fetchsupplies.php")
      .then(response => response.json())
      .then(data => {
        supplyTableBody.innerHTML = "";
        data.forEach((supply) => {
          supplyTableBody.innerHTML += `
            <tr>
              <td>${supply.code}</td>
              <td>${supply.item_name}</td>
              <td>${supply.category || "N/A"}</td>
              <td>${supply.unit || "N/A"}</td>
              <td>${supply.quantity}</td>
              <td>${supply.date_added}</td>
              <td>
                <button class="btn btn-sm btn-warning edit-btn" 
                  data-code="${supply.code}" 
                  data-item_name="${supply.item_name}" 
                  data-category="${supply.category}" 
                  data-unit="${supply.unit}" 
                  data-quantity="${supply.quantity}">
                  Edit
                </button>
                <button class="btn btn-sm btn-danger delete-btn" data-code="${supply.code}">
                  Delete
                </button>
              </td>
            </tr>
          `;
        });
        // Attach listeners for Edit buttons
        document.querySelectorAll(".edit-btn").forEach(button => {
          button.addEventListener("click", function() {
            editSupply(
              this.getAttribute("data-code"),
              this.getAttribute("data-item_name"),
              this.getAttribute("data-category"),
              this.getAttribute("data-unit"),
              this.getAttribute("data-quantity")
            );
          });
        });
        // Attach listeners for Delete buttons
        attachDeleteListeners();
      })
      .catch(err => console.error("Error fetching supplies:", err));
  }
  fetchSupplies();

  // --- Delete Supply Confirm Action ---
  document.getElementById("confirmDeleteButton").addEventListener("click", () => {
    if (!deleteSupplyCode) {
      return;
    }
    // Proceed to delete the supply using a POST request.
    fetch("../deletesupplies.php", {
      method: "POST",
      body: new URLSearchParams({ code: deleteSupplyCode })
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
        // Hide the delete confirmation modal
        const deleteModalElem = document.getElementById("deleteConfirmModal");
        const deleteModal = bootstrap.Modal.getInstance(deleteModalElem);
        deleteModal.hide();
        showAlertModal("Supply deleted successfully.");
        fetchSupplies(); // Refresh the table
      } else {
        showAlertModal("Error: " + data.message);
      }
    })
    .catch(err => {
      console.error("Error deleting supply:", err);
      showAlertModal("An error occurred while deleting the supply.");
    });
  });

  // --- Add Supply Form Submission with Duplicate Check ---
  const addSupplyForm = document.getElementById("addSupplyForm");
  if (addSupplyForm) {
    addSupplyForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const formData = new FormData(addSupplyForm);
      const itemName = formData.get("item_name").trim();
      // Perform duplicate check on the brand name
      fetch("../check_supply.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ item_name: itemName })
      })
      .then(response => response.json())
      .then(result => {
        if (result.exists === true || result.exists === "true") {
          const addSupplyModalElem = document.getElementById("addSupplyModal");
          const addSupplyModal = bootstrap.Modal.getInstance(addSupplyModalElem)
                                  || new bootstrap.Modal(addSupplyModalElem);
          addSupplyModal.hide();
          addSupplyModalElem.addEventListener('hidden.bs.modal', () => {
            showAlertModal("Error: Brand name already exists. Please use a different brand name.");
          }, { once: true });
        } else {
          const addSupplyModalElem = document.getElementById("addSupplyModal");
          const addSupplyModal = bootstrap.Modal.getInstance(addSupplyModalElem)
                                  || new bootstrap.Modal(addSupplyModalElem);
          addSupplyModal.hide();
          addSupplyModalElem.addEventListener('hidden.bs.modal', () => {
            fetch("../addsupplies.php", {
              method: "POST",
              body: formData,
            })
            .then(response => response.json())
            .then(data => {
              if (data.status === "success") {
                const successModalElem = document.getElementById("successModal");
                if (successModalElem) {
                  const successModal = new bootstrap.Modal(successModalElem);
                  successModal.show();
                } else {
                  showAlertModal("Item added successfully.");
                }
              } else {
                showAlertModal("Error: " + data.message);
              }
            })
            .catch(error => {
              console.error("Error while adding supply:", error);
              showAlertModal("An error occurred while adding the supply.");
            });
          }, { once: true });
        }
      })
      .catch(error => {
        console.error("Error in duplicate check:", error);
        const addSupplyModalElem = document.getElementById("addSupplyModal");
        const addSupplyModal = bootstrap.Modal.getInstance(addSupplyModalElem)
                                || new bootstrap.Modal(addSupplyModalElem);
        addSupplyModal.hide();
        addSupplyModalElem.addEventListener('hidden.bs.modal', () => {
          showAlertModal("An error occurred during duplicate check.");
        }, { once: true });
      });
    });
  }

  // --- Edit Supply Function ---
  window.editSupply = (code, itemName, category, unit, quantity) => {
    document.getElementById("updateCode").value = code;
    document.getElementById("updateItemName").value = itemName;
    document.getElementById("updateUnit").value = unit;
    document.getElementById("updateQuantity").value = quantity;
    fetch("../get_categories.php")
      .then(response => response.json())
      .then(categories => {
        const categorySelect = document.getElementById("updateCategory");
        categorySelect.innerHTML = '<option value="">Select Generic</option>';
        categories.forEach(cat => {
          const option = document.createElement("option");
          option.value = cat;
          option.textContent = cat;
          categorySelect.appendChild(option);
        });
        categorySelect.value = category;
      });
    const updateSupplyModalElem = document.getElementById("updateSupplyModal");
    const updateSupplyModal = new bootstrap.Modal(updateSupplyModalElem);
    updateSupplyModal.show();
  };

  // --- Update Supply Form Submission ---
  const updateSupplyForm = document.getElementById("updateSupplyForm");
  if (updateSupplyForm) {
    updateSupplyForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const formData = new FormData(updateSupplyForm);
      const category = formData.get("category");
      console.log("Generic (Category) value being submitted:", category);
      fetch("../updatesupplies.php", {
        method: "POST",
        body: formData,
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === "success") {
          const updateSupplyModalElem = document.getElementById("updateSupplyModal");
          const updateSupplyModal = bootstrap.Modal.getInstance(updateSupplyModalElem)
                                    || new bootstrap.Modal(updateSupplyModalElem);
          updateSupplyModal.hide();
          showAlertModal("Supply updated successfully.");
        } else {
          showAlertModal("Error: " + data.message);
        }
      })
      .catch(err => {
        console.error("Error in update fetch:", err);
        showAlertModal("An error occurred while updating the supply.");
      });
    });
  }

  // --- Search Functionality ---
  const searchInput = document.getElementById("searchInput");
  searchInput.addEventListener("keyup", () => {
    const filter = searchInput.value.toLowerCase();
    const rows = supplyTableBody.getElementsByTagName("tr");
    for (let i = 0; i < rows.length; i++) {
      const cells = rows[i].getElementsByTagName("td");
      let rowText = "";
      for (let j = 0; j < cells.length; j++) {
        rowText += cells[j].textContent.toLowerCase() + " ";
      }
      rows[i].style.display = rowText.indexOf(filter) > -1 ? "" : "none";
    }
  });
});
</script>

<script>
  // --- Update Stock Counts & Show Stock Details ---
  document.addEventListener("DOMContentLoaded", () => {
    const updateStockCounts = () => {
      fetch('../fetchlowstocks.php')
        .then(response => response.json())
        .then(data => {
          document.getElementById('month-count').textContent = data.lowStockCount;
          document.getElementById('today-count').textContent = data.outOfStockCount;
        })
        .catch(error => console.error('Error fetching stock data:', error));
    };
    updateStockCounts();
    setInterval(updateStockCounts, 30000);
    const outOfStockIcon = document.getElementById('outOfStockIcon');
    const lowStockIcon   = document.getElementById('lowStockIcon');
    if (outOfStockIcon) {
      outOfStockIcon.addEventListener('click', () => {
        fetchStockDetails("out");
      });
    }
    if (lowStockIcon) {
      lowStockIcon.addEventListener('click', () => {
        fetchStockDetails("low");
      });
    }
    function fetchStockDetails(type) {
      fetch(`../fetchlowstocks.php?type=${type}`)
        .then(response => response.json())
        .then(data => {
          const stockTableBody = document.getElementById('stockTableBody');
          stockTableBody.innerHTML = "";
          if (!Array.isArray(data) || data.length === 0) {
            stockTableBody.innerHTML = `<tr><td colspan="3" class="text-center">No items found.</td></tr>`;
          } else {
            data.forEach(item => {
              stockTableBody.innerHTML += `
                <tr>
                  <td>${item.item_name || item.brand_name || ""}</td>
                  <td>${item.unit || item.strip || ""}</td>
                  <td>${item.quantity || ""}</td>
                </tr>
              `;
            });
          }
          new bootstrap.Modal(document.getElementById('stockModal')).show();
        })
        .catch(error => console.error('Error fetching stock details:', error));
    }
  });
</script>

<script>
  // --- Update Profile Name ---
  document.addEventListener('DOMContentLoaded', function() {
    function updateProfileName() {
      fetch('../fetch_uname.php') 
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

<!-- --- Script for Suggestion-based Auto-Fill --- -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    // For Add Supply Modal
    const brandInput = document.getElementById("itemName");
    const genericSelect = document.getElementById("category");
    const brandDatalist = document.getElementById("brandDatalist");
    const suggestionList = document.getElementById("brandSuggestionList");
    const recDiv = document.getElementById("recDiv");
    brandInput.addEventListener("input", function() {
      const query = this.value.trim();
      if (query === "") {
        suggestionList.style.display = "none";
        if (recDiv) recDiv.style.display = "none";
        return;
      }
      fetch("fetch_med.php?brand=" + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
          suggestionList.innerHTML = "";
          if (data.length > 0) {
            if (recDiv) recDiv.style.display = "none";
            data.forEach(function(med) {
              const li = document.createElement("li");
              li.textContent = med.brand + " - " + med.generic;
              li.style.cursor = "pointer";
              li.style.padding = "5px";
              li.addEventListener("click", function() {
                brandInput.value = med.brand;
                genericSelect.innerHTML = `<option value="${med.generic}">${med.generic}</option>`;
                suggestionList.style.display = "none";
                if (recDiv) recDiv.style.display = "none";
              });
              suggestionList.appendChild(li);
            });
            suggestionList.style.display = "block";
          } else {
            suggestionList.style.display = "none";
            if (recDiv) {
              recDiv.style.display = "block";
              recDiv.querySelector(".alert").innerText = "No matching medicine found. Please verify your brand name or create a new record.";
            }
          }
        })
        .catch(error => console.error("[DEBUG] Error fetching suggestions:", error));
    });
    // For Update Supply Modal
    const updateBrandInput = document.getElementById("updateItemName");
    const updateGenericSelect = document.getElementById("updateCategory");
    if (updateBrandInput) {
      updateBrandInput.addEventListener("input", function() {
        const query = this.value.trim();
        if (query === "") {
          updateGenericSelect.innerHTML = "<option value=''>Select Generic</option>";
          return;
        }
        fetch("fetch_med.php?brand=" + encodeURIComponent(query))
          .then(response => response.json())
          .then(data => {
            const match = data.find(med => med.brand.toLowerCase() === query.toLowerCase());
            if (match) {
              updateGenericSelect.innerHTML = `<option value="${match.generic}">${match.generic}</option>`;
              updateBrandInput.value = match.brand;
            } else {
              updateGenericSelect.innerHTML = "<option value=''>Select Generic</option>";
            }
          })
          .catch(error => console.error("Error fetching suggestions:", error));
      });
    }
  });
</script>

<!-- --- Report Generation --- -->
<!-- The following code uses the fetched full name from PHP -->
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
      const table = document.querySelector("table");
      const rows = Array.from(table.querySelectorAll("tbody tr")).map(row => {
        return Array.from(row.querySelectorAll("td")).map(cell => cell.innerText);
      });
      doc.autoTable({
        head: [["#", "Item Name", "Category", "Unit", "Quantity", "Date Added"]],
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
</script>


<!-- Stock Details Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="stockModalLabel">Stock Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Brand Name</th>
                    <th>Strip</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody id="stockTableBody">
                <!-- Stock details will be injected here -->
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


</body>
</html>
