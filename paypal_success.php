<?php
session_start();
include 'config/db.php';

$order_id = $_GET['order_id'] ?? null;

if($order_id){
    // Update order as completed
    mysqli_query($conn,"UPDATE orders SET payment_status='completed' WHERE id='$order_id'");

    // Fetch order details
    $order_sql = "SELECT * FROM orders WHERE id='$order_id'";
    $order_res = mysqli_query($conn, $order_sql);
    $order = mysqli_fetch_assoc($order_res);

    // Fetch order items
    $items_sql = "SELECT mi.name, oi.quantity, oi.price 
                  FROM order_items oi
                  JOIN menu_items mi ON oi.item_id = mi.id
                  WHERE oi.order_id='$order_id'";
    $items_res = mysqli_query($conn, $items_sql);
    $items = [];
    while($row = mysqli_fetch_assoc($items_res)){
        $items[] = $row;
    }
}

// Clear cart after PayPal payment
unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 60px;
            color: #28a745;
        }
        h1 {
            color: #28a745;
            margin-bottom: 10px;
        }
        .order-details {
            text-align: left;
            margin-top: 30px;
        }
        .order-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-details th, .order-details td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .order-details th {
            background-color: #f0f0f0;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-top: 15px;
            text-align: right;
        }
        .btns {
            margin-top: 30px;
        }
        .btns a {
            text-decoration: none;
            padding: 12px 25px;
            margin: 0 10px;
            border-radius: 6px;
            font-weight: bold;
            color: #fff;
        }
        .btn-home { background-color: #007bff; }
        .btn-orders { background-color: #28a745; }
    </style>
</head>
<body>
<div class="container">
    <div class="success-icon">&#10004;</div>
    <h1>Payment Successful!</h1>
    <p>Your PayPal payment was successful and your order has been placed.</p>

    <?php if($order): ?>
    <div class="order-details">
        <h2>Order Details</h2>
        <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
        <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
        <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $it): ?>
                    <tr>
                        <td><?php echo $it['name']; ?></td>
                        <td><?php echo $it['quantity']; ?></td>
                        <td>$ <?php echo number_format($it['price'],2); ?></td>
                        <td>$ <?php echo number_format($it['price'] * $it['quantity'],2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            Total Paid: $ <?php echo number_format($order['total_amount'],2); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="btns">
        <a href="menu.php" class="btn-home">Order More</a>
        <a href="order_history.php" class="btn-orders">View My Orders</a>
    </div>
</div>
</body>
</html>
