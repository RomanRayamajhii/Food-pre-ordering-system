
<?php
session_start();
include 'includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $sql="select * from users where username='$username' And password='$password'";
    $result = mysqli_query($conn, $sql);
if( mysqli_num_rows($result)==1){
    $_SESSION['username']=$username;
    $_SESSION['user_id'] = $user['id'];
        header("Location: ./dashboard.php");
        exit; 
}
    else {
       
        $_SESSION['errormessage'] = "Invalid Username or Password";
       
        header("Location: ./login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    
    <style>
   body {
    font-family: Arial, sans-serif;
    background-color: #f7f7f7;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    width: 100%;
    max-width: 400px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.header {
    text-align: center;
    font-size: 25px;
    margin-bottom: 20px;
}
 
button {
    display: block;
    margin: 10px auto;
}
.btn {
    width: 100%;
    padding: 10px;
    border: none;
    background: #007bff;
    color: #fff;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}



.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-size: large;
    margin-left: 4px;
    margin-bottom: 5px;
}

.form-group input {
    width: 100%;
    box-sizing: border-box;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
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
  </style>
</head>
<body>
    <div class="container">
       
            <div class="header">
                <h3>Admin Login</h3>
            </div><br>

            <?php if (isset($_SESSION['errormessage'])): ?>
    <div class="error-message">
        <?php echo $_SESSION['errormessage']; ?>
    </div>
    <?php unset($_SESSION['errormessage']); ?>
<?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        
    </div>
</body>
</html>
