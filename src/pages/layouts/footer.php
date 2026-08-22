<?php
/**
 * ORLMS - Footer Component
 */
?>
<footer class="no-print print:hidden bg-white border-t border-gray-200 mt-auto py-4 px-6 text-center text-xs text-gray-500 flex flex-col sm:flex-row items-center justify-between gap-2">
    <div>
        &copy; <?= date('Y') ?> <span class="font-semibold text-primary"><?= APP_NAME ?></span>. All rights reserved.
    </div>
    <div class="flex items-center gap-4 text-gray-400">
        <span>Sangguniang Panlungsod ng CSJDM</span>
        <span>&bull;</span>
        <span>Version <?= APP_VERSION ?></span>
    </div>
</footer>
