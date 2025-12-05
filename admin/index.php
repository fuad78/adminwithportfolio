<?php
require_once '../config.php';
requireLogin();

$conn = getDBConnection();

// Get statistics
$stats = [
    'contacts' => $conn->query("SELECT COUNT(*) as count FROM contact_submissions")->fetch_assoc()['count'],
    'new_contacts' => $conn->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'new'")->fetch_assoc()['count'],
    'projects' => $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'],
    'blog_posts' => $conn->query("SELECT COUNT(*) as count FROM blog_posts")->fetch_assoc()['count'],
    'services' => $conn->query("SELECT COUNT(*) as count FROM services")->fetch_assoc()['count']
];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .menu-card {
            transition: all 0.3s ease;
        }
        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tachometer-alt text-white"></i>
                        </div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Admin Dashboard</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3 px-4 py-2 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold"><?php echo strtoupper(substr($_SESSION['admin_username'], 0, 1)); ?></span>
                        </div>
                        <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    </div>
                    <a href="/admin/logout.php" class="bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-2 rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-md hover:shadow-lg flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="mb-8 animate-fade-in-up">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
            <p class="text-gray-600">Here's what's happening with your portfolio today.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Total Contacts -->
            <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-envelope text-2xl"></i>
                    </div>
                    <span class="text-white/80 text-sm">Total</span>
                </div>
                <h3 class="text-3xl font-bold mb-1"><?php echo $stats['contacts']; ?></h3>
                <p class="text-white/90 text-sm">Contact Submissions</p>
            </div>

            <!-- New Contacts -->
            <div class="stat-card bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-bell text-2xl"></i>
                    </div>
                    <?php if ($stats['new_contacts'] > 0): ?>
                        <span class="bg-white/30 text-white text-xs px-2 py-1 rounded-full font-semibold">New</span>
                    <?php else: ?>
                        <span class="text-white/80 text-sm">No new</span>
                    <?php endif; ?>
                </div>
                <h3 class="text-3xl font-bold mb-1"><?php echo $stats['new_contacts']; ?></h3>
                <p class="text-white/90 text-sm">New Messages</p>
            </div>

            <!-- Projects -->
            <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-briefcase text-2xl"></i>
                    </div>
                    <span class="text-white/80 text-sm">Active</span>
                </div>
                <h3 class="text-3xl font-bold mb-1"><?php echo $stats['projects']; ?></h3>
                <p class="text-white/90 text-sm">Projects</p>
            </div>

            <!-- Blog Posts -->
            <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-blog text-2xl"></i>
                    </div>
                    <span class="text-white/80 text-sm">Published</span>
                </div>
                <h3 class="text-3xl font-bold mb-1"><?php echo $stats['blog_posts']; ?></h3>
                <p class="text-white/90 text-sm">Blog Posts</p>
            </div>

            <!-- Services -->
            <div class="stat-card bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white animate-fade-in-up" style="animation-delay: 0.5s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-cogs text-2xl"></i>
                    </div>
                    <span class="text-white/80 text-sm">Available</span>
                </div>
                <h3 class="text-3xl font-bold mb-1"><?php echo $stats['services']; ?></h3>
                <p class="text-white/90 text-sm">Services</p>
            </div>
        </div>

        <!-- Management Section -->
        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Content Management</h3>
            <p class="text-gray-600 mb-6">Manage your portfolio content and settings</p>
        </div>

        <!-- Management Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Home Section -->
            <a href="/admin/home.php" class="menu-card group bg-white rounded-xl shadow-md p-6 border border-gray-200 hover:border-blue-500 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="flex items-start space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-home text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">Home Section</h3>
                        <p class="text-gray-600 text-sm">Edit home page content, profile, and skills</p>
                        <div class="mt-4 flex items-center text-blue-600 text-sm font-medium">
                            <span>Manage</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- About Section -->
            <a href="/admin/about.php" class="menu-card group bg-white rounded-xl shadow-md p-6 border border-gray-200 hover:border-purple-500 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="flex items-start space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors">About Section</h3>
                        <p class="text-gray-600 text-sm">Edit about page content and skills</p>
                        <div class="mt-4 flex items-center text-purple-600 text-sm font-medium">
                            <span>Manage</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Services -->
            <a href="/admin/services.php" class="menu-card group bg-white rounded-xl shadow-md p-6 border border-gray-200 hover:border-green-500 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="flex items-start space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-cogs text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">Services</h3>
                        <p class="text-gray-600 text-sm">Manage services list</p>
                        <div class="mt-4 flex items-center text-green-600 text-sm font-medium">
                            <span>Manage</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Projects -->
            <a href="/admin/projects.php" class="menu-card group bg-white rounded-xl shadow-md p-6 border border-gray-200 hover:border-orange-500 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="flex items-start space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-briefcase text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">Projects</h3>
                        <p class="text-gray-600 text-sm">Manage projects portfolio</p>
                        <div class="mt-4 flex items-center text-orange-600 text-sm font-medium">
                            <span>Manage</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Blog -->
            <a href="/admin/blog.php" class="menu-card group bg-white rounded-xl shadow-md p-6 border border-gray-200 hover:border-pink-500 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.5s">
                <div class="flex items-start space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-blog text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-pink-600 transition-colors">Blog</h3>
                        <p class="text-gray-600 text-sm">Manage blog posts</p>
                        <div class="mt-4 flex items-center text-pink-600 text-sm font-medium">
                            <span>Manage</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Contact Submissions -->
            <a href="/admin/contacts.php" class="menu-card group bg-white rounded-xl shadow-md p-6 border border-gray-200 hover:border-red-500 transition-all duration-300 animate-fade-in-up relative" style="animation-delay: 0.6s">
                <?php if ($stats['new_contacts'] > 0): ?>
                    <div class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse">
                        <?php echo $stats['new_contacts']; ?> new
                    </div>
                <?php endif; ?>
                <div class="flex items-start space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-envelope-open text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-red-600 transition-colors">Contact Submissions</h3>
                        <p class="text-gray-600 text-sm">View and manage contact form submissions</p>
                        <div class="mt-4 flex items-center text-red-600 text-sm font-medium">
                            <span>View Messages</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-xl shadow-md p-6 border border-gray-200 animate-fade-in-up" style="animation-delay: 0.7s">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-4">
                <a href="/" target="_blank" class="flex items-center space-x-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Website</span>
                </a>
                <a href="/admin/home.php" class="flex items-center space-x-2 px-4 py-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="fas fa-edit"></i>
                    <span>Edit Profile</span>
                </a>
                <a href="/admin/projects.php" class="flex items-center space-x-2 px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>Add Project</span>
                </a>
                <a href="/admin/blog.php" class="flex items-center space-x-2 px-4 py-2 bg-pink-50 text-pink-600 rounded-lg hover:bg-pink-100 transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>New Blog Post</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
