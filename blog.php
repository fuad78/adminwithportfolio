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

<section id="blog" class="py-20 bg-gray-50 pt-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900">Blog</h2>
            <p class="mt-4 text-lg text-gray-600">
                <b>Insights and experiences from my journey in IT</b>
            </p>
        </div>

        <?php if ($selectedPost): ?>
            <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl mx-auto">
                <a href="/blog.php" class="text-blue-600 mb-4 inline-block hover:underline">← Back to blog list</a>
                <img src="<?php echo htmlspecialchars($selectedPost['image']); ?>" alt="<?php echo htmlspecialchars($selectedPost['title']); ?>" class="w-full h-64 object-cover rounded-lg mb-4" />
                <h3 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($selectedPost['title']); ?></h3>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="mr-4"><?php echo date('F j, Y', strtotime($selectedPost['date'])); ?></span>
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span><?php echo htmlspecialchars($selectedPost['author']); ?></span>
                </div>
                <div class="text-gray-700 whitespace-pre-line"><?php echo htmlspecialchars($selectedPost['content']); ?></div>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($posts as $post): ?>
                    <a href="/blog.php?id=<?php echo $post['id']; ?>" class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl cursor-pointer">
                        <img
                            src="<?php echo htmlspecialchars($post['image']); ?>"
                            alt="<?php echo htmlspecialchars($post['title']); ?>"
                            class="w-full h-48 object-cover"
                        />
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="mr-4"><?php echo date('F j, Y', strtotime($post['date'])); ?></span>
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span><?php echo htmlspecialchars($post['author']); ?></span>
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



