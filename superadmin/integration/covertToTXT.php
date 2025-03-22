<?php
// Server-side: Process conversion on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only allow PDF files in this example.
    $allowedTypes = ['pdf'];
    $uploadDir = '../../uploads/';

    if (!isset($_FILES['document']) || $_FILES['document']['error'] === UPLOAD_ERR_NO_FILE) {
        http_response_code(400);
        echo json_encode(["error" => "No file selected."]);
        exit();
    }
    if ($_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["error" => "An upload error occurred."]);
        exit();
    }

    $fileName = trim($_FILES['document']['name']);
    $fileTmpPath = $_FILES['document']['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid file type. Only PDF files are allowed."]);
        exit();
    }

    // Create a unique file name and move the uploaded file.
    $uniqueId = isset($_GET['unique_id']) ? htmlspecialchars($_GET['unique_id']) : uniqid();
    $newFileName = $uniqueId . '_' . time() . '.' . $fileExtension;
    $destination = $uploadDir . $newFileName;
    if (!move_uploaded_file($fileTmpPath, $destination)) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to move uploaded file."]);
        exit();
    }

    // Function that attempts conversion with pdftotext.
    function convertPDFWithPdftotext($filePath) {
        $command = "pdftotext " . escapeshellarg($filePath) . " -";
        error_log("pdftotext command: $command");
        $output = shell_exec($command);
        error_log("pdftotext output: " . var_export($output, true));
        return $output;
    }

    // Fallback conversion function: extract text between "BT" and "ET"
    function convertPDFWithRegex($filePath) {
        $data = file_get_contents($filePath);
        if ($data === false) {
            return "";
        }
        $matches = [];
        // Match text between BT and ET, including newlines.
        preg_match_all('/BT(.*?)ET/s', $data, $matches);
        $extracted = "";
        if (isset($matches[1]) && count($matches[1]) > 0) {
            foreach ($matches[1] as $block) {
                // Remove any parenthesized parts; this is basic and may require adjustments.
                $block = preg_replace('/\((.*?)\)/s', '$1', $block);
                $extracted .= " " . $block;
            }
        }
        return trim($extracted);
    }

    // Helper function to format the text (trim lines)
    function formatConvertedText($text) {
        $lines = explode("\n", $text);
        $formattedLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (!empty($trimmed)) {
                $formattedLines[] = $trimmed;
            }
        }
        return implode("\n", $formattedLines);
    }

    // First, try pdftotext.
    $convertedText = convertPDFWithPdftotext($destination);
    if (trim($convertedText) == "") {
        // If pdftotext fails (returns empty), try the regex fallback.
        error_log("pdftotext returned empty, using fallback method.");
        $convertedText = convertPDFWithRegex($destination);
    }

    if (trim($convertedText) == "") {
        http_response_code(500);
        echo json_encode(["error" => "Failed to convert PDF to text."]);
        exit();
    }

    $formattedText = formatConvertedText($convertedText);

    // Optionally, store details and converted text in your database.
    include '../../connection.php';
    if ($conn) {
        $sql = "INSERT INTO file_uploads (unique_id, file_name, file_type, file_path, converted_text, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $uniqueId, $fileName, $fileExtension, $destination, $formattedText);
            $stmt->execute();
            $stmt->close();
        }
        $conn->close();
    }

    http_response_code(200);
    echo json_encode([
        "message" => "PDF processed successfully.",
        "converted_text" => $formattedText,
        "download_filename" => $uniqueId . "_converted.txt"
    ]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Show PDF and Generate Text Copy</title>
  <link href="../../assets/img/bcp logo.png" rel="icon">
  <link href="../../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
  <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../../assets/css/style.css" rel="stylesheet">
  <style>
    .pagetitle { margin: 50px 0 20px; text-align: center; }
    #pdfPreview { width: 100%; height: 500px; border: 1px solid #ddd; margin-bottom: 20px; }
    #textPreview { white-space: pre-wrap; border: 1px solid #ccc; padding: 10px; margin-top: 20px; }
    #downloadBtn { margin-top: 10px; }
  </style>
</head>
<body>
  <main id="main" class="main" style="max-width:800px; margin: auto; padding:20px;">
    <div class="pagetitle">
      <h1>Show PDF and Generate Text Copy</h1>
      <p class="text-muted">Select a PDF file, click "Show PDF" to preview it, and see the generated text copy below.</p>
    </div>
    
    <section class="section">
      <div class="card">
        <div class="card-body">
          <form id="uploadForm" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="document" class="form-label">Select PDF:</label>
              <input type="file" name="document" id="document" class="form-control" accept=".pdf" required>
            </div>
            <button type="button" id="showBtn" class="btn btn-primary">Show PDF</button>
          </form>
          
          <!-- PDF Preview Area -->
          <div id="previewContainer" style="margin-top:20px; display:none;">
            <h5>PDF Preview</h5>
            <embed id="pdfPreview" src="" type="application/pdf">
          </div>
          
          <!-- Text Copy Preview Area -->
          <div id="textContainer" style="margin-top:20px; display:none;">
            <h5>Generated Text Copy</h5>
            <div id="textPreview"></div>
            <button id="downloadBtn" class="btn btn-success">
              <i class="bi bi-download"></i> Download Text Copy
            </button>
          </div>
        </div>
      </div>
    </section>
  </main>
  
  <!-- Modal for messages -->
  <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="messageModalLabel">Notification</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="messageModalBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    // Function to display messages in a modal.
    function showModal(message) {
      document.getElementById('messageModalBody').textContent = message;
      new bootstrap.Modal(document.getElementById('messageModal')).show();
    }
    
    let selectedFile = null;
    let convertedData = null;
    
    document.getElementById('document').addEventListener('change', function() {
      selectedFile = this.files[0] || null;
      // Reset preview areas when a new file is selected.
      document.getElementById('previewContainer').style.display = 'none';
      document.getElementById('textContainer').style.display = 'none';
      convertedData = null;
    });
    
    document.getElementById('showBtn').addEventListener('click', function() {
      if (!selectedFile) {
        showModal("Please select a PDF file first.");
        return;
      }
      
      // Create a blob URL for PDF preview.
      const fileURL = URL.createObjectURL(selectedFile);
      document.getElementById('pdfPreview').src = fileURL;
      document.getElementById('previewContainer').style.display = 'block';
      
      // Send the PDF to the server for conversion.
      const formData = new FormData();
      formData.append('document', selectedFile);
      
      fetch(window.location.href, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          showModal(data.error);
        } else {
          convertedData = data;
          document.getElementById('textPreview').innerHTML = '<pre>' + data.converted_text + '</pre>';
          document.getElementById('textContainer').style.display = 'block';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showModal("An error occurred while processing the file.");
      });
    });
    
    document.getElementById('downloadBtn').addEventListener('click', function() {
      if (!convertedData) {
        showModal("No converted text available.");
        return;
      }
      const blob = new Blob([convertedData.converted_text], { type: 'text/plain' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = convertedData.download_filename;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
    });
  </script>
</body>
</html>
