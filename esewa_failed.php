<?php
session_start();
include 'config/db.php';

$order_id = $_GET['order_id'] ?? null;
$user_id  = $_SESSION['user_id'] ?? null;

if($order_id && $user_id){

    // ✅ Check if order belongs to logged-in user
    $check = mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id'");
    
    if(mysqli_num_rows($check) > 0){

        // Restore cart
        $items_res = mysqli_query($conn, "SELECT item_id, quantity FROM order_items WHERE order_id='$order_id'");
        
        $_SESSION['cart'] = [];

        while($row = mysqli_fetch_assoc($items_res)){
            $_SESSION['cart'][$row['item_id']] = $row['quantity'];
        }

        // Delete only THIS order
        mysqli_query($conn, "DELETE FROM order_items WHERE order_id='$order_id'");
        mysqli_query($conn, "DELETE FROM orders WHERE id='$order_id'");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f7f7f7; text-align: center; padding: 50px; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .error-icon { font-size: 50px; color: #dc3545; margin-bottom: 20px; }
        h1 { color: #333; }
        p { color: #666; line-height: 1.6; }
        .btn { 
            display: inline-block; 
            padding: 10px 20px; 
            background: #007bff; 
            color: #fff; 
            text-decoration: none; 
            border-radius: 5px; 
            margin-top: 20px; 
        }
        .btn-retry { background: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">✘</div>
        <h1>Payment Failed</h1>
        <p>Your eSewa transaction could not be completed. Don't worry, your items have been restored to your cart.</p>
        
        <a href="checkout.php" class="btn btn-retry">Try Again</a>
        <a href="menu.php" class="btn">Back to Menu</a>
    </div>
</body>
</html>
