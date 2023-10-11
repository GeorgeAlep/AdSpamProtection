<?php
include 'common.php';

if (isset($_GET['ip'])) {
    $ip_range = $_GET['ip'];

    try {
        $stmt = $pdo->prepare("DELETE FROM permanent_blocks WHERE ip_range = ?");
        $stmt->execute([$ip_range]);

        // Add a success message or log it as you see fit.
        header("Location: dashboard.php?success=unblocked");
        exit();

    } catch (PDOException $e) {
        // Handle the error gracefully
        header("Location: dashboard.php?error=unblock_error");
        exit();
    }
} else {
    // If no IP is specified, just redirect back to dashboard.
    header("Location: dashboard.php");
    exit();
}
