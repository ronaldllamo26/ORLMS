<?php
/**
 * ORLMS - Audit Logs List View
 *
 * Variables:
 *   $logs         — array of audit log entries
 *   $allActions   — distinct action values for filter dropdown
 *   $allTables    — distinct table_name values for filter dropdown
 *   $filterAction, $filterTable, $filterUser, $filterDate — active filters
 */

function auditActionBadge(string $action): string {
    return match(true) {
        str_contains($action, 'CREATE') => 'badge-endorsed',
        str_contains($action, 'DELETE') => 'badge-rejected',
        str_contains($action, 'REJECT') => 'badge-rejected',
        str_contains($action, 'ENACT')  => 'badge-enacted',
        str_contains($action, 'ENDORSE')|| str_contains($action, 'APPROVE') => 'badge-approved',
        str_contains($action, 'SUBMIT') => 'badge-submitted',
        default => 'badge-draft',
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
                <li class="breadcrumb-item active">Audit Logs</li>
            </ul>
            <h1 class="page-title">Audit Logs</h1>
            <p class="page-subtitle">
                Complete record of all system actions — <?= count($logs) ?>
                entr<?= count($logs) === 1 ? 'y' : 'ies' ?> shown
                <?= count($logs) >= 500 ? '(limited to 500 most recent)' : '' ?>
            </p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="<?= APP_ROOT_URL ?>/audit_logs" id="audit-filter-form">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr auto;
                        gap:12px; align-items:end;">

                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:11px;">Action</label>
                    <select name="action" class="form-control form-select"
                            style="font-size:12px;" id="filter-action">
                        <option value="">All Actions</option>
                        <?php foreach ($allActions as $a): ?>
                        <option value="<?= htmlspecialchars($a) ?>"
                            <?= $filterAction === $a ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:11px;">Module</label>
                    <select name="table" class="form-control form-select"
                            style="font-size:12px;" id="filter-table">
                        <option value="">All Modules</option>
                        <?php foreach ($allTables as $t): ?>
                        <option value="<?= htmlspecialchars($t) ?>"
                            <?= $filterTable === $t ? 'selected' : '' ?>>
                            <?= ucfirst(htmlspecialchars($t)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:11px;">User</label>
                    <input type="text" name="user" class="form-control"
                           value="<?= htmlspecialchars($filterUser) ?>"
                           placeholder="Search by name..."
                           style="font-size:12px;" id="filter-user">
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:11px;">Date</label>
                    <input type="date" name="date" class="form-control"
                           value="<?= htmlspecialchars($filterDate) ?>"
                           style="font-size:12px;" id="filter-date">
                </div>

                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary btn-sm"
                            id="btn-filter-apply">
                        Filter
                    </button>
                    <a href="<?= APP_ROOT_URL ?>/audit_logs"
                       class="btn btn-outline-secondary btn-sm"
                       id="btn-filter-clear">
                        Clear
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

<?php if (empty($logs)): ?>
<div class="card">
    <div class="card-body" style="text-align:center; padding:48px 24px;">
        <div style="margin-bottom:16px; display:inline-flex; align-items:center; justify-content:center;
                    width:56px; height:56px; border-radius:50%; background-color:var(--color-bg);
                    color:var(--color-text-muted); border:1px solid var(--color-border-light);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="8" y1="6" x2="21" y2="6"></line>
                <line x1="8" y1="12" x2="21" y2="12"></line>
                <line x1="8" y1="18" x2="21" y2="18"></line>
                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                <line x1="3" y1="18" x2="3.01" y2="18"></line>
            </svg>
        </div>
        <div style="font-size:16px; font-weight:600; color:var(--color-text); margin-bottom:8px;">
            No Audit Logs Found
        </div>
        <div style="font-size:13px; color:var(--color-text-muted);">
            No entries match your current filter. Try clearing the filters.
        </div>
    </div>
</div>

<?php else: ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">System Activity Log</h2>
        <span style="font-size:12px; color:var(--color-text-muted);">
            Most recent first
        </span>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="audit-logs-table">
            <thead>
                <tr>
                    <th style="width:55px;">ID</th>
                    <th style="width:110px;">Action</th>
                    <th style="width:110px;">Module</th>
                    <th style="width:70px;">Record</th>
                    <th>Changes</th>
                    <th style="width:160px;">User</th>
                    <th style="width:150px;">Timestamp</th>
                    <th style="width:60px;" class="col-actions">Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="text-muted" style="font-size:12px;">#<?= $log['id'] ?></td>

                    <td>
                        <span class="badge <?= auditActionBadge($log['action']) ?>"
                              style="font-size:10px; white-space:nowrap;">
                            <?= htmlspecialchars($log['action']) ?>
                        </span>
                    </td>

                    <td style="font-size:12px; color:var(--color-text-muted);">
                        <?= ucfirst(htmlspecialchars($log['table_name'])) ?>
                    </td>

                    <td class="text-muted" style="font-size:12px; text-align:center;">
                        <?= $log['record_id'] ?? '—' ?>
                    </td>

                    <td>
                        <?php
                        $old = $log['old_value'] ? json_decode($log['old_value'], true) : null;
                        $new = $log['new_value'] ? json_decode($log['new_value'], true) : null;

                        if (!empty($old) && !empty($new)):
                            // Show changed fields only
                            $changed = [];
                            foreach ($new as $k => $v) {
                                if (!isset($old[$k]) || $old[$k] !== $v) {
                                    $changed[$k] = ['from' => $old[$k] ?? null, 'to' => $v];
                                }
                            }
                            if (!empty($changed)):
                        ?>
                            <div style="font-size:11px; color:var(--color-text);">
                                <?php foreach (array_slice($changed, 0, 2) as $field => $change): ?>
                                <span style="color:var(--color-text-muted);">
                                    <?= htmlspecialchars($field) ?>:
                                </span>
                                <span style="color:#842029; text-decoration:line-through;">
                                    <?= htmlspecialchars(
                                        strlen((string)($change['from'] ?? '')) > 20
                                        ? substr((string)($change['from'] ?? ''), 0, 20) . '…'
                                        : (string)($change['from'] ?? '—')
                                    ) ?>
                                </span>
                                →
                                <span style="color:var(--color-success);">
                                    <?= htmlspecialchars(
                                        strlen((string)$change['to']) > 20
                                        ? substr((string)$change['to'], 0, 20) . '…'
                                        : (string)$change['to']
                                    ) ?>
                                </span>
                                <br>
                                <?php endforeach; ?>
                                <?php if (count($changed) > 2): ?>
                                <span style="color:var(--color-text-muted); font-style:italic;">
                                    +<?= count($changed) - 2 ?> more field(s)
                                </span>
                                <?php endif; ?>
                            </div>
                        <?php elseif (!empty($new)): ?>
                            <div style="font-size:11px; color:var(--color-text-muted);">
                                <?php foreach (array_slice($new, 0, 2) as $k => $v): ?>
                                    <span style="color:var(--color-text-muted);"><?= htmlspecialchars($k) ?>:</span>
                                    <?= htmlspecialchars(
                                        strlen((string)$v) > 25
                                        ? substr((string)$v, 0, 25) . '…'
                                        : (string)$v
                                    ) ?><br>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php elseif (!empty($new)): ?>
                            <div style="font-size:11px; color:var(--color-text-muted);">
                                <?php
                                $newData = is_array($new) ? $new : json_decode($log['new_value'], true);
                                if (is_array($newData)):
                                    foreach (array_slice($newData, 0, 2) as $k => $v):
                                ?>
                                    <span><?= htmlspecialchars($k) ?>: <?= htmlspecialchars(
                                        strlen((string)$v) > 25
                                        ? substr((string)$v, 0, 25) . '…'
                                        : (string)$v
                                    ) ?></span><br>
                                <?php endforeach; endif; ?>
                            </div>
                        <?php else: ?>
                            <span style="font-size:12px; color:var(--color-text-muted);">—</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <div style="font-size:13px; font-weight:500;">
                            <?= htmlspecialchars($log['user_name'] ?? 'System') ?>
                        </div>
                        <div style="font-size:11px; color:var(--color-text-muted);">
                            <?= htmlspecialchars(str_replace('_', ' ', ucfirst($log['user_role'] ?? ''))) ?>
                        </div>
                    </td>

                    <td class="text-muted" style="font-size:12px;">
                        <?= date('M d, Y', strtotime($log['created_at'])) ?>
                        <div style="font-size:11px;">
                            <?= date('h:i:s A', strtotime($log['created_at'])) ?>
                        </div>
                    </td>

                    <td class="col-actions">
                        <a href="<?= APP_ROOT_URL ?>/audit_logs/view/<?= $log['id'] ?>"
                           class="btn btn-outline btn-sm">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>
