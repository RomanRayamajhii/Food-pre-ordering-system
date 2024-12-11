<?php
session_start();
include 'config/db.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

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
    if (isset($_SESSION['user_id'])) {
        sendResponse(true, "Already logged in", "index.php");
    }

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        sendResponse(false, "Invalid request method");
    }

    if (empty($_POST['login_id']) || empty($_POST['password'])) {
        sendResponse(false, "Please provide both email/phone and password");
    }

    $login_id = trim($_POST['login_id']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, email, phone, password, full_name FROM users WHERE email = ? OR phone = ?");
    if (!$stmt) {
        sendResponse(false, "Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("ss", $login_id, $login_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendResponse(false, "Account not found! Please check your email or phone number.");
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        sendResponse(false, "Invalid password!");
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];

    // Close the main statement and connection
    $stmt->close();
    $conn->close();

    sendResponse(true, "Login successful! Redirecting...", "index.php");

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    sendResponse(false, "An error occurred during login: " . $e->getMessage());
}
?>
