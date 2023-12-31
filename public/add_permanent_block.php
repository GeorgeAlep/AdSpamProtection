<?php
include 'common.php';

$ip_range = $_POST['ip_range'];

// Check if the IP or IP range already exists in the database
$stmt = $pdo->prepare("SELECT 1 FROM {$tablePrefix}permanent_blocks WHERE ip_range = ?");
$stmt->execute([$ip_range]);
$exists = $stmt->fetchColumn();

if ($exists) {
    // IP or IP range already exists, display a message
    echo "The IP or IP range is already blocked.";
} else {
    // Insert the new IP or IP range into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO {$tablePrefix}permanent_blocks (ip_range) VALUES (?)");
        $stmt->execute([$ip_range]);
        echo "IP or IP range blocked successfully!";
    } catch (PDOException $e) {
        // Handle the error gracefully
        echo "An error occurred: " . $e->getMessage();
    }
}

// Redirect back to dashboard after processing.
header("Location: dashboard.php");
exit();
?>