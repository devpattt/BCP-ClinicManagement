<?php
// viewMedicalRecords.php
session_start();
require_once "../connection.php";

// (Optional) Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
  header("Location: ../login.php");
  exit();
}
$uniqueId = isset($_GET['unique_id']) ? htmlspecialchars($_GET['unique_id']) : "";

$query = "SELECT id, fullname, student_number, contact, s_gender, age, year_level, conditions, treatment, 
         DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') AS formatted_created_at 
         FROM bcp_sms3_patients";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Medical Records</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background-color: #f8f9fa; }
    header.header {
      background-color: cyan;
      padding: 10px;
    }
    .header-title {
      margin: 0;
      font-size: 1.5rem;
      color: #000;
    }
    .main-content {
      transition: margin-right 0.3s ease;
      margin-top: 20px;
      text-align: left;
    }
    #searchInput { margin-bottom: 10px; }
    .check-col { text-align: center; vertical-align: middle; }
    .file-read-group { display: flex; align-items: center; }
    .file-read-group > * { margin-right: 10px; }
  </style>
  <!-- Include jsPDF and AutoTable -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
  <!-- Header Section -->
  <header id="header" class="header d-flex justify-content-between align-items-center">
    <div class="ms-3">
      <h1 class="header-title">Medical Records</h1>
    </div>
    <div class="me-3">
      <!-- Burger menu to toggle offcanvas navigation on the right -->
      <button class="btn btn-outline-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" aria-controls="offcanvasNav">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </header>

  <!-- Offcanvas Navigation (Right Side) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasNavLabel">Navigation</h5>
    <p id="uniqueRef" class="mb-0">Reference: <?php echo $uniqueId; ?></p>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-grid gap-2">
      <button onclick="window.location.href='integ.php'" class="btn btn-primary">Back</button>
      <button id="generateDocsBtn" class="btn btn-success">Generate DOC</button>
      <button id="generatePdfBtn" class="btn btn-warning">Generate PDF</button>
      <button onclick="window.location.href='nextPage.php?unique_id=<?php echo $uniqueId; ?>'" class="btn btn-info">Next</button>

    </div>
  </div>
