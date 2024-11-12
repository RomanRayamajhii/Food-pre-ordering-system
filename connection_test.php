<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>System Connection Test</h2>";

// 1. Database Connection Test
echo "<h3>1. Database Connection Test:</h3>";
try {
    require_once 'config/db.php';
    echo "✓ Database connected successfully<br>";
    
    // Test query
    $test_query = "SELECT COUNT(*) as count FROM menu_items";
    $result = $conn->query($test_query);
    $row = $result->fetch_assoc();
    echo "✓ Menu Items Count: " . $row['count'] . "<br>";
} catch(Exception $e) {
    echo "✗ Database Error: " . $e->getMessage() . "<br>";
}

// 2. Session Test
echo "<h3>2. Session Test:</h3>";
if(session_status() === PHP_SESSION_ACTIVE) {
    echo "✓ Sessions are working<br>";
    echo "Session ID: " . session_id() . "<br>";
} else {
    echo "✗ Sessions are not working<br>";
}

// 3. File Permissions Test
echo "<h3>3. File Permissions Test:</h3>";
$directories = [
    'images/',
    'config/',
    'uploads/'
];

foreach($directories as $dir) {
    if(file_exists($dir)) {
        echo "✓ {$dir} exists";
        echo is_writable($dir) ? " and is writable<br>" : " but is not writable<br>";
    } else {
        echo "✗ {$dir} does not exist<br>";
    }
}

// 4. Required Tables Test
echo "<h3>4. Database Tables Test:</h3>";
$required_tables = ['users', 'menu_items', 'orders', 'order_items'];

foreach($required_tables as $table) {
    $query = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        echo "✓ Table '$table' exists<br>";
    } else {
        echo "✗ Table '$table' is missing<br>";
    }
}

// 5. Cart Session Test
echo "<h3>5. Cart Session Test:</h3>";
if(isset($_SESSION['cart'])) {
    echo "✓ Cart exists in session<br>";
    echo "Cart items: " . count($_SESSION['cart']) . "<br>";
} else {
    echo "✗ Cart is not initialized<br>";
}

// 6. User Session Test
echo "<h3>6. User Session Test:</h3>";
if(isset($_SESSION['user_id'])) {
    echo "✓ User is logged in (ID: " . $_SESSION['user_id'] . ")<br>";
} else {
    echo "✗ No user is logged in<br>";
}

?> 