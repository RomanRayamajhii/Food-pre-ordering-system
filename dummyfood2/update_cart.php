<?php
session_start();

if(isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = $_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    
    if($quantity > 0 && $quantity <= 10) {
        $_SESSION['cart'][$item_id] = $quantity;
    }
}
?> 