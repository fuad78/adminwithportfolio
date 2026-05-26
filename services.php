<?php
require_once 'config.php';
$conn = getDBConnection();

// Get services
$servicesResult = $conn->query("SELECT * FROM services ORDER BY display_order ASC");
$services = [];
while ($row = $servicesResult->fetch_assoc()) {
    $services[] = $row;
}

$pageTitle = "Services - KM Fuad Hasan";
include 'includes/header.php';
?>

<section id="services" class="py-24 relative overflow-hidden flex flex-col justify-center min-h-screen">
    <!-- Extra Ambient Glows -->
    <div class="absolute top-1/4 left-1/4 w-80 h-80 bg-blue-500/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-fuchsia-500/5 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <!-- Section Title -->
        <div class="text-center mb-16">
            <h2 class="font-display text-4xl md:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-fuchsia-400 bg-clip-text text-transparent mb-3">
                My Services
            </h2>
            <div class="w-12 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 mx-auto rounded-full mb-4"></div>
            <p class="text-slate-300 font-display font-medium text-lg max-w-xl mx-auto">
                Comprehensive IT solutions for your infrastructure needs
            </p>
        </div>

        <!-- Services Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($services as $service): ?>
                <div class="glass-card p-6 rounded-2xl border border-slate-800/80 cursor-pointer hover:scale-[1.04] transition-all duration-300 hover:shadow-2xl shadow-slate-950/40 relative overflow-hidden group">
                    <!-- Glow effect on hover -->
                    <div class="absolute -inset-px bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none rounded-2xl"></div>
                    
                    <div class="relative space-y-4">
                        <!-- Icon -->
                        <div class="text-indigo-400 filter drop-shadow-[0_0_8px_rgba(129,140,248,0.25)] group-hover:scale-115 transition-transform duration-300 inline-block">
                            <?php
                            $iconMap = [
                                'Server' => '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>',
                                'Cloud' => '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4 4 0 003 15z"></path></svg>',
                                'Shield' => '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
                                'Settings' => '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'
                            ];
                            echo $iconMap[$service['icon']] ?? '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                            ?>
                        </div>
                        
                        <!-- Title & Description -->
                        <h3 class="text-lg font-bold text-slate-100 font-display group-hover:text-indigo-300 transition-colors">
                            <?php echo htmlspecialchars($service['title']); ?>
                        </h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            <?php echo htmlspecialchars($service['description']); ?>
                        </p>
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
