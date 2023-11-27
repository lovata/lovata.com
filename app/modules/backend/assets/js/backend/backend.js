/*
 * October General Utilities
 */

// Security helper
// Prevents front end service workers from leaking in to the backend
//
function unregisterServiceWorkers() {
    if (location.protocol === 'https:') {
        navigator.serviceWorker.getRegistrations().then(
            function(registrations) {
                for (var index=0; index<registrations.length; index++) {
                    registrations[index].unregister({ immediate: true })
                }
            }
        );
    }
}

// Persist site selection
//
addEventListener('ajax:setup', function(event) {
    var siteId = $('meta[name="backend-site"]').attr('content');
    if (siteId) {
        var options = event.detail.context.options;
        if (!options.headers) {
            options.headers = {};
        }
        options.headers['X-SITE-ID'] = siteId;
    }
});

// Path helpers
//
if ($.oc === undefined) {
    $.oc = {};
}

$.oc.backendUrl = function(url) {
    var backendBasePath = $('meta[name="backend-base-path"]').attr('content');

    if (!backendBasePath) {
        return url;
    }

    if (url.substr(0, 1) == '/') {
        url = url.substr(1);
    }

    return backendBasePath + '/' + url;
}

$.oc.backendCalculateTopContainerOffset = function() {
    var height = $('#layout-mainmenu > .main-menu-container').outerHeight();

    if ($('#layout-banner-area').length) {
        height += $('#layout-banner-area').outerHeight();
    }

    return height;
}

// String escape
//
$.oc.escapeHtmlString = function(string) {
    var htmlEscapes = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#x27;',
            '/': '&#x2F;'
        },
        htmlEscaper = /[&<>"'\/]/g

    return ('' + string).replace(htmlEscaper, function(match) {
        return htmlEscapes[match];
    });
}

// Touch Detection
//
$.oc.isTouchEnabled = function() {
    return document.documentElement.classList &&
        document.documentElement.classList.contains('user-touch');
}

;(function() {
    // Look if user is touching, not if device is capable
    window.addEventListener('touchstart', function onFirstTouch() {
        document.documentElement.classList.add('user-touch');
        Cookies.set('oc-user-touch', 1, { expires: 365, path: '/' });
    }, { once: true });

    // Cookie is found on a non-touch device (cookie was from debugging)
    if ($.oc.isTouchEnabled() && !isTouchEnabledBrowser()) {
        document.documentElement.classList.remove('user-touch');
        Cookies.remove('oc-user-touch', { path: '/' });
    }

    // Private
    function isTouchEnabledBrowser() {
        return ('ontouchstart' in window) ||
            (navigator.maxTouchPoints > 0) ||
            (navigator.msMaxTouchPoints > 0);
    }
})();

// Color Modes
//
$.oc.setColorModeTheme = function(theme) {
    if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.setAttribute('data-bs-theme', 'dark');
    }
    else {
        document.documentElement.setAttribute('data-bs-theme', theme);
    }
}

;(function() {
    if (document.documentElement.classList.contains('color-mode-auto')) {
        var current = document.documentElement.getAttribute('data-bs-theme'),
            preferred = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

        if (current === 'auto' || preferred !== Cookies.get('admin_color_mode')) {
            Cookies.set('admin_color_mode', preferred, { expires: 365, path: '/' });
            $.oc.setColorModeTheme(preferred);
        }
    }

    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        addEventListener('render', function() {
            const darkFavicon = document.querySelector('head > link[data-favicon-dark]');
            if (darkFavicon) {
                darkFavicon.href = darkFavicon.dataset.faviconDark;
            }
        });
    }
})();
