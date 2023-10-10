    <?php
    include '../includes/config.php';

    function fetchSettings($pdo)
    {
        $settings = [];
        $stmt = $pdo->prepare("SELECT name, value FROM settings");
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['name']] = $row['value'];
        }

        return $settings;
    }

    function getAdBlockMode($pdo)
    {
        $settings = fetchSettings($pdo);
        return $settings['ad_block_mode'] ?? 'single';
    }

    function deleteOldClickRecords($pdo, $ip_address, $ad_unit_id)
    {
        $stmt = $pdo->prepare("DELETE FROM clicks_table WHERE ip_address = ? AND ad_unit_id = ?");
        $stmt->execute([$ip_address, $ad_unit_id]);

    }

    function isIPBlocked($pdo, $ip_address, $ad_unit_id = null)
    {
        $adBlockMode = getAdBlockMode($pdo);

        if ($adBlockMode == 'single' && $ad_unit_id) {
            $stmt = $pdo->prepare("SELECT block_until FROM blocked_ips_table WHERE ip_address = ? AND ad_unit_id = ?");
            $stmt->execute([$ip_address, $ad_unit_id]);
            $row = $stmt->fetch();

            if ($row) {
                return true;
            }
        } elseif ($adBlockMode == 'all') {
            $stmt = $pdo->prepare("SELECT block_until FROM blocked_ips_table WHERE ip_address = ?");
            $stmt->execute([$ip_address]);
            $row = $stmt->fetch();

            if ($row) {
                $blockUntil = new DateTime($row['block_until']);
                $now = new DateTime();

                if ($blockUntil > $now) {
                    return true;
                } else {
                    // If the block duration has expired, delete the IP from the blocked_ips_table
                    $stmt = $pdo->prepare("DELETE FROM blocked_ips_table WHERE ip_address = ?");
                    $stmt->execute([$ip_address]);

                    // Delete old click records for the IP address
                    deleteOldClickRecords($pdo, $ip_address, $ad_unit_id);
                }
            }
        }

        // Check for permanent blocks
        $stmt = $pdo->prepare("SELECT 1 FROM permanent_blocks WHERE ip_range = ?");
        $stmt->execute([$ip_address]);
        $row = $stmt->fetch();

        if ($row) {
            return true; // IP is permanently blocked
        }

        return false;
    }


    function logAdClick($pdo, $ip_address)
    {
        $referrer = $_SERVER['HTTP_REFERER'] ?? 'None';
        file_put_contents('click_log.txt', date('Y-m-d H:i:s') . " - Ad clicked by IP: " . $ip_address . " - Referrer: " . $referrer . "\n", FILE_APPEND);
    }

    function updateSetting($pdo, $name, $value)
    {
        $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE name = ?");
        $stmt->execute([$value, $name]);
    }
    ?>