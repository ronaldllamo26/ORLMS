<?php
/**
 * ORLMS - Left Sidebar Navigation
 *
 * Role-aware menu: each role sees only the sections they have access to.
 *
 * Role access summary:
 *   super_admin        — all sections
 *   legislative_staff  — Dashboard, Documents, Publications, Archive, Monitoring, Amendments
 *   committee_member   — Dashboard, Documents, AI Reports, Reviews, Publications, Archive
 *   sp_member          — Dashboard, Documents, AI Reports, Approvals, Publications, Monitoring
 */

$role       = $_SESSION['user_role'] ?? '';
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

/**
 * Helper: Returns 'active' CSS class if the given path is in the current URL.
 *
 * @param  string $path  URL path segment to match
 * @return string        'active' or ''
 */
$isActive = function (string $path) use ($currentUri): string {
    return str_contains($currentUri, $path) ? 'active' : '';
};

/**
 * Helper: Returns true if the current user has any of the given roles.
 *
 * @param  string|array $roles
 * @return bool
 */
$hasRole = function (string|array $roles) use ($role): bool {
    return in_array($role, (array) $roles);
};

$roleLabels = [
    'super_admin'        => 'System Administrator',
    'legislative_staff'  => 'Legislative Staff',
    'committee_member'   => 'Committee Member',
    'sp_member'          => 'SP Member',
];
$currentName  = $_SESSION['user_name'] ?? 'User';
$roleLabel    = $roleLabels[$role] ?? ucfirst(str_replace('_', ' ', $role));
?>

<aside class="orlms-sidebar" id="main-sidebar">

    <!-- Sidebar User Profile Card -->
    <div class="sidebar-user-card">
        <div class="avatar-circle">
            <?= strtoupper(substr(htmlspecialchars($currentName), 0, 1)) ?>
        </div>
        <div class="user-info-text">
            <div class="user-name"><?= htmlspecialchars($currentName) ?></div>
            <div class="user-role"><?= htmlspecialchars($roleLabel) ?></div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         SECTION: MAIN
         ══════════════════════════════════════════ -->
    <span class="sidebar-section-label">Main</span>
    <ul class="sidebar-nav">
        <li>
            <a href="<?= APP_ROOT_URL ?>/dashboard"
               class="<?= $isActive('/dashboard') ?>">
                Dashboard
            </a>
        </li>
    </ul>

    <hr class="sidebar-divider">

    <!-- ══════════════════════════════════════════
         SECTION: DOCUMENTS
         All roles can see documents
         ══════════════════════════════════════════ -->
    <span class="sidebar-section-label">Documents</span>
    <ul class="sidebar-nav">
        <li>
            <a href="<?= APP_ROOT_URL ?>/ordinance"
               class="<?= $isActive('/ordinance') ?>">
                Ordinances
            </a>
        </li>
        <li>
            <a href="<?= APP_ROOT_URL ?>/resolution"
               class="<?= $isActive('/resolution') ?>">
                Resolutions
            </a>
        </li>
    </ul>

    <hr class="sidebar-divider">

    <!-- ══════════════════════════════════════════
         SECTION: VALIDATION & WORKFLOW
         ══════════════════════════════════════════ -->
    <span class="sidebar-section-label">Workflow</span>
    <ul class="sidebar-nav">

        <!-- AI Validation Reports — all roles -->
        <li>
            <a href="<?= APP_ROOT_URL ?>/ai_validation"
               class="<?= $isActive('/ai_validation') ?>">
                AI Validation Reports
            </a>
        </li>

        <!-- Review & Endorsement — committee_member + super_admin -->
        <?php if ($hasRole(['committee_member', 'super_admin'])): ?>
        <li>
            <a href="<?= APP_ROOT_URL ?>/review"
               class="<?= $isActive('/review') ?>">
                Review and Endorsement
            </a>
        </li>
        <?php endif; ?>

        <!-- Approval & Enactment — sp_member + super_admin -->
        <?php if ($hasRole(['sp_member', 'super_admin'])): ?>
        <li>
            <a href="<?= APP_ROOT_URL ?>/approval"
               class="<?= $isActive('/approval') ?>">
                Approval and Enactment
            </a>
        </li>
        <?php endif; ?>

    </ul>

    <hr class="sidebar-divider">

    <!-- ══════════════════════════════════════════
         SECTION: POST-APPROVAL
         ══════════════════════════════════════════ -->
    <span class="sidebar-section-label">Records</span>
    <ul class="sidebar-nav">

        <!-- Publications — all roles -->
        <li>
            <a href="<?= APP_ROOT_URL ?>/publications"
               class="<?= $isActive('/publications') ?>">
                Publications
            </a>
        </li>

        <!-- Archive — all roles -->
        <li>
            <a href="<?= APP_ROOT_URL ?>/archive"
               class="<?= $isActive('/archive') ?>">
                Archive
            </a>
        </li>

        <!-- Monitoring — legislative_staff + sp_member + super_admin -->
        <?php if ($hasRole(['legislative_staff', 'sp_member', 'super_admin'])): ?>
        <li>
            <a href="<?= APP_ROOT_URL ?>/implementation_monitoring"
               class="<?= $isActive('/implementation_monitoring') ?>">
                Implementation Monitoring
            </a>
        </li>
        <?php endif; ?>

        <!-- Amendments — legislative_staff + super_admin -->
        <?php if ($hasRole(['legislative_staff', 'super_admin'])): ?>
        <li>
            <a href="<?= APP_ROOT_URL ?>/amendments"
               class="<?= $isActive('/amendments') ?>">
                Amendments and Revisions
            </a>
        </li>
        <?php endif; ?>

    </ul>

    <!-- ══════════════════════════════════════════
         SECTION: ADMINISTRATION
         super_admin only
         ══════════════════════════════════════════ -->
    <?php if ($hasRole('super_admin')): ?>
    <hr class="sidebar-divider">
    <span class="sidebar-section-label">Administration</span>
    <ul class="sidebar-nav">
        <li>
            <a href="<?= APP_ROOT_URL ?>/user_management"
               class="<?= $isActive('/user_management') ?>">
                User Management
            </a>
        </li>
        <li>
            <a href="<?= APP_ROOT_URL ?>/committee"
               class="<?= $isActive('/committee') ?>">
                Committees
            </a>
        </li>
        <li>
            <a href="<?= APP_ROOT_URL ?>/audit_logs"
               class="<?= $isActive('/audit_logs') ?>">
                Audit Logs
            </a>
        </li>
    </ul>
    <?php endif; ?>

    <!-- Bottom padding so last item isn't cut off -->
    <div style="height: 30px;"></div>

</aside>
