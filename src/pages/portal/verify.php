<?php
/**
 * ORLMS - Public Portal Document Verification View
 *
 * Displays a security verification certificate proving document authenticity.
 *
 * Variables:
 *   $publication — joined publication record
 */

// Initialize variables to prevent undefined variable warnings
if (empty($publication) || !is_array($publication)) {
    $publication = [];
}

$docNo = $publication['doc_no'] ?? 'N/A';
$docType = $publication['document_type'] ?? 'ordinance';
$docTitle = $publication['doc_title'] ?? 'N/A';
$docSubject = $publication['doc_subject'] ?? 'N/A';
$publishedAt = $publication['published_at'] ?? '';
$publicationRef = $publication['publication_ref'] ?? 'N/A';
$docContent = $publication['doc_content'] ?? '';
$documentId = $publication['document_id'] ?? 0;
$authorName = $publication['author_name'] ?? 'Unknown';

// Generate document hash to act as a secure digital fingerprint
$fingerprint = hash('sha256', $docContent);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .verify-card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-top: 5px solid #198754; /* Success Green */
            border-radius: 8px;
            padding: 40px;
            max-width: 680px;
            margin: 40px auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .verify-badge-container {
            text-align: center;
            margin-bottom: 25px;
        }
        .verify-badge-icon {
            font-size: 60px;
            color: #198754;
            line-height: 1;
            margin-bottom: 12px;
            display: inline-block;
        }
        .verify-badge-title {
            font-size: 24px;
            font-weight: 800;
            color: #198754;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .verify-badge-subtitle {
            font-size: 12px;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 6px 0 0 0;
        }
        
        .verify-info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            font-size: 13.5px;
        }
        .verify-info-table th {
            text-align: left;
            padding: 12px 14px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            color: #495057;
            font-weight: 700;
            width: 30%;
        }
        .verify-info-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #dee2e6;
            color: #212529;
            line-height: 1.5;
        }
        
        .security-stamp {
            background-color: rgba(25, 135, 84, 0.05);
            border: 1px dashed rgba(25, 135, 84, 0.3);
            border-radius: 6px;
            padding: 16px;
            font-size: 11px;
            color: #198754;
            line-height: 1.6;
            font-family: monospace;
            word-break: break-all;
            margin-bottom: 30px;
        }
        .security-stamp strong {
            font-family: 'Inter', sans-serif;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }

        .verify-btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .btn-verify-primary {
            background-color: #0c2340;
            color: #ffffff;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.2s;
            border: none;
        }
        .btn-verify-primary:hover {
            background-color: #16365c;
            color: #ffffff;
        }
        .btn-verify-outline {
            background-color: transparent;
            color: #495057;
            border: 1px solid #ced4da;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-verify-outline:hover {
            background-color: #f8f9fa;
            border-color: #adb5bd;
            color: #212529;
        }

        @media print {
            body {
                background-color: #ffffff !important;
            }
            .public-navbar, .public-footer, .verify-btn-group, .verify-badge-icon {
                display: none !important;
            }
            .verify-card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .security-stamp {
                background-color: #ffffff !important;
                border: 1px solid #198754 !important;
            }
        }
    </style>
</head>
<body>

<div class="verify-card">
    <div class="verify-badge-container">
        <span class="verify-badge-icon"><i class="bi bi-patch-check-fill"></i></span>
        <h1 class="verify-badge-title">Officially Verified</h1>
        <p class="verify-badge-subtitle">City of San Jose del Monte • Sangguniang Panlungsod</p>
    </div>

    <table class="verify-info-table">
        <tr>
            <th>Document No.</th>
            <td style="font-weight:700; color:#0c2340;"><?= htmlspecialchars($docNo) ?></td>
        </tr>
        <tr>
            <th>Document Type</th>
            <td style="text-transform: capitalize; font-weight: 600;"><?= htmlspecialchars($docType) ?></td>
        </tr>
        <tr>
            <th>Subject / Title</th>
            <td><strong><?= htmlspecialchars($docSubject) ?></strong></td>
        </tr>
        <tr>
            <th>Author / Sponsor</th>
            <td><?= htmlspecialchars($authorName) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><span class="badge badge-published" style="padding: 4px 8px; font-size: 10px;">PUBLISHED ORIGINAL</span></td>
        </tr>
        <tr>
            <th>Published Date</th>
            <td><?= date('F d, Y', strtotime($publishedAt)) ?></td>
        </tr>
        <tr>
            <th>Publication Ref</th>
            <td style="font-family: monospace; font-size:12px; color:#495057;"><?= htmlspecialchars($publicationRef) ?></td>
        </tr>
    </table>

    <div class="security-stamp">
        <strong>Digital Security Fingerprint (SHA-256)</strong>
        <?= $fingerprint ?>
        <span style="display:block; margin-top:8px; font-family:'Inter', sans-serif; font-size:10px; color:#6c757d; font-style:italic;">
            This SHA-256 hash checksum represents a unique tamper-proof fingerprint generated directly from the document text. Any modification to the document will invalidate this seal.
        </span>
    </div>

    <div class="verify-btn-group">
        <a href="<?= APP_URL ?>/portal/view/<?= $docType ?>/<?= $documentId ?>" class="btn-verify-primary">
            <i class="bi bi-file-earmark-text"></i> View Full Document
        </a>
        <button onclick="window.print()" class="btn-verify-outline">
            <i class="bi bi-printer"></i> Print Verification
        </button>
    </div>
</div>

</body>
</html>
