<?php
session_start();
include 'includes/config.php';

// delete ,block,unblock user
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id =  $_GET['id'];
    
    switch($_GET['action']) {
        case 'block':
            // Add error handling
           
                $query = "UPDATE users SET status = 'blocked' WHERE id = '$id'";
                if(mysqli_query($conn, $query)) {
                    header("Location: manage_users.php?msg=blocked");
                } else {
                    header("Location: manage_users.php?msg=error&error=" . mysqli_error($conn));
                }
          
            break;
            
        case 'unblock':
           
                $query = "UPDATE users SET status = 'active' WHERE id = '$id'";
                if(mysqli_query($conn, $query)) {
                    header("Location: manage_users.php?msg=unblocked");
                } else {
                    header("Location: manage_users.php?msg=error&error=" . mysqli_error($conn));
                }
           
            break;
            
        case 'delete':
           
                $query = "DELETE FROM users WHERE id = '$id'";
                if(mysqli_query($conn, $query)) {
                    header("Location: manage_users.php?msg=deleted");
                } else {
                    header("Location: manage_users.php?msg=error&error=" . mysqli_error($conn));
                }

            break;
            
        default:
            header("Location: manage_users.php");
    }
    exit();
}

// go to manage_users.php
header("Location: manage_users.php");
exit();
?>