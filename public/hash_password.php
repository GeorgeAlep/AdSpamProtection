<?php
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    echo "Hashed Password: " . $hashedPassword;
}
?>

<form action="hash_password.php" method="post">
    Enter Password to Hash: <input type="password" name="password">
    <input type="submit" value="Hash Password">
</form>
