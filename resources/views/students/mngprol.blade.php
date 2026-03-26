<x-custom-admin-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style nonce="{{ $cspNonce }}">
        .header-container{
    font-size: 1.5rem;
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center;

}
.border {
    border: 4px solid #ced4da;
            border-radius: 0.25rem;
            padding: 10px;
            position: relative;
            margin-top: 20px;
            margin-bottom: 15px;
        }
        .border legend {
            font-size: 1rem;
            font-weight: 400;
            width: auto;
            padding: 0 5px;
            margin-bottom: 0;
            position: absolute;
            top: -0.8rem;
            left: 1rem;
            background: white;
        }
        .card-box {
  font-size: 0.875rem;
}

.small-label {
  font-size: 0.75rem;
}
.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

legend.small {
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.no-gutters {
  margin-right: 0;
  margin-left: 0;
}

.no-gutters > .col,
.no-gutters > [class*="col-"] {
  padding-right: 0;
  padding-left: 0;
}

.content-table2 {
    border-collapse: collapse;
    margin: 5px auto;
    font-size: 0.8rem; /* Smaller font */
    min-width: 400px;
    border-radius: 2px 2px 0 0;
    overflow: hidden;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.08);
    cursor: pointer;
    width: 100%;
}

.content-table2 thead tr {
    background-color: deepskyblue;
    text-align: left;
    font-weight: bold;
    font-size: 0.75rem; /* Even smaller header font */
}

.content-table2 th,
.content-table2 td {
    padding: 1px 3px; /* Minimal padding */
    line-height: 1.1; /* Very tight line spacing */
    height: 20px; /* Fixed row height */
}

.content-table2 tbody tr {
    border-bottom: 0.5px solid #dddddd; /* Thinner border */
    font-size: 0.75rem; /* Smaller body font */
    height: 20px; /* Consistent row height */
}

.content-table2 tbody tr:nth-of-type(even) {
    background-color:rgb(207, 206, 206);
}

.content-table2 tbody tr:last-of-type {
    border-bottom: 0.5px solid deepskyblue;
}

.content-table2 tbody tr.highlight {
    background-color: #d4edda; /* Subtle green highlight */
}
.pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 20px 0 0;
}

.pagination .page-item {
    margin: 0 5px;
}

.pagination .page-item a {
    color: deepskyblue;
    text-decoration: none;
    padding: 8px 12px;
    border: 1px solid deepskyblue;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

.pagination .page-item.active a {
    background-color: deepskyblue;
    color: white;
}

.pagination .page-item a:hover {
    background-color: #007bff;
    color: white;
}

.button-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px; /* Adjust as needed */
}

.button-container .col-sm-6 {
    width: 48%; /* Adjust as needed */
}
fieldset {
    background-color: white;
    border-radius: 3px;
    margin-bottom: 0.5rem;
}
.modal-body {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}
legend {
  background-color: none;
    border: none;
    padding: 1px 3px;
    font-size: 12px;
    margin-bottom: 0;
}

.form-control {
    height: 30px;
    padding: 1px 2px;
}

.btn {
    padding: 3px 10px;
}

.modal-content {
    background-color: white;
}
.form-control-sm {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.1rem 0.3rem;
    font-size: 11x;
}
.compact-form .form-group {
            margin-bottom: 0.1rem;
        }
        .compact-form .col-form-label-sm {
            padding-top: 0;
            padding-bottom: 0;
            font-size: 11x;
        }
        .compact-form .form-control-sm {
            padding-top: 0.1rem;
            padding-bottom: 0.1rem;
        }
        .compact-form fieldset {
            margin-bottom: 0.1rem;
        }
        .compact-form legend {
            font-size: 0.85rem;
        }
        .compact-form .btn-sm {
            padding: 0.25rem 0.5rem;
        }
        .modal-content {
            font-size: 0.85rem;
        }
        .small-font {
  font-size: 0.75rem;
}
.is-invalid {
    border: 1px solid red;
}
#employeeListModal .modal-dialog {
    max-width: 400px;
    width: 90%;
}
#employeeListModal .modal-content {
    background-color: #f8f9fa; /* Light gray background */
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
#employeeListModal .modal-header {
    background-color: #007bff; /* Blue header */
    color: white;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

#employeeListModal .modal-title {
    font-weight: bold;
}


#employeeListModal .form-group label {
    font-weight: bold;
    color: #333;
}

#employeeListModal .custom-select {
    border: 1px solid #ced4da;
    border-radius: 5px;
}

#employeeListModal .content-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

#employeeListModal .content-table th,
#employeeListModal .content-table td {
    border: 1px solid #dee2e6;
    padding: 10px;
    text-align: left;
    height: 20px; /* Set a fixed height for the rows */
    line-height: 10px; /* Adjust line height for vertical centering */
}

#employeeListModal .content-table thead {
    background-color: #e9ecef;
}

/* Ensure the content doesn't overflow */
#employeeListModal .content-table td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.toggle-switch {
        position: relative;
        display: inline-flex;
        width: 30px;
        height: 17px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #2196F3;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 13px;
        width: 13px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #0cf24d;
    }

    input:checked + .slider:before {
        transform: translateX(13px);
    }

    .slider.round {
        border-radius: 17px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
   /* Modal Overlay */
/* Specific styles for progress modal */
#progress-modal {
  display: none; /* Hide by default */
}

