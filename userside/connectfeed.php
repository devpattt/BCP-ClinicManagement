<?php


include '../connection.php';


if (!isset($_SESSION['username'])) {
    die("User is not logged in.");
}

$username = $_SESSION['username']; 

// Fetch fullname from the users table
$sql = "SELECT fullname FROM bcp_sms3_users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fullname = $row['fullname'];
} else {
    $fullname = "Unknown"; 
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $feedback = htmlspecialchars(trim($_POST['feedback']));

    $stmt = $conn->prepare("INSERT INTO bcp_sms3_feedback (user, feedback) VALUES (?, ?)");
    $stmt->bind_param("ss", $fullname, $feedback);

    if ($stmt->execute()) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('successMessage').style.display = 'block';
            });
        </script>";
    } else {
        echo "Error inserting record: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}

?>