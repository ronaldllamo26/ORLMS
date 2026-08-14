<?php
/**
 * ORLMS - Amendments Index View
 *
 * Variables:
 *   $amendments  — array of amendment records
 *   $summary     — array of counts: ['draft'=>N, 'submitted'=>N, 'approved'=>N, 'rejected'=>N]
 *   $enactedDocs — array of enacted/published documents for quick creation
 */

$amendments  = $amendments ?? [];
$summary     = $summary ?? [];
$enactedDocs = $enactedDocs ?? [];

$userRole = $_SESSION['user_role'] ?? '';
$canCreate = in_array($userRole, ['super_admin', 'legislative_staff']);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Amendments & Revisions</li>
            </ul>
            <h1 class="page-title">Amendments & Revisions</h1>
            <p class="page-subtitle">
                Track modifications, updates, and amendments made to enacted laws and active resolutions
            </p>
        </div>
    </div>
</div>

<!-- Status Summary Cards -->
<div class="row row-4" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-label">Draft Amendments</div>
        <div class="stat-value" style="color:var(--color-text-muted);"><?= $summary['draft'] ?></div>
        <div class="stat-meta">Work in progress</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Submitted</div>
        <div class="stat-value" style="color:var(--color-accent);"><?= $summary['submitted'] ?></div>
        <div class="stat-meta">Pending approval</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Approved</div>
        <div class="stat-value" style="color:var(--color-success);"><?= $summary['approved'] ?></div>
        <div class="stat-meta">Officially applied</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rejected</div>
        <div class="stat-value" style="color:#dc3545;"><?= $summary['rejected'] ?></div>
        <div class="stat-meta">Not approved</div>
    </div>
</div>

<div class="row row-2-1" style="align-items:start; gap:20px;">

    <!-- Left: Amendments List -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Amendments History</h2>
                <span style="font-size:12px; color:var(--color-text-muted);">
                    <?= count($amendments) ?> record(s) found
                </span>
            </div>

            <?php if (empty($amendments)): ?>
            <div class="card-body" style="text-align:center; padding:48px 24px;">
                <div style="margin-bottom:16px; display:inline-flex; align-items:center; justify-content:center;
                            width:56px; height:56px; border-radius:50%; background-color:var(--color-bg);
                            color:var(--color-text-muted); border:1px solid var(--color-border-light);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div style="font-size:16px; font-weight:600; color:var(--color-text); margin-bottom:8px;">
                    No Amendments Registered
                </div>
                <div style="font-size:13px; color:var(--color-text-muted);">
                    Select an enacted document from the sidebar to create an amendment.
                </div>
            </div>
            <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table" id="amendments-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th style="width:155px;">Amendment No.</th>
                            <th>Target Document</th>
                            <th>Description</th>
                            <th style="width:100px;">Status</th>
                            <th style="width:115px;">Date Created</th>
                            <th style="width:70px;" class="col-actions">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($amendments as $am): ?>
                        <tr>
                            <td class="text-muted"><?= $i++ ?></td>
                            <td>
                                <a href="<?= APP_ROOT_URL ?>/amendments/view/<?= $am['id'] ?>"
                                   style="font-weight:600; color:var(--color-primary); font-size:13px;">
                                    <?= htmlspecialchars($am['amendment_no'] ?? 'AMEND-N/A') ?>
                                </a>
                            </td>
                            <td>
                                <div style="font-weight:600; font-size:13px; color:var(--color-text);">
                                    <?= htmlspecialchars($am['doc_no'] ?? 'N/A') ?>
                                </div>
                                <div style="font-size:11px; color:var(--color-text-muted);" title="<?= htmlspecialchars($am['doc_title'] ?? '') ?>">
                                    <?= htmlspecialchars(
                                        strlen($am['doc_title'] ?? '') > 40
                                        ? substr($am['doc_title'] ?? '', 0, 40) . '...'
                                        : ($am['doc_title'] ?? '—')
                                    ) ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-size:12px; color:var(--color-text);" title="<?= htmlspecialchars($am['description']) ?>">
                                    <?= htmlspecialchars(
                                        strlen($am['description']) > 55
                                        ? substr($am['description'], 0, 55) . '...'
                                        : $am['description']
                                    ) ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $badgeClass = match($am['status']) {
                                    'draft'     => 'badge-draft',
                                    'submitted' => 'badge-submitted',
                                    'approved'  => 'badge-approved',
                                    'rejected'  => 'badge-rejected',
                                    default     => 'badge-draft',
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>" style="font-size:11px;">
                                    <?= ucfirst($am['status']) ?>
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:12px;">
                                <?= date('M d, Y', strtotime($am['amended_at'])) ?>
                            </td>
                            <td class="col-actions">
                                <a href="<?= APP_ROOT_URL ?>/amendments/view/<?= $am['id'] ?>"
                                   class="btn btn-outline btn-sm">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Enacted Documents for Quick Action -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Amend Enacted Doc</h2>
            </div>
            <div class="card-body" style="padding:16px;">
                <?php if (empty($enactedDocs)): ?>
                <div style="text-align:center; padding:16px; font-size:13px; color:var(--color-text-muted);">
                    No enacted or published documents found.
                </div>
                <?php else: ?>
                <div style="font-size:12px; color:var(--color-text-muted); margin-bottom:12px;">
                    Select an active document below to begin drafting an amendment:
                </div>
                <div style="display:flex; flex-direction:column; gap:8px; max-height:350px; overflow-y:auto; padding-right:4px;">
                    <?php foreach ($enactedDocs as $doc): ?>
                    <div style="background:var(--color-bg); border:1px solid var(--color-border-light);
                                border-radius:var(--radius); padding:10px; display:flex; flex-direction:column; gap:6px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-weight:700; font-size:12px; color:var(--color-primary);">
                                <?= htmlspecialchars($doc['doc_no']) ?>
                            </span>
                            <span class="badge <?= $doc['doc_type'] === 'ordinance' ? 'badge-enacted' : 'badge-endorsed' ?>"
                                  style="font-size:9px; padding:2px 6px;">
                                <?= ucfirst($doc['doc_type']) ?>
                            </span>
                        </div>
                        <div style="font-size:11px; color:var(--color-text); line-height:1.4;" title="<?= htmlspecialchars($doc['title']) ?>">
                            <?= htmlspecialchars(
                                strlen($doc['title']) > 55
                                ? substr($doc['title'], 0, 55) . '...'
                                : $doc['title']
                            ) ?>
                        </div>
                        <?php if ($canCreate): ?>
                        <div style="text-align:right; margin-top:2px;">
                            <a href="<?= APP_ROOT_URL ?>/amendments/create/<?= $doc['doc_type'] ?>/<?= $doc['id'] ?>"
                               class="btn btn-primary btn-sm" style="font-size:10px; padding:3px 8px;">
                                + Amend
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
