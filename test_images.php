<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}

// Disable in production
if ($_SERVER['SERVER_NAME'] !== 'localhost') {
    die('This tool is only available in development environment');
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Image System Test</h2>";

// Check paths
$doc_root = $_SERVER['DOCUMENT_ROOT'];
$project_dir = '/dummyfood2';
$upload_dir = $doc_root . $project_dir . '/uploads/menu/';

echo "<h3>System Information:</h3>";
echo "Document Root: " . $doc_root . "<br>";
echo "Project Directory: " . $project_dir . "<br>";
echo "Upload Directory: " . $upload_dir . "<br>";
echo "Current Directory: " . getcwd() . "<br>";

// Check upload directory
echo "<h3>Upload Directory Status:</h3>";
if (file_exists($upload_dir)) {
    echo "Directory exists<br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "<br>";
    echo "Is readable: " . (is_readable($upload_dir) ? "Yes" : "No") . "<br>";
    echo "Is writable: " . (is_writable($upload_dir) ? "Yes" : "No") . "<br>";
    
    // List files
    echo "<h3>Files in Directory:</h3>";
    $files = scandir($upload_dir);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo "File: " . $file . "<br>";
            echo "Size: " . filesize($upload_dir . $file) . " bytes<br>";
            echo "Web Path: " . $project_dir . '/uploads/menu/' . $file . "<br>";
            echo "<img src='" . $project_dir . '/uploads/menu/' . $file . "' style='max-width: 200px;'><br><br>";
        }
    }
} else {
    echo "Directory does not exist!<br>";
}

// Check database records
require_once('config/db.php');
echo "<h3>Database Records:</h3>";
$query = "SELECT id, name, image FROM menu_items WHERE image IS NOT NULL";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['id'] . "<br>";
    echo "Name: " . $row['name'] . "<br>";
    echo "Image: " . $row['image'] . "<br>";
    echo "File exists: " . (file_exists($upload_dir . $row['image']) ? "Yes" : "No") . "<br><br>";
}
?> 