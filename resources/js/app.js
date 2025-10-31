// FIXED: This line must be first to configure Axios
import './bootstrap'; 

import '../css/app.css';
import '../css/custom-style.css'; // Added your custom styles

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'BideshGomon';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue) // Changed from .use(ZiggyVue, Ziggy) to just ZiggyVue
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});