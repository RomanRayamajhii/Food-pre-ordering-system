<?php
session_start();
require_once('../includes/db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

echo "<div class='debug-info'>";
echo "<h5 class='mb-3'><i class='fas fa-bug'></i> Debug Information</h5>";

// Server Information
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "<strong>Upload Directory:</strong> " . $_SERVER['DOCUMENT_ROOT'] . '/dummyfood2/uploads/menu/' . "<br><br>";

// Check upload directory
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/dummyfood2/uploads/menu/';
if (is_dir($upload_dir)) {
    echo "<strong>Files in upload directory:</strong><br>";
    $files = scandir($upload_dir);
    echo "<ul class='list-unstyled ml-3'>";
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo "<li>📄 " . $file . " (size: " . filesize($upload_dir . $file) . " bytes)</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<div class='text-danger'>⚠️ Upload directory does not exist!</div>";
}

// Database Records
echo "<strong>Menu Items with Images:</strong><br>";
$query = "SELECT id, name, image FROM menu_items WHERE image IS NOT NULL";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    echo "<ul class='list-unstyled ml-3'>";
    while ($row = mysqli_fetch_assoc($result)) {
        $file_exists = file_exists($upload_dir . $row['image']);
        $status_icon = $file_exists ? "✅" : "❌";
        echo "<li>{$status_icon} ID: {$row['id']} - {$row['name']} (Image: {$row['image']})</li>";
    }
    echo "</ul>";
} else {
    echo "<div class='text-muted ml-3'>No menu items with images found</div>";
}

echo "</div>";

// Add some styling
?>
<style>
.debug-info {
    font-family: monospace;
    line-height: 1.6;
}
.debug-info ul {
    margin-bottom: 15px;
}
.debug-info li {
    padding: 2px 0;
}
</style> 