<?php
include 'common.php';

if (isset($_GET['ip'])) {
  $ip_range = $_GET['ip'];

  // Delete the IP or IP range from the permanent_blocks table
  $stmt = $pdo->prepare("DELETE FROM permanent_blocks WHERE ip_range = ?");
  $stmt->execute([$ip_range]);
}

header('Location: dashboard.php');
exit;
?>