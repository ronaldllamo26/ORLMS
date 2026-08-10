<?php
/**
 * ORLMS - Login Page
 *
 * Standalone page — rendered without the main layout.
 * Called via AuthController::login() with useLayout = false.
 * Styled with official CSJDM colors, Government Center background photo, and glassmorphic form panel.
 *
 * Variables passed from AuthController:
 *   $error  — error message string (if login failed)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login — Ordinance and Resolution Lifecycle Management System">
    <meta name="robots" content="noindex, nofollow">

    <title>Login | <?= APP_SHORT ?></title>

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
            font-size: 13.5px;
            width: 100%;
            color: #1e293b;
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

        .btn-login:disabled {
            background-color: rgba(242, 169, 0, 0.5);
            border-color: rgba(242, 169, 0, 0.5);
            color: rgba(12, 35, 64, 0.6);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .login-error {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .back-link {
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            opacity: 0.85;
            transition: opacity 0.2s, color 0.2s;
        }
        .back-link:hover {
            opacity: 1;
            color: var(--color-lgu-gold);
        }

        .login-footer {
            background-color: rgba(0, 0, 0, 0.15) !important;
            border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: rgba(255, 255, 255, 0.6) !important;
            font-size: 10.5px;
            padding: 14px 30px;
            text-align: center;
            border-radius: 0 0 12px 12px;
        }
    </style>
</head>
<body>

<div class="login-box">

    <!-- ── Header ──────────────────────────────────── -->
    <div class="login-header">
        <!-- Official Seal Image of CSJDM LGU -->
        <img src="<?= APP_URL ?>/public/img/csjdm_logo.webp" alt="CSJDM Logo" width="60" height="60">
        <h1>Ordinance and Resolution<br>Lifecycle Management System</h1>
        <p>Authorized Personnel Only</p>
    </div>

    <!-- ── Body: Login Form ────────────────────────── -->
    <div class="login-body">

        <!-- Session Flash Message -->
        <?php if (isset($_SESSION['flash'])): ?>
            <?php
                $flash     = $_SESSION['flash'];
                $flashType = htmlspecialchars($flash['type'] ?? 'info');
                $flashMsg  = htmlspecialchars($flash['message'] ?? '');
                
                $flashClass = $flashType === 'error' ? 'login-error' : 'alert alert-info py-2';
                unset($_SESSION['flash']);
            ?>
            <div class="<?= $flashClass ?>" role="alert" style="font-size: 13px; margin-bottom: 16px;">
                <?= $flashMsg ?>
            </div>
        <?php endif; ?>

        <!-- Error message (if login failed) -->
        <?php if (!empty($error)): ?>
            <div class="login-error" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= APP_ROOT_URL ?>/auth/login"
              method="POST"
              id="login-form"
              novalidate>

            <!-- Email -->
            <div class="form-group mb-3">
                <label for="email" class="form-label text-white-50 fw-semibold" style="font-size:12.5px; margin-bottom:6px; display:block;">
                    Email Address
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control-login"
                       placeholder="user@csjdm.gov.ph"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required
                       autocomplete="email"
                       autofocus>
            </div>

            <!-- Password -->
            <div class="form-group mb-4">
                <label for="password" class="form-label text-white-50 fw-semibold" style="font-size:12.5px; margin-bottom:6px; display:block;">
                    Password
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control-login"
                       placeholder="Enter your password"
                       required
                       autocomplete="current-password">
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="btn-login"
                    id="btn-login-submit">
                Log In
            </button>

            <!-- Back Link -->
            <div class="text-center mt-3">
                <a href="<?= APP_URL ?>/" class="back-link d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left-circle-fill"></i> Back to Landing Page
                </a>
            </div>

        </form>

    </div>

    <!-- ── Footer ──────────────────────────────────── -->
    <div class="login-footer">
        © 2026 CSJDM Legislative Dept. All rights reserved.
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Disable login button on submit to prevent double-click
    document.getElementById('login-form').addEventListener('submit', function () {
        var btn = document.getElementById('btn-login-submit');
        btn.disabled = true;
        btn.textContent = 'Logging in...';
    });
</script>

</body>
</html>
