<?php
session_start();
include_once('connect.php');

$error_message = '';

if (isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    
    if (empty($username) || empty($name) || empty($email) || empty($phone) || empty($pass)) {
        $error_message = 'All fields are required';
    } elseif ($pass !== $confirm_pass) {
        $error_message = 'Passwords do not match';
    } else {
        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = 'Username already exists';
        } else {
            $password = hash('sha256', $pass);
            $usertype = 'Doner';
            
            $stmt = $conn->prepare("INSERT INTO users (username, name, password, phone, email, usertype) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $username, $name, $password, $phone, $email, $usertype);
            
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                header('Location: donor.php');
                exit();
            } else {
                $error_message = 'Registration failed';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="nav-container">
                <a href="index.php" class="logo">
                    Al-Naqaa Charity Society
                </a>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php" class="active">Sign Up</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="form-container">
        <h2>Create Account</h2>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="tel" name="phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" name="signup" class="btn" style="width: 100%;">Sign Up</button>
        </form>
        
        <p style="text-align: center; margin-top: 15px;">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>

    <div class="footer">
        <p>Al-Naqaa Charity Society 2025</p>
    </div>
</body>
</html>
