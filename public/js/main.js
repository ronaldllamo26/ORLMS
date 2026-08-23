/**
 * ORLMS - Global JavaScript & Client Protection Module
 * Handles global UI behaviors and client-side anti-tampering protection.
 */

(function () {
    'use strict';

    // ─────────────────────────────────────────────────────────────────────────
    // 1. FLASH MESSAGE AUTO-DISMISS
    // ─────────────────────────────────────────────────────────────────────────
    var flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(function () {
            flash.style.transition = 'opacity 0.4s ease';
            flash.style.opacity = '0';
            setTimeout(function () { flash.remove(); }, 400);
        }, 4000);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. CLIENT-SIDE UI ANTI-TAMPERING & INSPECT ELEMENT PROTECTION MODULE
    // ─────────────────────────────────────────────────────────────────────────
    
    // Toast notification helper for blocked inspect events
    function showSecurityToast(message) {
        var existingToast = document.getElementById('orlms-sec-toast');
        if (existingToast) existingToast.remove();

        var toast = document.createElement('div');
        toast.id = 'orlms-sec-toast';
        toast.className = 'fixed bottom-5 right-5 z-[99999] bg-slate-900 text-white text-xs font-semibold px-4 py-3 rounded-lg shadow-2xl border border-slate-700 flex items-center gap-2 animate-bounce';
        toast.innerHTML = '<svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> <span>' + message + '</span>';
        
        document.body.appendChild(toast);
        
        setTimeout(function () {
            toast.style.transition = 'opacity 0.5s ease';
            toast.style.opacity = '0';
            setTimeout(function () { toast.remove(); }, 500);
        }, 2500);
    }

    // Prevent Right-Click Context Menu
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
        showSecurityToast('Security Notice: Right-click context menu is disabled.');
        return false;
    });

    // Prevent Keyboard Inspection Shortcuts (F12, Ctrl+Shift+I/J/C, Ctrl+U)
    document.addEventListener('keydown', function (e) {
        // F12 Key
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
            showSecurityToast('Security Notice: F12 Developer Tools shortcut is disabled.');
            return false;
        }

        // Ctrl + Shift + I (Inspect) | Ctrl + Shift + J (Console) | Ctrl + Shift + C (Inspect Element)
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c' || e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) {
            e.preventDefault();
            showSecurityToast('Security Notice: Inspect Element shortcut is disabled.');
            return false;
        }

        // Ctrl + U (View Page Source)
        if (e.ctrlKey && (e.key === 'U' || e.key === 'u' || e.keyCode === 85)) {
            e.preventDefault();
            showSecurityToast('Security Notice: View Page Source is disabled.');
            return false;
        }
    });

})();
