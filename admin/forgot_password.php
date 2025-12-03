<?php
require_once '../config.php';
require_once '../includes/Mailer.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare('SELECT id, username, email FROM admin_users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(32));
            $expiresAt = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

            $deleteStmt = $conn->prepare('DELETE FROM password_reset_tokens WHERE admin_id = ?');
            $deleteStmt->bind_param('i', $user['id']);
            $deleteStmt->execute();
            $deleteStmt->close();

            $insertStmt = $conn->prepare('INSERT INTO password_reset_tokens (admin_id, token, expires_at) VALUES (?, ?, ?)');
            $insertStmt->bind_param('iss', $user['id'], $token, $expiresAt);
            $insertStmt->execute();
            $insertStmt->close();

            $resetUrl = sprintf('%s/reset_password.php?token=%s', rtrim(getBaseUrl(), '/'), urlencode($token));
            $htmlBody = '<p>Hello ' . htmlspecialchars($user['username']) . ',</p>'
                . '<p>We received a request to reset your admin password. Click the button below to set a new password. '
                . 'This link will expire in 1 hour.</p>'
                . '<p><a href="' . htmlspecialchars($resetUrl) . '" style="display:inline-block;padding:10px 16px;background-color:#4f46e5;color:#fff;text-decoration:none;border-radius:6px;">Reset Password</a></p>'
                . '<p>If you did not request this change, you can ignore this email.</p>'
                . '<p>— Portfolio Admin</p>';
            $textBody = "Hello {$user['username']},\n\n"
                . "We received a request to reset your admin password. Use the link below to set a new password (valid for 1 hour):\n"
                . "{$resetUrl}\n\nIf you did not request this, you can ignore this email.\n\n— Portfolio Admin";

            try {
                $mailer = new SmtpMailer();
                $mailer->send($user['email'], $user['username'], 'Reset your Portfolio admin password', $htmlBody, $textBody);
                $success = 'If the email exists in our system, a reset link has been sent.';
            } catch (MailerException $ex) {
                $error = 'We could not send the reset email. Please verify SMTP settings.';
            }
        } else {
            $success = 'If the email exists in our system, a reset link has been sent.';
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Portfolio Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Forgot Password
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter your admin email to receive a reset link.
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" method="POST" action="">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input id="email" name="email" type="email" required class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="admin@example.com">
                    </div>
                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Send Reset Link
                        </button>
                    </div>
                </form>
                <div class="mt-6 text-center">
                    <a href="/admin/login.php" class="text-indigo-600 hover:text-indigo-500 text-sm">Back to login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

