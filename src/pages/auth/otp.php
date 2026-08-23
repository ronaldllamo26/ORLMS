<?php
/**
 * ORLMS - Multi-Factor Authentication (OTP) Page
 *
 * Standalone page — rendered without the main layout.
 * Called via AuthController::otp() with useLayout = false.
 * Styled with official CSJDM colors, City Hall background, and glassmorphic card panel.
 *
 * Variables passed from AuthController:
 *   $error  — error message string (if verification failed)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Multi-Factor Verification — Sangguniang Panlungsod CSJDM">
    <meta name="robots" content="noindex, nofollow">

    <title>MFA Verification | <?= APP_SHORT ?></title>

    <?php $gaCode = (defined('GA_TRACKING_ID') && !empty(trim(GA_TRACKING_ID))) ? trim(GA_TRACKING_ID) : 'G-M0BYB5DP6M'; ?>
    <!-- Google Analytics (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $gaCode ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= $gaCode ?>');
    </script>

    <?php if (defined('CLARITY_PROJECT_ID') && !empty(CLARITY_PROJECT_ID)): ?>
    <!-- Microsoft Clarity -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "<?= CLARITY_PROJECT_ID ?>");
    </script>
    <?php endif; ?>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- ORLMS Global Stylesheet -->
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">

    <style>
        :root {
            --color-lgu-blue: #0C2340;
            --color-lgu-gold: #F2A900;
            --color-lgu-sky: #0084FF;
        }

        body {
            background: linear-gradient(135deg, rgba(12, 35, 64, 0.45) 0%, rgba(8, 24, 44, 0.7) 100%), 
                        url('<?= APP_URL ?>/public/img/csjdm_cityhall.webp') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 16px;
            font-family: 'Inter', sans-serif;
        }

        .login-box {
            background: rgba(12, 35, 64, 0.15) !important; /* Premium Transparent LGU Blue */
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 12px !important;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35) !important;
            width: 100%;
            max-width: 440px;
            color: #ffffff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .login-header {
            background-color: transparent !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 35px 30px 25px 30px;
            text-align: center;
        }

        .login-header img {
            object-fit: contain;
            margin-bottom: 10px;
        }

        .login-header h1 {
            color: #ffffff !important;
            font-size: 15px !important;
            font-weight: 700;
            margin-top: 12px;
            line-height: 1.4;
            letter-spacing: -0.3px;
        }

        .login-header p {
            color: var(--color-lgu-gold) !important;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 6px 0 0 0;
        }

        .login-body {
            padding: 30px;
        }

        .form-control-login {
            background-color: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            padding: 11px 14px;
            font-size: 16px;
            width: 100%;
            color: #1e293b;
            text-align: center;
            letter-spacing: 6px;
            font-weight: 800;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control-login:focus {
            outline: none;
            border-color: var(--color-lgu-gold);
            box-shadow: 0 0 0 3px rgba(242, 169, 0, 0.3);
            background-color: #ffffff;
        }

        .btn-login {
            width: 100%;
            padding: 11px;
            background-color: var(--color-lgu-gold);
            color: var(--color-lgu-blue);
            border: 1px solid var(--color-lgu-gold);
            border-radius: 4px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 8px rgba(242, 169, 0, 0.15);
        }

        .btn-login:hover {
            background-color: #cc8f00;
            border-color: #cc8f00;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(242, 169, 0, 0.25);
        }

        .error-alert {
            background-color: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: 4px;
            color: #ea868f;
            font-size: 12.5px;
            padding: 10px 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .success-alert {
            background-color: rgba(25, 135, 84, 0.15);
            border: 1px solid rgba(25, 135, 84, 0.3);
            border-radius: 4px;
            color: #75b798;
            font-size: 12.5px;
            padding: 10px 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <!-- Header -->
    <div class="login-header">
        <img src="<?= APP_URL ?>/public/img/csjdm_logo.webp" alt="CSJDM Logo" width="65" height="65">
        <h1>SANGGUNIANG PANLUNGSOD</h1>
        <p>MFA Verification</p>
    </div>

    <!-- Body -->
    <div class="login-body">
        
        <!-- Flash Success Notification (if resent) -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="success-alert">
                <i class="bi bi-check-circle-fill"></i>
                <div><?= htmlspecialchars($_SESSION['flash']['message']) ?></div>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
            <div class="error-alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <!-- Simulated OTP Code Banner or Live Email Notification -->
        <?php if (!MFA_SMTP_ENABLE): ?>
            <div style="background-color: rgba(242, 169, 0, 0.15); border: 1px dashed var(--color-lgu-gold); border-radius: 6px; padding: 12px; margin-bottom: 24px; font-size: 11.5px; color: var(--color-lgu-gold); text-align: center; line-height: 1.5;">
                <i class="bi bi-shield-lock-fill me-1"></i> <strong>[DEMO MODE]</strong> Simulated Verification Code:<br>
                <span style="font-size: 20px; font-weight: 800; letter-spacing: 3px; display: block; margin-top: 4px; color: #ffffff;">
                    <?= $_SESSION['otp_code'] ?>
                </span>
            </div>
        <?php else: ?>
            <div style="background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 6px; padding: 12px; margin-bottom: 24px; font-size: 12px; color: #ffffff; text-align: center; line-height: 1.5;">
                <i class="bi bi-envelope-fill me-1" style="color: var(--color-lgu-gold);"></i> Nagpadala kami ng 6-digit verification code sa iyong rehistradong email. Pakisuri ang iyong **Inbox / Spam folder**.
            </div>
        <?php endif; ?>

        <form action="<?= APP_URL ?>/auth/otp" method="POST" autocomplete="off">
            <div class="mb-4">
                <label for="otp_code" style="display:block; font-size:12px; font-weight:600; text-transform:uppercase; color:rgba(255,255,255,0.7); margin-bottom:8px; text-align:center;">
                    Enter 6-Digit OTP Code
                </label>
                <input type="text" name="otp_code" id="otp_code" class="form-control-login" maxlength="6" autofocus placeholder="------" required>
            </div>

            <button type="submit" class="btn-login mb-3">
                Verify & Login
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 12.5px;">
            <span style="color: rgba(255,255,255,0.6);">Didn't receive the code?</span>
            <a href="<?= APP_URL ?>/auth/resend_otp" style="color: var(--color-lgu-gold); text-decoration: none; font-weight: 700; margin-left: 4px;">
                Resend Code
            </a>
        </div>

        <div style="text-align: center; margin-top: 15px; font-size: 12.5px;">
            <a href="<?= APP_URL ?>/auth/logout" style="color: rgba(255,255,255,0.4); text-decoration: none;">
                Cancel and Logout
            </a>
        </div>

    </div>
</div>

<!-- ORLMS Global JavaScript & Client Protection Module -->
<script src="<?= APP_URL ?>/public/js/main.js"></script>
</body>
</html>
