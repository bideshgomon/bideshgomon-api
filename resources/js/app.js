import './bootstrap'; // <-- MUST BE FIRST
import '../css/app.css';
import '../css/custom-style.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// 1. Import the Vue plugin (THIS IS THE CORRECTED PATH)
import { ZiggyVue } from 'ziggy-js'; 

// 2. Import the generated routes file (This was already correct)
import { Ziggy } from './ziggy.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            // 3. Use the plugin AND pass the routes to it
            .use(ZiggyVue, Ziggy) 
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});