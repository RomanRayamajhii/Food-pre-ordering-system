<?php
session_start();
include 'config/db.php';

$data = $_GET['data'] ?? '';

if(!$data){
    die("Invalid request");
}

// Decode the base64 data returned by eSewa v2
$decoded_data = json_decode(base64_decode($data), true);

$status = $decoded_data['status'] ?? '';
$transaction_uuid = $decoded_data['transaction_uuid'] ?? '';
$order_id = str_replace("ORDER_", "", $transaction_uuid);
$secret_key = "8gBm/:&EnhH.1/q";

if($status === 'COMPLETE'){
    // Verify Signature for security
    $message = "transaction_code={$decoded_data['transaction_code']},status={$decoded_data['status']},total_amount={$decoded_data['total_amount']},transaction_uuid={$decoded_data['transaction_uuid']},product_code={$decoded_data['product_code']},signed_field_names={$decoded_data['signed_field_names']}";
    $s = hash_hmac('sha256', $message, $secret_key, true);
    $expected_signature = base64_encode($s);

    if($expected_signature !== $decoded_data['signature']){
        die("Signature Verification Failed");
    }

    $stmt = $conn->prepare("UPDATE orders SET payment_status='completed', payment_method='esewa' WHERE id=?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
    unset($_SESSION['cart']);
} else {
    die("Payment failed or pending. Status: " . $status);
}

// Fetch order and items (same as your PayPal success page)
$order_res = mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id'");
$order = mysqli_fetch_assoc($order_res);

$items_res = mysqli_query($conn, "SELECT mi.name, oi.quantity, oi.price 
                                   FROM order_items oi
                                   JOIN menu_items mi ON oi.item_id = mi.id
                                   WHERE oi.order_id='$order_id'");
$items = [];
if($items_res) {
    while($row = mysqli_fetch_assoc($items_res)){
    $items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f7f7f7; text-align: center; padding: 50px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .success-icon { font-size: 50px; color: #28a745; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .total { font-weight: bold; font-size: 1.2em; margin-top: 20px; text-align: right; }
        .btn { display: inline-block; padding: 10px 20px; background: #28a745; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="success-icon">✔</div>
    <h1>eSewa Payment Successful!</h1>
    <p>Order ID: #<?php echo $order['id']; ?></p>

    <table>
        <tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
        <?php foreach($items as $it): ?>
        <tr>
            <td><?php echo $it['name']; ?></td>
            <td><?php echo $it['quantity']; ?></td>
            <td>Rs. <?php echo number_format($it['price'], 2); ?></td>
            <td>Rs. <?php echo number_format($it['price'] * $it['quantity'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div class="total">Total Paid: Rs. <?php echo number_format($order['total_amount'], 2); ?></div>
    
    <a href="menu.php" class="btn">Order More</a>
    <a href="order_history.php" class="btn" style="background:#007bff;">View Orders</a>
</div>
</body>
</html>