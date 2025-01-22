<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false];

if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = $_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0 && $quantity <= 15) {
        $_SESSION['cart'][$item_id] = $quantity;
        $response['success'] = true;
    } else {
        // Provide feedback if the quantity is invalid
        $response['message'] = 'Quantity must be between 1 and 10.';
        unset($_SESSION['cart'][$item_id]);
        $response['success'] = true; // Still consider it a success for removal
    }
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>
