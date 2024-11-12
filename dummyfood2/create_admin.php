<?php
require_once 'config/db.php';

$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT); // Change 'admin123' to your desired password
$email = "admin@example.com";
$is_admin = 1;

try {
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, is_admin) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $password, $email, $is_admin);
    
    if ($stmt->execute()) {
        echo "Admin user created successfully!";
    } else {
        echo "Error creating admin user: " . $conn->error;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 