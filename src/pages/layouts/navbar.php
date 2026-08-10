<?php
/**
 * ORLMS - Top Navigation Bar
 *
 * Displayed on all authenticated pages.
 * Shows system branding on the left and user info + logout on the right.
 *
 * Session variables used:
 *   $_SESSION['user_name'] — full name of logged-in user
 *   $_SESSION['user_role'] — role slug (e.g. 'super_admin')
 */

// Format role for display
$roleLabels = [
    'super_admin'        => 'System Administrator',
    'legislative_staff'  => 'Legislative Staff',
    'committee_member'   => 'Committee Member',
    'sp_member'          => 'SP Member',
];

$currentRole  = $_SESSION['user_role'] ?? '';
$currentName  = $_SESSION['user_name'] ?? 'User';
$roleLabel    = $roleLabels[$currentRole] ?? ucfirst(str_replace('_', ' ', $currentRole));
?>

<nav class="orlms-navbar" id="main-navbar">

    <!-- ── Left Group: Toggle + Branding ──────────────── -->
    <div class="nav-left-group" style="display:flex; align-items:center; gap:8px;">
        <!-- Sidebar Hamburger Toggle Button -->
        <button type="button" class="sidebar-toggle-btn" id="sidebar-toggle-btn" aria-label="Toggle Sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="4" y1="6" x2="20" y2="6"></line>
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <line x1="4" y1="18" x2="20" y2="18"></line>
            </svg>
        </button>

        <div class="brand" style="display:flex; align-items:center; gap:10px;">
            <span class="brand-short"><?= APP_SHORT ?></span>
            <span class="brand-name">
                Ordinance and Resolution Lifecycle Management System
            </span>
        </div>
    </div>

    <!-- ── Right: User Info + Logout ────────────────────── -->
    <div class="nav-right">

        <!-- Logged-in user info -->
        <div class="nav-user">
            <div>
                <div class="nav-user-name">
                    <?= htmlspecialchars($currentName) ?>
                </div>
                <div class="nav-user-role">
                    <?= htmlspecialchars($roleLabel) ?>
                </div>
            </div>
        </div>

        <!-- Vertical divider -->
        <div style="width:1px; height:28px; background:rgba(255,255,255,0.2);"></div>

        <!-- Logout link -->
        <a href="<?= APP_ROOT_URL ?>/auth/logout"
           class="nav-logout"
           onclick="return confirm('Are you sure you want to log out?')">
            Log Out
        </a>

    </div>

</nav>
