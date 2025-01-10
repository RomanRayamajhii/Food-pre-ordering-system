<?php
session_start();
include './header.php';
include 'includes/config.php';

// Handle deletion of a category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['id']);
    
    if ($id > 0) {
        // First delete the menu items related to the category
        $delete_items_query = "DELETE FROM menu_items WHERE category_id = $id";
        if (!mysqli_query($conn, $delete_items_query)) {
            $_SESSION['error_message'] = "Error deleting menu items: " . mysqli_error($conn);
        }

        // Then delete the category
        $delete_query = "DELETE FROM categories WHERE id = $id";
        if (!mysqli_query($conn, $delete_query)) {
            $_SESSION['error_message'] = "Error deleting category: " . mysqli_error($conn);
        } else {
            $_SESSION['success_message'] = "Category and related menu items deleted successfully!";
        }
    } else {
        $_SESSION['error_message'] = "Invalid category ID.";
    }

    // Redirect to avoid resubmission
    header('Location: manage_categories.php');
    exit();
}

// Fetch all categories
$cat_sql = "SELECT * FROM categories";
$categories = mysqli_query($conn, $cat_sql);
if (!$categories) {
    die("Error fetching categories: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
    <h2>Manage Categories</h2>

    <!-- Display success message -->
     <div class="message">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <?php 
            echo $_SESSION['success_message']; 
            unset($_SESSION['success_message']); // Clear the message after displaying
            ?>
            <button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Display error message -->
     <div class="message">
     
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message">
            <?php 
            echo $_SESSION['error_message']; 
            unset($_SESSION['error_message']); // Clear the message after displaying
            ?>
            <button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <table >
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($categories)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo $row['status'] ? 'Active' : 'Inactive'; ?></td>
                <td>
                    <form action="manage_categories.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this category?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    </div>
    <button class="add-btn" onclick="document.getElementById('addCategoryModal').style.display='block';">Add New Category</button>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" style="display:none;">
        <div class="modal-content">
            <span onclick="document.getElementById('addCategoryModal').style.display='none';" class="close">&times;</span>
            <h2>Add New Category</h2>
            <form action="add_category.php" method="POST">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" required>
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" required></textarea>
                <label for="status">Active</label>
                <input type="checkbox" id="status" name="status"  class="checkbox"checked>
                <button type="submit" class="add-btn" style="float:right;
               margin-bottom:20px;">Add Category</button>
            </form>
        </div>
        </div>
        </body>
        </html>
<style>
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fa;
    

}

.container {
    max-width: 1200px;
    margin: 40px 20px;
  
}

.heading {
    font-size: 2em;
    margin-bottom: 20px;
}

.message {
    margin-bottom: 20px;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 10px;
    border-radius: 5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 10px;
    border-radius: 5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

button.close {
    background: transparent;
    border: none;
    font-size: 18px;
    cursor: pointer;
}

button.close:hover {
    opacity: 0.7;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}



th, td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: rgb(192, 188, 188);
    font-weight: bold;
}

td {
    font-size: 14px;
}
table tr:nth-child(odd){
    background-color:rgb(234, 234, 234);
}




.delete-btn {
    background-color:rgb(228, 9, 9);
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 7px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.delete-btn:hover {
    background-color:rgb(243, 81, 81);
}

.add-btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.3s ease;
}

.add-btn:hover {
    background-color: #45a049;
}
.modal {
    display: none;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 30px;
    border: 1px solid #888;
    width: 40%;
    border-radius: 8px;
}

.close {
    color: black;
    font-size: 28px;
    cursor: pointer;
    float: right;
}

.close:hover,
.close:focus {
    opacity: 0.5;
}


label {
    display: block;
    margin: 10px 0 5px;
  
}

input[type="text"],
textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

textarea {
    resize: vertical;
}

input[type="checkbox"] {
    margin-right: 5px;
    transform: scale(1.5); 
}

.modal-content h2 {
    font-size: 1.5em;
    margin-bottom: 20px;
}

</style>
</body>
</html>