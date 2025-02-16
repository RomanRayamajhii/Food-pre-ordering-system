<?php
session_start();

include 'config/db.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input
    $login_id = trim($_POST['login_id']);
    $password = $_POST['password'];

    // Create the SQL query
    $query = "SELECT id, email, phone, password, full_name FROM users WHERE email = '$login_id'";
    $result = mysqli_query($conn, $query);

    // Check if the user exists
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password using password_verify
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];

            // Redirect to the homepage
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        .login-container {
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
            color: #000;
        }
        .form-group input {
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
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <!-- Display the error message if set -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']); // Unset error
                ?>
            </div>
        <?php endif; ?>

       
        <form action="" method="POST">
            <div class="form-group">
                <label for="login_id">Email Address</label>
                <input type="text" name="login_id" id="login_id" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="submit-btn">Login</button>
        </form>
        <div class="register-link">
            Don't have an account? <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>