#progress-modal .modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000; /* Ensure the modal is on top of other content */
}

#progress-modal .modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  text-align: center;
  width: 300px; /* Adjust width as needed */
}

#progress-modal #progress-bar-container {
  width: 100%;
  background-color: #f3f3f3;
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
  height: 20px;
  margin-top: 10px;
}

#progress-modal #progress-bar {
  height: 100%;
  background-color: #4caf50;
  width: 0%;
}
#otListModal .modal-dialog {
    max-width: 400px;
    width: 90%;
}
#otListModal .modal-content {
    background-color: #f8f9fa; /* Light gray background */
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
#otListModal .modal-header {
    background-color: #007bff; /* Blue header */
    color: white;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}
#otListModal .modal-title{
    font-weight: bold;
}
#otListModal .modal-body {
    padding: 20px;
}
#otListModal .form-group label {
    font-weight: bold;
    color: #333;
}
	.btn-enhanced {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    cursor: pointer;
    min-width: 80px;
}

.btn-enhanced:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
}

.btn-draft {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-draft:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    color: white;
}
.btn-final {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 9999;
            transform: translateX(400px);
            transition: all 0.5s ease;
        }
        
        .custom-alert.show {
            transform: translateX(0);
        }
        
        .alert-success {
            animation: successPulse 1s ease-in-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(0.95); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        .clickable-row {
            cursor: pointer;
        }
        .clickable-row:hover {
            background-color: #f8f9fa;
        }
        .table-active {
            background-color: #d1ecf1 !important;
        }
        .deductions-table-compact {
    font-size: 13px;
    margin-bottom: 0;
}

.deductions-table-compact thead th {
    padding: 6px 8px;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    vertical-align: middle;
    height: 36px;
}

.deductions-table-compact tbody td {
    padding: 4px 8px;
    vertical-align: middle;
    height: 32px;
}

.deductions-table-compact tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Make striped rows more subtle */
.deductions-table-compact.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.015);
}
    @keyframes slideIn {
    from { right: -100px; opacity: 0; }
    to { right: 20px; opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}


.btn-enhanced.disabled,
.btn-enhanced:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background-color: #95a5a6 !important;
    border-color: #7f8c8d !important;
    pointer-events: none;
}

.btn-enhanced.disabled:hover,
.btn-enhanced:disabled:hover {
    background-color: #95a5a6 !important;
    transform: none;
    box-shadow: none;
}

/* Tooltip styling */
.tooltip-inner {
    max-width: 300px;
    text-align: left;
    padding: 10px;
}

