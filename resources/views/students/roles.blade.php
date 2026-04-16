<x-custom-admin-layout>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@vite(['resources/css/pages/roles.css']) 
<style nonce="{{ $cspNonce }}">

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
                                            $html .= '<div class="module-parent"  >';
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
                                                    $html .= '<div class="module-child" >';
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

<script nonce="{{ $cspNonce }}">
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

    // If parent, sync all children to match
    if (cb.dataset.parent === 'true') {
        const buttonId = cb.dataset.buttonId;
        document.querySelectorAll('[data-child-of="' + buttonId + '"]').forEach(function (childCb) {
            childCb.checked = cb.checked;
            const childRow = childCb.closest('.module-child');
            if (childRow) childRow.classList.toggle('checked', cb.checked);
        });
    }

    // If child — only auto-CHECK parent when all siblings are checked
    // Never uncheck the parent when a child is unchecked
    if (cb.dataset.childOf) {
        const parentCb = document.querySelector('[data-button-id="' + cb.dataset.childOf + '"]');
        if (parentCb && cb.checked) { // ← only runs when checking, not unchecking
            const siblings  = document.querySelectorAll('[data-child-of="' + cb.dataset.childOf + '"]');
            const allChecked = Array.from(siblings).every(s => s.checked);
            if (allChecked) {
                parentCb.checked = true;
                const parentRow = parentCb.closest('.module-parent');
                if (parentRow) parentRow.classList.add('checked');
            }
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
    $('<style nonce="{{ $cspNonce }}">.spin{animation:spin 1s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}</style>').appendTo('head');

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

 document.addEventListener('click', function (e) {
    // Don't intercept direct checkbox clicks — the browser handles those natively
    if (e.target.type === 'checkbox') return;

    const row = e.target.closest('.module-parent, .module-child');
    if (row) {
        e.stopPropagation(); // prevent bubbling from child → parent both firing
        toggleModule(row);
    }
});
});
</script>

</x-custom-admin-layout>