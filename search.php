<?php
session_start();
include 'config/db.php';
include 'layout/header.php'; // Assuming you have a header file

if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    echo "<h1>Please enter a search term.</h1>";
    include 'layout/footer.php'; // Assuming you have a footer file
    exit();
}

$search_query = $_GET['query'];
$search_term = "%" . $search_query . "%";

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT id, name, description, price, image_path FROM menu_items WHERE name LIKE ? OR description LIKE ?");
$stmt->bind_param("ss", $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();

echo '<div class="container">';
echo '<h1>Search Results for "' . htmlspecialchars($search_query) . '"</h1>';

if ($result->num_rows > 0) {
    echo '<div class="menu-items">';
    while ($item = $result->fetch_assoc()) {
        // Display each item - customize this to match your menu.php layout
        echo '<div class="menu-item">';
        echo '<h3>' . htmlspecialchars($item['name']) . '</h3>';
        echo '<p>' . htmlspecialchars($item['description']) . '</p>';
        echo '<p>Price: $' . htmlspecialchars($item['price']) . '</p>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>No items found matching your search.</p>';
}
echo '</div>';

$stmt->close();
include 'layout/footer.php'; // Assuming you have a footer file
?>