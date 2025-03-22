<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: superadmin/mainpage.php");
    exit();
}
include '../fetchfname.php';
include '../connection.php';
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
$uniqueId = isset($_GET['unique_id']) ? htmlspecialchars($_GET['unique_id']) : "";

// --- Download TXT mode (plain text) ---
// (This block remains available if triggered externally.)
if (isset($_GET['download']) && $_GET['download'] === 'text') {
    if (isset($_GET['selected']) && !empty($_GET['selected'])) {
        $idsArray = array_filter(explode(",", $_GET['selected']), function($id) {
            return is_numeric($id);
        });
        if (empty($idsArray)) {
            echo "No valid records selected.";
            exit();
        }
        $idsString = implode(",", $idsArray);
        $query = "SELECT id, fullname, student_number, contact, s_gender, age, year_level, conditions, treatment,
                         DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') AS formatted_created_at 
                  FROM bcp_sms3_patients
                  WHERE id IN ($idsString)";
    } else {
        echo "No records selected.";
        exit();
    }
    
    $result = $conn->query($query);
    
    $fileContent  = "Selected Medical Records\n";
    $fileContent .= "ID\tFull Name\tStudent Number\tContact\tGender\tAge\tYear Level\tConditions\tTreatment\tCreated At\n";
    while ($row = $result->fetch_assoc()) {
        $fileContent .= $row['id'] . "\t" .
                        $row['fullname'] . "\t" .
                        $row['student_number'] . "\t" .
                        $row['contact'] . "\t" .
                        $row['s_gender'] . "\t" .
                        $row['age'] . "\t" .
                        $row['year_level'] . "\t" .
                        $row['conditions'] . "\t" .
                        $row['treatment'] . "\t" .
                        $row['formatted_created_at'] . "\n";
    }
    // Append the unique reference
    $fileContent .= "\nREFERENCE: " . $uniqueId;
    // Build file name: if the user provided a name, append " ($uniqueId)" before the extension.
    $baseName = isset($_GET['file_name']) ? preg_replace("/[^A-Za-z0-9\-_]/", "", $_GET['file_name']) : "SelectedMedicalRecords";
    $desiredName = $baseName . " (" . $uniqueId . ")";
    header("Content-Type: text/plain; charset=UTF-8");
    header("Content-Disposition: attachment; filename={$desiredName}.txt");
    echo $fileContent;
    exit();
}

