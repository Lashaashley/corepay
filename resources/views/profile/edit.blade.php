<x-custom-admin-layout>

<style nonce="{{ $cspNonce }}">
    /* ── Page ────────────────────────────────────────────────── */
    .profile-page {
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
    .profile-layout {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 900px) { .profile-layout { grid-template-columns: 1fr; } }

    /* ── Card ────────────────────────────────────────────────── */
    .p-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
    }

    .p-card:nth-child(2) { animation-delay: .07s; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .p-card-head {
        display: flex; align-items: center; gap: 10px;
        padding: 14px 20px; border-bottom: 1px solid var(--border);
    }

    .p-card-icon {
        width: 32px; height: 32px; border-radius: 9px; background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .p-card-icon .material-icons { font-size: 16px; color: var(--accent); }
    .p-card-icon.green  { background: var(--success-lt); }
    .p-card-icon.green  .material-icons { color: var(--success); }
    .p-card-icon.purple { background: #f3f0ff; }
    .p-card-icon.purple .material-icons { color: #7c3aed; }
    .p-card-icon.red    { background: var(--danger-lt); }
    .p-card-icon.red    .material-icons { color: var(--danger); }
    .p-card-icon.amber  { background: #fffbeb; }
    .p-card-icon.amber  .material-icons { color: var(--warning); }

    .p-card-title  { font-family: var(--font-head); font-size: 14px; font-weight: 700; color: var(--ink); margin: 0; }
    .p-card-body   { padding: 20px; }

    /* ── Avatar section ──────────────────────────────────────── */
    .avatar-section {
        display: flex; flex-direction: column; align-items: center;
        padding: 28px 20px 20px; border-bottom: 1px solid var(--border);
        gap: 14px;
    }

    .avatar-ring {
        position: relative; display: inline-block;
    }

    .avatar-ring img {
        width: 88px; height: 88px; border-radius: 50%;
        object-fit: cover; border: 3px solid var(--surface);
        box-shadow: 0 0 0 3px var(--accent);
        display: block;
    }

    .avatar-edit-btn {
        position: absolute; bottom: 2px; right: 2px;
        width: 26px; height: 26px; border-radius: 50%;
        background: var(--accent); border: 2px solid var(--surface);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: background .2s;
    }

    .avatar-edit-btn:hover { background: #1648c0; }
    .avatar-edit-btn .material-icons { font-size: 13px; color: #fff; }

    .avatar-name {
        font-family: var(--font-head); font-size: 16px; font-weight: 700; color: var(--ink);
        text-align: center; margin: 0;
    }

    .avatar-email { font-size: 12.5px; color: var(--muted); text-align: center; }

    /* ── 2FA status card ─────────────────────────────────────── */
    .tfa-strip {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 20px;
        gap: 12px;
    }

    .tfa-info { display: flex; align-items: center; gap: 10px; flex: 1; }

    .tfa-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .tfa-icon .material-icons { font-size: 18px; }

    .tfa-label { font-size: 13.5px; font-weight: 600; color: var(--ink); margin: 0 0 2px; }
    .tfa-sublabel { font-size: 12px; margin: 0; }

    .tfa-btn {
        height: 34px; padding: 0 14px; border: none; border-radius: 8px;
        font-family: var(--font-body); font-size: 13px; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
        transition: transform .2s, box-shadow .2s; white-space: nowrap;
        text-decoration: none;
    }

    .tfa-btn:hover { transform: translateY(-1px); }
    .tfa-btn .material-icons { font-size: 14px; }
    .tfa-btn.enable  { background: linear-gradient(135deg, #1a56db, #4f46e5); color: #fff; box-shadow: 0 3px 10px rgba(26,86,219,.22); }
    .tfa-btn.disable { background: var(--danger-lt); color: var(--danger); border: 1.5px solid #fca5a5; }

    /* ── Form fields ─────────────────────────────────────────── */
    .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }

    .field label { font-size: 12.5px; font-weight: 500; color: #374151; }
    .field label .req { color: var(--danger); margin-left: 2px; }

    .field input {
        height: 40px; padding: 0 12px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body);
        font-size: 14px; color: var(--ink); outline: none; width: 100%;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }

    .field input:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,219,.1);
    }

    .field-error { font-size: 12px; color: var(--danger); margin-top: 2px; }
    .field-success { font-size: 12px; color: var(--success); margin-top: 2px; }

    /* Email unverified banner */
    .unverified-banner {
        display: flex; align-items: center; gap: 8px;
        padding: 10px 13px; background: #fffbeb;
        border: 1.5px solid #fde68a; border-radius: var(--radius-sm);
        font-size: 12.5px; color: #92400e; margin-top: 6px;
    }

    .unverified-banner .material-icons { font-size: 15px; color: var(--warning); flex-shrink: 0; }

    /* ── Password input with eye toggle ──────────────────────── */
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
        width: 38px; height: 40px; border: none; border-left: 1px solid var(--border);
        background: #f3f4f8; cursor: pointer; color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        transition: background .2s, color .2s; flex-shrink: 0;
    }

    .pw-btn:hover { background: #e9ecef; color: var(--ink); }
    .pw-btn .material-icons { font-size: 17px; }

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

    /* ── Password strength bar ───────────────────────────────── */
    .pw-strength-bar {
        height: 4px; background: #e5e7eb; border-radius: 100px;
        overflow: hidden; margin: 6px 0 3px;
    }

    #strength-fill {
        height: 100%; border-radius: 100px; width: 0%;
        transition: width .3s, background .3s;
    }

    .pw-strength-label { font-size: 11.5px; color: var(--muted); }

    .pw-rules {
        display: flex; flex-wrap: wrap; gap: 6px 12px;
        margin-top: 6px;
    }

    .pw-rule {
        font-size: 11.5px; display: flex; align-items: center; gap: 4px;
    }

    .pw-rule .material-icons { font-size: 13px; }
    .pw-rule.ok  { color: var(--success); }
    .pw-rule.err { color: #d1d5db; }

    /* ── Password match ──────────────────────────────────────── */
    .pw-match { font-size: 12.5px; margin-top: 4px; display: flex; align-items: center; gap: 5px; }
    .pw-match .material-icons { font-size: 14px; }
    .pw-match.ok  { color: var(--success); }
    .pw-match.err { color: var(--danger); }

    /* ── Grid for password section ───────────────────────────── */
    .pw-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px;
    }

    @media (max-width: 640px) { .pw-grid { grid-template-columns: 1fr; } }

    /* ── Buttons ─────────────────────────────────────────────── */
    .btn {
        height: 40px; padding: 0 20px; border: none; border-radius: var(--radius-sm);
        font-family: var(--font-body); font-size: 14px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 7px;
        transition: transform .2s, box-shadow .2s, filter .2s; letter-spacing: .01em;
    }

    .btn .material-icons { font-size: 17px; }
    .btn:hover:not(:disabled) { transform: translateY(-1px); }
    .btn:active:not(:disabled) { transform: translateY(0); }
    .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

    .btn-save {
        background: linear-gradient(135deg, #1a56db, #4f46e5); color: #fff;
        box-shadow: 0 4px 14px rgba(26,86,219,.25);
    }

    .btn-save:hover:not(:disabled) { box-shadow: 0 7px 20px rgba(26,86,219,.35); filter: brightness(1.05); }

    .btn-change-pw {
        background: linear-gradient(135deg, #059669, #10b981); color: #fff;
        box-shadow: 0 4px 14px rgba(5,150,105,.22);
    }

    .btn-change-pw:hover:not(:disabled) { box-shadow: 0 7px 20px rgba(5,150,105,.32); filter: brightness(1.05); }

    .btn-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

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
    @keyframes toastIn  { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
    @keyframes toastOut { to   { opacity:0; transform:translateX(40px); } }

    .toast-msg.success { background: var(--success-lt); color: #065f46; }
    .toast-msg.danger  { background: var(--danger-lt);  color: #991b1b; }
    .toast-msg.warning { background: #fffbeb; color: #92400e; }
    .toast-msg .material-icons { font-size: 20px; flex-shrink: 0; }

    /* ── Photo modal ─────────────────────────────────────────── */
    .photo-modal-backdrop {
        position: fixed; inset: 0; background: rgba(0,0,0,.45);
        backdrop-filter: blur(4px); z-index: 8000;
        display: none; align-items: center; justify-content: center; padding: 20px;
    }

    .photo-modal-backdrop.open { display: flex; }

    .photo-modal-card {
        background: var(--surface); border-radius: 18px; width: 100%; max-width: 420px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2); overflow: hidden;
        animation: fadeUp .3s cubic-bezier(.22,.61,.36,1) both;
    }

    .photo-modal-head {
        display: flex; align-items: center; gap: 10px;
        padding: 16px 20px; border-bottom: 1px solid var(--border);
    }

    .photo-modal-title { font-family: var(--font-head); font-size: 15px; font-weight: 700; color: var(--ink); flex: 1; }

    .photo-modal-body { padding: 20px; }
    .photo-modal-foot {
        display: flex; gap: 10px; justify-content: flex-end;
        padding: 14px 20px; border-top: 1px solid var(--border); background: #fafafa;
    }

    /* File upload inside modal */
    .modal-file-wrap { display: flex; align-items: center; gap: 10px; }

    .modal-file-label {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 0 14px; height: 40px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        font-size: 13px; font-weight: 500; color: var(--muted);
        cursor: pointer; background: var(--surface); transition: all .2s; white-space: nowrap;
    }

    .modal-file-label:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-lt); }
    .modal-file-label .material-icons { font-size: 15px; }
    .modal-file-name { font-size: 12.5px; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .btn-ghost { background: var(--surface); color: var(--muted); border: 1.5px solid var(--border); }
    .btn-ghost:hover { color: var(--ink); border-color: #9ca3af; }

    @media (max-width: 640px) { .profile-page { padding: 18px 14px; } }
</style>

<div class="profile-page">

    <div class="page-heading">
        <h1>My Profile</h1>
        <p>Manage your account information and security settings.</p>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <div class="profile-layout">

        {{-- ── Left: Avatar + profile info + 2FA ───────────── --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Avatar card --}}
            <div class="p-card">
                <div class="avatar-section">
                    <div class="avatar-ring">
                        <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : asset('images/NO-IMAGE-AVAILABLE.jpg') }}"
                             alt="{{ Auth::user()->name }}"
                             id="avatarPreview">
                        <div class="avatar-edit-btn" onclick="openPhotoModal()" title="Change photo">
                            <span class="material-icons">edit</span>
                        </div>
                    </div>
                    <div>
                        <p class="avatar-name">{{ Auth::user()->name }}</p>
                        <p class="avatar-email">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="p-card-body" style="padding-top:14px;">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="field">
                            <label>Name <span class="req">*</span></label>
                            <input id="name" name="name" type="text"
                                   value="{{ old('name', $user->name) }}"
                                   required autofocus autocomplete="name">
                            @error('name')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label>Email <span class="req">*</span></label>
                            <input id="email" name="email" type="email"
                                   value="{{ old('email', $user->email) }}"
                                   required autocomplete="username">
                            @error('email')
                                <span class="field-error">{{ $message }}</span>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="unverified-banner">
                                    <span class="material-icons">warning_amber</span>
                                    <span>
                                        Email unverified.
                                        <button form="send-verification"
                                                style="background:none;border:none;padding:0;color:#92400e;font-weight:600;cursor:pointer;text-decoration:underline;">
                                            Resend verification
                                        </button>
                                    </span>
                                </div>
                                @if(session('status') === 'verification-link-sent')
                                    <span class="field-success">✓ Verification link sent.</span>
                                @endif
                            @endif
                        </div>

                        <div class="btn-row">
                            <button type="submit" class="btn btn-save">
                                <span class="material-icons">save</span> Save
                            </button>
                            @if(session('status') === 'profile-updated')
                                <span style="font-size:13px;color:var(--success);display:flex;align-items:center;gap:4px;">
                                    <span class="material-icons" style="font-size:15px;">check_circle</span> Saved
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- 2FA card --}}
            <div class="p-card">
                <div class="p-card-head">
                    <div class="p-card-icon {{ Auth::user()->google2fa_secret ? 'green' : 'amber' }}">
                        <span class="material-icons">shield</span>
                    </div>
                    <span class="p-card-title">Two-Factor Auth</span>
                </div>
                <div class="tfa-strip">
                    <div class="tfa-info">
                        <div class="tfa-icon" style="background:{{ Auth::user()->google2fa_secret ? 'var(--success-lt)' : '#fffbeb' }};">
                            <span class="material-icons" style="color:{{ Auth::user()->google2fa_secret ? 'var(--success)' : 'var(--warning)' }};">
                                {{ Auth::user()->google2fa_secret ? 'verified_user' : 'gpp_maybe' }}
                            </span>
                        </div>
                        <div>
                            <p class="tfa-label">Two-Factor Authentication</p>
                            @if(Auth::user()->google2fa_secret)
                                <p class="tfa-sublabel" style="color:var(--success);">Active — your account is secured</p>
                            @else
                                <p class="tfa-sublabel" style="color:var(--warning);">Not enabled — we recommend turning this on</p>
                            @endif
                        </div>
                    </div>
                    @if(Auth::user()->google2fa_secret)
                        <a href="{{ route('2fa.disable.form') }}" class="tfa-btn disable">
                            <span class="material-icons">lock_open</span> Disable
                        </a>
                    @else
                        <a href="{{ route('2fa.setup') }}" class="tfa-btn enable">
                            <span class="material-icons">lock</span> Enable
                        </a>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── Right: Change password ────────────────────────── --}}
        <div class="p-card">
            <div class="p-card-head">
                <div class="p-card-icon purple"><span class="material-icons">key</span></div>
                <span class="p-card-title">Update Password</span>
            </div>
            <div class="p-card-body">
                <form name="changepassf" id="changepassf" method="post">
                    @csrf
                    <input type="hidden" id="userid" name="userid" value="{{ $user->id }}">

                    <div class="pw-grid">

                        {{-- Current password --}}
                        <div class="field" style="grid-column:1/-1;">
                            <label>Current Password <span class="req">*</span></label>
                            <div class="pw-wrap">
                                <input id="current_password" name="current_password" type="password"
                                       autocomplete="current-password" placeholder="Enter current password">
                                <button type="button" class="pw-btn" onclick="togglePw('current_password','eyeCurrent')">
                                    <span class="material-icons" id="eyeCurrent">visibility</span>
                                </button>
                            </div>
                            <span class="field-error" id="current_password-error"></span>
                            @error('current_password')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- New password --}}
                        <div class="field">
                            <label>New Password <span class="req">*</span></label>
                            <div class="pw-wrap">
                                <input type="password" id="newpass" name="newpass"
                                       minlength="8" autocomplete="new-password"
                                       placeholder="Min. 8 characters"
                                       oninput="checkStrength()">
                                <button type="button" class="pw-btn" onclick="togglePw('newpass','eyeNew')">
                                    <span class="material-icons" id="eyeNew">visibility</span>
                                </button>
                                <button type="button" class="pw-gen-btn" id="generate-password"
                                        onclick="genPassword()">
                                    <span class="material-icons">auto_fix_high</span> Generate
                                </button>
                            </div>
                            <div class="pw-strength-bar">
                                <div id="strength-fill"></div>
                            </div>
                            <span class="pw-strength-label" id="strength-label"></span>
                            <div class="pw-rules" id="pw-rules"></div>
                            <span class="field-error" id="newpass-error"></span>
                        </div>

                        {{-- Confirm password --}}
                        <div class="field">
                            <label>Confirm Password <span class="req">*</span></label>
                            <div class="pw-wrap">
                                <input type="password" id="newpass_confirmation" name="newpass_confirmation"
                                       autocomplete="new-password" placeholder="Re-enter new password"
                                       oninput="checkMatch()">
                                <button type="button" class="pw-btn" onclick="togglePw('newpass_confirmation','eyeConfirm')">
                                    <span class="material-icons" id="eyeConfirm">visibility</span>
                                </button>
                            </div>
                            <div id="password-match-message"></div>
                            <span class="field-error" id="newpass_confirmation-error"></span>
                        </div>

                    </div>

                    <div class="btn-row" style="margin-top:6px;">
                        <button type="submit" class="btn btn-change-pw" id="changepass">
                            <span class="material-icons">shield</span> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- Photo update form (submits normally) --}}
<form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" id="photoForm">
    @csrf
    @method('POST')

    <div class="photo-modal-backdrop" id="photoModal">
        <div class="photo-modal-card">
            <div class="photo-modal-head">
                <div class="p-card-icon"><span class="material-icons">photo_camera</span></div>
                <span class="photo-modal-title">Update Profile Photo</span>
                <button type="button" class="btn-icon" onclick="closePhotoModal()"
                        style="width:30px;height:30px;border:1.5px solid var(--border);border-radius:8px;
                               background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);">
                    <span class="material-icons" style="font-size:17px;">close</span>
                </button>
            </div>
            <div class="photo-modal-body">
                <div class="modal-file-wrap">
                    <label class="modal-file-label" for="profile_photo_input">
                        <span class="material-icons">upload</span> Choose Photo
                    </label>
                    <input name="profile_photo" id="profile_photo_input" type="file"
                           accept="image/*" style="display:none;"
                           onchange="validateImage('profile_photo_input')">
                    <span class="modal-file-name" id="photoFileName">No file chosen</span>
                </div>
                @error('profile_photo')
                    <span class="field-error" style="margin-top:8px;display:block;">{{ $message }}</span>
                @enderror
                <p style="font-size:12px;color:var(--muted);margin-top:10px;">
                    JPG, PNG, GIF · Max 1 MB
                </p>
            </div>
            <div class="photo-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closePhotoModal()">Cancel</button>
                <button type="submit" class="btn btn-save">
                    <span class="material-icons">save</span> Update
                </button>
            </div>
        </div>
    </div>
