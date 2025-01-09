<?php
session_start();
include 'config/db.php';

header('Content-Type: application/json');

function sendResponse($success, $message, $redirect = null) {
    echo json_encode(["success" => $success, "message" => $message, "redirect" => $redirect]);
    exit;
}

if (isset($_SESSION['user_id'])) {
    sendResponse(false, "You are already logged in", "index.php");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, "Invalid request method");
}

foreach (['username', 'email', 'full_name', 'phone', 'address', 'password', 'confirm_password'] as $field) {
    if (empty(trim($_POST[$field] ?? ''))) {
        sendResponse(false, "Please fill in all required fields");
    }
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$full_name = trim($_POST['full_name']);
$phone = trim($_POST['phone']);
$address = trim($_POST['address']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(false, "Invalid email address");
}
if (!preg_match("/^[0-9]{10}$/", $phone)) {
    sendResponse(false, "Invalid phone number");
}
if (strlen($password) < 6) {
    sendResponse(false, "Password must be at least 6 characters");
}
if ($password !== $confirm_password) {
    sendResponse(false, "Passwords do not match");
}

try {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "SELECT id FROM users WHERE username = '$username' OR email = '$email' OR phone = '$phone'";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        sendResponse(false, "Username, email, or phone number already exists");
    }

    $query = "INSERT INTO users (username, email, full_name, phone, address, password, created_at) VALUES ('$username', '$email', '$full_name', '$phone', '$address', '$hashed_password', CURRENT_TIMESTAMP)";
    if (!$conn->query($query)) {
        throw new Exception("Database error: " . $conn->error);
    }

    sendResponse(true, "Registration successful! Please login.", "login.html");
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    sendResponse(false, "An error occurred. Please try again later.");
}

$conn->close();
?>
