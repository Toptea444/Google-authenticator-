<?php
require_once 'db_connection.php';

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    // Fetch user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['username'] = $username;

        // Check if 2FA is enabled
        if ($user['is_2fa_enabled'] == 0) {
            header("Location: setup_2fa.php");
            exit;
        } else {
            echo "Login successful!";
        }
    } else {
        echo "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="X-UA-Compatible" content="ie=edge">
 <title>Document</title>
</head>
<body>
 <form action="login.php" method="POST">
  <label for="">Username</label>
  <input type="text" name="username">
  <label for="">Password</label>
  <input type="text" name="password">
  <input type="submit" value="Submit">
 </form>
</body>
</html>