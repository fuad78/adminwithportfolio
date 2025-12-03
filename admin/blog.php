<?php
require_once '../config.php';
requireLogin();

$conn = getDBConnection();
$message = '';
$error = '';

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_post'])) {
        $title = sanitize($_POST['title']);
        $excerpt = sanitize($_POST['excerpt']);
        $content = sanitize($_POST['content']);
        $image = sanitize($_POST['image']);
        $author = sanitize($_POST['author']);
        $date = sanitize($_POST['date']);
        
        $stmt = $conn->prepare("INSERT INTO blog_posts (title, excerpt, content, image, author, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $title, $excerpt, $content, $image, $author, $date);
        
        if ($stmt->execute()) {
            $message = 'Blog post added successfully!';
        } else {
            $error = 'Failed to add blog post.';
        }
        $stmt->close();
    } elseif (isset($_POST['update_post'])) {
        $id = (int)$_POST['id'];
        $title = sanitize($_POST['title']);
        $excerpt = sanitize($_POST['excerpt']);
        $content = sanitize($_POST['content']);
        $image = sanitize($_POST['image']);
        $author = sanitize($_POST['author']);
        $date = sanitize($_POST['date']);
        
        $stmt = $conn->prepare("UPDATE blog_posts SET title = ?, excerpt = ?, content = ?, image = ?, author = ?, date = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $title, $excerpt, $content, $image, $author, $date, $id);
        
        if ($stmt->execute()) {
            $message = 'Blog post updated successfully!';
        } else {
            $error = 'Failed to update blog post.';
        }
        $stmt->close();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: /admin/blog.php');
    exit;
}

// Get post for editing
$editPost = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = $conn->query("SELECT * FROM blog_posts WHERE id = $id");
    $editPost = $result->fetch_assoc();
}

// Get all posts
$postsResult = $conn->query("SELECT * FROM blog_posts ORDER BY date DESC, created_at DESC");
$posts = [];
while ($row = $postsResult->fetch_assoc()) {
    $posts[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="/admin/index.php" class="text-gray-600 hover:text-gray-900">← Back to Dashboard</a>
                    <h1 class="text-xl font-bold text-gray-900">Manage Blog</h1>
                </div>
                <div class="flex items-center">
                    <a href="/admin/logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4"><?php echo $editPost ? 'Edit Blog Post' : 'Add New Blog Post'; ?></h2>
            <form method="POST">
                <?php if ($editPost): ?>
                    <input type="hidden" name="id" value="<?php echo $editPost['id']; ?>">
                <?php endif; ?>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" name="title" value="<?php echo $editPost ? htmlspecialchars($editPost['title']) : ''; ?>" class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                            <input type="text" name="author" value="<?php echo $editPost ? htmlspecialchars($editPost['author']) : 'KM Fuad Hasan'; ?>" class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                        <input type="url" name="image" value="<?php echo $editPost ? htmlspecialchars($editPost['image']) : ''; ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" value="<?php echo $editPost ? $editPost['date'] : date('Y-m-d'); ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
                        <textarea name="excerpt" rows="2" class="w-full border rounded px-3 py-2" required><?php echo $editPost ? htmlspecialchars($editPost['excerpt']) : ''; ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea name="content" rows="10" class="w-full border rounded px-3 py-2" required><?php echo $editPost ? htmlspecialchars($editPost['content']) : ''; ?></textarea>
                    </div>
                </div>
                <button type="submit" name="<?php echo $editPost ? 'update_post' : 'add_post'; ?>" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <?php echo $editPost ? 'Update Post' : 'Add Post'; ?>
                </button>
                <?php if ($editPost): ?>
                    <a href="/admin/blog.php" class="mt-4 ml-2 bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 inline-block">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">All Blog Posts</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Excerpt</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($post['title']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($post['excerpt']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($post['author']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y', strtotime($post['date'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="/admin/blog.php?edit=<?php echo $post['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <a href="/admin/blog.php?delete=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>



