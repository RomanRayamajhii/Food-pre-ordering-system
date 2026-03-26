<?php
session_start();
include 'config/db.php';

// Get all categories
$cat_sql = "SELECT * FROM categories WHERE status = 1";
$categories = $conn->query($cat_sql);

// Get menu items with images
$menu_sql = "SELECT m.*, c.name as category_name
FROM menu_items m
JOIN categories c ON m.category_id = c.id
WHERE m.status = 1
ORDER BY c.id, m.name";
$menu_items = $conn->query($menu_sql);


// Organize items by category
$menu_by_category = [];
while($item = $menu_items->fetch_assoc()) {
$menu_by_category[$item['category_name']][] = $item;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Our Menu</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
* {
margin: 0;
padding: 0;
box-sizing: border-box;
font-family: 'Poppins', sans-serif;
}

body {
background-color: #f5f5f5;
}
     .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #000;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px;
        }
.container {
max-width: 1200px;
margin: 0 auto;
padding: 20px;
}
 .logo {
            color: #fff;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }
.menu-header {
text-align: center;
padding: 40px 0;
background-color: #000;
color: #fff;
margin-bottom: 30px;
}

.menu-header h1 {
font-size: 36px;
margin-bottom: 10px;
}

.category-tabs {
display: flex;
justify-content: center;
gap: 15px;
margin-bottom: 30px;
flex-wrap: wrap;
}

.category-tab {
padding: 10px 20px;
background-color: #fff;
border: 2px solid #000;
border-radius: 25px;
cursor: pointer;
transition: all 0.3s ease;
}

.category-tab:hover,
.category-tab.active {
background-color: #000;
color: #fff;
}

.category-section {
margin-bottom: 40px;
}

.category-title {
font-size: 24px;
margin-bottom: 20px;
padding-bottom: 10px;
border-bottom: 2px solid #000;
}

.menu-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
gap: 25px;
margin-bottom: 30px;
}

.menu-item {
background: #fff;
border-radius: 15px;
overflow: hidden;
box-shadow: 0 3px 10px rgba(0,0,0,0.1);
transition: transform 0.3s ease;
}

.menu-item:hover {
transform: translateY(-5px);
}

.menu-item img {
width: 100%;
height: 200px;
object-fit: cover;
}

.item-info {
padding: 20px;
}

.item-name {
font-size: 18px;
font-weight: 600;
margin-bottom: 10px;
}

.item-description {
color: #666;
font-size: 14px;
margin-bottom: 15px;
height: 40px;
overflow: hidden;
}

.item-price {
font-weight: 600;
color: #000;
font-size: 18px;
margin-bottom: 15px;
}

.add-to-cart-form {
display: flex;
gap: 10px;
align-items: center;
}
.selector {
display: flex;
align-items: center;
}

.selector .quantity-btn {
padding: 5px;
margin: 4px;
border: none;
border-radius: 5px;
cursor: pointer;
font-size: 18px;
background-color: #f0f0f0;
}

.selector input {
width: 40px;
text-align: center;
padding: 5px;
font-size: 16px;
border: 2px solid #ccc;
border-radius: 5px;
}


.add-to-cart-btn {
flex: 1;
padding: 8px;
background-color: #000;
color: #fff;
border: none;
border-radius: 5px;
cursor: pointer;
transition: background-color 0.3s ease;
}

.add-to-cart-btn:hover {
background-color: #333;
}

.success-alert {
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
background-color: rgba(0, 0, 0, 0.7);
color: #fff;
display: none; /* Flex when active */
justify-content: center;
align-items: center;
flex-direction: column;
z-index: 1000;
font-size: 20px;
backdrop-filter: blur(5px);
}

@media (max-width: 768px) {
.menu-grid {
grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
}

.menu-header h1 {
font-size: 28px;
}

.category-tab {
padding: 8px 15px;
font-size: 14px;
}
}

