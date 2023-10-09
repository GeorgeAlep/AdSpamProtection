<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include 'common.php';

// Fetch the current settings
$settings = fetchSettings($pdo);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $clickLimit = $_POST['adProtection_clickLimit'];
  $timeFrame = $_POST['adProtection_timeFrame'];
  $blockDuration = $_POST['adProtection_blockDuration'];
  $fingerprintjsEnabled = isset($_POST['adProtection_fingerprintjsEnabled']) ? 1 : 0;

  // Update the settings in the database
  updateSetting($pdo, 'adProtection_clickLimit', $clickLimit);
  updateSetting($pdo, 'adProtection_timeFrame', $timeFrame);
  updateSetting($pdo, 'adProtection_blockDuration', $blockDuration);
  updateSetting($pdo, 'adProtection_fingerprintjsEnabled', $fingerprintjsEnabled);

  // Refresh the settings after update
  $settings = fetchSettings($pdo);
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
    <input type="submit" value="Update Settings">
  </form>
  <br>
  <!-- Back To Dashboard -->
  <h3>Dashboard</h3>
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>