<?php
/**
 * ORLMS - Left Sidebar Navigation (Tailwind CSS)
 *
 * Role-aware menu: each role sees only the sections they have access to.
 */

$role       = $_SESSION['user_role'] ?? '';
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

/**
 * Helper: Returns 'active' if the given path is in the current URL.
 */
$isActive = function (string $path) use ($currentUri): string {
    return str_contains($currentUri, $path) ? 'active' : '';
};

/**
 * Helper: Returns true if the current user has any of the given roles.
 */
$hasRole = function (string|array $roles) use ($role): bool {
    return in_array($role, (array) $roles);
};

/**
 * Helper: Generates Tailwind classes for sidebar navigation links.
 */
$getLinkClasses = function (string $path) use ($isActive): string {
    $active = $isActive($path);
    $base = "block px-[18px] py-2.5 text-[13px] font-medium transition-all duration-150 border-l-[3px] ";
    if (!empty($active)) {
        return $base . "text-white bg-white/10 border-accent";
    }
    return $base . "text-white/70 hover:text-white hover:bg-white/5 border-transparent";
};

$roleLabels = [
    'super_admin'        => 'Administrator',
    'legislative_staff'  => 'Legislative Staff',
    'committee_member'   => 'Committee Member',
    'sp_member'          => 'SP Member',
];
$currentName  = $_SESSION['user_name'] ?? 'User';
if ($currentName === 'System Administrator') {
    $currentName = 'Administrator';
}
$roleLabel    = $roleLabels[$role] ?? ucfirst(str_replace('_', ' ', $role));
?>

<aside class="no-print print:hidden orlms-sidebar bg-primary-dark border-r border-black/20 flex flex-col" id="main-sidebar">

    <!-- Sidebar User Profile Card -->
    <div class="flex items-center gap-3 p-5 border-b border-white/10 bg-black/15">
        <div class="w-10 h-10 rounded-full bg-accent text-primary font-bold text-base flex items-center justify-center shrink-0 shadow-md">
            <?= strtoupper(substr(htmlspecialchars($currentName), 0, 1)) ?>
        </div>
        <div class="flex flex-col min-w-0">
            <div class="font-semibold text-xs text-white truncate"><?= htmlspecialchars($currentName) ?></div>
            <div class="text-[10px] text-accent/80 font-semibold tracking-wider uppercase truncate"><?= htmlspecialchars($roleLabel) ?></div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         SECTION: MAIN
         ══════════════════════════════════════════ -->
    <span class="px-[18px] pt-4 pb-1 text-[10px] font-bold text-white/40 uppercase tracking-wider block">Main</span>
    <ul class="list-none p-0 m-0">
        <li>
            <a href="<?= APP_ROOT_URL ?>/dashboard" class="<?= $getLinkClasses('/dashboard') ?>">
                Dashboard
            </a>
        </li>
    </ul>

    <hr class="border-white/10 my-2 mx-4">

    <!-- ══════════════════════════════════════════
         SECTION: DOCUMENTS
         ══════════════════════════════════════════ -->
    <span class="px-[18px] pt-2 pb-1 text-[10px] font-bold text-white/40 uppercase tracking-wider block">Documents</span>
    <ul class="list-none p-0 m-0">
        <li>
            <a href="<?= APP_ROOT_URL ?>/ordinance" class="<?= $getLinkClasses('/ordinance') ?>">
                Ordinances
            </a>
        </li>
        <li>
            <a href="<?= APP_ROOT_URL ?>/resolution" class="<?= $getLinkClasses('/resolution') ?>">
                Resolutions
            </a>
        </li>
    </ul>

    <hr class="border-white/10 my-2 mx-4">

    <!-- ══════════════════════════════════════════
         SECTION: VALIDATION & WORKFLOW
         ══════════════════════════════════════════ -->
    <span class="px-[18px] pt-2 pb-1 text-[10px] font-bold text-white/40 uppercase tracking-wider block">Workflow</span>
    <ul class="list-none p-0 m-0">
        <li>
            <a href="<?= APP_ROOT_URL ?>/ai_validation" class="<?= $getLinkClasses('/ai_validation') ?>">
                AI Validation Reports
            </a>
        </li>

        <?php if ($hasRole(['committee_member', 'sp_member', 'super_admin'])): ?>
        <li>
            <a href="<?= APP_ROOT_URL ?>/review" class="<?= $getLinkClasses('/review') ?>">
                Review and Endorsement
            </a>
        </li>
        <?php endif; ?>

        <?php if ($hasRole(['sp_member', 'super_admin'])): ?>
        <li>
            <a href="<?= APP_ROOT_URL ?>/approval" class="<?= $getLinkClasses('/approval') ?>">
                Approval and Enactment
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <hr class="border-white/10 my-2 mx-4">

    <!-- ══════════════════════════════════════════
         SECTION: POST-APPROVAL
         ══════════════════════════════════════════ -->
    <span class="px-[18px] pt-2 pb-1 text-[10px] font-bold text-white/40 uppercase tracking-wider block">Records</span>
    <ul class="list-none p-0 m-0">
        <li>
            <a href="<?= APP_ROOT_URL ?>/publications" class="<?= $getLinkClasses('/publications') ?>">
                Publications
            </a>
        </li>

        <?php if ($hasRole(['legislative_staff', 'super_admin'])): ?>
        <li>
            <a href="<?= APP_ROOT_URL ?>/post_enactment" class="<?= $getLinkClasses('/post_enactment') ?>">
                Post-Enactment Tracking
            </a>
        </li>
        <?php endif; ?>

        <li>
            <a href="<?= APP_ROOT_URL ?>/archive" class="<?= $getLinkClasses('/archive') ?>">
                Archive
            </a>
        </li>

        <?php if ($hasRole(['legislative_staff', 'sp_member', 'super_admin'])): ?>
        <li>
            <a href="<?= APP_ROOT_URL ?>/implementation_monitoring" class="<?= $getLinkClasses('/implementation_monitoring') ?>">
                Implementation Monitoring
            </a>
        </li>
        <?php endif; ?>

        <?php if ($hasRole(['legislative_staff', 'super_admin'])): ?>
        <li>
            <a href="<?= APP_ROOT_URL ?>/amendments" class="<?= $getLinkClasses('/amendments') ?>">
                Amendments and Revisions
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <!-- ══════════════════════════════════════════
         SECTION: ADMINISTRATION
         ══════════════════════════════════════════ -->
    <?php if ($hasRole('super_admin')): ?>
    <hr class="border-white/10 my-2 mx-4">
    <span class="px-[18px] pt-2 pb-1 text-[10px] font-bold text-white/40 uppercase tracking-wider block">Administration</span>
    <ul class="list-none p-0 m-0">
        <li>
            <a href="<?= APP_ROOT_URL ?>/user_management" class="<?= $getLinkClasses('/user_management') ?>">
                User Management
            </a>
        </li>
        <li>
            <a href="<?= APP_ROOT_URL ?>/committee" class="<?= $getLinkClasses('/committee') ?>">
                Committees
            </a>
        </li>
        <li>
            <a href="<?= APP_ROOT_URL ?>/audit_logs" class="<?= $getLinkClasses('/audit_logs') ?>">
                Audit Logs
            </a>
        </li>
        <li>
            <a href="<?= APP_ROOT_URL ?>/backup" class="<?= $getLinkClasses('/backup') ?>">
                System Backup
            </a>
        </li>
    </ul>
    <?php endif; ?>

    <!-- Bottom padding so last item isn't cut off -->
    <div class="h-8"></div>

</aside>
