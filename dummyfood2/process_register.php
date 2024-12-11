<?php
session_start();
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match";
        header("Location: register.php");
        exit();
    }
    
    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Email already registered";
        header("Location: register.php");
        exit();
    }
    
    // Hash password and insert user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Registration failed";
        header("Location: register.php");
    }
    exit();
}
?> 