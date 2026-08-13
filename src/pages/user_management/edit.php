<?php
/**
 * ORLMS - Edit User Form
 *
 * Variables:
 *   $user   — existing user data (from DB)
 *   $errors — validation errors
 *   $input  — posted form values (for re-filling after error)
 */
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
                <li class="breadcrumb-item active">Edit User</li>
            </ul>
            <h1 class="page-title">Edit User</h1>
            <p class="page-subtitle"><?= htmlspecialchars($user['name']) ?></p>
        </div>
    </div>
</div>

<div class="row row-2-1" style="align-items:start;">

    <div>
        <!-- Profile Edit -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Profile Information</h2>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="<?= APP_ROOT_URL ?>/user_management/edit/<?= $user['id'] ?>"
                      id="edit-user-form">

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">
                            Full Name <span class="form-required">*</span>
                        </label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="<?= htmlspecialchars($input['name'] ?? $user['name']) ?>"
                               required>
                        <?php if (!empty($errors['name'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['name']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            Email Address <span class="form-required">*</span>
                        </label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($input['email'] ?? $user['email']) ?>"
                               required>
                        <?php if (!empty($errors['email'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['email']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Role -->
                    <div class="form-group">
                        <label for="role" class="form-label">
                            System Role <span class="form-required">*</span>
                        </label>
                        <select id="role" name="role" class="form-control form-select" required>
                            <option value="legislative_staff"
                                <?= ($input['role'] ?? $user['role']) === 'legislative_staff' ? 'selected' : '' ?>>
                                Legislative Staff
                            </option>
                            <option value="committee_member"
                                <?= ($input['role'] ?? $user['role']) === 'committee_member' ? 'selected' : '' ?>>
                                Committee Member
                            </option>
                            <option value="sp_member"
                                <?= ($input['role'] ?? $user['role']) === 'sp_member' ? 'selected' : '' ?>>
                                SP Member
                            </option>
                            <option value="super_admin"
                                <?= ($input['role'] ?? $user['role']) === 'super_admin' ? 'selected' : '' ?>>
                                Administrator
                            </option>
                        </select>
                        <?php if (!empty($errors['role'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['role']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Optional Password Change -->
                    <div style="border-top:1px solid var(--color-border-light);
                                margin:18px 0; padding-top:18px;">
                        <div style="font-size:13px; font-weight:600; color:var(--color-text);
                                    margin-bottom:4px;">
                            Change Password
                        </div>
                        <div style="font-size:12px; color:var(--color-text-muted); margin-bottom:14px;">
                            Leave blank to keep the current password.
                        </div>

                        <div class="form-group">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" id="new_password" name="new_password"
                                   class="form-control" minlength="8"
                                   placeholder="Minimum 8 characters"
                                   autocomplete="new-password">
                            <?php if (!empty($errors['new_password'])): ?>
                            <span class="form-error">
                                <?= htmlspecialchars($errors['new_password']) ?>
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirm" class="form-label">
                                Confirm New Password
                            </label>
                            <input type="password" id="new_password_confirm"
                                   name="new_password_confirm" class="form-control"
                                   minlength="8" placeholder="Re-enter new password"
                                   autocomplete="new-password">
                            <?php if (!empty($errors['new_password_confirm'])): ?>
                            <span class="form-error">
                                <?= htmlspecialchars($errors['new_password_confirm']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"
                                id="btn-save-changes">
                            Save Changes
                        </button>
                        <a href="<?= APP_ROOT_URL ?>/user_management"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Right: Account Info -->
    <div>
        <div class="card">
            <div class="card-header"><h2 class="card-title">Account Info</h2></div>
            <div class="card-body">
                <div class="doc-meta-grid">

                    <span class="doc-meta-label">User ID</span>
                    <span class="doc-meta-value" style="font-weight:700;">
                        #<?= $user['id'] ?>
                    </span>

                    <span class="doc-meta-label">Status</span>
                    <span class="doc-meta-value">
                        <?php if ($user['is_active']): ?>
                        <span style="color:var(--color-success); font-weight:600;
                                     font-size:13px;">
                            ● Active
                        </span>
                        <?php else: ?>
                        <span style="color:#842029; font-weight:600; font-size:13px;">
                            ● Inactive
                        </span>
                        <?php endif; ?>
                    </span>

                    <span class="doc-meta-label">Current Role</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= htmlspecialchars(str_replace('_', ' ', ucfirst($user['role']))) ?>
                    </span>

                    <span class="doc-meta-label">Registered</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= date('F d, Y', strtotime($user['created_at'])) ?>
                    </span>

                </div>

                <?php if ($user['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                <div style="margin-top:18px; padding-top:16px;
                            border-top:1px solid var(--color-border-light);">
                    <div style="font-size:12px; font-weight:600; color:var(--color-text);
                                margin-bottom:8px;">
                        Account Status
                    </div>
                    <form method="POST"
                          action="<?= APP_ROOT_URL ?>/user_management/toggle/<?= $user['id'] ?>"
                          onsubmit="return confirm('<?= $user['is_active']
                              ? 'Deactivate this user? They will not be able to log in.'
                              : 'Activate this user account?' ?>')">
                        <button type="submit"
                                class="btn <?= $user['is_active'] ? 'btn-danger' : 'btn-primary' ?>"
                                style="width:100%;">
                            <?= $user['is_active'] ? 'Deactivate Account' : 'Activate Account' ?>
                        </button>
                    </form>
                    <?php if ($user['is_active']): ?>
                    <div style="font-size:11px; color:var(--color-text-muted); margin-top:8px;
                                text-align:center; line-height:1.5;">
                        Deactivated users cannot log in but their records remain intact.
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</div>
