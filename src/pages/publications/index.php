<?php
/**
 * ORLMS - Publications Index View
 *
 * Variables:
 *   $published          — array of publication records (already published)
 *   $pendingPublication — enacted documents not yet published
 */

$published          = $published ?? [];
$pendingPublication = $pendingPublication ?? [];

$userRole = $_SESSION['user_role'] ?? '';
$canPublish = $userRole === 'super_admin';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-center justify-between">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/dashboard">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Publications</li>
            </ul>
            <h1 class="page-title">Publications</h1>
            <p class="page-subtitle">
                Official publication record of enacted ordinances and resolutions
            </p>
        </div>
    </div>
</div>

<!-- Pending Publication Queue -->
<?php if (!empty($pendingPublication) && $canPublish): ?>
<div class="card" style="margin-bottom:24px; border-left:4px solid var(--color-accent);">
    <div class="card-header">
        <h2 class="card-title">Awaiting Publication</h2>
        <span style="font-size:12px; color:var(--color-accent); font-weight:600;">
            <?= count($pendingPublication) ?> enacted — ready to publish
        </span>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:90px;">Type</th>
                    <th style="width:140px;">Document No.</th>
                    <th>Title</th>
                    <th style="width:160px;">Author</th>
                    <th style="width:110px;">Date Filed</th>
                    <th style="width:100px;" class="col-actions">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingPublication as $doc): ?>
                <tr>
                    <td>
                        <span class="badge <?= $doc['doc_type'] === 'ordinance' ? 'badge-enacted' : 'badge-endorsed' ?>"
                              style="font-size:10px;">
                            <?= ucfirst($doc['doc_type']) ?>
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:600; color:var(--color-primary); font-size:13px;">
                            <?= htmlspecialchars($doc['doc_no'] ?? 'No Number') ?>
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:500;">
                            <?= htmlspecialchars(
                                strlen($doc['title']) > 65
                                ? substr($doc['title'], 0, 65) . '...'
                                : $doc['title']
                            ) ?>
                        </div>
                    </td>
                    <td class="text-muted" style="font-size:13px;">
                        <?= htmlspecialchars($doc['author_name'] ?? '—') ?>
                    </td>
                    <td class="text-muted" style="font-size:13px;">
                        <?= $doc['date_filed'] ? date('M d, Y', strtotime($doc['date_filed'])) : '—' ?>
                    </td>
                    <td class="col-actions">
                        <a href="<?= APP_ROOT_URL ?>/publications/publish/<?= $doc['doc_type'] ?>/<?= $doc['id'] ?>"
                           class="btn btn-primary btn-sm">
                            Publish
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Published Documents -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Published Documents</h2>
        <span style="font-size:12px; color:var(--color-text-muted);">
            <?= count($published) ?> publication(s) on record
        </span>
    </div>

    <?php if (empty($published)): ?>
    <div class="card-body" style="text-align:center; padding:48px 24px;">
        <div style="margin-bottom:16px; display:inline-flex; align-items:center; justify-content:center;
                    width:56px; height:56px; border-radius:50%; background-color:var(--color-bg);
                    color:var(--color-text-muted); border:1px solid var(--color-border-light);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"></path>
                <path d="M18 14h-8M15 18h-5M10 6h8v4h-8V6Z"></path>
            </svg>
        </div>
        <div style="font-size:16px; font-weight:600; color:var(--color-text); margin-bottom:8px;">
            No Publications Yet
        </div>
        <div style="font-size:13px; color:var(--color-text-muted);">
            Enacted documents will appear here once published.
        </div>
    </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table class="data-table" id="publications-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:90px;">Type</th>
                    <th style="width:140px;">Document No.</th>
                    <th>Title</th>
                    <th style="width:220px;">Publication Reference</th>
                    <th style="width:150px;">Published By</th>
                    <th style="width:110px;">Date Published</th>
                    <th style="width:70px;" class="col-actions">View</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($published as $pub): ?>
                <tr>
                    <td class="text-muted"><?= $i++ ?></td>
                    <td>
                        <span class="badge <?= $pub['document_type'] === 'ordinance' ? 'badge-enacted' : 'badge-endorsed' ?>"
                              style="font-size:10px;">
                            <?= ucfirst($pub['document_type']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= APP_ROOT_URL ?>/publications/view/<?= $pub['id'] ?>"
                           style="font-weight:600; color:var(--color-primary); font-size:13px;">
                            <?= htmlspecialchars($pub['doc_no'] ?? 'No Number') ?>
                        </a>
                    </td>
                    <td style="font-weight:500;">
                        <?= htmlspecialchars(
                            strlen($pub['doc_title'] ?? '') > 55
                            ? substr($pub['doc_title'] ?? '', 0, 55) . '...'
                            : ($pub['doc_title'] ?? '—')
                        ) ?>
                    </td>
                    <td style="font-size:12px; color:var(--color-text-muted);">
                        <?= htmlspecialchars($pub['publication_ref'] ?? '—') ?>
                    </td>
                    <td style="font-size:13px; color:var(--color-text-muted);">
                        <?= htmlspecialchars($pub['published_by_name'] ?? '—') ?>
                    </td>
                    <td class="text-muted" style="font-size:12px;">
                        <?= date('M d, Y', strtotime($pub['published_at'])) ?>
                    </td>
                    <td class="col-actions">
                        <a href="<?= APP_ROOT_URL ?>/publications/view/<?= $pub['id'] ?>"
                           class="btn btn-outline btn-sm">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
