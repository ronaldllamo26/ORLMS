<?php
/**
 * ORLMS - AI Validation Report Detail View
 *
 * @var array $report              The validation report record
 * @var array $completenessDetails Decoded JSON from completeness_details
 * @var array $similarityDetails   Decoded JSON from similarity_details
 */

$report              = $report ?? [];
$completenessDetails = $completenessDetails ?? [];
$similarityDetails   = $similarityDetails ?? [];

$valStatus = $report['validation_status'] ?? 'pending';

$statusColor = match($valStatus) {
    'passed'  => 'var(--color-success)',
    'flagged' => '#fd7e14',
    'failed'  => '#dc3545',
    default   => 'var(--color-text-muted)',
};

$statusBadge = match($valStatus) {
    'passed'  => 'badge-ai-passed',
    'flagged' => 'badge-ai-flagged',
    'failed'  => 'badge-ai-failed',
    default   => 'badge-draft',
};

$checks           = $completenessDetails['checks'] ?? [];
$missingSections  = $completenessDetails['missing_sections'] ?? [];
$completenessNote = $completenessDetails['notes'] ?? '';
$simExplanation   = $similarityDetails['explanation'] ?? '';
$completenessScore = (int) ($report['completeness_score'] ?? 0);
$similarityScore   = (float) ($report['similarity_score'] ?? 0);

$docUrl = APP_ROOT_URL . '/' . $report['document_type'] . '/view/' . $report['document_id'];
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= APP_ROOT_URL ?>/ai_validation">AI Validation Reports</a></li>
                <li class="breadcrumb-item active">Report #<?= $report['id'] ?></li>
            </ul>
            <h1 class="page-title">AI Validation Report</h1>
            <p class="page-subtitle">
                <a href="<?= $docUrl ?>" style="color:var(--color-primary); font-weight:600;">
                    <?= htmlspecialchars($report['document_no'] ?? 'Document') ?>
                </a>
                &mdash; <?= htmlspecialchars($report['document_title'] ?? '') ?>
            </p>
        </div>
        <div class="d-flex gap-8">
            <a href="<?= $docUrl ?>" class="btn btn-outline btn-sm">View Document</a>
            <a href="<?= APP_ROOT_URL ?>/ai_validation" class="btn btn-outline-secondary btn-sm">Back to Reports</a>
        </div>
    </div>
</div>

<!-- Overall Result Banner -->
<div style="background-color:<?= $statusColor ?>; color:#fff; border-radius:var(--radius);
            padding:16px 24px; margin-bottom:24px; display:flex;
            align-items:center; justify-content:space-between;">
    <div>
        <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                    letter-spacing:1px; opacity:0.85; margin-bottom:4px;">
            Overall Validation Result
        </div>
        <div style="font-size:22px; font-weight:700; letter-spacing:0.5px;">
            <?= strtoupper($report['validation_status']) ?>
        </div>
    </div>
    <div style="text-align:right; font-size:12px; opacity:0.85;">
        <div>Validated by: <?= htmlspecialchars($report['validated_by_name'] ?? 'System') ?></div>
        <div><?= date('F d, Y h:i A', strtotime($report['created_at'])) ?></div>
        <div style="text-transform:capitalize;">
            Document: <?= htmlspecialchars($report['document_type']) ?>
        </div>
    </div>
</div>