/* Navbar Styles */
.navbar {
    background: #000;
    padding: 1rem;
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
    height: 70px;
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

.cart-count {
    position: relative;
    top: -10px;
    left: -5px;
    background-color: #ff0000;
    color: #fff;
    padding: 2px 5px;
    border-radius: 50%;
    font-size: 0.8rem;
}
</style>
</head>
<body>
    <div>
        <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Food Pre Ordering System</a>
            <div class="nav-links">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="order_history.php">My Orders</a>
                    <a href="logout.php">Logout</a>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart" style="color: white; font-family: 'Font Awesome 5 Free'; font-weight: 900;"></i>
                        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="cart-count"><?php echo array_sum($_SESSION['cart']); ?></span>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    </div> <br>
<div class="menu-header">
 

<h1>Our Menu</h1>
<p>Discover our delicious offerings</p>
</div>
<a href="index.php" class="back-btn">Back to Home</a>
<div class="container">
    <!-- search algorithm -->
    <input type="text" id="searchBox" onkeyup="searchMenu()" placeholder="Search food..." 
style="padding:10px; width:250px; display:block; margin:10px auto; border-radius:5px;">

<script>
function searchMenu() {
    let keyword = document.getElementById("searchBox").value.toLowerCase();
    let items = document.querySelectorAll('.menu-item');
    let sections = document.querySelectorAll('.category-section');
    let noResult = document.getElementById("noResult");

    let found = false;

    items.forEach(item => {
        let name = item.querySelector('.item-name').innerText.toLowerCase();
        if (name.includes(keyword)) {
            item.style.display = "block";
            found = true;
        } else {
            item.style.display = "none";
        }
    });

    // Show / hide categories based on available items
    sections.forEach(section => {
        let visibleItems = section.querySelectorAll('.menu-item[style*="block"]');

        if (visibleItems.length > 0) {
            section.style.display = "block";
        } else {
            section.style.display = "none";
        }
    });

    // Show "Search Not Found"
    noResult.style.display = found ? "none" : "block";
}
</script>

<!-- upto here search algorithm -->
<!-- filter  -->
 <div style="text-align:center; margin:10px 0;">
    <input type="number" id="minPrice" placeholder="Min Price" style="padding:7px; width:120px;">
    <input type="number" id="maxPrice" placeholder="Max Price" style="padding:7px; width:120px;">
    <button onclick="filterPrice()" style="padding:8px 15px; background:black; color:white; border:none; border-radius:5px;">
        Apply
    </button>
</div>
<script>
    function filterPrice() {
    let min = parseFloat(document.getElementById("minPrice").value) || 0;
    let max = parseFloat(document.getElementById("maxPrice").value) || Infinity;

    let items = document.querySelectorAll('.menu-item');

    items.forEach(item => {
        let price = parseFloat(item.querySelector('.item-price').innerText.replace('Rs. ',''));
        if (price >= min && price <= max) {
            item.style.display = "block";
        } else {
            item.style.display = "none";
        }
    });
}

</script>
<!-- sort -->
 <select id="sortOption" onchange="sortItems()" 
style="padding:10px; border-radius:5px; display:block; margin:10px auto;">
    <option value="">Sort by</option>
    <option value="low">Price: Low to High</option>
    <option value="high">Price: High to Low</option>
</select>
<!-- sort js -->
<script>
    function sortItems() {
    let option = document.getElementById("sortOption").value;
    let sections = document.querySelectorAll('.menu-grid');

    sections.forEach(grid => {
        let items = Array.from(grid.children);

        items.sort((a, b) => {
            let priceA = parseFloat(a.querySelector('.item-price').innerText.replace('Rs. ',''));
            let priceB = parseFloat(b.querySelector('.item-price').innerText.replace('Rs. ',''));

            return option === 'low' ? priceA - priceB : priceB - priceA;
        });

        items.forEach(i => grid.appendChild(i));
    });
}

</script>

<?php
if (empty($menu_by_category)) {
echo '<div style="color: red; padding: 20px;">No menu items found.</div>';
}
?>
<div class="category-tabs">
<div class="category-tab active" onclick="filterCategory('all')">All</div>
<?php while($category = $categories->fetch_assoc()): ?>
<div class="category-tab" onclick="filterCategory('<?php echo $category['name']; ?>')">
<?php echo $category['name']; ?>
</div>
<?php endwhile; ?>
</div>

<?php foreach($menu_by_category as $category => $items): ?>
<div class="category-section" data-category="<?php echo $category; ?>">
<h2 class="category-title"><?php echo $category; ?></h2>
<div class="menu-grid">
<?php foreach($items as $item): ?>
<div class="menu-item">
<?php
// Construct image path
$image_path = !empty($item['image'])
? "uploads/menu/" . $item['image']
: "assets/images/default-food.jpg";

// Check if file exists, otherwise use default
if (!empty($item['image']) && !file_exists($image_path)) {
$image_path = "assets/images/default-food.jpg";
}
?>
<img src="<?php echo htmlspecialchars($image_path); ?>"
alt="<?php echo htmlspecialchars($item['name']); ?>"
onerror="this.src='assets/images/default-food.jpg'">
<div class="item-info">
<h3 class="item-name"><?php echo $item['name']; ?></h3>
<p class="item-description"><?php echo $item['description']; ?></p>
<div class="item-price">Rs. <?php echo number_format($item['price'], 2); ?></div>
<form class="add-to-cart-form" onsubmit="return addToCart(this, event)">
<input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">

<div class="selector">
<button type="button" class="quantity-btn" onclick="decrementQuantity(this)">-</button>
<input type="number" class="quantity" name="quantity" value="1" min="1" max="15">
<button type="button" class="quantity-btn" onclick="incrementQuantity(this)">+</button>
</div>

<button type="submit" class="add-to-cart-btn">Add to Cart</button>
</form>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
<?php endforeach; ?>
</div>

<div id="successAlert" class="success-alert">
    <div style="background: white; color: black; padding: 30px 50px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
        <i class="fas fa-check-circle" style="font-size: 50px; color: #28a745; margin-bottom: 15px;"></i>
        <p style="font-weight: 600;">Item added to cart!</p>
    </div>
</div>
<script>
function incrementQuantity(button) {
const input = button.parentElement.querySelector('.quantity');
const currentValue = parseInt(input.value);
if (currentValue < 15) {
input.value = currentValue + 1;
}
}

function decrementQuantity(button) {
const input = button.parentElement.querySelector('.quantity');
const currentValue = parseInt(input.value);
if (currentValue > 1) {
input.value = currentValue - 1;
}
}

</script>
<script>
function filterCategory(category) {
const tabs = document.querySelectorAll('.category-tab');
const sections = document.querySelectorAll('.category-section');

tabs.forEach(tab => {
tab.classList.remove('active');
if(tab.innerText.toLowerCase() === category.toLowerCase() ||
(category === 'all' && tab.innerText.toLowerCase() === 'all')) {
tab.classList.add('active');
}
});

sections.forEach(section => {
if(category === 'all' || section.dataset.category === category) {
section.style.display = 'block';
} else {
section.style.display = 'none';
}
});
}

function addToCart(form, event) {
event.preventDefault();

fetch('add_to_cart.php', {
method: 'POST',
body: new FormData(form)
})
.then(response => response.json())
.then(data => {
    if(data.success) {
        const alert = document.getElementById('successAlert');
        alert.style.display = 'flex';
        setTimeout(() => {
            alert.style.display = 'none';
        }, 500);

        // Update cart count in navbar dynamically
        let cartCountSpan = document.querySelector('.cart-count');
        let addedQty = parseInt(form.querySelector('.quantity').value);
        
        if (cartCountSpan) {
            cartCountSpan.textContent = parseInt(cartCountSpan.textContent) + addedQty;
        } else {
            // If span doesn't exist (cart was empty), create it
            const cartIcon = document.querySelector('.cart-icon');
            cartIcon.insertAdjacentHTML('beforeend', `<span class="cart-count">${addedQty}</span>`);
        }
    }
});

return false;
}
</script>
</body>
</html>