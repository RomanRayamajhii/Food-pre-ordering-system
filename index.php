<?php
session_start();
include 'config/db.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    
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
        .cart-icon span{
            position: relative;
            top: -9px;
            right: 10px;
            background-color: #ff0000;
            color: #fff;
            padding: 2px 5px;
            border-radius: 50%;
            font-size: 0.8rem;
            font-weight: bold;
        }

        /* Hero Section */
        .hero {
            height: 80vh;
            background: url("./image/photo-1588644525273-f37b60d78512.jpeg") no-repeat center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-top: 60px;
        }

        .hero-content {
          
            max-width: 800px;
            padding: 0 20px;


           
        }

        .hero-content h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 1px 1px 3px #000;
           
           
        }

        .hero-content p {
            font-size: 2em;
            margin-bottom: 30px;
            color: #fff;
            text-shadow: 1px 1px 3px #000;
        }

        .order {
            display: inline-block;
            padding: 15px 30px;
            background-color: #fff;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .order:hover {
            background-color: #000;
            color: #fff;
            transform: translateY(-2px);
        }
        .special-hotels {
            text-align: center;
            background-color: #f9f9f9;
            padding: 50px 20px;
        }

        .special-hotels h1 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: #333;
            position: relative;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .special-hotel {
            display: inline-block;
            width: 300px;
            margin: 15px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .special-hotel:hover {
            transform: scale(1.05);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
        }

        .special-hotel img {
            width: 100%;
            height: auto;
            border-bottom: 2px solid #ddd;
        }

        .special-hotel h2 {
            font-size: 1.5rem;
            margin: 15px 0;
            color: #444;
        }

        .special-hotel p {
            font-size: 1rem;
            margin: 10px 15px;
            color: #666;
            line-height: 1.5;
        }

        .special-hotel a {
            display: inline-block;
            margin: 15px 0 20px;
            padding: 10px 20px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 1px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .special-hotel a:hover {
            background-color: #fff;
            color: #000;
            border: 2px solid #000;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display:flex;
                flex-direction: column;
                align-items: center;
                gap: 1rem;

            }
           
            .hero-content h1 {
                font-size: 2rem;
            }

            

    
        }


    </style>
   
  
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Food Pre Ordering System</a>
            <div class="nav-links">
                <a href="menu.php">Menu</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="order_history.php">My Orders</a>
                    <!-- <a href="checkout.php">Checkout</a> -->
                    <a href="logout.php">Logout</a>
                    <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count"><?php echo array_sum($_SESSION['cart']); ?></span>
                       
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
                </a>
              
            </div>
        </div>
    </nav>

   
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Save Time by Pre-Ordering Your Favorite Food</h1>
            <p class="hero-subtitle">Book now and pick it up at your convenience!</p>
            <a href="menu.php" class="order">Order Now</a>
        </div>
    </section>
    <section class="special-hotels">
    <h2 class="section-title">Our Restaurant</h2>
        <div class="special-hotel">
            <img src="./image/nepalese-restaurant.jpg" alt="">
            <h2>Hotel Everest</h2>
            <p>Hotel Everest is a Nepalese restaurant located in the heart of Kathmandu. We serve authentic Nepalese cuisine that will leave you wanting more. Our restaurant is known for its friendly staff and excellent service. Come and experience the taste of Nepal at Hotel Everest!</p>
            <a href="comment.php">View comments</a>
        </div>
    

    </section>

 
    <?php include 'footer.php'; ?>
</body>
</html>  