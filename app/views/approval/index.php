<?php
/**
 * ORLMS - Approval and Enactment List View
 *
 * Variables:
 *   $endorsed — merged array of endorsed/approved ordinances + resolutions
 */

function approvalStatusBadge(string $status): string {
    return match($status) {
        'endorsed' => 'badge-endorsed',
        'approved' => 'badge-approved',
        default    => 'badge-draft',
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
                <li class="breadcrumb-item active">Approval and Enactment</li>
            </ul>
            <h1 class="page-title">Approval and Enactment</h1>
            <p class="page-subtitle">
                Endorsed documents awaiting approval and enactment
                — <?= count($endorsed) ?> document(s)
            </p>
        </div>
    </div>
</div>

<?php if (empty($endorsed)): ?>
<!-- Empty State -->
<div class="card">
    <div class="card-body" style="text-align:center; padding:48px 24px;">
        <div style="font-size:32px; font-weight:700; color:var(--color-border);
                    margin-bottom:12px;">&#10003;</div>
        <div style="font-size:16px; font-weight:600; color:var(--color-text);
                    margin-bottom:8px;">
            No Documents Pending Approval
        </div>
        <div style="font-size:13px; color:var(--color-text-muted);">
            Documents will appear here once endorsed by the legislative committee.
            <br>
            <a href="<?= APP_ROOT_URL ?>/review"
               style="color:var(--color-accent); margin-top:8px; display:inline-block;">
                Go to Review Queue &rarr;
            </a>
        </div>
    </div>
</div>

<?php else: ?>

<!-- Documents Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Documents for Approval</h2>
        <span style="font-size:12px; color:var(--color-text-muted);">
            Endorsed documents require approval before enactment
        </span>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="approval-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:90px;">Type</th>
                    <th style="width:140px;">Document No.</th>
                    <th>Title</th>
                    <th style="width:160px;">Author</th>
                    <th style="width:100px;">Status</th>
                    <th style="width:110px;">Date Filed</th>
                    <th style="width:110px;" class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($endorsed as $doc): ?>
                <tr>
                    <td class="text-muted"><?= $i++ ?></td>

                    <td>
                        <span class="badge <?= $doc['doc_type'] === 'ordinance' ? 'badge-enacted' : 'badge-endorsed' ?>"
                              style="font-size:10px;">
                            <?= ucfirst($doc['doc_type']) ?>
                        </span>
                    </td>

                    <td>
                        <a href="<?= APP_ROOT_URL ?>/approval/view/<?= $doc['doc_type'] ?>/<?= $doc['id'] ?>"
                           style="font-weight:600; color:var(--color-primary); font-size:13px;">
                            <?= htmlspecialchars($doc['doc_no'] ?? 'No Number') ?>
                        </a>
                    </td>

                    <td>
                        <div style="font-weight:500; color:var(--color-text);"
                             title="<?= htmlspecialchars($doc['title']) ?>">
                            <?= htmlspecialchars(
                                strlen($doc['title']) > 60
                                ? substr($doc['title'], 0, 60) . '...'
                                : $doc['title']
                            ) ?>
                        </div>
                        <?php if (!empty($doc['subject'])): ?>
                        <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">
                            <?= htmlspecialchars(
                                strlen($doc['subject']) > 65
                                ? substr($doc['subject'], 0, 65) . '...'
                                : $doc['subject']
                            ) ?>
                        </div>
                        <?php endif; ?>
                    </td>

                    <td style="font-size:13px; color:var(--color-text-muted);">
                        <?= htmlspecialchars($doc['author_name'] ?? 'Unknown') ?>
                    </td>

                    <td>
                        <span class="badge <?= approvalStatusBadge($doc['status']) ?>">
                            <?= ucfirst($doc['status']) ?>
                        </span>
                    </td>

                    <td class="text-muted" style="font-size:13px;">
                        <?= $doc['date_filed']
                            ? date('M d, Y', strtotime($doc['date_filed']))
                            : '—' ?>
                    </td>

                    <td class="col-actions">
                        <a href="<?= APP_ROOT_URL ?>/approval/view/<?= $doc['doc_type'] ?>/<?= $doc['id'] ?>"
                           class="btn btn-primary btn-sm">
                            <?= $doc['status'] === 'approved' ? 'Enact' : 'Approve' ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>
