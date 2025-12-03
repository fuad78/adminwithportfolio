<?php
require_once '../config.php';
requireLogin();

$conn = getDBConnection();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $status = sanitize($_POST['status']);
    $stmt = $conn->prepare("UPDATE contact_submissions SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM contact_submissions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: /admin/contacts.php');
    exit;
}

// Get all submissions
$result = $conn->query("SELECT * FROM contact_submissions ORDER BY created_at DESC");
$submissions = [];
while ($row = $result->fetch_assoc()) {
    $submissions[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Submissions - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="/admin/index.php" class="text-gray-600 hover:text-gray-900">← Back to Dashboard</a>
                    <h1 class="text-xl font-bold text-gray-900">Contact Submissions</h1>
                </div>
                <div class="flex items-center">
                    <a href="/admin/logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No submissions yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $submission): ?>
                            <tr class="<?php echo $submission['status'] === 'new' ? 'bg-yellow-50' : ''; ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $submission['id']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($submission['name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($submission['email']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($submission['message']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?php echo $submission['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="text-xs border rounded px-2 py-1">
                                            <option value="new" <?php echo $submission['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                            <option value="read" <?php echo $submission['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                                            <option value="replied" <?php echo $submission['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y g:i A', strtotime($submission['created_at'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button onclick="showMessage('<?php echo htmlspecialchars(addslashes($submission['message'])); ?>', '<?php echo htmlspecialchars($submission['name']); ?>', '<?php echo htmlspecialchars($submission['email']); ?>')" class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                                    <a href="/admin/contacts.php?delete=<?php echo $submission['id']; ?>" onclick="return confirm('Are you sure you want to delete this submission?')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for viewing full message -->
    <div id="messageModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-2" id="modalName"></h3>
                <p class="text-sm text-gray-500 mb-4" id="modalEmail"></p>
                <p class="text-gray-700" id="modalMessage"></p>
                <div class="mt-4">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showMessage(message, name, email) {
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('messageModal').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('messageModal').classList.add('hidden');
        }
    </script>
</body>
</html>



