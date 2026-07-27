<?php
/**
 * ORLMS - Login Page
 *
 * Standalone page — rendered without the main layout.
 * Called via AuthController::login() with useLayout = false.
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
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- ORLMS Global Stylesheet -->
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">

    <style>
        /* Additional login-specific styles */
        .login-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 0;
        }

        .login-footer {
            padding: 14px 30px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 11px;
            color: #6c757d;
            border-radius: 0 0 6px 6px;
        }

        .form-control-login {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            width: 100%;
            color: #1c1c1c;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control-login:focus {
            outline: none;
            border-color: #1a3a5c;
            box-shadow: 0 0 0 3px rgba(26, 58, 92, 0.12);
            background-color: #ffffff;
        }

        .btn-login {
            width: 100%;
            padding: 11px;
            background-color: #1a3a5c;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: background-color 0.15s ease;
        }

        .btn-login:hover {
            background-color: #122840;
        }

        .btn-login:focus {
            outline: 2px solid #c9a84c;
            outline-offset: 2px;
        }

        .login-error {
            background-color: #f8d7da;
            border: 1px solid #f1aeb5;
            color: #842029;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <div class="login-box">

        <!-- ── Header ──────────────────────────────────── -->
        <div class="login-header">
            <div class="login-logo">ORLMS</div>
            <h1>Ordinance and Resolution<br>Lifecycle Management System</h1>
            <p>Authorized Personnel Only</p>
        </div>

        <!-- ── Body: Login Form ────────────────────────── -->
        <div class="login-body">

            <!-- Error message (if login failed) -->
            <?php if (!empty($error)): ?>
                <div class="login-error" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_ROOT_URL ?>/auth/login"
                  method="POST"
                  id="login-form"
                  novalidate>

                <!-- Email -->
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="email"
                           class="form-label"
                           style="font-size:13px; font-weight:500; margin-bottom:6px; display:block;">
                        Email Address
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-control-login"
                           placeholder="user@orlms.gov.ph"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required
                           autocomplete="email"
                           autofocus>
                </div>

                <!-- Password -->
                <div class="form-group" style="margin-bottom: 22px;">
                    <label for="password"
                           class="form-label"
                           style="font-size:13px; font-weight:500; margin-bottom:6px; display:block;">
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

            </form>

        </div>

        <!-- ── Footer ──────────────────────────────────── -->
        <div class="login-footer">
            <?= APP_NAME ?> &mdash; For official use only.
        </div>

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
