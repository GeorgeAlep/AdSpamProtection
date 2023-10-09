<?php
include 'common.php';

$settings = fetchSettings($pdo);
$clickLimit = $settings['adProtection_clickLimit'] ?? 3;
$timeQuantity = 20; // default value
$timeUnit = "SECOND"; // default value

// Extract quantity and unit from the settings if available
if (isset($settings['adProtection_timeFrame'])) {
    list($timeQuantity, $timeUnit) = explode(' ', $settings['adProtection_timeFrame']);
}

$blockDuration = $settings['adProtection_blockDuration'] ?? "1 HOUR";
$enableFingerprintJS = $settings['adProtection_fingerprintjsEnabled'] ?? false;

$ip_address = $_SERVER['REMOTE_ADDR'];
$fingerprint = "sampleFingerprint"; // This should be replaced with actual fingerprinting logic
$ad_unit_id = "adUnit1";
$message = isIPBlocked($pdo, $ip_address) ? "Ad not displayed because your IP is blocked." : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ad Page</title>
</head>

<body>
    <div id="adContent">
        <?php if (!$message): ?>
            <?php include 'ad_content.php'; ?>
        <?php else: ?>
            <p>
                <?php echo $message; ?>
            </p>
        <?php endif; ?>
    </div>

    <p id="message"></p>

    <script>
        function handleAdInteraction() {
            // Send AJAX request
            fetch('process_click.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ad_clicked=1'
            })
                .then(response => response.json())
                .then(data => {
                    // Update the message based on the server's response
                    document.getElementById('message').textContent = data.message;

                    // If the user is blocked, hide the ad content
                    if (data.message.includes("blocked")) {
                        document.getElementById('adContent').style.display = 'none';
                    }
                });
        }

        document.getElementById('adContent').addEventListener('click', handleAdInteraction);
        document.getElementById('adContent').addEventListener('touchstart', handleAdInteraction);
        document.getElementById('adContent').addEventListener('contextmenu', function (event) {
            event.preventDefault(); // Prevent the context menu from appearing
            handleAdInteraction();
        });

    </script>

    <!-- Include FingerprintJS library if enabled -->
    <?php if ($enableFingerprintJS): ?>
        <script src="path_to_fingerprintjs_library"></script>
        <script>
            // Initialize and get the fingerprint
            // Update the fingerprint variable in the PHP script accordingly
        </script>
    <?php endif; ?>
</body>

</html>