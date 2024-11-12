<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update profile.";
    }
    
    header("Location: dashboard.php");
    exit();
}

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - Nepali Food</title>
    <style>
        .edit-profile-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .submit-btn {
            background: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Include your navbar here -->

    <div class="edit-profile-container">
        <h2>Edit Profile</h2>
        
        <form action="edit_profile.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Update Profile</button>
        </form>
    </div>
</body>
</html> 