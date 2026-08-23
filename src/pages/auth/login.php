<?php
/**
 * ORLMS - Login Page (Tailwind CSS)
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

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#0C2340', // CSJDM Deep Blue
                            dark: '#08182c',
                            light: '#1e3a5f',
                        },
                        accent: {
                            DEFAULT: '#F2A900', // CSJDM Gold
                            dark: '#cc8f00',
                            light: '#ffd266',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background: linear-gradient(135deg, rgba(12, 35, 64, 0.45) 0%, rgba(8, 24, 44, 0.7) 100%), 
                        url('<?= APP_URL ?>/public/img/csjdm_cityhall.webp') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 font-sans antialiased">

<div class="w-full max-w-[440px] bg-primary/20 backdrop-blur-md border border-white/20 rounded-xl shadow-[0_20px_45px_rgba(0,0,0,0.35)] text-white overflow-hidden flex flex-col">

    <!-- ── Header ──────────────────────────────────── -->
    <div class="border-b border-white/10 p-[35px_30px_25px_30px] text-center">
        <!-- Official Seal Image of CSJDM LGU -->
        <img class="mx-auto object-contain mb-3" src="<?= APP_URL ?>/public/img/csjdm_logo.webp" alt="CSJDM Logo" width="60" height="60">
        <h1 class="text-white text-base font-bold mt-3 leading-relaxed tracking-tight">Ordinance and Resolution<br>Lifecycle Management System</h1>
        <p class="text-accent text-[11px] font-bold uppercase tracking-wider mt-1.5">Authorized Personnel Only</p>
    </div>

    <!-- ── Body: Login Form ────────────────────────── -->
    <div class="p-[30px]">

        <!-- Session Flash Message -->
        <?php if (isset($_SESSION['flash'])): ?>
            <?php
                $flash     = $_SESSION['flash'];
                $flashType = htmlspecialchars($flash['type'] ?? 'info');
                $flashMsg  = htmlspecialchars($flash['message'] ?? '');
                
                $flashClass = $flashType === 'error' 
                    ? 'bg-rose-500/10 border border-rose-500/30 text-rose-300' 
                    : 'bg-sky-500/10 border border-sky-500/30 text-sky-300';
                unset($_SESSION['flash']);
            ?>
            <div class="p-3 rounded text-sm mb-4 <?= $flashClass ?>" role="alert">
                <?= $flashMsg ?>
            </div>
        <?php endif; ?>

        <!-- Error message (if login failed) -->
        <?php if (!empty($error)): ?>
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 p-3 rounded text-sm mb-4 flex items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= APP_ROOT_URL ?>/auth/login"
              method="POST"
              id="login-form"
              novalidate>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-white/60 font-semibold text-xs mb-1.5">
                    Email Address
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       class="w-full bg-white/95 border border-white/20 rounded px-3.5 py-2.5 text-[13.5px] text-slate-800 transition duration-150 focus:outline-none focus:border-accent focus:ring-3 focus:ring-accent/30 placeholder-slate-400"
                       placeholder="user@csjdm.gov.ph"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required
                       autocomplete="email"
                       autofocus>
            </div>

            <!-- Password -->
            <div class="mb-5">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-white/60 font-semibold text-xs">
                        Password
                    </label>
                    <a href="<?= APP_ROOT_URL ?>/auth/forgot_password" class="text-accent hover:underline text-[11px] font-semibold">
                        Forgot Password?
                    </a>
                </div>
                <input type="password"
                       id="password"
                       name="password"
                       class="w-full bg-white/95 border border-white/20 rounded px-3.5 py-2.5 text-[13.5px] text-slate-800 transition duration-150 focus:outline-none focus:border-accent focus:ring-3 focus:ring-accent/30 placeholder-slate-400"
                       placeholder="Enter your password"
                       required
                       autocomplete="current-password">
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full py-2.5 bg-accent hover:bg-accent-dark text-primary font-bold text-sm border border-accent rounded shadow-md hover:shadow-lg transition duration-200"
                    id="btn-login-submit">
                Log In
            </button>

            <!-- Back Link -->
            <div class="text-center mt-4">
                <a href="<?= APP_URL ?>/" class="inline-flex items-center gap-1.5 text-white/80 hover:text-accent font-semibold text-xs transition duration-150">
                    <i class="bi bi-arrow-left-circle-fill"></i> Back to Landing Page
                </a>
            </div>

        </form>

    </div>

    <!-- ── Footer ──────────────────────────────────── -->
    <div class="bg-black/15 border-t border-white/10 text-white/50 text-[10.5px] p-[14px_30px] text-center">
        © 2026 CSJDM Legislative Dept. All rights reserved.
    </div>

</div>

<script>
    // Disable login button on submit to prevent double-click
    document.getElementById('login-form').addEventListener('submit', function () {
        var btn = document.getElementById('btn-login-submit');
        btn.disabled = true;
        btn.textContent = 'Logging in...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
    });
</script>

<!-- ORLMS Global JavaScript & Client Protection Module -->
<script src="<?= APP_URL ?>/public/js/main.js"></script>
</body>
</html>
