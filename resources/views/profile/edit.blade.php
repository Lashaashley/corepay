<x-custom-admin-layout>

@vite(['resources/css/pages/edit.css'])
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
                        <div class="avatar-edit-btn" id="edit-btn" title="Change photo">
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
                                <span class="prosuccess">
                                    <span class="material-icons" style="font-size:15px;">check_circle</span> Saved
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- 2FA card --}}
           

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
                                <button type="button" class="pw-btn" id="pw-btn">
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
                                       >
                                <button type="button" class="pw-btn" id="pw-btn2">
                                    <span class="material-icons" id="eyeNew">visibility</span>
                                </button>
                                <button type="button" class="pw-gen-btn" id="generate-password"
                                        >
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
                                      >
                                <button type="button" class="pw-btn" class="pw-btn3">
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
                <button type="button" class="btn-icon" id="btn-icon"
                        >
                    <span class="material-icons" style="font-size:17px;">close</span>
                </button>
            </div>
            <div class="photo-modal-body">
                <div class="modal-file-wrap">
                    <label class="modal-file-label" for="profile_photo_input">
                        <span class="material-icons">upload</span> Choose Photo
                    </label>
                    <input name="profile_photo" id="profile_photo_input" type="file"
                           accept="image/*"
                           onchange="">
                    <span class="modal-file-name" id="photoFileName">No file chosen</span>
                </div>
                @error('profile_photo')
                    <span class="field-error" style="margin-top:8px;display:block;">{{ $message }}</span>
                @enderror
                <p class="pstyle" >
                    JPG, PNG, GIF · Max 1 MB
                </p>
            </div>
            <div class="photo-modal-foot">
                <button type="button" class="btn btn-ghost" id="btn-ghost" >Cancel</button>
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


    $('#pw-btn').on('click', function () {
        togglePw('current_password','eyeCurrent');
     });

     $('#pw-btn2').on('click', function () {
        togglePw('newpass','eyeNew');
     });
      $('#pw-btn3').on('click', function () {
        togglePw('newpass_confirmation','eyeConfirm');
     });

      $('#newpass').on('input', function () {
        checkStrength();
     });

     $('#newpass_confirmation').on('input', function () {
        checkMatch();
     });

     $('#generate-password').on('click', function () {
        genPassword();
     });

      $('#profile_photo_input').on('change', function () {
        validateImage('profile_photo_input');
     });

      $('#btn-icon').on('click', function () {
        closePhotoModal();
     });
     $('#btn-ghost').on('click', function () {
        closePhotoModal();
     });
     $('#edit-btn').on('click', function () {
        openPhotoModal();
     });
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