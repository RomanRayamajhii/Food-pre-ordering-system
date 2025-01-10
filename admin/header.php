<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }

        .header {
            background-color: rgb(72, 69, 69);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
        }
        h1 a{
            text-decoration: none;
            color: white;
        }

        nav {
            margin-top: 20px;
        }
     nav a {
        text-decoration: none;
        color: white;
        padding: 10px 20px;
        margin: 0 10px;
        width: 20%;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
      nav a:hover {
        background-color: #121211;
    }


    @media screen and (max-width: 600px) {
        nav {

           
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        nav a{
            
            margin-top: 5px;
        }
            
           
         
    }
    </style>
</head>
<body>
    <div class="header">
    <h1>
        <a href="./dashboard.php">Admin Panel</a></h1>
    <nav >
        <a href="./manage_users.php">User</a>
        <a href="./manage_orders.php">Order</a>
        <a href="./manage_menu.php">Menu</a>
        <a href="./manage_categories.php">Category</a>
        <a href="./manage_comment.php">Comments</a>
        <a href="./logout.php">Logout</a>
    </nav>
 
    </div>
    
</body>
</html>