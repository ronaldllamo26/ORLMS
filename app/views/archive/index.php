<?php
/**
 * ORLMS - Archive List View
 *
 * Variables:
 *   $archived  — merged array of rejected/archived ordinances + resolutions
 *   $totalOrd  — count of archived ordinances
 *   $totalRes  — count of archived resolutions
 */
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Archive</li>
            </ul>
            <h1 class="page-title">Archive</h1>
            <p class="page-subtitle">
                Permanently archived documents — rejected at review or approval stage.
                These records are read-only and cannot be modified.
            </p>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row row-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-label">Total Archived</div>
        <div class="stat-value"><?= count($archived) ?></div>
        <div class="stat-meta">All document types</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ordinances</div>
        <div class="stat-value"><?= $totalOrd ?></div>
        <div class="stat-meta">Rejected ordinances</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Resolutions</div>
        <div class="stat-value"><?= $totalRes ?></div>
        <div class="stat-meta">Rejected resolutions</div>
    </div>
</div>

<!-- Archive Notice Banner -->
<div style="background-color:#fff3cd; border:1px solid #ffe69c;
            border-radius:var(--radius); padding:12px 18px;
            margin-bottom:20px; display:flex; align-items:center; gap:12px;">
    <div style="color:#664d03; display:inline-flex; align-items:center; justify-content:center;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
    </div>
    <div style="font-size:13px; color:#664d03; line-height:1.5;">
        <strong>Archive Notice:</strong> All documents in this archive have been permanently
        rejected during the review or approval process. They cannot be edited, re-submitted,
        or deleted. These records are maintained for audit and reference purposes only.
    </div>
</div>

<?php if (empty($archived)): ?>
<!-- Empty State -->
<div class="card">
    <div class="card-body" style="text-align:center; padding:48px 24px;">
        <div style="margin-bottom:16px; display:inline-flex; align-items:center; justify-content:center;
                    width:56px; height:56px; border-radius:50%; background-color:var(--color-bg);
                    color:var(--color-text-muted); border:1px solid var(--color-border-light);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="12" y1="3" x2="12" y2="21"></line>
            </svg>
        </div>
        <div style="font-size:16px; font-weight:600; color:var(--color-text); margin-bottom:8px;">
            Archive is Empty
        </div>
        <div style="font-size:13px; color:var(--color-text-muted);">
            No documents have been rejected or archived yet.
        </div>
    </div>
</div>

<?php else: ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Archived Documents</h2>
        <span style="font-size:12px; color:var(--color-text-muted);">
            Most recently archived shown first
        </span>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="archive-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:90px;">Type</th>
                    <th style="width:140px;">Document No.</th>
                    <th>Title</th>
                    <th style="width:150px;">Author</th>
                    <th style="width:210px;">Rejection Reason</th>
                    <th style="width:130px;">Rejected By</th>
                    <th style="width:100px;">Date</th>
                    <th style="width:70px;" class="col-actions">View</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($archived as $doc): ?>
                <tr>
                    <td class="text-muted"><?= $i++ ?></td>

                    <td>
                        <span class="badge <?= $doc['doc_type'] === 'ordinance' ? 'badge-enacted' : 'badge-endorsed' ?>"
                              style="font-size:10px;">
                            <?= ucfirst($doc['doc_type']) ?>
                        </span>
                    </td>

                    <td>
                        <a href="<?= APP_ROOT_URL ?>/archive/view/<?= $doc['doc_type'] ?>/<?= $doc['id'] ?>"
                           style="font-weight:600; color:var(--color-primary); font-size:13px;">
                            <?= htmlspecialchars($doc['doc_no'] ?? 'No Number') ?>
                        </a>
                    </td>

                    <td>
                        <div style="font-weight:500; color:var(--color-text);"
                             title="<?= htmlspecialchars($doc['title']) ?>">
                            <?= htmlspecialchars(
                                strlen($doc['title']) > 55
                                ? substr($doc['title'], 0, 55) . '...'
                                : $doc['title']
                            ) ?>
                        </div>
                    </td>

                    <td style="font-size:13px; color:var(--color-text-muted);">
                        <?= htmlspecialchars($doc['author_name'] ?? 'Unknown') ?>
                    </td>

                    <td>
                        <?php if (!empty($doc['rejection_reason'])): ?>
                        <span style="font-size:12px; color:#842029;"
                              title="<?= htmlspecialchars($doc['rejection_reason']) ?>">
                            <?= htmlspecialchars(
                                strlen($doc['rejection_reason']) > 70
                                ? substr($doc['rejection_reason'], 0, 70) . '...'
                                : $doc['rejection_reason']
                            ) ?>
                        </span>
                        <?php else: ?>
                        <span style="font-size:12px; color:var(--color-text-muted);">—</span>
                        <?php endif; ?>
                    </td>

                    <td style="font-size:12px; color:var(--color-text-muted);">
                        <?= htmlspecialchars($doc['rejected_by_name'] ?? '—') ?>
                    </td>

                    <td class="text-muted" style="font-size:12px;">
                        <?= !empty($doc['rejected_at'])
                            ? date('M d, Y', strtotime($doc['rejected_at']))
                            : ($doc['date_filed'] ? date('M d, Y', strtotime($doc['date_filed'])) : '—') ?>
                    </td>

                    <td class="col-actions">
                        <a href="<?= APP_ROOT_URL ?>/archive/view/<?= $doc['doc_type'] ?>/<?= $doc['id'] ?>"
                           class="btn btn-outline btn-sm">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>
