<?php
require_once '../config.php';
requireLogin();

$conn = getDBConnection();

// Get statistics
$stats = [
    'contacts' => $conn->query("SELECT COUNT(*) as count FROM contact_submissions")->fetch_assoc()['count'],
    'new_contacts' => $conn->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'new'")->fetch_assoc()['count'],
    'projects' => $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'],
    'blog_posts' => $conn->query("SELECT COUNT(*) as count FROM blog_posts")->fetch_assoc()['count']
];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">Admin Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="/admin/logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Total Contacts</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $stats['contacts']; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">New Contacts</h3>
                <p class="text-3xl font-bold text-red-600 mt-2"><?php echo $stats['new_contacts']; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Projects</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $stats['projects']; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Blog Posts</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $stats['blog_posts']; ?></p>
            </div>
        </div>

        <!-- Management Links -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="/admin/home.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Home Section</h3>
                <p class="text-gray-600">Edit home page content, profile, and skills</p>
            </a>
            <a href="/admin/about.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">About Section</h3>
                <p class="text-gray-600">Edit about page content and skills</p>
            </a>
            <a href="/admin/services.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Services</h3>
                <p class="text-gray-600">Manage services list</p>
            </a>
            <a href="/admin/projects.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Projects</h3>
                <p class="text-gray-600">Manage projects portfolio</p>
            </a>
            <a href="/admin/blog.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Blog</h3>
                <p class="text-gray-600">Manage blog posts</p>
            </a>
            <a href="/admin/contacts.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Contact Submissions</h3>
                <p class="text-gray-600">View and manage contact form submissions</p>
                <?php if ($stats['new_contacts'] > 0): ?>
                    <span class="inline-block mt-2 bg-red-600 text-white text-xs px-2 py-1 rounded"><?php echo $stats['new_contacts']; ?> new</span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</body>
</html>



