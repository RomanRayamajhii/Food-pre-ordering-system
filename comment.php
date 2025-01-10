<?php
session_start();
// Assuming user_id is set in session after login
if($_SESSION['user_id']){
    $conn = new mysqli('localhost', 'root', '', 'food_ordering'); // Replace with your database name

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Add comment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
        $userId = $_SESSION['user_id']; 
        $comment = $conn->real_escape_string($_POST['comment']);
        $conn->query("INSERT INTO comments (user_id, comment, created_at) VALUES ('$userId', '$comment', NOW())");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    
    
    // Delete comment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
       
        if (!isset($_SESSION['user_id'])) {
            die("Unauthorized access. You must be logged in to delete comments.");
        }
    
        $commentId = intval($_POST['comment_id']);
        $userId = $_SESSION['user_id'];
    
        // Check if the logged-in user owns the comment
        $result = $conn->query("SELECT user_id FROM comments WHERE id = $commentId");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['user_id'] == $userId) {
              
              $conn->query("DELETE FROM comments WHERE id = $commentId");
            } else {
                die("Unauthorized action. You can only delete your own comments.");
            }
        } else {
            die("Comment not found.");
        }
    
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Comment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<style>
  *{
            margin: 0;
            padding: 0;
        }
        .back{
            background-color: rgb(113, 113, 228);
            border: none; 
            height: 30px;
            padding-left: 10px;
            padding-right: 10px;
            cursor: pointer;
            color: white;
            margin-top: 10px;
            margin-left: 10px;
            font-family: 'roboto';
            border-radius: 5px;
            text-decoration: underline;
        }
        .show-comments{
            box-shadow: 2px 2px 8px black;
            border-radius: 5px;
            width: 400px;
            margin: auto;
            display: flex;
            min-height: 100px;
            margin-top: 10px;
        }
        .show-comments .icon{
            background-color: #f0f0f0;
            height: 40px;
            width: 40px;
            border-radius: 100px;
            margin-left: 6px;
            margin-top: 8px;
            cursor: pointer;
        }
        .show-comments .icon i{
            font-size: 25px;
            padding-left: 10px;
            padding-top: 7px;
            color: rgb(128, 128, 236);
        }
        .show-comments .username{
            font-family: 'poppins';
            font-size: 14px;
            margin-top: 10px;
            margin-left: 8px;
            font-weight: 600;
            color: rgb(102, 100, 100);
        }
        .show-comments span{
            margin-left: 8px;
        }
        .show-comments .comment{
            margin-left: 8px;
            margin-top: 5px;
            font-family: 'roboto';
            font-size: 14px;
            font-weight: 400;
            color: rgb(71, 70, 70);
        }
        .show-comments .delete-button{
            background-color: red;
            border-radius: 10px;
            letter-spacing: 1px;
            border: none;
            margin-top: 8px;
            margin-right: 10px;
            height: 30px;
            width: 60px;
            color: rgb(255, 253, 253);
            font-family: 'poppins';
            font-weight: 600;
            cursor: pointer;
        }
        .show-comments .delete-button:hover{
            background-color: rgb(248, 87, 87);
        }
        .enter-comment{
            width: 400px;
            margin: auto;
            margin-top: 20px;
        }
        .enter-comment form{
            display: flex;
            width: 100%;
        }
        .enter-comment form textarea{
            resize: none;
            height: 60px;
            width: 315px;
            font-size: 14px;
            font-family: 'poppins';
            padding-left: 5px;
            border-radius: 4px;
        }
       .enter-comment form .post-button{
        background-color: rgb(128, 128, 236);
        border: none;
        border-radius: 10px;

        margin-left: 15px;
        height: 60px;
        width: 80px;
        font-size: 16px;
        color: white;
        cursor: pointer;
    }

</style>
</head>
<body>
    <a href="index.php"  ><button class="back" style="text-decoration:none; font-size:14px"> <b> &lt; &lt; </b>Back to home</button></a>
    <main class="main">
        <?php
        $result = $conn->query("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id order by created_at desc");

        while ($row = $result->fetch_assoc()) {
            $formattedTime = date('d M Y, H:i', strtotime($row['created_at']));
            echo "
                <div class='show-comments'>
                    <div class='icon'><i class='fa-solid fa-user'></i></div>
                    <div class='comment-section'>
                        <p class='username'>{$row['username']}</p>
                        <span style='font-size: 12px; color: grey;'>{$formattedTime}</span>
                        <p class='comment'>{$row['comment']}</p>
                    </div>
                    <form method='post' style='margin-left: auto;'>
                        <input type='hidden' name='comment_id' value='{$row['id']}'>
                        <button type='submit' name='delete_comment' class='delete-button' '>Delete</button>
                    </form>
            </div>
            ";
        }
        ?>
    </main>

    <div class="enter-comment">
            <form method="post">
                <textarea class="type-comment" name="comment" placeholder="Type a comment" required></textarea>
                <button type="submit" name="add_comment" class="post-button">Post</button>
            </form>
    </div>

</body>
</html>