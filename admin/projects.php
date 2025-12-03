<?php
require_once '../config.php';
requireLogin();

$conn = getDBConnection();
$message = '';
$error = '';

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_project'])) {
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $image = sanitize($_POST['image']);
        $tags = sanitize($_POST['tags']);
        $order = (int)$_POST['display_order'];
        
        $stmt = $conn->prepare("INSERT INTO projects (title, description, image, tags, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $description, $image, $tags, $order);
        
        if ($stmt->execute()) {
            $message = 'Project added successfully!';
        } else {
            $error = 'Failed to add project.';
        }
        $stmt->close();
    } elseif (isset($_POST['update_project'])) {
        $id = (int)$_POST['id'];
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $image = sanitize($_POST['image']);
        $tags = sanitize($_POST['tags']);
        $order = (int)$_POST['display_order'];
        
        $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, image = ?, tags = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $title, $description, $image, $tags, $order, $id);
        
        if ($stmt->execute()) {
            $message = 'Project updated successfully!';
        } else {
            $error = 'Failed to update project.';
        }
        $stmt->close();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: /admin/projects.php');
    exit;
}

// Get project for editing
$editProject = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = $conn->query("SELECT * FROM projects WHERE id = $id");
    $editProject = $result->fetch_assoc();
}

// Get all projects
$projectsResult = $conn->query("SELECT * FROM projects ORDER BY display_order ASC");
$projects = [];
while ($row = $projectsResult->fetch_assoc()) {
    $projects[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="/admin/index.php" class="text-gray-600 hover:text-gray-900">← Back to Dashboard</a>
                    <h1 class="text-xl font-bold text-gray-900">Manage Projects</h1>
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
            <h2 class="text-2xl font-bold mb-4"><?php echo $editProject ? 'Edit Project' : 'Add New Project'; ?></h2>
            <form method="POST">
                <?php if ($editProject): ?>
                    <input type="hidden" name="id" value="<?php echo $editProject['id']; ?>">
                <?php endif; ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" value="<?php echo $editProject ? htmlspecialchars($editProject['title']) : ''; ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                        <input type="url" name="image" value="<?php echo $editProject ? htmlspecialchars($editProject['image']) : ''; ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full border rounded px-3 py-2" required><?php echo $editProject ? htmlspecialchars($editProject['description']) : ''; ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tags (comma-separated)</label>
                        <input type="text" name="tags" value="<?php echo $editProject ? htmlspecialchars($editProject['tags']) : ''; ?>" class="w-full border rounded px-3 py-2" placeholder="Tag1, Tag2, Tag3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input type="number" name="display_order" value="<?php echo $editProject ? $editProject['display_order'] : '0'; ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <button type="submit" name="<?php echo $editProject ? 'update_project' : 'add_project'; ?>" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <?php echo $editProject ? 'Update Project' : 'Add Project'; ?>
                </button>
                <?php if ($editProject): ?>
                    <a href="/admin/projects.php" class="mt-4 ml-2 bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 inline-block">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">All Projects</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tags</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($project['title']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($project['description']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars($project['tags']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $project['display_order']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="/admin/projects.php?edit=<?php echo $project['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <a href="/admin/projects.php?delete=<?php echo $project['id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900">Delete</a>
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



