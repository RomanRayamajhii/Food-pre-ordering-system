<?php
session_start();
include 'config/db.php';
if(!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: menu.php");
    exit();
}

$total = 0;
$cart_items = [];

foreach($_SESSION['cart'] as $item_id => $quantity) {
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Include Flatpickr CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #000;
            color: white;
        }
        .form-group {
            margin: 15px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .payment-btn {
            background-color: #00A950;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .payment-btn:hover {
            background-color: #008940;
        }
        .remove-btn {
            background-color: #ff0000;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .remove-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>

        <table>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            <?php foreach($cart_items as $item): ?>
                <tr id="item_<?php echo $item['id']; ?>">
                    <td><?php echo $item['name']; ?></td>
                    <td>Rs. <?php echo $item['price']; ?></td>
                    <td>
                        <span id="quantity_<?php echo $item['id']; ?>"><?php echo $item['quantity']; ?></span>
                    </td>
                    <td>Rs. <span id="subtotal_<?php echo $item['id']; ?>"><?php echo $item['subtotal']; ?></span></td>
                    <td>
                        <button class="remove-btn" onclick="removeItem(<?php echo $item['id']; ?>)">
                            Remove
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td colspan="2"><strong>Rs. <span id="total"><?php echo $total; ?></span></strong></td>
            </tr>
        </table>

        <form action="process_order.php" method="POST">
        
            <div class="form-group">
                <label for="preferred_time">Preferred Time:</label>
                <input type="text" name="preferred_time" id="preferred_time" style="font-size:15px" placeholder="12:00" required>
                

<script>
  $(document).ready(function () {
    // Initialize Flatpickr using jQuery
    $("#preferred_time").flatpickr({
      enableTime: true,
      noCalendar: true,
      dateFormat: "h:i K",
      time_24hr: false
    });
  });
</script>
            </div>
            


            <div class="form-group">
                <label>Comments (Optional):</label>
                <textarea name="comments"></textarea>
            </div>

            <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
            
            <button type="submit" class="payment-btn">Place Order</button>
        </form>
    </div>

    <script>
        function removeItem(itemId) {
            if(confirm('Are you sure you want to remove this item?')) {
                // Send AJAX request to remove item
                fetch('remove_cart_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'item_id=' + itemId
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Remove item row from table
                        document.getElementById('item_' + itemId).remove();
                        
                        // Update total
                        document.getElementById('total').textContent = data.new_total;
                        
                        // If cart is empty, redirect to menu
                        if(data.cart_empty) {
                            window.location.href = 'menu.php';
                        }
                    } else {
                        alert('Error removing item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing item');
                });
            }
        }
    </script>
</body>
</html>
