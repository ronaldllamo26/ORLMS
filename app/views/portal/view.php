<?php
/**
 * ORLMS - Public Portal Document Detail View
 *
 * Variables:
 *   $publication — joined publication record
 */

$docNo = $publication['doc_no'] ?? 'N/A';
?>

<!-- Page Header -->
<div style="margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid var(--color-border);">
    <div style="display:flex; justify-content:space-between; align-items:start; flex-wrap:wrap; gap:16px;">
        <div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                <a href="<?= APP_ROOT_URL ?>/portal"
                   style="font-size:12px; color:var(--color-accent); text-decoration:none; font-weight:600;">
                   &larr; Back to Registry
                </a>
                <span style="color:var(--color-text-light); font-size:11px;">/</span>
                <span class="badge <?= $publication['document_type'] === 'ordinance' ? 'badge-enacted' : 'badge-endorsed' ?>"
                      style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; padding:3px 8px;">
                    <?= $publication['document_type'] ?>
                </span>
            </div>
            <h1 style="font-size:22px; font-weight:700; color:var(--color-primary); margin:0 0 6px;">
                <?= htmlspecialchars($docNo) ?>
            </h1>
            <p style="font-size:13px; color:var(--color-text-muted); margin:0;">
                Published Reference: <strong><?= htmlspecialchars($publication['publication_ref'] ?? 'N/A') ?></strong>
            </p>
        </div>
        <div style="text-align:right; font-size:12px; color:var(--color-text-muted);">
            Published on <strong><?= date('F d, Y', strtotime($publication['published_at'])) ?></strong>
            <?php if (!empty($publication['file_path'])): ?>
            <div style="margin-top:10px;">
                <a href="<?= APP_URL ?>/public/uploads/documents/<?= htmlspecialchars($publication['file_path']) ?>"
                   target="_blank" class="btn btn-primary btn-sm"
                   style="font-size:12px; font-weight:600; padding:6px 16px; background-color:var(--color-accent); border-color:var(--color-accent); color:var(--color-primary);">
                    Download Official Document
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row row-2-1" style="align-items:start; gap:24px;">

    <!-- LEFT: Summary and Content -->
    <div>
        
        <!-- Plain-Language Summary (highly prominent) -->
        <div style="background-color:rgba(201, 168, 76, 0.08); border:1px solid rgba(201, 168, 76, 0.25);
                    border-radius:var(--radius-lg); padding:20px 24px; margin-bottom:24px;">
            <div style="font-size:10px; font-weight:700; text-transform:uppercase;
                        letter-spacing:0.6px; color:var(--color-primary); margin-bottom:8px;">
                Plain-Language Public Summary
            </div>
            <div style="font-size:14px; line-height:1.8; color:var(--color-text); font-weight:500;">
                <?= htmlspecialchars($publication['plain_summary'] ?? 'No summary available.') ?>
            </div>
            <div style="font-size:11px; color:var(--color-text-muted); margin-top:12px; font-style:italic;">
                This summary is provided to translate legal terminology into clear, accessible language for the public.
            </div>
        </div>

        <!-- Full Document Content -->
        <div class="card" style="margin-bottom:20px; box-shadow:var(--shadow-sm);">
            <div class="card-header" style="background:#ffffff; border-bottom:1px solid var(--color-border-light);">
                <h2 class="card-title" style="font-size:15px; font-weight:700; color:var(--color-primary);">
                    Official Document Text
                </h2>
            </div>
            <div class="card-body" style="padding:24px;">
                <div class="doc-content-body" style="font-size:13.5px; line-height:1.8; color:var(--color-text);
                                                     background:#fafafa; border:1px solid var(--color-border-light);
                                                     border-radius:var(--radius); padding:20px; white-space:pre-wrap;
                                                     font-family:monospace; user-select:text;">
                    <?= htmlspecialchars($publication['doc_content'] ?? 'No document text available.') ?>
                </div>
            </div>
        </div>

    </div>

    <!-- RIGHT: Document Metadata -->
    <div>
        <div class="card" style="box-shadow:var(--shadow-sm);">
            <div class="card-header" style="background:#ffffff; border-bottom:1px solid var(--color-border-light);">
                <h2 class="card-title" style="font-size:14px; font-weight:700; color:var(--color-primary);">
                    Metadata Details
                </h2>
            </div>
            <div class="card-body" style="padding:20px;">
                <div class="doc-meta-grid" style="row-gap:12px;">
                    <span class="doc-meta-label">Document No.</span>
                    <span class="doc-meta-value" style="font-weight:700; color:var(--color-primary);">
                        <?= htmlspecialchars($docNo) ?>
                    </span>

                    <span class="doc-meta-label">Type</span>
                    <span class="doc-meta-value" style="text-transform:capitalize;">
                        <?= htmlspecialchars($publication['document_type']) ?>
                    </span>

                    <span class="doc-meta-label">Subject</span>
                    <span class="doc-meta-value"><?= htmlspecialchars($publication['doc_subject'] ?? '—') ?></span>

                    <span class="doc-meta-label">Author / Sponsor</span>
                    <span class="doc-meta-value text-muted"><?= htmlspecialchars($publication['author_name'] ?? 'Unknown') ?></span>

                    <?php if ($publication['date_filed']): ?>
                    <span class="doc-meta-label">Date Filed</span>
                    <span class="doc-meta-value text-muted"><?= date('F d, Y', strtotime($publication['date_filed'])) ?></span>
                    <?php endif; ?>

                    <span class="doc-meta-label">Date Published</span>
                    <span class="doc-meta-value text-muted"><?= date('F d, Y', strtotime($publication['published_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>

</div>
