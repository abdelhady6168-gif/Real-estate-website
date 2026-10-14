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
                bunny('Playfair Display', {
                    weights: [500, 600, 700],
                    styles: ['normal', 'italic'],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        // Advertise the dev server as "localhost" rather than whatever raw
        // address it binds to (on Windows usually "[::1]"): the
        // Content-Security-Policy allows the dev server's origin while
        // `npm run dev` runs (see App\Http\Middleware\AddSecurityHeaders),
        // and CSP source lists can't express IPv6 literals like [::1].
        host: 'localhost',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
