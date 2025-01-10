<?php
session_start();

    $conn = new mysqli('localhost', 'root', '', 'food_ordering'); // Database

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Add comment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
        $userId = 1; // Hardcoded user ID
        $comment = $conn->real_escape_string($_POST['comment']);
        $conn->query("INSERT INTO comments (user_id, comment, created_at) VALUES ('$userId', '$comment', NOW())");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    // Delete comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $commentId = intval($_POST['comment_id']);
    $userId = 1;
        if ($userId==1) {
            $conn->query("DELETE FROM comments WHERE id = $commentId");
        }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
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
    main {
    margin-bottom: 20px;
    font-family: 'Arial', sans-serif;
}

.show-comments {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    width: 450px;
    margin: 15px auto;
    display: flex;
    background-color: #f9f9f9;
    padding: 15px;
    transition: transform 0.3s ease;
}

.show-comments:hover {
    transform: translateY(-5px);
}

.icon {
    background-color: #4e73df;
    height: 50px;
    width: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.icon i {
    font-size: 24px;
    color: white;
}

.comment-section {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.comment-section p {
    margin: 0;
    color: #333;
}

.comment-section .top-div {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comment-section .top-div p {
    font-size: 14px;
    font-weight: bold;
    color: #333;
}

.comment-section button {
    height: 28px;
    font-size: 14px;
    background-color: #e74a3b;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    padding: 0 12px;
    transition: background-color 0.3s ease;
}

.comment-section button:hover {
    background-color: #c0392b;
}

.comment-section .low-div p {
    font-size: 16px;
    color: #555;
    margin-top: 8px;
    font-family: 'Arial', sans-serif;
}

.enter-comment {
    width: 450px;
    margin: 20px auto;
    text-align: center;
}

.type-comment {
    width: 100%;
    height: 60px;
    padding: 10px;
    font-size: 16px;
    border: 2px solid #ccc;
    border-radius: 8px;
    resize: none;
    outline: none;
    margin: auto;
}

.type-comment:focus {
    border-color: #4e73df;
}

.post-button {
    margin-top: 10px;
    height: 40px;
    width: 100%;
    background-color: #4e73df;
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.post-button:hover {
    background-color: #2e5cb8;
}

</style>
</head>
<body>
    <a href="dashboard.php">Back to home</a>
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
                    <button type='submit' name='delete_comment' class='delete-button' '>Delete</button>
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

