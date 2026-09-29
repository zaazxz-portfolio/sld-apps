<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SLD Apps') ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #ffedd5 50%, #f1f5f9 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: #1e293b;
        }

        /* Glassmorphism */
        .glass-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Print Styles */
        @media print {
            @page { margin: 0; size: A4 portrait; }
            body { 
                margin: 0; 
                padding: 0; 
                background: white !important; 
            }
            .no-print, header, footer { display: none !important; }
            .glass-card { 
                box-shadow: none !important; 
                border: none !important;
                background: white !important;
            }
            /* Ensure the report looks clean */
            #reportDocument {
                padding: 1.5cm 1cm !important;
                margin: 0 !important;
            }
            
            /* Fix Grid/Flex causing clipping across pages */
            #reportDocument .grid {
                display: block !important;
            }
            #reportDocument .grid > div {
                margin-bottom: 1.5rem !important;
            }
            /* Keep signatures side-by-side */
            #reportDocument .grid.grid-cols-2.text-center {
                display: flex !important;
                justify-content: space-between !important;
            }

            /* Prevent elements from being cut in half across pages */
            #reportDocument table,
            #reportDocument tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            
            #reportDocument .border,
            #reportDocument .bg-slate-50,
            #reportDocument .bg-amber-50\\/60 {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                display: inline-block !important;
                width: 100% !important;
            }
        }
    </style>

    <?= $head ?? '' ?>
</head>
<body class="flex flex-col min-h-screen">

    <!-- Navigation -->
    <header class="glass-nav sticky top-0 z-40 px-4 lg:px-8 py-3">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-tr from-brand-600 to-orange-400 text-white p-2.5 rounded-xl shadow-md flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <a href="/" class="text-xl font-bold tracking-tight text-slate-800 hover:text-brand-600 transition-colors">
                        <?= htmlspecialchars($appName ?? 'SLD Apps') ?>
                    </a>
                    <?php if (!empty($appDesc)): ?>
                        <p class="text-xs text-slate-500 font-medium hidden sm:block"><?= htmlspecialchars($appDesc) ?></p>
                    <?php endif; ?>
                </div>
            </div>

<!-- Nav Links -->
             <nav class="flex items-center gap-1">
                 <a href="/" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-all">Home</a>
                 <a href="/about" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-all">About</a>
                 <a href="/history" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-all">History</a>
             </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 py-8">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white/80 border-t border-slate-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            <p>© <?= date('Y') ?> <?= htmlspecialchars($appName ?? 'SLD Apps') ?>. Crafted with ❤️ by <a href="https://temaningoding.id" class="hover:text-brand-600">Teman Ngoding ID</a>. All rights reserved.</p>
        </div>
    </footer>

    <?= $scripts ?? '' ?>
    <script>lucide.createIcons();</script>

</body>
</html>