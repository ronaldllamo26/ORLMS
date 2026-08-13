<?php
/**
 * ORLMS - Ordinance Detail View
 *
 * @var array $ordinance
 * @var array|false $aiReport
 * @var array $reviewHistory
 * @var array $committees
 */

$userRole  = $_SESSION['user_role'] ?? '';
$canEdit   = in_array($userRole, ['legislative_staff', 'super_admin'])
             && $ordinance['status'] === 'draft';
$canSubmit = in_array($userRole, ['legislative_staff', 'super_admin'])
             && $ordinance['status'] === 'draft';
$canDelete = in_array($userRole, ['legislative_staff', 'super_admin'])
             && $ordinance['status'] === 'draft';

// Status badge helper
function viewOrdBadge(string $status): string {
    return match($status) {
        'draft'         => 'badge-draft',
        'submitted',
        'pending_review'=> 'badge-submitted',
        'under_review'  => 'badge-under-review',
        'endorsed'      => 'badge-endorsed',
        'approved'      => 'badge-approved',
        'enacted'       => 'badge-enacted',
        'published'     => 'badge-published',
        'rejected'      => 'badge-rejected',
        'archived'      => 'badge-archived',
        'implemented'   => 'badge-implemented',
        'amended'       => 'badge-amended',
        default         => 'badge-draft',
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
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/ordinance">Ordinances</a>
                </li>
                <li class="breadcrumb-item active">
                    <?= htmlspecialchars($ordinance['ordinance_no'] ?? 'View') ?>
                </li>
            </ul>
            <h1 class="page-title">
                <?= htmlspecialchars($ordinance['ordinance_no'] ?? 'Ordinance Detail') ?>
            </h1>
            <p class="page-subtitle">
                <?= htmlspecialchars($ordinance['title']) ?>
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-8">

            <?php if ($canEdit): ?>
            <a href="<?= APP_ROOT_URL ?>/ordinance/edit/<?= $ordinance['id'] ?>"
               class="btn btn-outline btn-sm"
               id="btn-edit-ordinance">
                Edit
            </a>
            <?php endif; ?>

            <?php if ($canSubmit): ?>
            <a href="<?= APP_ROOT_URL ?>/ordinance/submit/<?= $ordinance['id'] ?>"
               class="btn btn-primary btn-sm"
               id="btn-submit-ordinance"
               onclick="return confirm('Submit this ordinance for review? This cannot be undone.')">
                Submit for Review
            </a>
            <?php endif; ?>

            <?php if ($canDelete): ?>
            <a href="<?= APP_ROOT_URL ?>/ordinance/delete/<?= $ordinance['id'] ?>"
               class="btn btn-danger btn-sm"
               id="btn-delete-ordinance"
               onclick="return confirm('Permanently delete this draft ordinance? This action cannot be undone.')">
                Delete Draft
            </a>
            <?php endif; ?>

            <a href="<?= APP_ROOT_URL ?>/ordinance"
               class="btn btn-outline-secondary btn-sm">
                Back to List
            </a>
        </div>
    </div>
</div>

<!-- ── Main Content Grid ─────────────────────────────────── -->
<div class="row row-2-1">

    <!-- LEFT: Document Content -->
    <div>

        <!-- Document Metadata Card -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Document Information</h2>
                <span class="badge <?= viewOrdBadge($ordinance['status']) ?>"
                      style="font-size:12px;">
                    <?= ucfirst(str_replace('_', ' ', $ordinance['status'])) ?>
                </span>
            </div>
            <div class="card-body">
                <div class="doc-meta-grid">

                    <span class="doc-meta-label">Ordinance No.</span>
                    <span class="doc-meta-value" style="font-weight:600; color:var(--color-primary);">
                        <?= htmlspecialchars($ordinance['ordinance_no'] ?? 'Not yet assigned') ?>
                    </span>

                    <span class="doc-meta-label">Title</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($ordinance['title']) ?>
                    </span>

                    <span class="doc-meta-label">Subject</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($ordinance['subject']) ?>
                    </span>

                    <span class="doc-meta-label">Author</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($ordinance['author_name'] ?? 'Unknown') ?>
                    </span>

                    <?php if (!empty($ordinance['committee_name'])): ?>
                    <span class="doc-meta-label">Committee</span>
                    <span class="doc-meta-value" style="font-weight:600; color:var(--color-primary);">
                        <?= htmlspecialchars($ordinance['committee_name']) ?>
                    </span>
                    <?php endif; ?>

                    <span class="doc-meta-label">Date Filed</span>
                    <span class="doc-meta-value">
                        <?= $ordinance['date_filed']
                            ? date('F d, Y', strtotime($ordinance['date_filed']))
                            : '—' ?>
                    </span>

                    <span class="doc-meta-label">Date Created</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= date('F d, Y h:i A', strtotime($ordinance['created_at'])) ?>
                    </span>

                    <span class="doc-meta-label">Last Updated</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= date('F d, Y h:i A', strtotime($ordinance['updated_at'])) ?>
                    </span>

                    <?php if (!empty($ordinance['file_path'])): ?>
                    <span class="doc-meta-label">Attached File</span>
                    <span class="doc-meta-value">
                        <a href="<?= APP_URL ?>/public/uploads/documents/<?= htmlspecialchars($ordinance['file_path']) ?>"
                           target="_blank"
                           style="color:var(--color-accent);">
                            Download File
                        </a>
                    </span>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Document Full Content -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Document Content</h2>
                <span style="font-size:11px; color:var(--color-text-muted);">
                    Full text of the ordinance
                </span>
            </div>
            <div class="card-body">
                <div class="doc-content-body" id="ordinance-content-body">
                    <?= htmlspecialchars($ordinance['content']) ?>
                </div>
            </div>
        </div>

        <!-- Public Consultation & Hearings History -->
        <div class="card" style="margin-top:20px; margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="bi bi-people-fill me-1" style="color:var(--color-accent);"></i> Public Consultation & Hearings
                </h2>
                <span style="font-size:11px; color:var(--color-text-muted);">
                    Feedback from the Public Hearing Subsystem
                </span>
            </div>
            <div class="card-body" style="padding: 20px;">
                <?php if (empty($consultations)): ?>
                    <div style="text-align:center; padding:15px; color:var(--color-text-muted); font-size:13.5px; background:var(--color-bg); border:1px solid var(--color-border-light); border-radius:var(--radius);">
                        No public hearings or consultation records have been submitted for this document yet.
                    </div>
                <?php else: ?>
                    <div style="display:flex; flex-direction:column; gap:16px;">
                        <?php foreach ($consultations as $consult): ?>
                            <div style="border: 1px solid var(--color-border-light); border-radius: var(--radius-lg); padding: 16px; background: #fafafa;">
                                <div style="display:flex; justify-content:space-between; align-items:start; flex-wrap:wrap; gap:8px; border-bottom:1px solid var(--color-border-light); padding-bottom:8px; margin-bottom:12px;">
                                    <div>
                                        <strong style="color:var(--color-primary); font-size:14px;">Hearing Venue: <?= htmlspecialchars($consult['venue']) ?></strong>
                                        <div style="font-size:11.5px; color:var(--color-text-muted); margin-top:2px;">
                                            Held on <?= date('F d, Y', strtotime($consult['hearing_date'])) ?>
                                        </div>
                                    </div>
                                    <div style="text-align:right;">
                                        <span class="badge badge-draft" style="font-size:11px; padding:3px 8px; background: rgba(0, 132, 255, 0.1); color: #0084FF; border: 1px solid rgba(0, 132, 255, 0.2);">
                                            <?= htmlspecialchars($consult['total_participants']) ?> Participants
                                        </span>
                                    </div>
                                </div>
                                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px; font-size:13px; margin-bottom:12px;">
                                    <div>
                                        <span style="display:block; font-size:11px; text-transform:uppercase; color:#64748b; font-weight:600;">Opinions Gathered</span>
                                        <strong><?= htmlspecialchars($consult['total_opinions']) ?> comments</strong>
                                    </div>
                                    <div>
                                        <span style="display:block; font-size:11px; text-transform:uppercase; color:#64748b; font-weight:600;">Sentiment Split</span>
                                        <strong style="color:#2ec4b6;"><?= htmlspecialchars($consult['sentiment_summary'] ?: 'N/A') ?></strong>
                                    </div>
                                    <?php if (!empty($consult['report_file_url'])): ?>
                                    <div>
                                        <span style="display:block; font-size:11px; text-transform:uppercase; color:#64748b; font-weight:600;">Official Report</span>
                                        <a href="<?= htmlspecialchars($consult['report_file_url']) ?>" target="_blank" style="color:var(--color-accent); font-weight:600; text-decoration:none;">
                                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> View Report &rarr;
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($consult['summary_report'])): ?>
                                    <div style="font-size:12.5px; line-height:1.6; padding:12px; background:#fff; border:1px solid #e2e8f0; border-radius:var(--radius); color:var(--color-text);">
                                        <strong style="display:block; font-size:11px; text-transform:uppercase; color:#64748b; margin-bottom:4px;">Consultation Summary</strong>
                                        <?= nl2br(htmlspecialchars($consult['summary_report'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- RIGHT: Status Panel + AI Validation -->
    <div>

        <!-- Committee Referral Card (Only for admin/staff when status is submitted/under_review) -->
        <?php if (in_array($userRole, ['legislative_staff', 'super_admin']) && in_array($ordinance['status'], ['submitted', 'under_review'])): ?>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Committee Referral</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= APP_ROOT_URL ?>/ordinance/refer/<?= $ordinance['id'] ?>">
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="committee_id" class="form-label" style="font-size:12px;">Select Committee</label>
                        <select name="committee_id" id="committee_id" class="form-select" style="font-size:13px;" required>
                            <option value="">-- Select Committee --</option>
                            <?php foreach ($committees as $comm): ?>
                                <option value="<?= $comm['id'] ?>" <?= ($ordinance['committee_id'] == $comm['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($comm['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width:100%;">
                        <?= $ordinance['committee_id'] ? 'Re-refer to Committee' : 'Refer to Committee' ?>
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Current Status & Visual Legislation Trail Card -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h2 class="card-title" style="font-size:14px; font-weight:700;">
                    <i class="bi bi-diagram-3-fill me-1" style="color:var(--color-primary);"></i> Legislation Trail
                </h2>
                <span style="font-size:10px; font-weight:700; text-transform:uppercase; color:var(--color-text-muted); background:var(--color-bg); padding:2px 8px; border-radius:10px; border:1px solid var(--color-border-light);">
                    Lifecycle History
                </span>
            </div>
            <div class="card-body" style="padding:16px;">

                <!-- Enhanced Visual Status Timeline -->
                <div style="display:flex; flex-direction:column; gap:0;">

                    <?php
                    $stages = [
                        'draft'        => ['label' => '1. Filing & Encoding', 'icon' => 'bi-pencil-square', 'desc' => 'Document drafted by author'],
                        'submitted'    => ['label' => '2. AI & First Reading', 'icon' => 'bi-robot', 'desc' => 'Read in plenary & AI checked'],
                        'under_review' => ['label' => '3. Committee Review', 'icon' => 'bi-people', 'desc' => 'Assigned to committee'],
                        'endorsed'     => ['label' => '4. Committee Endorsed', 'icon' => 'bi-file-earmark-check', 'desc' => 'Recom. approved for 2nd Reading'],
                        'approved'     => ['label' => '5. 3rd Reading Voted', 'icon' => 'bi-check2-square', 'desc' => 'Plenary voting passed'],
                        'enacted'      => ['label' => '6. Mayor Signature', 'icon' => 'bi-pen', 'desc' => 'Signed & approved by LCE'],
                        'published'    => ['label' => '7. Published to Registry', 'icon' => 'bi-globe', 'desc' => 'Active & searchable in portal'],
                    ];

                    $stageKeys  = array_keys($stages);
                    $currentIdx = array_search($ordinance['status'], $stageKeys);
                    if ($currentIdx === false) $currentIdx = -1;
                    ?>

                    <?php foreach ($stages as $key => $info): ?>
                        <?php
                        $idx       = array_search($key, $stageKeys);
                        $isDone    = $idx < $currentIdx;
                        $isCurrent = $key === $ordinance['status'];
                        
                        $nodeBg = $isCurrent ? 'var(--color-accent)' : ($isDone ? 'var(--color-success)' : '#e2e8f0');
                        $nodeColor = ($isCurrent || $isDone) ? '#ffffff' : '#64748b';
                        $borderLeft = ($idx < count($stages) - 1) ? "border-left: 2px dashed " . ($isDone ? "var(--color-success)" : "#cbd5e1") . ";" : "";
                        ?>
                        <div style="display:flex; gap:12px; position:relative; padding-bottom: 16px;">
                            <?php if ($idx < count($stages) - 1): ?>
                            <div style="position:absolute; left:13px; top:28px; bottom:0; width:2px; <?= $borderLeft ?>"></div>
                            <?php endif; ?>
                            
                            <div style="width:28px; height:28px; border-radius:50%; background:<?= $nodeBg ?>; color:<?= $nodeColor ?>; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; z-index:1; shadow:var(--shadow-sm);">
                                <i class="bi <?= $info['icon'] ?>"></i>
                            </div>
                            
                            <div style="flex-grow:1; <?= (!$isDone && !$isCurrent) ? 'opacity:0.5;' : '' ?>">
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12.5px; font-weight:<?= $isCurrent ? '700' : '600' ?>; color:<?= $isCurrent ? 'var(--color-primary)' : 'var(--color-text)' ?>;">
                                        <?= $info['label'] ?>
                                    </span>
                                    <?php if ($isCurrent): ?>
                                        <span class="badge" style="font-size:9.5px; background:var(--color-accent); color:#fff; padding:2px 6px;">CURRENT</span>
                                    <?php elseif ($isDone): ?>
                                        <i class="bi bi-check-circle-fill text-success" style="font-size:12px;"></i>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">
                                    <?= $info['desc'] ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if ($ordinance['status'] === 'rejected'): ?>
                    <div style="display:flex; gap:12px; padding-top:4px;">
                        <div style="width:28px; height:28px; border-radius:50%; background:#dc3545; color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0;">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div>
                            <span style="font-size:12.5px; font-weight:700; color:#dc3545;">Document Rejected</span>
                            <div style="font-size:11px; color:var(--color-text-muted);">Returned or disapproved during review</div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- AI Validation Report Panel -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">AI Validation</h2>
            </div>
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
                    <!-- Status Banner -->
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
                           style="font-size:11px; color:#fff; text-decoration:underline; opacity:0.85;">
                            Full Report &rarr;
                        </a>
                    </div>
                    <!-- Scores -->
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
                    <a href="<?= APP_ROOT_URL ?>/ai_validation/run/ordinance/<?= $ordinance['id'] ?>"
                       class="btn btn-outline-secondary btn-sm"
                       style="width:100%; text-align:center; display:block; margin-top:8px;"
                       id="btn-rerun-ai">
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
                        <a href="<?= APP_ROOT_URL ?>/ai_validation/run/ordinance/<?= $ordinance['id'] ?>"
                           class="btn btn-primary btn-sm" id="btn-run-ai-validation">
                            Run AI Validation
                        </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>
