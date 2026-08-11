<?php
/**
 * ORLMS - Public Layout (Tailwind CSS)
 *
 * Used for public portal pages. Does not render the left navigation sidebar.
 * Displays a clean government branding header with a portal login shortcut.
 *
 * @var string $content
 * @var string|null $pageTitle
 */

$pageTitle = $pageTitle ?? APP_SHORT;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ordinance and Resolution Public Portal — Access officially published documents.">
    
    <title><?= htmlspecialchars($pageTitle) ?> | Public Registry</title>

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#1a3a5c',
                            dark: '#122840',
                            light: '#2c4a6e',
                        },
                        accent: {
                            DEFAULT: '#c9a84c',
                            dark: '#a8873a',
                            light: '#d4b96a',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <!-- Top Public Navbar -->
    <nav class="public-navbar no-print print:hidden fixed top-0 left-0 right-0 h-[56px] bg-primary flex items-center justify-between px-8 shadow-[0_2px_4px_rgba(0,0,0,0.15)] z-[1000]">
        <a href="<?= APP_URL ?>/" class="flex items-center gap-3 no-underline text-white font-bold text-lg">
            <span class="bg-accent text-primary font-extrabold text-[13px] px-2 py-0.5 rounded tracking-wider"><?= APP_SHORT ?></span>
            <span class="hidden sm:inline text-[13px] font-medium text-white/85">
                Ordinance and Resolution Public Portal
            </span>
        </a>
        <div class="flex items-center gap-4">
            <a href="<?= APP_URL ?>/" class="text-white text-xs font-semibold hover:text-white/85 transition no-underline flex items-center gap-1">
                <i class="bi bi-arrow-left-circle-fill"></i> Back to Home
            </a>
            <a href="<?= APP_ROOT_URL ?>/dashboard" class="text-white text-xs font-semibold hover:bg-white/10 border border-white/40 hover:border-white px-3 py-1.5 rounded transition no-underline">
                Staff Login
            </a>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="mt-[80px] p-6 max-w-[1200px] mx-auto min-h-[calc(100vh-180px)]">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="public-footer no-print print:hidden bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 mt-12">
        <div>
            © 2026 Ordinance and Resolution Lifecycle Management System (ORLMS). All rights reserved.
        </div>
        <div class="text-[11px] text-slate-400 mt-1">
            Municipality Legislative Record and AI Gatekeeper Portal.
        </div>
    </footer>

</body>
</html>
