<?php
session_start();
include 'config/db.php';

// Assuming user_id is set in session after login
if(isset($_SESSION['user_id'])){
    
    // Add comment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
        $userId = $_SESSION['user_id']; 
        $comment = $conn->real_escape_string($_POST['comment']);
        $rating = intval($_POST['rating'] ?? 5);
        
        // Ensure the comments table has a 'rating' column
        $conn->query("INSERT INTO comments (user_id, comment, rating, created_at) VALUES ('$userId', '$comment', '$rating', NOW())");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    //Edit comment
     if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'])) {
       
        if (!isset($_SESSION['user_id'])) {
            die("Unauthorized access. You must be logged in to edit comments.");
        }
    
        $commentId = intval($_POST['comment_id']);
        $userId = $_SESSION['user_id'];
        $comment = $conn->real_escape_string($_POST['comment']);
        $rating = intval($_POST['rating'] ?? 5);
    
        // Check if the logged-in user owns the comment
        $result = $conn->query("SELECT user_id FROM comments WHERE id = $commentId");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['user_id'] == $userId) {
              
              $conn->query("UPDATE comments SET comment = '$comment', rating = '$rating' WHERE id = $commentId");
            } else {
                die("Unauthorized action. You can only edit your own comments.");
            }
        } else {
            die("Comment not found.");
        }
    
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
    <title>Restaurant Reviews</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
    body {
        background-color: #f5f5f5;
        padding-bottom: 50px;
    }
    .header-banner {
        background: #000;
        color: #fff;
        text-align: center;
        padding: 40px 0;
        margin-bottom: 30px;
    }
    .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #000;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px;
        }
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .comment-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            display: flex;
            gap: 15px;
        }
    .user-avatar {
            background-color: #f0f0f0;
            height: 50px;
            width: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    .user-avatar i {
            font-size: 20px;
            color: #000;
        }
    .comment-content { flex-grow: 1; }
    .username {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 2px;
        }
    .timestamp {
        font-size: 12px;
        color: #888;
        display: block;
        margin-bottom: 5px;
    }
    .comment-text {
            color: #444;
            font-size: 14px;
            line-height: 1.5;
            margin-top: 8px;
        }
    .delete-btn {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
        }
    .edit-btn {
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
            margin-right: 5px;
        }
    .input-section {
        background: #fff;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        margin-top: 40px;
    }
    .input-section h3 { margin-bottom: 15px; }
    textarea {
        width: 100%;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        resize: vertical;
        min-height: 100px;
        margin-bottom: 15px;
    }
    .post-btn {
        background: #000;
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s;
    }
    .post-btn:hover { background: #333; }
    .star-rating {
        color: #ffc107;
    }
    .star-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 15px;
    }
    .star-input input {
        display: none;
    }
    .star-input label {
        cursor: pointer;
        font-size: 24px;
        color: #ccc;
        transition: color 0.2s;
    }
    .star-input label:before {
        content: '\f005';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
    }
    .star-input input:checked ~ label,
    .star-input label:hover,
    .star-input label:hover ~ label {
        color: #ffc107;
    }
</style>
</head>
<body>
    
    <div class="header-banner">
        <h1>Customer Reviews</h1>
        <p>What our guests say about Hotel Everest</p>
    </div>
 <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Home</a>
    <div class="container">
        <?php
        $result = $conn->query("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id ORDER BY created_at DESC");

        while ($row = $result->fetch_assoc()) {
            $formattedTime = date('d M Y, H:i', strtotime($row['created_at']));
            $rating = isset($row['rating']) ? intval($row['rating']) : 0;
            ?>
            <div class="comment-card">
                <div class="user-avatar"><i class="fa-solid fa-user"></i></div>
                <div class="comment-content">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <p class="username"><?php echo htmlspecialchars($row['username']); ?></p>
                            <span class="timestamp"><?php echo $formattedTime; ?></span>
                            <div class="star-rating">
                                <?php for($i=1; $i<=5; $i++) echo ($i <= $rating) ? "<i class='fa-solid fa-star'></i>" : "<i class='fa-regular fa-star'></i>"; ?>
                            </div>
                        </div>
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                        <div style="display: flex;">
                            <button type="button" class="edit-btn" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['comment']); ?>', <?php echo $rating; ?>)">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                <input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_comment" class="delete-btn">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <p class="comment-text"><?php echo nl2br(htmlspecialchars($row['comment'])); ?></p>
                </div>
            </div>
        <?php } ?>

        <?php if(isset($_SESSION['user_id'])): ?>
        <div class="input-section">
            <h3>Leave a Review</h3>
            <form method="post">
                <label>Rating:</label>
                <div class="star-input">
                    <?php for($i=5; $i>=1; $i--): ?>
                    <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i==5?'required':''; ?>><label for="star<?php echo $i; ?>"></label>
                    <?php endfor; ?>
                </div>
                <textarea name="comment" placeholder="Share your experience..." required></textarea>
                <button type="submit" name="add_comment" class="post-btn">Post Review</button>
            </form>
        </div>
        <?php else: ?>
        <div class="input-section" style="text-align: center;">
            <p>Please <a href="login.php" style="color: #000; font-weight: 600;">login</a> to leave a review.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1001;">
        <div style="background:#fff; width:90%; max-width:500px; margin:100px auto; padding:20px; border-radius:15px;">
            <h3>Edit Review</h3>
            <form method="post">
                <input type="hidden" name="comment_id" id="edit_comment_id">
                <label>Rating:</label>
                <div class="star-input">
                    <?php for($i=5; $i>=1; $i--): ?>
                    <input type="radio" name="rating" id="edit_star<?php echo $i; ?>" value="<?php echo $i; ?>"><label for="edit_star<?php echo $i; ?>"></label>
                    <?php endfor; ?>
                </div>
                <textarea name="comment" id="edit_comment_text" required></textarea>
                <div style="display:flex; gap:10px; justify-content: flex-end;">
                    <button type="button" onclick="closeEditModal()" style="padding:10px 20px; border-radius:5px; border:1px solid #ddd; cursor:pointer;">Cancel</button>
                    <button type="submit" name="edit_comment" class="post-btn" style="padding:10px 20px;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, text, rating) {
            document.getElementById('edit_comment_id').value = id;
            document.getElementById('edit_comment_text').value = text;
            const radio = document.getElementById('edit_star' + rating);
            if(radio) radio.checked = true;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>