<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch user's orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Nepali Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Include your existing navbar styles */

        .dashboard-container {
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 20px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
        }

        .sidebar {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #333;
            color: white;
        }

        .content-area {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .profile-section {
            margin-bottom: 30px;
        }

        .profile-section h2 {
            margin-bottom: 20px;
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .detail-item {
            margin-bottom: 15px;
        }

        .detail-item label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #666;
        }

        .orders-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-section th,
        .orders-section td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .orders-section th {
            background: #f5f5f5;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .status-pending { background: #ffd700; }
        .status-confirmed { background: #87ceeb; }
        .status-preparing { background: #98fb98; }
        .status-delivered { background: #90ee90; }
        .status-cancelled { background: #ff6b6b; }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Include your navbar here -->

    <div class="dashboard-container">
        <div class="dashboard-grid">
            <div class="sidebar">
                <ul class="sidebar-menu">
                    <li><a href="#profile" class="active">My Profile</a></li>
                    <li><a href="#orders">My Orders</a></li>
                    <li><a href="edit_profile.php">Edit Profile</a></li>
                    <li><a href="change_password.php">Change Password</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>

            <div class="content-area">
                <section id="profile" class="profile-section">
                    <h2>My Profile</h2>
                    <div class="profile-details">
                        <div class="detail-item">
                            <label>Username</label>
                            <div><?php echo htmlspecialchars($user['username']); ?></div>
                        </div>
                        <div class="detail-item">
                            <label>Full Name</label>
                            <div><?php echo htmlspecialchars($user['full_name']); ?></div>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <div><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="detail-item">
                            <label>Phone</label>
                            <div><?php echo htmlspecialchars($user['phone']); ?></div>
                        </div>
                        <div class="detail-item">
                            <label>Address</label>
                            <div><?php echo htmlspecialchars($user['address']); ?></div>
                        </div>
                    </div>
                </section>

                <section id="orders" class="orders-section">
                    <h2>My Orders</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['order_status']; ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="view_order.php?id=<?php echo $order['id']; ?>">View Details</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </div>
</body>
</html> 