<?php
session_start();
require_once 'config/db.php';

echo "<h2>Checkout Process Test</h2>";

// 1. Check Session
echo "<h3>1. Session Check:</h3>";
if(isset($_SESSION['user_id'])) {
    echo "✓ User logged in<br>";
} else {
    echo "✗ User not logged in<br>";
}

// 2. Check Cart
echo "<h3>2. Cart Check:</h3>";
if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "✓ Cart has items:<br>";
    foreach($_SESSION['cart'] as $item_id => $qty) {
        $stmt = $conn->prepare("SELECT name FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        echo "- {$item['name']}: {$qty} units<br>";
    }
} else {
    echo "✗ Cart is empty<br>";
}

// 3. Check Database Tables
echo "<h3>3. Database Tables Check:</h3>";
$tables = ['orders', 'order_items'];
foreach($tables as $table) {
    $result = $conn->query("DESCRIBE $table");
    if($result) {
        echo "✓ Table '$table' structure is valid<br>";
    } else {
        echo "✗ Table '$table' has issues<br>";
    }
}

// 4. Test Order Creation
echo "<h3>4. Test Order Creation:</h3>";
if(isset($_SESSION['user_id']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    try {
        $conn->begin_transaction();
        
        // Create test order
        $user_id = $_SESSION['user_id'];
        $total = 0;
        $address = "Test Address";
        
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, delivery_address) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $user_id, $total, $address);
        
        if($stmt->execute()) {
            echo "✓ Order creation works<br>";
            $conn->rollback();
        } else {
            echo "✗ Order creation failed<br>";
        }
    } catch(Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "<br>";
        $conn->rollback();
    }
} else {
    echo "✗ Cannot test order creation - missing user or cart data<br>";
}
?>