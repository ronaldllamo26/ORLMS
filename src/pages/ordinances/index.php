<?php
/**
 * ORLMS - Ordinances List View
 *
 * Variables passed from OrdinanceController::index():
 *   $ordinances — array of all ordinance records with author_name
 */

// Status badge helper
function ordStatusBadge(string $status): string {
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

$userRole = $_SESSION['user_role'] ?? '';
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
                <li class="breadcrumb-item active">Ordinances</li>
            </ul>
            <h1 class="page-title">Ordinances</h1>
            <p class="page-subtitle">
                All ordinances on record — <?= count($ordinances) ?> document(s) total
            </p>
        </div>
        <?php if ($canCreate): ?>
        <div>
            <a href="<?= APP_ROOT_URL ?>/ordinance/create"
               class="btn btn-primary"
               id="btn-new-ordinance">
                New Ordinance
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 14px 20px;">
        <form method="GET" action="" id="filter-form"
              style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:8px;">
                <label for="filter-status"
                       style="font-size:13px; font-weight:500; color:var(--color-text); white-space:nowrap;">
                    Filter by Status:
                </label>
                <select id="filter-status"
                        name="status"
                        class="form-control"
                        style="width:180px;"
                        onchange="this.form.submit()">
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
            <a href="<?= APP_ROOT_URL ?>/ordinance"
               class="btn btn-outline-secondary btn-sm">
                Clear Filter
            </a>
            <?php endif; ?>

            <span style="margin-left:auto; font-size:12px; color:var(--color-text-muted);">
                Showing <?= count($ordinances) ?> record(s)
            </span>
        </form>
    </div>
</div>

<!-- Ordinances Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Ordinance Records</h2>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="ordinances-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:140px;">Ordinance No.</th>
                    <th>Title</th>
                    <th style="width:160px;">Author</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:110px;">Date Filed</th>
                    <th style="width:130px;" class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ordinances)): ?>
                    <?php
                    // Apply client-side status filter if set
                    $filterStatus = $_GET['status'] ?? '';
                    $filtered = $filterStatus
                        ? array_filter($ordinances, fn($o) => $o['status'] === $filterStatus)
                        : $ordinances;
                    ?>
                    <?php if (!empty($filtered)): ?>
                        <?php $rowNum = 1; ?>
                        <?php foreach ($filtered as $ord): ?>
                        <tr>
                            <!-- Row Number -->
                            <td class="text-muted"><?= $rowNum++ ?></td>

                            <!-- Ordinance Number -->
                            <td>
                                <a href="<?= APP_ROOT_URL ?>/ordinance/view/<?= $ord['id'] ?>"
                                   style="font-weight:600; color:var(--color-primary); font-size:13px;">
                                    <?= htmlspecialchars($ord['ordinance_no'] ?? 'No Number') ?>
                                </a>
                            </td>

                            <!-- Title -->
                            <td>
                                <div style="font-weight:500; color:var(--color-text);"
                                     title="<?= htmlspecialchars($ord['title']) ?>">
                                    <?= htmlspecialchars(
                                        strlen($ord['title']) > 65
                                        ? substr($ord['title'], 0, 65) . '...'
                                        : $ord['title']
                                    ) ?>
                                </div>
                                <?php if (!empty($ord['subject'])): ?>
                                <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">
                                    <?= htmlspecialchars(
                                        strlen($ord['subject']) > 70
                                        ? substr($ord['subject'], 0, 70) . '...'
                                        : $ord['subject']
                                    ) ?>
                                </div>
                                <?php endif; ?>
                            </td>

                            <!-- Author -->
                            <td style="font-size:13px; color:var(--color-text-muted);">
                                <?= htmlspecialchars($ord['author_name'] ?? 'Unknown') ?>
                            </td>

                            <!-- Status Badge -->
                            <td>
                                <span class="badge <?= ordStatusBadge($ord['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $ord['status'])) ?>
                                </span>
                            </td>

                            <!-- Date Filed -->
                            <td class="text-muted" style="font-size:13px;">
                                <?= $ord['date_filed']
                                    ? date('M d, Y', strtotime($ord['date_filed']))
                                    : '—' ?>
                            </td>

                            <!-- Actions -->
                            <td class="col-actions">
                                <a href="<?= APP_ROOT_URL ?>/ordinance/view/<?= $ord['id'] ?>"
                                   class="btn btn-outline btn-sm">
                                    View
                                </a>

                                <?php if ($canCreate && $ord['status'] === 'draft'): ?>
                                <a href="<?= APP_ROOT_URL ?>/ordinance/edit/<?= $ord['id'] ?>"
                                   class="btn btn-outline-secondary btn-sm">
                                    Edit
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="table-empty">
                                No ordinances found with the selected status filter.
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="table-empty">
                            No ordinances on record.
                            <?php if ($canCreate): ?>
                            <a href="<?= APP_ROOT_URL ?>/ordinance/create"
                               style="color:var(--color-accent);">
                                Create the first one.
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
