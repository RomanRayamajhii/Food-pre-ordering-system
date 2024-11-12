<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Upload Directory Test</h2>";

// Define paths
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/dummyfood2';
$upload_path = $base_path . '/uploads/menu';

// Check directory structure
echo "<h3>Directory Structure:</h3>";
echo "Base Path: " . $base_path . "<br>";
echo "Upload Path: " . $upload_path . "<br>";
echo "Base Path Exists: " . (file_exists($base_path) ? "Yes" : "No") . "<br>";
echo "Upload Path Exists: " . (file_exists($upload_path) ? "Yes" : "No") . "<br>";

// Create directories if they don't exist
if (!file_exists($upload_path)) {
    echo "<br>Creating directories...<br>";
    if (mkdir($upload_path, 0777, true)) {
        echo "Successfully created upload directory<br>";
    } else {
        echo "Failed to create upload directory<br>";
    }
}

// List all files in upload directory
echo "<h3>Files in Upload Directory:</h3>";
if (is_dir($upload_path)) {
    $files = scandir($upload_path);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo "File: " . $file . "<br>";
            echo "Size: " . filesize($upload_path . '/' . $file) . " bytes<br>";
            echo "Permissions: " . substr(sprintf('%o', fileperms($upload_path . '/' . $file)), -4) . "<br>";
            echo "<hr>";
        }
    }
} else {
    echo "Upload directory not found or not accessible<br>";
}

// Check database records
require_once('config/db.php');
echo "<h3>Database Records:</h3>";
$sql = "SELECT id, name, image FROM menu_items WHERE image IS NOT NULL";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['id'] . "<br>";
    echo "Name: " . $row['name'] . "<br>";
    echo "Image: " . $row['image'] . "<br>";
    echo "File exists: " . (file_exists($upload_path . '/' . $row['image']) ? "Yes" : "No") . "<br>";
    echo "<hr>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #333; }
code { background: #f8f9fa; padding: 2px 5px; border-radius: 3px; }
</style> 