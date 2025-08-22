<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all orders for the current user, including payment columns
$sql = "SELECT o.*, 
        GROUP_CONCAT(CONCAT(mi.name, ' x', oi.quantity) SEPARATOR ', ') as items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN menu_items mi ON oi.item_id = mi.id
        WHERE o.user_id = $user_id
        GROUP BY o.id
        ORDER BY o.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <style>
        .container { 
            max-width: 800px; 
            margin: 20px auto; 
            padding: 20px;
        }
        .order-card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            background: white;
        }
        .order-header {
            background: #f5f5f5;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            border-radius: 5px 5px 0 0;
            display: flex;
            justify-content: space-between;
        }
        .status-pending {
            color: #ffc107;
            background-color: #fff8e1;
        }
        .status-preparing {
            color: #C86482;
            background-color: #FFE4E1;
        }
        .status-completed {
            color: #28a745;
            background-color: #e8f5e9;
        }
        .status-cancelled {
            color: #dc3545;
            background-color: #ffebee;
        }
        .status-ready {
            color: #FF6347;
            background-color: #FFE4E1;
        }
        .status-confirmed {
            color: #17a2b8;
            background-color: #e3f2fd;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #000;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .payment-info {
            margin-top: 10px;
            font-size: 15px;
        }
        .payment-method {
            font-weight: bold;
        }
        .payment-status {
            font-weight: bold;
        }
     
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">Back to Home</a>
        <h1>My Orders</h1>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($order = $result->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-header">
                        <strong>Order #<?php echo $order['id']; ?></strong>
                        <span class="status-<?php echo strtolower($order['status']); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    <p><strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                    <p><strong>Items:</strong> <?php echo $order['items']; ?></p>
                    <p><strong>Total Amount:</strong> $ <?php echo number_format($order['total_amount'], 2); ?></p>
                    <p><strong>Preferred Time:</strong> <?php echo htmlspecialchars($order['preferred_time']); ?></p>
                    <p><strong>Comment:</strong> <?php echo htmlspecialchars($order['comment'] ?? 'N/A'); ?></p>
                    <?php if ($order['comments']): ?>
                        <p><strong>Comments:</strong> <?php echo htmlspecialchars($order['comments']); ?></p>
                    <?php endif; ?>
                    <div class="payment-info">
                        <span class="payment-method">Payment Method: <?php echo ucfirst($order['payment_method'] ?? 'cash'); ?></span><br>
                        <span class="payment-status">Payment Status: <?php echo ucfirst($order['payment_status'] ?? 'pending'); ?></span>
                        
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="order-card">
                <p>You haven't placed any orders yet.</p>
                <a href="menu.php" class="back-btn">Browse Menu</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
