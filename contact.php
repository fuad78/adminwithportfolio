<?php
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $messageText = sanitize($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($messageText)) {
        $error = 'All fields are required.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO contact_submissions (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $messageText);
        
        if ($stmt->execute()) {
            $message = 'Message sent successfully!';
            $name = $email = $messageText = '';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

$conn = getDBConnection();
$homeResult = $conn->query("SELECT email, phone, location FROM home_content LIMIT 1");
$home = $homeResult->fetch_assoc();
$conn->close();

$pageTitle = "Contact - KM Fuad Hasan";
include 'includes/header.php';
?>

<section id="contact" class="py-10 bg-gray-50 pt-32">
    <div class="max-w-3xl mx-auto px-4 lg:px-6">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Contact Me</h2>
            <p class="mt-2 text-sm text-gray-600"><b>Let's collaborate or discuss your project</b>.</p>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-2 gap-6 items-center">
            <div class="space-y-4">
                <div class="flex items-center space-x-3 p-3 bg-white shadow-sm rounded-md hover:shadow-md transition-transform duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm"><?php echo htmlspecialchars($home['email']); ?></span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-white shadow-sm rounded-md hover:shadow-md transition-transform duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span class="text-sm"><?php echo htmlspecialchars($home['phone']); ?></span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-white shadow-sm rounded-md hover:shadow-md transition-transform duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-sm"><?php echo htmlspecialchars($home['location']); ?></span>
                </div>
            </div>

            <form method="POST" action="/contact.php" class="bg-white shadow-md p-4 rounded-md space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700">Name</label>
                    <input
                        type="text"
                        name="name"
                        value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                        class="mt-1 w-full p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 transition hover:border-blue-500 text-sm"
                        required
                    />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                        class="mt-1 w-full p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 transition hover:border-blue-500 text-sm"
                        required
                    />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Message</label>
                    <textarea
                        name="message"
                        rows="3"
                        class="mt-1 w-full p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 transition hover:border-blue-500 text-sm"
                        required
                    ><?php echo isset($messageText) ? htmlspecialchars($messageText) : ''; ?></textarea>
                </div>
                <button
                    type="submit"
                    class="w-full flex items-center justify-center space-x-2 px-4 py-2 text-white font-semibold rounded-md shadow-md transition transform text-sm bg-blue-600 hover:bg-blue-700 hover:scale-105"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span>Send Message</span>
                </button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>



