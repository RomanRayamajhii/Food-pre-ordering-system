<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "SELECT * FROM menu_items WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($item = mysqli_fetch_assoc($result)) {
        echo json_encode($item);
    } else {
        echo json_encode(['error' => 'Item not found']);
    }
}
?> 