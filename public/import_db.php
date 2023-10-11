<?php
include 'common.php';

// Check if file was uploaded
if (isset($_FILES['sqlfile']) && $_FILES['sqlfile']['error'] == UPLOAD_ERR_OK) {
  $fileContent = file_get_contents($_FILES['sqlfile']['tmp_name']);

  // Split the file content into individual SQL statements
  $queries = array_filter(array_map('trim', explode(';', $fileContent)), function($query) {
    // Filter out conditional MySQL comments and empty queries
    return !preg_match('/^\/\*![0-9]{5}/', $query) && !empty($query);
  });

  try {
    // Start a transaction to ensure all queries run or none
    $pdo->beginTransaction();

    foreach ($queries as $query) {
      $pdo->exec($query);
    }
    
    // Check if a transaction is currently active
    if($pdo->inTransaction()) {
        $pdo->commit();
    }

    // Redirect back to dashboard with success message
    header('Location: dashboard.php?import=success');
    exit;
  } catch (PDOException $e) {
    // Rollback any changes in case of error, if a transaction is currently active
    if($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Last Query: {$query}<br>Error importing database: " . $e->getMessage());
  }
} else {
  header('Location: dashboard.php?import=error');
  exit;
}
?>
