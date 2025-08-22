<?php
session_start();
include 'config/db.php';

// Redirect if user not logged in or cart is empty
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: menu.php");
    exit();
}

$total = 0;
$cart_items = [];

foreach ($_SESSION['cart'] as $item_id => $quantity) {
    $sql = "SELECT * FROM menu_items WHERE id = $item_id";
    $result = $conn->query($sql);
    $item = $result->fetch_assoc();

    $subtotal = $quantity * $item['price'];
    $cart_items[] = [
        'id' => $item['id'],
        'name' => $item['name'],
        'price' => $item['price'],
        'quantity' => $quantity,
        'subtotal' => $subtotal
    ];
    $total += $subtotal;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <meta charset="UTF-8">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        body { font-family: Arial; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #000; color: white; }
        .form-group { margin-bottom: 15px; }
        input, textarea, select, button { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd; }
        button.payment-btn { background-color: #00A950; color: white; cursor: pointer; font-size: 16px; }
        button.remove-btn { background-color: #ff0000; color: white; padding: 5px 10px; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <h1>Checkout</h1>

    <table>
        <tr>
            <th>Item</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th>
        </tr>
        <?php foreach($cart_items as $item): ?>
        <tr id="item_<?php echo $item['id']; ?>">
            <td><?php echo $item['name']; ?></td>
            <td>$ <?php echo $item['price']; ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>$ <?php echo $item['subtotal']; ?></td>
            <td><button class="remove-btn" onclick="removeItem(<?php echo $item['id']; ?>)">Remove</button></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td colspan="2"><strong>$ <span id="total"><?php echo $total; ?></span></strong></td>
        </tr>
    </table>

  <form id="checkoutForm" action="process_order.php" method="POST">
    <div class="form-group">
        <label>Preferred Time:</label>
        <input type="text" name="preferred_time" id="preferred_time" placeholder="12:00 PM" required>
    </div>
    <div class="form-group">
        <label>Comments (Optional):</label>
        <textarea name="comments"></textarea>
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
        <label style="font-weight: bold; font-size: 18px; display: block; margin-bottom: 12px; color: #333;">
            Choose Payment Method
        </label>
        <div style="display: flex; gap: 30px; align-items: center;">
            <!-- Cash on Delivery -->
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 8px 12px; border: 2px solid #00A950; border-radius: 8px;">
                <input type="radio" name="payment_method" value="cash" checked style="accent-color: #00A950; width: 18px; height: 18px;">
                <span style="font-size: 16px; font-weight: 500; color: #00A950;">Cash on Delivery</span>
            </label>

            <!-- PayPal -->
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 8px 12px; border: 2px solid #0070ba; border-radius: 8px;">
                <input type="radio" name="payment_method" value="paypal" style="accent-color: #0070ba; width: 18px; height: 18px;">
                <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg" alt="PayPal" style="height: 32px; width: auto;">
                <span style="font-size: 16px; font-weight: 500; color: #0070ba;">PayPal</span>
            </label>
        </div>
    </div>

    <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
   
    <button type="submit" class="payment-btn">Place Order</button>
</form>
</div>

<script>
$(document).ready(function(){
    // Flatpickr Time Picker
    $("#preferred_time").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false
    });
});

// Remove item via AJAX
function removeItem(itemId){
    if(confirm('Remove this item?')){
        fetch('remove_cart_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'item_id=' + itemId
        }).then(res => res.json())
          .then(data => {
              if(data.success){
                  document.getElementById('item_' + itemId).remove();
                  document.getElementById('total').textContent = data.new_total;
                  if(data.cart_empty){ window.location.href='menu.php'; }
              } else { alert('Error removing item'); }
          });
    }
}
</script>
</body>
</html>
