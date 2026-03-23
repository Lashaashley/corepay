<x-custom-admin-layout>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<style>
    /* ── Page-specific — tokens from corepay.css ─────────────── */

    .roles-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }

    /* ── Tab bar ──────────────────────────────────────────────── */
    .tab-bar {
        display: flex; gap: 4px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px 16px 0 0;
        padding: 10px 14px 0;
        border-bottom: none; flex-wrap: wrap;
    }

    .tab-btn {
        position: relative; padding: 9px 18px 11px;
        background: none; border: none;
        border-radius: var(--radius-sm) var(--radius-sm) 0 0;
        font-family: var(--font-body); font-size: 13px; font-weight: 500; color: var(--muted);
        cursor: pointer; display: flex; align-items: center; gap: 6px;
        transition: color .2s, background .2s; white-space: nowrap;
    }

    .tab-btn .material-icons { font-size: 16px; }
    .tab-btn:hover { color: var(--ink); background: var(--bg); }

    .tab-btn.active { color: var(--accent); font-weight: 600; background: var(--bg); }

    .tab-btn.active::after {
        content: ''; position: absolute; bottom: 0; left: 12px; right: 12px;
        height: 2.5px; border-radius: 2px 2px 0 0;
        background: linear-gradient(90deg, #1a56db, #6366f1);
    }

    /* ── Tab body ─────────────────────────────────────────────── */
    .tab-body {
        background: var(--surface); border: 1px solid var(--border);
        border-top: none; border-radius: 0 0 16px 16px; box-shadow: var(--shadow);
    }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; animation: fadeUp .35s cubic-bezier(.22,.61,.36,1) both; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Two-column layout (Create Roles tab) ─────────────────── */
    .roles-layout { display: grid; grid-template-columns: 320px 1fr; gap: 20px; padding: 24px; }

    @media (max-width: 860px) { .roles-layout { grid-template-columns: 1fr; } }

    /* ── Card ────────────────────────────────────────────────── */
    .r-card {
        background: var(--bg); border: 1px solid var(--border);
        border-radius: 14px; overflow: hidden;
    }

    .r-card-head {
        display: flex; align-items: center; gap: 9px;
        padding: 12px 16px; background: var(--surface);
        border-bottom: 1px solid var(--border);
    }

    .r-card-icon {
        width: 30px; height: 30px; border-radius: 8px; background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .r-card-icon .material-icons { font-size: 15px; color: var(--accent); }
    .r-card-icon.purple { background: #f3f0ff; }
    .r-card-icon.purple .material-icons { color: #7c3aed; }
    .r-card-icon.green  { background: var(--success-lt); }
    .r-card-icon.green  .material-icons { color: var(--success); }

    .r-card-title { font-family: var(--font-head); font-size: 14px; font-weight: 700; color: var(--ink); }
    .r-card-body  { padding: 16px; }

    /* ── Form fields ─────────────────────────────────────────── */
    .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }

    .field label { font-size: 12.5px; font-weight: 500; color: #374151; }
    .field label .req { color: var(--danger); margin-left: 2px; }

    .field input, .field textarea, .field select {
        padding: 9px 12px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: var(--surface); font-family: var(--font-body);
        font-size: 14px; color: var(--ink); outline: none; width: 100%;
        appearance: none; -webkit-appearance: none;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }

    .field input { height: 40px; }
    .field textarea { resize: vertical; min-height: 80px; }

    .field input:focus, .field textarea:focus, .field select:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,219,.1);
    }

    .field-error { font-size: 12px; color: var(--danger); margin-top: 2px; }

    /* Select arrow */
    .select-wrap { position: relative; }
    .select-wrap::after {
        content: 'expand_more'; font-family: 'Material Icons'; font-size: 17px;
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        color: var(--muted); pointer-events: none;
    }
    .select-wrap select { height: 40px; padding: 0 32px 0 12px; }

    /* ── Buttons ─────────────────────────────────────────────── */
    .btn {
        height: 40px; padding: 0 18px; border: none; border-radius: var(--radius-sm);
        font-family: var(--font-body); font-size: 13.5px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 7px;
        transition: transform .2s, box-shadow .2s, filter .2s; letter-spacing: .01em;
        text-decoration: none;
    }

    .btn .material-icons { font-size: 16px; }
    .btn:hover:not(:disabled) { transform: translateY(-1px); }
    .btn:active:not(:disabled) { transform: translateY(0); }
    .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

    .btn-create { background: linear-gradient(135deg, #1a56db, #4f46e5); color: #fff; box-shadow: 0 3px 10px rgba(26,86,219,.25); }
    .btn-create:hover:not(:disabled) { box-shadow: 0 6px 16px rgba(26,86,219,.35); filter: brightness(1.05); }

    .btn-assign { background: linear-gradient(135deg, #059669, #10b981); color: #fff; box-shadow: 0 3px 10px rgba(5,150,105,.22); }
    .btn-assign:hover:not(:disabled) { box-shadow: 0 6px 16px rgba(5,150,105,.32); filter: brightness(1.05); }

    .btn-select { background: var(--accent-lt); color: var(--accent); border: 1.5px solid #bfdbfe; }
    .btn-select:hover:not(:disabled) { background: #dbeafe; }

    .btn-clear { background: #fffbeb; color: #92400e; border: 1.5px solid #fde68a; }
    .btn-clear:hover:not(:disabled) { background: #fef3c7; }

    .btn-report { background: #f3f0ff; color: #7c3aed; border: 1.5px solid #ddd6fe; }
    .btn-report:hover:not(:disabled) { background: #ede9fe; }

    /* ── Roles table ─────────────────────────────────────────── */
    .roles-table { width: 100%; border-collapse: collapse; font-size: 13.5px; font-family: var(--font-body); }

    .roles-table thead th {
        background: #f9fafb; color: var(--muted); font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: .06em;
        padding: 10px 14px; border-bottom: 1px solid var(--border); white-space: nowrap;
    }

    .roles-table tbody td {
        padding: 11px 14px; border-bottom: 1px solid #f3f4f8;
        vertical-align: middle; color: var(--ink);
    }

    .roles-table tbody tr:last-child td { border-bottom: none; }
    .roles-table tbody tr:hover td { background: #f8faff; }

    .roles-table .role-desc { font-size: 12px; color: var(--muted); }

    /* Delete button */
    .btn-del {
        width: 30px; height: 30px; border: 1.5px solid var(--border); border-radius: 7px;
        background: var(--surface); cursor: pointer; display: flex; align-items: center;
        justify-content: center; color: var(--muted); transition: all .2s;
    }
    .btn-del:hover { border-color: var(--danger); color: var(--danger); background: var(--danger-lt); }
    .btn-del .material-icons { font-size: 15px; }

    /* ── Pagination ──────────────────────────────────────────── */
    #pagination-controls { display: flex; gap: 6px; justify-content: flex-end; padding-top: 10px; }

    #pagination-controls button {
        height: 30px; min-width: 30px; padding: 0 10px;
        border: 1.5px solid var(--border); border-radius: 7px; background: var(--surface);
        font-family: var(--font-body); font-size: 12.5px; color: var(--muted);
        cursor: pointer; transition: all .2s;
    }

    #pagination-controls button:hover { background: var(--accent-lt); border-color: var(--accent); color: var(--accent); }
    #pagination-controls button.active { background: var(--accent); border-color: transparent; color: #fff; }

    /* ── Modules panel ───────────────────────────────────────── */
    .modules-panel-body {
        padding: 16px 20px;
    }

    .modules-top-row {
        display: flex; align-items: flex-end; justify-content: space-between;
        gap: 16px; margin-bottom: 16px; flex-wrap: wrap;
    }

    .modules-scroll {
        max-height: 420px; overflow-y: auto; padding: 2px;
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        background: var(--bg); margin-bottom: 16px;
    }

    .modules-scroll::-webkit-scrollbar { width: 4px; }
    .modules-scroll::-webkit-scrollbar-track { background: transparent; }
    .modules-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

    /* ── Module / button tree ────────────────────────────────── */
    .module-group { padding: 10px 12px; border-bottom: 1px solid var(--border); }
    .module-group:last-child { border-bottom: none; }

    /* Parent row */
    .module-parent {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 10px; border-radius: 8px;
        background: var(--surface); border: 1px solid var(--border);
        margin-bottom: 6px; cursor: pointer; transition: background .15s;
    }

    .module-parent:hover { background: var(--accent-lt); border-color: #bfdbfe; }

    .module-parent input[type="checkbox"] { display: none; }

    .m-checkbox {
        width: 18px; height: 18px; border-radius: 5px;
        border: 2px solid var(--border); background: var(--surface);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; transition: all .2s;
    }

    .m-checkbox .material-icons { font-size: 12px; color: #fff; opacity: 0; }

    .module-parent.checked .m-checkbox { border-color: var(--accent); background: var(--accent); }
    .module-parent.checked .m-checkbox .material-icons { opacity: 1; }
    .module-parent.checked { background: var(--accent-lt); border-color: #bfdbfe; }

    .m-parent-icon {
        width: 26px; height: 26px; border-radius: 7px;
        background: var(--accent-lt); display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .m-parent-icon .material-icons { font-size: 14px; color: var(--accent); }
    .m-parent-icon img { width: 16px; height: 16px; object-fit: contain; }

    .m-parent-name { font-size: 13.5px; font-weight: 600; color: var(--ink); flex: 1; }
    .module-parent.checked .m-parent-name { color: var(--accent); }

    /* Children */
    .module-children { padding-left: 20px; display: flex; flex-direction: column; gap: 4px; }

    .module-child {
        display: flex; align-items: center; gap: 8px;
        padding: 6px 10px; border-radius: 7px; background: var(--surface);
        border: 1px solid var(--border); cursor: pointer; transition: background .15s;
    }

    .module-child:hover { background: #f3f4f8; }

    .module-child input[type="checkbox"] { display: none; }

    .module-child.checked .m-checkbox { border-color: var(--accent); background: var(--accent); }
    .module-child.checked .m-checkbox .material-icons { opacity: 1; }
    .module-child.checked { background: var(--accent-lt); border-color: #bfdbfe; }

    .m-child-name { font-size: 13px; font-weight: 500; color: var(--ink); flex: 1; }
    .module-child.checked .m-child-name { color: var(--accent); }

    /* ── Actions row ─────────────────────────────────────────── */
    .modules-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

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

    @media (max-width: 640px) {
        .roles-page { padding: 18px 14px; }
        .modules-top-row { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="roles-page">

    <div class="page-heading">
        <h1>Roles Management</h1>
       
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    @if(session('success'))
        <div style="display:flex;align-items:center;gap:8px;padding:12px 16px;background:var(--success-lt);
                    border:1.5px solid #6ee7b7;border-radius:var(--radius-sm);margin-bottom:16px;
                    font-size:13.5px;color:#065f46;">
            <span class="material-icons" style="font-size:17px;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Tab bar ──────────────────────────────────────────── --}}
    <div class="tab-bar">
        <button class="tab-btn active" data-tab="deductions">
            <span class="material-icons">add_circle</span> Create Roles
        </button>
        <button class="tab-btn" data-tab="summaries" id="summaries-tab">
            <span class="material-icons">admin_panel_settings</span> Module Allocation
        </button>
    </div>

    <div class="tab-body">

        {{-- ═══════════════════════════════════════
             TAB 1 — CREATE ROLES
        ═══════════════════════════════════════ --}}
        <div class="tab-panel active" id="panel-deductions">
            <div class="roles-layout">

                {{-- Left: Create role form --}}
                <div class="r-card">
                    <div class="r-card-head">
                        <div class="r-card-icon"><span class="material-icons">add_circle</span></div>
                        <span class="r-card-title">New Role</span>
                    </div>
                    <div class="r-card-body">
                        <form id="rolesform">
                            @csrf
                            <div class="field">
                                <label>Role Name <span class="req">*</span></label>
                                <input name="rolename" id="rolename" type="text"
                                       placeholder="e.g. Payroll Admin" required autocomplete="off">
                                <span class="field-error" id="rolename-error"></span>
                            </div>
                            <div class="field">
                                <label>Description</label>
                                <textarea name="rdesc" id="rdesc" placeholder="Brief description of this role's permissions…" autocomplete="off"></textarea>
                            </div>
                            <button type="submit" class="btn btn-create">
                                <span class="material-icons">save</span> Create Role
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Right: Roles table --}}
                <div class="r-card">
                    <div class="r-card-head">
                        <div class="r-card-icon purple"><span class="material-icons">list</span></div>
                        <span class="r-card-title">All Roles</span>
                    </div>
                    <div class="r-card-body">
                        <div style="overflow-x:auto;">
                            <table class="roles-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody id="roles-table-body">
                                    <tr>
                                        <td colspan="4" style="text-align:center;padding:28px;color:var(--muted);">
                                            Loading roles…
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination-controls"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════
             TAB 2 — MODULE ALLOCATION
        ═══════════════════════════════════════ --}}
        <div class="tab-panel" id="panel-summaries">
            <div class="modules-panel-body">

                <form id="moduleAssignForm">
                    @csrf

                    {{-- Role selector --}}
                    <div class="modules-top-row">
                        <div class="field" style="min-width:260px;margin-bottom:0;">
                            <label>Select Role <span class="req">*</span></label>
                            <div class="select-wrap">
                                <select name="roleid" id="roleid" required>
                                    <option value="">— Select Role —</option>
                                </select>
                            </div>
                            <span class="field-error" id="roleid-error"></span>
                        </div>
                    </div>

                    {{-- Modules tree --}}
                    <div class="modules-scroll" id="modulesContainer">
                        @php
                            function renderButtonsModern($buttons, $parentId = null) {
                                if ($parentId === null) {
                                    // Top level: render parent items with their children
                                    $html = '';
                                    foreach ($buttons as $button) {
                                        if ($button->parentid == null && $button->isparent == 'YES') {
                                            $html .= '<div class="module-group">';

                                            // Parent row
                                            $html .= '<div class="module-parent" onclick="toggleModule(this)">';
                                            $html .= '<input class="module-checkbox" type="checkbox" name="modules[]" value="' . $button->ID . '" id="module' . $button->ID . '" data-parent="true" data-button-id="' . $button->ID . '">';
                                            $html .= '<div class="m-checkbox"><span class="material-icons">check</span></div>';

                                            // Icon
                                            if ($button->icon) {
                                                if (str_contains($button->icon, '.png') || str_contains($button->icon, '.jpg') || str_contains($button->icon, '.svg')) {
                                                    $html .= '<div class="m-parent-icon"><img src="' . asset($button->icon) . '" alt=""></div>';
                                                } else {
                                                    $html .= '<div class="m-parent-icon"><span class="material-icons" style="font-size:14px;color:var(--accent);">folder</span></div>';
                                                }
                                            } else {
                                                $html .= '<div class="m-parent-icon"><span class="material-icons" style="font-size:14px;color:var(--accent);">folder</span></div>';
                                            }

                                            $html .= '<span class="m-parent-name">' . htmlspecialchars($button->Bname) . '</span>';
                                            $html .= '</div>'; // /module-parent

                                            // Children
                                            $html .= '<div class="module-children">';
                                            foreach ($buttons as $child) {
                                                if ($child->parentid == $button->ID) {
                                                    $html .= '<div class="module-child" onclick="toggleModule(this)">';
                                                    $html .= '<input class="module-checkbox" type="checkbox" name="modules[]" value="' . $child->ID . '" id="module' . $child->ID . '" data-child-of="' . $button->ID . '">';
                                                    $html .= '<div class="m-checkbox"><span class="material-icons">check</span></div>';
                                                    $html .= '<span class="m-child-name">' . htmlspecialchars($child->Bname) . '</span>';
                                                    $html .= '</div>';
                                                }
                                            }
                                            $html .= '</div>'; // /module-children

                                            $html .= '</div>'; // /module-group
                                        }
                                    }
                                    return $html;
                                }
                                return '';
                            }

                            echo renderButtonsModern($buttons);
                        @endphp
                    </div>

                    {{-- Action row --}}
                    <div class="modules-actions">
                        <button type="button" class="btn btn-assign" id="assignBtn">
                            <span class="material-icons">assignment</span> Assign Modules
                        </button>
                        <button type="button" class="btn btn-select" id="selectAllBtn">
                            <span class="material-icons">done_all</span> Select All
                        </button>
                        <button type="button" class="btn btn-clear" id="deselectAllBtn">
                            <span class="material-icons">close</span> Deselect All
                        </button>
                        <a href="{{ route('roles.report') }}" class="btn btn-report" target="_blank">
                            <span class="material-icons">picture_as_pdf</span> Roles Report
                        </a>
                    </div>

                </form>

                <div id="alertContainer" class="mt-4"></div>

            </div>
        </div>

    </div>{{-- /tab-body --}}
</div>{{-- /roles-page --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Tab switching ─────────────────────────────────────── */
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const panel = document.getElementById('panel-' + this.dataset.tab);
            if (panel) panel.classList.add('active');
        });
    });

    /* Legacy openTab compatibility */
    window.openTab = function (event, tabId) {
        const btn = document.querySelector('[data-tab="' + tabId + '"]');
        if (btn) btn.click();
    };

    /* ── Module toggle ─────────────────────────────────────── */
    window.toggleModule = function (row) {
        const cb = row.querySelector('input[type="checkbox"]');
        if (!cb) return;

        cb.checked = !cb.checked;
        row.classList.toggle('checked', cb.checked);

        // If parent, sync children
        if (cb.dataset.parent === 'true') {
            const buttonId = cb.dataset.buttonId;
            document.querySelectorAll('[data-child-of="' + buttonId + '"]').forEach(function (childCb) {
                childCb.checked = cb.checked;
                const childRow = childCb.closest('.module-child');
                if (childRow) childRow.classList.toggle('checked', cb.checked);
            });
        }

        // If child, check if all siblings checked → auto-check parent
        if (cb.dataset.childOf) {
            const parentCb = document.querySelector('[data-button-id="' + cb.dataset.childOf + '"]');
            if (parentCb) {
                const siblings = document.querySelectorAll('[data-child-of="' + cb.dataset.childOf + '"]');
                const allChecked = Array.from(siblings).every(s => s.checked);
                parentCb.checked = allChecked;
                const parentRow = parentCb.closest('.module-parent');
                if (parentRow) parentRow.classList.toggle('checked', allChecked);
            }
        }
    };

    /* ── Select / deselect all ─────────────────────────────── */
    document.getElementById('selectAllBtn').addEventListener('click', function () {
        document.querySelectorAll('.module-checkbox').forEach(function (cb) {
            cb.checked = true;
            const row = cb.closest('.module-parent, .module-child');
            if (row) row.classList.add('checked');
        });
        showToast('success', 'Selected', 'All modules selected.');
    });

    document.getElementById('deselectAllBtn').addEventListener('click', function () {
        document.querySelectorAll('.module-checkbox').forEach(function (cb) {
            cb.checked = false;
            const row = cb.closest('.module-parent, .module-child');
            if (row) row.classList.remove('checked');
        });
        showToast('success', 'Cleared', 'All selections cleared.');
    });

    /* ── Load roles into select ────────────────────────────── */
    function loadRolesDropdown () {
        $.ajax({
            url:  "{{ route('roles.getDropdown') }}",
            type: 'GET',
            success: function (response) {
                const sel = $('#roleid');
                sel.empty().append('<option value="">— Select Role —</option>');
                (response.data || response).forEach(function (role) {
                    sel.append($('<option>', { value: role.ID, text: role.rolename }));
                });
            }
        });
    }

    loadRolesDropdown();

    /* ── Load roles table ──────────────────────────────────── */
    let currentPage = 1;

    function loadRolesTable (page) {
        currentPage = page;
        $.ajax({
            url:  "{{ route('roles.getall') }}",
            type: 'GET',
            data: { page: page },
            success: function (response) {
                const tbody = $('#roles-table-body');
                tbody.empty();

                const roles = response.data || response;

                if (!roles.length) {
                    tbody.html('<tr><td colspan="4" style="text-align:center;padding:28px;color:var(--muted);">No roles found.</td></tr>');
                    return;
                }

                roles.forEach(function (role) {
                    tbody.append(`
                        <tr>
                            <td>${role.ID}</td>
                            <td style="font-weight:600;">${role.rolename}</td>
                            <td><span class="role-desc">${role.rdesc || '—'}</span></td>
                            <td>
                                <button class="btn-del" onclick="deleteRole(${role.ID}, this)" title="Delete">
                                    <span class="material-icons">delete_outline</span>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                // Pagination
                if (response.last_page && response.last_page > 1) {
                    renderPagination(response.current_page, response.last_page);
                } else {
                    $('#pagination-controls').empty();
                }
            }
        });
    }

    function renderPagination (current, last) {
        const ctrl = $('#pagination-controls');
        ctrl.empty();
        for (let i = 1; i <= last; i++) {
            const btn = $('<button>').text(i).toggleClass('active', i === current);
            btn.on('click', function () { loadRolesTable(i); });
            ctrl.append(btn);
        }
    }

    loadRolesTable(1);

    /* ── Delete role ───────────────────────────────────────── */
    window.deleteRole = function (id, btn) {
        if (!confirm('Delete this role? This cannot be undone.')) return;
        $.ajax({
            url:  "{{ url('roles') }}/" + id,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                showToast('success', 'Deleted', response.message || 'Role deleted.');
                loadRolesTable(currentPage);
                loadRolesDropdown();
            },
            error: function () {
                showToast('danger', 'Error', 'Failed to delete role.');
            }
        });
    };

    /* ── Create role form ──────────────────────────────────── */
    $('#rolesform').on('submit', function (e) {
        e.preventDefault();
        $('.field-error').text('');

        const btn = $(this).find('button[type="submit"]');
        const orig = btn.html();
        btn.html('<span class="material-icons spin">sync</span> Saving…').prop('disabled', true);

        $.ajax({
            url:  "{{ route('roles.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                showToast('success', 'Created', response.message || 'Role created.');
                $('#rolesform')[0].reset();
                loadRolesTable(currentPage);
                loadRolesDropdown();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors || {};
                    $.each(errors, function (key, val) {
                        $('#' + key + '-error').text(val[0]);
                    });
                    showToast('danger', 'Validation', 'Please check the form.');
                } else {
                    showToast('danger', 'Error', xhr.responseJSON?.message || 'Failed to create role.');
                }
            },
            complete: function () { btn.html(orig).prop('disabled', false); }
        });
    });

    /* ── Assign modules ────────────────────────────────────── */
    $('#assignBtn').on('click', function () {
        const roleId     = $('#roleid').val();
        const selectedIds = $('.module-checkbox:checked').map(function () { return this.value; }).get();

        if (!roleId) { showToast('warning', 'Required', 'Please select a role.'); return; }
        if (!selectedIds.length) { showToast('warning', 'Required', 'Please select at least one module.'); return; }

        const btn  = $(this);
        const orig = btn.html();
        btn.html('<span class="material-icons spin">sync</span> Assigning…').prop('disabled', true);

        $.ajax({
            url:  "{{ route('modules.save') }}",
            type: 'POST',
            data: {
                _token:   $('meta[name="csrf-token"]').attr('content'),
                roleid:   roleId,
                modules:  selectedIds
            },
            success: function (response) {
                showToast('success', 'Assigned', response.message || 'Modules assigned successfully.');
            },
            error: function (xhr) {
                showToast('danger', 'Error', xhr.responseJSON?.message || 'Failed to assign modules.');
            },
            complete: function () { btn.html(orig).prop('disabled', false); }
        });
    });

    /* ── Auto-load existing modules when role changes ────────── */
    $('#roleid').on('change', function () {
        const roleid = $(this).val();

        // Uncheck all and clear visual state
        $('.module-checkbox').prop('checked', false);
        document.querySelectorAll('.module-parent, .module-child').forEach(function (row) {
            row.classList.remove('checked');
        });

        if (!roleid) return;

        $('#modulesContainer').css('opacity', '0.5');

        $.ajax({
            url:    "{{ route('modules.getRoleModules') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                roleid: roleid
            },
            success: function (response) {
                if (response.status === 'success') {
                    response.buttonIds.forEach(function (buttonId) {
                        const cb = document.getElementById('module' + buttonId);
                        if (cb) {
                            cb.checked = true;
                            const row = cb.closest('.module-parent, .module-child');
                            if (row) row.classList.add('checked');
                        }
                    });
                    showToast('success', 'Loaded', 'Role modules loaded successfully.');
                }
            },
            error: function () {
                showToast('danger', 'Error', 'Failed to load role modules.');
            },
            complete: function () {
                $('#modulesContainer').css('opacity', '1');
            }
        });
    });

    /* ── Spinner style ────────────────────────────────────── */
    $('<style>.spin{animation:spin 1s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}</style>').appendTo('head');

    /* ── Toast ─────────────────────────────────────────────── */
    function showToast (type, title, message) {
        const icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber', info:'info' };
        const t = $('<div>').addClass('toast-msg ' + type).html(
            '<span class="material-icons">' + (icons[type]||'info') + '</span>'
            + '<div><strong>' + title + '</strong> ' + message + '</div>'
        );
        $('#toastWrap').append(t);
        const dismiss = () => { t.addClass('leaving'); setTimeout(() => t.remove(), 300); };
        t.on('click', dismiss);
        setTimeout(dismiss, 5000);
    }

    window.showAlert = function (type, title, message) { showToast(type, title, message); };
    window.showMessage = function (msg, type) { showToast(type || 'info', 'Notice', msg); };

});
</script>

</x-custom-admin-layout>