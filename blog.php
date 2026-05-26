<?php
require_once 'config.php';
$conn = getDBConnection();

// Get blog posts
$postsResult = $conn->query("SELECT * FROM blog_posts ORDER BY date DESC, created_at DESC");
$posts = [];
while ($row = $postsResult->fetch_assoc()) {
    $posts[] = $row;
}

$selectedPostId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$selectedPost = null;
if ($selectedPostId) {
    foreach ($posts as $post) {
        if ($post['id'] == $selectedPostId) {
            $selectedPost = $post;
            break;
        }
    }
}

$pageTitle = $selectedPost ? htmlspecialchars($selectedPost['title']) . " - Blog" : "Blog - KM Fuad Hasan";
include 'includes/header.php';
?>

<section id="blog" class="py-24 relative overflow-hidden flex flex-col justify-center min-h-screen">
    <!-- Extra Ambient Glows -->
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-indigo-500/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-purple-500/5 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        
        <!-- Section Title (Only shown in list mode) -->
        <?php if (!$selectedPost): ?>
            <div class="text-center mb-16">
                <h2 class="font-display text-4xl md:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-fuchsia-400 bg-clip-text text-transparent mb-3">
                    Tech Insights
                </h2>
                <div class="w-12 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 mx-auto rounded-full mb-4"></div>
                <p class="text-slate-300 font-display font-medium text-lg max-w-xl mx-auto">
                    Experiences and architectures from my journey in systems and DevOps
                </p>
            </div>
        <?php endif; ?>

        <?php if ($selectedPost): ?>
            <!-- Single Post Page -->
            <div class="glass-card rounded-2xl p-6 md:p-8 max-w-3xl mx-auto border border-slate-800/80 shadow-2xl relative">
                <!-- Back Link -->
                <a href="blog.php" class="text-indigo-400 hover:text-indigo-300 font-semibold mb-6 inline-flex items-center space-x-1 group">
                    <span class="group-hover:-translate-x-1 transition-transform">&larr;</span>
                    <span>Back to articles</span>
                </a>
                
                <!-- Main Post Image -->
                <?php if ($selectedPost['image']): ?>
                    <img src="<?php echo htmlspecialchars($selectedPost['image']); ?>" 
                         alt="<?php echo htmlspecialchars($selectedPost['title']); ?>" 
                         class="w-full h-72 object-cover rounded-xl border border-slate-900 mb-6 shadow-lg" />
                <?php endif; ?>

                <!-- Title -->
                <h1 class="font-display text-3xl md:text-4xl font-extrabold text-slate-100 mb-4 tracking-tight leading-tight">
                    <?php echo htmlspecialchars($selectedPost['title']); ?>
                </h1>

                <!-- Meta Details -->
                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 mb-6 border-b border-slate-900 pb-4">
                    <span class="flex items-center space-x-1 bg-slate-950/60 px-2.5 py-1 rounded-md border border-slate-900">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span><?php echo date('F j, Y', strtotime($selectedPost['date'])); ?></span>
                    </span>
                    <span class="flex items-center space-x-1 bg-slate-950/60 px-2.5 py-1 rounded-md border border-slate-900">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>By <?php echo htmlspecialchars($selectedPost['author']); ?></span>
                    </span>
                </div>

                <!-- Text Content -->
                <div class="text-slate-300 leading-relaxed text-base whitespace-pre-line space-y-4 font-light">
                    <?php echo htmlspecialchars($selectedPost['content']); ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Blog List View -->
            <div class="grid md:grid-cols-3 gap-6 max-w-6xl mx-auto">
                <?php foreach ($posts as $post): ?>
                    <a href="blog.php?id=<?php echo $post['id']; ?>" 
                       class="glass-card rounded-2xl overflow-hidden border border-slate-800/80 shadow-2xl hover:border-indigo-500/30 transition-all duration-300 group flex flex-col h-full cursor-pointer">
                        <!-- Post Thumbnail -->
                        <div class="overflow-hidden h-48 relative border-b border-slate-900">
                            <img
                                src="<?php echo htmlspecialchars($post['image']); ?>"
                                alt="<?php echo htmlspecialchars($post['title']); ?>"
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                            />
                        </div>
                        
                        <!-- Content Preview -->
                        <div class="p-5 flex flex-col flex-grow space-y-3">
                            <h3 class="text-lg font-bold text-slate-100 font-display group-hover:text-indigo-300 transition-colors leading-snug">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h3>
                            <p class="text-xs text-slate-400 leading-relaxed flex-grow">
                                <?php echo htmlspecialchars($post['excerpt']); ?>
                            </p>
                            
                            <!-- Card Meta Footer -->
                            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-3 border-t border-slate-950">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-indigo-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span><?php echo date('M j, Y', strtotime($post['date'])); ?></span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-purple-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($post['author']); ?></span>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$conn->close();
include 'includes/footer.php';
?>
