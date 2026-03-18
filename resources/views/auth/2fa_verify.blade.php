<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Pay — Two-Factor Verification</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    @vite(['resources/css/app.scss', 'resources/css/icon-font.min.css', 'resources/css/style.css'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:       #0d1117;
            --surface:   #ffffff;
            --muted:     #6b7280;
            --border:    #e5e7eb;
            --accent:    #1a56db;
            --accent-lt: #eff6ff;
            --success:   #059669;
            --success-lt:#ecfdf5;
            --danger:    #dc2626;
            --danger-lt: #fef2f2;
            --warning:   #d97706;
            --warning-lt:#fffbeb;
            --radius:    14px;
            --shadow-lg: 0 12px 48px rgba(0,0,0,.12);
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: #f3f4f8;
            color: var(--ink);
        }

        .page-shell {
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr;
        }

        /* ── Top bar ── */
        .topbar {
            padding: 18px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .topbar .brand img { height: 36px; object-fit: contain; }
        .topbar .tagline { font-size: 13px; color: var(--muted); letter-spacing: .02em; }

        /* ── Stage ── */
        .stage {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 20px;
        }

        /* ── Card ── */
        .card {
            background: var(--surface);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            animation: rise .55s cubic-bezier(.22,.61,.36,1) both;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-accent {
            height: 5px;
            background: linear-gradient(90deg, #1a56db 0%, #6366f1 60%, #8b5cf6 100%);
        }

        .card-body { padding: 40px 44px 44px; }

        /* ── Shield icon ── */
        .shield-wrap {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: var(--accent-lt);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .shield-wrap .material-icons {
            font-size: 32px;
            color: var(--accent);
        }

        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--ink);
            text-align: center;
            margin-bottom: 6px;
        }

        .card-subtitle {
            font-size: 14px;
            color: var(--muted);
            text-align: center;
            margin-bottom: 32px;
        }

        /* ── Inline alerts ── */
        .inline-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-radius: var(--radius);
            font-size: 13.5px;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .inline-alert .material-icons { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
        .inline-alert.info    { background: var(--accent-lt); color: #1e40af; }
        .inline-alert.danger  { background: var(--danger-lt); color: var(--danger); }

        /* ── OTP digit inputs ── */
        .otp-label {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 12px;
            display: block;
        }

        .otp-boxes {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 10px;
        }

        .otp-box {
            width: 52px;
            height: 58px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            background: #fafafa;
            font-family: 'Syne', sans-serif;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            color: var(--ink);
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            caret-color: var(--accent);
        }

        .otp-box:focus {
            border-color: var(--accent);
            background: var(--surface);
            box-shadow: 0 0 0 4px rgba(26,86,219,.1);
        }

        .otp-box.filled {
            border-color: #6366f1;
            background: var(--accent-lt);
        }

        .otp-box.error {
            border-color: var(--danger);
            background: var(--danger-lt);
            animation: shake .4s ease;
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-6px); }
            40%      { transform: translateX(6px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }

        .field-error {
            font-size: 12px;
            color: var(--danger);
            text-align: center;
            min-height: 18px;
            margin-bottom: 4px;
        }

        /* Hidden real input for form submit */
        #one_time_password { display: none; }

        /* ── Countdown ── */
        .countdown-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 28px;
        }

        .countdown-row .material-icons { font-size: 15px; }
        #countdown { font-weight: 600; color: var(--ink); transition: color .3s; }
        #countdown.expired { color: var(--danger); }
        #countdown.warning { color: var(--warning); }

        /* ── Submit ── */
        .btn-verify {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #1a56db, #4f46e5);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: .02em;
            transition: transform .2s, box-shadow .2s, filter .2s, background .3s;
            box-shadow: 0 4px 16px rgba(26,86,219,.3);
        }

        .btn-verify:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(26,86,219,.4);
            filter: brightness(1.05);
        }

        .btn-verify:active:not(:disabled) { transform: translateY(0); }

        .btn-verify:disabled {
            opacity: .65;
            cursor: not-allowed;
            transform: none;
        }

        .btn-verify.success-state {
            background: linear-gradient(135deg, #059669, #10b981);
            box-shadow: 0 4px 16px rgba(5,150,105,.35);
        }

        .btn-verify .material-icons { font-size: 20px; }

        /* Spinner */
        .spin { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Divider ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 28px 0 20px;
            color: var(--border);
            font-size: 12px;
            color: var(--muted);
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── Footer links ── */
        .footer-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            font-size: 13px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: opacity .2s;
        }

        .footer-links a:hover { opacity: .75; }
        .footer-links a .material-icons { font-size: 15px; }

        .footer-links .sep {
            width: 1px;
            height: 14px;
            background: var(--border);
        }

        /* ── Card footer ── */
        .card-footer {
            padding: 14px 44px;
            background: #f9fafb;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            color: var(--muted);
        }

        .card-footer .material-icons { font-size: 14px; color: var(--success); }

        /* ── Toast notification ── */
        .toast-wrap {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast-msg {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 14px;
            min-width: 280px;
            max-width: 360px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            animation: toastIn .35s cubic-bezier(.22,.61,.36,1) both;
            cursor: pointer;
        }

        .toast-msg.leaving { animation: toastOut .3s ease forwards; }

        @keyframes toastIn {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes toastOut {
            to { opacity: 0; transform: translateX(40px); }
        }

        .toast-msg.success { background: var(--success-lt); color: #065f46; }
        .toast-msg.danger  { background: var(--danger-lt);  color: #991b1b; }
        .toast-msg .material-icons { font-size: 20px; flex-shrink: 0; }

        /* ── Recovery modal ── */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(3px);
            z-index: 8000;
            display: none;
            align-items: center;
            justify-content: center;
            animation: fadeIn .2s ease;
        }

        .modal-backdrop.open { display: flex; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .modal-card {
            background: var(--surface);
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            margin: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
            animation: rise .3s cubic-bezier(.22,.61,.36,1) both;
        }

        .modal-header {
            padding: 20px 24px;
            background: var(--warning-lt);
            border-bottom: 1px solid #fde68a;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #92400e;
        }

        .modal-header .material-icons { font-size: 20px; color: var(--warning); }

        .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            color: #92400e;
            display: flex;
            align-items: center;
            padding: 4px;
            border-radius: 6px;
            transition: background .2s;
        }

        .modal-close:hover { background: rgba(0,0,0,.07); }
        .modal-close .material-icons { font-size: 20px; }

        .modal-body { padding: 24px; }

        .modal-body p {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 16px;
        }

        .modal-field label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .modal-field input {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-family: 'DM Sans', monospace;
            font-size: 15px;
            letter-spacing: 2px;
            color: var(--ink);
            background: #fafafa;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .modal-field input:focus {
            border-color: var(--warning);
            background: var(--surface);
            box-shadow: 0 0 0 4px rgba(217,119,6,.1);
        }

        .btn-recovery {
            width: 100%;
            height: 46px;
            margin-top: 16px;
            background: linear-gradient(135deg, #d97706, #f59e0b);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 4px 14px rgba(217,119,6,.3);
        }

        .btn-recovery:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(217,119,6,.4);
        }

        /* ── Responsive ── */
        @media (max-width: 520px) {
            .card-body { padding: 32px 24px 36px; }
            .topbar { padding: 16px 20px; }
            .otp-box { width: 44px; height: 52px; font-size: 20px; }
        }
    </style>
</head>
<body>

<div class="page-shell">

    <!-- Top bar -->
    <header class="topbar">
        <div class="brand">
            <img src="{{ asset('images/schaxist.png') }}" alt="Core Pay">
        </div>
        <span class="tagline">Payroll Management System</span>
    </header>

    <!-- Stage -->
    <main class="stage">
        <div class="card">
            <div class="card-accent"></div>

            <div class="card-body">

                <!-- Icon -->
                <div class="shield-wrap">
                    <span class="material-icons">shield</span>
                </div>

                <h1 class="card-title">Two-Factor Verification</h1>
                <p class="card-subtitle">Enter the 6-digit code from your authenticator app</p>

                <!-- Session error -->
                @if(session('error'))
                <div class="inline-alert danger">
                    <span class="material-icons">error_outline</span>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                <!-- Info hint -->
                <div class="inline-alert info">
                    <span class="material-icons">info_outline</span>
                    <span>Open your authenticator app and enter the current code to continue.</span>
                </div>

                <!-- 2FA Form -->
                <form id="twoFactorForm" method="POST">
                    @csrf
                    <!-- Hidden input sent on submit -->
                    <input type="text" id="one_time_password" name="one_time_password" readonly>

                    <label class="otp-label">Authentication code</label>

                    <!-- 6 visual digit boxes -->
                    <div class="otp-boxes" id="otpBoxes">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="0">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="1">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="2">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="3">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="4">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="5">
                    </div>

                    <p class="field-error" id="otp-error">
                        @error('one_time_password') {{ $message }} @enderror
                    </p>

                    <!-- Countdown -->
                    <div class="countdown-row">
                        <span class="material-icons">schedule</span>
                        Code expires in <span id="countdown">5:00</span>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-verify" id="submitBtn" disabled>
                        <span class="material-icons">verified_user</span>
                        <span id="btnLabel">Verify and Continue</span>
                    </button>
                </form>

                <div class="divider">or</div>

                <div class="footer-links">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="material-icons">logout</span> Back to Login
                    </a>
                    <div class="sep"></div>
                    <a href="#" id="openRecovery">
                        <span class="material-icons">key</span> Use Recovery Code
                    </a>
                </div>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">
                    @csrf
                </form>

            </div><!-- /card-body -->

            <div class="card-footer">
                <span class="material-icons">lock</span>
                Secured with two-factor authentication
            </div>
        </div><!-- /card -->
    </main>
</div>

<!-- Toast container -->
<div class="toast-wrap" id="toastWrap"></div>

<!-- Recovery Code Modal -->
<div class="modal-backdrop" id="recoveryModal">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-header-left">
                <span class="material-icons">key</span>
                Use Recovery Code
            </div>
            <button class="modal-close" id="closeRecovery">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="modal-body">
            <p>Lost access to your authenticator? Enter one of your one-time recovery codes below.</p>
            <form method="POST" id="recoveryForm">
                @csrf
                <div class="modal-field">
                    <label for="recovery_code">Recovery code</label>
                    <input type="text" name="recovery_code" id="recovery_code"
                           placeholder="xxxx-xxxx-xxxx" autocomplete="off" required>
                </div>
                <button type="submit" class="btn-recovery">Verify Recovery Code</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── OTP boxes logic ─────────────────────────── */
    const boxes      = Array.from(document.querySelectorAll('.otp-box'));
    const hiddenInput = document.getElementById('one_time_password');
    const submitBtn  = document.getElementById('submitBtn');
    const otpError   = document.getElementById('otp-error');

    function getCode() { return boxes.map(b => b.value).join(''); }

    function syncHidden() {
        const code = getCode();
        hiddenInput.value = code;
        submitBtn.disabled = code.length < 6;
        boxes.forEach(b => b.classList.toggle('filled', b.value !== ''));
    }

    boxes.forEach((box, i) => {
        box.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (box.value === '' && i > 0) {
                    boxes[i - 1].value = '';
                    boxes[i - 1].focus();
                } else {
                    box.value = '';
                }
                syncHidden();
                e.preventDefault();
            }
        });

        box.addEventListener('input', function () {
            const val = box.value.replace(/[^0-9]/g, '');
            box.value = val.slice(-1);
            syncHidden();
            otpError.textContent = '';
            boxes.forEach(b => b.classList.remove('error'));

            if (val && i < 5) boxes[i + 1].focus();
            if (getCode().length === 6) submitForm();
        });

        box.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            pasted.split('').forEach((ch, idx) => {
                if (boxes[idx]) boxes[idx].value = ch;
            });
            syncHidden();
            if (pasted.length === 6) submitForm();
            else if (boxes[pasted.length]) boxes[pasted.length].focus();
        });
    });

    boxes[0].focus();

    /* ── Form submit ─────────────────────────────── */
    function submitForm() {
        const code = getCode();
        if (code.length !== 6) return;

        const btnLabel = document.getElementById('btnLabel');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-icons spin">sync</span><span>Verifying…</span>';

        const formData = new FormData(document.getElementById('twoFactorForm'));

        fetch("{{ route('2fa.check') }}", {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json().then(data => ({ ok: r.ok, status: r.status, data })))
        .then(({ ok, status, data }) => {
            if (ok) {
                submitBtn.classList.add('success-state');
                submitBtn.innerHTML = '<span class="material-icons">check_circle</span><span>Verified!</span>';
                showToast('success', 'Verified!', data.message || 'Redirecting…');
                setTimeout(() => window.location.href = data.redirect, 1200);
            } else {
                // Reset boxes on error
                boxes.forEach(b => { b.value = ''; b.classList.add('error'); });
                syncHidden();
                setTimeout(() => boxes.forEach(b => b.classList.remove('error')), 600);
                boxes[0].focus();

                let msg = 'Invalid code. Please try again.';
                if (data?.errors?.one_time_password) msg = data.errors.one_time_password[0];
                else if (data?.message) msg = data.message;

                otpError.textContent = msg;
                showToast('danger', 'Invalid Code', msg);

                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span class="material-icons">verified_user</span><span>Verify and Continue</span>';
            }
        })
        .catch(() => {
            showToast('danger', 'Error', 'Something went wrong. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="material-icons">verified_user</span><span>Verify and Continue</span>';
            boxes[0].focus();
        });
    }

    document.getElementById('twoFactorForm').addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm();
    });

    /* ── Countdown ───────────────────────────────── */
    let timeLeft = 300;
    const countdownEl = document.getElementById('countdown');

    const timer = setInterval(() => {
        timeLeft--;
        if (timeLeft <= 0) {
            clearInterval(timer);
            countdownEl.textContent = 'Expired';
            countdownEl.classList.add('expired');
            submitBtn.disabled = true;
            showToast('danger', 'Code Expired', 'Please go back and log in again.');
        } else {
            const m = Math.floor(timeLeft / 60);
            const s = timeLeft % 60;
            countdownEl.textContent = `${m}:${s.toString().padStart(2, '0')}`;
            countdownEl.classList.toggle('warning', timeLeft <= 60);
        }
    }, 1000);

    /* ── Toast ───────────────────────────────────── */
    function showToast(type, title, message) {
        const wrap = document.getElementById('toastWrap');
        const t = document.createElement('div');
        const icon = type === 'success' ? 'check_circle' : 'error_outline';
        t.className = `toast-msg ${type}`;
        t.innerHTML = `<span class="material-icons">${icon}</span><div><strong>${title}</strong> ${message}</div>`;
        wrap.appendChild(t);

        const dismiss = () => {
            t.classList.add('leaving');
            setTimeout(() => t.remove(), 300);
        };

        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }

    /* ── Recovery modal ──────────────────────────── */
    const modal        = document.getElementById('recoveryModal');
    const openBtn      = document.getElementById('openRecovery');
    const closeBtn     = document.getElementById('closeRecovery');

    openBtn.addEventListener('click', (e) => { e.preventDefault(); modal.classList.add('open'); });
    closeBtn.addEventListener('click', () => modal.classList.remove('open'));
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('open'); });

});
</script>
</body>
</html>