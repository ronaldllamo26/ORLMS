<?php
/**
 * ORLMS - Implementation Monitoring Index View
 *
 * Variables:
 *   $documents — all enacted/published docs with latest monitoring status
 *   $summary   — ['pending'=>N, 'ongoing'=>N, 'completed'=>N, 'delayed'=>N, 'no_log'=>N]
 */

function implStatusBadge(string $status): string {
    return match($status) {
        'pending'   => 'badge-draft',
        'ongoing'   => 'badge-submitted',
        'completed' => 'badge-approved',
        'delayed'   => 'badge-rejected',
        default     => 'badge-draft',
    };
}

function implStatusLabel(string $status): string {
    return match($status) {
        'pending'   => 'Pending',
        'ongoing'   => 'Ongoing',
        'completed' => 'Completed',
        'delayed'   => 'Delayed',
        default     => ucfirst($status),
    };
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Implementation Monitoring</li>
            </ul>
            <h1 class="page-title">Implementation Monitoring</h1>
            <p class="page-subtitle">
                Track the implementation progress of enacted and published ordinances and resolutions
            </p>
        </div>
    </div>
</div>

<!-- Status Summary Cards -->
<div class="row row-5" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-label">No Update Yet</div>
        <div class="stat-value" style="color:var(--color-text-muted);">
            <?= $summary['no_log'] ?>
        </div>
        <div class="stat-meta">Not yet monitored</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending</div>
        <div class="stat-value" style="color:var(--color-text-muted);">
            <?= $summary['pending'] ?>
        </div>
        <div class="stat-meta">Awaiting action</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ongoing</div>
        <div class="stat-value" style="color:var(--color-accent);">
            <?= $summary['ongoing'] ?>
        </div>
        <div class="stat-meta">Currently implementing</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Completed</div>
        <div class="stat-value" style="color:var(--color-success);">
            <?= $summary['completed'] ?>
        </div>
        <div class="stat-meta">Fully implemented</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Delayed</div>
        <div class="stat-value" style="color:#dc3545;">
            <?= $summary['delayed'] ?>
        </div>
        <div class="stat-meta">Needs attention</div>
    </div>
</div>

<?php if (empty($documents)): ?>
<div class="card">
    <div class="card-body" style="text-align:center; padding:48px 24px;">
        <div style="margin-bottom:16px; display:inline-flex; align-items:center; justify-content:center;
                    width:56px; height:56px; border-radius:50%; background-color:var(--color-bg);
                    color:var(--color-text-muted); border:1px solid var(--color-border-light);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
            </svg>
        </div>
        <div style="font-size:16px; font-weight:600; color:var(--color-text); margin-bottom:8px;">
            No Documents to Monitor
        </div>
        <div style="font-size:13px; color:var(--color-text-muted);">
            Enacted or published documents will appear here for monitoring.
        </div>
    </div>
</div>

<?php else: ?>

<!-- Delayed Docs Alert -->
<?php if ($summary['delayed'] > 0): ?>
<div style="background-color:#f8d7da; border:1px solid #f5c2c7;
            border-radius:var(--radius); padding:12px 18px;
            margin-bottom:20px; display:flex; align-items:center; gap:12px;">
    <div style="color:#dc3545; display:inline-flex; align-items:center; justify-content:center;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
    </div>
    <div style="font-size:13px; color:#842029;">
        <strong><?= $summary['delayed'] ?> document(s)</strong> have been marked as
        <strong>Delayed</strong> and require immediate attention.
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Enacted & Published Documents</h2>
        <span style="font-size:12px; color:var(--color-text-muted);">
            <?= count($documents) ?> document(s) under monitoring
        </span>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="monitoring-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:90px;">Type</th>
                    <th style="width:140px;">Document No.</th>
                    <th>Title</th>
                    <th style="width:120px;">Impl. Status</th>
                    <th style="width:180px;">Latest Update</th>
                    <th style="width:120px;">Last Updated</th>
                    <th style="width:80px;" class="col-actions">Monitor</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($documents as $doc): ?>
                <?php
                $implStatus = $doc['latest_impl_status'] ?? null;
                ?>
                <tr style="<?= $implStatus === 'delayed' ? 'background-color:#fff5f5;' : '' ?>">
                    <td class="text-muted"><?= $i++ ?></td>
                    <td>
                        <span class="badge <?= $doc['doc_type'] === 'ordinance' ? 'badge-enacted' : 'badge-endorsed' ?>"
                              style="font-size:10px;">
                            <?= ucfirst($doc['doc_type']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= APP_ROOT_URL ?>/implementation_monitoring/view/<?= $doc['doc_type'] ?>/<?= $doc['id'] ?>"
                           style="font-weight:600; color:var(--color-primary); font-size:13px;">
                            <?= htmlspecialchars($doc['doc_no'] ?? 'No Number') ?>
                        </a>
                    </td>
                    <td>
                        <div style="font-weight:500;">
                            <?= htmlspecialchars(
                                strlen($doc['title']) > 60
                                ? substr($doc['title'], 0, 60) . '...'
                                : $doc['title']
                            ) ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($implStatus): ?>
                        <span class="badge <?= implStatusBadge($implStatus) ?>"
                              style="font-size:11px;">
                            <?= implStatusLabel($implStatus) ?>
                        </span>
                        <?php else: ?>
                        <span style="font-size:12px; color:var(--color-text-muted); font-style:italic;">
                            No update yet
                        </span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:12px; color:var(--color-text-muted);">
                        <?php if (!empty($doc['latest_notes'])): ?>
                        <?= htmlspecialchars(
                            strlen($doc['latest_notes']) > 55
                            ? substr($doc['latest_notes'], 0, 55) . '...'
                            : $doc['latest_notes']
                        ) ?>
                        <?php else: ?>
                        <span style="font-style:italic;">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted" style="font-size:12px;">
                        <?php if (!empty($doc['last_logged_at'])): ?>
                            <?= date('M d, Y', strtotime($doc['last_logged_at'])) ?>
                            <div style="font-size:11px;">
                                by <?= htmlspecialchars($doc['last_logged_by'] ?? '—') ?>
                            </div>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td class="col-actions">
                        <a href="<?= APP_ROOT_URL ?>/implementation_monitoring/view/<?= $doc['doc_type'] ?>/<?= $doc['id'] ?>"
                           class="btn btn-outline btn-sm">Log</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>
