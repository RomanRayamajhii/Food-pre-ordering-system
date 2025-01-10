<!-- <?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false];

if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = $_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0 && $quantity <= 10) {
        $_SESSION['cart'][$item_id] = $quantity;
        $response['success'] = true;
    } else {
        unset($_SESSION['cart'][$item_id]);
        $response['success'] = true;
    }
}

echo json_encode($response);
?>
