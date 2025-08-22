<?php
session_start();
include 'config/db.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) { header("Location: menu.php"); exit(); }

$order_res = mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id'");
$order = mysqli_fetch_assoc($order_res);

$items_res = mysqli_query($conn, "SELECT mi.name, oi.quantity, oi.price 
                                  FROM order_items oi 
                                  JOIN menu_items mi ON oi.item_id = mi.id 
                                  WHERE oi.order_id='$order_id'");
$items = [];
while($row = mysqli_fetch_assoc($items_res)){
    $items[] = $row;
}

unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Placed</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
   <style>
body {
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(135deg, #e8f5e9, #f0f4f8);
    margin: 0;
    padding: 0;
    color: #333;
}

.container {
    max-width: 900px;
    margin: 50px auto;
    padding: 35px 30px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

h1 {
    text-align: center;
    color: #00A950;
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 25px;
    letter-spacing: 1px;
}

.info {
    font-weight: 500;
    color: #444;
    margin: 8px 0;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 25px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

table th {
    background: linear-gradient(90deg, #00A950, #007a39);
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
    padding: 14px 16px;
    font-size: 14px;
}

table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 15px;
    text-align: center;

}

table tr:nth-child(even) {
    background: #f9f9f9;
}

table tr:last-child td {
    border-bottom: none;
}

table tr:hover {
    background: #e6f7ee;
    transition: 0.3s;
}

.total {
    text-align: right;
    font-size: 1.2rem;
    font-weight: 700;
    margin-top: 20px;
    color: #222;
}

.links {
    margin-top: 35px;
    text-align: center;
}

.links a {
    display: inline-block;
    text-decoration: none;
    color: #fff;
    background: linear-gradient(90deg, #00A950, #007a39);
    padding: 12px 26px;
    border-radius: 12px;
    margin: 0 12px;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 12px rgba(0,0,0,0.08);
}

.links a:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.15);
}

.badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-left: 5px;
    min-width: 90px;
    text-align: center;
}

.payment-status.completed { background: #28a745; color: #fff; }
.payment-status.pending { background: #ffc107; color: #212529; }
.payment-status.cancelled { background: #dc3545; color: #fff; }

/* Add subtle icon-like style to payment method */
.payment-method {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.payment-method i {
    font-size: 18px;
    color: #00A950;
}
</style>

</head>
<body>
    <div class="container">
        <h1>Order Placed Successfully!</h1>

        <p class="info"><strong>Order ID:</strong> <?= $order['id'] ?></p>
        <p class="info"><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
        <p class="info">
            <strong>Payment Status:</strong> 
            <span class="badge payment-status <?= strtolower($order['payment_status']) ?>">
                <?= ucfirst($order['payment_status']) ?>
            </span>
        </p>
        

        <table>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach($items as $it): ?>
            <tr>
                <td><?= $it['name'] ?></td>
                <td><?= $it['quantity'] ?></td>
                <td>$ <?= $it['price'] ?></td>
                <td>$ <?= $it['quantity'] * $it['price'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <p class="total"><strong>Total Amount to be Paid:</strong> $ <?= $order['total_amount'] ?></p>

        <div class="links">
            <a href="menu.php">Order More</a>
            <a href="order_history.php">View Orders</a>
        </div>
    </div>
</body>
</html>
