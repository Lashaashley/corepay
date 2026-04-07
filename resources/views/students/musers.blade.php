










<x-custom-admin-layout>

<style nonce="{{ $cspNonce }}">
    /* ── Page-specific — tokens from corepay.css ─────────────── */

    .users-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }

    /* ── Page header ─────────────────────────────────────────── */
    .page-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        margin-bottom: 24px; flex-wrap: wrap; gap: 16px;
    }

    .page-heading h1 {
        font-family: var(--font-head);
        font-size: 22px; font-weight: 700; color: var(--ink); margin: 0 0 4px;
    }

    .page-heading p { font-size: 13.5px; color: var(--muted); margin: 0; }

    /* ── Table card ──────────────────────────────────────────── */
    .table-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 16px; box-shadow: var(--shadow); overflow: hidden;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Toolbar */
    .table-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 24px; border-bottom: 1px solid var(--border);
        flex-wrap: wrap; gap: 12px;
    }

    .toolbar-left { display: flex; align-items: center; gap: 10px; }

    .toolbar-icon {
        width: 36px; height: 36px; border-radius: 10px; background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .toolbar-icon .material-icons { font-size: 18px; color: var(--accent); }
    .toolbar-title { font-family: var(--font-head); font-size: 15px; font-weight: 700; color: var(--ink); }
    .toolbar-subtitle { font-size: 12px; color: var(--muted); }

    .toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

    /* Search */
    .search-box { position: relative; display: flex; align-items: center; }

    .search-box .material-icons {
        position: absolute; left: 10px; font-size: 17px; color: var(--muted); pointer-events: none;
    }

    .search-box input {
        height: 38px; padding: 0 13px 0 35px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body); font-size: 13.5px;
        color: var(--ink); outline: none; width: 220px;
        transition: border-color .2s, box-shadow .2s, width .3s;
    }

    .search-box input:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,219,.1); width: 260px;
    }

    .search-box input::placeholder { color: #adb5bd; }

    /* Page length */
    .page-length-select {
        height: 38px; padding: 0 28px 0 12px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body); font-size: 13.5px;
        color: var(--ink); outline: none; appearance: none; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236b7280'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 6px center; background-size: 18px;
        transition: border-color .2s;
    }

    .page-length-select:focus { border-color: var(--border-focus); outline: none; }

    /* ── Table overrides ─────────────────────────────────────── */
    .table-wrap { overflow-x: auto; }

    #users-table_wrapper .dataTables_filter,
    #users-table_wrapper .dataTables_length { display: none !important; }

    #users-table_wrapper .dataTables_info {
        font-size: 12.5px; color: var(--muted); padding: 14px 24px;
    }

    #users-table_wrapper .dataTables_paginate {
        padding: 10px 20px 16px; display: flex; justify-content: flex-end; gap: 4px;
    }

    #users-table_wrapper .paginate_button {
        height: 32px; min-width: 32px; padding: 0 10px !important;
        border-radius: 8px !important; border: 1.5px solid var(--border) !important;
        background: var(--surface) !important; color: var(--muted) !important;
        font-size: 13px !important; font-family: var(--font-body) !important;
        cursor: pointer; display: inline-flex !important; align-items: center;
        justify-content: center; transition: all .2s;
    }

    #users-table_wrapper .paginate_button:hover:not(.disabled) {
        background: var(--accent-lt) !important; border-color: var(--accent) !important; color: var(--accent) !important;
    }

    #users-table_wrapper .paginate_button.current {
        background: linear-gradient(135deg, #1a56db, #4f46e5) !important;
        border-color: transparent !important; color: #fff !important;
        box-shadow: 0 3px 10px rgba(26,86,219,.3);
    }

    #users-table_wrapper .paginate_button.disabled { opacity: .4; cursor: not-allowed; }

    table#users-table {
        width: 100% !important; border-collapse: collapse;
        font-size: 13.5px; font-family: var(--font-body);
    }

    table#users-table thead th {
        background: #f9fafb; color: var(--muted); font-size: 11.5px; font-weight: 600;
        text-transform: uppercase; letter-spacing: .06em;
        padding: 12px 16px; border-bottom: 1px solid var(--border); white-space: nowrap;
    }

    table#users-table thead th:first-child { padding-left: 24px; }
    table#users-table thead th:last-child  { padding-right: 24px; }

    table#users-table tbody td {
        padding: 13px 16px; border-bottom: 1px solid #f3f4f8;
        vertical-align: middle; color: var(--ink);
    }

    table#users-table tbody td:first-child { padding-left: 24px; }
    table#users-table tbody td:last-child  { padding-right: 24px; }
    table#users-table tbody tr:last-child td { border-bottom: none; }
    table#users-table tbody tr:hover td { background: #f8faff; }

    /* User cell */
    .user-cell { display: flex; align-items: center; gap: 10px; }

    .user-avatar-img {
        width: 36px; height: 36px; border-radius: 50%;
        object-fit: cover; border: 2px solid var(--border); flex-shrink: 0;
    }

    .user-name { font-weight: 600; font-size: 13.5px; color: var(--ink); }

    /* Status badges */
    .status-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; border-radius: 100px;
        font-size: 11.5px; font-weight: 600;
    }

    .status-badge .dot { width: 5px; height: 5px; border-radius: 50%; }
    .status-badge.active   { background: var(--success-lt); color: var(--success); }
    .status-badge.active   .dot { background: var(--success); }
    .status-badge.expired  { background: var(--danger-lt);  color: var(--danger); }
    .status-badge.expired  .dot { background: var(--danger); }
    .status-badge.approver { background: #f3f0ff; color: #7c3aed; }
    .status-badge.approver .dot { background: #7c3aed; }
    .status-badge.standard { background: var(--bg); color: var(--muted); }

    /* Action dropdown */
    .action-wrap { position: relative; }

    .action-trigger {
        width: 32px; height: 32px; border: 1.5px solid var(--border); border-radius: 8px;
        background: var(--surface); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all .2s; color: var(--muted);
    }

    .action-trigger:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-lt); }
    .action-trigger .material-icons { font-size: 18px; }

    .action-menu {
        position: absolute; right: 0; top: calc(100% + 6px);
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius-sm); box-shadow: var(--shadow-lg);
        min-width: 150px; z-index: 100; overflow: hidden;
        display: none; animation: menuIn .15s ease;
    }

    @keyframes menuIn { from { opacity:0; transform:translateY(-6px) scale(.97); } to { opacity:1; transform:translateY(0) scale(1); } }

    .action-menu.open { display: block; }

    .action-menu a {
        display: flex; align-items: center; gap: 8px;
        padding: 10px 14px; font-size: 13px; color: var(--ink);
        text-decoration: none; transition: background .15s;
    }

    .action-menu a .material-icons { font-size: 15px; color: var(--muted); }
    .action-menu a:hover { background: var(--accent-lt); color: var(--accent); }
    .action-menu a:hover .material-icons { color: var(--accent); }

    /* ── Edit Modal ──────────────────────────────────────────── */
    .modal-backdrop-custom {
        position: fixed; inset: 0; background: rgba(0,0,0,.45);
        backdrop-filter: blur(4px); z-index: 8000;
        display: none; align-items: flex-start; justify-content: center;
        padding: 40px 20px; overflow-y: auto;
    }

    .modal-backdrop-custom.open { display: flex; }

    .modal-card {
        background: var(--surface); border-radius: 20px; width: 100%; max-width: 700px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        animation: fadeUp .3s cubic-bezier(.22,.61,.36,1) both; margin: auto;
        overflow: hidden;
    }

    .modal-header {
        display: flex; align-items: center; gap: 12px;
        padding: 18px 24px; border-bottom: 1px solid var(--border);
    }

    .modal-header-icon {
        width: 34px; height: 34px; border-radius: 9px; background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .modal-header-icon .material-icons { font-size: 17px; color: var(--accent); }
    .modal-header-title { font-family: var(--font-head); font-size: 15px; font-weight: 700; color: var(--ink); flex: 1; }

    .modal-close-btn {
        width: 30px; height: 30px; border: 1.5px solid var(--border); border-radius: 8px;
        background: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: var(--muted); transition: all .2s;
    }

    .modal-close-btn:hover { color: var(--ink); border-color: #9ca3af; background: var(--bg); }
    .modal-close-btn .material-icons { font-size: 17px; }

    .modal-body { padding: 24px; overflow-y: auto; max-height: 70vh; }

    .modal-footer {
        display: flex; align-items: center; justify-content: flex-end; gap: 10px;
        padding: 14px 24px; border-top: 1px solid var(--border); background: #fafafa;
    }

    /* ── Modal form ──────────────────────────────────────────── */
    .modal-section-label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .08em; color: var(--muted); margin-bottom: 14px;
        display: flex; align-items: center; gap: 8px;
    }

    .modal-section-label::after { content: ''; flex: 1; height: 1px; background: var(--border); }

    .mgrid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 14px 16px; margin-bottom: 20px; }

    .mc-4 { grid-column: span 4; }
    .mc-6 { grid-column: span 6; }
    .mc-12 { grid-column: span 12; }

    @media (max-width: 640px) {
        .mc-4, .mc-6 { grid-column: span 12; }
        .modal-body { padding: 16px; }
    }

    .mfield { display: flex; flex-direction: column; gap: 5px; }

    .mfield label { font-size: 12.5px; font-weight: 500; color: #374151; }
    .mfield label .req { color: var(--danger); margin-left: 2px; }

    .mfield input, .mfield select {
        height: 40px; padding: 0 12px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body);
        font-size: 14px; color: var(--ink); outline: none; width: 100%;
        appearance: none; -webkit-appearance: none;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }

    .mfield input:focus, .mfield select:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,219,.1);
    }

    .mfield input[readonly] { background: #f3f4f8; color: var(--muted); cursor: not-allowed; }

    /* Avatar section */
    .avatar-edit {
        display: flex; align-items: center; gap: 14px; margin-bottom:20px;
    }

    .avatar-current {
        width: 56px; height: 56px; border-radius: 50%;
        border: 2px solid var(--border); object-fit: cover; flex-shrink: 0; display:none;
    }

    .avatar-upload-label {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px; border: 1.5px solid var(--border);
        border-radius: var(--radius-sm); font-size: 13px; font-weight: 500;
        color: var(--muted); cursor: pointer; background: #fafafa;
        transition: all .2s;
    }

    .avatar-upload-label:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-lt); }
    .avatar-upload-label .material-icons { font-size: 15px; }
    .avatar-hint { font-size: 11.5px; color: var(--muted); margin-top: 4px; }

    /* Payroll chips */
    .payroll-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom:20px; }

    .payroll-chip { position: relative; }
    .payroll-chip input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; }

    .payroll-chip label {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px; border: 1.5px solid var(--border);
        border-radius: 100px; font-size: 13px; font-weight: 500;
        color: var(--muted); cursor: pointer; background: #fafafa; transition: all .2s;
    }

    .payroll-chip input:checked + label { border-color: var(--accent); color: var(--accent); background: var(--accent-lt); }
    .payroll-chip label:hover { border-color: #9ca3af; color: var(--ink); }

    /* Approver chip */
    .approver-chip { position: relative; display: inline-block; }
    .approver-chip input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; }

    .approver-chip label {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 16px; border: 1.5px solid var(--border);
        border-radius: var(--radius-sm); font-size: 13.5px; font-weight: 500;
        color: var(--muted); cursor: pointer; background: #fafafa; transition: all .2s;
    }

    .approver-chip label .material-icons { font-size: 16px; }
    .approver-chip input:checked + label { border-color: var(--accent); color: var(--accent); background: var(--accent-lt); }

    /* Password section toggle */
    .pw-toggle-check { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px; font-weight: 500; color: var(--ink); }
    .pw-toggle-check input[type="checkbox"] { accent-color: var(--accent); width: 15px; height: 15px; }

    /* Password with eye + generate */
    .pw-wrap {
        display: flex; align-items: stretch;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        overflow: hidden; background: #fafafa;
        transition: border-color .2s, box-shadow .2s;
    }

    .pw-wrap:focus-within {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,219,.1);
    }

    .pw-wrap input {
        flex: 1; height: 40px; padding: 0 12px; border: none !important;
        background: transparent !important; box-shadow: none !important;
        font-family: var(--font-body); font-size: 14px; color: var(--ink); outline: none;
    }

    .pw-btn {
        width: 36px; height: 40px; border: none; border-left: 1px solid var(--border);
        background: #f3f4f8; cursor: pointer; color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        transition: background .2s, color .2s; flex-shrink: 0;
    }

    .pw-btn:hover { background: #e9ecef; color: var(--ink); }
    .pw-btn .material-icons { font-size: 16px; }

    .pw-gen-btn {
        display: flex; align-items: center; gap: 5px;
        padding: 0 12px; height: 40px; border: none;
        border-left: 1px solid var(--border); background: var(--accent-lt);
        color: var(--accent); font-family: var(--font-body);
        font-size: 12px; font-weight: 600; cursor: pointer;
        transition: background .2s; white-space: nowrap; flex-shrink: 0;
    }

    .pw-gen-btn:hover { background: #dbeafe; }
    .pw-gen-btn .material-icons { font-size: 14px; }

    /* Strength bar */
    .pw-strength-bar { height: 4px; background: #e5e7eb; border-radius: 100px; overflow: hidden; margin: 6px 0 3px; }
    .pw-strength-fill { height: 100%; border-radius: 100px; width: 0%; transition: width .3s, background .3s; }
    .pw-strength-label { font-size: 11px; color: var(--muted); }

    /* Match indicator */
    .pw-match { font-size: 12px; margin-top: 4px; }
    .pw-match.ok  { color: var(--success); }
    .pw-match.err { color: var(--danger); }

    /* ── Buttons ─────────────────────────────────────────────── */
    .btn {
        height: 40px; padding: 0 18px; border: none;
        border-radius: var(--radius-sm); font-family: var(--font-body);
        font-size: 13.5px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 7px;
        transition: transform .2s, box-shadow .2s, filter .2s; letter-spacing: .01em;
    }

    .btn .material-icons { font-size: 16px; }
    .btn:hover:not(:disabled) { transform: translateY(-1px); }
    .btn:active:not(:disabled) { transform: translateY(0); }
    .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

    .btn-save { background: linear-gradient(135deg, #059669, #10b981); color: #fff; box-shadow: 0 4px 14px rgba(5,150,105,.25); }
    .btn-save:hover:not(:disabled) { box-shadow: 0 7px 20px rgba(5,150,105,.35); filter: brightness(1.05); }

    .btn-ghost { background: var(--surface); color: var(--muted); border: 1.5px solid var(--border); }
    .btn-ghost:hover:not(:disabled) { color: var(--ink); border-color: #9ca3af; }

    /* ── Toast ───────────────────────────────────────────────── */
    .toast-wrap {
        position: fixed; top: 20px; right: 20px; z-index: 9999;
        display: flex; flex-direction: column; gap: 10px;
    }

    .toast-msg {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 18px; border-radius: 14px;
        min-width: 280px; max-width: 360px; font-size: 14px; font-weight: 500;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        animation: toastIn .35s cubic-bezier(.22,.61,.36,1) both; cursor: pointer;
    }

    .toast-msg.leaving { animation: toastOut .3s ease forwards; }

    @keyframes toastIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
    @keyframes toastOut { to { opacity:0; transform:translateX(40px); } }

    .toast-msg.success { background: var(--success-lt); color: #065f46; }
    .toast-msg.danger  { background: var(--danger-lt);  color: #991b1b; }
    .toast-msg.warning { background: #fffbeb; color: #92400e; }
    .toast-msg .material-icons { font-size: 20px; flex-shrink: 0; }

    @media (max-width: 768px) {
        .users-page { padding: 18px 14px; }
    }
    #edituserModal {
    display: contents;
}
#password-reset-section{display:none;}
.profilepic{display:none;}
.marginbot{margin-bottom:20px;}
.passreset{font-weight:400;text-transform:none;letter-spacing:0;font-size:11.5px;color:var(--muted);}
</style>

<div class="users-page">

    <div class="page-header">
        <div class="page-heading">
            <h1>Manage Users</h1>
            <p>View and manage all system user accounts.</p>
        </div>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <!-- Table card -->
    <div class="table-card">

        <div class="table-toolbar">
            <div class="toolbar-left">
                <div class="toolbar-icon"><span class="material-icons">manage_accounts</span></div>
                <div>
                    <div class="toolbar-title">All Users</div>
                    <div class="toolbar-subtitle" id="recordCount">Loading…</div>
                </div>
            </div>
            <div class="toolbar-right">
                <div class="search-box">
                    <span class="material-icons">search</span>
                    <input type="text" id="dt-search" placeholder="Search users…">
                </div>
                <select id="dt-length" class="page-length-select">
                    <option value="10">10 / page</option>
                    <option value="25" selected>25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </select>
            </div>
        </div>

        <div class="table-wrap">
            <table id="users-table" class="stripe hover nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>Password Exp</th>
                        <th>Payroll</th>
                        <th>Approver</th>
                        <th class="datatable-nosort">Option</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>

<!-- Edit User Modal — pure custom, no Bootstrap dependency -->
<div class="modal-backdrop-custom" id="edituserModalBackdrop">
    {{-- NOTE: id="edituserModal" kept here so musers.js $(...).modal() shim works --}}
    <div id="edituserModal">
        <div class="modal-card">

            <div class="modal-header">
                <div class="modal-header-icon"><span class="material-icons">manage_accounts</span></div>
                <span class="modal-header-title">Edit User</span>
                <button class="modal-close-btn" data-dismiss="modal" id="modalCloseBtn">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="edituserForm" id="edituserForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="form_type" value="edit_user">
                    <input type="hidden" name="user_id" id="edit_user_id">

                    <!-- Account info -->
                    <p class="modal-section-label">Account Information</p>

                    <div class="mgrid">
                        <div class="mfield mc-4">
                            <label>Name <span class="req">*</span></label>
                            <input type="text" id="eusername" name="name" required>
                        </div>
                        <div class="mfield mc-4">
                            <label>User ID</label>
                            <input type="text" id="edit_userId" name="userId" readonly>
                        </div>
                        <div class="mfield mc-4">
                            <label>Email <span class="req">*</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>

                    <!-- Profile photo -->
                    <p class="modal-section-label">Profile Photo</p>

                    <div class="avatar-edit" >
                        <img id="current-photo" src="" alt="Photo"
                             class="avatar-current"
                             onerror="this.style.display='none'">
                        <div>
                            <label class="avatar-upload-label" for="profilepic">
                                <span class="material-icons">upload</span> Choose Photo
                            </label>
                            <input type="file" id="profilepic" class="profilepic" name="profilepic"
                                   accept="image/*"
                                   >
                            <div class="avatar-hint">Max 2 MB · JPG, PNG, GIF</div>
                        </div>
                    </div>

                    <!-- Payroll access -->
                    <p class="modal-section-label">Payroll Access</p>

                    <div id="payroll-checkboxes" class="payroll-chips">
                        {{-- Populated dynamically by musers.js --}}
                    </div>

                    <!-- Approver -->
                    <div class="marginbot">
    <div class="approver-chip">
        <input type="checkbox" id="approvelvl" name="approvelvl" value="YES">
        <label for="approvelvl">
            <span class="material-icons">verified_user</span>
            Is Approver
        </label>
    </div>
    <div class="approver-chip">
        <input type="checkbox" id="mfa" name="mfa" value="ON">
        <label for="mfa">
            <span class="material-icons">shield</span>
            Two Factor Enabled
        </label>
    </div>
    <div class="approver-chip">
        <input type="checkbox" id="activeacc" name="activeacc" value="ACTIVE">
        <label for="activeacc">
            <span class="material-icons">check_circle</span>
            Active Account
        </label>
    </div>
</div>

                    <!-- Password reset -->
                    <p class="modal-section-label passreset">Password Reset <span>(optional)</span></p>

                    <div class="marginbot">
                        <label class="pw-toggle-check">
                            <input type="checkbox" id="enable_password_reset">
                            Change user password
                        </label>
                    </div>

                    <div id="password-reset-section">
                        <div class="mgrid">
                            <div class="mfield mc-6">
                                <label>New Password <span class="req">*</span></label>
                                <div class="pw-wrap">
                                    <input type="password" id="newpass" name="newpass" minlength="8"
                                           placeholder="Min. 8 characters" autocomplete="new-password"
                                          >
                                    <button type="button" class="pw-btn" id="togglePassword"
                                            onclick="toggleEditPw('newpass','editEye1')">
                                        <span class="material-icons" id="editEye1">visibility</span>
                                    </button>
                                    <button type="button" class="pw-gen-btn" id="generate-password"
                                           >
                                        <span class="material-icons">auto_fix_high</span> Generate
                                    </button>
                                </div>
                                <div class="pw-strength-bar"><div class="pw-strength-fill" id="editStrengthFill"></div></div>
                                <span class="pw-strength-label" id="editStrengthLabel"></span>
                            </div>

                            <div class="mfield mc-6">
                                <label>Confirm Password <span class="req">*</span></label>
                                <div class="pw-wrap">
                                    <input type="password" id="newpass_confirmation" name="newpass_confirmation"
                                           placeholder="Re-enter password" autocomplete="new-password"
                                           >
                                    <button type="button" class="pw-btn"
                                            onclick="toggleEditPw('newpass_confirmation','editEye2')">
                                        <span class="material-icons" id="editEye2">visibility</span>
                                    </button>
                                </div>
                                <div class="pw-match" id="password-match-message"></div>
                            </div>
                        </div>
                    </div>

                
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" data-dismiss="modal" id="modalCancelBtn">
                    <span class="material-icons">close</span> Cancel
                </button>
                <button type="submit" class="btn btn-save" id="save-user-btn">
                    <span class="material-icons">save</span> Save Changes
                </button>
            </div>
            </form>

        </div>
    </div>
</div>

<script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>

<script nonce="{{ $cspNonce }}">
    const amanage    = '{{ route("musers.data") }}';
    window.APP_URL      = "{{ url('/') }}";
    window.STORAGE_URL  = "{{ asset('storage') }}";
    window.UPLOADS_URL  = "{{ asset('uploads') }}";
    const getpayroll = '{{ route("getPayroll.types") }}';
    const getuser    = '{{ route("get.user", ":id") }}';
    const updateuser = '{{ route("update.user", ":id") }}';
</script>

<script nonce="{{ $cspNonce }}">
/* ════════════════════════════════════════════════════════
   jQuery shim — intercepts $(...).modal('show'/'hide')
   so musers.js works WITHOUT Bootstrap's modal JS.
   MUST run BEFORE musers.js is called.
════════════════════════════════════════════════════════ */
(function ($) {
    const backdrop = document.getElementById('edituserModalBackdrop');

    function openCustomModal () {
        backdrop.classList.add('open');
        // Reset password section each open
        document.getElementById('enable_password_reset').checked = false;
        document.getElementById('password-reset-section').style.display = 'none';
    }

    function closeCustomModal () {
        backdrop.classList.remove('open');
    }

    // Patch $.fn.modal so musers.js calls work seamlessly
    $.fn.modal = function (action) {
        const el = this[0];
        if (!el) return this;

        if (action === 'show') {
            openCustomModal();
            // Fire expected Bootstrap events so any .on('shown.bs.modal') listeners work
            $(el).trigger('show.bs.modal').trigger('shown.bs.modal');
        } else if (action === 'hide') {
            closeCustomModal();
            $(el).trigger('hide.bs.modal').trigger('hidden.bs.modal');
        }
        return this;
    };

    // Close on backdrop click
    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) closeCustomModal();
    });

    // Expose globals so close buttons work
    window._closeEditModal = closeCustomModal;

})(jQuery);
</script>

{{-- musers.js loads here — AFTER the shim so $.fn.modal is already patched --}}
<script src="{{ asset('js/musers.js') }}"></script>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function () {

    /* ── Close buttons ───────────────────────────────────── */
    ['modalCloseBtn','modalCancelBtn'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', function () {
            window._closeEditModal();
        });
    });

    /* ── Escape key ──────────────────────────────────────── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window._closeEditModal();
    });

    /* ── DataTable ───────────────────────────────────────── */
    

    /* ── Custom search ───────────────────────────────────── */
    let searchTimer;
    document.getElementById('dt-search').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => table.search(this.value).draw(), 350);
    });

    /* ── Page length ─────────────────────────────────────── */
    document.getElementById('dt-length').addEventListener('change', function () {
        table.page.len(parseInt(this.value)).draw();
    });

    /* ── Action dropdown ─────────────────────────────────── */
    // ── Action menu toggle (replaces inline onclick) ──────────────────
