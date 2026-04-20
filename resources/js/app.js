import jQuery from 'jquery';
import Alpine from 'alpinejs';
import * as Popper from '@popperjs/core';

import 'bootstrap';
import * as bootstrap from 'bootstrap';
import 'bootstrap-select';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import Swal from 'sweetalert2';

// Expose jQuery globally BEFORE plugins
window.jQuery = jQuery;
window.$ = jQuery;
window.Popper = Popper;
window.bootstrap = bootstrap;
window.DataTable = DataTable;
$.fn.dataTable = DataTable;
const cspNonce = document.querySelector('meta[name="csp-nonce"]')?.content ?? '';
window.Swal = Swal.mixin({
    customClass: {},
    ...(cspNonce && { cspNonce }),   // only add if nonce exists
});

window.showModal = function(id) {
    const el = document.getElementById(id);
    if (!el) return console.error(`Modal #${id} not found`);
    (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
};

window.hideModal = function(id) {
    const el = document.getElementById(id);
    if (!el) return console.error(`Modal #${id} not found`);
    const m = bootstrap.Modal.getInstance(el);
    if (m) m.hide();
};

import select2 from 'select2';
select2(window);


const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

if (token) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });
}


// Global App config (CSP-safe)
window.App = window.App || {};

function initAppRoutes() {
    const el = document.getElementById('appConfig');
    if (el) {
        try {
            window.App.routes = JSON.parse(el.dataset.routes);
        } catch (e) {
            console.error('Failed to parse routes JSON', e);
            window.App.routes = {};
        }
    } else {
        console.warn('appConfig element not found — App.routes will be empty');
        window.App.routes = {};
    }

    // Safe getter — use App.route('loadpriori') anywhere
    window.App.route = function (key) {
        const url = window.App.routes[key];
        if (!url) console.error(`App.route: unknown key "${key}"`);
        return url;
    };
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAppRoutes);
} else {
    initAppRoutes();
}

// Alpine
window.Alpine = Alpine;
Alpine.start();