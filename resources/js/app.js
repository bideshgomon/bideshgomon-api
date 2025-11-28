import '../css/app.css';
import '../css/performance.css';
import 'flag-icons/css/flag-icons.min.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Import PWA Manager
import { pwa } from './pwa';

// Import performance utilities
import { clearExpiredCache } from './utils/performance';

// Import lazy load directives
import { lazyLoadDirective, lazyLoadBgDirective } from './directives/lazyLoad';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Clear expired cache on app start
clearExpiredCache();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);
        
        // Register global directives
        app.directive('lazy', lazyLoadDirective);
        app.directive('lazy-bg', lazyLoadBgDirective);
        
        return app.mount(el);
    },
    progress: {
        color: '#4F46E5',
        showSpinner: true,
    },
});
