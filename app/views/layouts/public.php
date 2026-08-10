<?php
/**
 * ORLMS - Public Layout
 *
 * Used for public portal pages. Does not render the left navigation sidebar.
 * Displays a clean government branding header with a portal login shortcut.
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

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- ORLMS Global Stylesheet -->
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
    
    <style>
        /* Custom overrides for public layout */
        body {
            background-color: #f8f9fa;
        }
        
        .public-navbar {
            background-color: var(--color-primary);
            height: var(--navbar-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .public-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            font-weight: 700;
            font-size: var(--font-size-lg);
            text-decoration: none;
        }
        
        .public-brand:hover {
            color: #ffffff;
            opacity: 0.9;
        }
        
        .public-brand-short {
            background-color: var(--color-accent);
            color: var(--color-primary);
            font-weight: 800;
            font-size: 13px;
            padding: 3px 8px;
            border-radius: var(--radius-sm);
            letter-spacing: 0.5px;
        }

        .public-brand-name {
            font-size: 13px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
        }

        .public-login-btn {
            color: #ffffff;
            font-size: var(--font-size-sm);
            padding: 5px 14px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: var(--radius);
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .public-login-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: #ffffff;
        }

        .public-container {
            margin-top: calc(var(--navbar-height) + 24px);
            padding: var(--content-padding);
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            min-height: calc(100vh - var(--navbar-height) - 100px);
        }
        
        .public-footer {
            background-color: #ffffff;
            border-top: 1px solid var(--color-border);
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: var(--color-text-muted);
            margin-top: 48px;
        }

        @media (max-width: 768px) {
            .public-brand-name {
                display: none;
            }
            .public-navbar {
                padding: 0 16px;
            }
            .public-container {
                padding: 16px;
            }
        }
    </style>
</head>
<body>

    <!-- Top Public Navbar -->
    <nav class="public-navbar">
        <a href="<?= APP_URL ?>/" class="public-brand">
            <span class="public-brand-short"><?= APP_SHORT ?></span>
            <span class="public-brand-name">
                Ordinance and Resolution Public Portal
            </span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="<?= APP_URL ?>/" class="text-white text-decoration-none fw-semibold d-flex align-items-center gap-1" style="font-size: 13.5px;">
                <i class="bi bi-arrow-left-circle-fill"></i> Back to Home
            </a>
            <a href="<?= APP_ROOT_URL ?>/dashboard" class="public-login-btn">
                Staff Login
            </a>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="public-container">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="public-footer">
        <div>
            © 2026 Ordinance and Resolution Lifecycle Management System (ORLMS). All rights reserved.
        </div>
        <div style="font-size:11px; margin-top:4px;">
            Municipality Legislative Record and AI Gatekeeper Portal.
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
