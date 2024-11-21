import './bootstrap';
import '../css/app.css';
import '../css/style.css';
import '../css/editor.css';
import '../css/admin.css';

import { createApp, h, Transition, TransitionGroup } from 'vue';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { CkeditorPlugin } from '@ckeditor/ckeditor5-vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
window.toast = toast;
import axios from 'axios';
window.axios = axios;
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import { Icon } from '@iconify/vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';

import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);
export default pinia;

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(CkeditorPlugin)
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura
                }
            })
            .component('transition', Transition)
            .component('DataTable', Vue3EasyDataTable)
            .component('Icon', Icon)
            .use(ZiggyVue)
            .use(pinia)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
