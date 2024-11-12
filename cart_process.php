<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            addToCart();
            break;
        case 'update':
            updateCart();
            break;
        case 'remove':
            removeFromCart();
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
}

function addToCart() {
    if (isset($_POST['menu_id']) && isset($_POST['qty'])) {
        $menu_id = $_POST['menu_id'];
        $qty = (int)$_POST['qty'];
        
        // Validate menu item exists
        global $conn;
        $stmt = $conn->prepare("SELECT id, price FROM menu WHERE id = ? AND status = 1");
        $stmt->bind_param("i", $menu_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = array();
            }
            
            if (isset($_SESSION['cart'][$menu_id])) {
                $_SESSION['cart'][$menu_id] += $qty;
            } else {
                $_SESSION['cart'][$menu_id] = $qty;
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid menu item']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    }
}

function updateCart() {
    if (isset($_POST['menu_id']) && isset($_POST['qty'])) {
        $menu_id = $_POST['menu_id'];
        $qty = (int)$_POST['qty'];
        
        if ($qty > 0) {
            $_SESSION['cart'][$menu_id] = $qty;
            echo json_encode(['status' => 'success', 'message' => 'Cart updated']);
        } else {
            unset($_SESSION['cart'][$menu_id]);
            echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
        }
    }
}

function removeFromCart() {
    if (isset($_POST['menu_id'])) {
        $menu_id = $_POST['menu_id'];
        unset($_SESSION['cart'][$menu_id]);
        echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
    }
}
?>