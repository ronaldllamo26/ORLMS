<?php
/**
 * ORLMS - Committees List View
 *
 * Variables:
 *   $committees — array of committee records
 */

$committees = $committees ?? [];
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Committees</li>
            </ul>
            <h1 class="page-title">Committees</h1>
            <p class="page-subtitle">
                Manage legislative committees, jurisdictions, and designated chairpersons
            </p>
        </div>
        <div>
            <a href="<?= APP_ROOT_URL ?>/committee/create"
               class="btn btn-primary" id="btn-new-committee">
                + New Committee
            </a>
        </div>
    </div>
</div>

<!-- Committees Summary Cards -->
<div class="row row-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-label">Total Committees</div>
        <div class="stat-value"><?= count($committees) ?></div>
        <div class="stat-meta">Defined in database</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Active Committees</div>
        <div class="stat-value" style="color:var(--color-success);">
            <?php
            $activeCount = 0;
            foreach ($committees as $c) {
                if ($c['is_active']) $activeCount++;
            }
            echo $activeCount;
            ?>
        </div>
        <div class="stat-meta">Operating committees</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Inactive</div>
        <div class="stat-value" style="color:var(--color-text-muted);">
            <?= count($committees) - $activeCount ?>
        </div>
        <div class="stat-meta">Currently suspended</div>
    </div>
</div>

<!-- Committees Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Legislative Committees</h2>
        <span style="font-size:12px; color:var(--color-text-muted);">
            Active committees review documents during the review process
        </span>
    </div>

    <?php if (empty($committees)): ?>
    <div class="card-body" style="text-align:center; padding:48px 24px;">
        <div style="margin-bottom:16px; display:inline-flex; align-items:center; justify-content:center;
                    width:56px; height:56px; border-radius:50%; background-color:var(--color-bg);
                    color:var(--color-text-muted); border:1px solid var(--color-border-light);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
        <div style="font-size:16px; font-weight:600; color:var(--color-text); margin-bottom:8px;">
            No Committees Defined
        </div>
        <div style="font-size:13px; color:var(--color-text-muted);">
            Click "+ New Committee" at the top right to register a new committee.
        </div>
    </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table class="data-table" id="committees-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:230px;">Committee Name</th>
                    <th>Jurisdiction</th>
                    <th style="width:180px;">Chairperson</th>
                    <th style="width:90px;">Status</th>
                    <th style="width:140px;" class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($committees as $c): ?>
                <tr style="<?= !$c['is_active'] ? 'opacity:0.55;' : '' ?>">
                    <td class="text-muted"><?= $i++ ?></td>
                    <td>
                        <div style="font-weight:600; font-size:14px; color:var(--color-text);">
                            <?= htmlspecialchars($c['name']) ?>
                        </div>
                    </td>
                    <td style="font-size:12px; line-height:1.5; color:var(--color-text-muted);">
                        <?= htmlspecialchars(
                            strlen($c['jurisdiction'] ?? '') > 90
                            ? substr($c['jurisdiction'], 0, 90) . '...'
                            : ($c['jurisdiction'] ?? '—')
                        ) ?>
                    </td>
                    <td>
                        <?php if (!empty($c['chairperson_name'])): ?>
                        <div style="font-weight:500; font-size:13px; color:var(--color-text);">
                            <?= htmlspecialchars($c['chairperson_name']) ?>
                        </div>
                        <?php else: ?>
                        <span style="font-size:12px; color:var(--color-text-muted); font-style:italic;">
                            No chairperson assigned
                        </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($c['is_active']): ?>
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
                            Suspended
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="col-actions">
                        <div style="display:flex; gap:6px;">
                            <a href="<?= APP_ROOT_URL ?>/committee/edit/<?= $c['id'] ?>"
                               class="btn btn-outline btn-sm">Edit</a>

                            <?php $confirmMsg = ($c['is_active'] ? 'Suspend ' : 'Activate ') . $c['name'] . '?'; ?>
                            <form method="POST"
                                  action="<?= APP_ROOT_URL ?>/committee/toggle/<?= $c['id'] ?>"
                                  style="display:inline;"
                                  onsubmit="return confirm(<?= htmlspecialchars(json_encode($confirmMsg), ENT_QUOTES) ?>)">
                                <button type="submit"
                                        class="btn <?= $c['is_active'] ? 'btn-outline-secondary' : 'btn-outline' ?> btn-sm"
                                        style="font-size:11px;">
                                    <?= $c['is_active'] ? 'Suspend' : 'Activate' ?>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>