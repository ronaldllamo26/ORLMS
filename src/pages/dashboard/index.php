<?php
/**
 * ORLMS - Dashboard View (Tailwind CSS)
 *
 * @var array $stats
 * @var array $recentOrdinances
 * @var array $recentResolutions
 * @var array $chartStatusData
 * @var array $committeeStats
 * @var string $userRole
 * @var array $roleData
 */

// Initialize variables to prevent IDE/Static analysis warnings
$stats = $stats ?? [];
$recentOrdinances = $recentOrdinances ?? [];
$recentResolutions = $recentResolutions ?? [];
$chartStatusData = $chartStatusData ?? [];
$committeeStats = $committeeStats ?? [];
$userRole = $userRole ?? $_SESSION['user_role'] ?? 'legislative_staff';
$roleData = $roleData ?? [];
$userName = $_SESSION['user_name'] ?? 'Colleague';
if ($userName === 'System Administrator' || $userName === 'Administrator') {
    $displayName = 'System Administrator';
} else {
    $displayName = $userName;
}

// Helper: returns Tailwind CSS badge classes based on document status
function statusBadge(string $status): string {
    return match($status) {
        'draft'        => 'bg-slate-100 text-slate-700 border-slate-200',
        'submitted'    => 'bg-blue-50 text-blue-700 border-blue-200',
        'under_review' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'endorsed'     => 'bg-purple-50 text-purple-700 border-purple-200',
        'approved'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'enacted'      => 'bg-teal-50 text-teal-700 border-teal-200',
        'published'    => 'bg-amber-50 text-amber-700 border-amber-200',
        'rejected'     => 'bg-rose-50 text-rose-700 border-rose-200',
        'archived'     => 'bg-gray-150 text-gray-700 border-gray-200',
        'implemented'  => 'bg-green-50 text-green-700 border-green-200',
        'amended'      => 'bg-orange-50 text-orange-700 border-orange-200',
        default        => 'bg-slate-100 text-slate-700 border-slate-200',
    };
}
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     ROLE-TAILORED HERO WORKSPACE SECTION
     ═══════════════════════════════════════════════════════════════════════ -->

<?php if ($userRole === 'super_admin'): ?>
<!-- ── [1] SYSTEM ADMINISTRATOR CONTROL CENTER ────────────────────────── -->
<div class="mb-8 bg-gradient-to-r from-slate-900 via-slate-850 to-slate-800 rounded-xl p-6 text-white shadow-md border border-slate-700/50">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b border-white/10">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[11px] font-semibold uppercase tracking-wider rounded-full mb-2">
                <i class="bi bi-shield-check"></i> System Administrator Control Center
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white">Welcome back, <?= htmlspecialchars($displayName) ?></h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                Technical operations, user access control, security audit logs, and database maintenance for the Legislative Management System.
            </p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="<?= APP_ROOT_URL ?>/user_management" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white text-slate-900 hover:bg-slate-100 text-xs font-semibold rounded-lg shadow-sm transition">
                <i class="bi bi-people-fill text-primary"></i> User Management
            </a>
            <a href="<?= APP_ROOT_URL ?>/audit_logs" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-lg border border-white/20 transition">
                <i class="bi bi-clock-history"></i> Audit Logs
            </a>
            <a href="<?= APP_ROOT_URL ?>/backup" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-semibold rounded-lg shadow-sm transition">
                <i class="bi bi-database-down"></i> Backup DB
            </a>
        </div>
    </div>

    <!-- Admin Metric Highlights -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-5">
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Active User Accounts</div>
            <div class="text-xl sm:text-2xl font-extrabold text-white mt-1"><?= number_format($roleData['total_users'] ?? 0) ?></div>
            <div class="text-[11px] text-slate-400 mt-0.5">Authorized staff & councilors</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Standing Committees</div>
            <div class="text-xl sm:text-2xl font-extrabold text-white mt-1"><?= number_format($roleData['total_committees'] ?? 0) ?></div>
            <div class="text-[11px] text-slate-400 mt-0.5">Active legislative bodies</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">System Audit Trail</div>
            <div class="text-xl sm:text-2xl font-extrabold text-emerald-400 mt-1">Active</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Real-time action recording</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Database Status</div>
            <div class="text-xl sm:text-2xl font-extrabold text-teal-300 mt-1">Operational</div>
            <div class="text-[11px] text-slate-400 mt-0.5">MariaDB / MySQL Online</div>
        </div>
    </div>

    <!-- Admin Mini Audit Stream -->
    <?php if (!empty($roleData['recent_logs'])): ?>
    <div class="mt-5 pt-4 border-t border-white/10">
        <div class="flex items-center justify-between mb-2.5">
            <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                <i class="bi bi-broadcast text-emerald-400"></i> Recent System Transactions
            </span>
            <a href="<?= APP_ROOT_URL ?>/audit_logs" class="text-[11px] text-amber-400 hover:text-amber-300 underline font-medium">View full audit trail &rarr;</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            <?php foreach (array_slice($roleData['recent_logs'], 0, 3) as $log): ?>
            <div class="bg-white/5 border border-white/5 rounded px-3 py-2 text-xs flex items-center justify-between">
                <div class="truncate mr-2">
                    <span class="font-semibold text-white"><?= htmlspecialchars($log['user_name'] ?? 'System') ?></span>
                    <span class="text-slate-400">· <?= htmlspecialchars($log['action'] ?? 'Action') ?> on <?= htmlspecialchars($log['table_name'] ?? '') ?></span>
                </div>
                <span class="text-[10px] text-slate-400 shrink-0"><?= date('H:i', strtotime($log['created_at'])) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($userRole === 'legislative_staff'): ?>
