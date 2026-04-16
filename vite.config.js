import { defineConfig, loadEnv } from 'vite';  // ← add loadEnv
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig(({ mode }) => {  // ← wrap in function
    const env = loadEnv(mode, process.cwd(), '');
    
    // Extract path from APP_URL for local, use /build/ for production
    const appUrl = env.APP_URL || 'http://localhost';
    const urlPath = new URL(appUrl).pathname;  // → '/dashboard/corepay/public'
    const base = urlPath.endsWith('/') ? `${urlPath}build/` : `${urlPath}/build/`;

    return {
        base,  // → '/dashboard/corepay/public/build/' locally, '/build/' on real server
        plugins: [
            laravel({
                input: ['resources/css/app.scss', 'resources/js/app.js', 'resources/src/plugins/jquery/jquery.min.js',
                 'resources/css/icon-font.min.css',
                 'resources/css/style.css',
                  'resources/css/pages/login.css',
                  'resources/css/pages/2fasetup.css',
                  'resources/css/pages/2faverify.css',
                  'resources/css/pages/passexp.css',
                  'resources/css/pages/leftsidebar.css',
                  'resources/css/pages/navbar.css',
                  'resources/css/pages/edit.css',
                  'resources/css/pages/aimport.css',
                  'resources/css/pages/amanage.css',
                  'resources/css/pages/analytics.css',
                  'resources/css/pages/areport.css',
                  'resources/css/pages/auditpdf.css',
                  'resources/css/pages/closep.css',
                  'resources/css/pages/massign.css',
                  'resources/css/pages/mngprol.css',
                  'resources/css/pages/musers.css',
                  'resources/css/pages/nagent.css',
                  'resources/css/pages/newuser.css',
                  'resources/css/pages/papprove.css',
                  'resources/css/pages/payimport.css',
                  'resources/css/pages/pitems.css',
                  'resources/css/pages/preports.css',
                  'resources/css/pages/rapprove.css',
                  'resources/css/pages/ritems.css',
                  'resources/css/pages/roles.css',
                  'resources/css/pages/static.css',
                  'resources/css/pages/vaudit.css',
                  'resources/css/pages/dashboard.css',
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
                '@fonts': path.resolve(__dirname, 'resources/fonts'),
                '@vendor': path.resolve(__dirname, 'public/vendors'),
                '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
                '~popper.js': '/node_modules/@popperjs/core'
            }
        },
        build: {
            rollupOptions: {
                output: {
                    assetFileNames: 'assets/[name]-[hash][extname]'
                }
            }
        }
    };
});