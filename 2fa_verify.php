<?php
require_once 'db_connection.php';
use PragmaRX\Google2FA\Google2FA;

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $google2fa = new Google2FA();

    $stmt = $pdo->prepare("SELECT secret_key FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();

    $userCode = $_POST['auth_code'];
    if ($google2fa->verifyKey($user['secret_key'], $userCode)) {
        $_SESSION['logged_in'] = true;
        echo "Login successful!";
    } else {
        echo "Invalid 2FA code.";
    }
}
?>

<form method="POST" action="2fa_verify.php">
    <label for="auth_code">Enter the 2FA code:</label>
    <input type="text" name="auth_code" id="auth_code" required>
    <button type="submit">Verify</button>
</form>