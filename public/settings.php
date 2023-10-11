<?php
session_start();

ini_set('display_errors', 0); // Turn off error displaying
ini_set('log_errors', 1); // Turn on error logging
ini_set('error_log', '/path_to_log_directory/php-error.log'); // Set the log file path

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit;
}

include 'common.php';

// Fetch the current settings
$settings = fetchSettings($pdo, $tablePrefix);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $clickLimit = $_POST['adProtection_clickLimit'];
  $timeFrame = $_POST['adProtection_timeFrame'];
  $blockDuration = $_POST['adProtection_blockDuration'];
  $fingerprintjsEnabled = isset($_POST['adProtection_fingerprintjsEnabled']) ? 1 : 0;
  $ad_block_mode = $_POST['ad_block_mode'];

  // Update the settings in the database
  updateSetting($pdo, $tablePrefix, 'adProtection_clickLimit', $clickLimit);
  updateSetting($pdo, $tablePrefix, 'adProtection_timeFrame', $timeFrame);
  updateSetting($pdo, $tablePrefix, 'adProtection_blockDuration', $blockDuration);
  updateSetting($pdo, $tablePrefix, 'adProtection_fingerprintjsEnabled', $fingerprintjsEnabled);
  updateSetting($pdo, $tablePrefix, 'ad_block_mode', $ad_block_mode);

  // Refresh the settings after update
  $settings = fetchSettings($pdo, $tablePrefix);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings</title>
</head>

<body>
  <h2>Update Settings</h2>
  <form action="settings.php" method="post">
    <label>
      Click Limit:
      <input type="number" name="adProtection_clickLimit"
        value="<?php echo $settings['adProtection_clickLimit'] ?? ''; ?>">
    </label>
    <br>
    <label>
      Time Frame (e.g., "20 SECOND"):
      <input type="text" name="adProtection_timeFrame" value="<?php echo $settings['adProtection_timeFrame'] ?? ''; ?>">
    </label>
    <br>
    <label>
      Block Duration (e.g., "1 HOUR"):
      <input type="text" name="adProtection_blockDuration"
        value="<?php echo $settings['adProtection_blockDuration'] ?? ''; ?>">
    </label>
    <br>
    <label>
      Enable FingerprintJS:
      <input type="checkbox" name="adProtection_fingerprintjsEnabled" <?php echo isset($settings['adProtection_fingerprintjsEnabled']) && $settings['adProtection_fingerprintjsEnabled'] ? 'checked' : ''; ?>>
    </label>
    <br>

    <h3>Ad Block Mode</h3>
    <label>
      <input type="radio" name="ad_block_mode" value="single" <?php echo (isset($settings['ad_block_mode']) && $settings['ad_block_mode'] == 'single') ? 'checked' : ''; ?>>
      Block only the clicked ad
    </label>
    <br>
    <label>
      <input type="radio" name="ad_block_mode" value="all" <?php echo (isset($settings['ad_block_mode']) && $settings['ad_block_mode'] == 'all') ? 'checked' : ''; ?>>
      Block all ads on the page
    </label>
    <br>
    <input type="submit" value="Update">
  </form>
  <br>
  <!-- Back To Dashboard -->
  <h3>Dashboard</h3>
  <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>