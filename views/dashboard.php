<?php
use App\Services\Analytics;

$analytics = new Analytics();
$stats = $analytics->getOverallStats();
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener - Futuristic Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/url-shortener/public/assets/css/style.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        neon: {
                            cyan: '#00f5ff',
                            lime: '#c0ff00',
                            purple: '#b026ff'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen relative" style="background: #0a0e1a;">
    <!-- Header - Minimal & Futuristic -->
    <header class="relative z-10 glass-premium border-b border-white border-opacity-10">
        <div class="max-w-6xl mx-auto px-6 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Neon Icon -->
                    <div class="relative">
                        <svg class="w-10 h-10 text-neon-cyan" style="filter: drop-shadow(0 0 10px #00f5ff);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-neon-cyan" style="font-weight: 800; letter-spacing: -0.5px;">
                            URL SHORTENER
                        </h1>
                        <p class="text-sm" style="color: #8b95a8; font-weight: 300;">Shorten your links instantly.</p>
                    </div>
                </div>

                <!-- Dark Mode Toggle (Always on for this design) -->
                <button onclick="darkModeToggle()" class="p-3 rounded-xl transition-all duration-300 hover:scale-110"
                    style="background: rgba(0, 245, 255, 0.1); border: 1px solid rgba(0, 245, 255, 0.3);">
                    <svg class="w-6 h-6 text-neon-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <!-- Stats Cards - Compact & Glowing -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
            <!-- Total URLs -->
            <div class="stat-card glass-premium rounded-2xl stat-icon-cyan">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium"
                            style="color: #8b95a8; text-transform: uppercase; letter-spacing: 1px;">Total URLs</p>
                        <p class="text-4xl font-bold mt-2 text-neon-cyan" id="totalUrls" style="font-weight: 800;">
                            <?php echo $stats['total_urls']; ?>
                        </p>
                    </div>
                    <div class="stat-icon-cyan p-4 rounded-xl">
                        <svg class="w-8 h-8 text-neon-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Clicks -->
            <div class="stat-card glass-premium rounded-2xl stat-icon-lime">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium"
                            style="color: #8b95a8; text-transform: uppercase; letter-spacing: 1px;">Total Clicks</p>
                        <p class="text-4xl font-bold mt-2 text-neon-lime" id="totalClicks" style="font-weight: 800;">
                            <?php echo $stats['total_clicks']; ?>
                        </p>
                    </div>
                    <div class="stat-icon-lime p-4 rounded-xl">
                        <svg class="w-8 h-8 text-neon-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Clicks Today -->
            <div class="stat-card glass-premium rounded-2xl stat-icon-purple">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium"
                            style="color: #8b95a8; text-transform: uppercase; letter-spacing: 1px;">Clicks Today</p>
                        <p class="text-4xl font-bold mt-2" id="clicksToday" style="color: #b026ff; font-weight: 800;">
                            <?php echo $stats['clicks_today']; ?>
                        </p>
                    </div>
                    <div class="stat-icon-purple p-4 rounded-xl">
                        <svg class="w-8 h-8" style="color: #b026ff;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Focus: URL Shortener Form - HERO SECTION -->
        <div class="glass-premium rounded-3xl p-6 sm:p-12 mb-12"
            style="border: 2px solid rgba(0, 245, 255, 0.2); box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white mb-2" style="font-weight: 800; letter-spacing: -1px;">
                    Transform Your Link
                </h2>
                <p class="text-lg" style="color: #8b95a8;">Paste your long URL and watch the magic happen</p>
            </div>

            <div class="max-w-3xl mx-auto space-y-6">
                <!-- Input + Button - Single Row (Stacks on mobile) -->
                <div class="flex flex-col sm:flex-row gap-4 items-stretch flex-input-wrapper">
                    <input type="url" id="urlInput" onkeypress="handleKeyPress(event)"
                        placeholder="https://example.com/your/very/long/url/here"
                        class="input-futuristic flex-1 focus-glow" style="font-size: 18px; padding: 20px 28px;">
                    <button onclick="shortenUrl()" class="btn-neon w-full sm:w-auto"
                        style="padding: 20px 56px; font-size: 18px;">
                        <span style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Shorten
                        </span>
                    </button>
                </div>

                <!-- Loading State -->
                <div id="loading" class="hidden">
                    <div class="flex items-center justify-center gap-3">
                        <div class="spinner-cyberpunk"></div>
                        <span class="text-neon-cyan font-medium" style="font-size: 16px;">Processing your link...</span>
                    </div>
                </div>

                <!-- Error State -->
                <div id="error" class="hidden"
                    style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; padding: 16px; border-radius: 12px; font-weight: 500;">
                </div>

                <!-- Success Result -->
                <div id="result" class="hidden result-success">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium mb-3"
                                style="color: #8b95a8; text-transform: uppercase; letter-spacing: 1px;">
                                ✨ Your Shortened URL
                            </p>
                            <a id="shortUrl" href="#" target="_blank"
                                class="text-2xl font-bold text-neon-cyan hover:text-neon-lime transition-colors"
                                style="font-weight: 800; word-break: break-all; text-shadow: 0 0 10px rgba(0, 245, 255, 0.5);"></a>
                        </div>
                        <button onclick="copyToClipboard(document.getElementById('shortUrl').textContent)"
                            class="btn-copy ml-4">
                            <div class="flex items-center gap-2" style="color: white; font-weight: 600;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Copy
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- URL List Table - Minimalist -->
        <div class="glass-premium rounded-3xl overflow-hidden" style="border: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="px-8 py-6"
                style="background: rgba(0, 0, 0, 0.3); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <h2 class="text-2xl font-bold text-white" style="font-weight: 800; letter-spacing: -0.5px;">Recent URLs
                </h2>
                <p class="text-sm mt-1" style="color: #8b95a8;">Your shortened links history</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-futuristic">
                    <thead>
                        <tr style="background: rgba(0, 0, 0, 0.2);">
                            <th class="px-8 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: #00f5ff;">
                                Short Code
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: #00f5ff;">
                                Original URL
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: #00f5ff;">
                                Clicks
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: #00f5ff;">
                                Created
                            </th>
                        </tr>
                    </thead>
                    <tbody id="urlTableBody" style="color: white;">
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 border-t mt-16"
        style="background: rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.1);">
        <div class="max-w-6xl mx-auto px-6 py-8">
            <p class="text-center text-sm" style="color: #8b95a8;">
                Murat Cem EREN <span class="text-neon-cyan">Modern PHP</span> •
                <span class="text-neon-lime">Futuristic Design</span> •
                PSR-12 Compliant
            </p>
        </div>
    </footer>

    <script src="/url-shortener/public/assets/js/app.js"></script>
</body>

</html>