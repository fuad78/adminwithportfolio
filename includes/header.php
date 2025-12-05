<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'KM Fuad Hasan - Portfolio'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'spin-slow': 'spin 3s linear infinite',
                        'fade-in': 'fadeIn 1s ease-in',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 3s linear infinite;
        }
    </style>
</head>
<body class="bg-white">
    <nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 backdrop-blur-lg shadow-lg" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="text-xl font-bold bg-gradient-to-r from-purple-600 to-blue-500 bg-clip-text text-transparent hover:from-blue-500 hover:to-purple-600 transition-all duration-300">
                        KM Fuad Hasan
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-1">
                    <a href="/" class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 group <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'text-white bg-gradient-to-r from-purple-600 to-blue-500' : 'text-gray-600 hover:text-gray-900'; ?>">
                        <span class="relative z-10">Home</span>
                    </a>
                    <a href="/about.php" class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 group <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'text-white bg-gradient-to-r from-purple-600 to-blue-500' : 'text-gray-600 hover:text-gray-900'; ?>">
                        <span class="relative z-10">About</span>
                    </a>
                    <a href="/services.php" class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 group <?php echo (basename($_SERVER['PHP_SELF']) == 'services.php') ? 'text-white bg-gradient-to-r from-purple-600 to-blue-500' : 'text-gray-600 hover:text-gray-900'; ?>">
                        <span class="relative z-10">Services</span>
                    </a>
                    <a href="/projects.php" class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 group <?php echo (basename($_SERVER['PHP_SELF']) == 'projects.php') ? 'text-white bg-gradient-to-r from-purple-600 to-blue-500' : 'text-gray-600 hover:text-gray-900'; ?>">
                        <span class="relative z-10">Projects</span>
                    </a>
                    <a href="/blog.php" class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 group <?php echo (basename($_SERVER['PHP_SELF']) == 'blog.php') ? 'text-white bg-gradient-to-r from-purple-600 to-blue-500' : 'text-gray-600 hover:text-gray-900'; ?>">
                        <span class="relative z-10">Blog</span>
                    </a>
                    <a href="/contact.php" class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 group <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'text-white bg-gradient-to-r from-purple-600 to-blue-500' : 'text-gray-600 hover:text-gray-900'; ?>">
                        <span class="relative z-10">Contact</span>
                    </a>
                    <a href="/vlsm_calculator.php" class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 group <?php echo (basename($_SERVER['PHP_SELF']) == 'vlsm_calculator.php') ? 'text-white bg-gradient-to-r from-purple-600 to-blue-500' : 'text-gray-600 hover:text-gray-900'; ?>">
                        <span class="relative z-10">VLSM Calculator</span>
                    </a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="mobileMenuBtn" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobileMenu" class="md:hidden hidden absolute w-full bg-white/90 backdrop-blur-lg">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="/" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-600 hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-500 hover:text-white">Home</a>
                <a href="/about.php" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-600 hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-500 hover:text-white">About</a>
                <a href="/services.php" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-600 hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-500 hover:text-white">Services</a>
                <a href="/projects.php" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-600 hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-500 hover:text-white">Projects</a>
                <a href="/blog.php" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-600 hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-500 hover:text-white">Blog</a>
                <a href="/contact.php" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-600 hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-500 hover:text-white">Contact</a>
                <a href="/vlsm_calculator.php" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-600 hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-500 hover:text-white">VLSM Calculator</a>
            </div>
        </div>
    </nav>
    <script>
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 20) {
                navbar.classList.add('bg-white/80', 'backdrop-blur-lg', 'shadow-lg');
            } else {
                navbar.classList.remove('bg-white/80', 'backdrop-blur-lg', 'shadow-lg');
            }
        });
    </script>



