<?php
session_start();
 include "./header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            
        }

        .user, .order, .menu, .category,.comment {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 20%;
            margin-bottom: 20px;
            text-align: center;
        }

        .user h2, .order h2, .menu h2, .category h2,.comment h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 10px;
        }

        .user p, .order p, .menu p, .category p ,.comment p{
            font-size: 16px;
            color: #666;
            margin-bottom: 15px;
        }

        .user a, .order a, .menu a, .category a, .comment a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

       .container div a:hover {
            background-color: #0056b3;
        }
        @media screen and (max-width: 600px) {
        .container {
            display: flex;
           
            flex-direction: column;
            align-items: center;
         
        }
        .user, .order, .menu, .category, .comment {
            width:90%;
        }
    }
    </style>
</head>
<body>
    <div class="container">
       <div class="user">
        <h2>Manage User</h2>
        <p>View and manage user accounts</p>
        <a href="./manage_users.php">Go to User</a>
       </div>

       <div class="order">
        <h2>Manage Order</h2>
        <p>View and manage customer orders</p>
        <a href="./manage_orders.php">Go to Order</a>
       </div>

       <div class="menu">
        <h2>Manage Menu</h2>
        <p>View and manage menu items</p>
        <a href="./manage_menu.php">Go to Order</a>
       </div>

       <div class="category">
        <h2>Manage Categories</h2>
        <p>Add, edit, or remove categories</p>
        <a href="./manage_categories.php">Go to Categories</a>
       </div>
       <div class="comment">
        <h2>Manage Comments</h2>
        <p>Add,Remove comments</p>
        <a href="./manage_comment.php">Go to Comment</a>
       </div>
       
    </div>
</body>
</html>