<?php
include 'common.php';

if (isset($_GET['ip'])) {
  $ip_address = $_GET['ip'];

  // Delete the IP from the blocked_ips_table
  $stmt = $pdo->prepare("DELETE FROM {$tablePrefix}blocked_ips_table WHERE ip_address = ?");
  $stmt->execute([$ip_address]);

  header('Location: dashboard.php');
  exit;
}