.tooltip.show {
    opacity: 1;
}
  /* ── Modal shell ─────────────────────────────────────────── */
    #exampleModal .modal-dialog { max-width: 900px; }
 
    #exampleModal .modal-content {
        border: none;
        border-radius: 18px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        font-family: var(--font-body);
        /* NO overflow:hidden here — lets Choices.js dropdown escape */
    }
 
    #exampleModal .modal-content::before {
        content: '';
        display: block;
        height: 4px;
        background: linear-gradient(90deg, #1a56db 0%, #6366f1 60%, #8b5cf6 100%);
        border-radius: 18px 18px 0 0;
    }
 
    #exampleModal .modal-header {
        padding: 12px 20px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: 10px;
        background: var(--surface);
    }
 
    .pp-modal-icon {
        width: 30px; height: 30px; border-radius: 8px;
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
 
    .pp-modal-icon .material-icons { font-size: 15px; color: var(--accent); }
 
    #exampleModal .modal-title {
        font-family: var(--font-head);
        font-size: 14px; font-weight: 700; color: var(--ink); margin: 0; flex: 1;
    }
 
    #exampleModal .close {
        width: 28px; height: 28px; border: 1.5px solid var(--border);
        border-radius: 7px; background: none; opacity: 1; color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        transition: all .2s; padding: 0;
    }
 
    #exampleModal .close:hover { color: var(--ink); border-color: #9ca3af; background: var(--bg); }
 
    /* ── Modal body — compact, enough height for Choices dropdown ── */
    #exampleModal .modal-body {
        padding: 12px 16px;
        background: var(--bg);
        overflow-y: auto;
        overflow-x: visible; /* let dropdowns overflow */
        max-height: calc(92vh - 100px);
    }
 
    /* ── Panel ───────────────────────────────────────────────── */
    .pp-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        margin-bottom: 10px;
        /* CRITICAL: no overflow:hidden — Choices.js needs to escape */
    }
 
    .pp-panel-head {
        display: flex; align-items: center; gap: 6px;
        padding: 6px 12px;
        background: #f9fafb;
        border-bottom: 1px solid var(--border);
        border-radius: 10px 10px 0 0;
        font-size: 10px; font-weight: 600;
        text-transform: uppercase; letter-spacing: .07em; color: var(--muted);
    }
 
    .pp-panel-head .material-icons { font-size: 13px; }
    .pp-panel-body { padding: 10px 12px; }
 
    /* ── Grid ────────────────────────────────────────────────── */
    .pp-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 8px 10px;
    }
 
    .ppc-2  { grid-column: span 2; }
    .ppc-3  { grid-column: span 3; }
    .ppc-4  { grid-column: span 4; }
    .ppc-5  { grid-column: span 5; }
    .ppc-6  { grid-column: span 6; }
    .ppc-7  { grid-column: span 7; }
    .ppc-8  { grid-column: span 8; }
    .ppc-12 { grid-column: span 12; }
 
    @media (max-width: 640px) {
        .ppc-2,.ppc-3,.ppc-4,.ppc-5,.ppc-6,.ppc-7,.ppc-8 { grid-column: span 12; }
    }
 
    /* ── Field ───────────────────────────────────────────────── */
    .pp-field { display: flex; flex-direction: column; gap: 3px; }
 
    .pp-field label {
        font-size: 10.5px; font-weight: 500; color: #374151; letter-spacing: .01em;
    }
 
    .pp-field input,
    .pp-field select {
        height: 30px; padding: 0 8px;
        border: 1.5px solid var(--border); border-radius: 7px;
        background: #fafafa; font-family: var(--font-body);
        font-size: 12.5px; color: var(--ink); outline: none; width: 100%;
        appearance: none; -webkit-appearance: none;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }
 
    .pp-field input[type="date"] { height: 30px; }
    .pp-field input[type="number"] { height: 30px; }
 
    .pp-field input:focus,
    .pp-field select:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3px rgba(26,86,219,.1);
    }
 
    .pp-field input[readonly] { background: #f3f4f8; color: var(--muted); cursor: not-allowed; }
    .pp-field input::placeholder { color: #adb5bd; font-size: 11.5px; }
 
    /* Select arrow */
    .pp-select-wrap { position: relative; }
 
    .pp-select-wrap::after {
        content: 'expand_more'; font-family: 'Material Icons'; font-size: 16px;
        position: absolute; right: 7px; top: 50%; transform: translateY(-50%);
        color: var(--muted); pointer-events: none; z-index: 1;
    }
 
    .pp-select-wrap select { padding-right: 26px; }
 
    /* ── Choices.js overrides — CRITICAL for visibility ─────── */
 
    /* Container must not clip */
    .choices {
        overflow: visible !important;
        margin-bottom: 0;
        font-size: 12.5px;
    }
 
    /* The visible button/box */
    .choices__inner {
        min-height: 30px !important;
        padding: 4px 8px !important;
        border: 1.5px solid var(--border) !important;
        border-radius: 7px !important;
        background: #fafafa !important;
        font-size: 12.5px !important;
        line-height: 1.4;
    }
 
    .choices.is-focused .choices__inner,
    .choices__inner:focus {
        border-color: var(--border-focus) !important;
        background: var(--surface) !important;
        box-shadow: 0 0 0 3px rgba(26,86,219,.1) !important;
    }
 
    /* The dropdown list — must use fixed or high z-index to escape panel */
    .choices__list--dropdown {
        z-index: 9999 !important;
        border: 1.5px solid var(--border) !important;
        border-radius: 0 0 10px 10px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,.12) !important;
        font-size: 12.5px !important;
        max-height: 220px !important;
        overflow-y: auto !important;
    }
 
    .choices__list--dropdown .choices__item {
        padding: 7px 10px !important;
        font-size: 12.5px;
    }
 
    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background: var(--accent-lt) !important;
        color: var(--accent) !important;
    }
 
    /* Search input inside Choices */
    .choices__input {
        font-size: 12.5px !important;
        padding: 2px 4px !important;
        background: transparent !important;
        margin-bottom: 0 !important;
    }
 
    /* Single item text */
    .choices__list--single .choices__item {
        font-size: 12.5px;
    }
 
    /* Select2 overrides (for #pitem) */
    .select2-container { width: 100% !important; }
 
    .select2-container .select2-selection--single {
        height: 30px !important;
        border: 1.5px solid var(--border) !important;
        border-radius: 7px !important;
        background: #fafafa !important;
        display: flex !important;
        align-items: center !important;
        font-size: 12.5px !important;
    }
 
    .select2-container .select2-selection__rendered {
        line-height: 28px !important;
        font-size: 12.5px !important;
        color: var(--ink) !important;
        padding-left: 8px !important;
    }
 
    .select2-container .select2-selection__arrow {
        height: 28px !important;
    }
 
    .select2-dropdown {
        border: 1.5px solid var(--border) !important;
        border-radius: 0 0 10px 10px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,.12) !important;
        font-size: 12.5px !important;
        z-index: 9999 !important;
    }
 
    .select2-results__option {
        font-size: 12.5px !important;
        padding: 6px 10px !important;
    }
 
    .select2-results__option--highlighted {
        background: var(--accent) !important;
    }
 
    /* ── Toggle switch ───────────────────────────────────────── */
    .pp-toggle-wrap { display: inline-flex; align-items: center; gap: 7px; }
 
    .toggle-switch {
        position: relative; display: inline-block;
        width: 34px; height: 18px; flex-shrink: 0;
    }
 
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
 
    .toggle-switch .slider {
        position: absolute; inset: 0;
        background: #d1d5db; border-radius: 18px; cursor: pointer; transition: background .2s;
    }
 
    .toggle-switch .slider:before {
        content: ''; position: absolute;
        height: 12px; width: 12px; left: 3px; bottom: 3px;
        background: #fff; border-radius: 50%;
        transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2);
    }
 
    .toggle-switch input:checked + .slider { background: var(--accent); }
    .toggle-switch input:checked + .slider:before { transform: translateX(16px); }
 
    .pp-toggle-label { font-size: 12px; font-weight: 500; color: var(--ink); min-width: 44px; }
 
    /* ── Post row ────────────────────────────────────────────── */
    .pp-post-row {
        display: flex; align-items: flex-end; gap: 8px;
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 10px; padding: 10px 12px; margin-bottom: 10px; flex-wrap: wrap;
    }
 
    .pp-post-row .pp-field { flex: 1; min-width: 110px; }
 
    /* ── Buttons ─────────────────────────────────────────────── */
    #submitBtn {
        height: 30px; padding: 0 14px;
        background: linear-gradient(135deg, #1a56db, #4f46e5);
        color: #fff; border: none; border-radius: 7px;
        font-family: var(--font-body); font-size: 12.5px; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 3px 10px rgba(26,86,219,.25); white-space: nowrap;
        flex-shrink: 0; align-self: flex-end;
    }
 
    #submitBtn:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(26,86,219,.35); }
    #submitBtn:disabled { opacity: .55; cursor: not-allowed; transform: none; }
    #submitBtn .material-icons { font-size: 14px; }
 
    #btnopenot {
        height: 30px; padding: 0 12px;
        background: linear-gradient(135deg, #059669, #10b981);
        color: #fff; border: none; border-radius: 7px;
        font-family: var(--font-body); font-size: 12px; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; gap: 4px;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 3px 8px rgba(5,150,105,.2); flex-shrink: 0;
    }
 
    #btnopenot:hover { transform: translateY(-1px); }
    #btnopenot .material-icons { font-size: 13px; }
 
    /* ── Input group (Duration/Ends In) ──────────────────────── */
    .pp-input-group {
        display: flex; align-items: center;
        border: 1.5px solid var(--border); border-radius: 7px; overflow: hidden;
        background: #fafafa; height: 30px;
    }
 
    .pp-input-group .pp-ig-label {
        padding: 0 8px; height: 100%;
        background: #f3f4f8; border-right: 1px solid var(--border);
        font-size: 11px; font-weight: 500; color: var(--muted);
        display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;
    }
 
    .pp-input-group input {
        border: none !important; background: transparent !important;
        box-shadow: none !important; height: 100%; flex: 1;
        padding: 0 8px; font-size: 12.5px; font-family: var(--font-body);
        color: var(--ink); outline: none;
    }
 
    /* ── Review table ────────────────────────────────────────── */
    .pp-table-wrap { overflow-x: auto; }
 
    #contentTable2 {
        width: 100%; border-collapse: collapse;
        font-size: 12px; font-family: var(--font-body);
    }
 
    #contentTable2 thead th {
        background: #f9fafb; color: var(--muted);
        font-size: 10.5px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .06em; padding: 7px 10px;
        border-bottom: 1px solid var(--border); white-space: nowrap;
    }
 
    #contentTable2 tbody td {
        padding: 7px 10px; border-bottom: 1px solid #f3f4f8;
        color: var(--ink); vertical-align: middle;
    }
 
    #contentTable2 tbody tr:last-child td { border-bottom: none; }
    #contentTable2 tbody tr:hover td { background: #f8faff; }
 
    .pp-totals-row {
        display: flex; align-items: center; justify-content: flex-end;
        gap: 7px; padding: 8px 12px;
    }
 
    .pp-totals-row label { font-size: 12px; font-weight: 600; color: var(--ink); }
 
    .pp-totals-row input {
        height: 30px; padding: 0 8px;
        border: 1.5px solid var(--border); border-radius: 7px;
        background: #f9fafb; font-family: var(--font-body);
        font-size: 12.5px; font-weight: 600; color: var(--ink);
        outline: none; width: 130px; text-align: right;
    }
 
    /* ── Validation ──────────────────────────────────────────── */
    .pp-field input.is-invalid,
    .pp-field select.is-invalid { border-color: var(--danger) !important; }
 
    /* ── Inline validation message ───────────────────────────── */
    .pp-err {
        font-size: 10.5px; color: var(--danger); margin-top: 2px; display: none;
    }
 
    .pp-err.show { display: block; }
    </style>
    
    <!-- Make sure CSS is loaded before content -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <div class="mobile-menu-overlay"></div>
    <h1 class="header-container"></h1>
    <div>
        <div class="pd-ltr-20 xs-pd-20-10">
            <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <div class="toast-wrap" id="toastWrap"></div>
    </div>
    <div class="card-box p-3 mb-3" >
        <div class="row align-items-center mb-2" hidden>
            <div class="col-md-4 pr-md-2">
                <div class="border p-2">
                    <legend class="small mb-1">Current Payroll Period</legend>
                    <div class="row no-gutters">
                        <div class="col-md-6 pr-md-1">
                            <div class="form-group mb-1">
                                <label for="currentMonth" class="small-label mb-0">Current Month</label>
                                <input type="text" class="form-control form-control-sm" id="currentMonth" value="{{ $month }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 pl-md-1">
                            <div class="form-group mb-1">
                                <label for="currentYear" class="small-label mb-0">Current Year</label>
                                <input type="text" class="form-control form-control-sm" id="currentYear" value="{{ $year }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        {{-- Active Period Display --}}
                <div class="alert alert-info" id="period-info">
                    <i class="fa fa-calendar"></i> Loading period...
                </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <button type="button" class="btn btn-sm btn-primary mr-1" id="empmodal" data-toggle="modal" data-target="#exampleModal">Post by Parameter</button>
                <button type="button" class="btn btn-sm btn-secondary mr-1" data-toggle="modal" data-target="#employeeModal" disabled>Process by Employee</button>
                <!------<button type="button" class="btn btn-sm btn-secondary mr-1">Edit Mode</button>---->
                <button type="button" class="btn btn-sm btn-info" disabled>View loan schedule</button>
            </div>
        </div>
        
    </div>
    <div class="card-box mb-30">
        <div class="pd-20">
    
                
                

                {{-- Search Bar --}}
                <div class="row mb-0">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" 
                                   id="searchInput" 
                                   class="form-control" 
                                   placeholder="Search by name or work number...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" id="searchBtn">
                                    <i class="fa fa-search"></i> Search
                                </button>
                                <button class="btn btn-secondary" id="clearBtn">
                                    <i class="fa fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <span id="total-records" class="badge badge-info" style="font-size: 14px;"></span>
                    </div>
                </div>

                {{-- Table Container --}}
                <div class="table-responsive">
    <table class="table table-striped table-hover deductions-table-compact" id="deductionsTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Work Number</th>
                <th>Department</th>
                <th>Parameter Code</th>
                <th>Parameter Name</th>
                <th>Parameter Category</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <tr>
                <td colspan="7" class="text-center">
                    <i class="fa fa-spinner fa-spin"></i> Loading data...
                </td>
            </tr>
        </tbody>
    </table>
