<?php
session_start();
include 'includes/config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Handle status update
if(isset($_POST['update_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    
    try {
        // Update order status
        $update_query = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
        
        if(mysqli_stmt_execute($stmt)) {
            header("Location: manage_orders.php?msg=status_updated");
        } else {
            header("Location: manage_orders.php?msg=error&error=" . mysqli_error($conn));
        }
    } catch(Exception $e) {
        header("Location: manage_orders.php?msg=error&error=" . $e->getMessage());
    }
    exit();
}

// Handle order deletion
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    try {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        // First delete from order_items
        $delete_items = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = mysqli_prepare($conn, $delete_items);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        
        // Then delete the order
        $delete_order = "DELETE FROM orders WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_order);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        
        if(mysqli_stmt_execute($stmt)) {
            // Commit transaction
            mysqli_commit($conn);
            header("Location: manage_orders.php?msg=deleted");
        } else {
            // Rollback on error
            mysqli_rollback($conn);
            header("Location: manage_orders.php?msg=error&error=" . mysqli_error($conn));
        }
    } catch(Exception $e) {
        // Rollback on exception
        mysqli_rollback($conn);
        header("Location: manage_orders.php?msg=error&error=" . $e->getMessage());
    }
    exit();
}

// Handle order view
if(isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    try {
        // Get order details
        $query = "SELECT o.*, u.username, u.email, u.phone 
                 FROM orders o 
                 LEFT JOIN users u ON o.user_id = u.id 
                 WHERE o.id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($result);
        
        // Get order items
        $items_query = "SELECT oi.*, m.name as item_name 
                       FROM order_items oi 
                       LEFT JOIN menu_items m ON oi.menu_item_id = m.id 
                       WHERE oi.order_id = ?";
        $stmt = mysqli_prepare($conn, $items_query);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $items_result = mysqli_stmt_get_result($stmt);
        
        // Return JSON response
        $response = [
            'order' => $order,
            'items' => mysqli_fetch_all($items_result, MYSQLI_ASSOC)
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
}

// Handle order search
if(isset($_GET['action']) && $_GET['action'] == 'search') {
    $search = mysqli_real_escape_string($conn, $_GET['term']);
    
    try {
        $query = "SELECT o.*, u.username, u.email 
                 FROM orders o 
                 LEFT JOIN users u ON o.user_id = u.id 
                 WHERE o.id LIKE ? 
                 OR u.username LIKE ? 
                 OR u.email LIKE ?";
        $search_term = "%$search%";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($orders);
        exit();
    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
}

// If no valid action is performed, redirect back
header("Location: manage_orders.php");
exit();
?>
