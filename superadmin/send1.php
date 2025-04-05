<?php
session_start();
require '../connection.php';
include '../fetchfname.php';

if (!isset($_SESSION['username'])) {
    header("Location: superadmin/mainpage.php");
    exit();
}

if (!isset($_GET['uid'])) {
    die("Invalid request.");
}

$unique_id = $_GET['uid'];

// Use custom filename if provided via GET, otherwise default.
if (isset($_GET['filename']) && trim($_GET['filename']) != "") {
    // Use basename to avoid directory traversal and trim spaces.
    $filename = basename(trim($_GET['filename']));
    // Append .pdf if not provided.
    if (strtolower(substr($filename, -4)) !== ".pdf") {
        $filename .= ".pdf";
    }
    $pdf_filename = $filename;
} else {
    $pdf_filename = "Patient_Report_{$unique_id}.pdf";
}

// Define the folder where the PDF will be saved
$pdf_folder = "../uploads/";
$pdf_filepath = $pdf_folder . $pdf_filename;
$pdf_url = "uploads/" . $pdf_filename; 

// Fetch patient data
$stmt = $conn->prepare("SELECT patient, fullname, student_number, contact_number, sex, birthday, year_level, department_code, diagnostic, recommendation, meds, created_at FROM bcp_sms3_patients WHERE unique_id = ?");
$stmt->bind_param("s", $unique_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No data found.");
}

$row = $result->fetch_assoc();
$stmt->close();

// Check if the uploads folder exists and is writable
if (!is_dir($pdf_folder)) {
    die("Uploads folder does not exist.");
}
if (!is_writable($pdf_folder)) {
    die("Uploads folder is not writable by PHP.");
}

// ------------------
// PDF GENERATION CODE
// ------------------

// Escape helper function for PDF content
function pdfEscape($text) {
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
}

// -----------------------------------------------------------------------------
// 1) READ LOGO IMAGE DATA
// -----------------------------------------------------------------------------
$imagePath = "../assets/img/bcp logo.png";
$imageData = file_get_contents($imagePath);
list($imgWidth, $imgHeight, $imgType) = getimagesize($imagePath);

// -----------------------------------------------------------------------------
// 2) HEADER CONTENT (LOGO + HEADER TEXT)
// -----------------------------------------------------------------------------
$headerContent = "";
$headerContent .= "q\n";                   // Save graphics state
$headerContent .= "20 0 0 24 30 805 cm\n";   // Scale & translate: width=20, height=24 at (30,805)
$headerContent .= "/Im1 Do\n";               // Paint the image
$headerContent .= "Q\n";                     // Restore graphics state

// Header Text 1 (Bold, 22pt)
$headerContent .= "BT\n";
$headerContent .= "/F2 22 Tf\n";
$headerContent .= "1 0 0 1 130 775 Tm\n";  
$headerContent .= "(Bestlink College of the Philippines) Tj\n";
$headerContent .= "ET\n";

// Header Text 2 (Bold, 20pt)
$headerContent .= "BT\n";
$headerContent .= "/F2 20 Tf\n";
$headerContent .= "1 0 0 1 165 750 Tm\n";  
$headerContent .= "(College of Computer Studies) Tj\n";
$headerContent .= "ET\n";

// Real-Time Date (Upper Right)
$currentDate = date("F d, Y H:i:s");
$headerContent .= "BT\n";
$headerContent .= "/F2 12 Tf\n";
$headerContent .= "1 0 0 1 450 815 Tm\n";
$headerContent .= "(" . pdfEscape($currentDate) . ") Tj\n";
$headerContent .= "ET\n";

// -----------------------------------------------------------------------------
// 3) MAIN CONTENT (PATIENT DATA)
// -----------------------------------------------------------------------------
$startY = 630;
$lineHeight = 20;
$curY = $startY;

$mainContent = "";

// Title: Patient Report
$mainContent .= sprintf("BT\n1 0 0 1 50 %d Tm\n", $curY);
$mainContent .= "/F2 19 Tf\n";
$mainContent .= "(Patient Report) Tj\nET\n";
$curY -= $lineHeight;

// Separator
$mainContent .= sprintf("BT\n1 0 0 1 50 %d Tm\n", $curY);
$mainContent .= "/F2 12 Tf\n";
$mainContent .= "(----------------------------) Tj\nET\n";
$curY -= $lineHeight;

