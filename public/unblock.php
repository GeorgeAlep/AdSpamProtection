<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/config.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM {$tablePrefix}blocked_ips_table WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: dashboard.php");
exit;
