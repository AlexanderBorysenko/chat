import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import sassGlobImports from 'vite-plugin-sass-glob-import';
import cssPurge from 'vite-plugin-purgecss';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
	plugins: [
		sassGlobImports(),
		cssPurge({
			contentFunction: sourceInputFile => {
				if (vuePath.test(sourceInputFile)) {
					return [sourceInputFile.replace(vuePath, '.vue')];
				}
				return [
					'resources/js/**/*.vue',
					'resources/views/**/*.blade.php'
				];
			},
			defaultExtractor(content) {
				if (content.startsWith('<template')) {
					content = `${content.split('</template')[0]}</template>`;
				}
				return content.match(/[\w-/:]+(?<!:)/g) || [];
			}
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
