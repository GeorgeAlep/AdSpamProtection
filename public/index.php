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
$fingerprint = $_POST['fingerprint'] ?? null;
$ad_unit_id = "adUnit1";
$message1 = isIPBlocked($pdo, $ip_address, "adUnit1") ? "Ad not displayed because your IP is blocked." : "";
$message2 = isIPBlocked($pdo, $ip_address, "adUnit2") ? "Ad not displayed because your IP is blocked." : "";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ad Page</title>
</head>

<body>
    <!-- First Ad -->
    <div id="adContent1">
        <?php if (!$message1): ?>
            <?php include 'ad_content1.php'; ?>
        <?php else: ?>
            <p>
                <?php echo $message1; ?>
            </p>
        <?php endif; ?>
    </div>

    <br>

    <!-- Second Ad -->
    <div id="adContent2">
        <?php if (!$message2): ?>
            <?php include 'ad_content2.php'; ?>
        <?php else: ?>
            <p>
                <?php echo $message2; ?>
            </p>
        <?php endif; ?>
    </div>

    <p id="message"></p>

    <script>
        function handleAdInteraction(adUnit) {
            <?php if ($enableFingerprintJS): ?>
                // If FingerprintJS is enabled, capture the fingerprint and send it with the AJAX request
                const fpPromise = import('https://openfpcdn.io/fingerprintjs/v4')
                    .then(FingerprintJS => FingerprintJS.load());

                fpPromise
                    .then(fp => fp.get())
                    .then(result => {
                        let fingerprint = result.visitorId;
                        console.log("Captured Fingerprint:", fingerprint);
                        sendAdInteractionRequest(adUnit, fingerprint);
                    });
            <?php else: ?>
                // If FingerprintJS is not enabled, send the AJAX request without the fingerprint
                sendAdInteractionRequest(adUnit);
            <?php endif; ?>
        }

        function sendAdInteractionRequest(adUnit, fingerprint = null) {
            let requestBody = 'ad_clicked=1&ad_unit=' + adUnit;
            if (fingerprint) {
                requestBody += '&fingerprint=' + fingerprint;
            }

            fetch('process_click.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: requestBody
            })
                .then(response => response.json())
                .then(parsedData => {
                    // Update the message based on the server's response
                    document.getElementById('message').textContent = parsedData.message;

                    if (parsedData.message.includes("blocked")) {
                        if (adUnit === 'adUnit1') {
                            document.getElementById('adContent1').style.display = 'none';
                        } else if (adUnit === 'adUnit2') {
                            document.getElementById('adContent2').style.display = 'none';
                        }

                        // If mode is set to block all ads, hide both ads
                        let adBlockMode = "<?php echo getAdBlockMode($pdo); ?>";
                        if (adBlockMode === 'all') {
                            document.getElementById('adContent1').style.display = 'none';
                            document.getElementById('adContent2').style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('message').textContent = 'An error occurred. Check the console for details.';
                });
        }

        document.getElementById('adContent1').addEventListener('click', function () { handleAdInteraction('adUnit1'); });
        document.getElementById('adContent2').addEventListener('click', function () { handleAdInteraction('adUnit2'); });
    </script>

    <!-- Include FingerprintJS library if enabled -->
    <?php if ($enableFingerprintJS): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.0/fingerprint2.min.js"></script>
    <?php endif; ?>
</body>

</html>