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

        <!-- Card 1: Patient Today -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card sales-card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i ></i></a>
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
              <a class="icon" href="#" data-bs-toggle="dropdown"><i></i></a>
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
              <a class="icon" href="#" data-bs-toggle="dropdown"><i></i></a>
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

    <!-- Supplies Table -->
    <div class="container mt-5">
      <button class="btn btn-primary mb-3" style="background-color: #1e3a8a; border-color: #1e3a8a;" data-bs-toggle="modal" data-bs-target="#addSupplyModal">
        Add Supply
      </button>
      <button class="btn btn-primary mb-3" style="background-color: #1e3a8a; border-color: #1e3a8a;" onclick="generateReport()">
        Generate Report
      </button>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Item Name</th>
            <th>Category</th>
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
              <div class="mb-3">
                <label for="itemName" class="form-label">Item Name</label>
                <input type="text" class="form-control custom-border" id="itemName" name="item_name" required>
              </div>
              <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                  <option value="">Select Category</option>
                  <option value="Analgesics">Analgesics</option>
                  <option value="Antibiotics">Antibiotics</option>
                  <option value="Antipyretics">Antipyretics</option>
                  <option value="Antihistamines">Antihistamines</option>
                  <option value="CoughCold">Cough and Cold Medications</option>
                  <option value="AntiInflammatory">Anti-inflammatory Drugs</option>
                  <option value="Gastrointestinal">Gastrointestinal Medications</option>
                  <option value="Antiseptics">Antiseptics</option>
                  <option value="Disinfectants">Disinfectants</option>
                  <option value="Bronchodilators">Bronchodilators</option>
                  <option value="Asthma Medications">Asthma Medications</option>
                  <option value="Vitamins">Vitamins</option>
                  <option value="Supplements">Supplements</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="unit" class="form-label">Unit</label>
                <input type="number" class="form-control custom-border" id="unit" name="unit" required>
              </div>
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
                <label for="updateItemName" class="form-label">Item Name</label>
                <input type="text" class="form-control custom-border" id="updateItemName" name="item_name" required>
              </div>
              <div class="mb-3">
                <label for="updateCategory" class="form-label">Category</label>
                <select class="form-select" id="updateCategory" name="category" required>
                  <option value="">Select Category</option>
                  <option value="Analgesics">Analgesics</option>
                  <option value="Antibiotics">Antibiotics</option>
                  <option value="Antipyretics">Antipyretics</option>
                  <option value="Antihistamines">Antihistamines</option>
                  <option value="CoughCold">Cough and Cold Medications</option>
                  <option value="AntiInflammatory">Anti-inflammatory Drugs</option>
                  <option value="Gastrointestinal">Gastrointestinal Medications</option>
                  <option value="Antiseptics">Antiseptics</option>
                  <option value="Disinfectants">Disinfectants</option>
                  <option value="Bronchodilators">Bronchodilators</option>
                  <option value="Asthma Medications">Asthma Medications</option>
                  <option value="Vitamins">Vitamins</option>
                  <option value="Supplements">Supplements</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="updateUnit" class="form-label">Unit</label>
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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Bootstrap & Additional JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
  <script src="../assets/js/main.js"></script>

  <!-- Client-Side Script to Handle Fetching and Modal Alerts -->
  <script>
  // Function to display messages using the alert modal
function showAlertModal(message) {
    const alertModalBody = document.getElementById("alertModalBody");
    alertModalBody.textContent = message;

    const alertModalElem = document.getElementById("alertModal");
    const alertModal = new bootstrap.Modal(alertModalElem);
    alertModal.show();

    // Refresh the page when the alert modal is closed
    alertModalElem.addEventListener("hidden.bs.modal", () => {
        location.reload();
    }, { once: true }); // Ensure the event runs only once
}

