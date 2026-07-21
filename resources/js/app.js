
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;

Alpine.start();

// ─── Toast Notification System ───────────────────────────────────────────────

/**
 * Show a toast notification.
 * @param {string} message - The message to display.
 * @param {'success'|'error'|'warning'|'info'} type - The toast type.
 */
window.showToast = function (message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const icons = {
        success: '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        error: '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        warning: '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>',
        info: '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    };

    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.innerHTML = '<div class="flex items-center gap-3">' +
        (icons[type] || '') +
        '<span class="flex-1">' + message + '</span>' +
        '<button onclick="this.closest(\'.toast\').remove()" class="ml-2 opacity-60 hover:opacity-100 transition-opacity" aria-label="Close">' +
        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>' +
        '</button>' +
        '</div>';

    container.appendChild(toast);

    // Trigger enter animation
    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            toast.classList.add('toast-show');
        });
    });

    // Auto-dismiss after 4 seconds
    setTimeout(function () {
        toast.classList.remove('toast-show');
        setTimeout(function () {
            toast.remove();
        }, 300);
    }, 4000);
};

// ─── Global AJAX Helper ──────────────────────────────────────────────────────

/**
 * Perform an AJAX POST request with automatic toast feedback.
 * @param {string} url - The endpoint URL.
 * @param {object} options - Optional fetch options (body, headers, etc.).
 * @param {function} [callback] - Optional callback receiving the parsed JSON data (or null on error).
 * @returns {Promise<object|null>} The parsed JSON response, or null on error.
 */
window.ajaxPost = async function (url, options = {}, callback) {
    var defaults = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
    };

    var config = Object.assign({}, defaults, options);
    if (options.headers) {
        config.headers = Object.assign({}, defaults.headers, options.headers);
    }

    // If caller passed data as the options object (e.g. { simulation_id: 123 }),
    // serialize it as the request body. Exclude fetch-config keys.
    // NOTE: 'body' is NOT excluded here — callers may pass { body: 'text' }
    // as data (e.g. comments), which must be serialized into JSON.
    var fetchData = {};
    var fetchConfigKeys = ['method', 'headers', 'mode', 'credentials', 'cache', 'redirect', 'referrer', 'referrerPolicy', 'integrity', 'keepalive', 'signal'];
    for (var key in options) {
        if (options.hasOwnProperty(key) && fetchConfigKeys.indexOf(key) === -1) {
            fetchData[key] = options[key];
        }
    }
    if (Object.keys(fetchData).length > 0) {
        config.body = JSON.stringify(fetchData);
    }

    try {
        var response = await fetch(url, config);

        // Handle HTTP error status (401, 403, 404, 419, 500, etc.)
        if (!response.ok) {
            if (response.status === 401 || response.status === 419) {
                window.showToast('Sesi Anda telah berakhir. Silakan login kembali.', 'error');
                setTimeout(function () { window.location.href = '/login'; }, 1500);
            } else {
                window.showToast('Terjadi kesalahan (' + response.status + '). Silakan coba lagi.', 'error');
            }
            if (typeof callback === 'function') {
                callback(null);
            }
            return null;
        }

        // Guard against non-JSON responses (e.g. HTML error/redirect pages)
        var contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            window.showToast('Terjadi kesalahan. Respons server tidak valid.', 'error');
            if (typeof callback === 'function') {
                callback(null);
            }
            return null;
        }

        var data = await response.json();

        if (data.message) {
            window.showToast(data.message, data.success !== false ? 'success' : 'error');
        }

        if (typeof callback === 'function') {
            callback(data);
        }
        return data;
    } catch (error) {
        window.showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
        if (typeof callback === 'function') {
            callback(null);
        }
        return null;
    }
};

// ─── Custom Confirm Modal ──────────────────────────────────────────────────────

/**
 * Show a custom confirm modal (replaces native confirm()).
 * @param {string} message - The confirmation message.
 * @param {object} options - Optional config { title, confirmText, cancelText, confirmClass }.
 * @returns {Promise<boolean>} Resolves true if confirmed, false if cancelled.
 */
window.showConfirm = function (message, options = {}) {
    options = Object.assign({
        title: 'Konfirmasi',
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        confirmClass: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
    }, options);

    return new Promise(function (resolve) {
        var overlay = document.createElement('div');
        overlay.className = 'confirm-modal-overlay';
        overlay.innerHTML =
            '<div class="confirm-modal-card">' +
            '<div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">' +
            '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />' +
            '</svg>' +
            '</div>' +
            '<h3 class="text-lg font-semibold text-gray-900 text-center mb-2">' + options.title + '</h3>' +
            '<p class="text-sm text-gray-500 text-center mb-6">' + message + '</p>' +
            '<div class="flex gap-3">' +
            '<button type="button" class="confirm-modal-cancel flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">' +
            options.cancelText +
            '</button>' +
            '<button type="button" class="confirm-modal-ok flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-lg transition ' + options.confirmClass + '">' +
            options.confirmText +
            '</button>' +
            '</div>' +
            '</div>';

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        // Trigger animation
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                overlay.classList.add('confirm-modal-show');
            });
        });

        function close(result) {
            overlay.classList.remove('confirm-modal-show');
            document.body.style.overflow = '';
            setTimeout(function () {
                overlay.remove();
                resolve(result);
            }, 200);
        }

        overlay.querySelector('.confirm-modal-ok').addEventListener('click', function () {
            close(true);
        });

        overlay.querySelector('.confirm-modal-cancel').addEventListener('click', function () {
            close(false);
        });

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                close(false);
            }
        });

        overlay.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                close(false);
            }
        });
    });
};

/**
 * Submit a form after confirm modal approval.
 * @param {HTMLFormElement} form - The form to submit.
 * @param {string} message - The confirmation message.
 * @param {object} options - Optional config passed to showConfirm.
 */
window.confirmSubmit = function (form, message, options) {
    window.showConfirm(message, options).then(function (confirmed) {
        if (confirmed) {
            form.submit();
        }
    });
};
