<?php
include 'common.php';

if (!isset($_GET['table']) || !isset($_GET['id'])) {
  die('Table or ID not specified.');
}
$table = $_GET['table'];
$id = $_GET['id'];

// Fetch existing data
$stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $updates = [];
  $values = [];

  foreach ($_POST as $field => $value) {
    $updates[] = "$field = ?";
    $values[] = $value;
  }
  $values[] = $id; // Add ID for the WHERE clause
  $updateString = implode(", ", $updates);

  $stmt = $pdo->prepare("UPDATE {$table} SET {$updateString} WHERE id = ?");
  if ($stmt->execute($values)) {
    header("Location: dashboard.php");
    exit;
  } else {
    echo "Error updating row.";
  }
}

?>

<h2>Edit Row in Table
  <?php echo $table; ?>
</h2>
<form action="edit_row.php?table=<?php echo $table; ?>&id=<?php echo $id; ?>" method="post">
  <!-- Populate form with existing data -->
  <?php
  foreach ($data as $field => $value) {
    echo "<label>{$field}: <input type='text' name='{$field}' value='{$value}'></label><br/>";
  }
  ?>
  <input type="submit" value="Update Row">
</form>