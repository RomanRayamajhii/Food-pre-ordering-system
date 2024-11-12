<?php
session_start();
include 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Add at the top of menu_actions.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Get form data
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
                $price = mysqli_real_escape_string($conn, $_POST['price']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                
                // Initialize image name
                $image_name = '';
                
                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['image']['name'];
                    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($file_ext, $allowed)) {
                        // Create clean filename
                        $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
                        $image_name = $clean_name . '_' . time() . '.' . $file_ext;
                        
                        // Define upload path
                        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/dummyfood2/uploads/menu/';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        $upload_path = $upload_dir . $image_name;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            chmod($upload_path, 0644); // Set proper file permissions
                        } else {
                            $image_name = '';
                            error_log("Failed to upload image: " . error_get_last()['message']);
                        }
                    }
                }

                // Insert into database
                $query = "INSERT INTO menu_items (category_id, name, description, price, image, status) 
                          VALUES (?, ?, ?, ?, ?, 1)";
                
                try {
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "issds", $category_id, $name, $description, $price, $image_name);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['success_message'] = "Menu item added successfully!";
                        if (!empty($image_name)) {
                            $_SESSION['success_message'] .= " Image uploaded as: " . $image_name;
                        }
                    } else {
                        throw new Exception(mysqli_error($conn));
                    }
                    
                } catch (Exception $e) {
                    $_SESSION['error_message'] = "Error: " . $e->getMessage();
                    error_log("Database error: " . $e->getMessage());
                }
                
                mysqli_stmt_close($stmt);
                header('Location: manage_menu.php');
                exit();
                

            case 'edit':
                if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['category_id']) || empty($_POST['price'])) {
                    $_SESSION['error_message'] = "Please fill in all required fields";
                    header('Location: manage_menu.php');
                    exit();
                }

                $id = mysqli_real_escape_string($conn, $_POST['id']);
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
                $price = mysqli_real_escape_string($conn, $_POST['price']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
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
                            $old_image_query = "SELECT image FROM menu_items WHERE id = ?";
                            $stmt = mysqli_prepare($conn, $old_image_query);
                            mysqli_stmt_bind_param($stmt, "i", $id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $old_image = mysqli_fetch_assoc($result)['image'];
                            
                            if ($old_image && file_exists($upload_path . $old_image)) {
                                unlink($upload_path . $old_image);
                            }
                            
                            $image_update = ", image = '$image_name'";
                        }
                    }
                }

                $query = "UPDATE menu_items SET 
                         name = ?, 
                         category_id = ?, 
                         price = ?, 
                         description = ?,
                         status = ?
                         $image_update
                         WHERE id = ?";
                
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sidsii", $name, $category_id, $price, $description, $status, $id);
                
                if (mysqli_stmt_execute($stmt)) {
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
                $image_query = "SELECT image FROM menu_items WHERE id = ?";
                $stmt = mysqli_prepare($conn, $image_query);
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $image = mysqli_fetch_assoc($result)['image'];

                // Delete the record
                $query = "DELETE FROM menu_items WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $id);
                
                if (mysqli_stmt_execute($stmt)) {
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