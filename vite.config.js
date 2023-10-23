import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import sassGlobImports from 'vite-plugin-sass-glob-import';
import cssPurge from 'vite-plugin-purgecss';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
	plugins: [
		sassGlobImports(),
		cssPurge({
			content: ['resources/**/*.vue', 'resources/views/**/*.blade.php']
		}),
		laravel({
			input: 'resources/js/app.ts',
			refresh: true
		}),
		vue({
			template: {
				transformAssetUrls: {
					base: null,
					includeAbsolute: false
				}
			}
		})
	]
});
