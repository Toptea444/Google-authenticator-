<?php
require_once 'db_connection.php'; // Secure database connection
require_once 'vendor/autoload.php';
use PragmaRX\Google2FA\Google2FA;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']); // Prevent XSS
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Hash the password securely
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Generate the secret key for 2FA
    $google2fa = new Google2FA();
    $secretKey = $google2fa->generateSecretKey();

    // Insert user data along with the secret key
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, secret_key, is_2fa_enabled) VALUES (?, ?, ?, ?, 0)");
    if ($stmt->execute([$username, $email, $passwordHash, $secretKey])) {
        $_SESSION['username'] = $username; // Set session variable for username
        // Redirect to the 2FA setup page after successful registration
        header("Location: setup_2fa.php");
        exit;
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "Error registering user: " . $errorInfo[2];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>
<body>
    <form method="POST" action="register.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Register</button>
    </form>
</body>
</html>