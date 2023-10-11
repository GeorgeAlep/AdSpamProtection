<?php
include '../includes/config.php';


$tablePrefix = $GLOBALS['tablePrefix']; // Assuming the $prefix variable is global in config.php
function fetchSettings($pdo, $tablePrefix)
{
    $settings = [];
    $stmt = $pdo->prepare("SELECT name, value FROM {$tablePrefix}settings");
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['name']] = $row['value'];
    }

    return $settings;
}

function getAdBlockMode($pdo, $tablePrefix)
{
    $settings = fetchSettings($pdo, $tablePrefix);
    return $settings['ad_block_mode'] ?? 'single';
}

function deleteOldClickRecords($pdo, $tablePrefix, $ip_address, $ad_unit_id)
{
    $stmt = $pdo->prepare("DELETE FROM {$tablePrefix}clicks_table WHERE ip_address = ? AND ad_unit_id = ?");
    $stmt->execute([$ip_address, $ad_unit_id]);
}

function isIPBlocked($pdo, $tablePrefix, $ip_address, $ad_unit_id = null)
{
    // 1. Check for permanent blocks first
    $stmt = $pdo->prepare("SELECT 1 FROM {$tablePrefix}permanent_blocks WHERE ip_range = ?");
    $stmt->execute([$ip_address]);
    if ($stmt->fetch()) {
        return true; // IP is permanently blocked
    }

    // Get the ad block mode
    $adBlockMode = getAdBlockMode($pdo, $tablePrefix);

    if ($adBlockMode == 'single' && $ad_unit_id) {
        $stmt = $pdo->prepare("SELECT block_until FROM {$tablePrefix}blocked_ips_table WHERE ip_address = ? AND ad_unit_id = ?");
        $stmt->execute([$ip_address, $ad_unit_id]);
        $row = $stmt->fetch();

        if ($row) {
            $blockUntil = new DateTime($row['block_until']);
            $now = new DateTime();

            if ($blockUntil > $now) {
                return true; // IP is blocked for this ad unit
            }
            // If the block duration has expired, delete the IP from the blocked_ips_table for this ad unit
            $stmt = $pdo->prepare("DELETE FROM {$tablePrefix}blocked_ips_table WHERE ip_address = ? AND ad_unit_id = ?");
            $stmt->execute([$ip_address, $ad_unit_id]);

            // Delete old click records for the IP address and this ad unit
            deleteOldClickRecords($pdo, $tablePrefix, $ip_address, $ad_unit_id);
        }
    } elseif ($adBlockMode == 'all') {
        $stmt = $pdo->prepare("SELECT block_until FROM {$tablePrefix}blocked_ips_table WHERE ip_address = ?");
        $stmt->execute([$ip_address]);
        $row = $stmt->fetch();

        if ($row) {
            $blockUntil = new DateTime($row['block_until']);
            $now = new DateTime();

            if ($blockUntil > $now) {
                return true; // IP is blocked for all ad units
            }

            // If the block duration has expired, delete the IP from the blocked_ips_table
            $stmt = $pdo->prepare("DELETE FROM {$tablePrefix}blocked_ips_table WHERE ip_address = ?");
            $stmt->execute([$ip_address]);

            // Delete old click records for the IP address and this ad unit
            deleteOldClickRecords($pdo, $tablePrefix, $ip_address, $ad_unit_id);
        }
    }

    return false;
}

function logAdClick($pdo, $ip_address)
{
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'None';
    file_put_contents('click_log.txt', date('Y-m-d H:i:s') . " - Ad clicked by IP: " . $ip_address . " - Referrer: " . $referrer . "\n", FILE_APPEND);
}

function updateSetting($pdo, $tablePrefix, $name, $value)
{
    $stmt = $pdo->prepare("UPDATE {$tablePrefix}settings SET value = ? WHERE name = ?");
    $stmt->execute([$value, $name]);
}

function tableExists($pdo, $tableName)
{
    try {
        $result = $pdo->query("SELECT 1 FROM `$tableName` LIMIT 1");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function renameTablesBackToOriginal($pdo, $currentPrefix)
{
    $originalNames = [
        'admins',
        'blocked_ips_table',
        'settings',
        'clicks_table',
        'permanent_blocks'
    ];

    foreach ($originalNames as $tableName) {
        $prefixedName = $currentPrefix . $tableName;
        $renameQuery = "ALTER TABLE `$prefixedName` RENAME `$tableName`";
        $pdo->exec($renameQuery);
    }
}

function renameTablesBasedOnPrefix($pdo, $currentPrefix, $newPrefix)
{
    renameTablesBackToOriginal($pdo, $currentPrefix);

    $originalNames = [
        'admins',
        'blocked_ips_table',
        'settings',
        'clicks_table',
        'permanent_blocks'
    ];

    foreach ($originalNames as $tableName) {
        $newTableName = $newPrefix . $tableName;
        $renameQuery = "ALTER TABLE `$tableName` RENAME `$newTableName`";
        $pdo->exec($renameQuery);
    }
}

?>