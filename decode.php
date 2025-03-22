<?php
// decode.php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: superadmin/mainpage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Decode Hashed File</title>
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .result { margin-top: 20px; white-space: pre-wrap; border: 1px solid #ccc; padding: 10px; }
  </style>
</head>
<body>
  <h2>Convert Hashed File to Plain Text</h2>
  <form action="decode.php" method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="hashedFile" class="form-label">Upload Hashed TXT File:</label>
      <input type="file" class="form-control" id="hashedFile" name="hashedFile" accept=".txt" required>
    </div>
    <button type="submit" class="btn btn-primary">Decode File</button>
  </form>

  <?php
  if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["hashedFile"])) {
      if ($_FILES["hashedFile"]["error"] === UPLOAD_ERR_OK) {
          $fileTmpPath = $_FILES["hashedFile"]["tmp_name"];
          $hashedContent = file_get_contents($fileTmpPath);
          // Decode the content (Base64 decode)
          $plainText = base64_decode($hashedContent);
          if ($plainText === false) {
              echo '<div class="alert alert-danger mt-3">Failed to decode file. Please ensure the file is valid.</div>';
          } else {
              echo '<h4 class="mt-3">Decoded Content:</h4>';
              echo '<div class="result">' . htmlspecialchars($plainText) . '</div>';
          }
      } else {
          echo '<div class="alert alert-danger mt-3">Error uploading file.</div>';
      }
  }
  ?>
  
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

