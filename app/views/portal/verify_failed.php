<?php
/**
 * ORLMS - Public Portal Document Verification Failed View
 *
 * Displays a warning when document verification fails.
 *
 * Variables:
 *   $type — document type
 *   $id   — document ID
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .verify-card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-top: 5px solid #dc3545; /* Danger Red */
            border-radius: 8px;
            padding: 40px;
            max-width: 580px;
            margin: 50px auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            text-align: center;
        }
        .verify-badge-icon {
            font-size: 60px;
            color: #dc3545;
            line-height: 1;
            margin-bottom: 16px;
            display: inline-block;
        }
        .verify-badge-title {
            font-size: 22px;
            font-weight: 800;
            color: #dc3545;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .verify-badge-subtitle {
            font-size: 11px;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 6px 0 0 0;
        }
        .verify-desc {
            font-size: 14px;
            line-height: 1.65;
            color: #495057;
            margin: 25px 0 35px 0;
        }
        .verify-details-box {
            background-color: #fff5f5;
            border: 1px solid #f5c2c2;
            border-radius: 6px;
            padding: 16px;
            font-size: 12.5px;
            color: #c92a2a;
            margin-bottom: 30px;
            text-align: left;
            line-height: 1.6;
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
    </style>
</head>
<body>

<div class="verify-card">
    <span class="verify-badge-icon"><i class="bi bi-x-circle-fill"></i></span>
    <h1 class="verify-badge-title">Verification Failed</h1>
    <p class="verify-badge-subtitle">City of San Jose del Monte • Sangguniang Panlungsod</p>

    <div class="verify-desc">
        Hindi mapatunayan ang kredensyal o pagkakakilanlan ng dokumentong ito sa aming database. Ang rekord na iyong hinahanap ay maaaring hindi pa opisyal na nai-publish o binago.
    </div>

    <div class="verify-details-box">
        <strong>Mga Posibleng Dahilan:</strong>
        <ul style="margin: 6px 0 0 0; padding-left: 20px;">
            <li>Maling verification link o corrupted QR code.</li>
            <li>Ang ordinansa/resolusyon ay nasa <em>Draft</em> o <em>Under Review</em> status pa lamang at hindi pa pampubliko.</li>
            <li>Inalis o in-archive na ang orihinal na dokumento.</li>
        </ul>
    </div>

    <div>
        <a href="<?= APP_URL ?>/portal" class="btn-verify-primary">
            <i class="bi bi-arrow-left"></i> Back to Public Registry
        </a>
    </div>
</div>

</body>
</html>