document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-action="toggle-menu"]');

    if (trigger) {
        e.stopPropagation();
        const menu = trigger.closest('.action-wrap').querySelector('.action-menu');
        const isOpen = menu.classList.contains('open');

        // Close all open menus first
        document.querySelectorAll('.action-menu.open').forEach(function (m) {
            m.classList.remove('open');
        });

        // Toggle the clicked one
        if (!isOpen) menu.classList.add('open');
        return;
    }

    // Click outside — close all menus
    document.querySelectorAll('.action-menu.open').forEach(function (m) {
        m.classList.remove('open');
    });
});

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.action-wrap'))
            document.querySelectorAll('.action-menu.open').forEach(m => m.classList.remove('open'));
    });

    /* ── Password reset toggle ───────────────────────────── */
    document.getElementById('enable_password_reset').addEventListener('change', function () {
        document.getElementById('password-reset-section').style.display = this.checked ? 'block' : 'none';
    });

    /* ── Password visibility toggle ─────────────────────── */
    window.toggleEditPw = function (inputId, iconId) {
        const inp  = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isTxt = inp.type === 'text';
        inp.type = isTxt ? 'password' : 'text';
        if (icon) icon.textContent = isTxt ? 'visibility' : 'visibility_off';
    };

    /* ── Password strength ───────────────────────────────── */
    window.editPwStrength = function () {
        const pw    = document.getElementById('newpass').value;
        const fill  = document.getElementById('editStrengthFill');
        const label = document.getElementById('editStrengthLabel');
        let score = 0;
        if (pw.length >= 8)           score++;
        if (/[A-Z]/.test(pw))         score++;
        if (/[a-z]/.test(pw))         score++;
        if (/[0-9]/.test(pw))         score++;
        if (/[^A-Za-z0-9]/.test(pw))  score++;
        const levels = [
            {pct:0,color:'',text:''},
            {pct:20,color:'#ef4444',text:'Very weak'},
            {pct:40,color:'#f97316',text:'Weak'},
            {pct:60,color:'#eab308',text:'Fair'},
            {pct:80,color:'#22c55e',text:'Strong'},
            {pct:100,color:'var(--success)',text:'Very strong'}
        ];
        const lvl = levels[Math.min(score, 5)];
        fill.style.width      = lvl.pct + '%';
        fill.style.background = lvl.color;
        label.textContent     = lvl.text;
        label.style.color     = lvl.color || 'var(--muted)';
        checkEditPwMatch();
    };

    window.checkEditPwMatch = function () {
        const pw  = document.getElementById('newpass').value;
        const cfm = document.getElementById('newpass_confirmation').value;
        const el  = document.getElementById('password-match-message');
        if (!cfm) { el.textContent = ''; return; }
        if (pw === cfm) { el.className = 'pw-match ok';  el.textContent = '✓ Passwords match'; }
        else            { el.className = 'pw-match err'; el.textContent = '✗ Passwords do not match'; }
    };

    /* ── Generate password ───────────────────────────────── */
    window.generateEditPassword = function () {
        const upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        const lower = 'abcdefghjkmnpqrstuvwxyz';
        const nums  = '23456789';
        const syms  = '!@#$%^&*_-+=?';
        const all   = upper + lower + nums + syms;
        let pw = [
            upper[Math.floor(Math.random() * upper.length)],
            lower[Math.floor(Math.random() * lower.length)],
            nums [Math.floor(Math.random() * nums.length)],
            syms [Math.floor(Math.random() * syms.length)],
        ];
        for (let i = pw.length; i < 14; i++) pw.push(all[Math.floor(Math.random() * all.length)]);
        pw = pw.sort(() => Math.random() - .5).join('');
        document.getElementById('newpass').value = pw;
        document.getElementById('newpass_confirmation').value = pw;
        document.getElementById('newpass').type = 'text';
        document.getElementById('newpass_confirmation').type = 'text';
        document.getElementById('editEye1').textContent = 'visibility_off';
        document.getElementById('editEye2').textContent = 'visibility_off';
        editPwStrength();
        showToast('success', 'Password Generated', 'A strong password has been set.');
    };

    /* ── Profile photo preview ───────────────────────────── */
    window.previewEditPhoto = function (input) {
        if (!input.files.length) return;
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            showToast('danger', 'File too large', 'Please choose an image under 2 MB.');
            input.value = ''; return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById('current-photo');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(file);
    };

    /* ── Toast ───────────────────────────────────────────── */
    function showToast (type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber' };
        const t = document.createElement('div');
        t.className = `toast-msg ${type}`;
        t.innerHTML = `<span class="material-icons">${icons[type]||'info'}</span>
                       <div><strong>${title}</strong> ${message}</div>`;
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }

    window.showMessage = function (msg, type) {
        showToast(type || 'info', 'Notice', msg);
    };

});
</script>

</x-custom-admin-layout>






