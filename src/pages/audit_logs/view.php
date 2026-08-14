<?php
/**
 * ORLMS - Audit Log Detail View
 *
 * Variables:
 *   $log      — the audit log entry
 *   $oldValue — decoded old_value (array or string)
 *   $newValue — decoded new_value (array or string)
 */

$log      = $log ?? [];
$oldValue = $oldValue ?? null;
$newValue = $newValue ?? null;
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
                    <a href="<?= APP_ROOT_URL ?>/audit_logs">Audit Logs</a>
                </li>
                <li class="breadcrumb-item active">Entry #<?= $log['id'] ?></li>
            </ul>
            <h1 class="page-title">Audit Log — Entry #<?= $log['id'] ?></h1>
            <p class="page-subtitle">
                <?= htmlspecialchars($log['action']) ?>
                on <?= ucfirst(htmlspecialchars($log['table_name'])) ?>
                at <?= date('F d, Y h:i:s A', strtotime($log['created_at'])) ?>
            </p>
        </div>
        <a href="<?= APP_ROOT_URL ?>/audit_logs" class="btn btn-outline-secondary btn-sm">
            Back to Logs
        </a>
    </div>
</div>

<div class="row row-2-1" style="align-items:start;">

    <!-- LEFT: Changes -->
    <div>

        <!-- Old vs New Values -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Data Changes</h2>
                <span style="font-size:11px; color:var(--color-text-muted);">
                    Before and after comparison
                </span>
            </div>
            <div class="card-body">

                <?php if (empty($oldValue) && empty($newValue)): ?>
                    <div style="text-align:center; padding:24px;
                                font-size:13px; color:var(--color-text-muted);">
                        No data changes recorded for this action.
                    </div>

                <?php elseif (is_array($newValue) && is_array($oldValue)): ?>
                    <!-- Show side-by-side diff -->
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                                        letter-spacing:0.5px; color:#842029;
                                        margin-bottom:10px; padding-bottom:6px;
                                        border-bottom:2px solid #f5c2c7;">
                                Before
                            </div>
                            <?php if (empty($oldValue)): ?>
                                <div style="font-size:12px; color:var(--color-text-muted);
                                            font-style:italic;">
                                    (New record — no previous data)
                                </div>
                            <?php else: ?>
                                <?php foreach ($oldValue as $key => $val): ?>
                                <div style="margin-bottom:8px; padding-bottom:8px;
                                            border-bottom:1px solid var(--color-border-light);">
                                    <div style="font-size:10px; font-weight:700;
                                                color:var(--color-text-muted);
                                                text-transform:uppercase; letter-spacing:0.4px;
                                                margin-bottom:2px;">
                                        <?= htmlspecialchars($key) ?>
                                    </div>
                                    <div style="font-size:12px; color:#842029;
                                                word-break:break-word; font-family:monospace;">
                                        <?= htmlspecialchars((string) $val) ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div>
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                                        letter-spacing:0.5px; color:var(--color-success);
                                        margin-bottom:10px; padding-bottom:6px;
                                        border-bottom:2px solid #c3e6cb;">
                                After
                            </div>
                            <?php if (empty($newValue)): ?>
                                <div style="font-size:12px; color:var(--color-text-muted);
                                            font-style:italic;">
                                    (Record deleted)
                                </div>
                            <?php else: ?>
                                <?php foreach ($newValue as $key => $val): ?>
                                <?php $changed = !isset($oldValue[$key]) || $oldValue[$key] !== $val; ?>
                                <div style="margin-bottom:8px; padding-bottom:8px;
                                            border-bottom:1px solid var(--color-border-light);
                                            <?= $changed ? 'background-color:#f0fff4; padding:6px 8px; border-radius:4px; border:1px solid #c3e6cb;' : '' ?>">
                                    <div style="font-size:10px; font-weight:700;
                                                color:var(--color-text-muted);
                                                text-transform:uppercase; letter-spacing:0.4px;
                                                margin-bottom:2px;">
                                        <?= htmlspecialchars($key) ?>
                                        <?php if ($changed): ?>
                                        <span style="color:var(--color-success); font-size:9px;
                                                     margin-left:4px;">CHANGED</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size:12px;
                                                color:<?= $changed ? 'var(--color-success)' : 'var(--color-text)' ?>;
                                                word-break:break-word; font-family:monospace;">
                                        <?= htmlspecialchars((string) $val) ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif (is_array($newValue)): ?>
                    <!-- Only new value (e.g. CREATE) -->
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                                letter-spacing:0.5px; color:var(--color-success);
                                margin-bottom:10px;">
                        Created Record
                    </div>
                    <?php foreach ($newValue as $key => $val): ?>
                    <div style="margin-bottom:8px; padding-bottom:8px;
                                border-bottom:1px solid var(--color-border-light);">
                        <div style="font-size:10px; font-weight:700;
                                    color:var(--color-text-muted); text-transform:uppercase;
                                    letter-spacing:0.4px; margin-bottom:2px;">
                            <?= htmlspecialchars($key) ?>
                        </div>
                        <div style="font-size:12px; color:var(--color-text);
                                    word-break:break-word; font-family:monospace;">
                            <?= htmlspecialchars((string) $val) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <div style="font-size:12px; color:var(--color-text-muted); font-style:italic;">
                        Raw value: <?= htmlspecialchars((string)($newValue ?? $oldValue ?? '—')) ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <!-- RIGHT: Entry Details -->
    <div>
        <div class="card">
            <div class="card-header"><h2 class="card-title">Entry Details</h2></div>
            <div class="card-body">
                <div class="doc-meta-grid">

                    <span class="doc-meta-label">Log ID</span>
                    <span class="doc-meta-value" style="font-weight:700;">
                        #<?= $log['id'] ?>
                    </span>

                    <span class="doc-meta-label">Action</span>
                    <span class="doc-meta-value">
                        <span class="badge badge-submitted" style="font-size:11px;">
                            <?= htmlspecialchars($log['action']) ?>
                        </span>
                    </span>

                    <span class="doc-meta-label">Module</span>
                    <span class="doc-meta-value">
                        <?= ucfirst(htmlspecialchars($log['table_name'])) ?>
                    </span>

                    <span class="doc-meta-label">Record ID</span>
                    <span class="doc-meta-value text-muted">
                        <?= $log['record_id'] ?? '—' ?>
                    </span>

                    <span class="doc-meta-label">Performed By</span>
                    <span class="doc-meta-value" style="font-weight:600;">
                        <?= htmlspecialchars($log['user_name'] ?? 'System') ?>
                    </span>

                    <span class="doc-meta-label">Role</span>
                    <span class="doc-meta-value text-muted">
                        <?= htmlspecialchars(str_replace('_', ' ',
                            ucfirst($log['user_role'] ?? 'Unknown'))) ?>
                    </span>

                    <?php if (!empty($log['ip_address'])): ?>
                    <span class="doc-meta-label">IP Address</span>
                    <span class="doc-meta-value" style="font-family:monospace; font-size:12px;">
                        <?= htmlspecialchars($log['ip_address']) ?>
                    </span>
                    <span class="doc-meta-label">Location</span>
                    <span class="doc-meta-value">
                        📍 <?= htmlspecialchars($log['location'] ?? (class_exists('GeoIPHelper') ? GeoIPHelper::getLocation($log['ip_address']) : 'Local Network')) ?>
                    </span>
                    <?php endif; ?>

                    <span class="doc-meta-label">Timestamp</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= date('F d, Y', strtotime($log['created_at'])) ?>
                        <br>
                        <?= date('h:i:s A', strtotime($log['created_at'])) ?>
                    </span>

                </div>
            </div>
        </div>

    </div>

</div>
