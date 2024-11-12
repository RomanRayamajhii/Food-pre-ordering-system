<?php
function getCartItems() {
    global $conn;
    $cart_items = [];
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $menu_ids = array_keys($_SESSION['cart']);
        $ids_string = implode(',', array_map('intval', $menu_ids));
        
        $sql = "SELECT m.*, c.name as category_name 
                FROM menu m 
                INNER JOIN category c ON m.category_id = c.id 
                WHERE m.id IN ($ids_string)";
        
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['quantity'] = $_SESSION['cart'][$row['id']];
                $row['subtotal'] = $row['price'] * $row['quantity'];
                $cart_items[] = $row;
            }
        }
    }
    
    return $cart_items;
}

function getCartTotal() {
    $cart_items = getCartItems();
    $total = 0;
    
    foreach ($cart_items as $item) {
        $total += $item['subtotal'];
    }
    
    return $total;
}

function getCartCount() {
    if (isset($_SESSION['cart'])) {
        return array_sum($_SESSION['cart']);
    }
    return 0;
}

function clearCart() {
    unset($_SESSION['cart']);
}
?> 