</div>

                {{-- Pagination --}}
                <div class="row mt-0">
                    <div class="col-md-6">
                        <div id="showing-info"></div>
                    </div>
                    <div class="col-md-6">
                        <ul class="pagination justify-content-end" id="pagination"></ul>
                    </div>
                </div>
                
            </div>
            <div class="mt-0">
    <div class="d-flex">

        <div class="custom-control custom-checkbox mr-3">
            <input class="custom-control-input toggle-checkbox" 
                   type="checkbox" id="nhif" data-model="nhif"
                   {{ $nhif == 'ACTIVE' ? 'checked' : '' }}>
            <label class="custom-control-label" for="nhif">NHIF</label>
        </div>

        <div class="custom-control custom-checkbox mr-3">
            <input class="custom-control-input toggle-checkbox"
                   type="checkbox" id="nssf" data-model="nssf"
                   {{ $nssf == 'ACTIVE' ? 'checked' : '' }}>
            <label class="custom-control-label" for="nssf">NSSF</label>
        </div>

        <div class="custom-control custom-checkbox mr-3">
            <input class="custom-control-input toggle-checkbox"
                   type="checkbox" id="shif" data-model="shif"
                   {{ $shif == 'ACTIVE' ? 'checked' : '' }}>
            <label class="custom-control-label" for="shif">SHIF</label>
        </div>

        <div class="custom-control custom-checkbox mr-3">
            <input class="custom-control-input toggle-checkbox"
                   type="checkbox" id="pension" data-model="pension"
                   {{ $pension == 'ACTIVE' ? 'checked' : '' }}>
            <label class="custom-control-label" for="pension">Pension</label>
        </div>

        <div class="custom-control custom-checkbox mr-3">
            <input class="custom-control-input toggle-checkbox"
                   type="checkbox" id="hlevy" data-model="hlevy"
                   {{ $hlevy == 'ACTIVE' ? 'checked' : '' }}>
            <label class="custom-control-label" for="hlevy">Housing Levy</label>
        </div>

    </div>
