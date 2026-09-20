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
                       placeholder="6-digit code"
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
                       placeholder="Enter strong password"
                       required>

                <!-- Caps Lock Warning Indicator -->
                <div id="caps-lock-warning" class="hidden mt-2 flex items-center gap-1.5 text-amber-300 text-[11px] font-semibold bg-amber-500/20 border border-amber-500/40 rounded px-2.5 py-1.5 animate-pulse">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>BABALA: Naka-ON ang <strong>Caps Lock</strong></span>
                </div>

                <!-- Password Policy Checklist -->
                <div class="mt-3 bg-white/5 border border-white/10 rounded-lg p-2.5 text-[11px] space-y-1">
                    <div class="text-white/70 font-semibold mb-1">Mga Patakaran sa Seguridad ng Password:</div>
                    <div id="rule-length" class="flex items-center gap-1.5 text-white/40 transition">
                        <i class="bi bi-circle"></i> Hindi bababa sa 8 na karakter
                    </div>
                    <div id="rule-upper" class="flex items-center gap-1.5 text-white/40 transition">
                        <i class="bi bi-circle"></i> May Uppercase / Caps Lock (A-Z)
                    </div>
                    <div id="rule-lower" class="flex items-center gap-1.5 text-white/40 transition">
                        <i class="bi bi-circle"></i> May Lowercase (a-z)
                    </div>
                    <div id="rule-number" class="flex items-center gap-1.5 text-white/40 transition">
                        <i class="bi bi-circle"></i> May Numero (0-9)
                    </div>
                    <div id="rule-special" class="flex items-center gap-1.5 text-white/40 transition">
                        <i class="bi bi-circle"></i> May Special Character (!@#$%^&*)
                    </div>
                </div>
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
                <div id="match-warning" class="hidden mt-1.5 text-rose-300 text-[11px] font-semibold flex items-center gap-1">
                    <i class="bi bi-x-circle-fill"></i> Hindi magkatugma ang password
                </div>
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

<script>
    const newPwd = document.getElementById('new_password');
    const confPwd = document.getElementById('confirm_password');
    const capsWarning = document.getElementById('caps-lock-warning');
    const matchWarning = document.getElementById('match-warning');

    const ruleLength = document.getElementById('rule-length');
    const ruleUpper = document.getElementById('rule-upper');
    const ruleLower = document.getElementById('rule-lower');
    const ruleNumber = document.getElementById('rule-number');
    const ruleSpecial = document.getElementById('rule-special');

    function updateRule(elem, passed) {
        if (!elem) return;
        if (passed) {
            elem.className = 'flex items-center gap-1.5 text-emerald-400 font-medium transition';
            elem.querySelector('i').className = 'bi bi-check-circle-fill text-emerald-400';
        } else {
            elem.className = 'flex items-center gap-1.5 text-white/40 transition';
            elem.querySelector('i').className = 'bi bi-circle';
        }
    }

    if (newPwd) {
        newPwd.addEventListener('input', function() {
            const val = this.value;
            updateRule(ruleLength, val.length >= 8);
            updateRule(ruleUpper, /[A-Z]/.test(val));
            updateRule(ruleLower, /[a-z]/.test(val));
            updateRule(ruleNumber, /[0-9]/.test(val));
            updateRule(ruleSpecial, /[^A-Za-z0-9]/.test(val));
            checkMatch();
        });

        ['keydown', 'keyup'].forEach(evt => {
            newPwd.addEventListener(evt, function(e) {
                if (e.getModifierState && e.getModifierState('CapsLock')) {
                    capsWarning.classList.remove('hidden');
                } else {
                    capsWarning.classList.add('hidden');
                }
            });
        });

        newPwd.addEventListener('blur', function() {
            capsWarning.classList.add('hidden');
        });
    }

    function checkMatch() {
        if (!confPwd || !newPwd) return;
        if (confPwd.value && confPwd.value !== newPwd.value) {
            matchWarning.classList.remove('hidden');
        } else {
            matchWarning.classList.add('hidden');
        }
    }

    if (confPwd) {
        confPwd.addEventListener('input', checkMatch);
    }
</script>

<!-- ORLMS Global JavaScript & Client Protection Module -->
<script src="<?= APP_URL ?>/public/js/main.js"></script>
</body>
</html>
