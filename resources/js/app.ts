import { createInertiaApp } from '@inertiajs/vue3';
import { MotionPlugin } from '@vueuse/motion';
import PrimeVue from 'primevue/config';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useAppearance';
import AdminLayout from '@/layouts/AdminLayout.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return PublicLayout;
            case name === 'Browse':
                return PublicLayout;
            case name.startsWith('lawyers/'):
                return PublicLayout; // Public details and edit profile views use PublicLayout
            case name.startsWith('cases/'):
                return PublicLayout; // Public case details uses PublicLayout
            case name.startsWith('admin/'):
                return AdminLayout; // Admin dashboard and pages use AdminLayout
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [PublicLayout, SettingsLayout];
            default:
                return PublicLayout;
        }
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue)
            .use(MotionPlugin);

        if (el) {
            app.mount(el);
        }

        return app;
    },
    progress: {
        color: '#4B5563',
    },
});

if (typeof window !== 'undefined') {
    // This will set light / dark mode on page load...
    initializeTheme();

    // This will listen for flash toast data from the server...
    initializeFlashToast();
}
