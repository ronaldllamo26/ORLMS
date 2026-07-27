/**
 * ORLMS - Global JavaScript
 * Handles global UI behaviors across all pages.
 */

(function () {
    'use strict';

    // Auto-dismiss flash messages after 4 seconds (backup for inline script)
    var flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(function () {
            flash.style.transition = 'opacity 0.4s ease';
            flash.style.opacity = '0';
            setTimeout(function () { flash.remove(); }, 400);
        }, 4000);
    }

})();
