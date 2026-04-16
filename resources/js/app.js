import jQuery from 'jquery';
import Alpine from 'alpinejs';
import * as Popper from '@popperjs/core';

// Expose jQuery globally BEFORE other plugins that depend on it
window.jQuery = jQuery;
window.$ = jQuery;

import 'bootstrap';
import 'bootstrap-select';
import DataTable from 'datatables.net';

// Popper
window.Popper = Popper;

// Alpine
window.Alpine = Alpine;
Alpine.start();