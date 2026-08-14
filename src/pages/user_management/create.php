<?php
/**
 * ORLMS - Create User Form
 *
 * Variables:
 *   $errors — validation errors
 *   $input  — posted form values (for re-filling after error)
 */

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
                    <a href="<?= APP_ROOT_URL ?>/user_management">User Management</a>
                </li>
                <li class="breadcrumb-item active">New User</li>
            </ul>
            <h1 class="page-title">Create New User</h1>
            <p class="page-subtitle">
                Add a new system account. The user can log in immediately after creation.
            </p>
        </div>
    </div>
</div>

<div class="row row-2-1" style="align-items:start;">

    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Account Information</h2>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="<?= APP_ROOT_URL ?>/user_management/create"
                      id="create-user-form">

                    <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-error" style="margin-bottom:16px;">
                        <?= htmlspecialchars($errors['general']) ?>
                    </div>
                    <?php endif; ?>

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name <span class="form-required">*</span></label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="<?= htmlspecialchars($input['name'] ?? '') ?>"
                               placeholder="e.g. Juan dela Cruz" required>
                        <?php if (!empty($errors['name'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['name']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address <span class="form-required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($input['email'] ?? '') ?>"
                               placeholder="e.g. user@municipality.gov.ph" required>
                        <?php if (!empty($errors['email'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['email']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Role -->
                    <div class="form-group">
                        <label for="role" class="form-label">System Role <span class="form-required">*</span></label>
                        <select id="role" name="role" class="form-control form-select" required>
                            <option value="">— Select a role —</option>
                            <option value="legislative_staff"
                                <?= ($input['role'] ?? '') === 'legislative_staff' ? 'selected' : '' ?>>
                                Legislative Staff — Can create and submit documents
                            </option>
                            <option value="committee_member"
                                <?= ($input['role'] ?? '') === 'committee_member' ? 'selected' : '' ?>>
                                Committee Member — Can review and endorse documents
                            </option>
                            <option value="sp_member"
                                <?= ($input['role'] ?? '') === 'sp_member' ? 'selected' : '' ?>>
                                SP Member — Can review and endorse documents
                            </option>
                            <option value="super_admin"
                                <?= ($input['role'] ?? '') === 'super_admin' ? 'selected' : '' ?>>
                                Administrator — Full access
                            </option>
                        </select>
                        <?php if (!empty($errors['role'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['role']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password <span class="form-required">*</span></label>
                        <input type="password" id="password" name="password"
                               class="form-control" minlength="8"
                               placeholder="Minimum 8 characters" required autocomplete="new-password">
                        <?php if (!empty($errors['password'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['password']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirm" class="form-label">
                            Confirm Password <span class="form-required">*</span>
                        </label>
                        <input type="password" id="password_confirm" name="password_confirm"
                               class="form-control" minlength="8"
                               placeholder="Re-enter password" required autocomplete="new-password">
                        <?php if (!empty($errors['confirm'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['confirm']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="btn-create-user">
                            Create User
                        </button>
                        <a href="<?= APP_ROOT_URL ?>/user_management"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Role Guide -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Role Permissions Guide</h2>
            </div>
            <div class="card-body">
                <div style="font-size:12px; line-height:1.8; color:var(--color-text);">

                    <div style="margin-bottom:14px;">
                        <div style="font-weight:700; color:var(--color-primary); margin-bottom:4px;">
                            Legislative Staff
                        </div>
                        <ul style="margin:0; padding-left:16px; color:var(--color-text-muted);">
                            <li>Create, edit, delete DRAFT documents</li>
                            <li>Submit documents for review</li>
                            <li>View AI validation reports</li>
                        </ul>
                    </div>

                    <div style="margin-bottom:14px;">
                        <div style="font-weight:700; color:var(--color-primary); margin-bottom:4px;">
                            Committee Member / SP Member
                        </div>
                        <ul style="margin:0; padding-left:16px; color:var(--color-text-muted);">
                            <li>All Legislative Staff permissions</li>
                            <li>Review submitted documents</li>
                            <li>Endorse, return, or reject documents</li>
                        </ul>
                    </div>

                    <div style="margin-bottom:0;">
                        <div style="font-weight:700; color:var(--color-primary); margin-bottom:4px;">
                            Administrator
                        </div>
                        <ul style="margin:0; padding-left:16px; color:var(--color-text-muted);">
                            <li>Full system access</li>
                            <li>Approve and enact documents</li>
                            <li>Manage users and committees</li>
                            <li>View audit logs</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
