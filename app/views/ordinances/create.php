<?php
/**
 * ORLMS - Create Ordinance View
 *
 * Variables passed from OrdinanceController::create():
 *   $errors — associative array of validation errors
 *   $input  — previously submitted form values (for re-population on error)
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
                    <a href="<?= APP_ROOT_URL ?>/ordinance">Ordinances</a>
                </li>
                <li class="breadcrumb-item active">New Ordinance</li>
            </ul>
            <h1 class="page-title">New Ordinance</h1>
            <p class="page-subtitle">
                Encode a new ordinance. All required fields must be completed before submission.
            </p>
        </div>
        <div>
            <a href="<?= APP_ROOT_URL ?>/ordinance"
               class="btn btn-outline-secondary btn-sm">
                Cancel
            </a>
        </div>
    </div>
</div>

<!-- General Error -->
<?php if (!empty($errors['general'])): ?>
<div class="alert alert-error" role="alert">
    <?= htmlspecialchars($errors['general']) ?>
</div>
<?php endif; ?>

<!-- Create Form -->
<form action="<?= APP_ROOT_URL ?>/ordinance/create"
      method="POST"
      enctype="multipart/form-data"
      id="create-ordinance-form"
      novalidate>

    <div class="row row-2-1" style="align-items:start;">

        <!-- LEFT: Main Form Fields -->
        <div>

            <!-- Basic Information Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <h2 class="card-title">Basic Information</h2>
                </div>
                <div class="card-body">

                    <!-- Title -->
                    <div class="form-group">
                        <label for="title" class="form-label">
                            Title <span class="required">*</span>
                        </label>
                        <input type="text"
                               id="title"
                               name="title"
                               class="form-control <?= !empty($errors['title']) ? 'is-invalid' : '' ?>"
                               placeholder="e.g. An Ordinance Regulating Noise Pollution in Residential Areas"
                               value="<?= htmlspecialchars($input['title'] ?? '') ?>"
                               maxlength="500"
                               required>
                        <?php if (!empty($errors['title'])): ?>
                            <span class="form-error"><?= htmlspecialchars($errors['title']) ?></span>
                        <?php else: ?>
                            <span class="form-hint">Enter the full formal title of the ordinance.</span>
                        <?php endif; ?>
                    </div>

                    <!-- Subject -->
                    <div class="form-group">
                        <label for="subject" class="form-label">
                            Subject / Purpose <span class="required">*</span>
                        </label>
                        <input type="text"
                               id="subject"
                               name="subject"
                               class="form-control <?= !empty($errors['subject']) ? 'is-invalid' : '' ?>"
                               placeholder="e.g. To regulate noise levels in residential areas of the municipality"
                               value="<?= htmlspecialchars($input['subject'] ?? '') ?>"
                               maxlength="500"
                               required>
                        <?php if (!empty($errors['subject'])): ?>
                            <span class="form-error"><?= htmlspecialchars($errors['subject']) ?></span>
                        <?php else: ?>
                            <span class="form-hint">Brief description of the ordinance's purpose.</span>
                        <?php endif; ?>
                    </div>

                    <!-- Date Filed -->
                    <div class="form-group">
                        <label for="date_filed" class="form-label">Date Filed</label>
                        <input type="date"
                               id="date_filed"
                               name="date_filed"
                               class="form-control"
                               value="<?= htmlspecialchars($input['date_filed'] ?? date('Y-m-d')) ?>"
                               max="<?= date('Y-m-d') ?>">
                        <span class="form-hint">Date the ordinance was formally filed. Defaults to today.</span>
                    </div>

                </div>
            </div>

            <!-- Document Content Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Document Content</h2>
                    <span style="font-size:11px; color:var(--color-text-muted);">Required</span>
                </div>
                <div class="card-body">

                    <!-- Content Textarea -->
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="content" class="form-label">
                            Full Text of the Ordinance <span class="required">*</span>
                        </label>
                        <textarea id="content"
                                  name="content"
                                  class="form-control <?= !empty($errors['content']) ? 'is-invalid' : '' ?>"
                                  rows="20"
                                  placeholder="Paste or type the complete text of the ordinance here.

Include all required sections:
- WHEREAS clauses
- NOW THEREFORE clause
- Articles and Sections
- Separability clause
- Repealing clause
- Effectivity clause"
                                  required
                                  style="font-family: 'Courier New', monospace; font-size:13px; line-height:1.7;"><?= htmlspecialchars($input['content'] ?? '') ?></textarea>
                        <?php if (!empty($errors['content'])): ?>
                            <span class="form-error"><?= htmlspecialchars($errors['content']) ?></span>
                        <?php else: ?>
                            <span class="form-hint">
                                The AI Validation module will check this content for completeness,
                                duplicate detection, and similarity with existing documents once submitted.
                            </span>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>

        <!-- RIGHT: File Upload + Submit Panel -->
        <div>

            <!-- File Upload Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <h2 class="card-title">Attached Document</h2>
                </div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="document_file" class="form-label">
                            Upload File
                            <span style="font-weight:400; color:var(--color-text-muted);">
                                (Optional)
                            </span>
                        </label>
                        <input type="file"
                               id="document_file"
                               name="document_file"
                               class="form-control <?= !empty($errors['file']) ? 'is-invalid' : '' ?>"
                               accept=".pdf,.doc,.docx">
                        <?php if (!empty($errors['file'])): ?>
                            <span class="form-error"><?= htmlspecialchars($errors['file']) ?></span>
                        <?php else: ?>
                            <span class="form-hint">
                                Accepted formats: PDF, DOC, DOCX.<br>
                                Maximum file size: 10MB.<br>
                                The typed content above is still required even with an upload.
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- AI Notice Card -->
            <div class="card" style="margin-bottom:20px;
                                     border-left:4px solid var(--color-accent);">
                <div class="card-body" style="padding:16px 18px;">
                    <div style="font-size:12px; font-weight:600;
                                color:var(--color-primary); margin-bottom:6px;
                                text-transform:uppercase; letter-spacing:0.4px;">
                        AI Validation Notice
                    </div>
                    <div style="font-size:12px; color:var(--color-text-muted); line-height:1.6;">
                        Once this ordinance is submitted for review, the system will automatically
                        run an AI validation check covering:
                        <ul style="margin:8px 0 0 16px; padding:0;">
                            <li>Duplicate and similarity detection</li>
                            <li>Completeness check (WHEREAS, NOW THEREFORE, Separability, Effectivity)</li>
                            <li>Comparison with existing enacted documents</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Panel -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Save Document</h2>
                </div>
                <div class="card-body">

                    <div style="font-size:12px; color:var(--color-text-muted);
                                margin-bottom:16px; line-height:1.6;">
                        Saving will create this ordinance as a <strong>Draft</strong>.
                        You can review and edit it before submitting for review.
                    </div>

                    <!-- Save as Draft -->
                    <button type="submit"
                            name="action"
                            value="draft"
                            class="btn btn-primary"
                            style="width:100%; margin-bottom:10px;"
                            id="btn-save-draft">
                        Save as Draft
                    </button>

                    <!-- Cancel -->
                    <a href="<?= APP_ROOT_URL ?>/ordinance"
                       class="btn btn-outline-secondary"
                       style="width:100%; text-align:center; display:block;">
                        Cancel
                    </a>

                </div>
            </div>

        </div>

    </div>

</form>

<script>
    // Character count feedback for title
    (function () {
        var titleInput = document.getElementById('title');
        if (!titleInput) return;
        titleInput.addEventListener('input', function () {
            var remaining = 500 - this.value.length;
            var hint = this.nextElementSibling;
            if (hint && hint.classList.contains('form-hint')) {
                hint.textContent = 'Enter the full formal title of the ordinance. ('
                    + remaining + ' characters remaining)';
            }
        });
    })();

    // Prevent double-submit
    document.getElementById('create-ordinance-form')
        .addEventListener('submit', function () {
            var btn = document.getElementById('btn-save-draft');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Saving...';
            }
        });
</script>
