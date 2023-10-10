<?php
include 'common.php';

session_start();

// Ensure the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $currentPassword = $_POST['current_password'];
  $newPassword = $_POST['new_password'];
  $confirmPassword = $_POST['confirm_password'];

  // Fetch the hashed password for the logged-in user from the database
  $stmt = $pdo->prepare("SELECT hashed_password FROM admins WHERE username = ?");
  $stmt->execute([$_SESSION['username']]);
  $result = $stmt->fetch();

  if ($result && password_verify($currentPassword, $result['hashed_password'])) {
    if ($newPassword === $confirmPassword) {
      $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("UPDATE admins SET hashed_password = ? WHERE username = ?");
      $stmt->execute([$hashedPassword, $_SESSION['username']]);
      $message = "Password changed successfully!";
    } else {
      $message = "New password and confirm password do not match!";
    }
  } else {
    $message = "Current password is incorrect!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password</title>
</head>

<body>
  <h2>Change Admin Password</h2>
  <form action="change_password.php" method="post">
    <label>
      Current Password:
      <input type="password" name="current_password" required>
    </label>
    <br>
    <label>
      New Password:
      <input type="password" name="new_password" required>
    </label>
    <br>
    <label>
      Confirm New Password:
      <input type="password" name="confirm_password" required>
    </label>
    <br>
    <input type="submit" value="Change Password">
  </form>
  <?php if ($message): ?>
    <p>
      <?php echo $message; ?>
    </p>
  <?php endif; ?>
  <br>
  <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>