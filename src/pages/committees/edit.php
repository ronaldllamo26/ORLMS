<?php
/**
 * ORLMS - Edit Committee Form
 *
 * Variables:
 *   $committee — existing committee data
 *   $users     — list of potential chairpersons
 *   $errors    — validation errors
 *   $input     — re-filled form input
 */

$committee = $committee ?? [];
$users     = $users ?? [];
$errors    = $errors ?? [];
$input     = $input ?? $committee;
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
                    <a href="<?= APP_ROOT_URL ?>/committee">Committees</a>
                </li>
                <li class="breadcrumb-item active">Edit Committee</li>
            </ul>
            <h1 class="page-title">Edit Committee</h1>
            <p class="page-subtitle"><?= htmlspecialchars($committee['name'] ?? 'Committee') ?></p>
        </div>
    </div>
</div>

<div class="row row-2-1" style="align-items:start; gap:20px;">

    <!-- Left: Form -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Committee Information</h2>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="<?= APP_ROOT_URL ?>/committee/edit/<?= $committee['id'] ?>"
                      id="edit-committee-form">

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">
                            Committee Name <span class="form-required">*</span>
                        </label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="<?= htmlspecialchars($input['name'] ?? $committee['name']) ?>"
                               required>
                        <?php if (!empty($errors['name'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['name']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Jurisdiction -->
                    <div class="form-group">
                        <label for="jurisdiction" class="form-label">
                            Jurisdiction / Scope <span class="form-required">*</span>
                        </label>
                        <textarea id="jurisdiction" name="jurisdiction" class="form-control" rows="6"
                                  required><?= htmlspecialchars($input['jurisdiction'] ?? $committee['jurisdiction']) ?></textarea>
                        <?php if (!empty($errors['jurisdiction'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['jurisdiction']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Chairperson -->
                    <div class="form-group">
                        <label for="chairperson_id" class="form-label">
                            Designated Chairperson <span style="font-size:11px; color:var(--color-text-muted); font-weight:400;">(optional)</span>
                        </label>
                        <select id="chairperson_id" name="chairperson_id" class="form-control form-select">
                            <option value="">— Select chairperson —</option>
                            <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>"
                                <?= ($input['chairperson_id'] ?? $committee['chairperson_id']) == $u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['name']) ?> (<?= ucfirst(str_replace('_', ' ', $u['role'])) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="btn-save-committee-changes">
                            Save Changes
                        </button>
                        <a href="<?= APP_ROOT_URL ?>/committee" class="btn btn-outline-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Right: Committee Meta Details -->
    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><h2 class="card-title">Status</h2></div>
            <div class="card-body">
                <div class="doc-meta-grid">
                    <span class="doc-meta-label">Committee ID</span>
                    <span class="doc-meta-value" style="font-weight:700;">#<?= $committee['id'] ?></span>

                    <span class="doc-meta-label">Current Status</span>
                    <span class="doc-meta-value">
                        <?php if ($committee['is_active']): ?>
                        <span style="color:var(--color-success); font-weight:600;">● Active</span>
                        <?php else: ?>
                        <span style="color:#842029; font-weight:600;">● Suspended</span>
                        <?php endif; ?>
                    </span>

                    <span class="doc-meta-label">Created At</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= date('F d, Y', strtotime($committee['created_at'])) ?>
                    </span>
                </div>

                <div style="margin-top:16px; padding-top:16px; border-top:1px solid var(--color-border-light);">
                    <form method="POST"
                          action="<?= APP_ROOT_URL ?>/committee/toggle/<?= $committee['id'] ?>"
                          onsubmit="return confirm('Change committee status?')">
                        <button type="submit"
                                class="btn <?= $committee['is_active'] ? 'btn-danger' : 'btn-primary' ?>"
                                style="width:100%;">
                            <?= $committee['is_active'] ? 'Suspend Committee' : 'Activate Committee' ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
