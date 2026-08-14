<?php
/**
 * ORLMS - Master Layout
 *
 * All authenticated views are rendered inside this layout.
 * The $content variable holds the buffered output of the requested view,
 * captured in Controller::view() via output buffering (ob_start/ob_get_clean).
 *
 * Variables available here (set by Controller::view()):
 * @var string $content     — the rendered view HTML
 * @var string $pageTitle   — optional page title (defaults to APP_NAME)
 */

// Default page title if not set by the controller
$pageTitle = $pageTitle ?? APP_SHORT;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ordinance and Resolution Lifecycle Management System — Legislative Document Validation and Gatekeeper">
    <meta name="robots" content="noindex, nofollow">

    <title><?= htmlspecialchars($pageTitle) ?> | <?= APP_SHORT ?></title>

    <?php if (defined('GA_TRACKING_ID') && !empty(GA_TRACKING_ID)): ?>
    <!-- Google Analytics (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= GA_TRACKING_ID ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= GA_TRACKING_ID ?>');
    </script>
    <?php endif; ?>

    <?php if (defined('CLARITY_PROJECT_ID') && !empty(CLARITY_PROJECT_ID)): ?>
    <!-- Microsoft Clarity -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "<?= CLARITY_PROJECT_ID ?>");
    </script>
    <?php endif; ?>

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

    <!-- ORLMS Global Stylesheet -->
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    <!-- Prevent UI flickering by applying saved sidebar state immediately -->
    <script>
        (function () {
            var state = localStorage.getItem('sidebar-collapsed');
            if (state === 'true' && window.innerWidth > 900) {
                document.body.classList.add('sidebar-collapsed');
            }
        })();
    </script>

    <!-- ═══════════════════════════════════════════════════════
         TOP NAVIGATION BAR
         ═══════════════════════════════════════════════════════ -->
    <?php require_once ROOT . '/src/pages/layouts/navbar.php'; ?>

    <!-- ═══════════════════════════════════════════════════════
         LEFT SIDEBAR
         ═══════════════════════════════════════════════════════ -->
    <?php require_once ROOT . '/src/pages/layouts/sidebar.php'; ?>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- ═══════════════════════════════════════════════════════
         MAIN CONTENT AREA
         ═══════════════════════════════════════════════════════ -->
    <main class="orlms-main">

        <!-- Flash Message (shown once, then cleared) -->
        <?php if (isset($_SESSION['flash'])): ?>
            <?php
                $flash     = $_SESSION['flash'];
                $flashType = htmlspecialchars($flash['type'] ?? 'info');
                $flashMsg  = htmlspecialchars($flash['message'] ?? '');

                // Map type to Tailwind alert classes
                $alertBg = match($flashType) {
                    'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                    'error'   => 'bg-rose-50 text-rose-800 border-rose-200',
                    'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
                    default   => 'bg-sky-50 text-sky-800 border-sky-200',
                };

                // Clear the flash message after displaying
                unset($_SESSION['flash']);
            ?>
            <div class="flex items-center justify-between p-4 mb-6 border rounded-lg <?= $alertBg ?>" id="flash-message" role="alert">
                <span class="text-sm font-medium"><?= $flashMsg ?></span>
                <button type="button" class="text-xl font-bold leading-none hover:opacity-75 focus:outline-none" onclick="this.parentElement.remove()"
                        aria-label="Close">&times;</button>
            </div>
        <?php endif; ?>

        <!-- View Content (rendered by the controller) -->
        <?= $content ?>

    </main>

    <!-- ═══════════════════════════════════════════════════════
         SCRIPTS
         ═══════════════════════════════════════════════════════ -->

    <!-- Bootstrap JS Bundle omitted (Tailwind Migration) -->

    <!-- ORLMS Global JavaScript -->
    <script src="<?= APP_URL ?>/public/js/main.js"></script>

    <!-- Sidebar Collapse and Mobile Responsive Toggle JS -->
    <script>
        (function () {
            var toggleBtn = document.getElementById('sidebar-toggle-btn');
            var overlay   = document.getElementById('sidebar-overlay');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    if (window.innerWidth > 900) {
                        // Desktop collapse
                        document.body.classList.toggle('sidebar-collapsed');
                        var isCollapsed = document.body.classList.contains('sidebar-collapsed');
                        localStorage.setItem('sidebar-collapsed', isCollapsed);
                    } else {
                        // Mobile slide-out drawer
                        document.body.classList.toggle('sidebar-open');
                    }
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function () {
                    document.body.classList.remove('sidebar-open');
                });
            }

            // Close mobile sidebar on window resize
            window.addEventListener('resize', function () {
                if (window.innerWidth > 900) {
                    document.body.classList.remove('sidebar-open');
                }
            });

            // Auto-dismiss flash message after 4 seconds
            var flash = document.getElementById('flash-message');
            if (flash) {
                setTimeout(function () {
                    flash.style.transition = 'opacity 0.4s ease';
                    flash.style.opacity = '0';
                    setTimeout(function () {
                        flash.remove();
                    }, 400);
                }, 4000);
            }
        })();
    </script>

</body>
</html>
