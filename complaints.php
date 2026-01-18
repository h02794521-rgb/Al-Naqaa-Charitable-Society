<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include_once("connect.php");

$username = $_SESSION['username'];
$error_message = '';
$success_message = '';

$name = '';
$phone = '';
$email = '';

$stmt = $conn->prepare("SELECT name, phone, email FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $phone = $row['phone'];
    $email = $row['email'];
}
$stmt->close();

if (isset($_POST['submit'])) {
    $type = trim($_POST['type']);
    $message = trim($_POST['message']);
    $date = date("Y-m-d");
    
    if (empty($type) || $type === 'null' || empty($message)) {
        $error_message = 'All fields are required';
    } else {
        $stmt = $conn->prepare("INSERT INTO complaints (name, phone, email, type, message, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $phone, $email, $type, $message, $date);
        
        if ($stmt->execute()) {
            $success_message = 'Your message has been sent successfully';
        } else {
            $error_message = 'An error occurred';
        }
        $stmt->close();
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaints & Suggestions</title>
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
                    <li><a href="donor.php">Donations</a></li>
                    <li><a href="complaints.php" class="active">Complaints & Suggestions</a></li>
                    <li><a href="?logout=1">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="form-container">
        <h2>Complaints & Suggestions</h2>
        <p style="text-align: center; margin-bottom: 20px;">We value your feedback</p>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" class="form-control" value="<?php echo $name; ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>Phone:</label>
                <input type="text" class="form-control" value="<?php echo $phone; ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="text" class="form-control" value="<?php echo $email; ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>Type:</label>
                <select name="type" class="form-control" required>
                    <option value="Complaint">Complaint</option>
                    <option value="Suggestion">Suggestion</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Message:</label>
                <textarea name="message" class="form-control" required style="height: 120px;"></textarea>
            </div>
            
            <button type="submit" name="submit" class="btn" style="width: 100%;">Send</button>
        </form>
    </div>

    <div class="footer">
        <p>Al-Naqaa Charity Society 2025</p>
    </div>
</body>
</html>
