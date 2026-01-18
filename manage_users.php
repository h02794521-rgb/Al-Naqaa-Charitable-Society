<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include_once("connect.php");

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT usertype FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['usertype'] !== 'Admin') {
        header('Location: donor.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
$stmt->close();

$error_message = '';
$success_message = '';

if (isset($_POST['add_employee'])) {
    $emp_username = trim($_POST['username']);
    $emp_name = trim($_POST['name']);
    $emp_email = trim($_POST['email']);
    $emp_phone = trim($_POST['phone']);
    $emp_password = $_POST['password'];
    
    if (empty($emp_username) || empty($emp_name) || empty($emp_email) || empty($emp_phone) || empty($emp_password)) {
        $error_message = 'All fields are required';
    } else {
        $check = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $check->bind_param("s", $emp_username);
        $check->execute();
        $check_result = $check->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = 'Username already exists';
        } else {
            $hashed_password = hash('sha256', $emp_password);
            $usertype = 'Employee';
            
            $stmt = $conn->prepare("INSERT INTO users (username, name, password, phone, email, usertype) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $emp_username, $emp_name, $hashed_password, $emp_phone, $emp_email, $usertype);
            
            if ($stmt->execute()) {
                $success_message = 'Employee added successfully';
            } else {
                $error_message = 'Failed to add employee';
            }
            $stmt->close();
        }
        $check->close();
    }
}

$employees = $conn->query("SELECT username, name, phone, email FROM users WHERE usertype = 'Employee'");

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
    <title>Manage Employees</title>
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
                    <li><a href="admin.php">Dashboard</a></li>
                    <li><a href="manage_users.php" class="active">Manage Employees</a></li>
                    <li><a href="view_complaints.php">Complaints</a></li>
                    <li><a href="?logout=1">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <h2>Add New Employee</h2>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" style="max-width: 600px; margin-bottom: 40px;">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="name" class="form-control" required>
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
            <button type="submit" name="add_employee" class="btn">Add Employee</button>
        </form>

        <h2>Employees List</h2>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($employees->num_rows > 0): ?>
                    <?php while($row = $employees->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No employees yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Al-Naqaa Charity Society 2025</p>
    </div>
</body>
</html>
