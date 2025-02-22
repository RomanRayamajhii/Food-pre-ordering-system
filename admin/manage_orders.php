<?php
session_start();
include './header.php';
include 'includes/config.php';



// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id =  $_POST['order_id'];
    $new_status =$_POST['new_status'];
  
    $update_query = "UPDATE orders SET status = '$new_status' WHERE id = '$order_id'";
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success_message'] = "Order status updated to '$new_status' successfully!";
    } else {
        $_SESSION['error_message'] = 'Error updating status: ' . mysqli_error($conn);
    }
    header('Location: manage_orders.php');
    exit();
}

// Function to get badge class based on status
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending':
            return 'status-pending';
        case 'confirmed':
            return 'status-confirmed';
        case 'preparing':
            return 'status-preparing';
        case 'ready':
            return 'status-ready';
        case 'completed':
            return 'status-completed';
        case 'cancelled':
            return 'status-cancelled';
        default:
            return 'status-secondary';
    }
}
// Handle delete action
if(isset($_GET['delete'])) {
    $order_id =  $_GET['delete'];
    
    // First delete from order_items
    $delete_items = "DELETE FROM order_items WHERE order_id = '$order_id'";
    mysqli_query($conn, $delete_items);
    
    // Then delete the order
    $delete_order = "DELETE FROM orders WHERE id = '$order_id'";
    if(mysqli_query($conn, $delete_order)) {
        
        $_SESSION['success_message'] = "Deleted Order Id '$order_id' successfully!";
    
    } else {
        $_SESSION['error_message'] = 'Error deleting order!';
    }
}

// Fetch orders
$query = "SELECT o.*,u.username, u.full_name, u.email, u.phone,u.address
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" role="alert">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="close">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error" role="alert">
            <?php 
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            ?>
            <button type="button" class="close">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Orders</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead >
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username'] ?? 'N/A'); ?></td>
                            <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($row['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <!-- View Button -->
                               

                                <!-- Update Status Form -->
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <select name="new_status" class="form-control" >
                                        <option value="pending" <?php echo (strtolower($row['status']) == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo (strtolower($row['status']) == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="preparing" <?php echo (strtolower($row['status']) == 'preparing') ? 'selected' : ''; ?>>Preparing</option>
                                        <option value="ready" <?php echo (strtolower($row['status']) == 'ready') ? 'selected' : ''; ?>>Ready</option>
                                        <option value="completed" <?php echo (strtolower($row['status']) == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo (strtolower($row['status']) == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-update ">
                                       Update
                                    </button>
                                    <button type="button" class="btn btn-view" data-toggle="modal" data-target="#orderModal<?php echo $row['id']; ?>">
                                        View
                                    </button>
                                </form>

                                <!-- Delete Button -->
                                <a href="?delete=<?php echo $row['id']; ?>" 
                                   
                                   onclick="return confirm('Are you sure you want to delete this order?');">
                                   <button  class="btn btn-danger "> Delete</button> 
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

<!-- Order Details Modals -->
<?php 
mysqli_data_seek($result, 0); // Reset the result pointer
while ($row = mysqli_fetch_assoc($result)) : 
    // Fetch order items for this order
    $order_id = $row['id'];
    $items_query = "SELECT oi.*, mi.name as menu_name, mi.price 
                   FROM order_items oi 
                   JOIN menu_items mi ON oi.item_id = mi.id 
                   WHERE oi.order_id = $order_id";
    $items_result = mysqli_query($conn, $items_query);
    
    if (!$items_result) {
        die("Query failed: " . mysqli_error($conn));
    }
?>
<div class="modal" id="orderModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel<?php echo $row['id']; ?>">
                    Order Details #<?php echo $row['id']; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span >&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div >
                        <h5>Customer Information</h5>
                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($row['full_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>

                    </div>
                    <div >
                        <h5>Order Information</h5>
                        <p><strong>Order Date:</strong> <?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></p>
                        <p><strong>Preferred_time:</strong> <?php echo htmlspecialchars($row['preferred_time']); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge <?php echo getStatusBadgeClass($row['status']); ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </p>
                        <p><strong>Total Amount:</strong> Rs. <?php echo number_format($row['total_amount'], 2); ?></p>
                        <strong>Comments:</strong> <?php echo htmlspecialchars($row['comments']); ?></p>
                    </div>
                </div>

                <h5>Order Items</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = 0;
                            while ($item = mysqli_fetch_assoc($items_result)) : 
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['menu_name']); ?></td>
                                    <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>Rs. <?php echo number_format($subtotal, 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total:</th>
                                <th>Rs. <?php echo number_format($total, 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
    const modalClosers = document.querySelectorAll('[data-dismiss="modal"]');

    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const targetModal = document.querySelector(this.dataset.target);
            if (targetModal) {
                targetModal.style.display = 'block';
            }
        });
    });
    // Close button functionality
    modalClosers.forEach(closer => {
        closer.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = this.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });

    // Alert close functionality
    const alertClosers = document.querySelectorAll('.alert .close');
    alertClosers.forEach(closer => {
        closer.addEventListener('click', function() {
            const alert = this.closest('.alert');
            if (alert) {
                alert.style.display = 'none';
            }
        });
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

