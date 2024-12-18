<?php 
session_start();
include 'includes/header.php'; 

// Fetch total orders and total sold price for the day
$totalOrders = 0; // Initialize total orders
$totalSoldPrice = 0.00; // Initialize total sold price

// Database connection (update with your actual connection details)
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get total orders and total sold price for today
$sql = "SELECT COUNT(*) as total_orders, SUM(price) as total_sold_price FROM orders WHERE DATE(order_date) = CURDATE()";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalOrders = $row['total_orders'];
    $totalSoldPrice = $row['total_sold_price'];
}

$conn->close();
?>

<div class="container mt-4">
    <div class="row mt-4">
        <!-- ... existing cards ... -->
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3 h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-chart-line"></i> Daily Report</h5>
                    <p class="card-text flex-grow-1">Total Orders Today: <span id="total-orders"><?php echo $totalOrders; ?></span></p>
                    <p class="card-text flex-grow-1">Total Sold Price: <span id="total-sold-price">$<?php echo number_format($totalSoldPrice, 2); ?></span></p>
                    <a href="daily_report.php" class="btn btn-light mt-auto">View Report</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 