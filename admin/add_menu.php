<?php
session_start();
include '../config/db.php'; // Ensure the path to db.php is correct

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $status = isset($_POST['status']) ? 1 : 0; // Default to active if checked

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $target_dir = "../uploads/menu/";
        $target_file = $target_dir . basename($image);
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

        // Allow certain file formats
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $_SESSION['error_message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $_SESSION['error_message'] = "Sorry, your file was not uploaded.";
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Prepare the SQL statement
                $sql = "INSERT INTO menu_items (name, description, price, category_id, image, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("ssissi", $name, $description, $price, $category_id, $image, $status);

                    // Execute the statement
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = "Menu item added successfully!";
                    } else {
                        $_SESSION['error_message'] = "Error adding menu item: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    $_SESSION['error_message'] = "Error preparing statement: " . $conn->error;
                }
            } else {
                $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $_SESSION['error_message'] = "No image file uploaded or an error occurred during upload.";
    }

    // Redirect to manage menu page
    header('Location: manage_menus.php');
    exit();
}
?>
