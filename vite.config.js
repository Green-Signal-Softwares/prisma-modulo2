import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        // Docker: escuta em todas as interfaces do container
        host: '0.0.0.0',
        port: parseInt(process.env.VITE_PORT ?? 5173),
        strictPort: true,
        // HMR: o browser se conecta pelo host da máquina (não pelo container)
        hmr: {
            host: 'localhost',
            port: parseInt(process.env.VITE_PORT ?? 5173),
        },
        watch: {
            // Evita rebuilds desnecessários dentro do Docker
            usePolling: true,
            interval: 1000,
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
