<?php
session_start();
include 'config/db.php';
include 'cart_functions.php';

$cart_items = getCartItems();
$cart_total = getCartTotal();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Add your CSS here -->
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2>Shopping Cart</h2>
        
        <?php if (!empty($cart_items)): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     class="cart-item-image">
                            </td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form class="update-cart-form">
                                    <input type="hidden" name="menu_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="qty" value="<?php echo $item['quantity']; ?>" 
                                           min="1" class="quantity-input">
                                </form>
                            </td>
                            <td>Rs. <?php echo number_format($item['subtotal'], 2); ?></td>
                            <td>
                                <form class="remove-item-form">
                                    <input type="hidden" name="menu_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="remove-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Total:</strong></td>
                        <td>Rs. <?php echo number_format($cart_total, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="cart-actions">
                <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <p>Your cart is empty. <a href="index.php">Continue shopping</a></p>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update quantity
        document.querySelectorAll('.update-cart-form .quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const form = this.closest('form');
                const formData = new FormData(form);
                formData.append('action', 'update');
                
                updateCart(formData);
            });
        });

        // Remove item
        document.querySelectorAll('.remove-item-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'remove');
                
                removeFromCart(formData);
            });
        });
    });
    </script>
</body>
</html> 