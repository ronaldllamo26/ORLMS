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
    'super_admin'        => 'Administrator',
    'legislative_staff'  => 'Legislative Staff',
    'committee_member'   => 'Committee Member',
    'sp_member'          => 'SP Member',
];

$currentRole  = $_SESSION['user_role'] ?? '';
$currentName  = $_SESSION['user_name'] ?? 'User';
if ($currentName === 'System Administrator') {
    $currentName = 'Administrator';
}
$roleLabel    = $roleLabels[$currentRole] ?? ucfirst(str_replace('_', ' ', $currentRole));
?>

<nav class="no-print print:hidden fixed top-0 left-0 right-0 h-[56px] bg-primary flex items-center justify-between px-3 md:px-6 z-[1001] shadow-md" id="main-navbar">

    <!-- ── Left Group: Toggle + Branding ──────────────── -->
    <div class="flex items-center gap-2 md:gap-3">
        <!-- Sidebar Hamburger Toggle Button -->
        <button type="button" class="bg-transparent border-0 text-white cursor-pointer flex items-center justify-center p-1.5 rounded hover:bg-white/10 transition-colors focus:outline-none" id="sidebar-toggle-btn" aria-label="Toggle Sidebar">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="4" y1="6" x2="20" y2="6"></line>
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <line x1="4" y1="18" x2="20" y2="18"></line>
            </svg>
        </button>

        <div class="flex items-center gap-2 text-white font-semibold text-base tracking-wide">
            <span class="bg-accent text-primary font-extrabold text-xs px-2 py-0.5 rounded tracking-wider"><?= APP_SHORT ?></span>
            <span class="hidden md:inline text-xs md:text-sm font-medium text-white/90">
                Ordinance and Resolution Lifecycle Management System
            </span>
            <span class="hidden sm:inline md:hidden text-xs font-medium text-white/90">
                CSJDM Portal
            </span>
        </div>
    </div>

    <!-- ── Right: User Info + Logout ────────────────────── -->
    <div class="flex items-center gap-3 sm:gap-5 text-white/90 text-xs">

        <!-- Logged-in user info -->
        <div class="flex items-center gap-2">
            <div class="hidden sm:block text-right">
                <div class="font-medium text-white text-sm">
                    <?= htmlspecialchars($currentName) ?>
                </div>
                <div class="text-[10px] text-accent font-semibold tracking-wider uppercase">
                    <?= htmlspecialchars($roleLabel) ?>
                </div>
            </div>
        </div>

        <!-- Vertical divider -->
        <div class="hidden sm:block w-[1px] h-7 bg-white/20"></div>

        <!-- Logout link -->
        <a href="<?= APP_ROOT_URL ?>/auth/logout"
           class="text-white/85 hover:text-white hover:bg-white/10 border border-white/20 hover:border-white/50 px-2.5 sm:px-3 py-1 rounded transition duration-150 text-[11px] sm:text-xs flex items-center gap-1"
           onclick="return confirm('Are you sure you want to log out?')">
            <svg class="w-3.5 h-3.5 sm:hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="hidden sm:inline">Log Out</span>
            <span class="sm:hidden">Logout</span>
        </a>

    </div>

</nav>
