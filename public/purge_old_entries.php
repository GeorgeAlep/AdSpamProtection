<?php
include 'common.php';

// Define the number of days after which an entry is considered old
$days_old = 30;

// Calculate the date threshold
$date_threshold = date('Y-m-d H:i:s', strtotime("-$days_old days"));

// Delete entries older than the threshold from the `blocked_ips_table`
$stmt = $pdo->prepare("DELETE FROM blocked_ips_table WHERE block_until < ?");
$stmt->execute([$date_threshold]);

echo "Old entries purged successfully!";
?>