<?php
/**
 * ORLMS - Create Amendment Form
 *
 * Variables:
 *   $document — the enacted/published document being amended
 *   $docType  — 'ordinance' or 'resolution'
 *   $errors   — validation errors
 *   $input    — re-filled form input
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
                    <a href="<?= APP_ROOT_URL ?>/amendments">Amendments</a>
                </li>
                <li class="breadcrumb-item active">New Amendment</li>
            </ul>
            <h1 class="page-title">Amend Document</h1>
            <p class="page-subtitle">Creating an amendment draft for <?= htmlspecialchars($docNo) ?></p>
        </div>
    </div>
</div>

<div class="row row-2-1" style="align-items:start; gap:20px;">

    <!-- Left: Form -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Amendment Information</h2>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="<?= APP_ROOT_URL ?>/amendments/create/<?= $docType ?>/<?= $document['id'] ?>"
                      id="create-amendment-form">

                    <!-- Amendment Number (Optional) -->
                    <div class="form-group">
                        <label for="amendment_no" class="form-label">
                            Amendment No. <span style="font-size:11px; color:var(--color-text-muted); font-weight:400;">(optional)</span>
                        </label>
                        <input type="text" id="amendment_no" name="amendment_no" class="form-control"
                               value="<?= htmlspecialchars($input['amendment_no'] ?? '') ?>"
                               placeholder="e.g. AMEND-<?= htmlspecialchars($docNo) ?>-001">
                        <span class="form-hint">
                            Leave empty to automatically generate a standard amendment identifier.
                        </span>
                    </div>

                    <!-- Description/Rationale -->
                    <div class="form-group">
                        <label for="description" class="form-label">
                            Rationale / Description <span class="form-required">*</span>
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="4"
                                  placeholder="Provide the reason, legislative context, or purpose for amending this document..."
                                  required><?= htmlspecialchars($input['description'] ?? '') ?></textarea>
                        <?php if (!empty($errors['description'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['description']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Specific Changes / Revision Details -->
                    <div class="form-group">
                        <label for="changes" class="form-label">
                            Amendment / Revision Details <span class="form-required">*</span>
                        </label>
                        <textarea id="changes" name="changes" class="form-control" rows="10"
                                  placeholder="Specify the exact sections, clauses, or words being updated, inserted, or deleted. For example:&#10;- Section 3 is amended to increase penalty from ₱1,000 to ₱5,000.&#10;- Delete Section 4 in its entirety."
                                  required><?= htmlspecialchars($input['changes'] ?? '') ?></textarea>
                        <span class="form-hint">
                            Be precise. Outline the differences clearly so reviewers can verify the exact revisions.
                        </span>
                        <?php if (!empty($errors['changes'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['changes']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="btn-save-amendment">
                            Save Draft Amendment
                        </button>
                        <a href="<?= APP_ROOT_URL ?>/amendments" class="btn btn-outline-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Right: Document Info Card -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Target Document</h2>
                <span class="badge badge-<?= $document['status'] ?? 'enacted' ?>" style="font-size:11px; text-transform:uppercase;">
                    <?= htmlspecialchars($document['status'] ?? '') ?>
                </span>
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
                    <span class="doc-meta-value"><?= htmlspecialchars($document['title']) ?></span>

                    <span class="doc-meta-label">Subject</span>
                    <span class="doc-meta-value"><?= htmlspecialchars($document['subject']) ?></span>

                    <span class="doc-meta-label">Author</span>
                    <span class="doc-meta-value text-muted">
                        <?= htmlspecialchars($document['author_name'] ?? 'Unknown') ?>
                    </span>

                    <span class="doc-meta-label">Date Filed</span>
                    <span class="doc-meta-value text-muted">
                        <?= $document['date_filed'] ? date('F d, Y', strtotime($document['date_filed'])) : '—' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>
