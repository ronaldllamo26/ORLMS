<?php
/**
 * ORLMS - Resolution Detail View
 *
 * Variables passed from ResolutionController::view():
 *   $resolution — single resolution record with author_name
 */

$userRole  = $_SESSION['user_role'] ?? '';
$canEdit   = in_array($userRole, ['legislative_staff', 'super_admin']) && $resolution['status'] === 'draft';
$canSubmit = in_array($userRole, ['legislative_staff', 'super_admin']) && $resolution['status'] === 'draft';
$canDelete = in_array($userRole, ['legislative_staff', 'super_admin']) && $resolution['status'] === 'draft';

function viewResBadge(string $status): string {
    return match($status) {
        'draft'          => 'badge-draft',
        'submitted',
        'pending_review' => 'badge-submitted',
        'under_review'   => 'badge-under-review',
        'endorsed'       => 'badge-endorsed',
        'approved'       => 'badge-approved',
        'enacted'        => 'badge-enacted',
        'published'      => 'badge-published',
        'rejected'       => 'badge-rejected',
        'archived'       => 'badge-archived',
        'implemented'    => 'badge-implemented',
        'amended'        => 'badge-amended',
        default          => 'badge-draft',
    };
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= APP_ROOT_URL ?>/resolution">Resolutions</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($resolution['resolution_no'] ?? 'View') ?></li>
            </ul>
            <h1 class="page-title"><?= htmlspecialchars($resolution['resolution_no'] ?? 'Resolution Detail') ?></h1>
            <p class="page-subtitle"><?= htmlspecialchars($resolution['title']) ?></p>
        </div>
        <div class="d-flex gap-8">
            <?php if ($canEdit): ?>
            <a href="<?= APP_ROOT_URL ?>/resolution/edit/<?= $resolution['id'] ?>"
               class="btn btn-outline btn-sm" id="btn-edit-resolution">Edit</a>
            <?php endif; ?>
            <?php if ($canSubmit): ?>
            <a href="<?= APP_ROOT_URL ?>/resolution/submit/<?= $resolution['id'] ?>"
               class="btn btn-primary btn-sm" id="btn-submit-resolution"
               onclick="return confirm('Submit this resolution for review? This cannot be undone.')">
                Submit for Review
            </a>
            <?php endif; ?>
            <?php if ($canDelete): ?>
            <a href="<?= APP_ROOT_URL ?>/resolution/delete/<?= $resolution['id'] ?>"
               class="btn btn-danger btn-sm" id="btn-delete-resolution"
               onclick="return confirm('Permanently delete this draft resolution? This cannot be undone.')">
                Delete Draft
            </a>
            <?php endif; ?>
            <a href="<?= APP_ROOT_URL ?>/resolution" class="btn btn-outline-secondary btn-sm">Back to List</a>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row row-2-1">

    <!-- LEFT: Document Content -->
    <div>
        <!-- Metadata Card -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Document Information</h2>
                <span class="badge <?= viewResBadge($resolution['status']) ?>" style="font-size:12px;">
                    <?= ucfirst(str_replace('_', ' ', $resolution['status'])) ?>
                </span>
            </div>
            <div class="card-body">
                <div class="doc-meta-grid">
                    <span class="doc-meta-label">Resolution No.</span>
                    <span class="doc-meta-value" style="font-weight:600; color:var(--color-primary);">
                        <?= htmlspecialchars($resolution['resolution_no'] ?? 'Not yet assigned') ?>
                    </span>

                    <span class="doc-meta-label">Title</span>
                    <span class="doc-meta-value"><?= htmlspecialchars($resolution['title']) ?></span>

                    <span class="doc-meta-label">Subject</span>
                    <span class="doc-meta-value"><?= htmlspecialchars($resolution['subject']) ?></span>

                    <span class="doc-meta-label">Author</span>
                    <span class="doc-meta-value"><?= htmlspecialchars($resolution['author_name'] ?? 'Unknown') ?></span>

                    <?php if (!empty($resolution['committee_name'])): ?>
                    <span class="doc-meta-label">Committee</span>
                    <span class="doc-meta-value" style="font-weight:600; color:var(--color-primary);">
                        <?= htmlspecialchars($resolution['committee_name']) ?>
                    </span>
                    <?php endif; ?>

                    <span class="doc-meta-label">Date Filed</span>
                    <span class="doc-meta-value">
                        <?= $resolution['date_filed'] ? date('F d, Y', strtotime($resolution['date_filed'])) : '—' ?>
                    </span>

                    <span class="doc-meta-label">Date Created</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= date('F d, Y h:i A', strtotime($resolution['created_at'])) ?>
                    </span>

                    <span class="doc-meta-label">Last Updated</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= date('F d, Y h:i A', strtotime($resolution['updated_at'])) ?>
                    </span>

                    <?php if (!empty($resolution['file_path'])): ?>
                    <span class="doc-meta-label">Attached File</span>
                    <span class="doc-meta-value">
                        <a href="<?= APP_URL ?>/public/uploads/documents/<?= htmlspecialchars($resolution['file_path']) ?>"
                           target="_blank" style="color:var(--color-accent);">Download File</a>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Content Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Document Content</h2>
                <span style="font-size:11px; color:var(--color-text-muted);">Full text of the resolution</span>
            </div>
            <div class="card-body">
                <div class="doc-content-body" id="resolution-content-body">
                    <?= htmlspecialchars($resolution['content']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Status + AI Panel -->
    <div>
        <!-- Committee Referral Card (Only for admin/staff when status is submitted/under_review) -->
        <?php if (in_array($userRole, ['legislative_staff', 'super_admin']) && in_array($resolution['status'], ['submitted', 'under_review'])): ?>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Committee Referral</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= APP_ROOT_URL ?>/resolution/refer/<?= $resolution['id'] ?>">
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="committee_id" class="form-label" style="font-size:12px;">Select Committee</label>
                        <select name="committee_id" id="committee_id" class="form-select" style="font-size:13px;" required>
                            <option value="">-- Select Committee --</option>
                            <?php foreach ($committees as $comm): ?>
                                <option value="<?= $comm['id'] ?>" <?= ($resolution['committee_id'] == $comm['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($comm['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width:100%;">
                        <?= $resolution['committee_id'] ? 'Re-refer to Committee' : 'Refer to Committee' ?>
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Status Timeline -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><h2 class="card-title">Document Status</h2></div>
            <div class="card-body">
                <?php
                $stages    = ['draft' => 'Draft', 'submitted' => 'Submitted for Review',
                              'under_review' => 'Under Review', 'endorsed' => 'Endorsed',
                              'approved' => 'Approved', 'enacted' => 'Enacted', 'published' => 'Published'];
                $stageKeys = array_keys($stages);
                $currentIdx = array_search($resolution['status'], $stageKeys);
                if ($currentIdx === false) $currentIdx = -1;
                ?>
                <?php foreach ($stages as $key => $label): ?>
                    <?php
                    $idx       = array_search($key, $stageKeys);
                    $isDone    = $idx < $currentIdx;
                    $isCurrent = $key === $resolution['status'];
                    ?>
                    <div style="display:flex; align-items:center; gap:12px; padding:8px 0;
                                <?= !$isDone && !$isCurrent ? 'opacity:0.4;' : '' ?>
                                border-bottom:1px solid var(--color-border-light);">
                        <div style="width:10px; height:10px; border-radius:50%; flex-shrink:0;
                                    background-color:<?= $isCurrent ? 'var(--color-accent)' : ($isDone ? 'var(--color-success)' : 'var(--color-border)') ?>;"></div>
                        <span style="font-size:13px; font-weight:<?= $isCurrent ? '600' : '400' ?>;
                                     color:<?= $isCurrent ? 'var(--color-primary)' : 'var(--color-text-muted)' ?>;">
                            <?= $label ?>
                            <?php if ($isCurrent): ?>
                                <span style="font-size:10px; color:var(--color-accent); margin-left:6px;
                                             text-transform:uppercase; letter-spacing:0.5px;">&larr; Current</span>
                            <?php elseif ($isDone): ?>
                                <span style="font-size:10px; color:var(--color-success); margin-left:6px;">&check;</span>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
                <?php if ($resolution['status'] === 'rejected'): ?>
                <div style="display:flex; align-items:center; gap:12px; padding:8px 0;">
                    <div style="width:10px; height:10px; border-radius:50%; background-color:#dc3545; flex-shrink:0;"></div>
                    <span style="font-size:13px; font-weight:600; color:#dc3545;">Rejected &larr; Current</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- AI Validation Panel -->
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
                                border-radius:var(--radius); padding:10px 14px; margin-bottom:14px;
                                display:flex; justify-content:space-between; align-items:center;">
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
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px;">
                        <div style="background:var(--color-bg); border:1px solid var(--color-border);
                                    border-radius:var(--radius); padding:10px; text-align:center;">
                            <div style="font-size:20px; font-weight:700;
                                        color:<?= (int)$aiReport['completeness_score'] >= 80
                                            ? 'var(--color-success)' : '#fd7e14' ?>;">
                                <?= (int)$aiReport['completeness_score'] ?>%
                            </div>
                            <div style="font-size:11px; color:var(--color-text-muted);">Completeness</div>
                        </div>
                        <div style="background:var(--color-bg); border:1px solid var(--color-border);
                                    border-radius:var(--radius); padding:10px; text-align:center;">
                            <div style="font-size:20px; font-weight:700;
                                        color:<?= (float)$aiReport['similarity_score'] > 60
                                            ? '#dc3545' : 'var(--color-success)' ?>;">
                                <?= number_format((float)$aiReport['similarity_score'], 0) ?>%
                            </div>
                            <div style="font-size:11px; color:var(--color-text-muted);">Similarity</div>
                        </div>
                    </div>
                    <?php if (!empty($aiReport['ai_summary'])): ?>
                    <div style="font-size:12px; line-height:1.6; color:var(--color-text);
                                background:var(--color-bg); border:1px solid var(--color-border-light);
                                border-radius:var(--radius); padding:12px; margin-bottom:12px;">
                        <?= htmlspecialchars($aiReport['ai_summary']) ?>
                    </div>
                    <?php endif; ?>
                    <a href="<?= APP_ROOT_URL ?>/ai_validation/report/<?= $aiReport['id'] ?>"
                       class="btn btn-outline btn-sm" style="width:100%; text-align:center;">
                        View Full Report
                    </a>
                    <?php if (in_array($userRole, ['super_admin'])): ?>
                    <a href="<?= APP_ROOT_URL ?>/ai_validation/run/resolution/<?= $resolution['id'] ?>"
                       class="btn btn-outline-secondary btn-sm"
                       style="width:100%; text-align:center; display:block; margin-top:8px;">
                        Re-run AI Validation
                    </a>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="text-align:center; padding:16px; font-size:13px;
                                color:var(--color-text-muted); background:var(--color-bg);
                                border-radius:var(--radius); border:1px solid var(--color-border);">
                        <div style="font-weight:600; color:var(--color-primary); margin-bottom:6px;">
                            No AI Report Yet
                        </div>
                        <div style="font-size:12px; line-height:1.6; margin-bottom:12px;">
                            Run AI Validation to check completeness and detect duplicates.
                        </div>
                        <?php if (in_array($userRole, ['super_admin'])): ?>
                        <a href="<?= APP_ROOT_URL ?>/ai_validation/run/resolution/<?= $resolution['id'] ?>"
                           class="btn btn-primary btn-sm" id="btn-run-ai-validation-res">
                            Run AI Validation
                        </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
