<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false];

if(isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    
    // Remove item from cart
    if(isset($_SESSION['cart'][$item_id])) {
        unset($_SESSION['cart'][$item_id]);
        
        // Calculate new total
        $new_total = 0;
        if(!empty($_SESSION['cart'])) {
            include 'config/db.php';
            
            foreach($_SESSION['cart'] as $id => $quantity) {
                $sql = "SELECT price FROM menu_items WHERE id = $id";
                $result = $conn->query($sql);
                $item = $result->fetch_assoc();
                $new_total += $item['price'] * $quantity;
            }
        }
        
        $response = [
            'success' => true,
            'new_total' => $new_total,
            'cart_empty' => empty($_SESSION['cart'])
        ];
    }
}

echo json_encode($response);
?> 