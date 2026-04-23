// ✅ CORRECT order — globals FIRST, then everything else
import jQuery from 'jquery';
import * as Popper from '@popperjs/core';
import * as bootstrap from 'bootstrap';
import 'bootstrap';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'sweetalert2/dist/sweetalert2.min.css';
import Swal from 'sweetalert2';
import Alpine from 'alpinejs';
import select2 from 'select2';
import Highcharts from 'highcharts';
import Choices from 'choices.js';              
import 'choices.js/public/assets/styles/choices.min.css';


// ✅ Set ALL globals immediately — Vite hoists imports so these
// run as soon as the module executes
window.jQuery  = jQuery;
window.$       = jQuery;
window.Popper  = Popper;
window.bootstrap = bootstrap;
window.DataTable = DataTable;
$.fn.dataTable   = DataTable;
window.Highcharts = Highcharts;
window.Choices   = Choices;  

select2(window);

// CSRF
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    jQuery.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': token }
    });
}


window.Swal = Swal.mixin({
    customClass: {},
    
});

// Bootstrap modal helpers
window.showModal = function (id) {
    const el = document.getElementById(id);
    if (!el) return console.error(`Modal #${id} not found`);
    (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
};
window.hideModal = function (id) {
    const el = document.getElementById(id);
    if (!el) return console.error(`Modal #${id} not found`);
    bootstrap.Modal.getInstance(el)?.hide();
};

// App routes
window.App = window.App || {};
function initAppRoutes() {
    const el = document.getElementById('appConfig');
    if (el) {
        try {
            window.App.routes = JSON.parse(el.dataset.routes);
        } catch (e) {
            window.App.routes = {};
        }
    } else {
        window.App.routes = {};
    }
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

// Alpine last — it scans the DOM on start
window.Alpine = Alpine;
Alpine.start();