</form>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<script nonce="{{ $cspNonce }}">
/* ── Photo modal ─────────────────────────────────────────── */
function openPhotoModal()  { document.getElementById('photoModal').classList.add('open'); }
function closePhotoModal() { document.getElementById('photoModal').classList.remove('open'); }

document.getElementById('photoModal').addEventListener('click', function(e) {
    if (e.target === this) closePhotoModal();
});

document.getElementById('profile_photo_input').addEventListener('change', function() {
    const name = this.files[0]?.name || 'No file chosen';
    document.getElementById('photoFileName').textContent = name;

    // Preview
    if (this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('avatarPreview');
            if (img) img.src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    }
});

function validateImage(id) {
    const file = document.getElementById(id).files[0];
    if (!file) return false;
    const t = file.type.split('/').pop().toLowerCase();
    if (!['jpeg','jpg','png','gif'].includes(t)) {
        showToast('danger', 'Invalid file', 'Please select a JPG, PNG or GIF image.');
        document.getElementById(id).value = '';
        return false;
    }
    if (file.size > 1050000) {
        showToast('danger', 'File too large', 'Max upload size is 1 MB.');
        document.getElementById(id).value = '';
        return false;
    }
    return true;
}

/* ── Password visibility toggle ──────────────────────────── */
function togglePw(inputId, iconId) {
    const inp  = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const isTxt = inp.type === 'text';
    inp.type = isTxt ? 'password' : 'text';
    if (icon) icon.textContent = isTxt ? 'visibility' : 'visibility_off';
}

