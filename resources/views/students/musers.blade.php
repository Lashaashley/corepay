<x-custom-admin-layout>
@vite(['resources/css/pages/musers.css']) 


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
            <table id="users-table" class="stripe hover nowrap" >
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






