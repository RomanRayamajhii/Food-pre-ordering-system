<?php
session_start();
include 'config/db.php';

// Set headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Function to send JSON response
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

try {
    // Check if already logged in
    if (isset($_SESSION['user_id'])) {
        sendResponse(true, "Already logged in", "index.php");
    }

    // Validate request method
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        sendResponse(false, "Invalid request method");
    }

    // Validate input
    if (empty($_POST['login_id']) || empty($_POST['password'])) {
        sendResponse(false, "Please provide both email/phone and password");
    }

    // Sanitize inputs
    $login_id = trim($_POST['login_id']);
    $password = $_POST['password'];

    // Prepare SQL statement to check email or phone
    $stmt = $conn->prepare("SELECT id, email, phone, password, full_name FROM users WHERE email = ? OR phone = ?");
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $stmt->bind_param("ss", $login_id, $login_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 0) {
        sendResponse(false, "Account not found! Please check your email or phone number.");
    }

    $user = $result->fetch_assoc();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        sendResponse(false, "Invalid password!");
    }

    // Set session variables on successful login
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];

    // Update last login timestamp (optional)
    $update_stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
    if ($update_stmt) {
        $update_stmt->bind_param("i", $user['id']);
        $update_stmt->execute();
        $update_stmt->close();
    }

    // Close statements and database connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($update_stmt)) {
        $update_stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }

    // Send success response with redirection
    sendResponse(true, "Login successful! Redirecting...", "index.php");

} catch (Exception $e) {
    // Add connection closing here too
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($update_stmt)) {
        $update_stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
    
    error_log("Login error: " . $e->getMessage()); // Log the error
    sendResponse(false, "An error occurred during login. Please try again later.");
}
?>
