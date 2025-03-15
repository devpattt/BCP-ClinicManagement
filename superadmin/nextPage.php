<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../superadmin/mainpage.php");
    exit();
}

include '../fetchfname.php';
include '../connection.php';
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$uniqueId = isset($_GET['unique_id']) ? htmlspecialchars($_GET['unique_id']) : "";

// Function to extract text from DOCX
function extractTextFromDocx($filePath) {
    $content = '';
    if (file_exists($filePath)) {
        $zip = new ZipArchive;
        if ($zip->open($filePath) === TRUE) {
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();
            if ($xml) {
                // Replace closing paragraph tags with newline.
                $xml = str_replace('</w:p>', "\n", $xml);
                $content = strip_tags($xml);
            }
        }
    }
    return trim($content);
}

// Function to extract text from PDF using pdftotext
function extractTextFromPdf($filePath) {
    $output = shell_exec("pdftotext " . escapeshellarg($filePath) . " -");
    return trim($output);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process only the uploaded file branch.
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $fileName    = trim($_FILES['document']['name']);
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        // Allow DOCX, DOC, PDF, and TXT.
        $allowedTypes = ['docx', 'pdf', 'doc', 'txt'];
        if (!in_array($fileExtension, $allowedTypes)) {
            echo "<script>alert('Invalid file type. Only DOCX, DOC, PDF, and TXT allowed.');</script>";
            exit();
        }
        if ($fileExtension == 'docx') {
            $textContent = extractTextFromDocx($fileTmpPath);
        } elseif ($fileExtension == 'pdf') {
            $textContent = extractTextFromPdf($fileTmpPath);
        } elseif ($fileExtension == 'txt') {
            $textContent = file_get_contents($fileTmpPath);
        } else {
            // For DOC files, treat similar to TXT.
            $textContent = file_get_contents($fileTmpPath);
        }
    } else {
        echo "<script>alert('No file uploaded or an upload error occurred.');</script>";
        exit();
    }
    
    // Use a memory stream and fgetcsv with tab delimiter to parse the file.
    $handle = fopen("data://text/plain," . $textContent, "r");
    if ($handle === false) {
        echo "<script>alert('Error opening data stream.');</script>";
        exit();
    }
    $rows = [];
    while (($data = fgetcsv($handle, 0, "\t")) !== false) {
        $data = array_map('trim', $data);
        if (implode('', $data) !== '') {
            $rows[] = $data;
        }
    }
    fclose($handle);
    
    // Expect at least 3 rows: title, header, and one data row.
    if (count($rows) < 3) {
        echo "<script>alert('Invalid file format. Not enough lines.');</script>";
        exit();
    }
    
    // Skip the first two rows (title and header)
    $dataRows = array_slice($rows, 2);
    
    $validRows = [];
    foreach ($dataRows as $row) {
        // Each valid row must have at least 10 columns and a numeric first column.
        if (count($row) >= 10 && is_numeric(trim($row[0]))) {
            $validRows[] = $row;
        }
    }
    
    if (empty($validRows)) {
        echo "<script>alert('No valid data rows found.');</script>";
        exit();
    }
    
    // Prepare SQL to insert each valid data row into bcp_sms3_inter_patient.
    $sql = "INSERT INTO bcp_sms3_inter_patient 
            (unique_id, fullname, student_number, contact, s_gender, age, year_level, conditions, treatment, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    if ($stmt = $conn->prepare($sql)) {
        $insertCount = 0;
        $i = 1;
        foreach ($validRows as $cols) {
            // Generate a new unique id for each row by appending a counter.
            $rowUniqueId = $uniqueId . '-' . $i;
            // Use columns 2 to 9 (indexes 1 to 8)
            $fullname       = $cols[1];
            $student_number = $cols[2];
            $contact        = $cols[3];
            $s_gender       = $cols[4];
            $age            = intval($cols[5]);
            $year_level     = $cols[6];
            $conditions     = $cols[7];
            $treatment      = $cols[8];
            
            if ($stmt->bind_param("sssssisss", $rowUniqueId, $fullname, $student_number, $contact, $s_gender, $age, $year_level, $conditions, $treatment)) {
                if ($stmt->execute()) {
                    $insertCount++;
                }
            }
            $i++;
        }
        // After successful insertion, update process status to "Done" in the bcp_sms3_reqmedreqcord table.
        if ($insertCount > 0) {
            // Note: Use unique_id = $uniqueId (without appended counter) if that's how it is stored in bcp_sms3_reqmedreqcord.
            $updateSQL = "UPDATE bcp_sms3_reqmedreqcord 
                          SET process='Done' 
                          WHERE unique_id = '" . $uniqueId . "'";
            if ($conn->query($updateSQL)) {
                echo "<script>alert('File processed and $insertCount record(s) successfully inserted!'); window.location.href='integ.php';</script>";
            } else {
                echo "<script>alert('Update failed: " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('No valid records found in file.');</script>";
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
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    .pagetitle { margin: 80px 0 20px; }
    .sidebar .logo-container { text-align: center; margin-bottom: 10px; }
    .card-header { background: #f7f7f7; border-bottom: 1px solid #e3e3e3; }
    .card-header .back-button { text-decoration: none; }
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
      <div class="logo-container">
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
              <a href="generatePDF.php?unique_id=<?php echo $uniqueId; ?>" class="btn btn-secondary back-button">
                <i class="bi bi-arrow-left"></i> Back
              </a>
              <h5 class="mb-0">Upload DOCX or PDF</h5>
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
              <!-- Reminder: The file must have a title row, a header row, then one or more data rows.
                   Each data row should have 10 tab-separated columns:
                   ID, Full Name, Student Number, Contact, Gender, Age, Year Level, Conditions, Treatment, Created At -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
  
  <!-- Include PDF.js and Mammoth.js for reading PDF and DOCX files -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.21/mammoth.browser.min.js"></script>
  
  <script>
    // Read button event handler to support TXT, DOC, PDF, and DOCX.
    document.getElementById('readBtn').addEventListener('click', function() {
        var fileInput = document.getElementById('uploadFile');
        if (!fileInput.files.length) {
            alert("Please upload a file first.");
            return;
        }
        var file = fileInput.files[0];
        console.log("File selected:", file);
        var fileExtension = file.name.split('.').pop().toLowerCase();
        
        if(fileExtension === 'txt' || fileExtension === 'doc'){
            var reader = new FileReader();
            reader.onload = function(e) {
                var text = e.target.result;
                processTextContent(text);
            };
            reader.onerror = function(e) {
                console.error("Error reading TXT/DOC file", e);
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
        else {
            alert("Unsupported file type for reading.");
        }
    });
    
    // Helper function to process extracted text content.
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
