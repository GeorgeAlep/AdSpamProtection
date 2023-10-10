<?php
include 'common.php';

try {
  $settings = fetchSettings($pdo);
  $clickLimit = $settings['adProtection_clickLimit'] ?? 3;
  $timeQuantity = 20; // default value
  $timeUnit = "SECOND"; // default value

  if (isset($settings['adProtection_timeFrame'])) {
    list($timeQuantity, $timeUnit) = explode(' ', $settings['adProtection_timeFrame']);
  }

  $blockDuration = $settings['adProtection_blockDuration'] ?? "1 HOUR";
  list($blockQuantity, $blockUnit) = explode(' ', $blockDuration);
  $ip_address = $_SERVER['REMOTE_ADDR'];
  $fingerprint = $_POST['fingerprint'] ?? null;

  $ad_unit_id = $_POST['ad_unit'] ?? "adUnit1";

  // Handle the click first
  $stmt = $pdo->prepare("INSERT INTO clicks_table (ip_address, ad_unit_id, fingerprint) VALUES (?, ?, ?)");
  $stmt->execute([$ip_address, $ad_unit_id, $fingerprint]);

  // Check if the IP has exceeded the click limit for the specific ad unit
  $stmt = $pdo->prepare("SELECT COUNT(*) as click_count FROM clicks_table WHERE ip_address = ? AND ad_unit_id = ? AND clicked_at > NOW() - INTERVAL ? $timeUnit");
  $stmt->execute([$ip_address, $ad_unit_id, $timeQuantity]);
  $clicks = $stmt->fetch();

  if ($clicks['click_count'] >= $clickLimit) {
    // Check if IP address already exists in the table for the specific ad unit
    $stmt = $pdo->prepare("SELECT 1 FROM blocked_ips_table WHERE ip_address = ? AND ad_unit_id = ?");
    $stmt->execute([$ip_address, $ad_unit_id]);
    $exists = $stmt->fetch();

    if ($exists) {
      // Update the existing record for the specific ad unit
      $stmt = $pdo->prepare("UPDATE blocked_ips_table SET block_until = NOW() + INTERVAL ? $blockUnit, fingerprint = ? WHERE ip_address = ? AND ad_unit_id = ?");
      $stmt->execute([$blockQuantity, $fingerprint, $ip_address, $ad_unit_id]);
    } else {
      // Insert a new record for the specific ad unit
      $stmt = $pdo->prepare("INSERT INTO blocked_ips_table (ip_address, ad_unit_id, block_until, fingerprint) VALUES (?, ?, NOW() + INTERVAL ? $blockUnit, ?) ON DUPLICATE KEY UPDATE block_until = NOW() + INTERVAL ? $blockUnit, fingerprint = ?");
      $stmt->execute([$ip_address, $ad_unit_id, $blockQuantity, $fingerprint, $blockQuantity, $fingerprint]);

    }

    $message = "You've clicked the ad too many times in a short period. Your IP has been temporarily blocked for this ad unit.";

    // Delete old click records for the IP address and ad unit
    deleteOldClickRecords($pdo, $ip_address, $ad_unit_id);
  } else {
    $message = "Thank you for clicking the ad!";
  }


  // Check if the IP is blocked
  if (isIPBlocked($pdo, $ip_address)) {
    $message = "Ad not displayed because your IP is blocked.";
  }

  // Return a JSON response
  header('Content-Type: application/json');
  echo json_encode(['message' => $message]);
} catch (Exception $e) {
  header('Content-Type: application/json');
  echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
}
?>