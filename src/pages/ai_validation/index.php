<?php
/**
 * ORLMS - AI Validation Reports List View
 *
 * Variables:
 *   $reports — all validation reports with document info
 *   $counts  — ['passed' => n, 'flagged' => n, 'failed' => n]
 */

$reports = $reports ?? [];
$counts  = $counts ?? [];

$userRole  = $_SESSION['user_role'] ?? '';
$canRun    = in_array($userRole, ['super_admin', 'legislative_staff']);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">AI Validation Reports</li>
            </ul>
            <h1 class="page-title">AI Validation Reports</h1>
            <p class="page-subtitle">
                Automated document completeness and similarity analysis powered by Groq AI
            </p>
        </div>
    </div>
</div>

<!-- Status Summary Cards -->
<div class="row row-3" style="margin-bottom:24px;">
    <div class="stat-card success">
        <div class="stat-label">Passed</div>
        <div class="stat-value"><?= $counts['passed'] ?></div>
        <div class="stat-meta">Complete and unique documents</div>
    </div>
    <div class="stat-card accent">
        <div class="stat-label">Flagged</div>
        <div class="stat-value"><?= $counts['flagged'] ?></div>
        <div class="stat-meta">Needs review before endorsement</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-label">Failed</div>
        <div class="stat-value"><?= $counts['failed'] ?></div>
        <div class="stat-meta">Incomplete or highly similar</div>
    </div>
</div>

<!-- Reports Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Validation Report History</h2>
        <span style="font-size:12px; color:var(--color-text-muted);">
            Most recent validations shown first
        </span>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="ai-reports-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:120px;">Document No.</th>
                    <th>Document Title</th>
                    <th style="width:90px;">Type</th>
                    <th style="width:90px;">Result</th>
                    <th style="width:100px;">Completeness</th>
                    <th style="width:100px;">Similarity</th>
                    <th style="width:130px;">Validated By</th>
                    <th style="width:110px;">Date</th>
                    <th style="width:70px;" class="col-actions">Report</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reports)): ?>
                    <?php $i = 1; foreach ($reports as $r): ?>
                    <tr>
                        <td class="text-muted"><?= $i++ ?></td>

                        <td>
                            <?php
                            $docUrl = APP_ROOT_URL . '/' . $r['document_type']
                                      . '/view/' . $r['document_id'];
                            ?>
                            <a href="<?= $docUrl ?>"
                               style="font-weight:600; color:var(--color-primary); font-size:13px;">
                                <?= htmlspecialchars($r['document_no'] ?? 'N/A') ?>
                            </a>
                        </td>

                        <td style="font-size:13px;">
                            <?= htmlspecialchars(
                                strlen($r['document_title'] ?? '') > 55
                                ? substr($r['document_title'], 0, 55) . '...'
                                : ($r['document_title'] ?? '—')
                            ) ?>
                        </td>

                        <td>
                            <span class="badge <?= $r['document_type'] === 'ordinance' ? 'badge-enacted' : 'badge-endorsed' ?>"
                                  style="font-size:10px;">
                                <?= ucfirst($r['document_type']) ?>
                            </span>
                        </td>

                        <td>
                            <?php
                            $statusClass = match($r['validation_status']) {
                                'passed'  => 'badge-ai-passed',
                                'flagged' => 'badge-ai-flagged',
                                'failed'  => 'badge-ai-failed',
                                default   => 'badge-ai-pending',
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>">
                                <?= ucfirst($r['validation_status']) ?>
                            </span>
                        </td>

                        <td>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div class="ai-similarity-bar" style="flex:1; height:6px;">
                                    <div class="ai-similarity-fill"
                                         style="width:<?= min(100, (int)$r['completeness_score']) ?>%;
                                                background-color:<?= (int)$r['completeness_score'] >= 80 ? 'var(--color-success)' : ((int)$r['completeness_score'] >= 60 ? '#fd7e14' : '#dc3545') ?>;">
                                    </div>
                                </div>
                                <span style="font-size:12px; font-weight:600; color:var(--color-text); width:32px;">
                                    <?= (int)$r['completeness_score'] ?>%
                                </span>
                            </div>
                        </td>

                        <td>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div class="ai-similarity-bar" style="flex:1; height:6px;">
                                    <?php $simScore = (float)$r['similarity_score']; ?>
                                    <div class="ai-similarity-fill <?= $simScore > 60 ? 'high' : ($simScore > 30 ? 'medium' : '') ?>"
                                         style="width:<?= min(100, $simScore) ?>%;">
                                    </div>
                                </div>
                                <span style="font-size:12px; font-weight:600; color:var(--color-text); width:32px;">
                                    <?= number_format($simScore, 0) ?>%
                                </span>
                            </div>
                        </td>

                        <td style="font-size:12px; color:var(--color-text-muted);">
                            <?= htmlspecialchars($r['validated_by_name'] ?? 'System') ?>
                        </td>

                        <td class="text-muted" style="font-size:12px;">
                            <?= date('M d, Y', strtotime($r['created_at'])) ?>
                        </td>

                        <td class="col-actions">
                            <a href="<?= APP_ROOT_URL ?>/ai_validation/report/<?= $r['id'] ?>"
                               class="btn btn-outline btn-sm">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="table-empty">
                            No AI validation reports yet.<br>
                            <?php if ($canRun): ?>
                            <span style="font-size:12px;">
                                Go to any
                                <a href="<?= APP_ROOT_URL ?>/ordinance" style="color:var(--color-accent);">ordinance</a>
                                or
                                <a href="<?= APP_ROOT_URL ?>/resolution" style="color:var(--color-accent);">resolution</a>
                                and click "Run AI Validation."
                            </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
