<?php
session_start();
include 'includes/config.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid category ID.";
    header('Location: manage_categories.php');
    exit();
}

$category_id = intval($_GET['id']);

// Fetch category data
$category_sql = "SELECT * FROM categories WHERE id = $category_id";
$category_result = mysqli_query($conn, $category_sql);

if (!$category_result || mysqli_num_rows($category_result) == 0) {
    $_SESSION['error_message'] = "Category not found.";
    header('Location: manage_categories.php');
    exit();
}

$category = mysqli_fetch_assoc($category_result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = isset($_POST['status']) ? 1 : 0;

    // Validate inputs
    if (empty($name) || empty($description)) {
        $_SESSION['error_message'] = "Please fill in all required fields.";
        header('Location: update_category.php?id=' . $category_id);
        exit();
    }

    // Check if category name already exists (excluding current category)
    $check_sql = "SELECT id FROM categories WHERE name = '" . mysqli_real_escape_string($conn, $name) . "' AND id != $category_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error_message'] = "Category with this name already exists.";
        header('Location: update_category.php?id=' . $category_id);
        exit();
    }

    // Update database
    $name = mysqli_real_escape_string($conn, $name);
    $description = mysqli_real_escape_string($conn, $description);
    $status = intval($status);

    $sql = "UPDATE categories SET name = '$name', description = '$description', status = '$status' WHERE id = $category_id";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Category updated successfully!";
        header('Location: manage_categories.php');
        exit();
    } else {
        $_SESSION['error_message'] = "Database error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Update Category</h2>

        <!-- Display success message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Display error message -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php 
                echo $_SESSION['error_message']; 
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <form action="update_category.php?id=<?php echo $category_id; ?>" method="POST">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($category['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="status">
                    <input type="checkbox" id="status" name="status" <?php echo $category['status'] ? 'checked' : ''; ?>>
                    Active
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Category</button>
                <a href="manage_categories.php" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: #007bff;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="checkbox"] {
            margin-right: 8px;
            transform: scale(1.2);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s;
            display: inline-block;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</body>
</html>