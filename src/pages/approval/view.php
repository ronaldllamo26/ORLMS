<?php

/**
 * ORLMS - Approval and Enactment Detail View
 *
 * @var array       $document      The ordinance or resolution record
 * @var string      $docType       'ordinance' or 'resolution'
 * @var array|false $aiReport      Latest AI validation report (or false)
 * @var array       $reviewHistory Array of review/endorsement log entries
 */

$noField  = $docType === 'ordinance' ? 'ordinance_no' : 'resolution_no';
$docNo    = $document[$noField] ?? 'N/A';
$docUrl   = APP_ROOT_URL . '/' . $docType . '/view/' . $document['id'];
$userRole = $_SESSION['user_role'] ?? '';
$canAct   = in_array($userRole, ['super_admin']);

$isEndorsed = $document['status'] === 'endorsed';
$isApproved = $document['status'] === 'approved';
$canApprove = $canAct && $isEndorsed;
$canEnact   = $canAct && $isApproved;
$canReject  = $canAct && in_array($document['status'], ['endorsed', 'approved']);

$aiStatusColor = match ($aiReport['validation_status'] ?? '') {
    'passed'  => 'var(--color-success)',
    'flagged' => '#fd7e14',
    'failed'  => '#dc3545',
    default   => 'var(--color-text-muted)',
};
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/approval">Approval and Enactment</a>
                </li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($docNo) ?></li>
            </ul>
            <h1 class="page-title">
                <?= $isApproved ? 'Enactment' : 'Approval' ?>:
                <?= htmlspecialchars($docNo) ?>
            </h1>
            <p class="page-subtitle"><?= htmlspecialchars($document['title']) ?></p>
        </div>
        <div class="d-flex gap-8">
            <a href="<?= $docUrl ?>" class="btn btn-outline btn-sm" target="_blank">
                View Full Document
            </a>
            <a href="<?= APP_ROOT_URL ?>/approval" class="btn btn-outline-secondary btn-sm">
                Back to Queue
            </a>
        </div>
    </div>
</div>