/* ── Password strength ───────────────────────────────────── */
function checkStrength() {
    const pw    = document.getElementById('newpass').value;
    const fill  = document.getElementById('strength-fill');
    const label = document.getElementById('strength-label');
    const rules = document.getElementById('pw-rules');

    const checks = {
        upper:  /[A-Z]/.test(pw),
        lower:  /[a-z]/.test(pw),
        number: /[0-9]/.test(pw),
        symbol: /[^A-Za-z0-9]/.test(pw),
        length: pw.length >= 8
    };

    const labels = { upper:'Uppercase', lower:'Lowercase', number:'Number', symbol:'Symbol', length:'8+ chars' };
    const score = Object.values(checks).filter(Boolean).length;

    const levels = [
        {pct:0,  color:'',                text:''},
        {pct:20, color:'#ef4444',         text:'Very weak'},
        {pct:40, color:'#f97316',         text:'Weak'},
        {pct:60, color:'#eab308',         text:'Fair'},
        {pct:80, color:'#22c55e',         text:'Strong'},
        {pct:100,color:'var(--success)',  text:'Very strong'},
    ];

    const lvl = levels[Math.min(score, 5)];
    fill.style.width      = lvl.pct + '%';
    fill.style.background = lvl.color;
    label.textContent     = lvl.text;
    label.style.color     = lvl.color || 'var(--muted)';

    rules.innerHTML = Object.entries(checks).map(([key, ok]) =>
        `<span class="pw-rule ${ok ? 'ok' : 'err'}">
            <span class="material-icons">${ok ? 'check_circle' : 'radio_button_unchecked'}</span>
            ${labels[key]}
         </span>`
    ).join('');

    checkMatch();
}

