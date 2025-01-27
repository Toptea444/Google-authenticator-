<?php
require_once 'vendor/autoload.php';
require_once 'db_connection.php'; // Secure database connection
use PragmaRX\Google2FA\Google2FA;

session_start();

$google2fa = new Google2FA();
$authCode = $_POST['auth_code'] ?? '';

// Fetch the secret key from the database for the current user
$stmt = $pdo->prepare("SELECT secret_key FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();

if ($user && $google2fa->verifyKey($user['secret_key'], $authCode)) {
    // Update user's 2FA status to enabled
    $stmt = $pdo->prepare("UPDATE users SET is_2fa_enabled = 1 WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    
    // Optionally, set a session flag to indicate 2FA is now active
    $_SESSION['2fa_active'] = true;
    
    // Redirect to a confirmation or dashboard page
    header("Location: dashboard.php?2fa_success=1");
    exit;
} else {
 
 echo "Invalid code";
    // Redirect back to setup_2fa.php with an error message
   // header("Location: setup_2fa.php?error=1");
  //  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Setup</title>
</head>
<body>

    <form method="POST" action="verify_2fa.php">
        <label for="auth_code">Enter the code generated in your app:</label>
        <input type="text" name="auth_code" id="auth_code" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>