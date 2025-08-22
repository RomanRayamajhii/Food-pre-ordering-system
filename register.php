<?php
session_start();
include 'config/db.php';

$errors = [];

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = trim($_POST['address']);

    // Simple validation
    if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Username must be between 3 and 50 characters.";
    }
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Phone number must be 10 digits.";
    }
    if (empty($address)) {
        $errors[] = "Address is required.";
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if username or email already exists
    if (empty($errors)) {
        $query = "SELECT id FROM users WHERE email = '$email' OR username = '$username'";
        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            $errors[] = "Email or username already exists.";
        }
    }

    // If no errors, insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, full_name, email, phone, password, address) 
                  VALUES ('$username', '$full_name', '$email', '$phone', '$hashed_password', '$address')";
        if ($conn->query($query)) {
            echo "<script>
                alert('Registration successful');
                window.location.href = 'login.php';
            </script>";
            exit();
        } else {
            $errors[] = "Registration failed. Please try again later.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Food Ordering System</title>
    
      
<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f4f4f9;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
}

.register-container {
    width: 100%;
    max-width: 400px;
    background: #ffffff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #000;
    font-size: 24px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    font-size: 16px;

    border: 1px solid #ddd;
    border-radius: 5px;
}

.form-group input[type="submit"] {
    background: #000;
    color: #fff;
    cursor: pointer;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
    margin-bottom: 20px;
    font-weight: bold;
}

.submit-btn {
    background: #000;
    color: #fff;
    padding: 10px 20px;
    width: 100%;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.submit-btn:hover {
    background: #444;
}

.login-link {
    text-align: center;
    margin-top: 20px;
}

.login-link a {
    text-decoration: none;
    color: #000;
    font-weight: bold;
}

.login-link a:hover {
    text-decoration: underline;
}

@media (max-width: 480px) {
    .register-container {
        padding: 30px 20px;
    }
}

</style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error) echo htmlspecialchars($error) . "<br>"; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" required minlength="3" maxlength="50" placeholder="Choose a username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" required placeholder="Enter your full name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" name="phone" minlength="10" maxlength="10" pattern="[0-9]{10}" placeholder="Enter your phone number" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" required rows="3" placeholder="Enter your address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" required minlength="6" placeholder="Choose a password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" required minlength="6" placeholder="Confirm your password">
            </div>

            <input type="submit" name="submit" class="submit-btn">
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
