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

<section id="contact" class="py-24 relative overflow-hidden flex flex-col justify-center min-h-screen">
    <!-- Extra Ambient Glows -->
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-fuchsia-500/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-indigo-500/5 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <!-- Section Title -->
        <div class="text-center mb-12">
            <h2 class="font-display text-4xl md:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-fuchsia-400 bg-clip-text text-transparent mb-3">
                Get In Touch
            </h2>
            <div class="w-12 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 mx-auto rounded-full mb-4"></div>
            <p class="text-slate-300 font-display font-medium text-lg max-w-xl mx-auto">
                Let's collaborate or discuss your system infrastructure project
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 items-center max-w-5xl mx-auto">
            <!-- Left Info Panel -->
            <div class="space-y-4">
                <!-- Email Item -->
                <div class="flex items-center space-x-4 p-4 glass-card rounded-2xl border border-slate-800/80 shadow-2xl hover:border-indigo-500/30 transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Email Address</span>
                        <span class="text-sm font-mono text-slate-300"><?php echo htmlspecialchars($home['email']); ?></span>
                    </div>
                </div>
                <!-- Phone Item -->
                <div class="flex items-center space-x-4 p-4 glass-card rounded-2xl border border-slate-800/80 shadow-2xl hover:border-purple-500/30 transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Phone Number</span>
                        <span class="text-sm font-mono text-slate-300"><?php echo htmlspecialchars($home['phone']); ?></span>
                    </div>
                </div>
                <!-- Location Item -->
                <div class="flex items-center space-x-4 p-4 glass-card rounded-2xl border border-slate-800/80 shadow-2xl hover:border-fuchsia-500/30 transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="w-10 h-10 rounded-lg bg-fuchsia-500/10 flex items-center justify-center text-fuchsia-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Location</span>
                        <span class="text-sm font-display text-slate-300"><?php echo htmlspecialchars($home['location']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Right Form Panel -->
            <div class="glass-card rounded-2xl p-6 md:p-8 border border-slate-800/80 shadow-2xl space-y-4">
                
                <?php if ($message): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs px-4 py-3 rounded-xl">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs px-4 py-3 rounded-xl">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="contact.php" class="space-y-4">
                    <!-- Name Input -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Name</label>
                        <input
                            type="text"
                            name="name"
                            value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                            class="w-full bg-slate-950/85 border border-slate-800 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm"
                            placeholder="John Doe"
                            required
                        />
                    </div>
                    <!-- Email Input -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                            class="w-full bg-slate-950/85 border border-slate-800 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm"
                            placeholder="john@example.com"
                            required
                        />
                    </div>
                    <!-- Message Textarea -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Message</label>
                        <textarea
                            name="message"
                            rows="4"
                            class="w-full bg-slate-950/85 border border-slate-800 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm"
                            placeholder="Tell me about your project..."
                            required
                        ><?php echo isset($messageText) ? htmlspecialchars($messageText) : ''; ?></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center space-x-2 py-3.5 text-white font-display font-semibold rounded-xl shadow-lg shadow-indigo-600/20 bg-gradient-to-r from-indigo-600 via-purple-600 to-fuchsia-600 hover:brightness-110 active:scale-[0.98] transition-all duration-300"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Send Message</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
