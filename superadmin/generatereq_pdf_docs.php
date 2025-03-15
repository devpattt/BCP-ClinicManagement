<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Number Document Generator</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .container {
      margin-top: 40px;
    }
    textarea {
      width: 100%;
      height: 200px;
      font-family: monospace;
    }
    .btn {
      margin-right: 10px;
    }
  </style>
</head>
<body>
<div class="container">
  <h2 class="text-center">Student Number Document Generator</h2>
  <p>
    Enter the student numbers below. They can be separated by spaces, commas, or newlines.
    The system will extract any valid student number that follows the rule:
    a lowercase <strong>s</strong> followed by exactly 8 digits.
    <br>
    For example: <code>s21018803</code>
  </p>
  <textarea id="studentNumbersInput" placeholder="s21018803, some text, s12345678 &#10;other stuff s87654321"></textarea>
  <br>
  <div class="mt-3">
    <button id="generateDocBtn" class="btn btn-primary">Generate DOC</button>
    <button id="generatePdfBtn" class="btn btn-secondary">Generate PDF</button>
  </div>
</div>

<script>
// This function extracts all valid student numbers (pattern: s followed by 8 digits) from the input.
function extractValidStudentNumbers() {
  var content = document.getElementById('studentNumbersInput').value;
  // Use regex to match valid tokens; it will ignore any extra characters.
  var matches = content.match(/s\d{8}/gi) || [];
  // Normalize tokens to lowercase and deduplicate
  var validNumbers = [...new Set(matches.map(s => s.toLowerCase()))];
  return validNumbers;
}

document.getElementById('generateDocBtn').addEventListener('click', function() {
  var validNumbers = extractValidStudentNumbers();
  if (validNumbers.length === 0) {
    alert("No valid student numbers found. Please ensure each valid student number starts with 's' followed by exactly 8 digits.");
    return;
  }
  
  // Create a formatted content for DOC using only valid student numbers.
  var docContent = `
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Student Number Input Guideline</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    h1, h2 { text-align: center; color: #0056b3; }
    .instructions ol { margin-left: 20px; }
    .sample { background-color: #f1f1f1; padding: 5px 10px; border: 1px dashed #ccc; display: inline-block; }
    pre { background: #f9f9f9; border: 1px solid #ddd; padding: 10px; }
  </style>
</head>
<body>
  <h1>Student Number Input Guideline</h1>
  <div class="instructions">
    <p>Please follow the format below when entering student numbers:</p>
    <ol>
      <li>Each student number must start with the lowercase letter <strong>s</strong>.</li>
      <li>Followed by exactly 8 digits (0–9).</li>
      <li>No spaces, dashes, or extra characters are allowed (the system will extract valid numbers only).</li>
    </ol>
    <p>Example: <span class="sample">s21018***</span></p>
  </div>
  <h2>Input Student Numbers</h2>
  <pre>${validNumbers.join("\n")}</pre>
</body>
</html>
  `;
  
  // Create a Blob and trigger DOC download.
  var blob = new Blob([docContent], { type: "application/msword" });
  var url = URL.createObjectURL(blob);
  var a = document.createElement("a");
  a.href = url;
  a.download = "StudentGuideline.doc";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
});

document.getElementById('generatePdfBtn').addEventListener('click', function() {
  var validNumbers = extractValidStudentNumbers();
  if (validNumbers.length === 0) {
    alert("No valid student numbers found. Please ensure each valid student number starts with 's' followed by exactly 8 digits.");
    return;
  }
  
  var { jsPDF } = window.jspdf;
  var doc = new jsPDF();
  
  // Title
  doc.setFontSize(16);
  doc.text("Student Number Input Guideline", 105, 20, { align: "center" });
  
  // Guidelines text
  doc.setFontSize(12);
  var guidelineText = "Each student number must start with 's' followed by exactly 8 digits. No spaces or extra characters are allowed. Example: s21018803";
  doc.text(guidelineText, 10, 30);
  
  // Subtitle for input
  doc.setFontSize(14);
  doc.text("Input Student Numbers:", 10, 45);
  
  // Add valid student numbers (wrap text)
  var lines = doc.splitTextToSize(validNumbers.join("\n"), 190);
  doc.text(lines, 10, 55);
  
  // Save the PDF
  doc.save("StudentGuideline.pdf");
});
</script>
<!-- Include jsPDF from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</body>
</html>
