<?php
session_start();
include 'config/db.php';

// Your eSewa Merchant ID
define('MERCHANT_ID', '9806800001'); // Replace with your eSewa ID

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
        .quantity-btn {
            padding: 5px 10px;
            margin: 0 5px;
            background-color: #000;
            color: white;
            border: none;
            cursor: pointer;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .payment-btn {
            background-color: #00A950; /* eSewa green color */
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
        #payment_amount {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        select[name="payment_type"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            margin-bottom: 10px;
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
                        <button type="button" class="quantity-btn" 
                                onclick="updateQuantity(<?php echo $item['id']; ?>, 'decrease')">-</button>
                        <span id="quantity_<?php echo $item['id']; ?>"><?php echo $item['quantity']; ?></span>
                        <button type="button" class="quantity-btn" 
                                onclick="updateQuantity(<?php echo $item['id']; ?>, 'increase')">+</button>
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
                <label>Full Name:</label>
                <input type="text" name="full_name" required>
            </div>

            <div class="form-group">
                <label>Phone Number:</label>
                <input type="tel" name="phone" required>
            </div>

            <div class="form-group">
                <label>Delivery Address:</label>
                <textarea name="address" required></textarea>
            </div>

            <div class="form-group">
                <label>Preferred Time:</label>
                <select name="delivery_time" required>
                    <option value="">Select Time</option>
                    <option value="asap">As Soon As Possible</option>
                    <option value="1hour">Within 1 Hour</option>
                    <option value="2hour">Within 2 Hours</option>
                </select>
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
        function updateQuantity(itemId, action) {
            let quantitySpan = document.getElementById('quantity_' + itemId);
            let quantity = parseInt(quantitySpan.innerText);
            let price = <?php echo json_encode(array_column($cart_items, 'price', 'id')); ?>[itemId];
            
            if(action === 'increase' && quantity < 10) {
                quantity++;
            } else if(action === 'decrease' && quantity > 1) {
                quantity--;
            }
            
            quantitySpan.innerText = quantity;
            
            // Update subtotal
            let subtotal = quantity * price;
            document.getElementById('subtotal_' + itemId).innerText = subtotal;
            
            // Update total
            updateTotal();
            
            // Update server-side cart
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'item_id=' + itemId + '&quantity=' + quantity
            });
        }

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

        function updateTotal() {
            let total = 0;
            <?php foreach($cart_items as $item): ?>
                let quantity_<?php echo $item['id']; ?> = parseInt(document.getElementById('quantity_<?php echo $item['id']; ?>').innerText);
                let price_<?php echo $item['id']; ?> = <?php echo $item['price']; ?>;
                total += quantity_<?php echo $item['id']; ?> * price_<?php echo $item['id']; ?>;
            <?php endforeach; ?>
            
            document.getElementById('total').innerText = total;
            document.getElementById('total_amount').value = total;
        }
    </script>
</body>
</html> 