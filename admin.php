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
} else {
    header('Location: login.php');
    exit();
}
$stmt->close();

$usertype = $row['usertype'];

$donations = $conn->query("SELECT * FROM donations ORDER BY date DESC");

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
    <title>Admin Dashboard</title>
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
                    <li><a href="admin.php" class="active">Dashboard</a></li>
                    <?php if ($usertype === 'Admin'): ?>
                        <li><a href="manage_users.php">Manage Employees</a></li>
                    <?php endif; ?>
                    <li><a href="view_complaints.php">Complaints</a></li>
                    <li><a href="?logout=1">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <h2>Statistics</h2>
        <div class="stats">
            <div class="stat-card">
                <h3>Total Donations</h3>
                <p><?php echo $donations->num_rows; ?></p>
            </div>
        </div>

        <h2>Donations List</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Material</th>
                    <th>Location</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($donations->num_rows > 0): ?>
                    <?php while($row = $donations->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['material']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td><?php echo $row['comments']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No donations yet</td>
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
