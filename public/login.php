<?php
include 'common.php';

session_start();
// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  header('Location: dashboard.php');
  exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Fetch the hashed password for the given username from the database
  $stmt = $pdo->prepare("SELECT hashed_password FROM {$tablePrefix}admins WHERE username = ?");
  $stmt->execute([$username]);
  $result = $stmt->fetch();

  if ($result && password_verify($password, $result['hashed_password'])) {
    // Password is correct, start a session
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;
    header('Location: dashboard.php');
    exit;
  } else {
    $error = "Invalid username or password!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
</head>

<body>
  <h2>Login</h2>
  <form action="login.php" method="post">
    <label>
      Username:
      <input type="text" name="username" required>
    </label>
    <br>
    <label>
      Password:
      <input type="password" name="password" required>
    </label>
    <br>
    <input type="submit" value="Login">
  </form>
  <?php if ($error): ?>
    <p style="color: red;">
      <?php echo $error; ?>
    </p>
  <?php endif; ?>
</body>

</html>