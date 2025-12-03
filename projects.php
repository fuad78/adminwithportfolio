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

<section id="projects" class="py-20 bg-white pt-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900">Projects</h2>
            <p class="mt-4 text-lg text-gray-600">
                Showcasing my technical expertise and achievements
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <?php foreach ($projects as $project): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
                    <img
                        src="<?php echo htmlspecialchars($project['image']); ?>"
                        alt="<?php echo htmlspecialchars($project['title']); ?>"
                        class="w-full h-48 object-cover"
                    />
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($project['description']); ?></p>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $tags = explode(',', $project['tags']);
                            foreach ($tags as $tag):
                            ?>
                                <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
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



