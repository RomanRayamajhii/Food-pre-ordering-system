<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for your order. Your order number is: <?php echo $_GET['id']; ?></p>
        <p>We will contact you shortly with delivery details.</p>
        <p><a href="menu.php">Return to Menu</a></p>
    </div>
</body>
</html> 