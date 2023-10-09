<?php
include 'common.php';

session_start();

// Ensure the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Fetch blocked IPs from the database
$stmt = $pdo->prepare("SELECT ip_address, block_until FROM blocked_ips_table");
$stmt->execute();
$blockedIPs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch permanently blocked IPs from the database
$stmt = $pdo->prepare("SELECT ip_range FROM permanent_blocks");
$stmt->execute();
$permanentlyBlockedIPs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>

<body>
    <h2>Ad Protection Dashboard</h2>

    <a href="logout.php">Logout</a>

    <!-- Configuration Options for the Script Settings -->
    <h3>Configuration</h3>
    <a href="settings.php">Configure Script Settings</a>

    <!-- Display Blocked IPs with Option to Delete -->
    <h3>Blocked IP Addresses</h3>
    <table>
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Blocked Until</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($blockedIPs as $ip): ?>
                <tr>
                    <td>
                        <?php echo $ip['ip_address']; ?>
                    </td>
                    <td>
                        <?php echo $ip['block_until']; ?>
                    </td>
                    <td><a href="delete_ip.php?ip=<?php echo $ip['ip_address']; ?>">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Form to Add Permanent Blocks -->
    <h3>Permanently Block IP or Range</h3>
    <form action="add_permanent_block.php" method="post">
        <label>
            IP Address or Range:
            <input type="text" name="ip_range" required>
        </label>
        <br>
        <input type="submit" value="Block">
    </form>

    <h3>Permanently Blocked IP Addresses</h3>
    <table>
        <thead>
            <tr>
                <th>IP Address or Range</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($permanentlyBlockedIPs as $ip): ?>
                <tr>
                    <td>
                        <?php echo $ip['ip_range']; ?>
                    </td>
                    <td><a href="delete_permanent_ip.php?ip=<?php echo $ip['ip_range']; ?>">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>