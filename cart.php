<?php
session_start();
include 'config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <style>
    body {
    font-family: Arial, sans-serif;
    background-color:rgb(255, 255, 255);
    }
    .container table{
        width: 100%;
        border-collapse: collapse;
        }
        .container table th, .container table td{
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            }
            .qty{
                display: flex;
                justify-content: center;
            }
        .container table th{
            background-color: #f0f0f0;
            }
        .container table tr:nth-child(even){
            background-color: #f9f9f9;
            }
        .remove-btn{
            background-color: #f44336;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size:14px;
            padding: 5px 5px;
            cursor: pointer;
        }
        .remove-btn:hover{
            background-color:rgba(246, 7, 7, 0.63);
            }
        .quantity-btn{
            background-color:rgb(137, 132, 132);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
       .cart-actions{
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        background-color: #f0f0f0;
        border-bottom: 1px solid #ddd;

       }
       .btn{
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 16px;
        text-decoration: none;
       }
       .btn-primary{
        background-color:rgb(4, 122, 247);

       }
       .btn:hover{
        background-color:rgba(4, 122, 247, 0.63);
        }
        .btn-secondary{
            background-color:rgb(51, 158, 5);
            }
            .btn-secondary:hover{
                background-color:rgba(51, 158, 5, 0.63);
                }


   </style>
</head>
<body>
 

    <div class="container">
        <h2>Shopping Cart</h2>
        
        <?php if (!empty($_SESSION['cart'])): ?>
            <?php $total = 0;
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

           <div class="container">
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
                    <td>$ <?php echo $item['price']; ?></td>
                    <td>
                        <span class="qty" id="quantity_<?php echo $item['id']; ?>"><?php echo $item['quantity']; ?></span>
                    </td>
                    <td>$ <span id="subtotal_<?php echo $item['id']; ?>"><?php echo $item['subtotal']; ?></span></td>
                    <td>
                        <button class="remove-btn" onclick="removeItem(<?php echo $item['id']; ?>)">
                            Remove
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td colspan="2"><strong>$ <span id="total"><?php echo $total; ?></span></strong></td>
            </tr>
        </table>
    </div>

            <div class="cart-actions">
                <a href="menu.php" class="btn btn-secondary">Continue Shopping</a>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <p>Your cart is empty. <a href="menu.php" class="btn btn-secondary">Continue shopping</a></p>
        <?php endif; ?>
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

        function updateTotal() {
            let total = 0;
            <?php foreach($cart_items as $item): ?>
                let quantity_<?php echo $item['id']; ?> = parseInt(document.getElementById('quantity_<?php echo $item['id']; ?>').innerText);
                let price_<?php  echo $item['id']; ?> = <?php echo $item['price']; ?>;
                total += quantity_<?php echo $item['id']; ?> * price_<?php echo $item['id']; ?>;
            <?php endforeach; ?>
            
            document.getElementById('total').innerText = total;
            document.getElementById('total_amount').value = total;
        }
    </script> 


</body>
</html>