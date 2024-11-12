<?php
$host = "localhost";
$username = "root"; // your database username
$password = ""; // your database password
$database = "food_ordering"; // your database name

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection failed");
}
?> 