<?php
require_once 'config.php';
$conn = getDBConnection();

// Get about content
$aboutResult = $conn->query("SELECT * FROM about_content LIMIT 1");
$about = $aboutResult->fetch_assoc();

// Get skills
$skillsResult = $conn->query("SELECT * FROM skills ORDER BY display_order ASC");
$skills = [];
while ($row = $skillsResult->fetch_assoc()) {
    $skills[] = $row;
}

$pageTitle = "About Me - KM Fuad Hasan";
include 'includes/header.php';
?>

<section id="about" class="py-24 relative overflow-hidden flex flex-col justify-center min-h-screen">
    <!-- Extra Ambient Glows -->
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-purple-500/5 rounded-full blur-[110px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-indigo-500/5 rounded-full blur-[130px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <!-- Section Title -->
        <div class="text-center mb-12">
            <h2 class="font-display text-4xl md:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-fuchsia-400 bg-clip-text text-transparent mb-3">
                <?php echo htmlspecialchars($about['title']); ?>
            </h2>
            <div class="w-12 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 mx-auto rounded-full mb-4"></div>
            <p class="text-slate-300 font-display font-medium text-lg max-w-xl mx-auto">
                <?php echo htmlspecialchars($about['subtitle']); ?>
            </p>
        </div>

        <div class="max-w-4xl mx-auto">
            <!-- Biography Card -->
            <div class="glass-card rounded-2xl p-6 md:p-8 shadow-2xl border border-slate-800/80 mb-12">
                <p class="text-base md:text-lg text-slate-300 leading-relaxed font-light">
                    <?php echo htmlspecialchars($about['description']); ?>
                </p>
            </div>

            <!-- Skills Showcase Header -->
            <div class="text-center mb-8">
                <h3 class="font-display text-2xl font-bold text-slate-100">Professional Skillset</h3>
                <p class="text-xs text-slate-400 mt-1">Tools and infrastructure engineering capabilities</p>
            </div>

            <!-- Skills Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                <?php foreach ($skills as $skill): ?>
                    <div class="glass-card rounded-xl p-5 border border-slate-800/60 hover:border-indigo-500/40 hover:bg-slate-900/60 transition-all duration-300 transform hover:scale-[1.03] hover:-translate-y-0.5 flex flex-col items-center justify-center text-center">
                        <div class="mb-3 text-indigo-400 filter drop-shadow-[0_0_6px_rgba(129,140,248,0.25)]">
                            <?php
                            $iconMap = [
                                'Server' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>',
                                'Cloud' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4 4 0 003 15z"></path></svg>',
                                'Terminal' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
                                'User' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
                                'Box' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
                                'Cpu' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>',
                                'GitPullRequest' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>',
                                'Code2' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>'
                            ];
                            echo $iconMap[$skill['icon']] ?? '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                            ?>
                        </div>
                        <span class="text-sm font-semibold font-display text-slate-200"><?php echo htmlspecialchars($skill['name']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Home link redirect helper note -->
            <div class="mt-8 text-center">
                <p class="text-xs text-slate-500">
                    Looking for detailed profile administration? Visit the <a href="admin/login.php" class="text-indigo-400 hover:text-indigo-300 underline font-medium">Admin Control Panel</a>.
                </p>
            </div>
        </div>
    </div>
</section>

<?php
$conn->close();
include 'includes/footer.php';
?>
