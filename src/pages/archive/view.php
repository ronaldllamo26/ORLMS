<?php
/**
 * ORLMS - Archive Detail View (Read-Only)
 *
 * Variables injected via extract() from ArchiveController::view():
 *   $document        — the archived document record
 *   $docType         — 'ordinance' or 'resolution'
 *   $workflowHistory — full review log from submission to rejection
 *   $rejectionEntry  — the specific rejection log entry
 *   $aiReport        — AI validation report or false
 *
 * @var array       $document
 * @var string      $docType
 * @var array       $workflowHistory
 * @var array|null  $rejectionEntry
 * @var array|false $aiReport
 */

$noField = $docType === 'ordinance' ? 'ordinance_no' : 'resolution_no';
$docNo   = $document[$noField] ?? 'N/A';
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
                    <a href="<?= APP_ROOT_URL ?>/archive">Archive</a>
                </li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($docNo) ?></li>
            </ul>
            <h1 class="page-title">Archived: <?= htmlspecialchars($docNo) ?></h1>
            <p class="page-subtitle"><?= htmlspecialchars($document['title']) ?></p>
        </div>
        <div>
            <a href="<?= APP_ROOT_URL ?>/archive"
               class="btn btn-outline-secondary btn-sm">Back to Archive</a>
        </div>
    </div>
</div>

<!-- Read-Only Banner -->
<div style="background-color:#f8d7da; border:1px solid #f5c2c7;
            border-radius:var(--radius); padding:12px 18px;
            margin-bottom:20px; display:flex; align-items:center; gap:12px;">
    <div style="color:#842029; display:inline-flex; align-items:center; justify-content:center;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
    </div>
    <div style="font-size:13px; color:#842029; line-height:1.5;">
        <strong>Read-Only Record.</strong> This document has been permanently archived
        and cannot be edited, re-submitted, or deleted. It is preserved for
        legal and audit reference purposes only.
    </div>
</div>

<!-- Rejection Reason Card (prominent) -->
<?php if (!empty($rejectionEntry)): ?>
<div style="background-color:#fff5f5; border:2px solid #dc3545;
            border-radius:var(--radius); padding:18px 20px; margin-bottom:24px;">
    <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                letter-spacing:0.5px; color:#842029; margin-bottom:8px;">
        Rejection Reason
    </div>
    <div style="font-size:14px; color:#842029; line-height:1.7; margin-bottom:10px;">
        "<?= htmlspecialchars($rejectionEntry['reason']) ?>"
    </div>
    <div style="font-size:12px; color:var(--color-text-muted);">
        Rejected by <strong><?= htmlspecialchars($rejectionEntry['reviewer_name'] ?? 'Unknown') ?></strong>
        on <?= date('F d, Y h:i A', strtotime($rejectionEntry['created_at'])) ?>
    </div>
</div>
<?php endif; ?>

