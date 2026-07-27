<?php
/**
 * ORLMS - Review Document View
 *
 * Variables:
 *   $document      — the ordinance or resolution record
 *   $docType       — 'ordinance' or 'resolution'
 *   $aiReport      — latest AI validation report (or false)
 *   $reviewHistory — array of previous review actions
 */

$noField  = $docType === 'ordinance' ? 'ordinance_no' : 'resolution_no';
$docNo    = $document[$noField] ?? 'N/A';
$docUrl   = APP_ROOT_URL . '/' . $docType . '/view/' . $document['id'];
$userRole = $_SESSION['user_role'] ?? '';

$canAct = in_array($userRole, ['sp_member', 'committee_member', 'super_admin']);

$allowedStatuses = ['pending_review', 'submitted', 'reviewed'];
$canReview = $canAct && in_array($document['status'], $allowedStatuses);

// AI report helpers
$aiPassed  = !empty($aiReport) && $aiReport['validation_status'] === 'passed';
$aiFlagged = !empty($aiReport) && $aiReport['validation_status'] === 'flagged';
$aiFailed  = !empty($aiReport) && $aiReport['validation_status'] === 'failed';

$aiStatusColor = match($aiReport['validation_status'] ?? '') {
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
                <li class="breadcrumb-item"><a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= APP_ROOT_URL ?>/review">Review and Endorsement</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($docNo) ?></li>
            </ul>
            <h1 class="page-title">Reviewing: <?= htmlspecialchars($docNo) ?></h1>
            <p class="page-subtitle"><?= htmlspecialchars($document['title']) ?></p>
        </div>
        <div class="d-flex gap-8">
            <a href="<?= $docUrl ?>" class="btn btn-outline btn-sm" target="_blank">
                View Full Document
            </a>
            <a href="<?= APP_ROOT_URL ?>/review" class="btn btn-outline-secondary btn-sm">
                Back to Queue
            </a>
        </div>
    </div>
</div>

<!-- ── Main Layout ────────────────────────────────────────── -->
<div class="row row-2-1" style="align-items:start;">

    <!-- LEFT: Document Info + Content -->
    <div>

        <!-- Document Metadata -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Document Information</h2>
                <span class="badge badge-submitted" style="font-size:11px;">
                    <?= ucfirst(str_replace('_', ' ', $document['status'])) ?>
                </span>
            </div>
            <div class="card-body">
                <div class="doc-meta-grid">
                    <span class="doc-meta-label">Document No.</span>
                    <span class="doc-meta-value" style="font-weight:700; color:var(--color-primary);">
                        <?= htmlspecialchars($docNo) ?>
                    </span>

                    <span class="doc-meta-label">Type</span>
                    <span class="doc-meta-value" style="text-transform:capitalize;">
                        <?= $docType ?>
                    </span>

                    <span class="doc-meta-label">Title</span>
                    <span class="doc-meta-value"><?= htmlspecialchars($document['title']) ?></span>

                    <span class="doc-meta-label">Subject</span>
                    <span class="doc-meta-value"><?= htmlspecialchars($document['subject']) ?></span>

                    <span class="doc-meta-label">Author</span>
                    <span class="doc-meta-value"><?= htmlspecialchars($document['author_name'] ?? 'Unknown') ?></span>

                    <span class="doc-meta-label">Date Filed</span>
                    <span class="doc-meta-value">
                        <?= $document['date_filed'] ? date('F d, Y', strtotime($document['date_filed'])) : '—' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Document Content -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Document Content</h2>
                <span style="font-size:11px; color:var(--color-text-muted);">Full text for review</span>
            </div>
            <div class="card-body">
                <div class="doc-content-body" id="doc-content-review">
                    <?= htmlspecialchars($document['content']) ?>
                </div>
            </div>
        </div>

        <!-- Review History -->
        <?php if (!empty($reviewHistory)): ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Review History</h2>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Reason / Notes</th>
                            <th>Reviewed By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviewHistory as $log): ?>
                        <tr>
                            <td>
                                <?php
                                $actionBadge = match($log['action']) {
                                    'endorsed'             => 'badge-endorsed',
                                    'rejected'             => 'badge-rejected',
                                    'returned_for_revision'=> 'badge-draft',
                                    default                => 'badge-draft',
                                };
                                $actionLabel = match($log['action']) {
                                    'endorsed'             => 'Endorsed',
                                    'rejected'             => 'Rejected',
                                    'returned_for_revision'=> 'Returned for Revision',
                                    default                => ucfirst($log['action']),
                                };
                                ?>
                                <span class="badge <?= $actionBadge ?>" style="font-size:11px;">
                                    <?= $actionLabel ?>
                                </span>
                            </td>
                            <td style="font-size:12px; color:var(--color-text-muted); max-width:220px;">
                                <?= !empty($log['reason']) ? htmlspecialchars($log['reason']) : '—' ?>
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

    <!-- RIGHT: AI Report + Review Actions -->
    <div>

        <!-- AI Validation Summary -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">AI Validation Summary</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($aiReport)): ?>

                    <!-- Overall status -->
                    <div style="background-color:<?= $aiStatusColor ?>; color:#fff;
                                border-radius:var(--radius); padding:10px 14px;
                                margin-bottom:14px; display:flex;
                                justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-size:10px; font-weight:700; text-transform:uppercase;
                                        letter-spacing:0.5px; opacity:0.85;">AI Result</div>
                            <div style="font-size:16px; font-weight:700;">
                                <?= strtoupper($aiReport['validation_status']) ?>
                            </div>
                        </div>
                        <a href="<?= APP_ROOT_URL ?>/ai_validation/report/<?= $aiReport['id'] ?>"
                           style="font-size:11px; color:#fff; text-decoration:underline; opacity:0.85;">
                            Full Report &rarr;
                        </a>
                    </div>

                    <!-- Scores -->
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                        <div style="background:var(--color-bg); border:1px solid var(--color-border);
                                    border-radius:var(--radius); padding:12px; text-align:center;">
                            <div style="font-size:22px; font-weight:700;
                                        color:<?= (int)$aiReport['completeness_score'] >= 80 ? 'var(--color-success)' : ((int)$aiReport['completeness_score'] >= 60 ? '#fd7e14' : '#dc3545') ?>;">
                                <?= (int)$aiReport['completeness_score'] ?>%
                            </div>
                            <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">
                                Completeness
                            </div>
                        </div>
                        <div style="background:var(--color-bg); border:1px solid var(--color-border);
                                    border-radius:var(--radius); padding:12px; text-align:center;">
                            <div style="font-size:22px; font-weight:700;
                                        color:<?= (float)$aiReport['similarity_score'] > 60 ? '#dc3545' : ((float)$aiReport['similarity_score'] > 30 ? '#fd7e14' : 'var(--color-success)') ?>;">
                                <?= number_format((float)$aiReport['similarity_score'], 0) ?>%
                            </div>
                            <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">
                                Similarity
                            </div>
                        </div>
                    </div>

                    <!-- AI Summary -->
                    <?php if (!empty($aiReport['ai_summary'])): ?>
                    <div style="font-size:12px; color:var(--color-text); line-height:1.6;
                                background:var(--color-bg); border-radius:var(--radius);
                                border:1px solid var(--color-border-light); padding:12px;">
                        <strong style="color:var(--color-primary); font-size:11px;
                                       text-transform:uppercase; letter-spacing:0.4px;">
                            AI Summary:
                        </strong><br>
                        <?= htmlspecialchars($aiReport['ai_summary']) ?>
                    </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div style="text-align:center; padding:16px; font-size:13px;
                                color:var(--color-text-muted);">
                        No AI validation report found for this document.
                        <?php if (in_array($userRole, ['super_admin'])): ?>
                        <div style="margin-top:10px;">
                            <a href="<?= APP_ROOT_URL ?>/ai_validation/run/<?= $docType ?>/<?= $document['id'] ?>"
                               class="btn btn-primary btn-sm">
                                Run AI Validation
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Review Decision Card -->
        <?php if ($canReview): ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Review Decision</h2>
            </div>
            <div class="card-body">

                <div style="font-size:12px; color:var(--color-text-muted);
                            margin-bottom:18px; line-height:1.6;">
                    Select your decision for <strong><?= htmlspecialchars($docNo) ?></strong>.
                    Your action will be permanently logged with your name and timestamp.
                </div>

                <!-- ENDORSE -->
                <div style="border:1px solid var(--color-border); border-radius:var(--radius);
                            padding:14px; margin-bottom:12px;">
                    <div style="font-size:13px; font-weight:600; color:var(--color-success);
                                margin-bottom:6px;">
                        Endorse Document
                    </div>
                    <div style="font-size:12px; color:var(--color-text-muted); margin-bottom:12px;">
                        The document meets all requirements and is endorsed for approval.
                    </div>
                    <form method="POST"
                          action="<?= APP_ROOT_URL ?>/review/endorse/<?= $docType ?>/<?= $document['id'] ?>"
                          id="form-endorse"
                          onsubmit="return confirm('Endorse <?= htmlspecialchars($docNo) ?>? This action will be permanently recorded.')">
                        <button type="submit" class="btn btn-primary" style="width:100%;"
                                id="btn-endorse">
                            Endorse — Forward for Approval
                        </button>
                    </form>
                </div>

                <!-- RETURN FOR REVISION -->
                <div style="border:1px solid var(--color-border); border-radius:var(--radius);
                            padding:14px; margin-bottom:12px;">
                    <div style="font-size:13px; font-weight:600; color:#fd7e14; margin-bottom:6px;">
                        Return for Revision
                    </div>
                    <div style="font-size:12px; color:var(--color-text-muted); margin-bottom:10px;">
                        Return the document to the author for corrections before re-submission.
                    </div>
                    <form method="POST"
                          action="<?= APP_ROOT_URL ?>/review/return/<?= $docType ?>/<?= $document['id'] ?>"
                          id="form-return">
                        <div class="form-group" style="margin-bottom:10px;">
                            <textarea name="reason" class="form-control"
                                      rows="3" required
                                      placeholder="Describe the corrections needed..."
                                      style="font-size:12px;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-secondary" style="width:100%;"
                                id="btn-return">
                            Return for Revision
                        </button>
                    </form>
                </div>

                <!-- REJECT -->
                <div style="border:1px solid #f5c2c7; border-radius:var(--radius);
                            padding:14px; background-color:#fff5f5;">
                    <div style="font-size:13px; font-weight:600; color:#dc3545; margin-bottom:6px;">
                        Reject Document
                    </div>
                    <div style="font-size:12px; color:var(--color-text-muted); margin-bottom:10px;">
                        The document is rejected and will be permanently moved to the Archive.
                        This action cannot be undone.
                    </div>
                    <form method="POST"
                          action="<?= APP_ROOT_URL ?>/review/reject/<?= $docType ?>/<?= $document['id'] ?>"
                          id="form-reject"
                          onsubmit="return confirm('REJECT <?= htmlspecialchars($docNo) ?>?\n\nThis will permanently move the document to the Archive. This cannot be undone.')">
                        <div class="form-group" style="margin-bottom:10px;">
                            <textarea name="reason" class="form-control"
                                      rows="3" required
                                      placeholder="State the legal or technical reason for rejection..."
                                      style="font-size:12px; border-color:#f5c2c7;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger" style="width:100%;"
                                id="btn-reject">
                            Reject and Archive
                        </button>
                    </form>
                </div>

            </div>
        </div>
        <?php else: ?>
        <!-- No permission notice -->
        <div class="card">
            <div class="card-body" style="text-align:center; padding:24px;">
                <div style="font-size:13px; color:var(--color-text-muted);">
                    You do not have permission to perform review actions on this document.
                    Review actions are restricted to SP Members and Committee Members.
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

</div>
