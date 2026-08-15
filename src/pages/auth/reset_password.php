<?php
/**
 * ORLMS - Reset Password View (Enter Reset Code & New Password)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reset Password — Ordinance and Resolution Lifecycle Management System">
    <meta name="robots" content="noindex, nofollow">

    <title>Reset Password | <?= APP_SHORT ?></title>

    <?php if (defined('GA_TRACKING_ID') && !empty(GA_TRACKING_ID)): ?>
    <!-- Google Analytics (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= GA_TRACKING_ID ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= GA_TRACKING_ID ?>');
    </script>
    <?php endif; ?>

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#0C2340', dark: '#08182c', light: '#1e3a5f' },
                        accent: { DEFAULT: '#F2A900', dark: '#cc8f00', light: '#ffd266' },
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
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

    <!-- Header -->
    <div class="border-b border-white/10 p-[30px_30px_20px_30px] text-center">
        <img class="mx-auto object-contain mb-3" src="<?= APP_URL ?>/public/img/csjdm_logo.webp" alt="CSJDM Logo" width="55" height="55">
        <h1 class="text-white text-base font-bold tracking-tight">Mag-set ng Bagong Password</h1>
        <p class="text-white/60 text-xs mt-1">Ilagay ang 6-digit Reset Code at ang bagong password.</p>
    </div>

    <!-- Body -->
    <div class="p-[30px]">

        <?php if (!empty($error)): ?>
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 p-3 rounded text-xs mb-4 flex items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= APP_ROOT_URL ?>/auth/reset_password" method="POST" id="reset-form">

            <!-- Reset Code -->
            <div class="mb-4">
                <label for="reset_code" class="block text-white/70 font-semibold text-xs mb-1.5">
                    6-Digit Reset Code (mula sa Email)
                </label>
                <input type="text"
                       id="reset_code"
                       name="reset_code"
                       maxlength="6"
                       class="w-full bg-white/95 border border-white/20 rounded px-3.5 py-2.5 text-[18px] font-bold text-center tracking-[4px] text-slate-800 focus:outline-none focus:border-accent focus:ring-3 focus:ring-accent/30 placeholder-slate-400"
                       placeholder="123456"
                       required
                       autofocus>
            </div>

            <!-- New Password -->
            <div class="mb-4">
                <label for="new_password" class="block text-white/70 font-semibold text-xs mb-1.5">
                    Bagong Password
                </label>
                <input type="password"
                       id="new_password"
                       name="new_password"
                       class="w-full bg-white/95 border border-white/20 rounded px-3.5 py-2.5 text-[13.5px] text-slate-800 focus:outline-none focus:border-accent focus:ring-3 focus:ring-accent/30 placeholder-slate-400"
                       placeholder="Minimum 6 characters"
                       required>
            </div>

            <!-- Confirm Password -->
            <div class="mb-5">
                <label for="confirm_password" class="block text-white/70 font-semibold text-xs mb-1.5">
                    Kumpirmahin ang Bagong Password
                </label>
                <input type="password"
                       id="confirm_password"
                       name="confirm_password"
                       class="w-full bg-white/95 border border-white/20 rounded px-3.5 py-2.5 text-[13.5px] text-slate-800 focus:outline-none focus:border-accent focus:ring-3 focus:ring-accent/30 placeholder-slate-400"
                       placeholder="Ulitin ang bagong password"
                       required>
            </div>

            <button type="submit"
                    class="w-full py-2.5 bg-accent hover:bg-accent-dark text-primary font-bold text-sm border border-accent rounded shadow-md transition duration-200"
                    id="btn-reset-submit">
                I-save ang Bagong Password
            </button>

            <div class="text-center mt-4">
                <a href="<?= APP_ROOT_URL ?>/auth/login" class="inline-flex items-center gap-1.5 text-white/80 hover:text-accent font-semibold text-xs transition duration-150">
                    <i class="bi bi-arrow-left-circle-fill"></i> Bumalik sa Login
                </a>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="bg-black/15 border-t border-white/10 text-white/50 text-[10.5px] p-[14px_30px] text-center">
        © 2026 CSJDM Legislative Dept. All rights reserved.
    </div>

</div>

</body>
</html>
