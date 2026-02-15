<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id']) || empty($_SESSION['cart'])){
    header("Location: menu.php"); exit();
}

$user_id        = $_SESSION['user_id'];
$total_amount   = $_POST['total_amount'];
$preferred_time = $_POST['preferred_time'];
$comments       = $_POST['comments'] ?? '';
$payment_method = $_POST['payment_method'];

// Use prepared statements to prevent SQL injection
// Insert order with payment_status 'pending'
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, preferred_time, comments, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("issss", $user_id, $total_amount, $preferred_time, $comments, $payment_method);
$stmt->execute();
$order_id = mysqli_insert_id($conn);
$stmt->close();

// Insert order items
$item_stmt = $conn->prepare("SELECT price FROM menu_items WHERE id = ?");
$order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");

foreach($_SESSION['cart'] as $item_id => $qty){
    // Get the price for the item
    $item_stmt->bind_param("i", $item_id);
    $item_stmt->execute();
    $result = $item_stmt->get_result();
    $item = $result->fetch_assoc();
    $price = $item['price'];

    // Insert the order item
    $order_item_stmt->bind_param("iiid", $order_id, $item_id, $qty, $price);
    $order_item_stmt->execute();
}
$item_stmt->close();
$order_item_stmt->close();

// PayPal redirect
if($payment_method=='paypal'){
    header("Location: paypal_redirect.php?order_id=$order_id&amount=$total_amount");
    exit();
}

// Cash on Delivery
$stmt = $conn->prepare("UPDATE orders SET payment_status='pending' WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->close();

unset($_SESSION['cart']);
header("Location: order_sucess.php?order_id=$order_id&amount=$total_amount");

?>
