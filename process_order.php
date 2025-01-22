<?php
session_start();
include 'config/db.php';

// Check if user is logged in and cart exists
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: menu.php");
    exit();
}

// Get form data
$user_id = $_SESSION['user_id'];
$total_amount = $_POST['total_amount'];
$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$delivery_time = $_POST['delivery_time'];
$comments = isset($_POST['comments']) ? $_POST['comments'] : '';

// Simple insert query for orders
$sql = "INSERT INTO orders (user_id, total_amount, full_name, phone, address, delivery_time, comments) 
        VALUES ('$user_id', '$total_amount', '$full_name', '$phone', '$address', '$delivery_time', '$comments')";

if (mysqli_query($conn, $sql)) {
    $order_id = mysqli_insert_id($conn);
    
    // Insert order items
    foreach ($_SESSION['cart'] as $item_id => $quantity) {
        // Get item price
        $price_query = "SELECT price FROM menu_items WHERE id = '$item_id'";
        $price_result = mysqli_query($conn, $price_query);
        $item = mysqli_fetch_assoc($price_result);
        $price = $item['price'];
        
        // Insert order item
        $item_sql = "INSERT INTO order_items (order_id, item_id, quantity, price) 
                     VALUES ('$order_id', '$item_id', '$quantity', '$price')";
        mysqli_query($conn, $item_sql);
    }
    
    // Clear the cart
    unset($_SESSION['cart']);
    
    // JavaScript alert and redirect
    echo "<script>
        alert('Order placed successfully!');
        window.location.href = 'order_history.php';
    </script>";
    exit();
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>