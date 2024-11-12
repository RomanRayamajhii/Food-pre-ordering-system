<?php
session_start();
include 'config/db.php';

// Only fetch menu items if requested
$showMenu = isset($_GET['show_menu']);
if ($showMenu) {
    $sql = "SELECT m.*, c.name as category_name 
            FROM menu_items m 
            INNER JOIN categories c ON m.category_id = c.id 
            WHERE m.status = 1 
            ORDER BY c.name, m.name";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Food Pre Ordering System</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="menu.php">Menu</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="order_history.php">My Orders</a>
                    <a href="checkout.php">Checkout</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.html">Login</a>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Save Time by Pre-Ordering Your Favorite Food</h1>
            <p class="hero-subtitle">Book now and pick it up at your convenience!</p>
            <a href="menu.php" class="cta-button">Order Now</a>
        </div>
    </section>

    <!-- Special Menu Section -->
    <section class="special-menu">
        <div class="container">
            <h2 class="section-title">Special Menu</h2>
            <div class="special-menu-grid">
                <?php
                // Fetch only special menu items (for example, top 4 items)
                $sql = "SELECT m.*, c.name as category_name 
                        FROM menu_items m 
                        INNER JOIN categories c ON m.category_id = c.id 
                        WHERE m.status = 1 
                        ORDER BY RAND() 
                        LIMIT 4";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()): ?>
                        <div class="special-item">
                            <div class="special-item-image">
                                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['name']); ?>">
                            </div>
                            <div class="special-item-content">
                                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="category"><?php echo htmlspecialchars($row['category_name']); ?></p>
                                <p class="description"><?php echo htmlspecialchars($row['description']); ?></p>
                                <p class="price">Rs. <?php echo number_format($row['price'], 2); ?></p>
                                <a href="menu.php" class="view-menu-btn">View Full Menu</a>
                            </div>
                        </div>
                    <?php endwhile;
                }
                ?>
            </div>
        </div>
    </section>

    <style>
        /* Basic Reset and Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Navbar Styles */
        .navbar {
            background: #000;
            padding: 1rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: #fff;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #ccc;
        }

        /* Hero Section */
        .hero {
            height: 80vh;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            margin-top: 60px;
            
        }
       

        .hero-content {
            max-width: 800px;
            padding: 0 20px;
        }

        .hero-content h1 {
            font-size: 1.5 rem;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            color: #eee;
        }

        .cta-button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #fff;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background-color: #000;
            color: #fff;
            transform: translateY(-2px);
        }

        /* Special Menu Section */
        .special-menu {
            padding: 80px 0;
            background-color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: #000;
            margin-bottom: 40px;
            position: relative;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .section-title:after {
            content: '';
            display: block;
            width: 60px;
            height: 2px;
            background: #000;
            margin: 15px auto;
        }

        .special-menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .special-item {
            background: #fff;
            border: 1px solid #e0e0e0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .special-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .special-item-image {
            position: relative;
            height: 250px;
            overflow: hidden;
        }

        .special-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
            filter: grayscale(0);
        }

        .special-item:hover .special-item-image img {
            transform: scale(1.1);
            filter: grayscale(50%);
        }

        .special-item-content {
            padding: 25px;
        }

        .special-item-content h3 {
            font-size: 1.5rem;
            color: #000;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .category {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .description {
            color: #444;
            font-size: 0.95rem;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .price {
            font-size: 1.3rem;
            color: #000;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .view-menu-btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 2px solid #000;
        }

        .view-menu-btn:hover {
            background-color: #fff;
            color: #000;
        }

        /* Cart Icon */
        .cart-icon {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #fff;
            color: #000;
            font-size: 0.8rem;
            padding: 2px 6px;
            border-radius: 50%;
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .special-menu {
                padding: 60px 0;
            }

            .section-title {
                font-size: 2rem;
            }

            .special-menu-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .special-menu {
                padding: 40px 0;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .special-item-content h3 {
                font-size: 1.3rem;
            }

            .nav-links {
                gap: 1rem;
            }
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Menu toggle functionality
        const menuLinks = document.querySelectorAll('.menu-link');
        const menuSection = document.getElementById('menu-section');

        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Show loading state
                menuSection.innerHTML = '<div class="loading">Loading menu...</div>';
                menuSection.style.display = 'block';

                // Fetch menu content
                fetch('index.php?show_menu=1')
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newMenuContent = doc.getElementById('menu-section');
                        
                        if (newMenuContent) {
                            menuSection.innerHTML = newMenuContent.innerHTML;
                            // Scroll to menu section
                            menuSection.scrollIntoView({ behavior: 'smooth' });
                            // Initialize add to cart functionality for new content
                            initializeAddToCart();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        menuSection.innerHTML = '<div class="error">Error loading menu. Please try again.</div>';
                    });
            });
        });

        // Add to cart functionality
        function initializeAddToCart() {
            const forms = document.querySelectorAll('.add-to-cart-form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    formData.append('action', 'add');

                    fetch('cart_process.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert('Item added to cart!');
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error adding item to cart');
                    });
                });
            });
        }
    });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html> 