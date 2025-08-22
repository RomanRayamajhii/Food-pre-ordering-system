<?php
session_start();
include './header.php';
include 'includes/config.php';

// Handle status update (Order & Payment)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    $new_payment_status = $_POST['payment_status'] ?? 'pending';

    $update_query = "UPDATE orders SET status='$new_status', payment_status='$new_payment_status' WHERE id='$order_id'";
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success_message'] = "Order #$order_id updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating order: " . mysqli_error($conn);
    }
    header('Location: manage_orders.php');
    exit();
}

// Handle delete action
if (isset($_GET['delete'])) {
    $order_id = $_GET['delete'];

    // Delete order items first
    mysqli_query($conn, "DELETE FROM order_items WHERE order_id='$order_id'");
    // Then delete the order
    if (mysqli_query($conn, "DELETE FROM orders WHERE id='$order_id'")) {
        $_SESSION['success_message'] = "Deleted Order #$order_id successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting order!";
    }
    header('Location: manage_orders.php');
    exit();
}

// Function to get badge class based on status
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending': return 'status-pending';
        case 'confirmed': return 'status-confirmed';
        case 'preparing': return 'status-preparing';
        case 'ready': return 'status-ready';
        case 'completed': return 'status-completed';
        case 'cancelled': return 'status-cancelled';
        default: return 'status-secondary';
    }
}

// Fetch all orders with customer info
$query = "SELECT o.*, u.username, u.full_name, u.email, u.phone, u.address
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);
if (!$result) die("Query failed: " . mysqli_error($conn));
?>

<div class="container-fluid">

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="close">&times;</button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="close">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">Manage Orders</h3></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username'] ?? 'N/A'); ?></td>
                            <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><?php echo ucfirst($row['payment_method'] ?? 'cash'); ?></td>
                            <td>
                                <span class="badge <?php echo ($row['payment_status']=='completed')?'status-completed':(($row['payment_status']=='pending')?'status-pending':'status-cancelled'); ?>">
                                    <?php echo ucfirst($row['payment_status'] ?? 'pending'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <!-- Update Status Form -->
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">

                                    <select name="new_status" class="form-control">
                                        <option value="pending" <?php echo ($row['status']=='pending')?'selected':''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo ($row['status']=='confirmed')?'selected':''; ?>>Confirmed</option>
                                        <option value="preparing" <?php echo ($row['status']=='preparing')?'selected':''; ?>>Preparing</option>
                                        <option value="ready" <?php echo ($row['status']=='ready')?'selected':''; ?>>Ready</option>
                                        <option value="completed" <?php echo ($row['status']=='completed')?'selected':''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo ($row['status']=='cancelled')?'selected':''; ?>>Cancelled</option>
                                    </select>

                                    <select name="payment_status" class="form-control" style="margin-top:5px;">
                                        <option value="pending" <?php echo ($row['payment_status']=='pending')?'selected':''; ?>>Pending</option>
                                        <option value="completed" <?php echo ($row['payment_status']=='completed')?'selected':''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo ($row['payment_status']=='cancelled')?'selected':''; ?>>Cancelled</option>
                                    </select>

                                    <button type="submit" name="update_status" class="btn btn-update" style="margin-top:5px;">Update</button>
                                </form>

                                <!-- View Button -->
                                <button class="btn btn-view" data-toggle="modal" data-target="#orderModal<?php echo $row['id']; ?>">View</button>

                                <!-- Delete Button -->
                                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">
                                    <button class="btn btn-danger">Delete</button>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Order Details -->
<?php 
mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) :
    $order_id = $row['id'];
    $items_query = "SELECT oi.*, mi.name as menu_name, mi.price FROM order_items oi JOIN menu_items mi ON oi.item_id=mi.id WHERE oi.order_id=$order_id";
    $items_result = mysqli_query($conn, $items_query);
