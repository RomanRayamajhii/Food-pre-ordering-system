<?php
session_start();
include 'config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$item_id = $_GET['id'];
$sql = "SELECT * FROM menu_items WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['name']); ?> - Nepali Food</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        /* Navigation Bar Styles (same as index.php) */
        .navbar {
            background-color: #333;
            padding: 1rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 1.5rem;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        /* Item Detail Styles */
        .item-container {
            max-width: 1200px;
            margin: 100px auto 40px;
            padding: 20px;
            display: flex;
            gap: 40px;
        }

        .item-image {
            flex: 1;
            max-width: 500px;
        }

        .item-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .item-details {
            flex: 1;
            padding: 20px;
        }

        .item-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .item-price {
            font-size: 1.8rem;
            color: #ff4444;
            margin-bottom: 1.5rem;
        }

        .item-description {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #666;
            margin-bottom: 2rem;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quantity-btn {
            background: #eee;
            border: none;
            padding: 10px 15px;
            font-size: 1.2rem;
            cursor: pointer;
            border-radius: 4px;
        }

        .quantity-btn:hover {
            background: #ddd;
        }

        #quantity {
            width: 60px;
            padding: 8px;
            text-align: center;
            font-size: 1.1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .add-to-cart-btn {
            background: #ff4444;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .add-to-cart-btn:hover {
            background: #ff0000;
        }

        .nutrition-info {
            margin-top: 2rem;
            padding: 1rem;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .nutrition-title {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .nutrition-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .nutrition-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        @media (max-width: 768px) {
            .item-container {
                flex-direction: column;
            }
            
            .item-image {
                max-width: 100%;
            }
            
            .item-title {
                font-size: 2rem;
            }
        }

        /* Add these styles to your existing CSS */
        .item-image img {
            width: 100%;
            height: 400px; /* Fixed height */
            object-fit: cover; /* This will maintain aspect ratio */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Add a loading state */
        .item-image img:not([src]) {
            visibility: hidden;
        }

        /* Add a placeholder background while image loads */
        .item-image {
            background: #f5f5f5;
            position: relative;
            border-radius: 8px;
        }

        /* Add error handling styles */
        .item-image img.error {
            object-fit: contain;
            padding: 20px;
            background: #f8f8f8;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Nepali Food</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="index.php#menu">Menu</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="checkout.php">Checkout</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count"><?php echo array_sum($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </nav>

    <!-- Item Details -->
    <div class="item-container">
        <div class="item-image">
            <?php
            // Debug information (only show if needed)
            $debug = false;
            
            if ($debug) {
                echo "<div style='background: #f8f9fa; padding: 10px; margin-bottom: 10px;'>";
                echo "<h4>Debug Information</h4>";
                echo "Item ID: " . $item['id'] . "<br>";
                echo "Image name: " . ($item['image'] ?? 'None') . "<br>";
                echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
                echo "</div>";
            }

            // Construct image paths
            $relative_path = "uploads/menu/" . $item['image'];
            $absolute_path = $_SERVER['DOCUMENT_ROOT'] . "/dummyfood2/uploads/menu/" . $item['image'];
            $web_path = "/dummyfood2/uploads/menu/" . $item['image'];
            $default_image = "/dummyfood2/assets/images/default-food.jpg";

            // Check if image exists and use appropriate path
            if (!empty($item['image']) && file_exists($absolute_path)) {
                $display_path = $web_path;
            } else {
                $display_path = $default_image;
            }
            ?>

            <img src="<?php echo htmlspecialchars($display_path); ?>" 
                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                 onerror="this.onerror=null; this.src='<?php echo $default_image; ?>';"
                 style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px;">
        </div>
        
        <div class="item-details">
            <h1 class="item-title"><?php echo htmlspecialchars($item['name']); ?></h1>
            <div class="item-price">Rs. <?php echo number_format($item['price'], 2); ?></div>
            <p class="item-description"><?php echo htmlspecialchars($item['description']); ?></p>
            
            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                <div class="quantity-selector">
                    <button type="button" class="quantity-btn" onclick="decrementQuantity()">-</button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="10">
                    <button type="button" class="quantity-btn" onclick="incrementQuantity()">+</button>
                </div>
                <button type="submit" class="add-to-cart-btn">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </form>

            <div class="nutrition-info">
                <h3 class="nutrition-title">Nutrition Information</h3>
                <div class="nutrition-grid">
                    <div class="nutrition-item">
                        <strong>Calories</strong>
                        <p>350 kcal</p>
                    </div>
                    <div class="nutrition-item">
                        <strong>Protein</strong>
                        <p>12g</p>
                    </div>
                    <div class="nutrition-item">
                        <strong>Carbs</strong>
                        <p>45g</p>
                    </div>
                    <div class="nutrition-item">
                        <strong>Fat</strong>
                        <p>15g</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function incrementQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            if (currentValue < 10) {
                input.value = currentValue + 1;
            }
        }

        function decrementQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const itemImage = document.querySelector('.item-image img');
            
            itemImage.onerror = function() {
                this.src = 'assets/images/default-food.jpg';
                this.classList.add('error');
            };
            
            itemImage.onload = function() {
                this.style.visibility = 'visible';
            };
        });
    </script>
</body>
</html> 