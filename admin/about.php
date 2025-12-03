<?php
require_once '../config.php';
requireLogin();

$conn = getDBConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_about'])) {
    $title = sanitize($_POST['title']);
    $subtitle = sanitize($_POST['subtitle']);
    $description = sanitize($_POST['description']);
    
    $stmt = $conn->prepare("UPDATE about_content SET title = ?, subtitle = ?, description = ? WHERE id = 1");
    $stmt->bind_param("sss", $title, $subtitle, $description);
    
    if ($stmt->execute()) {
        $message = 'About content updated successfully!';
    } else {
        $error = 'Failed to update about content.';
    }
    $stmt->close();
}

$aboutResult = $conn->query("SELECT * FROM about_content LIMIT 1");
$about = $aboutResult->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="/admin/index.php" class="text-gray-600 hover:text-gray-900">← Back to Dashboard</a>
                    <h1 class="text-xl font-bold text-gray-900">Manage About Section</h1>
                </div>
                <div class="flex items-center">
                    <a href="/admin/logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">About Content</h2>
            <form method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($about['title']); ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <input type="text" name="subtitle" value="<?php echo htmlspecialchars($about['subtitle']); ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="8" class="w-full border rounded px-3 py-2" required><?php echo htmlspecialchars($about['description']); ?></textarea>
                    </div>
                </div>
                <button type="submit" name="update_about" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update About Content</button>
            </form>
        </div>

        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <p class="text-gray-600">Note: Skills displayed on the About page are managed from the <a href="/admin/home.php" class="text-blue-600 hover:underline">Home Section</a>.</p>
        </div>
    </div>
</body>
</html>



