<?php
include 'common.php';
session_start();

if (isset($_POST['renameTables'])) {
    $currentPrefix = $_POST['currentPrefix'];
    $newPrefix = $_POST['newPrefix'];
    renameTablesBasedOnPrefix($pdo, $currentPrefix, $newPrefix);
}

$noAdmins = true; // Default to true

// Check if admins table exists
$query = "SHOW TABLES LIKE '{$tablePrefix}admins'";
$result = $pdo->query($query);
$adminsTableExists = $result->rowCount() > 0;

// If admins table exists, then check if it has any records
if ($adminsTableExists) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_admins FROM {$tablePrefix}admins");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $noAdmins = $result['total_admins'] == 0;
}

// Check if blocked_ips_table exists
$query = "SHOW TABLES LIKE '{$tablePrefix}blocked_ips_table'";
$result = $pdo->query($query);
$blockedIPsTableExists = $result->rowCount() > 0;

// Ensure the user is logged in only if there are admins in the database
if (!$noAdmins && (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true)) {
    header('Location: login.php');
    exit;
}

// Attempt to fetch blocked IPs if the table exists
$blockedIPs = [];
if ($blockedIPsTableExists) {
    try {
        $stmt = $pdo->prepare("SELECT ip_address, GROUP_CONCAT(fingerprint) as fingerprints, GROUP_CONCAT(ad_unit_id) as ad_units, block_until FROM {$tablePrefix}blocked_ips_table GROUP BY ip_address");
        $stmt->execute();
        $blockedIPs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage(); // Display the error message. Don't use this in production as it might reveal sensitive information.
        $blockedIPs = false;
    }
}

// Fetch permanently blocked IPs
$permanentBlockedIPs = [];
try {
    $stmt = $pdo->prepare("SELECT ip_range FROM {$tablePrefix}permanent_blocks");
    $stmt->execute();
    $permanentBlockedIPs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
    $permanentBlockedIPs = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>
    <h2>Ad Protection Dashboard</h2>

    <?php if (!$noAdmins): ?>
        <a href="logout.php">Logout</a>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th colspan="2">Configuration</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="settings.php">Configure Script Settings</a></td>
                <td><a href="change_password.php">Change Admin Password</a></td>
            </tr>
            <tr>
                <td colspan="2">
                    <h4>Blocked IP Addresses</h4>
                    <?php if ($blockedIPs !== false): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Fingerprints</th>
                                    <th>Blocked Ad Units</th>
                                    <th>Blocked Until</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blockedIPs as $ip): ?>
                                    <tr>
                                        <td>
                                            <?php echo $ip['ip_address']; ?>
                                        </td>
                                        <td>
                                            <?php echo implode(', ', explode(',', $ip['fingerprints'])); ?>
                                        </td>
                                        <td>
                                            <?php echo implode(', ', explode(',', $ip['ad_units'])); ?>
                                        </td>
                                        <td>
                                            <?php echo $ip['block_until']; ?>
                                        </td>
                                        <td>
                                            <a href="unblock_ip.php?ip=<?php echo urlencode($ip['ip_address']); ?>">Unblock</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>There was an error fetching blocked IPs. Please ensure the database is properly set up.</p>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <td colspan="2">

                    <form action="add_permanent_block.php" method="post">
                        <label for="ip_range">Enter IP or Range:</label>
                        <input type="text" name="ip_range" id="ip_range">
                        <input type="submit" value="Block IP/Range Permanently">
                    </form>

                    <h4>Permanently Blocked IP Addresses</h4>
                    <?php if ($permanentBlockedIPs !== false && !empty($permanentBlockedIPs)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>IP Address or Range</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($permanentBlockedIPs as $ip): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($ip['ip_range']); ?>
                                        </td>
                                        <td>
                                            <a
                                                href="unblock_permanent_ip.php?ip=<?php echo urlencode($ip['ip_range']); ?>">Unblock</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No IP addresses or ranges are permanently blocked.</p>
                    <?php endif; ?>

                    <h4>Import SQL Schema</h4>
                    <form action="import_db.php" method="post" enctype="multipart/form-data">
                        <label>
                            SQL File:
                            <input type="file" name="sqlfile" accept=".sql">
                        </label>
                        <br>
                        <input type="submit" value="Import">
                    </form>

                    <h4>Update Table Prefix</h4>

                    <form action="dashboard.php" method="post">
                        Current Prefix: <input type="text" name="currentPrefix" /><br>
                        New Prefix: <input type="text" name="newPrefix" /><br>
                        <input type="submit" name="renameTables" value="Rename Tables" />
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>