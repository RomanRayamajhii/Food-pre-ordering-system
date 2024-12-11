<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Print POST data
    echo "POST data received:<br>";
    print_r($_POST);
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    echo "<br>Username: " . $username . "<br>";
    echo "Password: " . $password . "<br>";
    
    if ($username === "admin" && $password === "admin@123") {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or password";
        header("Location: index.php?error=1");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>