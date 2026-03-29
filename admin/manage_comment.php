<?php
session_start();
include "./header.php";
include "./includes/config.php";

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    // Add comment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
        $userId = $_SESSION['admin_user_id'] ?? ($_SESSION['user_id'] ?? 1);
        $comment = $conn->real_escape_string($_POST['comment']);
        $rating = 5;
        
        if (empty(trim($comment))) {
            $error_message = "Comment cannot be empty";
        } else {
            $sql = "INSERT INTO comments (user_id, comment, rating, created_at) VALUES ('$userId', '$comment', '$rating', NOW())";
            if ($conn->query($sql)) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $error_message = "Error adding comment: " . $conn->error;
            }
        }
    }
    
    // Edit comment - admin can edit any comment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'])) {
        $commentId = intval($_POST['comment_id']);
        $comment = $conn->real_escape_string($_POST['comment']);
        $rating = intval($_POST['rating'] ?? 5);
        
        $conn->query("UPDATE comments SET comment = '$comment', rating = '$rating' WHERE id = $commentId");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Delete comment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
        $commentId = intval($_POST['comment_id']);
        $conn->query("DELETE FROM comments WHERE id = $commentId");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Comment Management</title>
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
    
    <div class="container">
        <br>
        <h2>Manage Comments</h2> <br>
        <?php
        $result = $conn->query("SELECT comments.*, users.username FROM comments LEFT JOIN users ON comments.user_id = users.id ORDER BY created_at DESC");

        while ($row = $result->fetch_assoc()) {
            $formattedTime = date('d M Y, H:i', strtotime($row['created_at']));
            $rating = isset($row['rating']) ? intval($row['rating']) : 0;
        ?>
            <div class="comment-card">
                <div class="user-avatar"><i class="fa-solid fa-user"></i></div>
                <div class="comment-content">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <p class="username"><?php echo htmlspecialchars($row['username'] ?? 'Admin'); ?></p>
                            <span class="timestamp"><?php echo $formattedTime; ?></span>
                            <div class="star-rating">
                                <?php for($i=1; $i<=5; $i++) echo ($i <= $rating) ? "<i class='fa-solid fa-star'></i>" : "<i class='fa-regular fa-star'></i>"; ?>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <?php
                            $currentAdminId = $_SESSION['admin_user_id'] ?? ($_SESSION['user_id'] ?? 1);
                            if ($row['user_id'] == $currentAdminId): ?>
                            <button type="button" class="edit-btn" onclick="openEditModal(<?php echo $row['id']; ?>, <?php echo htmlspecialchars(json_encode($row['comment'])); ?>, <?php echo $rating; ?>)">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <?php endif; ?>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this comment?');" style="margin:0;">
                                <input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_comment" class="delete-btn">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <p class="comment-text"><?php echo nl2br(htmlspecialchars($row['comment'])); ?></p>
                </div>
            </div>
        <?php } ?>

        <div class="input-section">
            <h3>Add Admin Comment</h3>
            <?php if (isset($error_message)): ?>
                <div style="color: red; margin-bottom: 15px;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form method="post">
                <textarea name="comment" placeholder="Enter admin comment..." required></textarea>
                <button type="submit" name="add_comment" class="post-btn">Post Comment</button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1001;">
        <div style="background:#fff; width:90%; max-width:500px; margin:100px auto; padding:20px; border-radius:15px;">
            <h3 style="margin-bottom:15px;">Edit Comment</h3>
            <form method="post">
                <input type="hidden" name="comment_id" id="edit_comment_id">
                <textarea name="comment" id="edit_comment_text" required></textarea>
                <div style="margin-bottom:15px;">
                    <label style="font-weight:600; font-size:14px; display:block; margin-bottom:8px;">Rating</label>
                    <div class="star-input">
                        <input type="radio" name="rating" id="edit_star5" value="5"><label for="edit_star5"></label>
                        <input type="radio" name="rating" id="edit_star4" value="4"><label for="edit_star4"></label>
                        <input type="radio" name="rating" id="edit_star3" value="3"><label for="edit_star3"></label>
                        <input type="radio" name="rating" id="edit_star2" value="2"><label for="edit_star2"></label>
                        <input type="radio" name="rating" id="edit_star1" value="1"><label for="edit_star1"></label>
                    </div>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:15px;">
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
            // Set the correct star rating radio button
            var starInput = document.getElementById('edit_star' + rating);
            if (starInput) starInput.checked = true;
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
