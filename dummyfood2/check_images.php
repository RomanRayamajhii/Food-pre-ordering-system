<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Image System Check</h2>";

// Define paths
$doc_root = $_SERVER['DOCUMENT_ROOT'];
$project_path = '/dummyfood2';
$upload_dir = $doc_root . $project_path . '/uploads/menu/';
$web_path = $project_path . '/uploads/menu/';

echo "<h3>System Paths:</h3>";
echo "Document Root: " . $doc_root . "<br>";
echo "Project Path: " . $project_path . "<br>";
echo "Upload Directory: " . $upload_dir . "<br>";
echo "Web Path: " . $web_path . "<br>";

// Check directories
echo "<h3>Directory Check:</h3>";
echo "Upload directory exists: " . (file_exists($upload_dir) ? "Yes" : "No") . "<br>";
echo "Upload directory is readable: " . (is_readable($upload_dir) ? "Yes" : "No") . "<br>";
echo "Upload directory permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "<br>";

// List files in upload directory
echo "<h3>Files in Upload Directory:</h3>";
if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $file_path = $upload_dir . $file;
            echo "<li>";
            echo "File: " . $file . "<br>";
            echo "Size: " . filesize($file_path) . " bytes<br>";
            echo "Permissions: " . substr(sprintf('%o', fileperms($file_path)), -4) . "<br>";
            echo "Web URL: " . $web_path . $file . "<br>";
            // Try to display the image
            echo "<img src='" . $web_path . $file . "' style='max-width: 200px; margin: 10px 0;'><br>";
            echo "</li>";
        }
    }
    echo "</ul>";
}

// Check database records
echo "<h3>Database Records:</h3>";
require_once('config/db.php');

$sql = "SELECT id, name, image FROM menu_items WHERE image IS NOT NULL";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>";
        echo "ID: " . $row['id'] . "<br>";
        echo "Name: " . $row['name'] . "<br>";
        echo "Image: " . $row['image'] . "<br>";
        $file_exists = file_exists($upload_dir . $row['image']);
        echo "File exists: " . ($file_exists ? "Yes" : "No") . "<br>";
        if ($file_exists) {
            echo "<img src='" . $web_path . $row['image'] . "' style='max-width: 200px; margin: 10px 0;'><br>";
        }
        echo "</li>";
    }
    echo "</ul>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    line-height: 1.6;
}
h2, h3 {
    color: #333;
}
ul {
    list-style: none;
    padding: 0;
}
li {
    margin-bottom: 20px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}
</style> 