?>
<div class="modal" id="orderModal<?php echo $row['id']; ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Order Details #<?php echo $row['id']; ?></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h5>Customer Info</h5>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($row['full_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>

                <h5>Order Info</h5>
                <p><strong>Date:</strong> <?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></p>
                <p><strong>Preferred Time:</strong> <?php echo htmlspecialchars($row['preferred_time']); ?></p>
                <p><strong>Status:</strong> <?php echo $row['status']; ?></p>
                <p><strong>Payment Status:</strong> <?php echo $row['payment_status']; ?></p>
                <p><strong>Total Amount:</strong> $<?php echo number_format($row['total_amount'],2); ?></p>
                <p><strong>Comments:</strong> <?php echo htmlspecialchars($row['comments']); ?></p>
                <h5>Order Items</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total=0; while($item=mysqli_fetch_assoc($items_result)): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo $item['menu_name']; ?></td>
                            <td>$<?php echo number_format($item['price'],2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($subtotal,2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total: </th>
                            <th>$<?php echo number_format($total,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
    const modalClosers = document.querySelectorAll('[data-dismiss="modal"]');
    modalTriggers.forEach(trigger=>{
        trigger.addEventListener('click', function(e){
            e.preventDefault();
            document.querySelector(this.dataset.target).style.display='block';
        });
    });
    modalClosers.forEach(closer=>{
        closer.addEventListener('click', function(e){
            e.preventDefault();
            this.closest('.modal').style.display='none';
        });
    });
    window.addEventListener('click', function(e){
        if(e.target.classList.contains('modal')) e.target.style.display='none';
    });
    document.querySelectorAll('.alert .close').forEach(c=>{
        c.addEventListener('click', function(){ this.parentElement.style.display='none'; });
    });
});
</script>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f4f4f4;
}

/* Container */
.container-fluid {
    width: 95%;
    margin: 20px auto;
    padding: 0 15px;
}

/* Card Styles */
.card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
    border-radius: 8px 8px 0 0;
}

.card-title {
    font-size: 1.5em;
    color: #333;
}

.card-body {
    padding: 20px;
}

/* Table Styles */
.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
    background: #fff;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background: #333;
    color: #fff;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

/* Badge Styles */
.badge {
    padding: 5px 10px;
    border-radius: 4px;
    color: #fff;
    font-size: 0.85em;
}

.status-pending { background: #ffc107; color: #000; }
.status-confirmed { background: #17a2b8; }
.status-preparing { background: #DB7093; }
.status-completed { background: #28a745; }
.status-ready { background:#FF6347; }
.status-cancelled { background: #dc3545; }



.form-control {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    display: inline-block;
    width: auto;
}

select.form-control {
    padding-right: 30px;
}


/* Button Styles */
.btn {
    display: inline-block;
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    margin: 2px;
    color: #fff;
    text-decoration: none;
}



.btn-update { background: #007bff; }
.btn-view { background: #17a2b8; }
.btn-danger { background: #dc3545; }
.btn-secondary { background:rgb(81, 80, 80); }

.btn:hover {
    opacity: 0.9;
}


.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1050;
}

.modal.show {
    display: block;
}

.modal-dialog {
    position: relative;
    width: 90%;
    max-width: 800px;
    margin: 30px auto;
    background: #fff;
    border-radius: 8px;
}

.modal-content {
    position: relative;
    background: #fff;
    border-radius: 8px;
}

.modal-header {
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
}

.modal-title {
    font-size: 1.2em;
    margin: 0;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #ddd;
    text-align: right;
}

.close {
    position: absolute;
    right: 15px;
    top: 15px;
    font-size: 24px;
    cursor: pointer;
    background: none;
    border: none;
}

/* Alert Styles */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    position: relative;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert .close {
    position: absolute;
    right: 10px;
    top: 10px;
    color: inherit;
}
@media (max-width: 768px) {
    .container-fluid {
        width: 100%;
        padding: 10px;
    }

    .form-control, .btn {
        width: 100%;
        margin: 5px 0;
    }

    .modal-dialog {
        width: 95%;
        margin: 10px auto;
    }

    td, th {
        padding: 8px;
    }
}


.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1050;
}

.modal.show {
    display: block;
}


.modal-footer .btn-secondary {
    color: #fff;
    text-decoration: none;
}

.modal-footer .btn-secondary:hover {
    color: #fff;
    text-decoration: none;
}
</style>