<div class="row row-2">

    <!-- LEFT COLUMN -->
    <div>

        <!-- Completeness Check -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Completeness Check</h2>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="ai-similarity-bar" style="width:100px; height:8px;">
                        <div class="ai-similarity-fill"
                             style="width:<?= $completenessScore ?>%;
                                    background-color:<?= $completenessScore >= 80 ? 'var(--color-success)' : ($completenessScore >= 60 ? '#fd7e14' : '#dc3545') ?>;">
                        </div>
                    </div>
                    <span style="font-size:18px; font-weight:700;
                                 color:<?= $completenessScore >= 80 ? 'var(--color-success)' : ($completenessScore >= 60 ? '#fd7e14' : '#dc3545') ?>;">
                        <?= $completenessScore ?>%
                    </span>
                </div>
            </div>
            <div class="card-body">

                <!-- Section Checklist -->
                <ul class="ai-checklist">
                    <?php
                    $checkLabels = [
                        'has_whereas'           => 'WHEREAS Clause(s)',
                        'has_enacting_clause'   => 'Enacting / Operative Clause (NOW THEREFORE / BE IT RESOLVED)',
                        'has_separability_clause' => 'Separability Clause',
                        'has_repealing_clause'  => 'Repealing Clause',
                        'has_effectivity_clause'=> 'Effectivity Clause',
                    ];
                    ?>
                    <?php foreach ($checkLabels as $key => $label): ?>
                    <?php $passed = !empty($checks[$key]); ?>
                    <li>
                        <span class="<?= $passed ? 'ai-check-pass' : 'ai-check-fail' ?>">
                            <?= $passed ? '&#10003;' : '&#10007;' ?>
                        </span>
                        <span style="font-size:13px; color:<?= $passed ? 'var(--color-text)' : '#dc3545' ?>;">
                            <?= $label ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <?php if (!empty($missingSections)): ?>
                <div style="margin-top:16px; padding:12px 14px; background:#fff3cd;
                            border:1px solid #ffe69c; border-radius:var(--radius); font-size:13px;">
                    <strong style="color:#856404;">Missing Sections:</strong>
                    <ul style="margin:6px 0 0 18px; color:#664d03;">
                        <?php foreach ($missingSections as $s): ?>
                        <li><?= htmlspecialchars($s) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($completenessNote)): ?>
                <div style="margin-top:14px; font-size:13px; color:var(--color-text-muted);
                            border-top:1px solid var(--color-border-light); padding-top:12px;">
                    <strong style="color:var(--color-text);">AI Notes:</strong><br>
                    <?= htmlspecialchars($completenessNote) ?>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- AI Summary -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">AI-Generated Summary</h2>
            </div>
            <div class="card-body">
                <p style="font-size:13px; line-height:1.7; color:var(--color-text); margin:0;">
                    <?= nl2br(htmlspecialchars($report['ai_summary'] ?? 'No summary available.')) ?>
                </p>
            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div>

        <!-- Similarity Check -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Similarity Detection</h2>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="ai-similarity-bar" style="width:100px; height:8px;">
                        <div class="ai-similarity-fill <?= $similarityScore > 60 ? 'high' : ($similarityScore > 30 ? 'medium' : '') ?>"
                             style="width:<?= min(100, $similarityScore) ?>%;">
                        </div>
                    </div>
                    <span style="font-size:18px; font-weight:700;
                                 color:<?= $similarityScore > 60 ? '#dc3545' : ($similarityScore > 30 ? '#fd7e14' : 'var(--color-success)') ?>;">
                        <?= number_format($similarityScore, 0) ?>%
                    </span>
                </div>
            </div>
            <div class="card-body">

                <div style="font-size:12px; color:var(--color-text-muted); margin-bottom:14px;">
                    <strong style="color:var(--color-text);">Score Guide:</strong>
                    0% = Completely unique &nbsp;|&nbsp;
                    100% = Identical document
                </div>

                <?php if (!empty($report['similar_document_no'])): ?>
                <div style="background:var(--color-bg); border:1px solid var(--color-border);
                            border-radius:var(--radius); padding:14px; margin-bottom:14px;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                                letter-spacing:0.5px; color:var(--color-text-muted); margin-bottom:8px;">
                        Most Similar Document
                    </div>
                    <a href="<?= APP_ROOT_URL ?>/<?= $report['similar_document_type'] ?>/view/<?= $report['similar_document_id'] ?>"
                       style="font-size:14px; font-weight:700; color:var(--color-primary);">
                        <?= htmlspecialchars($report['similar_document_no']) ?>
                    </a>
                    <div style="font-size:12px; color:var(--color-text-muted); margin-top:2px; text-transform:capitalize;">
                        <?= htmlspecialchars($report['similar_document_type'] ?? '') ?>
                    </div>
                </div>
                <?php else: ?>
                <div style="background:var(--color-success-bg); border:1px solid #badbcc;
                            border-radius:var(--radius); padding:14px; margin-bottom:14px;
                            font-size:13px; color:var(--color-success);">
                    No significantly similar documents found. This document appears to be unique.
                </div>
                <?php endif; ?>

                <?php if (!empty($simExplanation)): ?>
                <div style="font-size:13px; color:var(--color-text-muted);
                            border-top:1px solid var(--color-border-light); padding-top:12px;">
                    <strong style="color:var(--color-text);">AI Explanation:</strong><br>
                    <?= htmlspecialchars($simExplanation) ?>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Recommendation -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">AI Recommendation</h2>
            </div>
            <div class="card-body">
                <p style="font-size:13px; line-height:1.7; color:var(--color-text); margin:0;">
                    <?= nl2br(htmlspecialchars($report['recommendation'] ?? 'No recommendation available.')) ?>
                </p>
            </div>
        </div>

        <!-- Report Metadata -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Report Details</h2>
            </div>
            <div class="card-body">
                <div class="doc-meta-grid">
                    <span class="doc-meta-label">Report ID</span>
                    <span class="doc-meta-value">#<?= $report['id'] ?></span>

                    <span class="doc-meta-label">Document</span>
                    <span class="doc-meta-value">
                        <a href="<?= $docUrl ?>" style="color:var(--color-primary); font-weight:600;">
                            <?= htmlspecialchars($report['document_no'] ?? 'N/A') ?>
                        </a>
                    </span>

                    <span class="doc-meta-label">Type</span>
                    <span class="doc-meta-value" style="text-transform:capitalize;">
                        <?= htmlspecialchars($report['document_type']) ?>
                    </span>

                    <span class="doc-meta-label">Result</span>
                    <span class="doc-meta-value">
                        <span class="badge <?= $statusBadge ?>">
                            <?= ucfirst($report['validation_status']) ?>
                        </span>
                    </span>

                    <span class="doc-meta-label">AI Model</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= GROQ_MODEL ?>
                    </span>

                    <span class="doc-meta-label">Validated By</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($report['validated_by_name'] ?? 'System') ?>
                    </span>

                    <span class="doc-meta-label">Date</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= date('F d, Y h:i A', strtotime($report['created_at'])) ?>
                    </span>
                </div>
            </div>
        </div>

    </div>

</div>