</div>


  <!-- Main Content -->
  <div class="container main-content">
    <!-- File Input and Read Button Group -->
    <div class="mb-3 file-read-group">
      <div class="flex-grow-1">
        <label for="uploadFile" class="form-label">Upload PDF or DOCS File:</label>
        <!-- Accept .txt for testing -->
        <input type="file" id="uploadFile" class="form-control" accept=".pdf,.doc,.docx,.txt">
      </div>
      <div class="mt-4">
        <button id="readBtn" class="btn btn-secondary btn-sm">Read</button>
      </div>
    </div>
    <!-- Search Input -->
    <div class="mb-3">
      <input type="text" id="searchInput" class="form-control" placeholder="Search for patients...">
    </div>
    <!-- Responsive Table -->
    <div class="table-responsive">
      <table class="table table-bordered" id="recordsTable">
        <thead>
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
            <th class="check-col">
              Check All <br>
              <input type="checkbox" id="checkAll">
            </th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
          <tr>
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
            <td class="check-col">
              <input type="checkbox" class="row-check">
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal for Unmatched Student Numbers -->
  <div class="modal fade" id="unmatchedModal" tabindex="-1" aria-labelledby="unmatchedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="unmatchedModalLabel">This Students don't have a Records:</h5>
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

  <!-- JavaScript -->
  <script>
  // Search functionality: search in Full Name (index 1), Student Number (index 2), Contact (index 3), Gender (index 4), Age (index 5), Year Level (index 6), and Created At (index 9)
  document.getElementById("searchInput").addEventListener("keyup", function() {
    var filter = this.value.toUpperCase();
    var table = document.getElementById("recordsTable");
    var tr = table.getElementsByTagName("tr");
    var searchableIndices = [1, 2, 3, 4, 5, 6, 9];
    for (var i = 1; i < tr.length; i++) { // skip header row
      var tds = tr[i].getElementsByTagName("td");
      var rowContainsFilter = false;
      for (var j = 0; j < searchableIndices.length; j++) {
        var index = searchableIndices[j];
        if (tds[index] && tds[index].textContent.toUpperCase().indexOf(filter) > -1) {
          rowContainsFilter = true;
          break;
        }
      }
      tr[i].style.display = rowContainsFilter ? "" : "none";
    }
  });

  // Master "Check All" functionality: only check visible rows
  document.getElementById("checkAll").addEventListener("change", function() {
    var isChecked = this.checked;
    var table = document.getElementById("recordsTable");
    var tr = table.getElementsByTagName("tr");
    for (var i = 1; i < tr.length; i++) {
      if (tr[i].style.display !== "none") {
        var chk = tr[i].querySelector(".row-check");
        if (chk) {
          chk.checked = isChecked;
        }
      }
    }
  });

  // Adjust main content margin when offcanvas navigation opens/closes
  var offcanvasEl = document.getElementById('offcanvasNav');
  offcanvasEl.addEventListener('shown.bs.offcanvas', function () {
    var offcanvasWidth = offcanvasEl.offsetWidth;
    document.querySelector('.main-content').style.marginRight = offcanvasWidth + "px";
  });
  offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
    document.querySelector('.main-content').style.marginRight = "";
  });

  // Function to gather selected row data from visible rows only
  function getSelectedRowsData() {
    var table = document.getElementById("recordsTable");
    var tbody = table.tBodies[0];
    var rows = tbody.getElementsByTagName("tr");
    var data = [];
    for (var i = 0; i < rows.length; i++) {
      // Only consider visible rows
      if (rows[i].style.display === "none") continue;
      var checkbox = rows[i].querySelector(".row-check");
      if (checkbox && checkbox.checked) {
        var cells = rows[i].getElementsByTagName("td");
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

  // Generate DOC file from selected rows data
  document.getElementById('generateDocsBtn').addEventListener('click', function() {
    var selectedData = getSelectedRowsData();
    if (selectedData.length === 0) {
      alert("Please select at least one visible row.");
      return;
    }
    
    var htmlContent = `
<!DOCTYPE html>
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
      </tr>
    </thead>
    <tbody>
`;
    selectedData.forEach(function(row) {
      htmlContent += `<tr>
        <td>${row.id}</td>
        <td>${row.fullname}</td>
        <td>${row.student_number}</td>
        <td>${row.contact}</td>
        <td>${row.s_gender}</td>
        <td>${row.age}</td>
        <td>${row.year_level}</td>
        <td>${row.conditions}</td>
        <td>${row.treatment}</td>
        <td>${row.formatted_created_at}</td>
      </tr>`;
    });
    
    htmlContent += `
    </tbody>
  </table>
</body>
</html>
`;
    var blob = new Blob([htmlContent], { type: "application/msword" });
    var url = URL.createObjectURL(blob);
    var a = document.createElement("a");
    a.href = url;
    a.download = "SelectedMedicalRecords.doc";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  });

  // Generate PDF file from selected rows data using jsPDF and AutoTable
  document.getElementById('generatePdfBtn').addEventListener('click', function() {
    var selectedData = getSelectedRowsData();
    if (selectedData.length === 0) {
      alert("Please select at least one visible row.");
      return;
    }
    
    var { jsPDF } = window.jspdf;
    var doc = new jsPDF();
    
    doc.setFontSize(16);
    doc.text("Selected Medical Records", 105, 20, { align: "center" });
    
    var columns = [
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
      body: selectedData.map(function(row) {
        return columns.map(function(col) {
          return row[col.dataKey];
        });
      })
    });
    
    doc.save("SelectedMedicalRecords.pdf");
  });

  // Read Button: Extract student numbers from the file and filter table rows.
  document.getElementById('readBtn').addEventListener('click', function() {
    var fileInput = document.getElementById('uploadFile');
    if(fileInput.files.length === 0){
      alert("Please upload a file first.");
      return;
    }
    var file = fileInput.files[0];
    var reader = new FileReader();
    reader.onload = function(e){
      var text = e.target.result;
      // Extract valid student numbers using regex (pattern: s followed by exactly 8 digits)
      var regex = /s\d{8}/gi;
      var matches = text.match(regex);
      if(matches){
        var extractedNumbers = [...new Set(matches.map(s => s.toLowerCase()))];
        console.log("Extracted student numbers: ", extractedNumbers);
        // Filter table rows: display only rows whose student number (column index 2) is found in extractedNumbers.
        var table = document.getElementById("recordsTable");
        var rows = table.getElementsByTagName("tr");
        // Build a list of student numbers present in the table (only from visible rows)
        var tableNumbers = [];
        for(var i = 1; i < rows.length; i++){
          var td = rows[i].getElementsByTagName("td")[2];
          if(td){
            var num = td.textContent.trim().toLowerCase();
            tableNumbers.push(num);
            if(extractedNumbers.indexOf(num) !== -1){
              rows[i].style.display = "";
            } else {
              rows[i].style.display = "none";
            }
          }
        }
        // Determine unmatched extracted numbers
        var unmatched = extractedNumbers.filter(function(num){
          return tableNumbers.indexOf(num) === -1;
        });
        if(unmatched.length > 0){
          document.getElementById("unmatchedList").innerText = unmatched.join(", ");
          var unmatchedModal = new bootstrap.Modal(document.getElementById('unmatchedModal'));
          unmatchedModal.show();
        }
      } else {
        alert("No student numbers found in the file.");
      }
    };
    reader.readAsText(file);
  });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
