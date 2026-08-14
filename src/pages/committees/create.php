<?php
/**
 * ORLMS - Create Committee Form
 *
 * Variables:
 *   $users  — list of potential chairpersons
 *   $errors — validation errors
 *   $input  — re-filled form input
 */

$users  = $users ?? [];
$errors = $errors ?? [];
$input  = $input ?? [];
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
                <li class="breadcrumb-item active">New Committee</li>
            </ul>
            <h1 class="page-title">Create Committee</h1>
            <p class="page-subtitle">Add a new legislative committee to the system</p>
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
                      action="<?= APP_ROOT_URL ?>/committee/create"
                      id="create-committee-form">

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">
                            Committee Name <span class="form-required">*</span>
                        </label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="<?= htmlspecialchars($input['name'] ?? '') ?>"
                               placeholder="e.g. Committee on Health and Sanitation" required>
                        <?php if (!empty($errors['name'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['name']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Jurisdiction -->
                    <div class="form-group">
                        <label for="jurisdiction" class="form-label">
                            Jurisdiction / Area of Responsibility <span class="form-required">*</span>
                        </label>
                        <textarea id="jurisdiction" name="jurisdiction" class="form-control" rows="5"
                                  placeholder="Describe the topics, departments, and public services within this committee's review scope..."
                                  required><?= htmlspecialchars($input['jurisdiction'] ?? '') ?></textarea>
                        <?php if (!empty($errors['jurisdiction'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['jurisdiction']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Chairperson Dropdown -->
                    <div class="form-group">
                        <label for="chairperson_id" class="form-label">
                            Designated Chairperson <span style="font-size:11px; color:var(--color-text-muted); font-weight:400;">(optional)</span>
                        </label>
                        <select id="chairperson_id" name="chairperson_id" class="form-control form-select">
                            <option value="">— Select chairperson —</option>
                            <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>"
                                <?= ($input['chairperson_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['name']) ?> (<?= ucfirst(str_replace('_', ' ', $u['role'])) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-hint">
                            Only active Committee Members, SP Members, and Admins can chair committees.
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="btn-create-committee">
                            Create Committee
                        </button>
                        <a href="<?= APP_ROOT_URL ?>/committee" class="btn btn-outline-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Right: Sidebar Guide -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Scope & Routing</h2>
            </div>
            <div class="card-body" style="font-size:12px; line-height:1.7; color:var(--color-text-muted);">
                <p style="margin-bottom:12px;">
                    <strong style="color:var(--color-primary);">Role in System:</strong>
                    Legislative committees are responsible for reviewing ordinances and resolutions during the **Review and Endorsement** stage.
                </p>
                <p>
                    When a legislative staff member submits a draft document, it can be referred to one of these active committees for review, and the designated chairperson is authorized to endorse, return, or reject the document.
                </p>
            </div>
        </div>
    </div>

</div>
