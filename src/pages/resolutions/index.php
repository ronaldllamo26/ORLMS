<?php
/**
 * ORLMS - Resolutions List View
 *
 * Variables passed from ResolutionController::index():
 *   $resolutions — array of all resolution records with author_name
 */

function resStatusBadge(string $status): string {
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

$userRole  = $_SESSION['user_role'] ?? '';
$canCreate = in_array($userRole, ['legislative_staff', 'super_admin']);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Resolutions</li>
            </ul>
            <h1 class="page-title">Resolutions</h1>
            <p class="page-subtitle">
                All resolutions on record — <?= count($resolutions) ?> document(s) total
            </p>
        </div>
        <?php if ($canCreate): ?>
        <div>
            <a href="<?= APP_ROOT_URL ?>/resolution/create"
               class="btn btn-primary"
               id="btn-new-resolution">
                New Resolution
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:14px 20px;">
        <form method="GET" action="" id="filter-form"
              style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:8px;">
                <label for="filter-status"
                       style="font-size:13px; font-weight:500; color:var(--color-text); white-space:nowrap;">
                    Filter by Status:
                </label>
                <select id="filter-status" name="status" class="form-control"
                        style="width:180px;" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="draft"        <?= ($_GET['status'] ?? '') === 'draft'         ? 'selected' : '' ?>>Draft</option>
                    <option value="submitted"    <?= ($_GET['status'] ?? '') === 'submitted'     ? 'selected' : '' ?>>Submitted</option>
                    <option value="under_review" <?= ($_GET['status'] ?? '') === 'under_review'  ? 'selected' : '' ?>>Under Review</option>
                    <option value="endorsed"     <?= ($_GET['status'] ?? '') === 'endorsed'      ? 'selected' : '' ?>>Endorsed</option>
                    <option value="approved"     <?= ($_GET['status'] ?? '') === 'approved'      ? 'selected' : '' ?>>Approved</option>
                    <option value="enacted"      <?= ($_GET['status'] ?? '') === 'enacted'       ? 'selected' : '' ?>>Enacted</option>
                    <option value="published"    <?= ($_GET['status'] ?? '') === 'published'     ? 'selected' : '' ?>>Published</option>
                    <option value="rejected"     <?= ($_GET['status'] ?? '') === 'rejected'      ? 'selected' : '' ?>>Rejected</option>
                    <option value="amended"      <?= ($_GET['status'] ?? '') === 'amended'       ? 'selected' : '' ?>>Amended</option>
                </select>
            </div>
            <?php if (!empty($_GET['status'])): ?>
            <a href="<?= APP_ROOT_URL ?>/resolution" class="btn btn-outline-secondary btn-sm">
                Clear Filter
            </a>
            <?php endif; ?>
            <span style="margin-left:auto; font-size:12px; color:var(--color-text-muted);">
                Showing <?= count($resolutions) ?> record(s)
            </span>
        </form>
    </div>
</div>

<!-- Resolutions Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Resolution Records</h2>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="resolutions-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:140px;">Resolution No.</th>
                    <th>Title</th>
                    <th style="width:160px;">Author</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:110px;">Date Filed</th>
                    <th style="width:130px;" class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resolutions)): ?>
                    <?php
                    $filterStatus = $_GET['status'] ?? '';
                    $filtered = $filterStatus
                        ? array_filter($resolutions, fn($r) => $r['status'] === $filterStatus)
                        : $resolutions;
                    ?>
                    <?php if (!empty($filtered)): ?>
                        <?php $rowNum = 1; ?>
                        <?php foreach ($filtered as $res): ?>
                        <tr>
                            <td class="text-muted"><?= $rowNum++ ?></td>
                            <td>
                                <a href="<?= APP_ROOT_URL ?>/resolution/view/<?= $res['id'] ?>"
                                   style="font-weight:600; color:var(--color-primary); font-size:13px;">
                                    <?= htmlspecialchars($res['resolution_no'] ?? 'No Number') ?>
                                </a>
                            </td>
                            <td>
                                <div style="font-weight:500; color:var(--color-text);"
                                     title="<?= htmlspecialchars($res['title']) ?>">
                                    <?= htmlspecialchars(strlen($res['title']) > 65 ? substr($res['title'], 0, 65) . '...' : $res['title']) ?>
                                </div>
                                <?php if (!empty($res['subject'])): ?>
                                <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">
                                    <?= htmlspecialchars(strlen($res['subject']) > 70 ? substr($res['subject'], 0, 70) . '...' : $res['subject']) ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:13px; color:var(--color-text-muted);">
                                <?= htmlspecialchars($res['author_name'] ?? 'Unknown') ?>
                            </td>
                            <td>
                                <span class="badge <?= resStatusBadge($res['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $res['status'])) ?>
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:13px;">
                                <?= $res['date_filed'] ? date('M d, Y', strtotime($res['date_filed'])) : '—' ?>
                            </td>
                            <td class="col-actions">
                                <a href="<?= APP_ROOT_URL ?>/resolution/view/<?= $res['id'] ?>"
                                   class="btn btn-outline btn-sm">View</a>
                                <?php if ($canCreate && $res['status'] === 'draft'): ?>
                                <a href="<?= APP_ROOT_URL ?>/resolution/edit/<?= $res['id'] ?>"
                                   class="btn btn-outline-secondary btn-sm">Edit</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="table-empty">No resolutions found with the selected status filter.</td></tr>
                    <?php endif; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="table-empty">
                            No resolutions on record.
                            <?php if ($canCreate): ?>
                            <a href="<?= APP_ROOT_URL ?>/resolution/create" style="color:var(--color-accent);">Create the first one.</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