<!-- ── [2] LEGISLATIVE STAFF DRAFTING & VALIDATION DESK ────────────────── -->
<div class="mb-8 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-xl p-6 text-white shadow-md border border-blue-700/40">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b border-white/10">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-blue-500/20 border border-blue-400/30 text-blue-200 text-[11px] font-semibold uppercase tracking-wider rounded-full mb-2">
                <i class="bi bi-file-earmark-code"></i> Legislative Staff Drafting Desk
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white">Welcome back, <?= htmlspecialchars($displayName) ?></h1>
            <p class="text-xs text-blue-100/80 mt-1 max-w-2xl">
                Encoding workspace: Prepare drafts, run AI Universal Text Compliance Scans, and monitor legislative post-enactment records.
            </p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="<?= APP_ROOT_URL ?>/ordinance/create" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <i class="bi bi-plus-circle"></i> New Ordinance
            </a>
            <a href="<?= APP_ROOT_URL ?>/resolution/create" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white text-slate-900 hover:bg-slate-100 text-xs font-semibold rounded-lg shadow-sm transition">
                <i class="bi bi-plus-circle"></i> New Resolution
            </a>
            <a href="<?= APP_ROOT_URL ?>/ai_validation" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-lg border border-white/20 transition">
                <i class="bi bi-robot"></i> AI Validation
            </a>
        </div>
    </div>

    <!-- Staff Metric Highlights -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-5">
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-blue-200 tracking-wider">Active Drafts in Queue</div>
            <div class="text-xl sm:text-2xl font-extrabold text-amber-300 mt-1"><?= number_format($roleData['draft_count'] ?? 0) ?></div>
            <div class="text-[11px] text-blue-100/60 mt-0.5">Work-in-progress files</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-blue-200 tracking-wider">AI Flags Pending Fix</div>
            <div class="text-xl sm:text-2xl font-extrabold <?= ($roleData['flagged_ai_count'] ?? 0) > 0 ? 'text-rose-400' : 'text-emerald-400' ?> mt-1">
                <?= number_format($roleData['flagged_ai_count'] ?? 0) ?>
            </div>
            <div class="text-[11px] text-blue-100/60 mt-0.5">Completeness / Similarity flags</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-blue-200 tracking-wider">Awaiting Committee Review</div>
            <div class="text-xl sm:text-2xl font-extrabold text-white mt-1"><?= number_format($stats['pending_review'] ?? 0) ?></div>
            <div class="text-[11px] text-blue-100/60 mt-0.5">Submitted for deliberation</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-blue-200 tracking-wider">Official Enacted Laws</div>
            <div class="text-xl sm:text-2xl font-extrabold text-teal-300 mt-1"><?= number_format($stats['enacted'] ?? 0) ?></div>
            <div class="text-[11px] text-blue-100/60 mt-0.5">Passed and recorded</div>
        </div>
    </div>

    <!-- Staff Priority Draft Queue -->
    <?php if (!empty($roleData['staff_queue'])): ?>
    <div class="mt-5 pt-4 border-t border-white/10">
        <div class="flex items-center justify-between mb-2.5">
            <span class="text-[11px] font-bold text-blue-200 uppercase tracking-wider flex items-center gap-1.5">
                <i class="bi bi-pencil-square text-amber-400"></i> Priority Drafts Needing Attention
            </span>
            <span class="text-[11px] text-blue-200/70">Click to view & edit</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
            <?php foreach ($roleData['staff_queue'] as $doc): ?>
            <a href="<?= APP_ROOT_URL ?>/<?= $doc['type'] ?>/view/<?= $doc['id'] ?>" class="bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded p-3 text-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-amber-300 uppercase text-[10px] tracking-wider"><?= htmlspecialchars($doc['doc_no'] ?? 'DRAFT') ?></span>
                        <span class="px-1.5 py-0.2 text-[9px] font-semibold bg-white/10 rounded uppercase">Draft</span>
                    </div>
                    <div class="text-white font-medium line-clamp-1"><?= htmlspecialchars($doc['title'] ?? 'Untitled') ?></div>
                </div>
                <div class="mt-2 text-[10px] text-blue-200/60">Updated: <?= date('M d, Y', strtotime($doc['updated_at'])) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($userRole === 'committee_member'): ?>