</div>
<div class="mt-3">
        
        <button 
        id="preview-totals-btn" 
        class="btn btn-enhanced btn-final {{ !$isApproved ? 'disabled' : '' }}"
        {{ !$isApproved ? 'disabled' : '' }}
        data-toggle="tooltip" 
        data-placement="top" 
        data-html="true"
        title="{{ !$isApproved ? '<strong>Action Required:</strong><br>Payments for ' . $month . ' ' . $year . ' are pending approval.<br>Please wait for approval before calculating.' : 'Click to auto-calculate payroll totals' }}"
        >
        <i class="fas fa-bolt"></i> Auto Calculate
    </button>

@if(!$isApproved)
    <div class="alert alert-warning mt-2" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Pending Approval:</strong> 
        The payments for {{ $month }} {{ $year }} are currently <span class="badge badge-warning">{{ $approvalStatus }}</span>. 
        Auto-calculation will be available after approval.
    </div>
@endif

 <button class="btn btn-enhanced btn-draft" id="NofityApprover">
    <i class="fas fa-paper-plane"></i> Notify Approver
</button>
    </div>
    </div>
    
   <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
 
            <div class="modal-header">
                <div class="pp-modal-icon"><span class="material-icons">post_add</span></div>
                <h5 class="modal-title" id="exampleModalLabel">Post by Parameter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="material-icons" style="font-size:16px;">close</span>
                </button>
            </div>
 
            <div class="modal-body">
                <form id="payrollForm" class="compact-form">
 
                    {{-- ── Row 1: Period + Payroll Item ──────────── --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
 
                        {{-- Period --}}
                        <div class="pp-panel">
                            <div class="pp-panel-head">
                                <span class="material-icons">calendar_month</span> Payroll Period
                            </div>
                            <div class="pp-panel-body">
                                <div class="pp-grid">
                                    <div class="pp-field ppc-6">
                                        <label>Month</label>
                                        <input type="text" id="month" value="{{ $month }}" readonly>
                                    </div>
                                    <div class="pp-field ppc-6">
                                        <label>Year</label>
                                        <input type="text" id="year" value="{{ $year }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
 
                        {{-- Payroll Item --}}
                        <div class="pp-panel">
                            <div class="pp-panel-head">
                                <span class="material-icons">receipt_long</span> Payroll Item
                            </div>
                            <div class="pp-panel-body">
                                <div class="pp-field">
                                    <label>Select Item <span style="color:var(--danger)">*</span></label>
                                    {{-- Select2 is initialised by JS on modal open --}}
                                    <select name="pitem" id="pitem" required autocomplete="off" onchange="populateCategory()">
                                        <option value="">Select Item</option>
                                    </select>
                                    <input name="category"  id="category"  type="text" hidden>
                                    <input name="increREDU" id="increREDU" type="text" hidden>
                                    <input name="codebal"   id="codebal"   type="text" hidden>
                                </div>
                            </div>
                        </div>
 
                    </div>
 
                    {{-- ── Staff search ────────────────────────── --}}
                    <div class="pp-panel" style="margin-bottom:10px;">
                        <div class="pp-panel-head">
                            <span class="material-icons">manage_search</span> Staff Search
                        </div>
                        <div class="pp-panel-body">
 
                            <div class="pp-grid" style="margin-bottom:8px;">
                                <div class="pp-field ppc-8">
                                    <label>Select Staff <span style="color:var(--danger)">*</span></label>
                                    {{-- Choices.js is initialised by JS on modal open --}}
                                    <select name="searchValue" id="searchValue" required onchange="searchstaffdet()">
                                        <option value="">Search staff…</option>
                                    </select>
                                </div>
                                {{-- Hidden category select (used by JS) --}}
                                <div hidden>
                                    <select id="searchCategory">
                                        <option value="WorkNumber">Work number</option>
                                        <option value="Surname">Name</option>
                                    </select>
                                </div>
                            </div>
 
                            <div class="pp-grid">
                                <div class="pp-field ppc-3">
                                    <label>Surname</label>
                                    <input type="text" id="surname" readonly>
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Other Name</label>
                                    <input type="text" id="othername" readonly>
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Work Number</label>
                                    <input type="text" id="workNumber" readonly>
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Department</label>
                                    <input type="text" id="department" hidden>
                                    <input type="text" id="departmentname" readonly>
                                </div>
                            </div>
 
                        </div>
                    </div>
 
                    {{-- ── Amount / Balance / Post ─────────────── --}}
                    <div class="pp-post-row">
                        <div class="pp-field">
                            <label>Amount</label>
                            <input type="text" id="amount" placeholder="Enter amount">
                        </div>
                        <div class="pp-field">
                            <label>Balance</label>
                            <input type="text" id="balance" placeholder="Enter balance">
                        </div>
                        <button type="button" id="submitBtn">
                            <span class="material-icons">send</span> Post
                        </button>
                    </div>
 
                    {{-- ── Loan (hiddenContainer) ───────────────── --}}
                    <div class="pp-panel" id="hiddenContainer" style="display:none;margin-bottom:10px;">
                        <div class="pp-panel-head">
                            <span class="material-icons">account_balance_wallet</span> Loan Details
                        </div>
                        <div class="pp-panel-body">
                            <div class="pp-grid" style="align-items:center;">
                                <div class="pp-field ppc-3">
                                    <label>Months</label>
                                    <input type="text" id="months">
                                </div>
                                <div class="pp-field ppc-4">
                                    <label>End Date</label>
                                    <input type="text" id="enddate" readonly>
                                </div>
                                <div class="ppc-5" style="display:flex;align-items:center;gap:8px;padding-top:16px;">
                                    <div class="pp-toggle-wrap">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="activeinaclonToggle" checked>
                                            <span class="slider round"></span>
                                        </label>
                                        <span class="pp-toggle-label" id="toggleLabel3">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    {{-- ── Balance (hiddenContainer2) ───────────── --}}
                    <div class="pp-panel" id="hiddenContainer2" style="display:none;margin-bottom:10px;">
                        <div class="pp-panel-head">
                            <span class="material-icons">balance</span> Balance Details
                        </div>
                        <div class="pp-panel-body">
                            <div class="pp-grid" style="align-items:center;">
                                <div class="ppc-3" style="display:flex;align-items:center;gap:7px;padding-top:2px;">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="fixedOpenToggle" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="pp-toggle-label" id="toggleLabel">Open</span>
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Current Balance</label>
                                    <input type="number" id="cbalance" name="duration" placeholder="—" readonly>
                                </div>
                                <div id="Fixed" class="ppc-6" style="display:none;">
                                    <div class="pp-grid">
                                        <div class="ppc-6">
                                            <div class="pp-input-group">
                                                <span class="pp-ig-label">Duration</span>
                                                <input type="text" id="duration" name="duration" placeholder="months">
                                            </div>
                                        </div>
                                        <div class="ppc-6">
                                            <div class="pp-input-group">
                                                <span class="pp-ig-label">Ends In</span>
                                                <input type="text" id="balend" name="balend" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="Open" class="ppc-3" style="display:flex;align-items:center;gap:7px;">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="activeinacToggle" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="pp-toggle-label" id="toggleLabel2">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    {{-- ── Pension (pensionContainer) ───────────── --}}
                    <div class="pp-panel" id="pensionContainer" style="display:none;margin-bottom:10px;">
                        <div class="pp-panel-head">
                            <span class="material-icons">savings</span> Pension
                        </div>
                        <div class="pp-panel-body">
                            <div class="pp-grid">
                                <div class="pp-field ppc-3">
                                    <label>Employee %</label>
                                    <input type="text" id="epmpenperce">
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Employer %</label>
                                    <input type="text" id="emplopenperce">
                                </div>
                                <div class="pp-field ppc-4">
                                    <label>Pensionable</label>
                                    <input type="text" id="pensionable" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    {{-- ── OT / Formula (otContainer) ──────────── --}}
                    <div class="pp-panel" id="otContainer" style="display:none;margin-bottom:10px;">
                        <div class="pp-panel-head">
                            <span class="material-icons">functions</span> Calculation / OT
                        </div>
                        <div class="pp-panel-body">
                            <div class="pp-grid" style="align-items:flex-end;">
                                <div class="pp-field ppc-3">
                                    <label>Formula</label>
                                    <input name="formular" id="formular" type="text" autocomplete="off" readonly>
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Date</label>
                                    <input type="date" id="otdate">
                                </div>
                                <div class="pp-field ppc-2">
                                    <label>Quantity</label>
                                    <input type="text" id="quantity">
                                </div>
                                <div class="ppc-2" style="padding-bottom:0;">
                                    <button type="button" id="btnopenot">
                                        <span class="material-icons">open_in_new</span> Open
                                    </button>
                                </div>
                                <input type="text" id="camountf" hidden>
                            </div>
                        </div>
                    </div>
 
                    {{-- ── Posted items table ───────────────────── --}}
                    <div class="pp-panel">
                        <div class="pp-panel-head">
                            <span class="material-icons">table_view</span> Posted Items
                        </div>
                        <div style="padding:0;">
                            <div class="pp-table-wrap">
                                <table id="contentTable2" class="content-table2">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Work Number</th>
                                            <th>Department</th>
                                            <th>Parameter Code</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="pp-totals-row">
                                <label for="totalsvar">Total:</label>
                                <input type="text" id="totalsvar" readonly>
                            </div>
                        </div>
                    </div>
 
                </form>
            </div>{{-- /modal-body --}}
        </div>{{-- /modal-content --}}
    </div>{{-- /modal-dialog --}}
</div>{{-- /modal --}}
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="exampleModalLabel">Process by Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-2">
                    <form id="payrollempForm" class="compact-form">
                        <div class="row no-gutters">
                            <div class="col-md-6 pr-md-1">
                                <fieldset class="border p-1 mb-2">
                                    <legend class="w-auto small mb-0">Current Payroll Period</legend>
                                    <div class="form-group row no-gutters">
                                       
                                        <label for="month" class="col-sm-3 col-form-label col-form-label-sm">MONTH</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="month" value="<?php echo htmlspecialchars($month); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row no-gutters">
                                        <label for="year" class="col-sm-3 col-form-label col-form-label-sm">YEAR</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="year" value="<?php echo htmlspecialchars($year); ?>" readonly>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6 pl-md-1">
                                <fieldset class="border p-1 mb-2">
                                    <legend class="w-auto small mb-0">Employee</legend>
                                    <div class="form-group row no-gutters">
                                        
                                        <div class="col-sm-12">
                                           
                                            <select name="WorkNo" id="WorkNo" class="custom-select form-control" required="true" autocomplete="off" onchange="searchstaffdet2()">
                                            <option value="">Select Staff</option>
                                            
                                        </select>
                                        </div>
                                    </div>
                                    <div class="form-group row no-gutters">
                                        <label for="year" class="col-sm-3 col-form-label col-form-label-sm">Name</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control form-control-sm" id="empname"  readonly>
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control form-control-sm" id="empname1"  readonly>
                                        </div>
                                    </div>
                                    
                                </fieldset>
                            </div>
                        </div>
                        <input type="text" class="form-control form-control-sm" id="empdept"  readonly hidden>
                    </form>
                    <div class="mt-3">
    <div class="row mb-2">
        <div class="col" hidden>
            <input type="text" class="form-control form-control-sm" id="inputID" placeholder="ID">
        </div>
        <div class="col">
            <label for="inputPCode" class="col-form-label col-form-label-sm">Code</label>
            <input type="text" class="form-control form-control-sm" id="inputPCode" placeholder="PCode">  
        </div>
        <div class="col">
            <label for="inputname" class="col-form-label col-form-label-sm">Parameter Name</label>
            <input type="text" class="form-control form-control-sm" id="inputname" placeholder="Category" readonly>
        </div>
        <div class="col">
            <label for="inputAmount" class="col-form-label col-form-label-sm">Amount</label>
            <input type="text" class="form-control form-control-sm" id="inputAmount" placeholder="Amount">
        </div>
        <div class="col">
            <label for="inputBalance" class="col-form-label col-form-label-sm">Balance</label>
            <input type="text" class="form-control form-control-sm" id="inputBalance" placeholder="Balance">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <button id="submitButton" class="btn btn-primary btn-sm">Submit</button>
        </div>
    </div>
    <table id="employeeDataTable" class="content-table">
        <thead>
            <tr>
                <th hidden>ID</th>
                <th>PCode</th>
                <th>Item Name</th>
                
                <th>Amount</th>
                <th>Balance</th>
                <th>Date Posted</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div class="row mb-3">
    <div class="col-md-6 d-flex align-items-end">
        <button id="recalcButton" class="btn btn-primary btn-sm me-2">Recalculate</button>
    </div>
    <div class="col-md-3">
        <label for="monthPicker" class="form-label">Period:</label>
        <input type="month" id="periodPick" class="form-control" name="monthPicker">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button id="viewp" class="btn btn-info btn-sm">View Payslip</button>
    </div>
</div>
</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="codeSelectionModal" tabindex="-1" role="dialog" aria-labelledby="codeSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="codeSelectionModalLabel">Select Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
    <p>Double click to select a code from the table:</p>
    <table class="content-table" >
        <thead>
            <tr>
                <th>Code</th>
            </tr>
        </thead>
        <tbody id="codeList" style="padding: 3px 5px; font-size: 12px;">
            <!-- Codes will be dynamically inserted here -->
        </tbody>
    </table>
</div>
        </div>
    </div>
</div>


    
<div id="successMessage" style="display:none;"></div>
<div id="progress-modal">
  <div class="modal-overlay">
    <div class="modal-content">
      <h4>Processing</h4>
      <div id="progress-bar-container">
        <div id="progress-bar"></div>
      </div>
      <p id="progress-message">Processing totals...</p>
    </div>
  </div>
</div>

<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Payslip Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
    </div>
  </div>
</div>

    
    <!-- Proper order of script loading -->
    <!-- 1. First jQuery -->

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- 2. Then DataTables core and styles -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    
    <!-- 3. SweetAlert Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script nonce="{{ $cspNonce }}">
        const autocalc = '{{ route("autocalc.process") }}';
        const getwuth = '{{ route("payroll.deductions.data") }}';
        const tstatus = '{{ route("toggle.status") }}';
         const getcodes = '{{ route("mngprol.getcodes") }}';
         const staffsearch = '{{ route("payroll.staff.search") }}';
         const staffdet = '{{ route("staff.search.details") }}';
         const fetchitems = '{{ route("fetch.items") }}';
         const paysubmit = '{{ route("payroll.submit") }}';
    </script>
    <script src="{{ asset('js/mngprol.js') }}"></script>

    
    <!-- 4. Your custom scripts -->
    <script nonce="{{ $cspNonce }}">
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover focus',
        boundary: 'window'
    });
    
    // Enhanced tooltip for disabled button
    $('#preview-totals-btn').on('mouseenter', function() {
        if ($(this).is(':disabled')) {
            $(this).tooltip('show');
        }
    });
    
    $('[data-toggle="tooltip"]').tooltip({
        html: true,
        trigger: 'hover focus'
    });
    
    // Notify Approver button click
    $('#NofityApprover').on('click', function(e) {
        e.preventDefault();
        
        var month = $('#currentMonth').val();
        var year = $('#currentYear').val();
        
        if (!month || !year) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Month and year are required'
            });
            return;
        }
        
        // Confirmation dialog
        Swal.fire({
            title: 'Notify Approver?',
            html: `Are you sure you want to submit the netpay for <strong>${month} ${year}</strong> for approval?<br><br>Make sure you have run Auto Calculate first.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e67e22',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Yes, Notify',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    html: 'Calculating totals and sending notification',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit notification
                $.ajax({
                    url: '{{ route("netpay.notify.approver") }}',
                    method: 'POST',
                    data: {
                        month: month,
                        year: year,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Notification Sent!',
                            html: response.message + '<br><br>Employees: ' + response.data.employee_count,
                            confirmButtonColor: '#4CAF50'
                        });
                    },
                    error: function(xhr) {
                        var errorMessage = 'Failed to send notification';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });
    
});
</script>
</x-custom-admin-layout>