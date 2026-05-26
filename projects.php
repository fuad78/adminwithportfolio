<?php
require_once 'config.php';
$conn = getDBConnection();

// Get projects
$projectsResult = $conn->query("SELECT * FROM projects ORDER BY display_order ASC");
$projects = [];
while ($row = $projectsResult->fetch_assoc()) {
    $projects[] = $row;
}

$pageTitle = "Projects - KM Fuad Hasan";
include 'includes/header.php';
?>

<section id="projects" class="py-24 relative overflow-hidden flex flex-col justify-center min-h-screen">
    <!-- Extra Ambient Glows -->
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-fuchsia-500/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-purple-500/5 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <!-- Section Title -->
        <div class="text-center mb-16">
            <h2 class="font-display text-4xl md:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-fuchsia-400 bg-clip-text text-transparent mb-3">
                My Projects
            </h2>
            <div class="w-12 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 mx-auto rounded-full mb-4"></div>
            <p class="text-slate-300 font-display font-medium text-lg max-w-xl mx-auto">
                Showcasing my technical expertise and infrastructure achievements
            </p>
        </div>

        <!-- Projects Grid -->
        <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <?php foreach ($projects as $project): ?>
                <div class="glass-card rounded-2xl overflow-hidden border border-slate-800/80 shadow-2xl hover:border-indigo-500/30 transition-all duration-500 group relative">
                    <!-- Image Wrapper -->
                    <div class="overflow-hidden h-52 relative border-b border-slate-900">
                        <div class="absolute inset-0 bg-slate-950/20 z-10 group-hover:bg-slate-950/10 transition-colors duration-500"></div>
                        <img
                            src="<?php echo htmlspecialchars($project['image']); ?>"
                            alt="<?php echo htmlspecialchars($project['title']); ?>"
                            class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                        />
                    </div>
                    
                    <!-- Content Body -->
                    <div class="p-6 space-y-4">
                        <h3 class="text-xl font-bold text-slate-100 font-display group-hover:text-indigo-300 transition-colors">
                            <?php echo htmlspecialchars($project['title']); ?>
                        </h3>
                        <p class="text-sm text-slate-400 leading-relaxed min-h-[3.5rem]">
                            <?php echo htmlspecialchars($project['description']); ?>
                        </p>
                        
                        <!-- Tags Section -->
                        <div class="flex flex-wrap gap-2 pt-2">
                            <?php
                            $tags = explode(',', $project['tags']);
                            foreach ($tags as $tag):
                                if (trim($tag) === "") continue;
                            ?>
                                <span class="bg-indigo-500/10 text-indigo-300 text-xs font-semibold px-3 py-1 rounded-full border border-indigo-500/20 tracking-wide">
                                    <?php echo htmlspecialchars(trim($tag)); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
$conn->close();
include 'includes/footer.php';
?>
