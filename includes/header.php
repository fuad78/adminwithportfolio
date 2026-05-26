<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'KM Fuad Hasan - Portfolio'; ?></title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                        mono: ['Fira Code', 'monospace'],
                    },
                    animation: {
                        'spin-slow': 'spin 12s linear infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 5px rgba(99, 102, 241, 0.2), 0 0 10px rgba(99, 102, 241, 0.1)' },
                            '100%': { boxShadow: '0 0 20px rgba(99, 102, 241, 0.6), 0 0 35px rgba(168, 85, 247, 0.4)' }
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #030712; /* slate-950 */
            color: #f3f4f6; /* gray-100 */
        }
        .font-display {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #030712;
        }
        ::-webkit-scrollbar-thumb {
            background: #1f2937;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #374151;
        }
        /* Glassmorphism styling helper */
        .glass-card {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-nav {
            background: rgba(3, 7, 18, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        /* Text selection */
        ::selection {
            background: rgba(139, 92, 246, 0.3);
            color: #ffffff;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col selection:bg-purple-500/30 selection:text-white bg-slate-950 text-slate-100">
    
    <!-- Radial Ambient Glow Backgrounds -->
    <div class="fixed top-0 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-[128px] pointer-events-none z-0"></div>
    <div class="fixed bottom-1/4 right-1/4 w-[400px] h-[400px] bg-purple-500/10 rounded-full blur-[140px] pointer-events-none z-0"></div>
    <div class="fixed top-1/3 right-10 w-72 h-72 bg-fuchsia-500/5 rounded-full blur-[100px] pointer-events-none z-0"></div>

    <!-- Header Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass-nav" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="index.php" class="font-display text-xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-fuchsia-400 bg-clip-text text-transparent hover:brightness-110 transition-all duration-300">
                        KM Fuad Hasan
                    </a>
                </div>
                
                <!-- Desktop Nav Menu -->
                <div class="hidden md:flex items-center space-x-1">
                    <?php
                    $current_page = basename($_SERVER['PHP_SELF']);
                    $nav_items = [
                        'index.php' => 'Home',
                        'about.php' => 'About',
                        'services.php' => 'Services',
                        'projects.php' => 'Projects',
                        'blog.php' => 'Blog',
                        'contact.php' => 'Contact',
                        'vlsm_calculator.php' => 'VLSM Calc',
                        'password_generator.php' => 'Pass Gen'
                    ];
                    
                    foreach ($nav_items as $url => $label):
                        $is_active = ($current_page == $url) || ($url == 'index.php' && $current_page == '');
                        $active_class = $is_active 
                            ? 'text-white bg-gradient-to-r from-indigo-600 to-purple-600 shadow-md shadow-indigo-600/20' 
                            : 'text-slate-400 hover:text-white hover:bg-white/5';
                    ?>
                        <a href="<?php echo $url; ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 font-display flex items-center space-x-1 <?php echo $active_class; ?>">
                            <span><?php echo $label; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobileMenuBtn" class="text-slate-400 hover:text-white transition-colors duration-200 focus:outline-none p-2 rounded-lg hover:bg-white/5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div id="mobileMenu" class="md:hidden hidden absolute w-full glass-nav border-t border-slate-900 shadow-xl">
            <div class="px-3 pt-2 pb-4 space-y-1">
                <?php
                foreach ($nav_items as $url => $label):
                    $is_active = ($current_page == $url) || ($url == 'index.php' && $current_page == '');
                    $active_class = $is_active 
                        ? 'text-white bg-indigo-600' 
                        : 'text-slate-400 hover:text-white hover:bg-white/5';
                ?>
                    <a href="<?php echo $url; ?>" class="block px-4 py-3 rounded-lg text-base font-medium transition-all duration-200 font-display <?php echo $active_class; ?>">
                        <?php echo $label; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <!-- Content Wrapper spacing for Fixed Nav -->
    <div class="flex-grow">
