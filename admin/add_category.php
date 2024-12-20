<?php
session_start();
include 'includes/header.php';
include 'includes/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim the inputs to remove leading/trailing spaces
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = isset($_POST['status']) ? 1 : 0; // Default to active if checked

    // Input validation
    if (empty($name) || empty($description)) {
        $_SESSION['error_message'] = "Both name and description are required!";
        header('Location: manage_categories.php');
        exit();
    }

    if (strlen($name) < 3 || strlen($name) > 100) {
        $_SESSION['error_message'] = "Category name must be between 3 and 100 characters!";
        header('Location: manage_categories.php');
        exit();
    }

    // Prepare the SQL statement without bind_param
    $sql = "INSERT INTO categories (name, description, status) VALUES ('$name', '$description', $status)";

    // Execute the statement
    if ($conn->query($sql)) {
        $_SESSION['success_message'] = "Category added successfully!";
    } else {
        $_SESSION['error_message'] = "Error adding category: " . $conn->error;
    }

    // Redirect to manage categories page
    header('Location: manage_categories.php');
    exit();
}
?>
