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
    if ($row['usertype'] !== 'Admin' && $row['usertype'] !== 'Employee') {
        header('Location: donor.php');
        exit();
    }
    $usertype = $row['usertype'];
} else {
    header('Location: login.php');
    exit();
}
$stmt->close();

$complaints = $conn->query("SELECT * FROM complaints ORDER BY date DESC");

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
    <title>View Complaints</title>
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
                    <?php if ($usertype === 'Admin'): ?>
                        <li><a href="manage_users.php">Manage Employees</a></li>
                    <?php endif; ?>
                    <li><a href="view_complaints.php" class="active">Complaints</a></li>
                    <li><a href="?logout=1">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <h2>Statistics</h2>
        <div class="stats">
            <div class="stat-card">
                <h3>Total Messages</h3>
                <p><?php echo $complaints->num_rows; ?></p>
            </div>
        </div>

        <h2>Complaints & Suggestions List</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($complaints->num_rows > 0): ?>
                    <?php while($row = $complaints->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['type']; ?></td>
                            <td><?php echo $row['message']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No messages yet</td>
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
