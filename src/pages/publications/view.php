<?php
/**
 * ORLMS - Publication Detail View
 *
 * Variables:
 *   $publication — full publication record with joined document data
 */

$publication = $publication ?? [];
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
                    <a href="<?= APP_ROOT_URL ?>/publications">Publications</a>
                </li>
                <li class="breadcrumb-item active">
                    <?= htmlspecialchars($publication['doc_no'] ?? 'Publication') ?>
                </li>
            </ul>
            <h1 class="page-title">
                <?= htmlspecialchars($publication['doc_no'] ?? 'Publication Record') ?>
            </h1>
            <p class="page-subtitle"><?= htmlspecialchars($publication['doc_title'] ?? '') ?></p>
        </div>
        <a href="<?= APP_ROOT_URL ?>/publications" class="btn btn-outline-secondary btn-sm">
            Back to Publications
        </a>
    </div>
</div>

<!-- Publication Reference Banner -->
<div style="background: linear-gradient(135deg, var(--color-primary), #264d7a);
            color:#fff; border-radius:var(--radius); padding:18px 24px;
            margin-bottom:24px; display:flex; align-items:center;
            justify-content:space-between;">
    <div>
        <div style="font-size:10px; font-weight:700; text-transform:uppercase;
                    letter-spacing:0.8px; opacity:0.75; margin-bottom:4px;">
            Official Publication Reference
        </div>
        <div style="font-size:16px; font-weight:700;">
            <?= htmlspecialchars($publication['publication_ref'] ?? '—') ?>
        </div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:10px; opacity:0.75; margin-bottom:2px;">Published on</div>
        <div style="font-size:14px; font-weight:600;">
            <?= date('F d, Y', strtotime($publication['published_at'])) ?>
        </div>
        <div style="font-size:11px; opacity:0.75;">
            by <?= htmlspecialchars($publication['published_by_name'] ?? 'Unknown') ?>
        </div>
    </div>
</div>

<div class="row row-2-1" style="align-items:start;">

    <!-- LEFT: Document Info + Content + Summary -->
    <div>

        <!-- Metadata -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Document Information</h2>
                <span class="badge badge-published" style="font-size:11px;">PUBLISHED</span>
            </div>
            <div class="card-body">
                <div class="doc-meta-grid">
                    <span class="doc-meta-label">Document No.</span>
                    <span class="doc-meta-value" style="font-weight:700; color:var(--color-primary);">
                        <?= htmlspecialchars($publication['doc_no'] ?? '—') ?>
                    </span>

                    <span class="doc-meta-label">Type</span>
                    <span class="doc-meta-value" style="text-transform:capitalize;">
                        <?= htmlspecialchars($publication['document_type']) ?>
                    </span>

                    <span class="doc-meta-label">Title</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($publication['doc_title'] ?? '—') ?>
                    </span>

                    <span class="doc-meta-label">Subject</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($publication['doc_subject'] ?? '—') ?>
                    </span>

                    <span class="doc-meta-label">Author</span>
                    <span class="doc-meta-value text-muted">
                        <?= htmlspecialchars($publication['author_name'] ?? '—') ?>
                    </span>

                    <span class="doc-meta-label">Date Filed</span>
                    <span class="doc-meta-value text-muted">
                        <?= !empty($publication['date_filed'])
                            ? date('F d, Y', strtotime($publication['date_filed']))
                            : '—' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Plain-Language Summary -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h2 class="card-title">Plain-Language Summary</h2>
                <span style="font-size:11px; color:var(--color-text-muted);">
                    For public reference
                </span>
            </div>
            <div class="card-body">
                <div style="font-size:14px; line-height:1.8; color:var(--color-text);">
                    <?= htmlspecialchars($publication['plain_summary'] ?? '—') ?>
                </div>
            </div>
        </div>

        <!-- Full Document Content -->
        <?php if (!empty($publication['doc_content'])): ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Full Document Text</h2>
            </div>
            <div class="card-body">
                <div class="doc-content-body" id="pub-doc-content">
                    <?= htmlspecialchars($publication['doc_content']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- RIGHT: Publication Details -->
    <div>
        <div class="card">
            <div class="card-header"><h2 class="card-title">Publication Record</h2></div>
            <div class="card-body">
                <div class="doc-meta-grid">

                    <span class="doc-meta-label">Pub. ID</span>
                    <span class="doc-meta-value" style="font-weight:700;">
                        #<?= $publication['id'] ?>
                    </span>

                    <span class="doc-meta-label">Reference</span>
                    <span class="doc-meta-value" style="font-size:12px;">
                        <?= htmlspecialchars($publication['publication_ref'] ?? '—') ?>
                    </span>

                    <span class="doc-meta-label">Published By</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($publication['published_by_name'] ?? '—') ?>
                    </span>

                    <span class="doc-meta-label">Date Published</span>
                    <span class="doc-meta-value">
                        <?= date('F d, Y', strtotime($publication['published_at'])) ?>
                    </span>

                    <span class="doc-meta-label">Time</span>
                    <span class="doc-meta-value text-muted" style="font-size:12px;">
                        <?= date('h:i A', strtotime($publication['published_at'])) ?>
                    </span>

                </div>

                <?php if (!empty($publication['file_path'])): ?>
                <div style="margin-top:18px; padding-top:16px;
                            border-top:1px solid var(--color-border-light);">
                    <div style="font-size:12px; font-weight:600; color:var(--color-text);
                                margin-bottom:8px;">
                        Publication File
                    </div>
                    <a href="<?= APP_URL ?>/public/uploads/documents/<?= htmlspecialchars($publication['file_path']) ?>"
                       target="_blank"
                       class="btn btn-outline btn-sm" style="width:100%; text-align:center;">
                        Download Publication File
                    </a>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</div>
