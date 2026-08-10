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

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- ORLMS Global Stylesheet -->
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body class="">

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

                // Map type to CSS class
                $alertClass = match($flashType) {
                    'success' => 'alert-success',
                    'error'   => 'alert-danger',
                    'warning' => 'alert-warning',
                    default   => 'alert-info',
                };

                // Clear the flash message after displaying
                unset($_SESSION['flash']);
            ?>
            <div class="alert <?= $alertClass ?> alert-dismissible" id="flash-message" role="alert">
                <?= $flashMsg ?>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()"
                        aria-label="Close">&times;</button>
            </div>
        <?php endif; ?>

        <!-- View Content (rendered by the controller) -->
        <?= $content ?>

    </main>

    <!-- ═══════════════════════════════════════════════════════
         SCRIPTS
         ═══════════════════════════════════════════════════════ -->

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
