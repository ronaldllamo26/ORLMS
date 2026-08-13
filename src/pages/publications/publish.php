<?php
/**
 * ORLMS - Publish Document Form
 *
 * Variables:
 *   $document — the enacted document record
 *   $docType  — 'ordinance' or 'resolution'
 *   $errors   — validation errors
 *   $input    — re-filled form values on error
 */

$noField = $docType === 'ordinance' ? 'ordinance_no' : 'resolution_no';
$docNo   = $document[$noField] ?? 'N/A';
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
                <li class="breadcrumb-item active">Publish <?= htmlspecialchars($docNo) ?></li>
            </ul>
            <h1 class="page-title">Publish Document</h1>
            <p class="page-subtitle"><?= htmlspecialchars($document['title']) ?></p>
        </div>
    </div>
</div>

<div class="row row-2-1" style="align-items:start;">

    <!-- LEFT: Publish Form -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Publication Details</h2>
            </div>
            <div class="card-body">

                <form method="POST"
                      action="<?= APP_ROOT_URL ?>/publications/publish/<?= $docType ?>/<?= $document['id'] ?>"
                      enctype="multipart/form-data"
                      id="publish-form">

                    <!-- Publication Reference -->
                    <div class="form-group">
                        <label for="publication_ref" class="form-label">
                            Publication Reference <span class="form-required">*</span>
                        </label>
                        <input type="text" id="publication_ref" name="publication_ref"
                               class="form-control"
                               value="<?= htmlspecialchars($input['publication_ref'] ?? '') ?>"
                               placeholder="e.g. Official Gazette Vol. 120 No. 15, Jul 5, 2026"
                               required>
                        <span class="form-hint">
                            Official citation where this document was published
                            (e.g. Official Gazette, Municipal Bulletin Board, etc.)
                        </span>
                        <?php if (!empty($errors['publication_ref'])): ?>
                        <span class="form-error">
                            <?= htmlspecialchars($errors['publication_ref']) ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Plain-Language Summary -->
                    <?php 
                        $summaryValue = $input['plain_summary'] ?? $document['ai_summary'] ?? '';
                        $hasAiSummary = !empty($document['ai_summary']);
                    ?>
                    <div class="form-group">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
                            <label for="plain_summary" class="form-label" style="margin:0;">
                                Plain-Language Summary <span class="form-required">*</span>
                            </label>
                            <?php if ($hasAiSummary): ?>
                                <span class="badge badge-info" style="font-size:10px; padding:3px 8px; background:#e0f2fe; color:#0369a1; border-radius:12px; font-weight:600;">
                                    ✨ AI Summary Suggested
                                </span>
                            <?php endif; ?>
                        </div>
                        <textarea id="plain_summary" name="plain_summary"
                                  class="form-control" rows="5"
                                  placeholder="Write a simple, easy-to-understand summary of what this <?= $docType ?> does and who it affects..."
                                  required><?= htmlspecialchars($summaryValue) ?></textarea>
                        <span class="form-hint">
                            This summary will be visible to the general public on the Public Portal.
                            <?= $hasAiSummary ? 'Pre-filled by AI Validation Engine — you can edit or customize it.' : 'Use simple language — avoid legal jargon.' ?>
                        </span>
                        <?php if (!empty($errors['plain_summary'])): ?>
                        <span class="form-error">
                            <?= htmlspecialchars($errors['plain_summary']) ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Publication File Upload (optional) -->
                    <div class="form-group">
                        <label for="publication_file" class="form-label">
                            Publication File
                            <span style="font-size:11px; color:var(--color-text-muted);
                                         font-weight:400;">(optional)</span>
                        </label>
                        <input type="file" id="publication_file" name="publication_file"
                               class="form-control"
                               accept=".pdf,.doc,.docx"
                               style="padding:8px;">
                        <span class="form-hint">
                            Upload the official published version (PDF or Word, max 10MB)
                        </span>
                        <?php if (!empty($errors['file'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['file']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"
                                id="btn-publish"
                                onclick="return confirm('Publish <?= htmlspecialchars($docNo) ?>? This will mark it as officially published and it will be visible in the public record.')">
                            Publish Officially
                        </button>
                        <a href="<?= APP_ROOT_URL ?>/publications"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT: Document Info -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Document Being Published</h2>
                <span class="badge badge-enacted" style="font-size:11px;">ENACTED</span>
            </div>
            <div class="card-body">
                <div class="doc-meta-grid">
                    <span class="doc-meta-label">Document No.</span>
                    <span class="doc-meta-value" style="font-weight:700; color:var(--color-primary);">
                        <?= htmlspecialchars($docNo) ?>
                    </span>

                    <span class="doc-meta-label">Type</span>
                    <span class="doc-meta-value" style="text-transform:capitalize;">
                        <?= $docType ?>
                    </span>

                    <span class="doc-meta-label">Title</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($document['title']) ?>
                    </span>

                    <span class="doc-meta-label">Subject</span>
                    <span class="doc-meta-value">
                        <?= htmlspecialchars($document['subject']) ?>
                    </span>

                    <span class="doc-meta-label">Author</span>
                    <span class="doc-meta-value text-muted">
                        <?= htmlspecialchars($document['author_name'] ?? 'Unknown') ?>
                    </span>

                    <span class="doc-meta-label">Date Filed</span>
                    <span class="doc-meta-value text-muted">
                        <?= $document['date_filed'] ? date('F d, Y', strtotime($document['date_filed'])) : '—' ?>
                    </span>
                </div>

                <div style="margin-top:18px; padding:12px; background:var(--color-bg);
                            border-radius:var(--radius); border:1px solid var(--color-border-light);
                            font-size:12px; color:var(--color-text-muted); line-height:1.6;">
                    <strong style="color:var(--color-primary);">Note:</strong>
                    Publishing this document will change its status from
                    <strong>Enacted</strong> to <strong>Published</strong>.
                    This action is permanent and will be recorded in the audit log.
                </div>
            </div>
        </div>
    </div>

</div>
