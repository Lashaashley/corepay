import jQuery from 'jquery';
import Alpine from 'alpinejs';
import * as Popper from '@popperjs/core';

// Expose jQuery globally BEFORE plugins
window.jQuery = jQuery;
window.$ = jQuery;

const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

if (token) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });
}
// Plugins
import 'bootstrap';
import 'bootstrap-select';
import DataTable from 'datatables.net';

// Popper
window.Popper = Popper;

// Global App config (CSP-safe)
const el = document.getElementById('appConfig');

if (el) {
    try {
        window.App = window.App || {};
        window.App.routes = JSON.parse(el.dataset.routes);
    } catch (e) {
        console.error('Failed to parse routes JSON', e);
    }
} else {
    console.warn('appConfig not found');
}

// Alpine
window.Alpine = Alpine;
Alpine.start();