<div class="row row-2-1" style="align-items:start;">

    <!-- LEFT: Document Info + Content + History -->
    <div>

        <!-- Document Metadata -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Document Information</h2>
                <span class="badge badge-<?= $document['status'] ?>" style="font-size:11px;">
                    <?= ucfirst($document['status']) ?>
                </span>
            </div>
            <div class="card-body">
                <div class="doc-meta-grid">

                    <span class="doc-meta-label">Document No.</span>
                    <span class="doc-meta-value"
                        style="font-weight:700; color:var(--color-primary);">
                        <?= htmlspecialchars($docNo) ?>
                    </span>

                    <span class="doc-meta-label">Type</span>
                    <span class="doc-meta-value" style="text-transform:capitalize;">
                        <?= $docType ?>
                    </span>

                    <span class="doc-meta-label">Title</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($document['title']) ?>
                    </span>

                    <span class="doc-meta-label">Subject</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($document['subject']) ?>
                    </span>

                    <span class="doc-meta-label">Author</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($document['author_name'] ?? 'Unknown') ?>
                    </span>

                    <?php if (!empty($document['committee_name'])): ?>
                    <span class="doc-meta-label">Committee</span>
                    <span class="doc-meta-value" style="font-weight:600; color:var(--color-primary);">
                        <?= htmlspecialchars($document['committee_name']) ?>
                    </span>
                    <?php endif; ?>

                    <span class="doc-meta-label">Date Filed</span>
                    <span class="doc-meta-value">
                        <?= $document['date_filed']
                            ? date('F d, Y', strtotime($document['date_filed']))
                            : '—' ?>
                    </span>

                </div>
            </div>
        </div>

        <!-- Document Content -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Document Content</h2>
                <span style="font-size:11px; color:var(--color-text-muted);">
                    Full text for final review
                </span>
            </div>
            <div class="card-body">
                <div class="doc-content-body" id="doc-content-approval">
                    <?= htmlspecialchars($document['content']) ?>
                </div>
            </div>
        </div>

        <!-- Review History -->
        <?php if (!empty($reviewHistory)): ?>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Workflow History</h2>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Notes / Reason</th>
                                <th>By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviewHistory as $log): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $logBadge = match ($log['action']) {
                                            'endorsed'              => 'badge-endorsed',
                                            'approved'              => 'badge-approved',
                                            'enacted'               => 'badge-enacted',
                                            'rejected'              => 'badge-rejected',
                                            'returned_for_revision' => 'badge-draft',
                                            default                 => 'badge-draft',
                                        };
                                        $logLabel = match ($log['action']) {
                                            'endorsed'              => 'Endorsed',
                                            'approved'              => 'Approved',
                                            'enacted'               => 'Enacted',
                                            'rejected'              => 'Rejected',
                                            'returned_for_revision' => 'Returned for Revision',
                                            default                 => ucfirst($log['action']),
                                        };
                                        ?>
                                        <span class="badge <?= $logBadge ?>" style="font-size:11px;">
                                            <?= $logLabel ?>
                                        </span>
                                    </td>
                                    <td style="font-size:12px; color:var(--color-text-muted);">
                                        <?= !empty($log['reason'])
                                            ? htmlspecialchars($log['reason'])
                                            : '—' ?>
                                    </td>
                                    <td style="font-size:13px;">
                                        <?= htmlspecialchars($log['reviewer_name'] ?? 'Unknown') ?>
                                    </td>
                                    <td class="text-muted" style="font-size:12px;">
                                        <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- RIGHT: AI Summary + Decision Panel -->
    <div>

        <!-- AI Validation Summary -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">AI Validation</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($aiReport)): ?>
                    <div style="background-color:<?= $aiStatusColor ?>; color:#fff;
                                border-radius:var(--radius); padding:10px 14px;
                                margin-bottom:14px; display:flex;
                                justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-size:10px; opacity:0.85; font-weight:700;
                                        text-transform:uppercase; letter-spacing:0.5px;">
                                AI Result
                            </div>
                            <div style="font-size:16px; font-weight:700;">
                                <?= strtoupper($aiReport['validation_status']) ?>
                            </div>
                        </div>
                        <a href="<?= APP_ROOT_URL ?>/ai_validation/report/<?= $aiReport['id'] ?>"
                            style="font-size:11px; color:#fff;
                                  text-decoration:underline; opacity:0.85;">
                            Full Report &rarr;
                        </a>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr;
                                gap:10px; margin-bottom:12px;">
                        <div style="background:var(--color-bg); border:1px solid var(--color-border);
                                    border-radius:var(--radius); padding:10px; text-align:center;">
                            <div style="font-size:20px; font-weight:700;
                                        color:<?= (int)$aiReport['completeness_score'] >= 80
                                                    ? 'var(--color-success)'
                                                    : '#fd7e14' ?>;">
                                <?= (int)$aiReport['completeness_score'] ?>%
                            </div>
                            <div style="font-size:11px; color:var(--color-text-muted);">
                                Completeness
                            </div>
                        </div>
                        <div style="background:var(--color-bg); border:1px solid var(--color-border);
                                    border-radius:var(--radius); padding:10px; text-align:center;">
                            <div style="font-size:20px; font-weight:700;
                                        color:<?= (float)$aiReport['similarity_score'] > 60
                                                    ? '#dc3545'
                                                    : 'var(--color-success)' ?>;">
                                <?= number_format((float)$aiReport['similarity_score'], 0) ?>%
                            </div>
                            <div style="font-size:11px; color:var(--color-text-muted);">
                                Similarity
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($aiReport['ai_summary'])): ?>
                        <div style="font-size:12px; color:var(--color-text); line-height:1.6;
                                background:var(--color-bg); border-radius:var(--radius);
                                border:1px solid var(--color-border-light); padding:12px;">
                            <?= htmlspecialchars($aiReport['ai_summary']) ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="text-align:center; padding:16px; font-size:13px;
                                color:var(--color-text-muted);">
                        No AI validation report for this document.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Approval Decision Panel -->
        <?php if ($canAct): ?>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <?= $isApproved ? 'Enactment Decision' : 'Approval Decision' ?>
                    </h2>
                </div>
                <div class="card-body">

                    <div style="font-size:12px; color:var(--color-text-muted);
                            margin-bottom:18px; line-height:1.6;">
                        <?php if ($isEndorsed): ?>
                            <strong><?= htmlspecialchars($docNo) ?></strong> has been endorsed
                            by the committee. Approve to move it to final enactment.
                        <?php elseif ($isApproved): ?>
                            <strong><?= htmlspecialchars($docNo) ?></strong> has been approved.
                            Enact to make it an official law/policy of the municipality.
                        <?php endif; ?>
                    </div>

                    <!-- APPROVE (only if endorsed) -->
                    <?php if ($canApprove): ?>
                        <div style="border:1px solid var(--color-border); border-radius:var(--radius);
                            padding:14px; margin-bottom:12px;">
                            <div style="font-size:13px; font-weight:600; color:var(--color-success);
                                margin-bottom:6px;">
                                Approve Document
                            </div>
                            <div style="font-size:12px; color:var(--color-text-muted); margin-bottom:12px;">
                                Document is approved and ready for final enactment.
                            </div>
                            <form method="POST"
                                action="<?= APP_ROOT_URL ?>/approval/approve/<?= $docType ?>/<?= $document['id'] ?>"
                                id="form-approve"
                                onsubmit="return confirm('Approve <?= htmlspecialchars($docNo) ?>?')">
                                <button type="submit" class="btn btn-primary" style="width:100%;"
                                    id="btn-approve">
                                    Approve Document
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- ENACT (only if approved) -->
                    <?php if ($canEnact): ?>
                        <div style="border:1px solid var(--color-border); border-radius:var(--radius);
                            padding:14px; margin-bottom:12px;
                            background:linear-gradient(135deg,#f0fdf4,#fff);">
                            <div style="font-size:13px; font-weight:600; color:var(--color-primary);
                                margin-bottom:6px;">
                                Enact Document
                            </div>
                            <div style="font-size:12px; color:var(--color-text-muted); margin-bottom:12px;">
                                This is the final legislative action. The document will become
                                an enacted ordinance / resolution of the municipality.
                            </div>
                            <form method="POST"
                                action="<?= APP_ROOT_URL ?>/approval/enact/<?= $docType ?>/<?= $document['id'] ?>"
                                id="form-enact"
                                onsubmit="return confirm('ENACT <?= htmlspecialchars($docNo) ?>?\n\nThis will officially enact this document. This action is final.')">
                                <div class="form-group" style="margin-bottom:10px;">
                                    <label for="effective_date" class="form-label"
                                        style="font-size:12px;">
                                        Effective Date
                                    </label>
                                    <input type="date" id="effective_date" name="effective_date"
                                        class="form-control"
                                        value="<?= date('Y-m-d') ?>"
                                        style="font-size:13px;">
                                    <span class="form-hint">
                                        Date the ordinance/resolution takes effect.
                                    </span>
                                </div>
                                <button type="submit" class="btn btn-primary" style="width:100%;"
                                    id="btn-enact">
                                    Enact — Make Official
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- REJECT -->
                    <?php if ($canReject): ?>
                        <div style="border:1px solid #f5c2c7; border-radius:var(--radius);
                            padding:14px; background-color:#fff5f5;">
                            <div style="font-size:13px; font-weight:600; color:#dc3545;
                                margin-bottom:6px;">
                                Reject Document
                            </div>
                            <div style="font-size:12px; color:var(--color-text-muted); margin-bottom:10px;">
                                Reject and permanently move to the Archive.
                                This action cannot be undone.
                            </div>
                            <form method="POST"
                                action="<?= APP_ROOT_URL ?>/approval/reject/<?= $docType ?>/<?= $document['id'] ?>"
                                id="form-reject-approval"
                                onsubmit="return confirm('REJECT and ARCHIVE <?= htmlspecialchars($docNo) ?>?\n\nThis cannot be undone.')">
                                <div class="form-group" style="margin-bottom:10px;">
                                    <textarea name="reason" class="form-control" rows="3" required
                                        placeholder="State the reason for rejection..."
                                        style="font-size:12px; border-color:#f5c2c7;">
                            </textarea>
                                </div>
                                <button type="submit" class="btn btn-danger" style="width:100%;"
                                    id="btn-reject-approval">
                                    Reject and Archive
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body" style="text-align:center; padding:24px;">
                    <div style="font-size:13px; color:var(--color-text-muted);">
                        Approval and enactment actions are restricted to the
                        Administrator (approving authority).
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

</div>