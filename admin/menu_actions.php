<?php
session_start();
include './header.php';
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'edit':
                if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['category_id']) || empty($_POST['price'])) {
                    $_SESSION['error_message'] = "Please fill in all required fields";
                    header('Location: manage_menu.php');
                    exit();
                }

                $id =  $_POST['id'];
                $name =  $_POST['name'];
                $category_id =  $_POST['category_id'];
                $price =  $_POST['price'];
                $description = $_POST['description'];
                $status = isset($_POST['status']) ? 1 : 0;

                // Handle image upload for edit
                $image_update = "";
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['image']['name'];
                    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($file_ext, $allowed)) {
                        $image_name = time() . '_' . $filename;
                        $upload_path = '../uploads/menu/';
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path . $image_name)) {
                            // Delete old image
                            $old_image_query = "SELECT image FROM menu_items WHERE id = '$id'";
                            $old_image_result = mysqli_query($conn, $old_image_query);
                            $old_image = mysqli_fetch_assoc($old_image_result)['image'];
                            
                            if ($old_image && file_exists($upload_path . $old_image)) {
                                unlink($upload_path . $old_image);
                            }
                            
                            $image_update = ", image = '$image_name'";
                        }
                    }
                }

                $query = "UPDATE menu_items SET 
                         name = '$name', 
                         category_id = '$category_id', 
                         price = '$price', 
                         description = '$description',
                         status = '$status'
                         $image_update
                         WHERE id = '$id'";
                
                if (mysqli_query($conn, $query)) {
                    $_SESSION['success_message'] = "Menu item updated successfully!";
                } else {
                    $_SESSION['error_message'] = "Error updating menu item: " . mysqli_error($conn);
                }
                break;

            case 'delete':
                if (empty($_POST['id'])) {
                    $_SESSION['error_message'] = "Invalid item ID";
                    header('Location: manage_menu.php');
                    exit();
                }

                $id = mysqli_real_escape_string($conn, $_POST['id']);

                // Get image filename before deleting
                $image_query = "SELECT image FROM menu_items WHERE id = '$id'";
                $image_result = mysqli_query($conn, $image_query);
                $image = mysqli_fetch_assoc($image_result)['image'];

                // Delete the record
                $query = "DELETE FROM menu_items WHERE id = '$id'";
                
                if (mysqli_query($conn, $query)) {
                    // Delete image file if it exists
                    if ($image && file_exists('../uploads/menu/' . $image)) {
                        unlink('../uploads/menu/' . $image);
                    }
                    $_SESSION['success_message'] = "Menu item deleted successfully!";
                } else {
                    $_SESSION['error_message'] = "Error deleting menu item: " . mysqli_error($conn);
                }
                break;
        }
    }
}

header('Location: manage_menu.php');
exit();
?>
