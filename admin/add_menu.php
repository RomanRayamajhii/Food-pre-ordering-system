<?php
session_start();
include '../config/db.php'; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $status = isset($_POST['status']) ? 1 : 0; // Default to active if checked

    // Validate inputs
    if (empty($name) || empty($description) || $price <= 0 || $category_id <= 0) {
        $_SESSION['error_message'] = "Please fill in all required fields correctly.";
        header('Location: manage_menu.php');
        exit();
    }

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uniqid() . '_' . basename($_FILES['image']['name']); // Unique filename
        $target_dir = "../uploads/menu/";
        $target_file = $target_dir . $image;
        $uploadOk = 1;

        // Check if image file is a valid image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            $_SESSION['error_message'] = "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (e.g., limit to 2MB)
        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $_SESSION['error_message'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        $allowed=['jpg', 'png', 'jpeg', 'gif'];
        // Allow certain file formats
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, $allowed)) {
            $_SESSION['error_message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $_SESSION['error_message'] = "Sorry, your file was not uploaded.";
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Escape inputs to prevent SQL injection (not foolproof)
                $name = mysqli_real_escape_string($conn, $nam);
                $description = mysqli_real_escape_string($conn, $description);
                $price = mysqli_real_escape_string($conn, $price);
                $category_id = mysqli_real_escape_string($conn, $category_id);
                $image = mysqli_real_escape_string($conn, $image);
                $status = mysqli_real_escape_string($conn, $status);

                // Prepare the SQL query
                $sql = "INSERT INTO menu_items (name, description, price, category_id, image, status) 
                        VALUES ('$name', '$description', '$price', '$category_id', '$image', '$status')";

                // Execute the query
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = "Menu item added successfully!";
                } else {
                    $_SESSION['error_message'] = "Error adding menu item: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $_SESSION['error_message'] = "No image file uploaded or an error occurred during upload.";
    }

    // goto manage menu page
    header('Location: manage_menu.php');
    exit();
}
?>