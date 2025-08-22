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

// Insert order with payment_status 'pending'
$sql = "INSERT INTO orders (user_id,total_amount,preferred_time,comments,payment_method,payment_status)
        VALUES ('$user_id','$total_amount','$preferred_time','$comments','$payment_method','pending')";
mysqli_query($conn,$sql);
$order_id = mysqli_insert_id($conn);

// Insert order items
foreach($_SESSION['cart'] as $item_id => $qty){
    $item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT price FROM menu_items WHERE id='$item_id'"));
    mysqli_query($conn,"INSERT INTO order_items (order_id,item_id,quantity,price)
                        VALUES ('$order_id','$item_id','$qty','".$item['price']."')");
}

// PayPal redirect
if($payment_method=='paypal'){
    header("Location: paypal_redirect.php?order_id=$order_id&amount=$total_amount");
    exit();
}

// Cash on Delivery
mysqli_query($conn,"UPDATE orders SET payment_status='pending' WHERE id='$order_id'");
unset($_SESSION['cart']);
header("Location: order_sucess.php?order_id=$order_id&amount=$total_amount");

?>
