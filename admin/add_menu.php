<?php
session_start();
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $status = isset($_POST['status']) ? 1 : 0;

    // Validate inputs
    if (empty($name) || empty($description) || $price <= 0 || $category_id <= 0) {
        $_SESSION['error_message'] = "Please fill in all required fields correctly.";
        header('Location: manage_menu.php');
        exit();
    }

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_dir = __DIR__ . "/../Uploads/menu/";
        $target_file = $target_dir . $image;

        // Check if directory exists, create if needed
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Validate image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            $_SESSION['error_message'] = "File is not a valid image.";
            header('Location: manage_menu.php');
            exit();
        }

        // Check file size (limit to 5MB)
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = "File size too large. Maximum allowed: 5MB.";
            header('Location: manage_menu.php');
            exit();
        }

        // Allow only certain file formats
        $allowed_types = ['jpg', 'jpeg', 'png','webp'];
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['error_message'] = "Only JPG, JPEG, PNG & WEBP files are allowed.";
            header('Location: manage_menu.php');
            exit();
        }

        // Upload file
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $_SESSION['error_message'] = "Failed to upload image file.";
            header('Location: manage_menu.php');
            exit();
        }

        // Save to database
        $name = mysqli_real_escape_string($conn, $name);
        $description = mysqli_real_escape_string($conn, $description);
        $price = floatval($price);
        $category_id = intval($category_id);
        $image = mysqli_real_escape_string($conn, $image);
        $status = intval($status);

        $sql = "INSERT INTO menu_items (name, description, price, category_id, image, status) 
                VALUES ('$name', '$description', '$price', '$category_id', '$image', '$status')";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_message'] = "Menu item added successfully!";
        } else {
            $_SESSION['error_message'] = "Database error: " . mysqli_error($conn);
            // Clean up uploaded file if database insertion failed
            if (file_exists($target_file)) {
                unlink($target_file);
            }
        }
    } else {
        $_SESSION['error_message'] = "Please select an image file.";
        header('Location: manage_menu.php');
        exit();
    }

    header('Location: manage_menu.php');
    exit();
}
?>
