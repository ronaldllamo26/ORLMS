<?php
/**
 * ORLMS - User Management List View
 *
 * Variables:
 *   $users — all users from the DB
 */

$roleLabels = [
    'super_admin'       => 'Administrator',
    'legislative_staff' => 'Legislative Staff',
    'committee_member'  => 'Committee Member',
    'sp_member'         => 'SP Member',
];
$roleColors = [
    'super_admin'       => 'badge-enacted',
    'legislative_staff' => 'badge-submitted',
    'committee_member'  => 'badge-endorsed',
    'sp_member'         => 'badge-approved',
];
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">User Management</li>
            </ul>
            <h1 class="page-title">User Management</h1>
            <p class="page-subtitle">
                Manage system accounts and access levels — <?= count($users) ?> user(s) registered
            </p>
        </div>
        <div>
            <a href="<?= APP_ROOT_URL ?>/user_management/create"
               class="btn btn-primary" id="btn-new-user">
                + New User
            </a>
        </div>
    </div>
</div>

<!-- Role Summary -->
<div class="row row-4" style="margin-bottom:24px;">
    <?php
    $roleSummary = [
        'super_admin'       => ['label' => 'Administrators', 'count' => 0],
        'legislative_staff' => ['label' => 'Legislative Staff', 'count' => 0],
        'committee_member'  => ['label' => 'Committee Members', 'count' => 0],
        'sp_member'         => ['label' => 'SP Members', 'count' => 0],
    ];
    foreach ($users as $u) {
        if (isset($roleSummary[$u['role']])) {
            $roleSummary[$u['role']]['count']++;
        }
    }
    foreach ($roleSummary as $info):
    ?>
    <div class="stat-card">
        <div class="stat-label"><?= $info['label'] ?></div>
        <div class="stat-value"><?= $info['count'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">System Users</h2>
        <span style="font-size:12px; color:var(--color-text-muted);">
            Users are never deleted — only deactivated
        </span>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="users-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Name</th>
                    <th style="width:220px;">Email</th>
                    <th style="width:170px;">Role</th>
                    <th style="width:80px;">Status</th>
                    <th style="width:110px;">Registered</th>
                    <th style="width:140px;" class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($users as $user): ?>
                <tr style="<?= !$user['is_active'] ? 'opacity:0.55;' : '' ?>">
                    <td class="text-muted"><?= $i++ ?></td>

                    <td>
                        <div style="font-weight:600; font-size:14px; color:var(--color-text);">
                            <?= htmlspecialchars($user['name']) ?>
                        </div>
                        <?php if (!$user['is_active']): ?>
                        <div style="font-size:11px; color:#842029; font-style:italic;">
                            Account deactivated
                        </div>
                        <?php endif; ?>
                    </td>

                    <td style="font-size:13px; color:var(--color-text-muted);">
                        <?= htmlspecialchars($user['email']) ?>
                    </td>

                    <td>
                        <span class="badge <?= $roleColors[$user['role']] ?? 'badge-draft' ?>"
                              style="font-size:11px;">
                            <?= $roleLabels[$user['role']] ?? ucfirst($user['role']) ?>
                        </span>
                    </td>

                    <td>
                        <?php if ($user['is_active']): ?>
                        <span style="display:inline-flex; align-items:center; gap:4px;
                                     font-size:12px; color:var(--color-success); font-weight:600;">
                            <span style="width:7px; height:7px; border-radius:50%;
                                         background:var(--color-success); display:inline-block;"></span>
                            Active
                        </span>
                        <?php else: ?>
                        <span style="display:inline-flex; align-items:center; gap:4px;
                                     font-size:12px; color:#842029; font-weight:600;">
                            <span style="width:7px; height:7px; border-radius:50%;
                                         background:#842029; display:inline-block;"></span>
                            Inactive
                        </span>
                        <?php endif; ?>
                    </td>

                    <td class="text-muted" style="font-size:12px;">
                        <?= date('M d, Y', strtotime($user['created_at'])) ?>
                    </td>

                    <td class="col-actions">
                        <div style="display:flex; gap:6px;">
                            <a href="<?= APP_ROOT_URL ?>/user_management/edit/<?= $user['id'] ?>"
                               class="btn btn-outline btn-sm">Edit</a>

                            <?php if ($user['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                            <form method="POST"
                                  action="<?= APP_ROOT_URL ?>/user_management/toggle/<?= $user['id'] ?>"
                                  style="display:inline;"
                                  onsubmit="return confirm('<?= $user['is_active']
                                      ? 'Deactivate ' . htmlspecialchars($user['name']) . '? They will no longer be able to log in.'
                                      : 'Activate ' . htmlspecialchars($user['name']) . '?' ?>')">
                                <button type="submit"
                                        class="btn <?= $user['is_active'] ? 'btn-outline-secondary' : 'btn-outline' ?> btn-sm"
                                        style="font-size:11px;">
                                    <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
