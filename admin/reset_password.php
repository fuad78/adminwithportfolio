<?php
require_once '../config.php';

$tokenValue = sanitize($_GET['token'] ?? $_POST['token'] ?? '');
$error = '';
$success = '';
$showForm = true;
$user = null;

if (!$tokenValue) {
    $error = 'Invalid or missing reset token.';
    $showForm = false;
} else {
    $conn = getDBConnection();
    $stmt = $conn->prepare('SELECT prt.id, prt.admin_id, prt.expires_at, au.username FROM password_reset_tokens prt INNER JOIN admin_users au ON au.id = prt.admin_id WHERE prt.token = ? LIMIT 1');
    $stmt->bind_param('s', $tokenValue);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $tokenRow = $result->fetch_assoc();
        $now = new DateTime();
        $expires = new DateTime($tokenRow['expires_at']);

        if ($expires < $now) {
            $error = 'This reset link has expired. Please request a new one.';
            $showForm = false;
        } else {
            $user = $tokenRow;
        }
    } else {
        $error = 'Invalid reset token.';
        $showForm = false;
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $updateStmt = $conn->prepare('UPDATE admin_users SET password = ? WHERE id = ?');
        $updateStmt->bind_param('si', $hash, $user['admin_id']);

        if ($updateStmt->execute()) {
            $deleteStmt = $conn->prepare('DELETE FROM password_reset_tokens WHERE admin_id = ?');
            $deleteStmt->bind_param('i', $user['admin_id']);
            $deleteStmt->execute();
            $deleteStmt->close();

            $success = 'Password updated successfully. You can now log in.';
            $showForm = false;
        } else {
            $error = 'Failed to update password. Please try again.';
        }

        $updateStmt->close();
        $conn->close();
        $conn = null;
    }
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Portfolio Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Reset Password
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Choose a new password for your admin account.
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

                <?php if ($showForm && $user): ?>
                    <form class="space-y-6" method="POST" action="">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($tokenValue); ?>">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input id="password" name="password" type="password" required minlength="8" class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input id="confirm_password" name="confirm_password" type="password" required minlength="8" class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Password
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center">
                        <a href="/admin/forgot_password.php" class="text-indigo-600 hover:text-indigo-500 text-sm">Request another reset link</a>
                    </div>
                <?php endif; ?>

                <div class="mt-6 text-center">
                    <a href="/admin/login.php" class="text-indigo-600 hover:text-indigo-500 text-sm">Back to login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