$query = "SELECT id, fullname, student_number, contact, s_gender, age, year_level, conditions, treatment,
         DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') AS formatted_created_at 
         FROM bcp_sms3_patients";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clinic Management System / Medical Records</title>
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <!-- Include PDF.js, Mammoth.js, jsPDF, and AutoTable -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.21/mammoth.browser.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
  <style>
    .responsive-input { width: 100%; }
    .toolbar { flex-wrap: wrap; gap: 0.5rem; }
    @media (max-width: 768px) { .toolbar button { width: 100%; } }
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
  
  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <div class="logo-container text-center mb-2">
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
  
  <!-- Main Content -->
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Medical Records</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Medical Records</li>
        </ol>
      </nav>
      <p class="text-muted">Reference: <?php echo $uniqueId; ?></p>
    </div>
    
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Patient Medical Records</h5>
              
              <!-- Toolbar Buttons -->
              <div class="d-flex toolbar mb-3">
                <button onclick="window.location.href='integ.php'" class="btn btn-primary">Back</button>
                <!-- Generate Text button removed -->
                <button id="generatePdfBtn" class="btn btn-info">Generate PDF</button>
                <button id="generateDocBtn" class="btn btn-info">Generate DOC</button>
                <button onclick="window.location.href='send.php?unique_id=<?php echo $uniqueId; ?>'" class="btn btn-info">Next</button>
              </div>
              
              <!-- File Input and Search -->
              <div class="row g-3 mb-3">
                <div class="col-sm-12 col-md-6">
                  <label for="uploadFile" class="form-label">Upload PDF, DOC, or TXT File:</label>
                  <input type="file" id="uploadFile" class="form-control responsive-input" accept=".pdf,.doc,.docx,.txt">
                  <button id="readBtn" class="btn btn-secondary mt-2">Read</button>
                </div>
                <div class="col-sm-12 col-md-6">
                  <label for="searchInput" class="form-label">Search for patients:</label>
                  <input type="text" id="searchInput" class="form-control responsive-input" placeholder="Search...">
                </div>
              </div>
              
              <!-- Responsive Table -->
              <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle" id="recordsTable">
                  <thead class="table-light">
                    <tr>
                      <th>ID</th>
                      <th>Full Name</th>
                      <th>Student Number</th>
                      <th>Contact</th>
                      <th>Gender</th>
                      <th>Age</th>
                      <th>Year Level</th>
                      <th>Conditions</th>
                      <th>Treatment</th>
                      <th>Created At</th>
                      <th class="text-center">
                        Check All<br>
                        <input type="checkbox" id="checkAll">
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr data-id="<?php echo htmlspecialchars($row['id']); ?>">
                      <td><?php echo htmlspecialchars($row['id']); ?></td>
                      <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                      <td><?php echo htmlspecialchars($row['student_number']); ?></td>
                      <td><?php echo htmlspecialchars($row['contact']); ?></td>
                      <td><?php echo htmlspecialchars($row['s_gender']); ?></td>
                      <td><?php echo htmlspecialchars($row['age']); ?></td>
                      <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                      <td><?php echo htmlspecialchars($row['conditions']); ?></td>
                      <td><?php echo htmlspecialchars($row['treatment']); ?></td>
                      <td><?php echo htmlspecialchars($row['formatted_created_at']); ?></td>
                      <td class="text-center">
                        <input type="checkbox" class="row-check">
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
              
              <!-- Modal for Unmatched Student Numbers -->
              <div class="modal fade" id="unmatchedModal" tabindex="-1" aria-labelledby="unmatchedModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="unmatchedModalLabel">These Students don't have Records:</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p id="unmatchedList" class="fw-bold"></p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
              
            </div><!-- End Card Body -->
          </div><!-- End Card -->
        </div><!-- End Column -->
      </div><!-- End Row -->
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
          <button type="button" class="btn btn-primary" id="confirmSendButton">Confirm</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- File Name Modal for Custom File Naming -->
  <div class="modal fade" id="fileNameModal" tabindex="-1" aria-labelledby="fileNameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="fileNameModalLabel">Enter File Name</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="text" id="customFileName" class="form-control" placeholder="Enter file name">
        </div>
        <div class="modal-footer">
          <button type="button" id="confirmFileName" class="btn btn-primary">Generate File</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Alert Modal -->
  <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="alertModalLabel">Notice</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="alertModalBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Result Modal -->
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
    // Function to display modal alerts
    function showAlert(message) {
      console.log("Alert:", message);
      document.getElementById("alertModalBody").textContent = message;
      let alertModal = new bootstrap.Modal(document.getElementById("alertModal"));
      alertModal.show();
    }
    
    // Add debugging logs for Generate PDF button
    document.getElementById('generatePdfBtn').addEventListener('click', function() {
      console.log("Generate PDF button clicked");
      let selectedData = getSelectedRowsData();
      if (selectedData.length === 0) {
        showAlert("Please select at least one visible row.");
        return;
      }
      currentFileType = 'pdf';
      currentSelectedData = selectedData;
      let fileNameModal = new bootstrap.Modal(document.getElementById("fileNameModal"));
      fileNameModal.show();
    });
    
    // Add debugging logs for Generate DOC button
    document.getElementById('generateDocBtn').addEventListener('click', function() {
      console.log("Generate DOC button clicked");
      let selectedData = getSelectedRowsData();
      if (selectedData.length === 0) {
        showAlert("Please select at least one visible row.");
        return;
      }
      currentFileType = 'doc';
      currentSelectedData = selectedData;
      let fileNameModal = new bootstrap.Modal(document.getElementById("fileNameModal"));
      fileNameModal.show();
    });
    
    // Event listener for file name confirmation (if needed for PDF/DOC generation)
    document.getElementById("confirmFileName").onclick = function() {
      let fileName = document.getElementById("customFileName").value.trim();
      if (!fileName) {
        showAlert("Please enter a valid file name.");
        return;
      }
      // Append unique ID to file name.
      fileName = fileName + " (<?php echo $uniqueId; ?>)";
      let modalElem = document.getElementById("fileNameModal");
      let fileNameModal = bootstrap.Modal.getInstance(modalElem);
      fileNameModal.hide();
      
      if (currentFileType === 'txt') {
        window.location.href = "generatePDF.php?unique_id=<?php echo $uniqueId; ?>&download=text&selected=" +
          encodeURIComponent(currentSelectedIds) + "&file_name=" + encodeURIComponent(fileName);
      } else if (currentFileType === 'pdf') {
        let { jsPDF } = window.jspdf;
        let doc = new jsPDF();
        doc.setFontSize(16);
        doc.text("Selected Medical Records", 105, 20, { align: "center" });
        let columns = [
          { header: 'ID', dataKey: 'id' },
          { header: 'Full Name', dataKey: 'fullname' },
          { header: 'Student Number', dataKey: 'student_number' },
          { header: 'Contact', dataKey: 'contact' },
          { header: 'Gender', dataKey: 's_gender' },
          { header: 'Age', dataKey: 'age' },
          { header: 'Year Level', dataKey: 'year_level' },
          { header: 'Conditions', dataKey: 'conditions' },
          { header: 'Treatment', dataKey: 'treatment' },
          { header: 'Created At', dataKey: 'formatted_created_at' }
        ];
        doc.autoTable({
          startY: 30,
          head: [columns.map(col => col.header)],
          body: currentSelectedData.map(row => columns.map(col => row[col.dataKey]))
        });
        let finalY = doc.lastAutoTable.finalY || 30;
        doc.setFontSize(12);
        doc.text("REFERENCE: <?php echo $uniqueId; ?>", 10, finalY + 10);
        doc.save(fileName + ".pdf");
      } else if (currentFileType === 'doc') {
        let htmlContent = `<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Selected Medical Records</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #0056b3; color: white; }
  </style>
</head>
<body>
  <h1 style="text-align: center;">Selected Medical Records</h1>
  <table>
    <thead>
      <tr>`;
        for (let key in currentSelectedData[0]) {
          htmlContent += `<th>${key}</th>`;
        }
        htmlContent += `</tr>
    </thead>
    <tbody>`;
        currentSelectedData.forEach(function(row) {
          htmlContent += `<tr>`;
          for (let key in row) {
            htmlContent += `<td>${row[key]}</td>`;
          }
          htmlContent += `</tr>`;
        });
        htmlContent += `
    </tbody>
  </table>
  <p>REFERENCE: <?php echo $uniqueId; ?></p>
</body>
</html>`;
        let blob = new Blob([htmlContent], { type: "application/msword" });
        let url = URL.createObjectURL(blob);
        let a = document.createElement("a");
        a.href = url;
        a.download = fileName + ".doc";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      }
    };
    
    // AJAX submission for Send operation.
    document.getElementById('confirmSendButton').addEventListener('click', function() {
      console.log("confirmSendButton clicked.");
      let form = document.getElementById('sendForm');
      let formData = new FormData(form);
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
    
    // Helper function to retrieve selected rows' data
    function getSelectedRowsData() {
      let table = document.getElementById("recordsTable");
      let tbody = table.tBodies[0];
      let rows = tbody.getElementsByTagName("tr");
      let data = [];
      for (let i = 0; i < rows.length; i++) {
        if (rows[i].style.display === "none") continue;
        let checkbox = rows[i].querySelector(".row-check");
        if (checkbox && checkbox.checked) {
          let cells = rows[i].getElementsByTagName("td");
          data.push({
            id: cells[0].textContent.trim(),
            fullname: cells[1].textContent.trim(),
            student_number: cells[2].textContent.trim(),
            contact: cells[3].textContent.trim(),
            s_gender: cells[4].textContent.trim(),
            age: cells[5].textContent.trim(),
            year_level: cells[6].textContent.trim(),
            conditions: cells[7].textContent.trim(),
            treatment: cells[8].textContent.trim(),
            formatted_created_at: cells[9].textContent.trim()
          });
        }
      }
      return data;
    }
  </script>
  
</body>
</html>
