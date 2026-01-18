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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $location = trim($_POST['location']);
    $material = trim($_POST['material']);
    $comments = trim($_POST['comments']);
    $date = date("Y-m-d");

    if (empty($location) || empty($material) || $material === 'null' || empty($comments)) {
        $error_message = 'All fields are required';
    } else {
        $stmt = $conn->prepare("INSERT INTO donations (name, phone, email, date, material, location, comments) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $phone, $email, $date, $material, $location, $comments);
        
        if ($stmt->execute()) {
            $success_message = 'Donation registered successfully';
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
    <title>Donor Page</title>
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
                    <li><a href="donor.php" class="active">Donate</a></li>
                    <li><a href="complaints.php">Complaints & Suggestions</a></li>
                    <li><a href="?logout=1">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="form-container">
        <h2>Welcome <?php echo $name; ?></h2>
        
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
                <label>Location:</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Material Type:</label>
                <select name="material" class="form-control" required>
                    <option value="null">Select material type</option>
                    <option value="Clothes">Clothes and Shoes</option>
                    <option value="Plastic">Plastic</option>
                    <option value="Glass">Glass</option>
                    <option value="Metals">Metals</option>
                    <option value="Farming Waste">Farming Waste</option>
                    <option value="Papers">Papers and Cardboard</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Notes:</label>
                <textarea name="comments" class="form-control" required></textarea>
            </div>
            
            <button type="submit" name="submit" class="btn" style="width: 100%;">Submit</button>
        </form>
    </div>

    <div class="footer">
        <p>Al-Naqaa Charity Society 2025</p>
    </div>
</body>
</html>
