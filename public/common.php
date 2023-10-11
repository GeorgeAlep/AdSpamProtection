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
    // 1. Check for permanent blocks first
    $stmt = $pdo->prepare("SELECT 1 FROM permanent_blocks WHERE ip_range = ?");
    $stmt->execute([$ip_address]);
    if ($stmt->fetch()) {
        return true; // IP is permanently blocked
    }

    // Get the ad block mode
    $adBlockMode = getAdBlockMode($pdo);

    if ($adBlockMode == 'single' && $ad_unit_id) {
        $stmt = $pdo->prepare("SELECT block_until FROM blocked_ips_table WHERE ip_address = ? AND ad_unit_id = ?");
        $stmt->execute([$ip_address, $ad_unit_id]);
        $row = $stmt->fetch();

        if ($row) {
            $blockUntil = new DateTime($row['block_until']);
            $now = new DateTime();

            if ($blockUntil > $now) {
                return true; // IP is blocked for this ad unit
            }
            // If the block duration has expired, delete the IP from the blocked_ips_table for this ad unit
            $stmt = $pdo->prepare("DELETE FROM blocked_ips_table WHERE ip_address = ? AND ad_unit_id = ?");
            $stmt->execute([$ip_address, $ad_unit_id]);

            // Delete old click records for the IP address and this ad unit
            deleteOldClickRecords($pdo, $ip_address, $ad_unit_id);
        }
    } elseif ($adBlockMode == 'all') {
        $stmt = $pdo->prepare("SELECT 1 FROM blocked_ips_table WHERE ip_address = ?");
        $stmt->execute([$ip_address]);
        if ($stmt->fetch()) {
            return true; // IP is blocked for all ad units because one of the ad units was abused
        }
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