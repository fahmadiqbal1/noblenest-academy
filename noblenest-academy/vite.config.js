import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        ViteImageOptimizer({
            png:  { quality: 85 },
            jpg:  { quality: 85 },
            jpeg: { quality: 85 },
            webp: { lossless: false, quality: 85 },
            svg:  { multipass: true, plugins: [{ name: 'preset-default' }] },
        }),
    ],
});
