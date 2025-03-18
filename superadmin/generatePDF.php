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

// --- Download TXT mode: If "download=text" is set in GET, output only the selected records in a plain text file ---
if (isset($_GET['download']) && $_GET['download'] === 'text') {
    // Expect a comma-separated list of record IDs in the GET parameter 'selected'
    if (isset($_GET['selected']) && !empty($_GET['selected'])) {
        // Explode and sanitize the IDs (allow only numeric values)
        $idsArray = array_filter(explode(",", $_GET['selected']), function($id) {
            return is_numeric($id);
        });
        if (empty($idsArray)) {
            echo "<script>alert('No valid records selected.');</script>";
            exit();
        }
        $idsString = implode(",", $idsArray);
        $query = "SELECT id, fullname, student_number, contact, s_gender, age, year_level, conditions, treatment,
                         DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') AS formatted_created_at 
                  FROM bcp_sms3_patients
                  WHERE id IN ($idsString)";
    } else {
        echo "<script>alert('No records selected.');</script>";
        exit();
    }
    
    $result = $conn->query($query);
    
    header("Content-Type: text/plain; charset=UTF-8");
    header("Content-Disposition: attachment; filename=SelectedMedicalRecords.txt");
    
    // Build output: Title, header, then one row per record.
    $output  = "Selected Medical Records\n";
    $output .= "ID\tFull Name\tStudent Number\tContact\tGender\tAge\tYear Level\tConditions\tTreatment\tCreated At\n";
    while ($row = $result->fetch_assoc()) {
        $output .= $row['id'] . "\t" .
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
    // Append the unique_id at the bottom in the desired format.
    $output .= "\nREFERENCE: " . $uniqueId;
    
    echo $output;
    exit();
}

// For DOC/PDF generation and table display, run the following query.
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
  <!-- Include PDF.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
  <!-- Include Mammoth.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.21/mammoth.browser.min.js"></script>
  <style>
    .responsive-input { width: 100%; }
    .toolbar { flex-wrap: wrap; gap: 0.5rem; }
    @media (max-width: 768px) { .toolbar button { width: 100%; } }
  </style>
  <!-- jsPDF and AutoTable -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
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
                <button id="generateDocsBtn" class="btn" style="background-color: white; color: black;"></button>
                <button id="generatePdfBtn" class="btn" style="background-color: white; color: black;"></button>
                <button id="generateTextBtn" class="btn btn-info">Generate Text</button>
                <button onclick="window.location.href='send.php?unique_id=<?php echo $uniqueId; ?>'" class="btn btn-info">Next</button>
              </div>
              
              <!-- File Input and Search -->
              <div class="row g-3 mb-3">
                <div class="col-sm-12 col-md-6">
                  <label for="uploadFile" class="form-label">Upload PDF, DOCX, DOC, or TXT File:</label>
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
  
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
  
  <script>
    // Search functionality.
    document.getElementById("searchInput").addEventListener("keyup", function() {
      var filter = this.value.toUpperCase();
      var table = document.getElementById("recordsTable");
      var tr = table.getElementsByTagName("tr");
      var searchableIndices = [1, 2, 3, 4, 5, 6, 9];
      for (var i = 1; i < tr.length; i++) {
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
    
    // Master "Check All" functionality.
    document.getElementById("checkAll").addEventListener("change", function() {
      var isChecked = this.checked;
      var table = document.getElementById("recordsTable");
      var tr = table.getElementsByTagName("tr");
      for (var i = 1; i < tr.length; i++) {
        if (tr[i].style.display !== "none") {
          var chk = tr[i].querySelector(".row-check");
          if (chk) { chk.checked = isChecked; }
        }
      }
    });
    
    // Get selected rows data from visible rows only.
    function getSelectedRowsData() {
      var table = document.getElementById("recordsTable");
      var tbody = table.tBodies[0];
      var rows = tbody.getElementsByTagName("tr");
      var data = [];
      for (var i = 0; i < rows.length; i++) {
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
    
    // Generate Text file from selected rows.
    document.getElementById('generateTextBtn').addEventListener('click', function() {
      var selectedData = getSelectedRowsData();
      if (selectedData.length === 0) {
        alert("Please select at least one visible row.");
        return;
      }
      // Build a comma-separated list of record IDs.
      var selectedIds = selectedData.map(function(row) {
        return row.id;
      });
      var idString = selectedIds.join(',');
      // Redirect to TXT download mode with the selected record IDs.
      window.location.href = "generatePDF.php?unique_id=<?php echo $uniqueId; ?>&download=text&selected=" + encodeURIComponent(idString);
    });
    
    // Generate DOC file from selected rows data.
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
    .ref { text-align: right; margin-top: 50px; font-weight: bold; }
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
    <tbody>`;
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
  <p class="ref">REFERENCE: <?php echo $uniqueId; ?></p>
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
    
    // Generate PDF file from selected rows data using jsPDF and AutoTable.
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
      
      // Add the reference text at the bottom right.
      var pageWidth = doc.internal.pageSize.getWidth();
      var pageHeight = doc.internal.pageSize.getHeight();
      doc.setFontSize(10);
      doc.text("REFERENCE: <?php echo $uniqueId; ?>", pageWidth - 10, pageHeight - 10, { align: "right" });
      
      doc.save("SelectedMedicalRecords.pdf");
    });
  </script>
  <script>
  document.getElementById('readBtn').addEventListener('click', function() {
      var fileInput = document.getElementById('uploadFile');
      if (!fileInput.files.length) {
          alert("Please upload a file first.");
          return;
      }
      var file = fileInput.files[0];
      console.log("File selected:", file);
      var fileExtension = file.name.split('.').pop().toLowerCase();
      
      if(fileExtension === 'txt'){
          var reader = new FileReader();
          reader.onload = function(e) {
              var text = e.target.result;
              processTextContent(text);
          };
          reader.onerror = function(e) {
              console.error("Error reading TXT file", e);
              alert("Error reading file.");
          };
          reader.readAsText(file);
      }
      else if(fileExtension === 'pdf'){
          var fileReader = new FileReader();
          fileReader.onload = function() {
              var typedarray = new Uint8Array(this.result);
              pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
                  var maxPages = pdf.numPages;
                  var countPromises = [];
                  for (var j = 1; j <= maxPages; j++) {
                      countPromises.push(
                          pdf.getPage(j).then(function(page) {
                              return page.getTextContent().then(function(textContent) {
                                  var pageText = textContent.items.map(function(item) {
                                      return item.str;
                                  }).join(" ");
                                  return pageText;
                              });
                          })
                      );
                  }
                  Promise.all(countPromises).then(function(pagesText) {
                      var fullText = pagesText.join("\n");
                      processTextContent(fullText);
                  });
              }).catch(function(error){
                  console.error("Error processing PDF:", error);
                  alert("Error processing PDF file.");
              });
          };
          fileReader.onerror = function(e) {
              console.error("Error reading PDF file", e);
              alert("Error reading file.");
          };
          fileReader.readAsArrayBuffer(file);
      }
      else if(fileExtension === 'docx'){
          var fileReader = new FileReader();
          fileReader.onload = function(event) {
              var arrayBuffer = event.target.result;
              mammoth.extractRawText({arrayBuffer: arrayBuffer})
                  .then(function(result){
                      var text = result.value;
                      processTextContent(text);
                  })
                  .catch(function(error){
                      console.error("Error processing DOCX:", error);
                      alert("Error processing DOCX file.");
                  });
          };
          fileReader.onerror = function(e) {
              console.error("Error reading DOCX file", e);
              alert("Error reading file.");
          };
          fileReader.readAsArrayBuffer(file);
      }
      else if(fileExtension === 'doc'){
          // Fallback: attempt to read DOC files as plain text.
          var reader = new FileReader();
          reader.onload = function(e) {
              var text = e.target.result;
              processTextContent(text);
          };
          reader.onerror = function(e) {
              console.error("Error reading DOC file", e);
              alert("Error reading file.");
          };
          reader.readAsText(file);
      }
      else {
          alert("Unsupported file type for reading.");
      }
  });
  
  // Helper function to process extracted text.
  function processTextContent(text) {
      console.log("Extracted text content:", text);
      var regex = /s\d{8}/gi;
      var matches = text.match(regex);
      if (matches) {
          var extractedNumbers = [...new Set(matches.map(function(s) {
              return s.toLowerCase();
          }))];
          var table = document.getElementById("recordsTable");
          var tr = table.getElementsByTagName("tr");
          var tableNumbers = [];
          for (var i = 1; i < tr.length; i++) {
              var td = tr[i].getElementsByTagName("td")[2];
              if (td) {
                  var num = td.textContent.trim().toLowerCase();
                  tableNumbers.push(num);
                  tr[i].style.display = extractedNumbers.indexOf(num) !== -1 ? "" : "none";
              }
          }
          var unmatched = extractedNumbers.filter(function(num) {
              return tableNumbers.indexOf(num) === -1;
          });
          if (unmatched.length > 0) {
              document.getElementById("unmatchedList").innerText = unmatched.join(", ");
              var unmatchedModal = new bootstrap.Modal(document.getElementById('unmatchedModal'));
              unmatchedModal.show();
          }
      } else {
          alert("No student numbers found in the file.");
      }
  }
</script>

</body>
</html>
