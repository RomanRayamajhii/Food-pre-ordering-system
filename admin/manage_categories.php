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

// Handle update of category name
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    if ($id > 0 && !empty($name)) {
        $update_query = "UPDATE categories SET name = '$name', description = '$description' WHERE id = $id";
        if (!mysqli_query($conn, $update_query)) {
            $_SESSION['error_message'] = "Error updating category: " . mysqli_error($conn);
        } else {
            $_SESSION['success_message'] = "Category updated successfully!";
        }
    } else {
        $_SESSION['error_message'] = "Invalid category data.";
    }

    // Redirect to avoid resubmission
    header('Location: manage_categories.php');
    exit();
}

// Handle activate/deactivate category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $id = intval($_POST['id']);
    
    if ($id > 0) {
        // Get current status
        $status_query = "SELECT status FROM categories WHERE id = $id";
        $status_result = mysqli_query($conn, $status_query);
        $current_status = mysqli_fetch_assoc($status_result)['status'];
        
        // Toggle status (1 becomes 0, 0 becomes 1)
        $new_status = $current_status ? 0 : 1;
        
        $update_query = "UPDATE categories SET status = $new_status WHERE id = $id";
        if (!mysqli_query($conn, $update_query)) {
            $_SESSION['error_message'] = "Error updating category status: " . mysqli_error($conn);
        } else {
            $status_text = $new_status ? 'activated' : 'deactivated';
            $_SESSION['success_message'] = "Category $status_text successfully!";
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
                    <!-- Update Button -->
                    <a href="update_category.php?id=<?php echo $row['id']; ?>" class="update-btn">Update</a>
                    
                    <!-- Toggle Status Button -->
                    <form action="manage_categories.php" method="POST" style="display:inline; margin-left: 5px;">
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="<?php echo $row['status'] ? 'deactivate-btn' : 'activate-btn'; ?>" 
                                onclick="return confirm('Are you sure you want to <?php echo $row['status'] ? 'deactivate' : 'activate'; ?> this category?');">
                            <?php echo $row['status'] ? 'Deactivate' : 'Activate'; ?>
                        </button>
                    </form>
                    
                    <!-- Delete Button -->
                    <form action="manage_categories.php" method="POST" style="display:inline; margin-left: 5px;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this category?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    </div>
    <a href="add_category.php" class="add-btn">Add New Category</a>

    <style>
        .add-btn {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s;
            display: inline-block;
            margin-top: 20px;
        }

        .add-btn:hover {
            background-color: #0056b3;
        }
    </style>
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

.update-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 7px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.update-btn:hover {
    background-color: #0056b3;
}

.activate-btn {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 7px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.activate-btn:hover {
    background-color: #1e7e34;
}

.deactivate-btn {
    background-color: #ffc107;
    color: #333;
    border: none;
    padding: 5px 10px;
    border-radius: 7px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.deactivate-btn:hover {
    background-color:rgb(197, 148, 0);
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
</style>
</body>
</html>
