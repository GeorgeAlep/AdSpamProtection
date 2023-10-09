<?php
include 'common.php';

$settings = fetchSettings($pdo);
$clickLimit = $settings['adProtection_clickLimit'] ?? 3;
$timeQuantity = 20; // default value
$timeUnit = "SECOND"; // default value

if (isset($settings['adProtection_timeFrame'])) {
  list($timeQuantity, $timeUnit) = explode(' ', $settings['adProtection_timeFrame']);
}

$blockDuration = $settings['adProtection_blockDuration'] ?? "1 HOUR";
$ip_address = $_SERVER['REMOTE_ADDR'];
$fingerprint = "sampleFingerprint"; // This should be replaced with actual fingerprinting logic
$ad_unit_id = "adUnit1";
$message = "";

if (isIPBlocked($pdo, $ip_address)) {
  $message = "Ad not displayed because your IP is blocked.";
} else {
  $stmt = $pdo->prepare("SELECT COUNT(*) as click_count FROM clicks_table WHERE ip_address = ? AND ad_unit_id = ? AND clicked_at > NOW() - INTERVAL ? $timeUnit");
  $stmt->execute([$ip_address, $ad_unit_id, $timeQuantity]);
  $clicks = $stmt->fetch();

  if ($clicks['click_count'] >= $clickLimit) {
    list($blockQuantity, $blockUnit) = explode(' ', $blockDuration);
    $stmt = $pdo->prepare("INSERT INTO blocked_ips_table (ip_address, block_until) VALUES (?, NOW() + INTERVAL ? $blockUnit)");
    $stmt->execute([$ip_address, $blockQuantity]);
    $message = "You've clicked the ad too many times in a short period. Your IP has been temporarily blocked.";

    // Delete old click records for the IP address
    deleteOldClickRecords($pdo, $ip_address);
  } else {
    $stmt = $pdo->prepare("INSERT INTO clicks_table (ip_address, ad_unit_id, fingerprint) VALUES (?, ?, ?)");
    $stmt->execute([$ip_address, $ad_unit_id, $fingerprint]);
    $message = "Thank you for clicking the ad!";
  }
}

// Return a JSON response
header('Content-Type: application/json');
echo json_encode(['message' => $message]);
?>