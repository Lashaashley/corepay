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

    const form = document.getElementById('twoFactorForm');
    /* ── Form submit ─────────────────────────────── */
    function submitForm() {
        
        const code = getCode();
        if (code.length !== 6) return;

        const btnLabel = document.getElementById('btnLabel');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-icons spin">sync</span><span>Verifying…</span>';

        const formData = new FormData(form);

        const verifyUrl = form.dataset.verifyUrl;

        fetch(verifyUrl, {
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