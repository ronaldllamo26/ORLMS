<?php
/**
 * ORLMS - Dashboard View
 *
 * Variables passed from DashboardController:
 *   $stats             — array of document counts
 *   $recentOrdinances  — array of latest 5 ordinances
 *   $recentResolutions — array of latest 5 resolutions
 */

// Helper: returns a CSS badge class based on document status
function statusBadge(string $status): string {
    return match($status) {
        'draft'        => 'badge-draft',
        'submitted'    => 'badge-submitted',
        'under_review' => 'badge-under-review',
        'endorsed'     => 'badge-endorsed',
        'approved'     => 'badge-approved',
        'enacted'      => 'badge-enacted',
        'published'    => 'badge-published',
        'rejected'     => 'badge-rejected',
        'archived'     => 'badge-archived',
        'implemented'  => 'badge-implemented',
        'amended'      => 'badge-amended',
        default        => 'badge-draft',
    };
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">
                System overview as of <?= date('F d, Y') ?>
            </p>
        </div>
        <?php if (in_array($_SESSION['user_role'], ['legislative_staff', 'super_admin'])): ?>
        <div class="d-flex gap-8">
            <a href="<?= APP_ROOT_URL ?>/ordinance/create" class="btn btn-primary btn-sm">
                New Ordinance
            </a>
            <a href="<?= APP_ROOT_URL ?>/resolution/create" class="btn btn-outline btn-sm">
                New Resolution
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ── Statistics Row ─────────────────────────────────────── -->
<div class="row row-4" style="margin-bottom: 24px;">

    <div class="stat-card">
        <div class="stat-label">Total Ordinances</div>
        <div class="stat-value"><?= number_format($stats['total_ordinances']) ?></div>
        <div class="stat-meta">All ordinances on record</div>
    </div>

    <div class="stat-card accent">
        <div class="stat-label">Total Resolutions</div>
        <div class="stat-value"><?= number_format($stats['total_resolutions']) ?></div>
        <div class="stat-meta">All resolutions on record</div>
    </div>

    <div class="stat-card success">
        <div class="stat-label">Enacted</div>
        <div class="stat-value"><?= number_format($stats['enacted']) ?></div>
        <div class="stat-meta">Enacted documents</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">For Review</div>
        <div class="stat-value"><?= number_format($stats['pending_review']) ?></div>
        <div class="stat-meta">Awaiting review</div>
    </div>

</div>

<!-- ── Second Stats Row ───────────────────────────────────── -->
<div class="row row-4" style="margin-bottom: 28px;">

    <div class="stat-card">
        <div class="stat-label">Drafts</div>
        <div class="stat-value"><?= number_format($stats['draft']) ?></div>
        <div class="stat-meta">Work in progress</div>
    </div>

    <div class="stat-card danger">
        <div class="stat-label">Rejected</div>
        <div class="stat-value"><?= number_format($stats['rejected']) ?></div>
        <div class="stat-meta">Archived rejections</div>
    </div>

    <div class="stat-card accent">
        <div class="stat-label">Total Documents</div>
        <div class="stat-value">
            <?= number_format($stats['total_ordinances'] + $stats['total_resolutions']) ?>
        </div>
        <div class="stat-meta">Ordinances and resolutions combined</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Current Year</div>
        <div class="stat-value"><?= date('Y') ?></div>
        <div class="stat-meta">Legislative session</div>
    </div>

</div>

<!-- ── Recent Documents ───────────────────────────────────── -->
<div class="row row-2">

    <!-- Recent Ordinances -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Ordinances</h2>
            <a href="<?= APP_ROOT_URL ?>/ordinance"
               class="btn btn-outline btn-sm">
                View All
            </a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Date Filed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentOrdinances)): ?>
                        <?php foreach ($recentOrdinances as $ord): ?>
                        <tr>
                            <td>
                                <a href="<?= APP_ROOT_URL ?>/ordinance/view/<?= $ord['id'] ?>"
                                   style="font-weight:500; color:var(--color-primary);">
                                    <?= htmlspecialchars($ord['ordinance_no'] ?? 'N/A') ?>
                                </a>
                            </td>
                            <td>
                                <span title="<?= htmlspecialchars($ord['title']) ?>">
                                    <?= htmlspecialchars(
                                        strlen($ord['title']) > 45
                                        ? substr($ord['title'], 0, 45) . '...'
                                        : $ord['title']
                                    ) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= statusBadge($ord['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $ord['status'])) ?>
                                </span>
                            </td>
                            <td class="text-muted">
                                <?= $ord['date_filed']
                                    ? date('M d, Y', strtotime($ord['date_filed']))
                                    : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="table-empty">
                                No ordinances on record.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Resolutions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Resolutions</h2>
            <a href="<?= APP_ROOT_URL ?>/resolution"
               class="btn btn-outline btn-sm">
                View All
            </a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Date Filed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentResolutions)): ?>
                        <?php foreach ($recentResolutions as $res): ?>
                        <tr>
                            <td>
                                <a href="<?= APP_ROOT_URL ?>/resolution/view/<?= $res['id'] ?>"
                                   style="font-weight:500; color:var(--color-primary);">
                                    <?= htmlspecialchars($res['resolution_no'] ?? 'N/A') ?>
                                </a>
                            </td>
                            <td>
                                <span title="<?= htmlspecialchars($res['title']) ?>">
                                    <?= htmlspecialchars(
                                        strlen($res['title']) > 45
                                        ? substr($res['title'], 0, 45) . '...'
                                        : $res['title']
                                    ) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= statusBadge($res['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $res['status'])) ?>
                                </span>
                            </td>
                            <td class="text-muted">
                                <?= $res['date_filed']
                                    ? date('M d, Y', strtotime($res['date_filed']))
                                    : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="table-empty">
                                No resolutions on record.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
