<?php
// Disable error display for production (errors will be logged instead)
ini_set('display_errors', 0);
error_reporting(0);

session_start();
include '../connection.php';

header('Content-Type: application/json');

// Decode the JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Check required fields: uid and updates are always expected.
if (!$data || !isset($data['uid']) || !isset($data['updates'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit();
}

$uid = $conn->real_escape_string($data['uid']);
$updates = $data['updates'];

// Pull diagnostic & recommendation from request (default to 'N/A' if not provided)
$diagnostic = isset($data['diagnostic']) ? $conn->real_escape_string($data['diagnostic']) : 'N/A';
$recommendation = isset($data['recommendation']) ? $conn->real_escape_string($data['recommendation']) : 'N/A';

// If an action is provided, escape and store it.
$action = isset($data['action']) ? $conn->real_escape_string($data['action']) : null;

$allSuccess = true;
$errorMessages = [];

// Array to hold new med usage for patient record
$patientMeds = [];

foreach ($updates as &$update) {
    if (!isset($update['med']) || !isset($update['oldQty']) || !isset($update['newQty'])) {
        $allSuccess = false;
        $errorMessages[] = "Missing med, oldQty or newQty in updates.";
        continue;
    }
    $med = $conn->real_escape_string($update['med']);
    $oldQty = (int)$update['oldQty'];
    $newQty = (int)$update['newQty'];

    // Calculate change: positive if patient is using more med, negative if less.
    $change = $newQty - $oldQty;

    // Retrieve current unit and quantity for the med from inventory
    $sqlSelect = "SELECT unit, quantity FROM bcp_sms3_medicalsupplies WHERE item_name = '$med'";
    $result = $conn->query($sqlSelect);
    if (!$result || $result->num_rows === 0) {
        $allSuccess = false;
        $errorMessages[] = "Medical supply $med not found.";
        continue;
    }
    $row = $result->fetch_assoc();
    $currentUnit = (int)$row['unit'];
    $currentQuantity = (int)$row['quantity'];

    // Convert current inventory into an effective total (each unit is 10)
    $currentEffectiveTotal = ($currentUnit * 10) + $currentQuantity;

    // Update inventory: if patient uses more, subtract that from inventory; if less, add it back.
    $newInventoryTotal = $currentEffectiveTotal - $change;

    // Deny update if inventory would drop below zero
    if ($newInventoryTotal < 0) {
        echo json_encode([
            "success" => false,
            "error" => "Update denied for med '$med': insufficient inventory. Attempting to use more than available."
        ]);
        exit();
    }

    // Convert new inventory total back to unit and quantity
    $newUnit = floor($newInventoryTotal / 10);
    $newQuantity = $newInventoryTotal % 10;

    $sqlUpdate = "UPDATE bcp_sms3_medicalsupplies
                  SET unit = $newUnit, quantity = $newQuantity
                  WHERE item_name = '$med'";
    if (!$conn->query($sqlUpdate)) {
        $allSuccess = false;
        $errorMessages[] = "Error updating inventory for $med: " . $conn->error;
    }

    // Save the new med usage for the patient record
    $patientMeds[$med] = $newQty;
}
unset($update); // break reference

// Build the med string for the patient record (only include meds with usage greater than 0)
$combinedMeds = [];
foreach ($patientMeds as $med => $usage) {
    if ($usage > 0) {
        $medEscaped = $conn->real_escape_string($med);
        $combinedMeds[] = "$medEscaped = $usage";
    }
}
$newMedsString = implode(", ", $combinedMeds);
if (!$newMedsString) {
    $newMedsString = "N/A";
}

// Build SET clause for patient record update.
// If $action is provided, include it in the update.
$setClause = "diagnostic = '$diagnostic', recommendation = '$recommendation', meds = '$newMedsString'";
if ($action !== null) {
    $setClause .= ", action = '$action'";
}

// Update the patient record with the new diagnostic, recommendation, meds usage (and action if provided)
$sqlPatient = "UPDATE bcp_sms3_other_patients SET $setClause WHERE unique_id = '$uid'";
$conn->query($sqlPatient);

// If no patient record was updated, insert a new one (include action if provided)
if ($conn->affected_rows === 0) {
    $columns = "unique_id, diagnostic, recommendation, meds";
    $values = "'$uid', '$diagnostic', '$recommendation', '$newMedsString'";
    if ($action !== null) {
        $columns .= ", action";
        $values .= ", '$action'";
    }
    $sqlInsert = "INSERT INTO bcp_sms3_other_patients ($columns) VALUES ($values)";
    if (!$conn->query($sqlInsert)) {
        $allSuccess = false;
        $errorMessages[] = "Error inserting patient record: " . $conn->error;
    }
}

if ($allSuccess) {
    echo json_encode([
        "success" => true,
        "newMeds" => $newMedsString
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => implode(" ", $errorMessages)
    ]);
}
?>
