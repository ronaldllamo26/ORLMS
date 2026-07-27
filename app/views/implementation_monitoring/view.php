<?php
/**
 * ORLMS - Implementation Monitoring View (per document)
 *
 * Variables:
 *   $document — the enacted/published document record
 *   $docType  — 'ordinance' or 'resolution'
 *   $logs     — all monitoring log entries for this document
 */

$noField = $docType === 'ordinance' ? 'ordinance_no' : 'resolution_no';
$docNo   = $document[$noField] ?? 'N/A';

// Latest status from most recent log
$latestLog    = !empty($logs) ? $logs[0] : null;
$latestStatus = $latestLog['implementation_status'] ?? 'pending';

$statusColors = [
    'pending'   => ['bg' => '#f8f9fa', 'text' => 'var(--color-text-muted)', 'border' => 'var(--color-border)'],
    'ongoing'   => ['bg' => '#fff8e1', 'text' => '#856404', 'border' => '#ffe69c'],
    'completed' => ['bg' => '#d1e7dd', 'text' => '#0f5132', 'border' => '#badbcc'],
    'delayed'   => ['bg' => '#f8d7da', 'text' => '#842029', 'border' => '#f5c2c7'],
];
$sc = $statusColors[$latestStatus] ?? $statusColors['pending'];
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
                    <a href="<?= APP_ROOT_URL ?>/implementation_monitoring">Monitoring</a>
                </li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($docNo) ?></li>
            </ul>
            <h1 class="page-title">Monitoring: <?= htmlspecialchars($docNo) ?></h1>
            <p class="page-subtitle"><?= htmlspecialchars($document['title']) ?></p>
        </div>
        <a href="<?= APP_ROOT_URL ?>/implementation_monitoring"
           class="btn btn-outline-secondary btn-sm">Back to Monitoring</a>
    </div>
</div>

