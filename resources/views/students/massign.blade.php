<x-custom-admin-layout>

@vite(['resources/css/pages/massign.css']) 

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