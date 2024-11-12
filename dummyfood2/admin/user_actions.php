<?php
session_start();
include 'includes/config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Handle GET actions (block/unblock/delete)
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    switch($_GET['action']) {
        case 'block':
            // Add error handling
            try {
                $query = "UPDATE users SET status = 'blocked' WHERE id = '$id'";
                if(mysqli_query($conn, $query)) {
                    header("Location: manage_users.php?msg=blocked");
                } else {
                    header("Location: manage_users.php?msg=error&error=" . mysqli_error($conn));
                }
            } catch (Exception $e) {
                header("Location: manage_users.php?msg=error&error=" . $e->getMessage());
            }
            break;
            
        case 'unblock':
            try {
                $query = "UPDATE users SET status = 'active' WHERE id = '$id'";
                if(mysqli_query($conn, $query)) {
                    header("Location: manage_users.php?msg=unblocked");
                } else {
                    header("Location: manage_users.php?msg=error&error=" . mysqli_error($conn));
                }
            } catch (Exception $e) {
                header("Location: manage_users.php?msg=error&error=" . $e->getMessage());
            }
            break;
            
        case 'delete':
            try {
                $query = "DELETE FROM users WHERE id = '$id'";
                if(mysqli_query($conn, $query)) {
                    header("Location: manage_users.php?msg=deleted");
                } else {
                    header("Location: manage_users.php?msg=error&error=" . mysqli_error($conn));
                }
            } catch (Exception $e) {
                header("Location: manage_users.php?msg=error&error=" . $e->getMessage());
            }
            break;
            
        default:
            header("Location: manage_users.php");
    }
    exit();
}

// Handle POST actions (add/update user)
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add User
    if(isset($_POST['add_user'])) {
        try {
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            
            // Check if email already exists
            $check_email = "SELECT id FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $check_email);
            
            if(mysqli_num_rows($result) > 0) {
                header("Location: manage_users.php?msg=email_exists");
                exit();
            }
            
            $query = "INSERT INTO users (username, email, phone, status) 
                      VALUES ('$username', '$email', '$phone', 'active')";
            
            if(mysqli_query($conn, $query)) {
                header("Location: manage_users.php?msg=added");
            } else {
                header("Location: manage_users.php?msg=error&error=" . mysqli_error($conn));
            }
        } catch (Exception $e) {
            header("Location: manage_users.php?msg=error&error=" . $e->getMessage());
        }
        exit();
    }
    
    // Update User
    if(isset($_POST['update_user'])) {
        try {
            $id = mysqli_real_escape_string($conn, $_POST['user_id']);
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            
            // Check if email exists for other users
            $check_email = "SELECT id FROM users WHERE email = '$email' AND id != '$id'";
            $result = mysqli_query($conn, $check_email);
            
            if(mysqli_num_rows($result) > 0) {
                header("Location: manage_users.php?msg=email_exists");
                exit();
            }
            
            $query = "UPDATE users SET 
                      username = '$username',
                      email = '$email',
                      phone = '$phone'
                      WHERE id = '$id'";
            
            if(mysqli_query($conn, $query)) {
                header("Location: manage_users.php?msg=updated");
            } else {
                header("Location: manage_users.php?msg=error&error=" . mysqli_error($conn));
            }
        } catch (Exception $e) {
            header("Location: manage_users.php?msg=error&error=" . $e->getMessage());
        }
        exit();
    }
}

// If we get here, redirect to manage users page
header("Location: manage_users.php");
exit();
?>