<!-- ── [3] COMMITTEE MEMBER REVIEW & ENDORSEMENT DESK ──────────────────── -->
<div class="mb-8 bg-gradient-to-r from-purple-950 via-indigo-950 to-slate-900 rounded-xl p-6 text-white shadow-md border border-purple-700/40">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b border-white/10">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-purple-500/20 border border-purple-400/30 text-purple-200 text-[11px] font-semibold uppercase tracking-wider rounded-full mb-2">
                <i class="bi bi-check2-circle"></i> Committee Review & Endorsement Desk
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white">Welcome, <?= htmlspecialchars($displayName) ?></h1>
            <p class="text-xs text-purple-100/80 mt-1 max-w-2xl">
                Examine referred legislative measures, conduct hearings and consultations, and submit formal Committee Reports & Endorsements.
            </p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="<?= APP_ROOT_URL ?>/review" class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <i class="bi bi-clipboard-check"></i> Open Review Desk
            </a>
            <a href="<?= APP_ROOT_URL ?>/ordinance" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-lg border border-white/20 transition">
                <i class="bi bi-file-earmark-text"></i> Browse Ordinances
            </a>
        </div>
    </div>

    <!-- Committee Metric Highlights -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-5">
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-purple-200 tracking-wider">Referred to Committees</div>
            <div class="text-xl sm:text-2xl font-extrabold text-amber-300 mt-1"><?= number_format($roleData['under_review_count'] ?? 0) ?></div>
            <div class="text-[11px] text-purple-100/60 mt-0.5">Under examination</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-purple-200 tracking-wider">Awaiting Committee Action</div>
            <div class="text-xl sm:text-2xl font-extrabold text-purple-300 mt-1"><?= number_format($roleData['ready_endorse_count'] ?? 0) ?></div>
            <div class="text-[11px] text-purple-100/60 mt-0.5">Hearings & report preparation</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-purple-200 tracking-wider">Officially Enacted Laws</div>
            <div class="text-xl sm:text-2xl font-extrabold text-emerald-400 mt-1"><?= number_format($stats['enacted'] ?? 0) ?></div>
            <div class="text-[11px] text-purple-100/60 mt-0.5">Enacted ordinances</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-purple-200 tracking-wider">Citywide Measures</div>
            <div class="text-xl sm:text-2xl font-extrabold text-teal-300 mt-1"><?= number_format($stats['total_ordinances'] + $stats['total_resolutions']) ?></div>
            <div class="text-[11px] text-purple-100/60 mt-0.5">Total registered in council</div>
        </div>
    </div>

    <!-- Committee Queue Widget -->
    <?php if (!empty($roleData['committee_queue'])): ?>
    <div class="mt-5 pt-4 border-t border-white/10">
        <div class="flex items-center justify-between mb-2.5">
            <span class="text-[11px] font-bold text-purple-200 uppercase tracking-wider flex items-center gap-1.5">
                <i class="bi bi-hourglass-split text-amber-400"></i> Measures Requiring Committee Deliberation
            </span>
            <a href="<?= APP_ROOT_URL ?>/review" class="text-[11px] text-purple-300 hover:text-white underline font-medium">Go to Review Desk &rarr;</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
            <?php foreach ($roleData['committee_queue'] as $doc): ?>
            <a href="<?= APP_ROOT_URL ?>/<?= $doc['type'] ?>/view/<?= $doc['id'] ?>" class="bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded p-3 text-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-purple-300 uppercase text-[10px] tracking-wider"><?= htmlspecialchars($doc['doc_no']) ?></span>
                        <span class="px-1.5 py-0.2 text-[9px] font-semibold bg-purple-500/30 text-purple-200 rounded uppercase"><?= htmlspecialchars(str_replace('_', ' ', $doc['status'])) ?></span>
                    </div>
                    <div class="text-white font-medium line-clamp-1"><?= htmlspecialchars($doc['title']) ?></div>
                </div>
                <div class="mt-2 text-[10px] text-purple-200/60 truncate">Committee: <?= htmlspecialchars($doc['committee_name']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($userRole === 'sp_member'): ?>
<!-- ── [4] SP COUNCIL MEMBER PLENARY & ENACTMENT DESK ─────────────────── -->
<div class="mb-8 bg-gradient-to-r from-emerald-950 via-teal-950 to-slate-900 rounded-xl p-6 text-white shadow-md border border-emerald-700/40">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b border-white/10">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-[11px] font-semibold uppercase tracking-wider rounded-full mb-2">
                <i class="bi bi-bank"></i> Plenary & Council Session Desk (Sangguniang Panlungsod)
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white">Welcome, Honorable <?= htmlspecialchars($displayName) ?></h1>
            <p class="text-xs text-emerald-100/80 mt-1 max-w-2xl">
                Plenary floor agenda: Deliberate on committee-endorsed measures, conduct 2nd and 3rd readings, record floor voting, and finalize enactment.
            </p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="<?= APP_ROOT_URL ?>/approval" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <i class="bi bi-award"></i> Open Plenary Enactment Desk
            </a>
            <a href="<?= APP_ROOT_URL ?>/archive" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-lg border border-white/20 transition">
                <i class="bi bi-archive"></i> Council Archive
            </a>
        </div>
    </div>

    <!-- SP Member Metric Highlights -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-5">
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-emerald-200 tracking-wider">Endorsed for Plenary</div>
            <div class="text-xl sm:text-2xl font-extrabold text-amber-300 mt-1"><?= number_format($roleData['endorsed_count'] ?? 0) ?></div>
            <div class="text-[11px] text-emerald-100/60 mt-0.5">Cleared by Committees for 2nd/3rd Reading</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-emerald-200 tracking-wider">Enacted Ordinances</div>
            <div class="text-xl sm:text-2xl font-extrabold text-emerald-300 mt-1"><?= number_format($roleData['enacted_count'] ?? 0) ?></div>
            <div class="text-[11px] text-emerald-100/60 mt-0.5">Approved municipal laws</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-emerald-200 tracking-wider">In Committee Review</div>
            <div class="text-xl sm:text-2xl font-extrabold text-white mt-1"><?= number_format($stats['pending_review'] ?? 0) ?></div>
            <div class="text-[11px] text-emerald-100/60 mt-0.5">Under committee consideration</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-lg p-3.5">
            <div class="text-[10px] uppercase font-bold text-emerald-200 tracking-wider">Total Council Measures</div>
            <div class="text-xl sm:text-2xl font-extrabold text-teal-300 mt-1"><?= number_format($stats['total_ordinances'] + $stats['total_resolutions']) ?></div>
            <div class="text-[11px] text-emerald-100/60 mt-0.5">Combined legislative registries</div>
        </div>
    </div>

    <!-- SP Member Priority Endorsed Queue -->
    <?php if (!empty($roleData['plenary_queue'])): ?>
    <div class="mt-5 pt-4 border-t border-white/10">
        <div class="flex items-center justify-between mb-2.5">
            <span class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider flex items-center gap-1.5">
                <i class="bi bi-card-checklist text-amber-400"></i> Measures Endorsed by Committees Ready for Council Action
            </span>
            <a href="<?= APP_ROOT_URL ?>/approval" class="text-[11px] text-emerald-300 hover:text-white underline font-medium">Open Enactment Desk &rarr;</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
            <?php foreach ($roleData['plenary_queue'] as $doc): ?>
            <a href="<?= APP_ROOT_URL ?>/<?= $doc['type'] ?>/view/<?= $doc['id'] ?>" class="bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded p-3 text-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-emerald-300 uppercase text-[10px] tracking-wider"><?= htmlspecialchars($doc['doc_no']) ?></span>
                        <span class="px-1.5 py-0.2 text-[9px] font-semibold bg-emerald-500/30 text-emerald-200 rounded uppercase">Endorsed</span>
                    </div>
                    <div class="text-white font-medium line-clamp-1"><?= htmlspecialchars($doc['title']) ?></div>
                </div>
                <div class="mt-2 text-[10px] text-emerald-200/60 truncate">Committee: <?= htmlspecialchars($doc['committee_name']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════════════
     CITYWIDE LEGISLATIVE OVERVIEW & REGISTRY METRICS (Shared Transparency)
     ═══════════════════════════════════════════════════════════════════════ -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-3">
    <div>
        <h2 class="text-base font-bold text-slate-800 tracking-tight flex items-center gap-2">
            <i class="bi bi-grid-fill text-slate-400"></i> Citywide Legislative Registry & Metrics
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Real-time legislative council statistics as of <?= date('F d, Y') ?>
        </p>
    </div>
</div>

<!-- ── Statistics Grid ────────────────────────────────────── -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    <!-- Total Ordinances Card -->
    <div class="bg-white border border-slate-200 border-t-2 border-t-primary rounded p-5 shadow-sm hover:shadow transition flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Ordinances</div>
            <div class="text-2xl font-bold text-slate-800 mt-1"><?= number_format($stats['total_ordinances']) ?></div>
            <div class="text-[11px] text-slate-400 mt-1.5">All ordinances on record</div>
        </div>
        <i class="bi bi-file-earmark-text text-slate-350 text-xl shrink-0"></i>
    </div>

    <!-- Total Resolutions Card -->
    <div class="bg-white border border-slate-200 border-t-2 border-t-accent rounded p-5 shadow-sm hover:shadow transition flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Resolutions</div>
            <div class="text-2xl font-bold text-slate-800 mt-1"><?= number_format($stats['total_resolutions']) ?></div>
            <div class="text-[11px] text-slate-400 mt-1.5">All resolutions on record</div>
        </div>
        <i class="bi bi-file-earmark-check text-slate-350 text-xl shrink-0"></i>
    </div>

    <!-- Enacted Card -->
    <div class="bg-white border border-slate-200 border-t-2 border-t-emerald-600 rounded p-5 shadow-sm hover:shadow transition flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Enacted</div>
            <div class="text-2xl font-bold text-emerald-700 mt-1"><?= number_format($stats['enacted']) ?></div>
            <div class="text-[11px] text-slate-400 mt-1.5">Officially enacted laws</div>
        </div>
        <i class="bi bi-check-circle text-slate-350 text-xl shrink-0"></i>
    </div>

    <!-- For Review Card -->
    <div class="bg-white border border-slate-200 border-t-2 border-t-rose-500 rounded p-5 shadow-sm hover:shadow transition flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">For Review</div>
            <div class="text-2xl font-bold text-rose-700 mt-1"><?= number_format($stats['pending_review']) ?></div>
            <div class="text-[11px] text-slate-400 mt-1.5">Awaiting council review</div>
        </div>
        <i class="bi bi-clock-history text-slate-350 text-xl shrink-0"></i>
    </div>

</div>

<!-- ── Second Statistics Grid ─────────────────────────────── -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <!-- Drafts Card -->
    <div class="bg-white border border-slate-200 border-t-2 border-t-slate-400 rounded p-5 shadow-sm hover:shadow transition flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Drafts</div>
            <div class="text-2xl font-bold text-slate-800 mt-1"><?= number_format($stats['draft']) ?></div>
            <div class="text-[11px] text-slate-400 mt-1.5">Work in progress files</div>
        </div>
        <i class="bi bi-pencil-square text-slate-350 text-xl shrink-0"></i>
    </div>

    <!-- Rejected Card -->
    <div class="bg-white border border-slate-200 border-t-2 border-t-red-650 rounded p-5 shadow-sm hover:shadow transition flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rejected</div>
            <div class="text-2xl font-bold text-red-700 mt-1"><?= number_format($stats['rejected']) ?></div>
            <div class="text-[11px] text-slate-400 mt-1.5">Archived rejections</div>
        </div>
        <i class="bi bi-x-circle text-slate-350 text-xl shrink-0"></i>
    </div>

    <!-- Total Documents Card -->
    <div class="bg-white border border-slate-200 border-t-2 border-t-primary rounded p-5 shadow-sm hover:shadow transition flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Documents</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">
                <?= number_format($stats['total_ordinances'] + $stats['total_resolutions']) ?>
            </div>
            <div class="text-[11px] text-slate-400 mt-1.5">Combined registries</div>
        </div>
        <i class="bi bi-files text-slate-350 text-xl shrink-0"></i>
    </div>

    <!-- Current Session Card -->
    <div class="bg-white border border-slate-200 border-t-2 border-t-slate-400 rounded p-5 shadow-sm hover:shadow transition flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Current Year</div>
            <div class="text-2xl font-bold text-slate-800 mt-1"><?= date('Y') ?></div>
            <div class="text-[11px] text-slate-400 mt-1.5">Legislative session</div>
        </div>
        <i class="bi bi-calendar3 text-slate-350 text-xl shrink-0"></i>
    </div>

</div>

<!-- ── Analytics Charts Row ───────────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Status Distribution Doughnut Chart -->
    <div class="bg-white border border-slate-200 rounded p-5 shadow-sm">
        <div class="border-b border-slate-200 pb-3 mb-5">
            <h2 class="text-sm font-bold text-slate-800 flex items-center uppercase tracking-wider">
                <i class="bi bi-pie-chart mr-2 text-primary"></i> Status Distribution
            </h2>
        </div>
        <div class="h-[280px] relative flex justify-center items-center">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Committee Distribution Bar Chart -->
    <div class="bg-white border border-slate-200 rounded p-5 shadow-sm">
        <div class="border-b border-slate-200 pb-3 mb-5">
            <h2 class="text-sm font-bold text-slate-800 flex items-center uppercase tracking-wider">
                <i class="bi bi-bar-chart mr-2 text-accent"></i> Committee Distribution
            </h2>
        </div>
        <div class="h-[280px] relative">
            <canvas id="committeeChart"></canvas>
        </div>
    </div>
</div>

<!-- ── Recent Documents ───────────────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Recent Ordinances -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-200 bg-slate-50/50">
            <h2 class="text-base font-bold text-slate-800">Recent Ordinances</h2>
            <a href="<?= APP_ROOT_URL ?>/ordinance" class="inline-flex items-center px-3 py-1 bg-white hover:bg-slate-50 border border-slate-250 text-slate-700 text-xs font-semibold rounded shadow-sm transition">
                View All
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-slate-650 font-bold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">No.</th>
                        <th class="px-5 py-3.5">Title</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Date Filed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (!empty($recentOrdinances)): ?>
                        <?php foreach ($recentOrdinances as $ord): ?>
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="px-5 py-3.5 align-middle">
                                <a href="<?= APP_ROOT_URL ?>/ordinance/view/<?= $ord['id'] ?>" class="font-semibold text-primary hover:text-accent transition">
                                    <?= htmlspecialchars($ord['ordinance_no'] ?? 'N/A') ?>
                                </a>
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <span class="truncate block max-w-[220px] sm:max-w-[280px]" title="<?= htmlspecialchars($ord['title']) ?>">
                                    <?= htmlspecialchars(
                                        strlen($ord['title']) > 45
                                        ? substr($ord['title'], 0, 45) . '...'
                                        : $ord['title']
                                    ) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= statusBadge($ord['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $ord['status'])) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-middle text-slate-500">
                                <?= $ord['date_filed']
                                    ? date('M d, Y', strtotime($ord['date_filed']))
                                    : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-450 bg-slate-50/20">
                                No ordinances on record.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Resolutions -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-200 bg-slate-50/50">
            <h2 class="text-base font-bold text-slate-800">Recent Resolutions</h2>
            <a href="<?= APP_ROOT_URL ?>/resolution" class="inline-flex items-center px-3 py-1 bg-white hover:bg-slate-50 border border-slate-250 text-slate-700 text-xs font-semibold rounded shadow-sm transition">
                View All
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-slate-650 font-bold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">No.</th>
                        <th class="px-5 py-3.5">Title</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Date Filed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (!empty($recentResolutions)): ?>
                        <?php foreach ($recentResolutions as $res): ?>
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="px-5 py-3.5 align-middle">
                                <a href="<?= APP_ROOT_URL ?>/resolution/view/<?= $res['id'] ?>" class="font-semibold text-primary hover:text-accent transition">
                                    <?= htmlspecialchars($res['resolution_no'] ?? 'N/A') ?>
                                </a>
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <span class="truncate block max-w-[220px] sm:max-w-[280px]" title="<?= htmlspecialchars($res['title']) ?>">
                                    <?= htmlspecialchars(
                                        strlen($res['title']) > 45
                                        ? substr($res['title'], 0, 45) . '...'
                                        : $res['title']
                                    ) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= statusBadge($res['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $res['status'])) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-middle text-slate-500">
                                <?= $res['date_filed']
                                    ? date('M d, Y', strtotime($res['date_filed']))
                                    : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-450 bg-slate-50/20">
                                No resolutions on record.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Chart.js and Initialization Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- 1. Doughnut Chart: Status Distribution ---
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = <?= json_encode(array_values($chartStatusData)) ?>;
    const statusLabels = <?= json_encode(array_keys($chartStatusData)) ?>;
    const totalDocs = statusData.reduce((a, b) => a + b, 0);
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: [
                    '#6c757d', // Slate for Drafts
                    '#1a3a5c', // Primary Navy for In Review
                    '#198754', // Success Green for Enacted
                    '#c9a84c', // Accent Gold for Published
                    '#842029'  // Danger Red for Rejected
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 10,
                        font: { size: 11, family: "'Inter', sans-serif" }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let percentage = totalDocs > 0 ? ((value / totalDocs) * 100).toFixed(1) + '%' : '0%';
                            return ` ${label}: ${value} (${percentage})`;
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });

    // --- 2. Bar Chart: Committee Distribution ---
    const committeeCtx = document.getElementById('committeeChart').getContext('2d');
    const committeeRaw = <?= json_encode($committeeStats) ?>;
    
    const committeeLabels = committeeRaw.map(item => {
        let name = item.name || 'General';
        // Clean up common long titles for cleaner graphs
        return name.replace('Committee on ', '').replace(' and Privileges', '').replace(' Laws, ', '');
    });
    const ordinanceCounts = committeeRaw.map(item => parseInt(item.ordinance_count) || 0);
    const resolutionCounts = committeeRaw.map(item => parseInt(item.resolution_count) || 0);

    new Chart(committeeCtx, {
        type: 'bar',
        data: {
            labels: committeeLabels,
            datasets: [
                {
                    label: 'Ordinances',
                    data: ordinanceCounts,
                    backgroundColor: '#1a3a5c', // Primary Navy
                    borderRadius: 0
                },
                {
                    label: 'Resolutions',
                    data: resolutionCounts,
                    backgroundColor: '#c9a84c', // Accent Gold
                    borderRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 10,
                        font: { size: 11, family: "'Inter', sans-serif" }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>
