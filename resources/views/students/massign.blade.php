<x-custom-admin-layout>

<style nonce="{{ $cspNonce }}">
    /* ── Page-specific — tokens from corepay.css ─────────────── */

    .assign-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }

    .page-heading { margin-bottom: 24px; }

    .page-heading h1 {
        font-family: var(--font-head);
        font-size: 22px; font-weight: 700; color: var(--ink); margin: 0 0 4px;
    }

    .page-heading p { font-size: 13.5px; color: var(--muted); margin: 0; }

    /* ── Two-column layout ───────────────────────────────────── */
    .assign-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 860px) { .assign-layout { grid-template-columns: 1fr; } }

    /* ── Card ────────────────────────────────────────────────── */
    .assign-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
    }

    .assign-card:nth-child(2) { animation-delay: .07s; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .card-head {
        display: flex; align-items: center; gap: 10px;
        padding: 16px 20px; border-bottom: 1px solid var(--border);
    }

    .card-icon {
        width: 34px; height: 34px; border-radius: 9px;
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .card-icon .material-icons { font-size: 17px; color: var(--accent); }

    .card-title {
        font-family: var(--font-head);
        font-size: 14px; font-weight: 700; color: var(--ink); margin: 0 0 2px;
    }

    .card-subtitle { font-size: 12px; color: var(--muted); margin: 0; }

    .card-body { padding: 20px; }

    /* ── User select ─────────────────────────────────────────── */
    .field { display: flex; flex-direction: column; gap: 5px; }

    .field label {
        font-size: 12.5px; font-weight: 500; color: #374151;
    }

    .select-wrap { position: relative; }

    .select-wrap::after {
        content: 'expand_more'; font-family: 'Material Icons'; font-size: 18px;
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        color: var(--muted); pointer-events: none;
    }

    .select-wrap select {
        height: 42px; padding: 0 36px 0 13px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body);
        font-size: 14px; color: var(--ink); outline: none; width: 100%;
        appearance: none; -webkit-appearance: none;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }

    .select-wrap select:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,219,.1);
    }

    .field-error { font-size: 12px; color: var(--danger); margin-top: 2px; }

    /* Selected user display */
    .selected-user {
        display: none;
        align-items: center; gap: 10px;
        padding: 12px 14px; margin-top: 14px;
        background: var(--accent-lt); border: 1.5px solid #bfdbfe;
        border-radius: var(--radius-sm);
    }

    .selected-user.show { display: flex; }

    .user-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        background: linear-gradient(135deg, #1a56db, #6366f1);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .user-avatar .material-icons { font-size: 17px; color: #fff; }

    .selected-user-name {
        font-size: 13.5px; font-weight: 600; color: var(--accent);
    }

    /* ── Action buttons ──────────────────────────────────────── */
    .action-stack {
        display: flex; flex-direction: column; gap: 10px; margin-top: 20px;
    }

    .btn {
        height: 42px; padding: 0 18px; border: none;
        border-radius: var(--radius-sm); font-family: var(--font-body);
        font-size: 13.5px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center; gap: 7px;
        transition: transform .2s, box-shadow .2s, filter .2s;
        width: 100%;
    }

    .btn .material-icons { font-size: 16px; }
    .btn:hover:not(:disabled) { transform: translateY(-1px); }
    .btn:active:not(:disabled) { transform: translateY(0); }
    .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

    .btn-assign {
        background: linear-gradient(135deg, #1a56db, #4f46e5);
        color: #fff; box-shadow: 0 4px 14px rgba(26,86,219,.25);
    }

    .btn-assign:hover:not(:disabled) { box-shadow: 0 7px 20px rgba(26,86,219,.35); filter: brightness(1.05); }

    .btn-select-all {
        background: var(--success-lt); color: var(--success);
        border: 1.5px solid #6ee7b7;
    }

    .btn-select-all:hover:not(:disabled) { background: #d1fae5; }

    .btn-deselect {
        background: #fffbeb; color: #92400e;
        border: 1.5px solid #fde68a;
    }

    .btn-deselect:hover:not(:disabled) { background: #fef3c7; }

    /* ── Roles card ──────────────────────────────────────────── */
    .roles-scroll {
        max-height: 440px;
        overflow-y: auto;
        padding: 2px;
    }

    .roles-scroll::-webkit-scrollbar { width: 4px; }
    .roles-scroll::-webkit-scrollbar-track { background: transparent; }
    .roles-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

    /* ── Role radio item ─────────────────────────────────────── */
    .role-item { position: relative; margin-bottom: 6px; }

    .role-item input[type="radio"] {
        position: absolute; opacity: 0; width: 0; height: 0;
    }

    .role-item label {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 12px 14px;
        border: 1.5px solid var(--border); border-radius: 10px;
        cursor: pointer; background: #fafafa;
        transition: all .2s; font-size: 13.5px; color: var(--ink);
        font-weight: 500;
    }

    .role-item label:hover { border-color: #93c5fd; background: var(--accent-lt); }

    .role-item input:checked + label {
        border-color: var(--accent); background: var(--accent-lt);
    }

    /* Radio dot */
    .role-dot {
        width: 18px; height: 18px; border-radius: 50%;
        border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 1px; transition: all .2s;
    }

    .role-item input:checked + label .role-dot {
        border-color: var(--accent); background: var(--accent);
    }

    .role-item input:checked + label .role-dot::after {
        content: ''; width: 6px; height: 6px;
        border-radius: 50%; background: #fff;
    }

    .role-label-text {
        display: flex; flex-direction: column; gap: 2px; flex: 1;
    }

    .role-name { font-weight: 600; font-size: 13.5px; color: var(--ink); }
    .role-desc { font-size: 12px; color: var(--muted); font-weight: 400; }

    .role-item input:checked + label .role-name { color: var(--accent); }

    /* ── Empty state ─────────────────────────────────────────── */
    .roles-empty {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: 40px 20px; text-align: center;
        color: var(--muted);
    }

    .roles-empty .material-icons { font-size: 36px; color: #d1d5db; margin-bottom: 10px; }
    .roles-empty p { font-size: 14px; margin: 0; }

    /* ── Selected role badge ─────────────────────────────────── */
    .selected-role-badge {
        display: none;
        align-items: center; gap: 7px;
        padding: 8px 12px; margin-top: 14px;
        background: var(--success-lt); border: 1.5px solid #6ee7b7;
        border-radius: var(--radius-sm);
        font-size: 13px; font-weight: 600; color: var(--success);
    }

    .selected-role-badge.show { display: flex; }
    .selected-role-badge .material-icons { font-size: 15px; }

    /* ── Toast ───────────────────────────────────────────────── */
    .toast-wrap {
        position: fixed; top: 20px; right: 20px; z-index: 9999;
        display: flex; flex-direction: column; gap: 10px;
    }

    .toast-msg {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 18px; border-radius: 14px;
        min-width: 280px; max-width: 360px;
        font-size: 14px; font-weight: 500;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        animation: toastIn .35s cubic-bezier(.22,.61,.36,1) both; cursor: pointer;
    }

    .toast-msg.leaving { animation: toastOut .3s ease forwards; }

    @keyframes toastIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
    @keyframes toastOut { to { opacity:0; transform:translateX(40px); } }

    .toast-msg.success { background: var(--success-lt); color: #065f46; }
    .toast-msg.danger  { background: var(--danger-lt);  color: #991b1b; }
    .toast-msg.warning { background: #fffbeb; color: #92400e; }
    .toast-msg.info    { background: var(--accent-lt);  color: #1e40af; }
    .toast-msg .material-icons { font-size: 20px; flex-shrink: 0; }

    @media (max-width: 640px) {
        .assign-page { padding: 18px 14px; }
        .card-body { padding: 16px; }
    }
</style>

<div class="assign-page">

    <div class="page-heading">
        <h1>Assign Modules</h1>
        <p>Select a user and assign them a role to control their access permissions.</p>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <form id="moduleAssignForm">
    @csrf

    <div class="assign-layout">

        {{-- ── Left: User selection + actions ──────────────── --}}
        <div class="assign-card">
            <div class="card-head">
                <div class="card-icon"><span class="material-icons">person</span></div>
                <div>
                    <p class="card-title">Select User</p>
                    <p class="card-subtitle">Choose who to assign a role to</p>
                </div>
            </div>

            <div class="card-body">

                <div class="field">
                    <label>User <span style="color:var(--danger)">*</span></label>
                    <div class="select-wrap">
                        <select name="users" id="users" required>
                            <option value="">— Select User —</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="field-error" id="users-error"></span>
                </div>

                <!-- Selected user display -->
                <div class="selected-user" id="selectedUserDisplay">
                    <div class="user-avatar">
                        <span class="material-icons">person</span>
                    </div>
                    <span class="selected-user-name" id="selectedUserName"></span>
                </div>

                <!-- Selected role feedback -->
                <div class="selected-role-badge" id="selectedRoleBadge">
                    <span class="material-icons">verified_user</span>
                    <span id="selectedRoleName">No role selected</span>
                </div>

                <div class="action-stack">
                    <button type="button" class="btn btn-assign" id="assignBtn">
                        <span class="material-icons">assignment_ind</span>
                        Assign Role
                    </button>
                    <button type="button" class="btn btn-deselect" id="deselectAllBtn">
                        <span class="material-icons">radio_button_unchecked</span>
                        Clear Selection
                    </button>
                </div>

            </div>
        </div>

        {{-- ── Right: Roles list ─────────────────────────── --}}
        <div class="assign-card">
            <div class="card-head">
                <div class="card-icon" style="background:#f3f0ff;">
                    <span class="material-icons" style="color:#7c3aed;">admin_panel_settings</span>
                </div>
                <div style="flex:1;">
                    <p class="card-title">Available Roles</p>
                    <p class="card-subtitle">Select one role to assign</p>
                </div>
                <span style="font-size:12px;color:var(--muted);background:var(--bg);
                             padding:3px 10px;border-radius:100px;border:1px solid var(--border);">
                    {{ count($roles) }} {{ Str::plural('role', count($roles)) }}
                </span>
            </div>

            <div class="card-body">
                <div class="roles-scroll" id="rolesContainer">
                    @if(count($roles) > 0)
                        @foreach($roles as $role)
                            <div class="role-item">
                                <input class="role-checkbox" type="radio"
                                       name="role" value="{{ $role->ID }}"
                                       id="role{{ $role->ID }}"
                                       data-name="{{ $role->rolename }}">
                                <label for="role{{ $role->ID }}">
                                    <div class="role-dot"></div>
                                    <div class="role-label-text">
                                        <span class="role-name">{{ $role->rolename }}</span>
                                        @if($role->rdesc)
                                            <span class="role-desc">{{ $role->rdesc }}</span>
                                        @endif
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    @else
                        <div class="roles-empty">
                            <span class="material-icons">admin_panel_settings</span>
                            <p>No roles available. Create roles first.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    </form>

    <div id="alertContainer"></div>

</div>

<script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>

<script nonce="{{ $cspNonce }}">
$(document).ready(function () {

    /* ── User selection display ──────────────────────────── */
    $('#users').on('change', function () {
        const name = $(this).find('option:selected').text();
        const val  = $(this).val();

        if (val) {
            $('#selectedUserName').text(name);
            $('#selectedUserDisplay').addClass('show');
        } else {
            $('#selectedUserDisplay').removeClass('show');
        }

        // Clear role highlight on user change
        $('.role-checkbox').prop('checked', false);
        $('#selectedRoleBadge').removeClass('show');
    });

    /* ── Role selection feedback ─────────────────────────── */
    $(document).on('change', '.role-checkbox', function () {
        const name = $(this).data('name') || $(this).closest('.role-item').find('.role-name').text();
        if (this.checked) {
            $('#selectedRoleName').text(name);
            $('#selectedRoleBadge').addClass('show');
        }
    });

    /* ── Assign role ─────────────────────────────────────── */
    $('#assignBtn').on('click', function () {
        const userId       = $('#users').val();
        const selectedRole = $('.role-checkbox:checked').val();

        // Clear errors
        $('.field-error').html('');

        if (!userId) {
            $('#users-error').text('Please select a user');
            showToast('warning', 'Validation', 'Please select a user first.');
            return;
        }

        if (!selectedRole) {
            showToast('warning', 'Validation', 'Please select a role to assign.');
            return;
        }

        const btn         = $(this);
        const origHtml    = btn.html();
        btn.html('<span class="material-icons spin">sync</span> Assigning…').prop('disabled', true);

        $.ajax({
            url:    "{{ route('modules.assign') }}",
            method: 'POST',
            data: {
                _token:  "{{ csrf_token() }}",
                workNo:  userId,
                roleid:  selectedRole
            },
            success: function (response) {
                if (response.status === 'success') {
                    showToast('success', 'Role Assigned', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors || {};
                    $.each(errors, function (key, value) {
                        $('#' + key + '-error').text(value[0]);
                    });
                    showToast('danger', 'Validation Error', 'Please check the form for errors.');
                } else {
                    showToast('danger', 'Error', xhr.responseJSON?.message || 'Failed to assign role.');
                }
            },
            complete: function () {
                btn.html(origHtml).prop('disabled', false);
            }
        });
    });

    /* ── Clear selection ─────────────────────────────────── */
    $('#deselectAllBtn').on('click', function () {
        $('.role-checkbox').prop('checked', false);
        $('#selectedRoleBadge').removeClass('show');
        showToast('info', 'Cleared', 'Role selection cleared.');
    });

    /* ── Spinner animation ───────────────────────────────── */
    $('<style nonce="{{ $cspNonce }}">.spin{animation:spin 1s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}</style>').appendTo('head');

    /* ── Toast ───────────────────────────────────────────── */
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

    // Legacy compatibility
    window.showAlert = function (type, title, message) {
        showToast(type === 'success' ? 'success' : type === 'danger' ? 'danger' : type === 'warning' ? 'warning' : 'info', title, message);
    };

});
</script>

</x-custom-admin-layout>