document.addEventListener("DOMContentLoaded", () => {
    const supplyTableBody = document.getElementById("supplyTableBody");

    // Function to fetch and display supplies
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
                            </td>
                        </tr>
                    `;
                });

                // Attach click event to dynamically added "Edit" buttons
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
            })
            .catch(err => console.error("Error fetching supplies:", err));
    }
    fetchSupplies();

    // Add Supply Form Submission
    const addSupplyForm = document.getElementById("addSupplyForm");
    if (addSupplyForm) {
        addSupplyForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const formData = new FormData(addSupplyForm);
            fetch("../addsupplies.php", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    // Close the Add Supply modal
                    const addSupplyModalElem = document.getElementById("addSupplyModal");
                    const addSupplyModal = bootstrap.Modal.getInstance(addSupplyModalElem) 
                                           || new bootstrap.Modal(addSupplyModalElem);
                    addSupplyModal.hide();

                    // Show success message
                    showAlertModal("Item added successfully.");
                } else {
                    showAlertModal("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showAlertModal("An error occurred while adding the supply.");
            });
        });
    }

    // Make editSupply function globally available
    window.editSupply = (code, itemName, category, unit, quantity) => {
        document.getElementById("updateCode").value = code;
        document.getElementById("updateItemName").value = itemName;
        document.getElementById("updateCategory").value = category;
        document.getElementById("updateUnit").value = unit;
        document.getElementById("updateQuantity").value = quantity;

        const updateSupplyModalElem = document.getElementById("updateSupplyModal");
        const updateSupplyModal = new bootstrap.Modal(updateSupplyModalElem);
        updateSupplyModal.show();
    };

    // Update Supply Form Submission
    const updateSupplyForm = document.getElementById("updateSupplyForm");
    if (updateSupplyForm) {
        updateSupplyForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const formData = new FormData(updateSupplyForm);
            fetch("../updatesupplies.php", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    // Close the Update Supply modal
                    const updateSupplyModalElem = document.getElementById("updateSupplyModal");
                    const updateSupplyModal = bootstrap.Modal.getInstance(updateSupplyModalElem) 
                                               || new bootstrap.Modal(updateSupplyModalElem);
                    updateSupplyModal.hide();

                    // Show success message
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
});




</script>

  <script> 
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

    // Fetch stock details when an icon is clicked
    const fetchStockDetails = (type) => {
        fetch(`../fetchlowstocks.php?type=${type}`)
            .then(response => response.json())
            .then(data => {
                const stockList = document.getElementById('stockList');
                stockList.innerHTML = "";
                if (data.length === 0) {
                    stockList.innerHTML = "<li>No items found.</li>";
                } else {
                    data.forEach(item => {
                        let listItem = document.createElement('li');
                        listItem.textContent = item;
                        stockList.appendChild(listItem);
                    });
                }
                new bootstrap.Modal(document.getElementById('stockModal')).show();
            })
            .catch(error => console.error('Error fetching stock details:', error));
    };

    document.querySelector('.bi-exclamation-triangle.text-danger').addEventListener('click', () => {
        fetchStockDetails("out");
    });

    document.querySelector('.bi-exclamation-triangle.text-primary').addEventListener('click', () => {
        fetchStockDetails("low");
    });
});


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
</body>
<!-- Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="stockModalLabel">Stock Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul id="stockList"></ul>
      </div>
    </div>
  </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
function generateReport() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Load the logo
    const logo = new Image();
    logo.src = "../assets/img/bcp logo.png"; // Path to your logo

    logo.onload = function () {
        // Add logo to the PDF (x: 10, y: 10, width: 20, height: 20)
        doc.addImage(logo, "PNG", 30, 10, 20, 24);

        // Centered Header
        doc.setFont("helvetica", "bold");
        doc.setFontSize(14);
        doc.text("Bestlink College of the Philippines", 105, 15, { align: "center" });
        doc.setFontSize(12);
        doc.text("College of Computer Studies", 105, 22, { align: "center" });

        // Report Title
        doc.setFontSize(16);
        doc.text("Medical Supplies Report", 105, 35, { align: "center" });

        // Extract table data
        const table = document.querySelector("table");
        const rows = Array.from(table.querySelectorAll("tbody tr")).map(row => {
            return Array.from(row.querySelectorAll("td")).map(cell => cell.innerText);
        });

        // Generate table in PDF
        doc.autoTable({
            head: [["#", "Item Name", "Category", "Unit", "Quantity", "Date Added"]],
            body: rows,
            startY: 45,
            theme: "grid",
        });

        // Get final Y position after the table
        const finalY = doc.lastAutoTable.finalY || 50;

        // Add timestamp at the bottom right
        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        doc.text(`Generated: ${new Date().toLocaleString()}`, 140, finalY + 10);

        // Save PDF
        doc.save("Medical_Supplies_Report.pdf");
    };
}
</script>



</html>