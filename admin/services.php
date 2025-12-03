<?php
require_once '../config.php';
requireLogin();

$conn = getDBConnection();
$message = '';
$error = '';

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_service'])) {
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $icon = sanitize($_POST['icon']);
        $order = (int)$_POST['display_order'];
        
        $stmt = $conn->prepare("INSERT INTO services (title, description, icon, display_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $description, $icon, $order);
        
        if ($stmt->execute()) {
            $message = 'Service added successfully!';
        } else {
            $error = 'Failed to add service.';
        }
        $stmt->close();
    } elseif (isset($_POST['update_service'])) {
        $id = (int)$_POST['id'];
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $icon = sanitize($_POST['icon']);
        $order = (int)$_POST['display_order'];
        
        $stmt = $conn->prepare("UPDATE services SET title = ?, description = ?, icon = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("sssii", $title, $description, $icon, $order, $id);
        
        if ($stmt->execute()) {
            $message = 'Service updated successfully!';
        } else {
            $error = 'Failed to update service.';
        }
        $stmt->close();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: /admin/services.php');
    exit;
}

// Get service for editing
$editService = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = $conn->query("SELECT * FROM services WHERE id = $id");
    $editService = $result->fetch_assoc();
}

// Get all services
$servicesResult = $conn->query("SELECT * FROM services ORDER BY display_order ASC");
$services = [];
while ($row = $servicesResult->fetch_assoc()) {
    $services[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="/admin/index.php" class="text-gray-600 hover:text-gray-900">← Back to Dashboard</a>
                    <h1 class="text-xl font-bold text-gray-900">Manage Services</h1>
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
            <h2 class="text-2xl font-bold mb-4"><?php echo $editService ? 'Edit Service' : 'Add New Service'; ?></h2>
            <form method="POST">
                <?php if ($editService): ?>
                    <input type="hidden" name="id" value="<?php echo $editService['id']; ?>">
                <?php endif; ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" value="<?php echo $editService ? htmlspecialchars($editService['title']) : ''; ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                        <select name="icon" class="w-full border rounded px-3 py-2" required>
                            <option value="Server" <?php echo ($editService && $editService['icon'] === 'Server') ? 'selected' : ''; ?>>Server</option>
                            <option value="Cloud" <?php echo ($editService && $editService['icon'] === 'Cloud') ? 'selected' : ''; ?>>Cloud</option>
                            <option value="Shield" <?php echo ($editService && $editService['icon'] === 'Shield') ? 'selected' : ''; ?>>Shield</option>
                            <option value="Settings" <?php echo ($editService && $editService['icon'] === 'Settings') ? 'selected' : ''; ?>>Settings</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input type="number" name="display_order" value="<?php echo $editService ? $editService['display_order'] : '0'; ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full border rounded px-3 py-2" required><?php echo $editService ? htmlspecialchars($editService['description']) : ''; ?></textarea>
                    </div>
                </div>
                <button type="submit" name="<?php echo $editService ? 'update_service' : 'add_service'; ?>" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <?php echo $editService ? 'Update Service' : 'Add Service'; ?>
                </button>
                <?php if ($editService): ?>
                    <a href="/admin/services.php" class="mt-4 ml-2 bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 inline-block">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">All Services</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($service['title']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars($service['description']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($service['icon']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $service['display_order']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="/admin/services.php?edit=<?php echo $service['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <a href="/admin/services.php?delete=<?php echo $service['id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900">Delete</a>
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



