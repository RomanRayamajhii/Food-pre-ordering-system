<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "localhost";
$username = "root";  // default XAMPP username
$password = "";      // default XAMPP password
$database = "food_ordering";  // corrected database name

try {
    $conn = mysqli_connect($host, $username, $password, $database);
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}
?> 