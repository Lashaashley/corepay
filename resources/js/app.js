import jQuery from 'jquery';
import Alpine from 'alpinejs';
import * as Popper from '@popperjs/core';
 // Change this line - import the JS, not SCSS
import 'bootstrap-select';
import 'bootstrap'; 
import { initializeStaffReport } from './reports';
//import 'src/plugins/bootstrap-select/dist/js/bootstrap-select.min.js';
document.addEventListener('DOMContentLoaded', function() {
    initializeStaffReport();
});
window.jQuery = jQuery;
window.$ = jQuery;
// Important: Set Popper correctly
window.Popper = Popper;
// If needed, also set the default export
window.PopperDefault = Popper.default;

window.Alpine = Alpine;
Alpine.start();