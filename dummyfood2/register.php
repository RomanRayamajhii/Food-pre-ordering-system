<?php
session_start();
include 'config/db.php';

// Set header to return JSON response
header('Content-Type: application/json');

// Function to return JSON response
function sendResponse($success, $message, $redirect = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($redirect) {
        $response['redirect'] = $redirect;
    }
    echo json_encode($response);
    exit;
}

// Check if already logged in
if(isset($_SESSION['user_id'])) {
    sendResponse(false, "You are already logged in", "index.php");
}

// Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    sendResponse(false, "Invalid request method");
}

// Validate input
$required_fields = ['username', 'email', 'full_name', 'phone', 'address', 'password', 'confirm_password'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        sendResponse(false, "Please fill in all required fields");
    }
}

try {
    // Sanitize inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, "Please enter a valid email address");
    }

    // Validate phone (10 digits)
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        sendResponse(false, "Please enter a valid 10-digit phone number");
    }

    // Validate password length
    if (strlen($password) < 6) {
        sendResponse(false, "Password must be at least 6 characters long");
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        sendResponse(false, "Passwords do not match");
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        sendResponse(false, "Username already exists");
    }
    $stmt->close();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        sendResponse(false, "Email already registered");
    }
    $stmt->close();

    // Check if phone already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        sendResponse(false, "Phone number already registered");
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, full_name, phone, address, password, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $username, $email, $full_name, $phone, $address, $hashed_password);
    
    if (!$stmt->execute()) {
        throw new Exception("Error creating account: " . $stmt->error);
    }

    $stmt->close();

    // Send success response
    sendResponse(true, "Registration successful! Please login.", "login.html");

} catch (Exception $e) {
    // Log the error (in a production environment)
    error_log("Registration error: " . $e->getMessage());
    
    // Send generic error message to user
    sendResponse(false, "An error occurred during registration. Please try again later.");
}

// Close database connection
$conn->close();
?>

