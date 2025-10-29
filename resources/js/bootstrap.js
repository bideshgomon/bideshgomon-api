import axios from 'axios';

window.axios = axios;

/**
 * ---------------------------------------------------------------
 * LARAVEL + SANCTUM AXIOS CONFIGURATION
 * ---------------------------------------------------------------
 *
 * This configuration ensures:
 *  - Proper base URL for all API requests.
 *  - Automatic inclusion of CSRF and XSRF tokens.
 *  - Cookie-based authentication (withCredentials).
 *  - AJAX request header recognition by Laravel.
 * ---------------------------------------------------------------
 */

// Base URL for all Axios requests (auto matches app URL)
window.axios.defaults.baseURL = window.location.origin;

// Let Laravel know it's an AJAX request
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Enable cookies and auth headers for Sanctum
window.axios.defaults.withCredentials = true;

// Automatically send CSRF token (from meta tag)
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error(
        '❌ CSRF token not found. Make sure your layout includes: <meta name="csrf-token" content="{{ csrf_token() }}">\nDocs: https://laravel.com/docs/csrf#csrf-x-csrf-token'
    );
}

/**
 * ---------------------------------------------------------------
 * OPTIONAL: LARAVEL ECHO CONFIGURATION (Uncomment when needed)
 * ---------------------------------------------------------------
 *
 * import Echo from 'laravel-echo';
 * import Pusher from 'pusher-js';
 * window.Pusher = Pusher;
 *
 * window.Echo = new Echo({
 *     broadcaster: 'pusher',
 *     key: import.meta.env.VITE_PUSHER_APP_KEY,
 *     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
 *     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
 *     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
 *     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
 *     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
 *     enabledTransports: ['ws', 'wss'],
 * });
 */

