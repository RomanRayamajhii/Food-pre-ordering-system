<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $quantity = min(15, max(1, (int)$_POST['quantity']));

    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Update quantity if item already exists
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] += $quantity; // Increment existing quantity
    } else {
        $_SESSION['cart'][$item_id] = $quantity; // Set new quantity
    }

    echo json_encode(['success' => true]);
    exit();
}

echo json_encode(['success' => false]);
?> 