/* ── Password match ──────────────────────────────────────── */
function checkMatch() {
    const pw  = document.getElementById('newpass').value;
    const cfm = document.getElementById('newpass_confirmation').value;
    const el  = document.getElementById('password-match-message');
    if (!cfm) { el.innerHTML = ''; return; }
    if (pw === cfm) {
        el.innerHTML = '<div class="pw-match ok"><span class="material-icons">check_circle</span> Passwords match</div>';
    } else {
        el.innerHTML = '<div class="pw-match err"><span class="material-icons">cancel</span> Passwords do not match</div>';
    }
}

/* ── Generate password ───────────────────────────────────── */
function genPassword() {
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
    document.getElementById('eyeNew').textContent    = 'visibility_off';
    document.getElementById('eyeConfirm').textContent = 'visibility_off';
    checkStrength();
    showToast('success', 'Password Generated', 'A strong password has been set in both fields.');
}

/* ── Change password form ────────────────────────────────── */
$(document).ready(function() {

    $('#changepassf').on('submit', function(e) {
        e.preventDefault();

        const password = $('#newpass').val();
        const confirm  = $('#newpass_confirmation').val();
        const current  = $('#current_password').val();

        $('.field-error').html('');

        if (!current) { showToast('danger', 'Error', 'Current password is required.'); return; }
        if (password !== confirm) { showToast('danger', 'Error', 'New passwords do not match.'); return; }
        if (!validatePassword(password)) { showToast('danger', 'Error', 'Password does not meet requirements.'); return; }

        const userId = $('#userid').val();
        const btn    = $('#changepass');
        const orig   = btn.html();
        btn.prop('disabled', true).html('<span class="material-icons spin">sync</span> Saving…');

        $.ajax({
            url:  '{{ route("change.pass", ["id" => "__id__"]) }}'.replace('__id__', userId),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', 'Password Updated', response.message);
                    $('#changepassf')[0].reset();
                    document.getElementById('strength-fill').style.width = '0%';
                    document.getElementById('strength-label').textContent = '';
                    document.getElementById('pw-rules').innerHTML = '';
                    document.getElementById('password-match-message').innerHTML = '';
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};
                    $.each(errors, function(key, val) {
                        $('#' + key + '-error').html(val[0]);
                    });
                    showToast('danger', 'Validation Error', 'Please check the form for errors.');
                } else {
                    showToast('danger', 'Error', 'Something went wrong. Please try again.');
                }
            },
            complete: function() { btn.prop('disabled', false).html(orig); }
        });
    });

    // Spinner style
    $('<style nonce="{{ $cspNonce }}">.spin{animation:spin 1s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}</style>').appendTo('head');
});

function validatePassword(password) {
    if (password.length < 8) return false;
    const rules = [/[A-Z]/.test(password), /[a-z]/.test(password), /[0-9]/.test(password), /[^A-Za-z0-9]/.test(password)];
    return rules.filter(Boolean).length >= 3;
}

/* ── Toast ───────────────────────────────────────────────── */
function showToast(type, title, message) {
    const icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber' };
    const t = document.createElement('div');
    t.className = 'toast-msg ' + type;
    t.innerHTML = '<span class="material-icons">' + (icons[type]||'info') + '</span>'
                + '<div><strong>' + title + '</strong> ' + message + '</div>';
    document.getElementById('toastWrap').appendChild(t);
    const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
    t.addEventListener('click', dismiss);
    setTimeout(dismiss, 5000);
}

/* Legacy shim */
function showMessage(msg, type) {
    showToast(type === 'success' ? 'success' : 'danger', type === 'success' ? 'Success' : 'Error', msg);
}
</script>

</x-custom-admin-layout>