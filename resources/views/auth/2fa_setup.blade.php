<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Pay — Setup Two-Factor Authentication</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    
     @vite(['resources/css/pages/2fasetup.css'])
     @vite('resources/css/app.scss')

    <style nonce="{{ $cspNonce }}">
      
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
        <span class="tagline">Payroll Management System</span>
    </header>

    <!-- Stage -->
    <main class="stage">
        <div class="setup-card">
            <div class="card-accent"></div>

            <!-- Header -->
            <div class="card-header">
                <div class="header-icon">
                    <span class="material-icons">shield</span>
                </div>
                <h1>Set Up Two-Factor Auth</h1>
                <p>Protect your account with an authenticator app</p>
            </div>

            <!-- Body -->
            <div class="card-body">

                @if(session('error'))
                <div class="inline-alert danger">
                    <span class="material-icons">error_outline</span>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                <!-- Step 1: QR Code -->
                <div class="step-label">
                    <div class="step-num">1</div>
                    <h2>Scan QR Code</h2>
                </div>

                <div class="qr-block">
                    <div class="qr-wrap">
                        <img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}" alt="QR Code">
                    </div>
                </div>

                <div class="inline-alert info">
                    <span class="material-icons">info_outline</span>
                    <span>Scan with <strong>Google Authenticator</strong>, <strong>Authy</strong>, or any TOTP-compatible app.</span>
                </div>

                <hr class="section-divider">

                <!-- Step 2: Manual entry -->
                <div class="step-label">
                    <div class="step-num">2</div>
                    <h2>Can't Scan? Manual Entry</h2>
                </div>

                <div class="secret-block">
                    <code id="secretKey">{{ $secret }}</code>
                    <button type="button" class="copy-btn" onclick="copySecretKey()">
                        <span class="material-icons">content_copy</span> Copy
                    </button>
                </div>

                <p style="font-size:12px;color:var(--muted);margin-bottom:0;">
                    Enter this secret key manually in your authenticator app if the QR scan doesn't work.
                </p>

                <hr class="section-divider">

                <!-- Step 3: Verify -->
                <div class="step-label">
                    <div class="step-num">3</div>
                    <h2>Verify Setup</h2>
                </div>

                <form id="setupForm" method="POST">
                    @csrf

                    <span class="otp-label">Enter the 6-digit code from your app</span>

                    <!-- Six digit boxes -->
                    <div class="otp-boxes" id="otpBoxes">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="0">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="1">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="2">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="3">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="4">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="5">
                    </div>

                    <p class="field-error" id="otp-error"></p>

                    {{-- Hidden input — populated by JS before submit --}}
                    <input type="text" id="otp" name="otp" readonly>

                    <button type="submit" class="btn-enable" id="submitBtn" disabled>
                        <span class="material-icons">verified_user</span>
                        <span id="btnLabel">Enable Two-Factor Authentication</span>
                    </button>

                    <a href="{{ route('profile.edit') }}" class="btn-cancel">
                        <span class="material-icons">close</span>
                        Cancel
                    </a>
                </form>

            </div>
        </div>
    </main>
</div>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function () {

    /* ── OTP boxes logic ─────────────────────────────────── */
    const boxes   = Array.from(document.querySelectorAll('.otp-box'));
    const hidden  = document.getElementById('otp');
    const submit  = document.getElementById('submitBtn');
    const errEl   = document.getElementById('otp-error');

    function getCode () { return boxes.map(b => b.value).join(''); }

    function syncHidden () {
        const code = getCode();
        hidden.value = code;
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
            if (getCode().length === 6) submitForm();
        });

        box.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/[^0-9]/g, '').slice(0,6);
            pasted.split('').forEach((ch, idx) => { if (boxes[idx]) boxes[idx].value = ch; });
            syncHidden();
            if (pasted.length === 6) submitForm();
            else if (boxes[pasted.length]) boxes[pasted.length].focus();
        });
    });

    boxes[0].focus();

    /* ── Form submit ─────────────────────────────────────── */
    function submitForm () {
        const code = getCode();
        if (code.length !== 6) return;

        const btnLabel = document.getElementById('btnLabel');
        submit.disabled = true;
        submit.innerHTML = '<span class="material-icons spin">sync</span><span>Verifying…</span>';

        const formData = new FormData(document.getElementById('setupForm'));

        fetch("{{ route('2fa.enable') }}", {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json().then(data => ({ ok: r.ok, status: r.status, data })))
        .then(({ ok, status, data }) => {
            if (ok) {
                submit.style.background = 'linear-gradient(135deg,#059669,#10b981)';
                submit.innerHTML = '<span class="material-icons">check_circle</span><span>2FA Enabled!</span>';
                showToast('success', '2FA Enabled', data.message || 'Redirecting…');
                setTimeout(() => window.location.href = data.redirect, 1200);
            } else {
                // Reset boxes
                boxes.forEach(b => { b.value = ''; b.classList.add('error'); });
                syncHidden();
                setTimeout(() => boxes.forEach(b => b.classList.remove('error')), 600);
                boxes[0].focus();

                let msg = 'Invalid code. Please try again.';
                if (data?.errors?.otp) msg = data.errors.otp[0];
                else if (data?.message) msg = data.message;

                errEl.textContent = msg;
                showToast('danger', 'Verification Failed', msg);

                submit.disabled = false;
                submit.innerHTML = '<span class="material-icons">verified_user</span><span>Enable Two-Factor Authentication</span>';
            }
        })
        .catch(() => {
            showToast('danger', 'Error', 'Something went wrong. Please try again.');
            submit.disabled = false;
            submit.innerHTML = '<span class="material-icons">verified_user</span><span>Enable Two-Factor Authentication</span>';
            boxes[0].focus();
        });
    }

    document.getElementById('setupForm').addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm();
    });

    /* ── Copy secret key ─────────────────────────────────── */
    window.copySecretKey = function () {
        const key = document.getElementById('secretKey').textContent.trim();
        navigator.clipboard?.writeText(key).then(() => {
            showToast('success', 'Copied!', 'Secret key copied to clipboard.');
        }).catch(() => {
            const el = document.createElement('textarea');
            el.value = key; document.body.appendChild(el);
            el.select(); document.execCommand('copy');
            document.body.removeChild(el);
            showToast('success', 'Copied!', 'Secret key copied to clipboard.');
        });
    };

    /* ── Toast ───────────────────────────────────────────── */
    function showToast (type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success:'check_circle', danger:'error_outline' };
        const t = document.createElement('div');
        t.className = 'toast-msg ' + type;
        t.innerHTML = '<span class="material-icons">' + icons[type] + '</span>'
                    + '<div><strong>' + title + '</strong> ' + message + '</div>';
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }

});
</script>
</body>
</html>