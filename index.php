<?php
require_once 'config.php';
$conn = getDBConnection();

// Get home content
$homeResult = $conn->query("SELECT * FROM home_content LIMIT 1");
$home = $homeResult->fetch_assoc();

// Get skills
$skillsResult = $conn->query("SELECT * FROM skills ORDER BY display_order ASC");
$skills = [];
while ($row = $skillsResult->fetch_assoc()) {
    $skills[] = $row;
}

$pageTitle = "KM Fuad Hasan - Portfolio";
include 'includes/header.php';
?>

<main class="pt-24 min-h-screen relative overflow-hidden flex flex-col justify-center py-12">
    <!-- Extra Ambient Glows -->
    <div class="absolute top-1/4 left-1/4 w-[450px] h-[450px] bg-indigo-500/5 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[450px] h-[450px] bg-purple-500/5 rounded-full blur-[140px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="text-center transform transition-all duration-1000 translate-y-0 opacity-100">
            <!-- Profile Image Section with Rotating Ring -->
            <div class="relative mb-8 inline-block">
                <!-- Glowing Spinning Ring -->
                <div class="absolute -inset-1.5 rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-fuchsia-500 animate-spin-slow opacity-80 blur-sm"></div>
                <!-- Profile Image -->
                <img
                    src="<?php echo htmlspecialchars($home['profile_image']); ?>"
                    alt="<?php echo htmlspecialchars($home['name']); ?>"
                    class="relative w-44 h-44 rounded-full border-4 border-slate-950 shadow-2xl object-cover transform hover:scale-105 transition-transform duration-500"
                />
            </div>

            <!-- Name and Title -->
            <h1 class="font-display text-5xl md:text-7xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-fuchsia-400 bg-clip-text text-transparent mb-4">
                <?php echo htmlspecialchars($home['name']); ?>
            </h1>
            <h2 class="text-2xl md:text-3xl text-slate-300 mb-8 font-light tracking-wide font-display">
                <?php echo htmlspecialchars($home['title']); ?>
            </h2>

            <!-- Skills Carousel Slider (Glassmorphic) -->
            <div class="max-w-xl mx-auto mb-10 h-32 relative flex items-center justify-center" id="skillsCarousel">
                <?php foreach ($skills as $index => $skill): ?>
                    <div class="skill-item transition-all duration-700 absolute w-full transform <?php echo $index === 0 ? 'opacity-100 scale-100 translate-y-0' : 'opacity-0 scale-95 translate-y-4 pointer-events-none'; ?>" data-index="<?php echo $index; ?>">
                        <div class="glass-card rounded-2xl p-5 border border-slate-800/80 shadow-2xl flex flex-col items-center max-w-md mx-auto">
                            <div class="text-indigo-400 mb-2 filter drop-shadow-[0_0_8px_rgba(129,140,248,0.3)]">
                                <?php
                                $iconMap = [
                                    'Server' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>',
                                    'Award' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>',
                                    'Terminal' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
                                    'Cloud' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4 4 0 003 15z"></path></svg>',
                                    'Box' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
                                    'Cpu' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>',
                                    'GitPullRequest' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>',
                                    'Code2' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>'
                                ];
                                echo $iconMap[$skill['icon']] ?? '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                ?>
                            </div>
                            <h3 class="text-lg font-bold text-slate-100 font-display mb-1"><?php echo htmlspecialchars($skill['name']); ?></h3>
                            <?php if ($skill['description']): ?>
                                <p class="text-xs text-slate-400 text-center leading-relaxed"><?php echo htmlspecialchars($skill['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Introduction Section (Glassmorphic) -->
            <div class="max-w-2xl mx-auto mb-10 glass-card rounded-2xl p-6 md:p-8 shadow-2xl border border-slate-800/80">
                <p class="text-base md:text-lg text-slate-300 leading-relaxed font-light">
                    <?php echo htmlspecialchars($home['introduction']); ?>
                </p>
            </div>

            <!-- Contact Information Grid -->
            <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                <!-- Email -->
                <div class="group glass-card p-4 rounded-xl shadow-md border border-slate-800/60 hover:border-indigo-500/40 hover:bg-slate-900/60 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="w-5 h-5 text-indigo-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm text-slate-300 font-mono"><?php echo htmlspecialchars($home['email']); ?></span>
                    </div>
                </div>
                <!-- Phone -->
                <div class="group glass-card p-4 rounded-xl shadow-md border border-slate-800/60 hover:border-purple-500/40 hover:bg-slate-900/60 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="w-5 h-5 text-purple-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-sm text-slate-300 font-mono"><?php echo htmlspecialchars($home['phone']); ?></span>
                    </div>
                </div>
                <!-- Location -->
                <div class="group glass-card p-4 rounded-xl shadow-md border border-slate-800/60 hover:border-fuchsia-500/40 hover:bg-slate-900/60 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="w-5 h-5 text-fuchsia-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm text-slate-300 font-display"><?php echo htmlspecialchars($home['location']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Call-to-Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="contact.php" class="w-48 py-3.5 bg-gradient-to-r from-indigo-600 via-purple-600 to-fuchsia-600 hover:brightness-110 rounded-xl font-display font-semibold text-white transition-all duration-300 shadow-lg shadow-indigo-600/20 text-center">
                    Get in Touch
                </a>
                <a href="<?php echo htmlspecialchars($home['cv_link']); ?>" target="_blank" rel="noopener noreferrer" 
                   class="w-48 py-3.5 rounded-xl font-display font-semibold text-indigo-400 hover:text-white border border-indigo-500/50 hover:bg-indigo-600/20 hover:border-indigo-400 transition-all duration-300 text-center flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Download CV</span>
                </a>
            </div>
        </div>
    </div>
</main>

<script>
// Skills Carousel Slider Animation
let currentSkill = 0;
const skills = document.querySelectorAll('.skill-item');
const totalSkills = skills.length;

if (totalSkills > 0) {
    setInterval(() => {
        skills[currentSkill].classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        skills[currentSkill].classList.add('opacity-0', 'scale-95', 'translate-y-4', 'pointer-events-none');
        
        currentSkill = (currentSkill + 1) % totalSkills;
        
        skills[currentSkill].classList.remove('opacity-0', 'scale-95', 'translate-y-4', 'pointer-events-none');
        skills[currentSkill].classList.add('opacity-100', 'scale-100', 'translate-y-0');
    }, 4000);
}
</script>

<?php
$conn->close();
include 'includes/footer.php';
?>
