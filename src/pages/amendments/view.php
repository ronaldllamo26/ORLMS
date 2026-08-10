<?php
/**
 * ORLMS - Amendment Detail View
 *
 * Variables:
 *   $amendment - array of amendment info with joined target document data
 *   $userRole  - role of the current user
 */

$statusColors = [
    'draft'     => ['bg' => '#f8f9fa', 'text' => 'var(--color-text-muted)', 'border' => 'var(--color-border)'],
    'submitted' => ['bg' => '#fff8e1', 'text' => '#856404', 'border' => '#ffe69c'],
    'approved'  => ['bg' => '#d1e7dd', 'text' => '#0f5132', 'border' => '#badbcc'],
    'rejected'  => ['bg' => '#f8d7da', 'text' => '#842029', 'border' => '#f5c2c7'],
];
$status = $amendment['status'] ?? 'draft';
$sc = $statusColors[$status] ?? $statusColors['draft'];
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
                    <a href="<?= APP_ROOT_URL ?>/amendments">Amendments</a>
                </li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($amendment['amendment_no']) ?></li>
            </ul>
            <h1 class="page-title">Amendment: <?= htmlspecialchars($amendment['amendment_no']) ?></h1>
            <p class="page-subtitle">Modifying <?= htmlspecialchars($amendment['doc_no']) ?>: <?= htmlspecialchars($amendment['doc_title']) ?></p>
        </div>
        <a href="<?= APP_ROOT_URL ?>/amendments" class="btn btn-outline-secondary btn-sm">
            Back to Amendments
        </a>
    </div>
</div>

<!-- Amendment Status Block -->
<div style="background-color:<?= $sc['bg'] ?>; border:1px solid <?= $sc['border'] ?>;
            border-radius:var(--radius); padding:18px 20px; margin-bottom:20px;
            display:flex; justify-content:space-between; align-items:center;">
    <div>
        <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                    letter-spacing:0.5px; color:<?= $sc['text'] ?>; margin-bottom:6px;">
            Amendment Status
        </div>
        <div style="font-size:22px; font-weight:700; color:<?= $sc['text'] ?>;
                    text-transform:uppercase; letter-spacing:0.5px;">
            <?= ucfirst($status) ?>
        </div>
    </div>
    <div style="text-align:right; font-size:12px; color:var(--color-text-muted);">
        Drafted by <strong><?= htmlspecialchars($amendment['amended_by_name'] ?? 'Unknown') ?></strong>
        on <?= date('F d, Y h:i A', strtotime($amendment['amended_at'])) ?>
    </div>
</div>

<div class="row row-2-1" style="align-items:start; gap:20px;">

    <!-- Left: Content & Rationale -->
    <div>
        <!-- Rationale -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Rationale & Description</h2>
            </div>
            <div class="card-body">
                <div style="font-size:14px; line-height:1.7; color:var(--color-text); white-space:pre-wrap;"><?= htmlspecialchars($amendment['description']) ?></div>
            </div>
        </div>

        <!-- Changes Detail -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Revision Details / Changes</h2>
            </div>
            <div class="card-body">
                <div style="background:#fdfdfd; border:1px solid var(--color-border-light);
                            border-radius:var(--radius); padding:18px; font-family:monospace;
                            font-size:13px; line-height:1.7; color:#333; white-space:pre-wrap;
                            max-height:500px; overflow-y:auto;"><?= htmlspecialchars($amendment['changes']) ?></div>
            </div>
        </div>
    </div>

    <!-- Right: Target Document and Action Pane -->
    <div>
        <!-- Actions Card -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Actions</h2>
            </div>
            <div class="card-body">
                <!-- If Draft and owner (or legislative staff/admin) can submit -->
                <?php if ($status === 'draft'): ?>
                    <form method="POST" action="<?= APP_ROOT_URL ?>/amendments/submit/<?= $amendment['id'] ?>"
                          onsubmit="return confirm('Submit this amendment for review and approval?')">
                        <button type="submit" class="btn btn-primary" style="width:100%;" id="btn-submit-amendment">
                            Submit Amendment
                        </button>
                    </form>
                    <div style="font-size:11px; text-align:center; color:var(--color-text-muted); margin-top:8px;">
                        Once submitted, this amendment will go to the Super Admin for approval.
                    </div>
                <?php endif; ?>

                <!-- If Submitted and current user is Admin -->
                <?php if ($status === 'submitted' && $userRole === 'super_admin'): ?>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <form method="POST" action="<?= APP_ROOT_URL ?>/amendments/approve/<?= $amendment['id'] ?>"
                              onsubmit="return confirm('Are you sure you want to approve this amendment? This will officially record the changes to the system.')">
                            <button type="submit" class="btn btn-success" style="width:100%; background:var(--color-success); border-color:var(--color-success); color:#fff;" id="btn-approve-amendment">
                                Approve Amendment
                            </button>
                        </form>

                        <div style="border-top:1px solid var(--color-border-light); margin:10px 0; padding-top:10px;">
                            <form method="POST" action="<?= APP_ROOT_URL ?>/amendments/reject/<?= $amendment['id'] ?>"
                                  id="reject-amendment-form">
                                <div class="form-group">
                                    <label class="form-label" style="font-size:11px;">Rejection Reason <span class="form-required">*</span></label>
                                    <textarea name="reason" class="form-control" rows="3" placeholder="State reason for rejecting..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger" style="width:100%; background:#dc3545; border-color:#dc3545; color:#fff;" id="btn-reject-amendment">
                                    Reject Amendment
                                </button>
                            </form>
                        </div>
                    </div>
                <?php elseif ($status === 'submitted'): ?>
                    <div style="text-align:center; padding:12px; background:var(--color-bg);
                                border-radius:var(--radius); font-size:12px; color:var(--color-text-muted); border:1px solid var(--color-border-light);">
                        Awaiting Administrator review and approval.
                    </div>
                <?php endif; ?>

                <!-- If Approved / Rejected -->
                <?php if (in_array($status, ['approved', 'rejected'])): ?>
                    <div style="text-align:center; padding:12px; background:var(--color-bg);
                                border-radius:var(--radius); font-size:12px; color:var(--color-text-muted); border:1px solid var(--color-border-light);">
                        This amendment is locked because it is already <strong><?= $status ?></strong>.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Target Document Details -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Target Document</h2>
                <span class="badge badge-<?= $amendment['doc_status'] ?? 'enacted' ?>" style="font-size:11px; text-transform:uppercase;">
                    <?= htmlspecialchars($amendment['doc_status'] ?? '') ?>
                </span>
            </div>
            <div class="card-body">
                <div class="doc-meta-grid">
                    <span class="doc-meta-label">Document No.</span>
                    <span class="doc-meta-value" style="font-weight:700; color:var(--color-primary);">
                        <?= htmlspecialchars($amendment['doc_no'] ?? '—') ?>
                    </span>

                    <span class="doc-meta-label">Type</span>
                    <span class="doc-meta-value" style="text-transform:capitalize;">
                        <?= htmlspecialchars($amendment['document_type']) ?>
                    </span>

                    <span class="doc-meta-label">Title</span>
                    <span class="doc-meta-value" style="font-size:12px;">
                        <?= htmlspecialchars($amendment['doc_title'] ?? '—') ?>
                    </span>
                </div>
                <div style="margin-top:16px; padding-top:16px; border-top:1px solid var(--color-border-light);">
                    <a href="<?= APP_ROOT_URL ?>/<?= $amendment['document_type'] ?>/view/<?= $amendment['document_id'] ?>"
                       class="btn btn-outline btn-sm" style="width:100%; text-align:center;">
                        View Original Document &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
