import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js', 'resources/src/plugins/jquery/jquery.min.js',
                 'resources/css/icon-font.min.css',
                 'resources/css/style.css',
                'resources/js/script.min.js',
                'resources/js/bootstrap.js',
                'resources/js/show.js',
                'resources/js/process.js'],
            refresh: true,
        }),
    ],
    
    
    resolve: {
        alias: {
            '~': '/resources',
            '@fonts': path.resolve(__dirname, 'public/fonts'),
            '@vendor': path.resolve(__dirname, 'public/vendors'),
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            '~popper.js': '/node_modules/@popperjs/core'
        }
    }
    
});