// Data Lines
$dataLines = [
    "Patient: "           => $row['patient'],
    "Name: "              => $row['fullname'],
    "Student Number: "    => $row['student_number'],
    "Contact: "           => $row['contact_number'],
    "Sex: "               => $row['sex'],
    "Birth date: "        => $row['birthday'],
    "Year Level: "        => $row['year_level'],
    "Department Code: "   => $row['department_code'],
    "Diagnostic: "        => $row['diagnostic'],
    "Recommendation: "    => $row['recommendation'],
    "Medications Given: " => $row['meds']
];

foreach ($dataLines as $label => $value) {
    $mainContent .= sprintf("BT\n1 0 0 1 50 %d Tm\n", $curY);
    $mainContent .= "/F2 12 Tf\n";
    $mainContent .= "(" . pdfEscape($label) . ") Tj\n";
    $mainContent .= "/F1 12 Tf\n";
    $mainContent .= "(" . pdfEscape($value) . ") Tj\nET\n";
    $curY -= $lineHeight;
}

// Footer Lines (bottom of page)
$sentByY = 50;
$mainContent .= sprintf("BT\n1 0 0 1 50 %d Tm\n", $sentByY);
$mainContent .= "/F2 12 Tf\n";
$mainContent .= "(Generated By: ) Tj\n";
$mainContent .= "/F1 12 Tf\n";
$mainContent .= "(" . $fullname . ") Tj\nET\n";

// Reference ID on bottom right
$refX = 400;
$mainContent .= sprintf("BT\n1 0 0 1 %d %d Tm\n", $refX, $sentByY);
$mainContent .= "/F2 12 Tf\n";
$mainContent .= "(Reference ID: ) Tj\n";
$mainContent .= "/F1 12 Tf\n";
$mainContent .= "(" . pdfEscape($unique_id) . ") Tj\nET\n";

// Combine header and main content
$pdf_text_content = $headerContent . $mainContent;

// -----------------------------------------------------------------------------
// 4) BUILD PDF OBJECTS
// -----------------------------------------------------------------------------
$contentLength = mb_strlen($pdf_text_content, '8bit');
$objects = [];

// Object 1: Catalog
$objects[] = "1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
";

// Object 2: Pages
$objects[] = "2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
";

// Object 3: Page (include fonts and image XObject)
$objects[] = "3 0 obj
<< /Type /Page
   /Parent 2 0 R
   /MediaBox [0 0 595 842]
   /Contents 4 0 R
   /Resources <<
       /Font << /F1 5 0 R /F2 6 0 R >>
       /XObject << /Im1 7 0 R >>
   >>
>>
endobj
";

// Object 4: Content Stream
$objects[] = "4 0 obj
<< /Length " . $contentLength . " >>
stream
" . $pdf_text_content . "
endstream
endobj
";

// Object 5: Normal Font (Helvetica)
$objects[] = "5 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
endobj
";

// Object 6: Bold Font (Helvetica-Bold)
$objects[] = "6 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>
endobj
";

// Object 7: Image XObject (logo)
$objects[] = "7 0 obj
<< /Type /XObject
   /Subtype /Image
   /Width $imgWidth
   /Height $imgHeight
   /ColorSpace /DeviceRGB
   /BitsPerComponent 8
   /Filter /FlateDecode
   /Length " . strlen($imageData) . " >>
stream
" . $imageData . "
endstream
endobj
";

// -----------------------------------------------------------------------------
// 5) ASSEMBLE THE PDF
// -----------------------------------------------------------------------------
$pdf = "%PDF-1.4\n";
$offsets = [];
foreach ($objects as $obj) {
    $offsets[] = mb_strlen($pdf, '8bit');
    $pdf .= $obj;
}

// Build the cross-reference table
$objectCount = count($objects) + 1;
$xref = "xref\n0 $objectCount\n";
$xref .= "0000000000 65535 f \n";
foreach ($offsets as $offset) {
    $xref .= sprintf("%010d 00000 n \n", $offset);
}

// Build the trailer
$trailer = "trailer
<< /Size $objectCount /Root 1 0 R >>
";
$startxref = mb_strlen($pdf, '8bit'); // offset where xref starts

$pdf .= $xref . $trailer . "startxref\n" . $startxref . "\n%%EOF";

// Write the PDF to file
if (file_put_contents($pdf_filepath, $pdf) === false) {
    die("Failed to write PDF file.");
}

// Optionally log the PDF generation in the database
$stmt = $conn->prepare("INSERT INTO bcp_sms3_send_integ (unique_id, request, date) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $unique_id, $pdf_url, $row['created_at']);
$stmt->execute();
$stmt->close();

// Serve the PDF inline to the user
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"$pdf_filename\"");
echo $pdf;
exit();
?>
