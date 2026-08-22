<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | <?= APP_SHORT ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#1a3a5c', dark: '#122840' },
                        accent: { DEFAULT: '#c9a84c', dark: '#a8873a' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200 text-center p-8">
        <div class="w-20 h-20 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        
        <h1 class="text-6xl font-black text-primary mb-2">404</h1>
        <h2 class="text-xl font-bold text-gray-800 mb-3">Page Not Found</h2>
        <p class="text-sm text-gray-600 mb-8 leading-relaxed">
            The requested page could not be located or may have been moved. Please check the URL or return to the system dashboard.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="<?= APP_ROOT_URL ?>/dashboard" 
               class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg shadow transition-colors duration-150 flex items-center justify-center gap-2">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
            <a href="<?= APP_ROOT_URL ?>/portal" 
               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors duration-150 flex items-center justify-center gap-2">
                <i class="bi bi-search"></i> Public Portal
            </a>
        </div>
    </div>
</body>
</html>
