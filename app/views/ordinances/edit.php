<?php
/**
 * ORLMS - Edit Ordinance View
 *
 * Variables passed from OrdinanceController::edit():
 *   $ordinance — original ordinance record from database
 *   $errors    — associative array of validation errors
 *   $input     — previously submitted form values (for re-population on error)
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
                <li class="breadcrumb-item">
                    <a href="<?= APP_ROOT_URL ?>/ordinance/view/<?= $ordinance['id'] ?>">
                        <?= htmlspecialchars($ordinance['ordinance_no']) ?>
                    </a>
                </li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
            <h1 class="page-title">Edit Ordinance</h1>
            <p class="page-subtitle">
                Editing <?= htmlspecialchars($ordinance['ordinance_no']) ?>
                — Only draft ordinances can be edited.
            </p>
        </div>
        <div>
            <a href="<?= APP_ROOT_URL ?>/ordinance/view/<?= $ordinance['id'] ?>"
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

<!-- Edit Form -->
<form action="<?= APP_ROOT_URL ?>/ordinance/edit/<?= $ordinance['id'] ?>"
      method="POST"
      enctype="multipart/form-data"
      id="edit-ordinance-form"
      novalidate>

    <div class="row row-2-1" style="align-items:start;">

        <!-- LEFT: Main Form Fields -->
        <div>

            <!-- Read-only Info Banner -->
            <div style="background-color:var(--color-primary); color:#fff;
                        padding:10px 16px; border-radius:var(--radius);
                        font-size:12px; margin-bottom:20px;
                        display:flex; align-items:center; gap:12px;">
                <span style="background:var(--color-accent); color:var(--color-primary-dark);
                              font-weight:700; padding:2px 8px; border-radius:3px;
                              font-size:11px; letter-spacing:0.5px;">
                    DRAFT
                </span>
                <span>
                    Ordinance No. <strong><?= htmlspecialchars($ordinance['ordinance_no']) ?></strong>
                    &mdash; Filed on <?= date('F d, Y', strtotime($ordinance['date_filed'])) ?>
                </span>
            </div>

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
                            <span class="form-hint">Full formal title of the ordinance.</span>
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
                               placeholder="Brief description of the ordinance's purpose"
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
                               value="<?= htmlspecialchars($input['date_filed'] ?? '') ?>"
                               max="<?= date('Y-m-d') ?>">
                        <span class="form-hint">Date the ordinance was formally filed.</span>
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
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="content" class="form-label">
                            Full Text of the Ordinance <span class="required">*</span>
                        </label>
                        <textarea id="content"
                                  name="content"
                                  class="form-control <?= !empty($errors['content']) ? 'is-invalid' : '' ?>"
                                  rows="20"
                                  required
                                  style="font-family:'Courier New', monospace; font-size:13px; line-height:1.7;"><?= htmlspecialchars($input['content'] ?? '') ?></textarea>
                        <?php if (!empty($errors['content'])): ?>
                            <span class="form-error"><?= htmlspecialchars($errors['content']) ?></span>
                        <?php else: ?>
                            <span class="form-hint">
                                Edit the full text of the ordinance. Changes are only saved when
                                you click "Save Changes" below.
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT: File Upload + Actions Panel -->
        <div>

            <!-- Current File Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <h2 class="card-title">Attached Document</h2>
                </div>
                <div class="card-body">

                    <!-- Show current file if any -->
                    <?php if (!empty($ordinance['file_path'])): ?>
                    <div style="background:var(--color-bg); border:1px solid var(--color-border);
                                border-radius:var(--radius); padding:12px 14px;
                                margin-bottom:14px; font-size:12px;">
                        <div style="font-weight:600; color:var(--color-text-muted);
                                    text-transform:uppercase; letter-spacing:0.4px;
                                    font-size:10px; margin-bottom:6px;">
                            Current File
                        </div>
                        <a href="<?= APP_URL ?>/public/uploads/documents/<?= htmlspecialchars($ordinance['file_path']) ?>"
                           target="_blank"
                           style="color:var(--color-primary); font-weight:500;">
                            <?= htmlspecialchars($ordinance['file_path']) ?>
                        </a>
                        <div style="color:var(--color-text-muted); margin-top:4px; font-size:11px;">
                            Upload a new file below to replace this.
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-group" style="margin-bottom:0;">
                        <label for="document_file" class="form-label">
                            <?= !empty($ordinance['file_path']) ? 'Replace File' : 'Upload File' ?>
                            <span style="font-weight:400; color:var(--color-text-muted);">(Optional)</span>
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
                                PDF, DOC, DOCX only. Max 10MB.
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Danger Zone — Delete Draft -->
            <div class="card" style="margin-bottom:20px;
                                     border-left:4px solid #dc3545;">
                <div class="card-body" style="padding:16px 18px;">
                    <div style="font-size:12px; font-weight:600;
                                color:#842029; margin-bottom:8px;
                                text-transform:uppercase; letter-spacing:0.4px;">
                        Delete This Draft
                    </div>
                    <div style="font-size:12px; color:var(--color-text-muted);
                                line-height:1.6; margin-bottom:12px;">
                        Permanently deletes this draft ordinance.
                        This action cannot be undone.
                    </div>
                    <a href="<?= APP_ROOT_URL ?>/ordinance/delete/<?= $ordinance['id'] ?>"
                       class="btn btn-danger btn-sm"
                       style="width:100%; text-align:center;"
                       id="btn-delete-draft"
                       onclick="return confirm('Permanently delete this draft? This cannot be undone.')">
                        Delete Draft
                    </a>
                </div>
            </div>

            <!-- Save Actions Panel -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Save Changes</h2>
                </div>
                <div class="card-body">

                    <div style="font-size:12px; color:var(--color-text-muted);
                                margin-bottom:16px; line-height:1.6;">
                        Changes will be saved to this draft. The ordinance will
                        remain in <strong>Draft</strong> status until submitted
                        for review.
                    </div>

                    <!-- Save Changes -->
                    <button type="submit"
                            class="btn btn-primary"
                            style="width:100%; margin-bottom:10px;"
                            id="btn-save-changes">
                        Save Changes
                    </button>

                    <!-- Submit for Review -->
                    <a href="<?= APP_ROOT_URL ?>/ordinance/submit/<?= $ordinance['id'] ?>"
                       class="btn btn-accent"
                       style="width:100%; text-align:center; display:block; margin-bottom:10px;"
                       id="btn-submit-review"
                       onclick="return confirm('Submit for review? You will no longer be able to edit this ordinance after submission.')">
                        Submit for Review
                    </a>

                    <!-- Cancel -->
                    <a href="<?= APP_ROOT_URL ?>/ordinance/view/<?= $ordinance['id'] ?>"
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
    // Prevent double-submit
    document.getElementById('edit-ordinance-form')
        .addEventListener('submit', function () {
            var btn = document.getElementById('btn-save-changes');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Saving...';
            }
        });
</script>
