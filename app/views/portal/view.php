<?php
/**
 * ORLMS - Public Portal Document Detail View
 *
 * Variables:
 *   $publication — joined publication record
 */

if (empty($publication) || !is_array($publication)) {
    $publication = [];
}

$docNo = $publication['doc_no'] ?? 'N/A';
$docType = $publication['document_type'] ?? 'ordinance';
$docId = $publication['document_id'] ?? 0;
$docContent = $publication['doc_content'] ?? '';

// Generate SHA-256 fingerprint for document integrity check
$fingerprint = hash('sha256', $docContent);
$verificationUrl = APP_URL . "/portal/verify/{$docType}/{$docId}";
?>

<!-- Print-specific styles to hide website decorations and expand page width -->
<style>
    @media print {
        .public-navbar, .public-footer, .btn, .btn-verify-primary, .btn-verify-outline, .back-link-wrapper, .no-print {
            display: none !important;
        }
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .row-2-1 {
            display: block !important;
        }
        .row-2-1 > div {
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .doc-content-body {
            background-color: #ffffff !important;
            border: 1px solid #000000 !important;
            padding: 15px !important;
            font-family: 'Courier New', Courier, monospace !important;
            font-size: 13px !important;
        }
    }
</style>

<!-- Page Header -->
<div style="margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid var(--color-border);">
    <div style="display:flex; justify-content:space-between; align-items:start; flex-wrap:wrap; gap:16px;">
        <div>
            <div class="back-link-wrapper" style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
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
        <div style="text-align:right; font-size:12px; color:var(--color-text-muted);" class="no-print">
            Published on <strong><?= date('F d, Y', strtotime($publication['published_at'])) ?></strong>
            <div style="margin-top:10px; display: flex; gap: 8px; justify-content: flex-end;">
                <button onclick="window.print()" class="btn btn-sm btn-outline-dark" style="font-size:12px; font-weight:600; padding:6px 12px;">
                    <i class="bi bi-printer me-1"></i> Print Text
                </button>
                <?php if (!empty($publication['file_path'])): ?>
                <a href="<?= APP_URL ?>/public/uploads/documents/<?= htmlspecialchars($publication['file_path']) ?>"
                   target="_blank" class="btn btn-primary btn-sm"
                   style="font-size:12px; font-weight:600; padding:6px 12px; background-color:var(--color-accent); border-color:var(--color-accent); color:var(--color-primary);">
                    Download PDF
                </a>
                <?php endif; ?>
            </div>
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
            <div style="font-size:11px; color:var(--color-text-muted); margin-top:12px; font-style:italic;" class="no-print">
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

    <!-- RIGHT: Document Metadata & Security Verification -->
    <div>
        <!-- 1. Metadata Details Card -->
        <div class="card" style="box-shadow:var(--shadow-sm); margin-bottom: 24px;">
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

        <!-- 2. Secure Document Verification Card -->
        <div class="card" style="box-shadow:var(--shadow-sm);">
            <div class="card-header" style="background:#ffffff; border-bottom:1px solid var(--color-border-light);">
                <h2 class="card-title" style="font-size:14px; font-weight:700; color:var(--color-primary);">
                    <i class="bi bi-shield-lock-fill text-success me-1"></i> Security & Verification
                </h2>
            </div>
            <div class="card-body" style="padding:20px; text-align: center;">
                <p style="font-size:12px; color:var(--color-text-muted); line-height: 1.5; margin-bottom: 15px;">
                    Scan the QR code to verify this document's official authenticity on the CSJDM Sangguniang Panlungsod database.
                </p>

                <!-- Scan-Ready QR Code from QRServer API -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=<?= urlencode($verificationUrl) ?>" 
                     alt="Verification QR Code" 
                     style="border: 1px solid #e2e8f0; padding: 6px; border-radius: 6px; display: block; margin: 0 auto 12px auto; width: 140px; height: 140px;">

                <a href="<?= $verificationUrl ?>" target="_blank" style="font-size:12.5px; color:#0084FF; text-decoration:none; font-weight:700; display:inline-block; margin-bottom:15px;" class="no-print">
                    <i class="bi bi-patch-check-fill me-1 text-success"></i> Verify Authenticity
                </a>

                <!-- SHA-256 Digital Fingerprint -->
                <div style="font-size:10px; font-family:monospace; background:#f8f9fa; border:1px solid #e2e8f0; border-radius:4px; padding:10px; text-align: left; word-break:break-all;">
                    <strong style="font-family:'Inter', sans-serif; display:block; font-size:9.5px; color:#64748b; text-transform:uppercase; margin-bottom:4px;">
                        Doc Fingerprint (SHA-256)
                    </strong>
                    <?= $fingerprint ?>
                </div>
            </div>
        </div>
    </div>

</div>
