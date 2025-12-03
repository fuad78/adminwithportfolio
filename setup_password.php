<?php
/**
 * Setup Script - Run this once to set up or reset admin password
 * 
 * Usage: php setup_password.php
 * Or access via browser: http://localhost/newport-master/setup_password.php
 */

require_once 'config.php';

// Only allow this script to run if not in production (add your own security check)
$action = $_GET['action'] ?? 'form';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_password'])) {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        $conn = getDBConnection();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing user
            $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $hashed_password, $username);
            if ($stmt->execute()) {
                $message = "Password updated successfully for user: $username";
            } else {
                $error = "Failed to update password.";
            }
        } else {
            // Create new user
            $email = sanitize($_POST['email'] ?? $username . '@example.com');
            $stmt = $conn->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $email);
            if ($stmt->execute()) {
                $message = "Admin user created successfully: $username";
            } else {
                $error = "Failed to create user.";
            }
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
    <title>Setup Admin Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Setup Admin Password
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Set or reset admin password
                </p>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form class="mt-8 space-y-6 bg-white p-6 rounded-lg shadow" method="POST">
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input id="username" name="username" type="text" required value="admin" class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email (for new users)</label>
                        <input id="email" name="email" type="email" class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input id="password" name="password" type="password" required class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <button type="submit" name="set_password" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Set Password
                    </button>
                </div>
            </form>
            
            <div class="text-center">
                <a href="/admin/login.php" class="text-indigo-600 hover:text-indigo-500">Go to Admin Login</a>
            </div>
        </div>
    </div>
</body>
</html>



