<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Pay — Disable Two-Factor Authentication</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    @vite(['resources/css/app.scss', 'resources/css/icon-font.min.css', 'resources/css/style.css'])

    <style nonce="{{ $cspNonce }}">
           
        /* ── Page shell ──────────────────────────────────────── */
        .page-shell {
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr;
        }

        /* ── Topbar ──────────────────────────────────────────── */
        .topbar {
            padding: 16px 40px;
            display: flex; align-items: center; justify-content: space-between;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .topbar .brand img { height: 34px; object-fit: contain; }
        .topbar .tagline { font-size: 13px; color: var(--muted); letter-spacing: .02em; }

        /* ── Stage ───────────────────────────────────────────── */
        .stage {
            display: flex; align-items: center; justify-content: center;
            padding: 48px 20px;
        }

        /* ── Card ────────────────────────────────────────────── */
        .disable-card {
            background: var(--surface);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            width: 100%; max-width: 440px;
            overflow: hidden;
            animation: rise .5s cubic-bezier(.22,.61,.36,1) both;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Red accent strip */
        .card-accent {
            height: 5px;
            background: linear-gradient(90deg, #dc2626 0%, #ef4444 60%, #f87171 100%);
        }

        /* ── Card header ─────────────────────────────────────── */
        .card-header {
            padding: 26px 32px 20px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .header-icon {
            width: 56px; height: 56px; border-radius: 16px;
            background: var(--danger-lt);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }

        .header-icon .material-icons { font-size: 28px; color: var(--danger); }

        .card-header h1 {
            font-family: var(--font-head);
            font-size: 20px; font-weight: 700; color: var(--ink);
            margin: 0 0 5px;
        }

        .card-header p { font-size: 13.5px; color: var(--muted); margin: 0; }

        /* ── Card body ───────────────────────────────────────── */
        .card-body { padding: 28px 32px; }

        /* ── Inline alerts ───────────────────────────────────── */
        .inline-alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 14px; border-radius: var(--radius-sm);
            font-size: 13.5px; margin-bottom: 20px; line-height: 1.5;
        }

        .inline-alert .material-icons { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
        .inline-alert.danger  { background: var(--danger-lt);  color: var(--danger); }
        .inline-alert.warning { background: var(--warning-lt); color: #92400e; }
        .inline-alert.warning .material-icons { color: var(--warning); }

        /* ── Description text ────────────────────────────────── */
        .desc-text {
            font-size: 13.5px; color: var(--muted);
            text-align: center; margin-bottom: 24px; line-height: 1.6;
        }

        /* ── OTP boxes ───────────────────────────────────────── */
        .otp-label {
            font-size: 12.5px; font-weight: 500; color: #374151;
            margin-bottom: 10px; display: block;
        }

        .otp-boxes {
            display: flex; gap: 8px; justify-content: center;
            margin-bottom: 8px;
        }

        .otp-box {
            width: 50px; height: 56px;
            border: 1.5px solid var(--border); border-radius: 12px;
            background: #fafafa;
            font-family: var(--font-head);
            font-size: 22px; font-weight: 700; text-align: center;
            color: var(--ink); outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            caret-color: var(--danger);
        }

        .otp-box:focus {
            border-color: var(--danger);
            background: var(--surface);
            box-shadow: 0 0 0 4px rgba(220,38,38,.1);
        }

        .otp-box.filled { border-color: #fca5a5; background: var(--danger-lt); }

        .otp-box.error {
            border-color: var(--danger);
            background: var(--danger-lt);
            animation: shake .4s ease;
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%     { transform: translateX(-6px); }
            40%     { transform: translateX(6px); }
            60%     { transform: translateX(-4px); }
            80%     { transform: translateX(4px); }
        }

        .field-error {
            font-size: 12px; color: var(--danger);
            text-align: center; min-height: 18px; margin-bottom: 16px;
        }

        /* Hidden real input */
        #otp { display: none; }

        /* ── Buttons ─────────────────────────────────────────── */
        .btn-disable {
            width: 100%; height: 50px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff; border: none;
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 15px; font-weight: 600;
            cursor: pointer; display: flex;
            align-items: center; justify-content: center; gap: 8px;
            letter-spacing: .02em;
            transition: transform .2s, box-shadow .2s, filter .2s;
            box-shadow: 0 4px 16px rgba(220,38,38,.28);
            margin-bottom: 12px;
        }

        .btn-disable:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,.38);
            filter: brightness(1.05);
        }

        .btn-disable:active:not(:disabled) { transform: translateY(0); }

        .btn-disable:disabled {
            opacity: .5; cursor: not-allowed; transform: none; filter: none;
        }

        .btn-disable .material-icons { font-size: 20px; }

        .btn-back {
            width: 100%; height: 44px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 14px; font-weight: 500; color: var(--muted);
            cursor: pointer; display: flex;
            align-items: center; justify-content: center; gap: 7px;
            text-decoration: none; transition: color .2s, border-color .2s;
        }

        .btn-back:hover { color: var(--ink); border-color: #9ca3af; }
        .btn-back .material-icons { font-size: 17px; }

        /* Spinner */
        .spin { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Toast ───────────────────────────────────────────── */
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
        .toast-msg.warning { background: var(--warning-lt); color: #92400e; }
        .toast-msg .material-icons { font-size: 20px; flex-shrink: 0; }

        @media (max-width: 520px) {
            .card-body, .card-header { padding: 20px 18px; }
            .topbar { padding: 14px 18px; }
            .otp-box { width: 42px; height: 50px; font-size: 20px; }
        }
    </style>
</head>
<body>

<div class="toast-wrap" id="toastWrap"></div>

<div class="page-shell">

    <!-- Topbar -->
    <header class="topbar">
        <div class="brand">
            <img src="{{ asset('images/schaxist.png') }}" alt="Core Pay">
        </div>
        <span class="tagline">Corepay</span>
    </header>

    <!-- Stage -->
    <main class="stage">
        <div class="disable-card">
            <div class="card-accent"></div>

            <!-- Header -->
            <div class="card-header">
                <div class="header-icon">
                    <span class="material-icons">no_encryption</span>
                </div>
                <h1>Disable Two-Factor Auth</h1>
                <p>This will reduce your account's security</p>
            </div>

            <!-- Body -->
            <div class="card-body">

                @if(session('error'))
                <div class="inline-alert danger">
                    <span class="material-icons">error_outline</span>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                <!-- Warning -->
                <div class="inline-alert warning">
                    <span class="material-icons">warning_amber</span>
                    <div>
                        <strong>Are you sure?</strong>
                        Disabling two-factor authentication will make your account significantly less secure. Anyone who knows your password could access your account.
                    </div>
                </div>

                <p class="desc-text">
                    Enter the 6-digit code from your authenticator app to confirm that you want to disable 2FA.
                </p>

                <form method="POST" action="{{ route('2fa.disable') }}" id="disableForm">
                    @csrf

                    <span class="otp-label">Authentication code</span>

                    <!-- Six digit boxes -->
                    <div class="otp-boxes" id="otpBoxes">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="0">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="1">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="2">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="3">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="4">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="5">
                    </div>

                    <p class="field-error" id="otp-field-error"></p>

                    {{-- Hidden real input populated by JS --}}
                    <input type="text" id="otp" name="otp" readonly>

                    <button type="submit" class="btn-disable" id="submitBtn" disabled>
                        <span class="material-icons">no_encryption</span>
                        <span id="btnLabel">Disable 2FA</span>
                    </button>

                    <a href="{{ route('profile.edit') }}" class="btn-back">
                        <span class="material-icons">arrow_back</span>
                        Back to Profile
                    </a>
                </form>

            </div>
        </div>
    </main>
</div>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function () {

    /* ── OTP boxes ───────────────────────────────────────── */
    const boxes  = Array.from(document.querySelectorAll('.otp-box'));
    const hidden = document.getElementById('otp');
    const submit = document.getElementById('submitBtn');
    const errEl  = document.getElementById('otp-field-error');

    function getCode () { return boxes.map(b => b.value).join(''); }

    function syncHidden () {
        const code = getCode();
        hidden.value   = code;
        submit.disabled = code.length < 6;
        boxes.forEach(b => b.classList.toggle('filled', b.value !== ''));
    }

    boxes.forEach((box, i) => {
        box.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (box.value === '' && i > 0) { boxes[i-1].value = ''; boxes[i-1].focus(); }
                else box.value = '';
                syncHidden(); e.preventDefault();
            }
        });

        box.addEventListener('input', function () {
            box.value = box.value.replace(/[^0-9]/g, '').slice(-1);
            syncHidden();
            errEl.textContent = '';
            boxes.forEach(b => b.classList.remove('error'));
            if (box.value && i < 5) boxes[i+1].focus();
        });

        box.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            pasted.split('').forEach((ch, idx) => { if (boxes[idx]) boxes[idx].value = ch; });
            syncHidden();
            if (boxes[pasted.length]) boxes[pasted.length].focus();
        });
    });

    boxes[0].focus();

    /* ── Form submit ─────────────────────────────────────── */
    document.getElementById('disableForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const code = getCode();
        if (code.length !== 6) {
            errEl.textContent = 'Please enter a valid 6-digit code.';
            boxes.forEach(b => b.classList.add('error'));
            setTimeout(() => boxes.forEach(b => b.classList.remove('error')), 600);
            return;
        }

        submit.disabled = true;
        submit.innerHTML = '<span class="material-icons spin">sync</span><span>Verifying…</span>';

        // Standard form POST (original used native form action, not AJAX)
        // Build a real form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('2fa.disable') }}";

        const csrfInput = document.createElement('input');
        csrfInput.type  = 'hidden';
        csrfInput.name  = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                       || document.querySelector('input[name="_token"]')?.value || '';

        const otpInput  = document.createElement('input');
        otpInput.type   = 'hidden';
        otpInput.name   = 'otp';
        otpInput.value  = code;

        form.appendChild(csrfInput);
        form.appendChild(otpInput);
        document.body.appendChild(form);
        form.submit();
    });

    /* ── Toast ───────────────────────────────────────────── */
    function showToast (type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber' };
        const t = document.createElement('div');
        t.className = 'toast-msg ' + type;
        t.innerHTML = '<span class="material-icons">' + (icons[type]||'info') + '</span>'
                    + '<div><strong>' + title + '</strong> ' + message + '</div>';
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }

    /* ── Legacy showAlert shim (in case any other JS calls it) */
    window.showAlert = function (type, title, message) {
        showToast(type === 'success' ? 'success' : 'danger', title, message);
    };

});
</script>
</body>
</html>