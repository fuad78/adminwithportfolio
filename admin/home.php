<?php
require_once '../config.php';
requireLogin();

$conn = getDBConnection();
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_home'])) {
        $profile_image = sanitize($_POST['profile_image']);
        $name = sanitize($_POST['name']);
        $title = sanitize($_POST['title']);
        $introduction = sanitize($_POST['introduction']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $location = sanitize($_POST['location']);
        $cv_link = sanitize($_POST['cv_link']);
        
        $stmt = $conn->prepare("UPDATE home_content SET profile_image = ?, name = ?, title = ?, introduction = ?, email = ?, phone = ?, location = ?, cv_link = ? WHERE id = 1");
        $stmt->bind_param("ssssssss", $profile_image, $name, $title, $introduction, $email, $phone, $location, $cv_link);
        
        if ($stmt->execute()) {
            $message = 'Home content updated successfully!';
        } else {
            $error = 'Failed to update home content.';
        }
        $stmt->close();
    }
}

// Get home content
$homeResult = $conn->query("SELECT * FROM home_content LIMIT 1");
$home = $homeResult->fetch_assoc();

// Get skills
$skillsResult = $conn->query("SELECT * FROM skills ORDER BY display_order ASC");
$skills = [];
while ($row = $skillsResult->fetch_assoc()) {
    $skills[] = $row;
}

// Handle skill operations
if (isset($_POST['add_skill'])) {
    $name = sanitize($_POST['skill_name']);
    $description = sanitize($_POST['skill_description']);
    $icon = sanitize($_POST['skill_icon']);
    $order = (int)$_POST['skill_order'];
    
    $stmt = $conn->prepare("INSERT INTO skills (name, description, icon, display_order) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $description, $icon, $order);
    $stmt->execute();
    $stmt->close();
    header('Location: /admin/home.php');
    exit;
}

if (isset($_GET['delete_skill'])) {
    $id = (int)$_GET['delete_skill'];
    $stmt = $conn->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: /admin/home.php');
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Home - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="/admin/index.php" class="text-gray-600 hover:text-gray-900">← Back to Dashboard</a>
                    <h1 class="text-xl font-bold text-gray-900">Manage Home Section</h1>
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
            <h2 class="text-2xl font-bold mb-4">Home Content</h2>
            <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Image URL</label>
                        <input type="text" name="profile_image" value="<?php echo htmlspecialchars($home['profile_image']); ?>" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($home['name']); ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($home['title']); ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Introduction</label>
                        <textarea name="introduction" rows="4" class="w-full border rounded px-3 py-2" required><?php echo htmlspecialchars($home['introduction']); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($home['email']); ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($home['phone']); ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($home['location']); ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CV Link</label>
                        <input type="url" name="cv_link" value="<?php echo htmlspecialchars($home['cv_link']); ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <button type="submit" name="update_home" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update Home Content</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">Skills</h2>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-3">Add New Skill</h3>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="skill_name" placeholder="Skill Name" class="border rounded px-3 py-2" required>
                    <input type="text" name="skill_description" placeholder="Description (optional)" class="border rounded px-3 py-2">
                    <select name="skill_icon" class="border rounded px-3 py-2" required>
                        <option value="Server">Server</option>
                        <option value="Award">Award</option>
                        <option value="Terminal">Terminal</option>
                        <option value="Cloud">Cloud</option>
                        <option value="Box">Box</option>
                        <option value="Cpu">Cpu</option>
                        <option value="GitPullRequest">GitPullRequest</option>
                        <option value="Code2">Code2</option>
                    </select>
                    <div class="flex gap-2">
                        <input type="number" name="skill_order" placeholder="Order" value="0" class="border rounded px-3 py-2 flex-1">
                        <button type="submit" name="add_skill" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add</button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($skills as $skill): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($skill['name']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars($skill['description'] ?? ''); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($skill['icon']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $skill['display_order']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="/admin/home.php?delete_skill=<?php echo $skill['id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900">Delete</a>
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



