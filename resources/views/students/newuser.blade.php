<x-custom-admin-layout>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<style nonce="{{ $cspNonce }}">
    /* ── Page-specific — tokens from corepay.css ─────────────── */
 
    .user-create-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }
 
    
 
    /* ── Form card ────────────────────────────────────────────── */
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
    }
 
    /* ── Section heads ────────────────────────────────────────── */
    .section-head {
        display: flex; align-items: center; gap: 10px;
        padding: 5px 24px; background: #f9fafb;
        border-bottom: 1px solid var(--border);
    }
 
    .section-icon {
        width: 32px; height: 32px; border-radius: 9px;
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
 
    .section-icon .material-icons { font-size: 16px; color: var(--accent); }
 
    .section-title {
        font-family: var(--font-head);
        font-size: 13.5px; font-weight: 700; color: var(--ink); margin: 0;
    }
 
    /* ── Section body ─────────────────────────────────────────── */
    .section-body { padding: 4px 24px; border-bottom: 1px solid var(--border); }
    .section-body:last-of-type { border-bottom: none; }
 
    /* ── Form grid ────────────────────────────────────────────── */
    .fgrid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 3px 18px;
    }
 
    .fc-3  { grid-column: span 3; }
    .fc-4  { grid-column: span 4; }
    .fc-6  { grid-column: span 6; }
    .fc-12 { grid-column: span 12; margin-top:-8px; }
 
    @media (max-width: 900px) {
        .fc-3, .fc-4, .fc-6 { grid-column: span 6; }
    }
 
    @media (max-width: 600px) {
        .fc-3, .fc-4, .fc-6 { grid-column: span 12; }
    }
 
    /* ── Field ────────────────────────────────────────────────── */
    .field { display: flex; flex-direction: column; gap: 2px; }
 
    .field label {
        font-size: 12.5px; font-weight: 500; color: #374151; letter-spacing: .01em;
    }
 
    .field label .req { color: var(--danger); margin-left: 2px; }
 
    .field input,
    .field select {
        height: 36px; padding: 0 13px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body);
        font-size: 14px; color: var(--ink); outline: none; width: 100%;
        appearance: none; -webkit-appearance: none;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }
 
    .field input::placeholder { color: #adb5bd; }
 
    .field input:focus, .field select:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,219,.1);
    }
 
    .field input.is-invalid { border-color: var(--danger) !important; }
    .field-error { font-size: 12px; color: var(--danger); margin-top: 2px; min-height: 16px; }
 
    /* ── Password input with eye toggle + generate btn ────────── */
    .pw-wrap {
        display: flex; align-items: stretch; gap: 0;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        overflow: hidden; background: #fafafa;
        transition: border-color .2s, box-shadow .2s;
    }
 
    .pw-wrap:focus-within {
        border-color: var(--border-focus);
        background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,219,.1);
    }
 
    .pw-wrap input {
        height: 42px; padding: 0 12px; border: none !important;
        background: transparent !important; flex: 1;
        font-size: 14px; color: var(--ink); outline: none;
        box-shadow: none !important;
        font-family: var(--font-body);
    }
 
    .pw-wrap input::placeholder { color: #adb5bd; }
 
    .pw-btn {
        width: 38px; height: 42px; border: none; border-left: 1px solid var(--border);
        background: #f3f4f8; cursor: pointer; color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        transition: background .2s, color .2s; flex-shrink: 0;
    }
 
    .pw-btn:hover { background: #e9ecef; color: var(--ink); }
    .pw-btn .material-icons { font-size: 17px; }
 
    /* Generate password button — wider, accent colored */
    .pw-gen-btn {
        display: flex; align-items: center; gap: 5px;
        padding: 0 12px; height: 42px;
        border: none; border-left: 1px solid var(--border);
        background: var(--accent-lt);
        color: var(--accent); font-family: var(--font-body);
        font-size: 12px; font-weight: 600; cursor: pointer;
        transition: background .2s; white-space: nowrap; flex-shrink: 0;
    }
 
    .pw-gen-btn:hover { background: #dbeafe; }
    .pw-gen-btn .material-icons { font-size: 15px; }
 
    /* Password strength meter */
    .pw-strength { margin-top: 6px; }
 
    .pw-strength-bar {
        height: 4px; background: #e5e7eb; border-radius: 100px; overflow: hidden; margin-bottom: 4px;
    }
 
    .pw-strength-fill {
        height: 100%; border-radius: 100px; width: 0%;
        transition: width .3s, background .3s;
    }
 
    .pw-strength-label { font-size: 11px; color: var(--muted); }
 
    /* Generated password display */
    .gen-pw-badge {
        display: none;
        align-items: center; gap: 8px;
        padding: 7px 12px; margin-top: 6px;
        background: var(--success-lt); border: 1px solid #6ee7b7;
        border-radius: var(--radius-sm); font-size: 12.5px;
    }
 
    .gen-pw-badge.show { display: flex; }
    .gen-pw-badge .material-icons { font-size: 14px; color: var(--success); }
 
    .gen-pw-badge code {
        font-family: monospace; font-size: 13px; font-weight: 700;
        color: #065f46; letter-spacing: 1px; flex: 1;
    }
 
    .gen-pw-copy {
        background: none; border: none; cursor: pointer;
        color: var(--success); font-size: 12px; font-weight: 600;
        display: flex; align-items: center; gap: 3px; padding: 0;
        transition: opacity .2s;
    }
 
    .gen-pw-copy:hover { opacity: .75; }
    .gen-pw-copy .material-icons { font-size: 14px; }
 
    /* ── Payroll checkboxes ───────────────────────────────────── */
    .payroll-chips {
        display: flex; flex-wrap: wrap; gap: 8px;
        max-height: 130px; overflow-y: auto; padding: 2px;
    }
 
    .payroll-chip { position: relative; }
 
    .payroll-chip input[type="checkbox"] {
        position: absolute; opacity: 0; width: 0; height: 0;
    }
 
    .payroll-chip label {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 13px; border: 1.5px solid var(--border);
        border-radius: 100px; font-size: 13px; font-weight: 500;
        color: var(--muted); cursor: pointer; background: #fafafa;
        transition: all .2s; white-space: nowrap;
    }
 
    .payroll-chip input:checked + label {
        border-color: var(--accent); color: var(--accent); background: var(--accent-lt);
    }
 
    .payroll-chip label:hover { border-color: #9ca3af; color: var(--ink); }
 
    /* ── Avatar upload ────────────────────────────────────────── */
    .avatar-upload {
        display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    }
 
    .avatar-preview {
        width: 72px; height: 72px; border-radius: 16px;
        border: 2px dashed var(--border); background: #f3f4f8;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; flex-shrink: 0; cursor: pointer;
        transition: border-color .2s;
    }
 
    .avatar-preview:hover { border-color: var(--accent); }
    .avatar-preview .material-icons { font-size: 28px; color: #d1d5db; }
    .avatar-preview img { width: 100%; height: 100%; object-fit: cover; display: none; }
 
    .avatar-upload-info { flex: 1; min-width: 180px; }
 
    .avatar-upload-info label.upload-trigger {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px; border: 1.5px solid var(--border);
        border-radius: var(--radius-sm); font-size: 13px; font-weight: 500;
        color: var(--muted); cursor: pointer; background: #fafafa;
        transition: all .2s; margin-bottom: 6px;
    }
 
    .avatar-upload-info label.upload-trigger:hover {
        border-color: var(--accent); color: var(--accent); background: var(--accent-lt);
    }
 
    .avatar-upload-info label.upload-trigger .material-icons { font-size: 15px; }
    .avatar-upload-info .hint { font-size: 11.5px; color: var(--muted); }
 
    /* ── Approver chip ────────────────────────────────────────── */
    .approver-chip { position: relative; display: inline-block; }
 
    .approver-chip input[type="checkbox"] {
        position: absolute; opacity: 0; width: 0; height: 0;
    }
 
    .approver-chip label {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 18px; border: 1.5px solid var(--border);
        border-radius: var(--radius-sm); font-size: 13.5px; font-weight: 500;
        color: var(--muted); cursor: pointer; background: #fafafa;
        transition: all .2s;
    }
 
    .approver-chip label .material-icons { font-size: 17px; }
 
    .approver-chip input:checked + label {
        border-color: var(--accent); color: var(--accent); background: var(--accent-lt);
    }
 
    /* ── Action bar ───────────────────────────────────────────── */
    .action-bar {
        display: flex; align-items: center; justify-content: flex-end;
        gap: 12px; padding: 18px 24px;
        border-top: 1px solid var(--border); background: #fafafa;
    }
 
    .btn {
        height: 42px; padding: 0 22px; border: none;
        border-radius: var(--radius-sm); font-family: var(--font-body);
        font-size: 14px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 7px;
        transition: transform .2s, box-shadow .2s, filter .2s;
        letter-spacing: .01em;
    }
 
    .btn .material-icons { font-size: 17px; }
    .btn:hover:not(:disabled) { transform: translateY(-1px); }
    .btn:active:not(:disabled) { transform: translateY(0); }
 
    .btn-save {
        background: linear-gradient(135deg, #1a56db, #4f46e5);
        color: #fff; box-shadow: 0 4px 14px rgba(26,86,219,.28);
    }
 
    .btn-save:hover { box-shadow: 0 7px 20px rgba(26,86,219,.38); filter: brightness(1.05); }
 
    .btn-reset {
        background: var(--surface); color: var(--muted);
        border: 1.5px solid var(--border);
    }
 
    .btn-reset:hover { color: var(--ink); border-color: #9ca3af; }
 
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
    .toast-msg .material-icons { font-size: 20px; flex-shrink: 0; }
 
    @media (max-width: 768px) {
        .user-create-page { padding: 18px 14px; }
        .section-body { padding: 12px; }
        .action-bar { padding: 14px 16px; }
    }
    .fontpass{font-size:11.5px;color:var(--muted);}
    .payfont{font-size:13px;color:var(--muted);}
    #profilepic{display:none;}
</style>
 
<div class="user-create-page">
 
    <div class="page-heading">
        <h1>New User</h1>
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
 
    <div class="form-card">
 
        {{-- ── Section 1: Account Info ────────────────────────── --}}
        <div class="section-head">
            <div class="section-icon"><span class="material-icons">person</span></div>
            <h2 class="section-title">Account Information</h2>
        </div>
 
        <form name="createuser" id="createuser" method="POST" enctype="multipart/form-data">
        @csrf
 
        <div class="section-body">
            <div class="fgrid">
                <div class="field fc-3">
                    <label>Name <span class="req">*</span></label>
                    <input name="name" id="name" type="text" placeholder="Full name" required autocomplete="off">
                    <span class="field-error" id="name-error"></span>
                </div>
 
                <div class="field fc-4">
                    <label>Email <span class="req">*</span></label>
                    <input name="email" id="email" type="email" placeholder="user@company.com" required autocomplete="off">
                    <span class="field-error" id="email-error"></span>
                </div>
            </div>
        </div>
 
        {{-- ── Section 2: Password ─────────────────────────────── --}}
        <div class="section-head">
            <div class="section-icon"><span class="material-icons">lock</span></div>
            <h2 class="section-title">Password</h2>
        </div>
 
        <div class="section-body">
            <div class="fgrid">
 
                {{-- Password --}}
                <div class="field fc-4">
                    <label>Password <span class="req">*</span></label>
                    <div class="pw-wrap" id="pwWrap1">
                        <input id="newPassword" name="newpass" id="newpass" type="password"
                               placeholder="Min. 8 characters" required autocomplete="new-password"
                               minlength="8" >
                        <button type="button" class="pw-btn" id="pw-btn"
                                title="Show/hide password">
                            <span class="material-icons" id="eyeIcon1">visibility</span>
                        </button>
                        <button type="button" class="pw-gen-btn" id="generatePwBtn"
                                 title="Auto-generate a strong password">
                            <span class="material-icons">auto_fix_high</span>
                            Generate
                        </button>
                    </div>
                    <div class="pw-strength">
                        <div class="pw-strength-bar">
                            <div class="pw-strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="pw-strength-label" id="strengthLabel"></span>
                    </div>
                    <div class="gen-pw-badge" id="genPwBadge">
                        <span class="material-icons">key</span>
                        <code id="genPwText"></code>
                        <button type="button" class="gen-pw-copy" id="gen-pw-copy">
                            <span class="material-icons">content_copy</span> Copy
                        </button>
                    </div>
                    <span class="field-error" id="newpass-error"></span>
                </div>
 
                {{-- Confirm password --}}
                <div class="field fc-4">
                    <label>Confirm Password <span class="req">*</span></label>
                    <div class="pw-wrap" id="pwWrap2">
                        <input id="confirmPassword" name="confirm" type="password"
                               placeholder="Re-enter password" required autocomplete="new-password" minlength="8">
                        <button type="button" class="pw-btn" id="pw-btn2"
                                title="Show/hide password">
                            <span class="material-icons" id="eyeIcon2">visibility</span>
                        </button>
                    </div>
                    <span class="field-error" id="confirm-error"></span>
                </div>
 
                {{-- Rules hint --}}
                <div class="fc-12">
                    <span  class="fontpass">
                        Password must be at least 8 characters and include 3 of 4: uppercase, lowercase, numbers, symbols (~!@#$%^*_-+=|(){}[]:;&lt;&gt;,.?/)
                    </span>
                </div>
 
            </div>
        </div>
 
        {{-- ── Section 3: Access & Profile ────────────────────── --}}
        <div class="section-head">
            <div class="section-icon"><span class="material-icons">admin_panel_settings</span></div>
            <h2 class="section-title">Access &amp; Profile</h2>
        </div>
 
        <div class="section-body">
            <div class="fgrid">
 
                {{-- Allowed payrolls --}}
                <div class="field fc-4">
                    <label>Allowed Payrolls</label>
                    <div class="payroll-chips">
                        @if($payrollTypes->count() > 0)
                            @foreach($payrollTypes as $pt)
                                <div class="payroll-chip">
                                    <input type="checkbox" name="allowedPayroll[]"
                                           id="payroll{{ $pt->ID }}" value="{{ $pt->ID }}">
                                    <label for="payroll{{ $pt->ID }}">{{ $pt->pname }}</label>
                                </div>
                            @endforeach
                        @else
                            <span class= "payfont">No payroll types found.</span>
                        @endif
                    </div>
                    <span class="field-error" id="allowedPayroll-error"></span>
                </div>
 
                {{-- Profile photo --}}
                <div class="field fc-4">
                    <label>Profile Photo</label>
                    <div class="avatar-upload">
                        <div class="avatar-preview" id="avatarPreview" onclick="document.getElementById('profilepic').click()">
                            <span class="material-icons">person</span>
                            <img id="previewImg" src="" alt="Preview">
                        </div>
                        <div class="avatar-upload-info">
                            <label class="upload-trigger" for="profilepic">
                                <span class="material-icons">upload</span> Choose Photo
                            </label>
                            <input type="file" id="profilepic" name="profilepic"
                                   accept="image/*" 
                                   >
                            <div class="hint">JPG, PNG, GIF · Max 2 MB</div>
                            <span class="field-error" id="profilepic-error"></span>
                        </div>
                    </div>
                </div>
 
                {{-- Is approver --}}
                <div class="field fc-4">
                    <label>Role Flags</label>
                    <div class="approver-chip">
                        <input type="checkbox" id="approvelvl" name="approvelvl" value="YES">
                        <label for="approvelvl">
                            <span class="material-icons">verified_user</span>
                            Is Approver
                        </label>
                    </div>
                </div>
 
            </div>
        </div>
 
        {{-- ── Action bar ──────────────────────────────────────── --}}
        <div class="action-bar">
            <button type="reset" class="btn btn-reset" id="btn-reset" >
                <span class="material-icons">restart_alt</span> Reset
            </button>
            <button type="submit" class="btn btn-save">
                <span class="material-icons">person_add</span> Create User
            </button>
        </div>
 
        </form>
 
        <div id="alertContainer"></div>
 
    </div>
</div>
    
    
    <script nonce="{{ $cspNonce }}">
        const amanage = '{{ route("newuser.store") }}';
       
    </script>
    <script src="{{ asset('js/nuser.js') }}"></script>
    
     
   
</x-custom-admin-layout>