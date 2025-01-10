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
        session_start();
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .main {
            width: 500px;
            margin-top: 20px;
        }

        .show-comments {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 100%;
            margin: 15px 0;
            display: flex;
            padding: 15px;
            background-color: #fff;
            transition: transform 0.3s ease;
        }

        .show-comments:hover {
            transform: translateY(-5px);
        }

        .icon {
            background-color: #5e72e4;
            height: 50px;
            width: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }

        .comment-section {
            margin-left: 15px;
            flex-grow: 1;
        }

        .comment-section p {
            margin: 5px 0;
            font-size: 14px;
        }

        .comment-section p strong {
            font-weight: 600;
            color: #5e72e4;
        }

        .comment-section p span {
            font-size: 12px;
            color: #9e9e9e;
        }

        .delete-button {
            background-color: #e74a3b;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete-button:hover {
            background-color: #c0392b;
        }

        .enter-comment {
            width: 100%;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            border-radius: 10px;
        }

        .type-comment {
            width: 100%;
            height: 80px;
            border-radius: 10px;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ddd;
            resize: none;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .type-comment:focus {
            border-color: #5e72e4;
        }

        .post-button {
            margin-top: 10px;
            background-color: #5e72e4;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            font-size: 16px;
        }

        .post-button:hover {
            background-color: #4e62d1;
        }

    </style>
</head>
<body>
    <main class="main">
        <?php
        $result = $conn->query("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id order by created_at desc");

        while ($row = $result->fetch_assoc()) {
            $formattedTime = date('d M Y, H:i', strtotime($row['created_at']));
            echo "
            <div class='show-comments'>
                <div class='icon'><i class='fa-regular fa-user'></i></div>
                <div class='comment-section'>
                    <p><strong>{$row['username']}:</strong> <span style='font-size: 12px; color: grey;'>{$formattedTime}</span></p>
                    <p>{$row['comment']}</p>
                </div>
                <form method='post' style='margin-left: auto;'>
                    <input type='hidden' name='comment_id' value='{$row['id']}'>
                    <button type='submit' name='delete_comment' class='delete-button'>Delete</button>
                </form>
            </div>";
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