<div class="row row-2-1" style="align-items:start;">

    <!-- LEFT: Log History + New Log Form -->
    <div>

        <!-- Add New Log Entry -->
        <div class="card" style="margin-bottom:20px;
             border-left:4px solid var(--color-accent);">
            <div class="card-header">
                <h2 class="card-title">Add Monitoring Update</h2>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="<?= APP_ROOT_URL ?>/implementation_monitoring/log/<?= $docType ?>/<?= $document['id'] ?>"
                      id="monitoring-log-form">

                    <div class="row row-2" style="gap:16px; margin-bottom:0;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label for="implementation_status" class="form-label">
                                Implementation Status <span class="form-required">*</span>
                            </label>
                            <select id="implementation_status"
                                    name="implementation_status"
                                    class="form-control form-select" required>
                                <option value="">— Select status —</option>
                                <option value="pending"
                                    <?= ($latestStatus === 'pending') ? 'selected' : '' ?>>
                                    Pending — Not yet started
                                </option>
                                <option value="ongoing"
                                    <?= ($latestStatus === 'ongoing') ? 'selected' : '' ?>>
                                    Ongoing — Currently implementing
                                </option>
                                <option value="completed"
                                    <?= ($latestStatus === 'completed') ? 'selected' : '' ?>>
                                    Completed — Fully implemented
                                </option>
                                <option value="delayed">
                                    Delayed — Behind schedule
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:14px;">
                        <label for="implementation_notes" class="form-label">
                            Notes / Observations <span class="form-required">*</span>
                        </label>
                        <textarea id="implementation_notes" name="implementation_notes"
                                  class="form-control" rows="4"
                                  placeholder="Describe the current state of implementation, actions taken, obstacles encountered, or next steps..."
                                  required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary"
                            id="btn-log-update">
                        Save Update
                    </button>

                </form>
            </div>
        </div>

        <!-- Log History -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Update History</h2>
                <span style="font-size:12px; color:var(--color-text-muted);">
                    <?= count($logs) ?> log entr<?= count($logs) === 1 ? 'y' : 'ies' ?>
                </span>
            </div>
            <?php if (empty($logs)): ?>
            <div class="card-body" style="text-align:center; padding:32px; color:var(--color-text-muted);">
                <div style="font-size:20px; margin-bottom:8px;">📋</div>
                No monitoring updates have been logged yet.
            </div>
            <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:110px;">Status</th>
                            <th>Notes</th>
                            <th style="width:150px;">Logged By</th>
                            <th style="width:140px;">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <?php
                        $sc2 = [
                            'pending'   => 'badge-draft',
                            'ongoing'   => 'badge-submitted',
                            'completed' => 'badge-approved',
                            'delayed'   => 'badge-rejected',
                        ];
                        ?>
                        <tr>
                            <td>
                                <span class="badge <?= $sc2[$log['implementation_status']] ?? 'badge-draft' ?>"
                                      style="font-size:11px;">
                                    <?= ucfirst($log['implementation_status']) ?>
                                </span>
                            </td>
                            <td style="font-size:13px; line-height:1.5;">
                                <?= htmlspecialchars($log['implementation_notes']) ?>
                            </td>
                            <td style="font-size:12px; color:var(--color-text-muted);">
                                <?= htmlspecialchars($log['logged_by_name'] ?? 'Unknown') ?>
                            </td>
                            <td class="text-muted" style="font-size:12px;">
                                <?= date('M d, Y', strtotime($log['logged_at'])) ?>
                                <div style="font-size:11px;">
                                    <?= date('h:i A', strtotime($log['logged_at'])) ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- RIGHT: Document Info + Status Banner -->
    <div>

        <!-- Current Implementation Status -->
        <div style="background-color:<?= $sc['bg'] ?>; border:1px solid <?= $sc['border'] ?>;
                    border-radius:var(--radius); padding:18px 20px; margin-bottom:20px;">
            <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                        letter-spacing:0.5px; color:<?= $sc['text'] ?>; margin-bottom:6px;">
                Current Implementation Status
            </div>
            <div style="font-size:22px; font-weight:700; color:<?= $sc['text'] ?>;
                        text-transform:uppercase; letter-spacing:0.5px;">
                <?= ucfirst($latestStatus) ?>
            </div>
            <?php if ($latestLog): ?>
            <div style="font-size:12px; color:<?= $sc['text'] ?>; opacity:0.8; margin-top:6px;">
                Last updated <?= date('M d, Y h:i A', strtotime($latestLog['logged_at'])) ?>
                by <?= htmlspecialchars($latestLog['logged_by_name'] ?? 'Unknown') ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Document Info -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Document Info</h2>
                <span class="badge badge-<?= $document['status'] ?? 'enacted' ?>"
                      style="font-size:11px; text-transform:uppercase;">
                    <?= htmlspecialchars($document['status'] ?? '') ?>
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
                    <span class="doc-meta-value" style="font-size:12px;">
                        <?= htmlspecialchars($document['title']) ?>
                    </span>

                    <span class="doc-meta-label">Subject</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= htmlspecialchars($document['subject']) ?>
                    </span>

                    <span class="doc-meta-label">Author</span>
                    <span class="doc-meta-value text-muted">
                        <?= htmlspecialchars($document['author_name'] ?? 'Unknown') ?>
                    </span>

                    <span class="doc-meta-label">Date Filed</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= $document['date_filed']
                            ? date('F d, Y', strtotime($document['date_filed']))
                            : '—' ?>
                    </span>

                    <span class="doc-meta-label">Total Logs</span>
                    <span class="doc-meta-value" style="font-weight:700;">
                        <?= count($logs) ?>
                    </span>
                </div>

                <div style="margin-top:16px; padding-top:16px;
                            border-top:1px solid var(--color-border-light);">
                    <a href="<?= APP_ROOT_URL ?>/<?= $docType ?>/view/<?= $document['id'] ?>"
                       class="btn btn-outline btn-sm" style="width:100%; text-align:center;">
                        View Full Document &rarr;
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
