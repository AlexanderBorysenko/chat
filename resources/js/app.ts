import '../css/style.scss';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

import Pusher from 'pusher-js';
window.Pusher = Pusher;
import Echo from 'laravel-echo';
window.Echo = new Echo({
	broadcaster: 'pusher',
	key: import.meta.env.VITE_PUSHER_APP_KEY,
	cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
	forceTLS: true
});

createInertiaApp({
	title: title => `${title} - ${appName}`,
	resolve: name =>
		resolvePageComponent(
			`./Pages/${name}.vue`,
			import.meta.glob<DefineComponent>('./Pages/**/*.vue')
		),
	setup({ el, App, props, plugin }) {
		createApp({ render: () => h(App, props) })
			.use(plugin)
			.use(ZiggyVue, Ziggy)
			.mount(el);
	},
	progress: {
		color: '#4B5563'
	}
});
