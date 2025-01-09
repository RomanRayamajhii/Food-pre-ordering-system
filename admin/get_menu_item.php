<?php
session_start();
include 'includes/config.php';

if (isset($_GET['id'])) {
    $id =  $_GET['id'];
    
    $query = "SELECT * FROM menu_items WHERE id = '$id'";
    $result = mysqli_query($conn, $query);

    if ($item = mysqli_fetch_assoc($result)) {
        echo json_encode($item);
    } else {
        echo json_encode(['error' => 'Item not found']);
    }
}
?> 