<div class="row row-2-1" style="align-items:start;">

    <!-- LEFT: Document Info + Content + History -->
    <div>

        <!-- Document Metadata -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Document Information</h2>
                <span class="badge badge-rejected" style="font-size:11px;">
                    <?= ucfirst($document['status']) ?>
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
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($document['author_name'] ?? 'Unknown') ?>
                    </span>

                    <span class="doc-meta-label">Date Filed</span>
                    <span class="doc-meta-value">
                        <?= $document['date_filed']
                            ? date('F d, Y', strtotime($document['date_filed']))
                            : '—' ?>
                    </span>

                    <span class="doc-meta-label">Date Created</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= date('F d, Y', strtotime($document['created_at'])) ?>
                    </span>

                    <?php if (!empty($document['file_path'])): ?>
                    <span class="doc-meta-label">Attached File</span>
                    <span class="doc-meta-value">
                        <a href="<?= APP_URL ?>/public/uploads/documents/<?= htmlspecialchars($document['file_path']) ?>"
                           target="_blank" style="color:var(--color-accent);">
                            Download File
                        </a>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Document Content — Read-Only -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Document Content</h2>
                <span style="font-size:11px; color:#842029; font-weight:600; display:inline-flex; align-items:center; gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Read-Only
                </span>
            </div>
            <div class="card-body">
                <div class="doc-content-body" id="archive-doc-content"
                     style="background-color:#fafafa; border:1px solid var(--color-border-light);
                            border-radius:var(--radius); padding:16px; cursor:default;
                            user-select:text;">
                    <?= htmlspecialchars($document['content']) ?>
                </div>
            </div>
        </div>

        <!-- Workflow History -->
        <?php if (!empty($workflowHistory)): ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Workflow History</h2>
                <span style="font-size:11px; color:var(--color-text-muted);">
                    Complete audit trail of this document
                </span>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:120px;">Action</th>
                            <th>Notes / Reason</th>
                            <th style="width:150px;">By</th>
                            <th style="width:140px;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workflowHistory as $log): ?>
                        <tr>
                            <td>
                                <?php
                                $badgeClass = match($log['action']) {
                                    'endorsed'              => 'badge-endorsed',
                                    'approved'              => 'badge-approved',
                                    'enacted'               => 'badge-enacted',
                                    'rejected'              => 'badge-rejected',
                                    'returned_for_revision' => 'badge-draft',
                                    default                 => 'badge-draft',
                                };
                                $actionLabel = match($log['action']) {
                                    'endorsed'              => 'Endorsed',
                                    'approved'              => 'Approved',
                                    'enacted'               => 'Enacted',
                                    'rejected'              => 'Rejected',
                                    'returned_for_revision' => 'Returned for Revision',
                                    default                 => ucfirst($log['action']),
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>" style="font-size:11px;">
                                    <?= $actionLabel ?>
                                </span>
                            </td>
                            <td style="font-size:12px; color:<?= $log['action'] === 'rejected' ? '#842029' : 'var(--color-text-muted)' ?>;">
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

    <!-- RIGHT: Status + AI Report -->
    <div>

        <!-- Archive Status -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><h2 class="card-title">Status</h2></div>
            <div class="card-body">
                <div style="text-align:center; padding:16px;">
                    <div style="display:inline-flex; align-items:center; justify-content:center;
                                width:60px; height:60px; border-radius:50%;
                                background-color:#f8d7da; margin-bottom:12px;">
                        <div style="color:#dc3545; display:inline-flex; align-items:center; justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </div>
                    </div>
                    <div style="font-size:18px; font-weight:700; color:#dc3545; margin-bottom:6px;">
                        REJECTED
                    </div>
                    <div style="font-size:12px; color:var(--color-text-muted); line-height:1.6; margin-bottom:16px;">
                        This document was permanently rejected and
                        moved to the Archive. It cannot be reactivated.
                    </div>
                    <?php if ($document['status'] === 'rejected' && in_array($_SESSION['user_role'] ?? '', ['legislative_staff', 'super_admin'])): ?>
                    <button type="button" class="btn btn-sm btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#overrideModal">
                        Override Committee Decision
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Override Modal -->
        <?php if ($document['status'] === 'rejected' && in_array($_SESSION['user_role'] ?? '', ['legislative_staff', 'super_admin'])): ?>
        <div class="modal fade" id="overrideModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="<?= APP_ROOT_URL ?>/archive/override/<?= $docType ?>/<?= $document['id'] ?>" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger">Override Committee Decision</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start">
                            <div class="alert alert-warning" style="font-size:13px;">
                                <strong>Warning:</strong> You are overriding a committee rejection. This requires a <strong>2/3 override vote</strong> by the council.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Reason / Reference (e.g. Minutes of Meeting Date) <span class="form-required">*</span></label>
                                <textarea name="override_reason" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Override</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- AI Validation (if any) -->
        <div class="card">
            <div class="card-header"><h2 class="card-title">AI Validation</h2></div>
            <div class="card-body">
                <?php if (!empty($aiReport)): ?>
                    <?php
                    $aiColor = match($aiReport['validation_status']) {
                        'passed'  => 'var(--color-success)',
                        'flagged' => '#fd7e14',
                        'failed'  => '#dc3545',
                        default   => 'var(--color-text-muted)',
                    };
                    ?>
                    <div style="background-color:<?= $aiColor ?>; color:#fff;
                                border-radius:var(--radius); padding:10px 14px;
                                margin-bottom:14px;
                                display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-size:10px; font-weight:700; text-transform:uppercase;
                                        letter-spacing:0.5px; opacity:0.85;">AI Result</div>
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
                                            ? 'var(--color-success)' : '#fd7e14' ?>;">
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
                                            ? '#dc3545' : 'var(--color-success)' ?>;">
                                <?= number_format((float)$aiReport['similarity_score'], 0) ?>%
                            </div>
                            <div style="font-size:11px; color:var(--color-text-muted);">
                                Similarity
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($aiReport['ai_summary'])): ?>
                    <div style="font-size:12px; line-height:1.6; color:var(--color-text);
                                background:var(--color-bg); border:1px solid var(--color-border-light);
                                border-radius:var(--radius); padding:12px;">
                        <?= htmlspecialchars($aiReport['ai_summary']) ?>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="text-align:center; padding:16px; font-size:13px;
                                color:var(--color-text-muted);">
                        No AI validation report was generated for this document.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>
