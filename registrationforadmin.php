<?php 
session_start();
include 'connection.php'; // Include database connection

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = htmlspecialchars(trim($_POST['email']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $cpassword = htmlspecialchars(trim($_POST['cpassword']));
    $type = "admin";

    if ($password !== $cpassword) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check for duplicate Full Name, Email, or Username
    $query = "SELECT fullname, email, username FROM bcp_sms3_admin WHERE fullname = ? OR email = ? OR username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $fullname, $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row['fullname'] === $fullname) {
            $errors[] = "Full Name is already taken.";
        }
        if ($row['email'] === $email) {
            $errors[] = "Email is already registered.";
        }
        if ($row['username'] === $username) {
            $errors[] = "Username is already taken.";
        }
    }

    $stmt->close(); // Close after checking duplicates

    // If no errors, insert the user into the database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO bcp_sms3_admin (user_type, fullname, email, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $type, $fullname, $email, $username, $hashed_password);

        
        if ($stmt->execute()) {
            session_destroy();
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Error in registration!";
        }
        $stmt->close();
    }

    $conn->close();

    // Return errors if any
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'message' => implode("<br>", $errors)]);
    }
}
?>