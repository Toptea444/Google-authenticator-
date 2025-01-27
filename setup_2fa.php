<?php
require_once 'vendor/autoload.php';
require_once 'db_connection.php'; // Secure database connection
use PragmaRX\Google2FA\Google2FA;

session_start();

// Ensure the username is set (this should already be done when the user logs in)
if (!isset($_SESSION['username'])) {
    // Handle the case if the user session is invalid (redirect to login, for example)
    header("Location: login.php");
    exit;
}

$google2fa = new Google2FA();

// Fetch the secret key from the database for the current user
$stmt = $pdo->prepare("SELECT secret_key, is_2fa_enabled FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();

/*if (!$user || !$user['secret_key']) {
    // Redirect back to registration if no secret key exists
    header("Location: register.php");
    exit;
}
*/
// Use the existing secret key from the database
$secretKey = $user['secret_key'];

// Generate QR code URL for display
$companyName = "MyWebApp";
$username = $_SESSION['username'];
$qrCodeUrl = $google2fa->getQRCodeUrl($companyName, $username, $secretKey);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Setup</title>
</head>
<body>
    <h2>Set up Google Authenticator</h2>
    <p>Use this secret key to set up your authenticator app:</p>
    <strong><?= $secretKey; ?></strong>
    <p>Alternatively, you can scan the QR code:</p>
    <img src="https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=<?= urlencode($qrCodeUrl); ?>" alt="QR Code">

    <form method="POST" action="verify_2fa.php">
        <label for="auth_code">Enter the code generated in your app:</label>
        <input type="text" name="auth_code" id="auth_code" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>