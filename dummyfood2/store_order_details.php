<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['temp_order'] = [
        'order_id' => $_POST['order_id'],
        'amount' => $_POST['amount'],
        'payment_type' => $_POST['payment_type'],
        'cart' => $_SESSION['cart'],
        'user_id' => $_SESSION['user_id'],
        'address' => $_POST['address'],
        'phone' => $_POST['phone'],
        'comments' => $_POST['comments'] ?? ''
    ];
}
?> 