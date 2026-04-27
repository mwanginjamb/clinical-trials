/**
 * Clinical Curator — App JS
 * Handles mobile navigation drawer behaviour.
 */

(function () {
    'use strict';

    /**
     * Toggle the mobile sidebar drawer open / closed.
     */
    function toggleMobileMenu() {
        var sidebar = document.getElementById('mobile-sidebar');
        var overlay = document.getElementById('mobile-menu-overlay');

        if (!sidebar || !overlay) return;

        var isOpen = !sidebar.classList.contains('-translate-x-full');

        if (isOpen) {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(function () {
                overlay.classList.add('hidden');
            }, 300);
        } else {
            overlay.classList.remove('hidden');
            // Allow paint before triggering transition
            setTimeout(function () {
                overlay.classList.remove('opacity-0');
            }, 10);
            sidebar.classList.remove('-translate-x-full');
        }
    }

    // Expose globally so inline onclick attributes can call it
    window.toggleMobileMenu = toggleMobileMenu;

    document.addEventListener('DOMContentLoaded', function () {
        // Close drawer when overlay is tapped
        var overlay = document.getElementById('mobile-menu-overlay');
        if (overlay) {
            overlay.addEventListener('click', toggleMobileMenu);
        }

        // Ensure correct state on viewport resize
        window.addEventListener('resize', function () {
            var sidebar = document.getElementById('mobile-sidebar');
            var overlay = document.getElementById('mobile-menu-overlay');
            if (!sidebar || !overlay) return;

            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden', 'opacity-0');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });
    });
}());