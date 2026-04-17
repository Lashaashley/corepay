<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Pay — Two-Factor Verification</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    @vite(['resources/css/pages/2faverify.css'])
    @vite('resources/css/app.scss')
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
    <a href="#" id="logoutLink">
        <span class="material-icons">logout</span> Back to Login
    </a>
    <div class="sep"></div>
    <a href="#" id="openRecovery">
        <span class="material-icons">key</span> Use Recovery Code
    </a>
</div>

<!-- Hidden logout form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function () {

    /* ── Logout functionality ─────────────────────── */
    const logoutLink = document.getElementById('logoutLink');
    const logoutForm = document.getElementById('logout-form');
    
    if (logoutLink && logoutForm) {
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            logoutForm.submit();
        });
    }

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

    if (openBtn) {
        openBtn.addEventListener('click', (e) => { 
            e.preventDefault(); 
            modal.classList.add('open'); 
        });
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', () => modal.classList.remove('open'));
    }
    
    if (modal) {
        modal.addEventListener('click', (e) => { 
            if (e.target === modal) modal.classList.remove('open'); 
        });
    }

});
</script>
</body>
</html>