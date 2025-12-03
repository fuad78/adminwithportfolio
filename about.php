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

<section id="about" class="py-20 bg-white pt-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($about['title']); ?></h2>
            <p class="mt-4 text-lg text-gray-600">
                <b><?php echo htmlspecialchars($about['subtitle']); ?></b>
            </p>
        </div>

        <div class="flex justify-center">
            <div class="text-center max-w-2xl">
                <p class="text-xl text-gray-700 mb-8">
                    <?php echo htmlspecialchars($about['description']); ?>
                </p>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <?php foreach ($skills as $skill): ?>
                        <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg hover:bg-blue-50 transition-all duration-300 transform hover:scale-105">
                            <div class="mb-2 text-blue-600">
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
                            <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($skill['name']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$conn->close();
include 'includes/footer.php';
?>



