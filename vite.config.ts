import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';

export default defineConfig(({ command }) => ({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        inertia(),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // Wayfinder shells out to `php artisan wayfinder:generate`, so it only
        // runs during local dev where PHP is available. The generated files in
        // resources/js/{actions,routes,wayfinder} are committed so production
        // builds (e.g. DigitalOcean App Platform, which has no PHP in the Node
        // build step) consume them directly without regenerating.
        ...(command === 'serve' ? [wayfinder({ formVariants: true })] : []),
    ],
}));
