<?php
include 'common.php';

if (!isset($_GET['table']) || !isset($_GET['id'])) {
  die('Table or ID not specified.');
}
$table = $_GET['table'];
$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
if ($stmt->execute([$id])) {
  header("Location: dashboard.php");
  exit;
} else {
  echo "Error deleting row.";
}
?>