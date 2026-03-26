<x-custom-admin-layout>
 <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<style nonce="{{ $cspNonce }}">
    .close-period-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }

 
    /* ── Two-column layout ───────────────────────────────────── */
    .cp-layout {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 20px;
        align-items: start;
    }
 
    @media (max-width: 900px) { .cp-layout { grid-template-columns: 1fr; } }
 
    /* ── Card ────────────────────────────────────────────────── */
    .cp-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
    }
 
    .cp-card:nth-child(2) { animation-delay: .07s; }
 
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
 
    .cp-card-head {
        display: flex; align-items: center; gap: 10px;
        padding: 16px 20px; border-bottom: 1px solid var(--border);
    }
 
    .cp-icon {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
 
    .cp-icon.red     { background: var(--danger-lt); }
    .cp-icon.red     .material-icons { color: var(--danger); font-size: 17px; }
    .cp-icon.blue    { background: var(--accent-lt); }
    .cp-icon.blue    .material-icons { color: var(--accent); font-size: 17px; }
    .cp-icon.green   { background: var(--success-lt); }
    .cp-icon.green   .material-icons { color: var(--success); font-size: 17px; }
 
    .cp-card-title {
        font-family: var(--font-head);
        font-size: 14px; font-weight: 700; color: var(--ink); margin: 0 0 2px;
    }
 
    .cp-card-subtitle { font-size: 12px; color: var(--muted); margin: 0; }
 
    .cp-card-body { padding: 20px; }
 
    /* ── Period display ──────────────────────────────────────── */
    .period-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
        margin-bottom: 18px;
    }
 
    .period-cell {
        background: var(--bg); border: 1px solid var(--border);
        border-radius: var(--radius-sm); padding: 12px 14px;
    }
 
    .period-cell .lbl {
        font-size: 10.5px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .07em; color: var(--muted); margin-bottom: 4px;
    }
 
    .period-cell .val {
        font-family: var(--font-head);
        font-size: 20px; font-weight: 700; color: var(--ink);
    }
 
    /* ── Warning box ─────────────────────────────────────────── */
    .cp-alert {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 11px 13px; background: #fffbeb;
        border: 1.5px solid #fde68a; border-radius: var(--radius-sm);
        font-size: 13px; color: #92400e; line-height: 1.5; margin-bottom: 18px;
    }
 
    .cp-alert .material-icons { font-size: 17px; color: var(--warning); flex-shrink: 0; margin-top: 1px; }
 
    /* ── Form fields ─────────────────────────────────────────── */
    .cp-field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
 
    .cp-field label {
        font-size: 12.5px; font-weight: 500; color: #374151;
    }
 
    .cp-field select, .cp-field input {
        height: 40px; padding: 0 12px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body);
        font-size: 14px; color: var(--ink); outline: none; width: 100%;
        appearance: none; -webkit-appearance: none;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }
 
    .cp-field select:focus, .cp-field input:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,219,.1);
    }
 
    .select-wrap { position: relative; }
 
    .select-wrap::after {
        content: 'expand_more'; font-family: 'Material Icons'; font-size: 17px;
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        color: var(--muted); pointer-events: none;
    }
 
    .select-wrap select { padding-right: 30px; }
 
    .field-hint { font-size: 11.5px; color: var(--muted); margin-top: 2px; line-height: 1.4; }
 
    /* ── Email toggle chip ───────────────────────────────────── */
    .email-chip {
        display: flex; align-items: center; gap: 9px;
        padding: 10px 14px; border: 1.5px solid var(--border);
        border-radius: var(--radius-sm); cursor: pointer;
        background: #fafafa; transition: all .2s;
        font-size: 13.5px; font-weight: 500; color: var(--muted);
        width: 100%; margin-bottom: 8px;
    }
 
    .email-chip input[type="checkbox"] { display: none; }
 
    .email-chip .ec-check {
        width: 18px; height: 18px; border-radius: 5px;
        border: 2px solid var(--border); background: var(--surface);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; transition: all .2s;
    }
 
    .email-chip .ec-check .material-icons { font-size: 13px; color: #fff; opacity: 0; }
 
    .email-chip.on {
        border-color: var(--accent); background: var(--accent-lt); color: var(--accent);
    }
 
    .email-chip.on .ec-check {
        border-color: var(--accent); background: var(--accent);
    }
 
    .email-chip.on .ec-check .material-icons { opacity: 1; }
 
    .email-chip .material-icons.mail-icon { font-size: 17px; flex-shrink: 0; }
 
    .pwd-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; background: #f3f4f8;
        border-radius: 100px; font-size: 11.5px; color: var(--muted);
    }
 
    .pwd-badge .material-icons { font-size: 13px; }
 
    /* ── Buttons ─────────────────────────────────────────────── */
    .btn {
        height: 42px; padding: 0 20px; border: none;
        border-radius: var(--radius-sm); font-family: var(--font-body);
        font-size: 14px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center; gap: 7px;
        transition: transform .2s, box-shadow .2s, filter .2s;
        width: 100%; letter-spacing: .01em;
    }
 
    .btn .material-icons { font-size: 17px; }
    .btn:hover:not(:disabled) { transform: translateY(-1px); }
    .btn:active:not(:disabled) { transform: translateY(0); }
    .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }
 
    .btn-danger-action {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: #fff; box-shadow: 0 4px 14px rgba(220,38,38,.25);
    }
 
    .btn-danger-action:hover:not(:disabled) { box-shadow: 0 7px 20px rgba(220,38,38,.38); filter: brightness(1.05); }
 
    .btn-generate {
        background: linear-gradient(135deg, #1a56db, #4f46e5);
        color: #fff; box-shadow: 0 4px 14px rgba(26,86,219,.25);
    }
 
    .btn-generate:hover:not(:disabled) { box-shadow: 0 7px 20px rgba(26,86,219,.35); filter: brightness(1.05); }
 
    /* ── Inline generation progress ─────────────────────────── */
    #progressSection { display: none; margin-top: 18px; }
 
    .progress-inset {
        background: var(--bg); border: 1px solid var(--border);
        border-radius: var(--radius-sm); padding: 16px;
    }
 
    .progress-inset-title {
        display: flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 600; color: var(--ink); margin-bottom: 10px;
    }
 
    .progress-inset-title .material-icons {
        font-size: 16px; color: var(--accent);
        animation: spin 1s linear infinite;
    }
 
    @keyframes spin { to { transform: rotate(360deg); } }
 
    .progress-track {
        height: 7px; background: #e5e7eb;
        border-radius: 100px; overflow: hidden; margin-bottom: 8px;
    }
 
    #progressBar {
        height: 100%;
        background: linear-gradient(90deg, #1a56db, #6366f1);
        border-radius: 100px; width: 0%; transition: width .4s ease;
    }
 
    #progressMessage, #progressStats {
        font-size: 12.5px; color: var(--muted); margin: 0;
    }
 
    #downloadLinksSection { display: none; margin-top: 14px; }
 
    #downloadLinksSection h6 {
        font-size: 12.5px; font-weight: 600; color: var(--ink); margin-bottom: 8px;
    }
 
    #downloadLinks a {
        display: flex; align-items: center; gap: 7px;
        padding: 9px 12px; margin-bottom: 4px;
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        color: var(--accent); font-size: 13px; text-decoration: none;
        background: var(--surface); transition: background .15s;
    }
 
    #downloadLinks a:hover { background: var(--accent-lt); }
    #downloadLinks a .material-icons { font-size: 15px; }
 
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
 
    /* ── Sending/progress modal ──────────────────────────────── */
    .pm-backdrop {
        position: fixed; inset: 0; background: rgba(0,0,0,.45);
        backdrop-filter: blur(4px); z-index: 9000;
        display: none; align-items: center; justify-content: center;
    }
 
    .pm-backdrop.open { display: flex; }
 
    .pm-card {
        background: var(--surface); border-radius: 18px;
        padding: 28px 32px; width: 100%; max-width: 340px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2); text-align: center;
        animation: fadeUp .3s cubic-bezier(.22,.61,.36,1) both;
    }
 
    .pm-spin-icon {
        width: 48px; height: 48px; border-radius: 13px;
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px;
    }
 
    .pm-spin-icon .material-icons {
        font-size: 24px; color: var(--accent);
        animation: spin 1.2s linear infinite;
    }
 
    .pm-card h3 {
        font-family: var(--font-head); font-size: 16px; font-weight: 700;
        color: var(--ink); margin: 0 0 4px;
    }
 
    .pm-card p { font-size: 13px; color: var(--muted); margin: 0 0 14px; }
 
    .pm-track { height: 7px; background: #e5e7eb; border-radius: 100px; overflow: hidden; }
 
    #progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #1a56db, #6366f1);
        border-radius: 100px; width: 0%; transition: width .4s ease;
    }
 
    @media (max-width: 640px) {
        .close-period-page { padding: 18px 14px; }
        .period-grid { grid-template-columns: 1fr; }
    }
</style>
 
<div class="close-period-page">
 
    <div class="page-heading">
        <h1>Close Period</h1>
        <p>Finalise the current payroll period and distribute agent payslips.</p>
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
    <div id="successMessage" style="display:none;"></div>
 
    <div class="cp-layout">
 
        {{-- ── Left: Close period ──────────────────────────── --}}
        <div class="cp-card">
            <div class="cp-card-head">
                <div class="cp-icon red"><span class="material-icons">event_busy</span></div>
                <div>
                    <p class="cp-card-title">Current Period</p>
                    <p class="cp-card-subtitle">Close when payroll is finalised</p>
                </div>
            </div>
 
            <div class="cp-card-body">
                <div class="period-grid">
                    <div class="period-cell">
                        <div class="lbl">Month</div>
                        <div class="val">{{ $month }}</div>
                    </div>
                    <div class="period-cell">
                        <div class="lbl">Year</div>
                        <div class="val">{{ $year }}</div>
                    </div>
                </div>
 
                {{-- Hidden fields for JS compatibility --}}
                <input type="hidden" id="currentMonth" value="{{ $month }}">
                <input type="hidden" id="currentYear"  value="{{ $year }}">
 
                <div class="cp-alert">
                    <span class="material-icons">warning_amber</span>
                    <span>Closing a period is <strong>irreversible</strong>. Ensure all data is reviewed and approved before proceeding.</span>
                </div>
 
                <button id="load" type="button" class="btn btn-danger-action">
                    <span class="material-icons">lock</span>
                    Close Period
                </button>
            </div>
        </div>
 
        {{-- ── Right: Bulk payslip generation ──────────────── --}}
        <div class="cp-card">
            <div class="cp-card-head">
                <div class="cp-icon green"><span class="material-icons">mail</span></div>
                <div>
                    <p class="cp-card-title">Bulk Payslip Generation</p>
                    <p class="cp-card-subtitle">Generate &amp; optionally email payslips to agents</p>
                </div>
            </div>
 
            <div class="cp-card-body">
                <form id="bulkPayslipForm">
                    @csrf
 
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
 
                        <div class="cp-field">
                            <label>Period <span style="color:var(--danger)">*</span></label>
                            <div class="select-wrap">
                                <select id="period" name="period" required>
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
 
                        <div class="cp-field">
                            <label>Download Method <span style="color:var(--danger)">*</span></label>
                            <div class="select-wrap">
                                <select id="download_method" name="download_method" required>
                                    <option value="nogeneration">No Download</option>
                                    <option value="zip">ZIP — file</option>
                                    
                                </select>
                            </div>
                            <span class="field-hint">ZIP: all payslips in one archive · Individual: separate PDFs per agent</span>
                        </div>
 
                    </div>
 
                    {{-- Email chip --}}
                    <div class="cp-field">
                        <label>Email Delivery</label>
                        <label class="email-chip" id="emailChip">
                            <input type="checkbox" id="send_email" name="send_email" value="1">
                            <div class="ec-check">
                                <span class="material-icons">check</span>
                            </div>
                            <span class="material-icons mail-icon">email</span>
                            Send payslips via email
                        </label>
                        <span class="pwd-badge">
                            <span class="material-icons">lock</span>
                            Payslip password: Employee's KRA PIN
                        </span>
                    </div>
 
                    <button type="submit" class="btn btn-generate" id="generateBtn">
                        <span class="material-icons">download</span>
                        Generate &amp; Download Payslips
                    </button>
 
                </form>
 
                {{-- Inline progress — shown by JS --}}
                <div id="progressSection">
                    <div class="progress-inset">
                        <div class="progress-inset-title">
                            <span class="material-icons">sync</span>
                            Generation Progress
                        </div>
                        <div class="progress-track">
                            <div id="progressBar"></div>
                        </div>
                        <p id="progressMessage">Starting…</p>
                        <p id="progressStats"></p>
                    </div>
 
                    <div id="downloadLinksSection">
                        <h6>Download Files</h6>
                        <div id="downloadLinks"></div>
                    </div>
                </div>
 
            </div>
        </div>
 
    </div>
</div>
 
{{-- ── Sending progress modal (#progress-modal — ID preserved) ── --}}
<div class="pm-backdrop" id="progress-modal">
    <div class="pm-card">
        <div class="pm-spin-icon">
            <span class="material-icons">sync</span>
        </div>
        <h3>Sending</h3>
        <p id="progress-message">Preparing payslips…</p>
        <div class="pm-track">
            <div id="progress-bar"></div>
        </div>
    </div>
</div>


    
    <!-- Proper order of script loading -->
    <!-- 1. First jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- 3. SweetAlert Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- 4. Your custom scripts -->
    <script nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function () {
 
    /* ── Email chip toggle ────────────────────────────────── */
    const emailChip = document.getElementById('emailChip');
    const emailCb   = document.getElementById('send_email');
 
    emailChip.addEventListener('click', function (e) {
        // Prevent double-firing from label's default checkbox behaviour
        e.preventDefault();
        emailCb.checked = !emailCb.checked;
        emailChip.classList.toggle('on', emailCb.checked);
    });
 
    /* ── Progress modal global helpers (existing JS uses these) */
    window.openProgressModal = function (msg) {
        document.getElementById('progress-message').textContent = msg || 'Processing…';
        document.getElementById('progress-bar').style.width = '5%';
        document.getElementById('progress-modal').classList.add('open');
    };
 
    window.updateProgressBar = function (pct) {
        document.getElementById('progress-bar').style.width = pct + '%';
    };
 
    window.closeProgressModal = function () {
        document.getElementById('progress-modal').classList.remove('open');
        document.getElementById('progress-bar').style.width = '0%';
    };
 
    /* ── Legacy show/hide for #progress-modal (old inline style) */
    // Some old JS does: document.getElementById('progress-modal').style.display = 'flex'
    // We intercept with a MutationObserver so both approaches work.
    const pmEl = document.getElementById('progress-modal');
    const observer = new MutationObserver(function () {
        if (pmEl.style.display === 'flex' || pmEl.style.display === 'block') {
            pmEl.style.display = '';
            pmEl.classList.add('open');
        } else if (pmEl.style.display === 'none') {
            pmEl.style.display = '';
            pmEl.classList.remove('open');
        }
    });
    observer.observe(pmEl, { attributes: true, attributeFilter: ['style'] });
 
    /* ── Toast ────────────────────────────────────────────── */
    window.showMessage = function (msg, isError) {
        showToast(isError ? 'danger' : 'success', isError ? 'Error' : 'Success', msg);
    };
 
    function showToast (type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber' };
        const t = document.createElement('div');
        t.className = 'toast-msg ' + type;
        t.innerHTML = '<span class="material-icons">' + (icons[type] || 'info') + '</span>'
                    + '<div><strong>' + title + '</strong> ' + message + '</div>';
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 6000);
    }
 
});
        function updateProgress(percent, message, stats) {
    progressBar.style.width = percent + '%';
    progressBar.textContent = percent + '%';
    progressMessage.textContent = message;
    progressStats.textContent = stats;
}
        document.addEventListener('DOMContentLoaded', function() {
            $('#period')
        .html('<option value="">Loading...</option>');
    
    $.ajax({
        url: '{{ route("summary.data") }}',
        type: 'GET',
        dataType: 'json',
        cache: true,
        timeout: 30000,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.error) {
                console.error("Error: " + data.error);
                
                // Handle session expiration
                if (data.error === 'Session expired' || data.error === 'Unauthorized access') {
                    showMessage('Your session has expired. Please login again.', true);
                    window.location.href = '{{ route("login") }}';
                    return;
                }
                
                showMessage('Error loading data: ' + data.error, true);
            } else if (data.success) {
                // Populate period dropdowns
                const periodHtml = '<option value="">Select Period</option>' + 
                    data.periodOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#period').html(periodHtml);
               
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
            
            if (xhr.status === 403) {
                showMessage('Security token expired. Please refresh the page.', true);
                location.reload();
            } else if (xhr.status === 401) {
                showMessage('Your session has expired. Please login again.', true);
                window.location.href = '{{ route("login") }}';
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
    // Function to format month number to month name
    function getMonthName(monthNumber) {
        const monthNames = ["January", "February", "March", "April", "May", "June",
                            "July", "August", "September", "October", "November", "December"];
        return monthNames[monthNumber - 1];
    }

    // Add click event listener to the #load button
    document.getElementById('load').addEventListener('click', function(event) {
    event.preventDefault();
    
    var selectedYear = $('#currentYear').val();
    var selectedMonthName = $('#currentMonth').val();
    const submitBtn = this; // Store reference to the button
    const originalText = submitBtn.innerHTML; // Store original HTML

    // Use SweetAlert for confirmation 
    Swal.fire({
        title: 'Are you sure?',
        text: `Are you sure you want to close the period ${selectedMonthName} ${selectedYear}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, close period!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show spinner on the button immediately
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Closing...';
            submitBtn.disabled = true;
            
            // Show progress modal using SweetAlert
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 5;
                if (progress <= 90) {
                    updateProgress(progress, 'Processing period closing...');
                }
            }, 200);

            // AJAX request to Laravel backend
            fetch('{{ route("period.close") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: new URLSearchParams({
                    'month': selectedMonthName,
                    'year': selectedYear
                })
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(progressInterval);
                
                // Reset button text BEFORE showing success message
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (data.status === 'success') {
                    updateProgress(100, 'Period closed successfully!');
                    
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Period Closed',
                            text: 'Period closed successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }, 500);
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                clearInterval(progressInterval);
                
                // Reset button text on error too
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred while closing the period.',
                    footer: `Error: ${error.message}`
                });
            });
        } else {
            // Canceled
            Swal.fire({
                icon: 'info',
                title: 'Cancelled',
                text: 'Closing period was cancelled.'
            });
        }
    });
});
});
document.addEventListener('DOMContentLoaded', function() {
   const form = document.getElementById('bulkPayslipForm');
const progressSection = document.getElementById('progressSection');
const progressBar = document.getElementById('progressBar');
const progressMessage = document.getElementById('progressMessage');
const progressStats = document.getElementById('progressStats');
const generateBtn = document.getElementById('generateBtn');
const downloadLinksSection = document.getElementById('downloadLinksSection');
const downloadLinks = document.getElementById('downloadLinks');

let directoryHandle = null; // For File System Access API

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const period = document.getElementById('period').value;
    const downloadMethod = document.getElementById('download_method').value;
    const sendEmail = document.getElementById('send_email').checked;
    
    if (!period) {
        alert('Please select a period');
        return;
    }

    // Confirm if sending emails
    if (sendEmail) {
        const confirmEmail = confirm(
            'You are about to send payslips via email to all employees.\n\n' +
            '• PDFs will be password-protected with KRA PIN\n' +
            '• Only employees with valid email addresses will receive payslips\n\n' +
            'Continue?'
        );
        
        if (!confirmEmail) {
            return;
        }
    }

    // For individual downloads, request directory access first
    if (downloadMethod === 'individual') {
        if (!('showDirectoryPicker' in window)) {
            alert('Your browser does not support folder selection. Please use ZIP download method.');
            return;
        }
        
        try {
            directoryHandle = await window.showDirectoryPicker();
        } catch (err) {
            if (err.name !== 'AbortError') {
                alert('Failed to select directory: ' + err.message);
            }
            return;
        }
    }

    // Show progress section
    progressSection.style.display = 'block';
    downloadLinksSection.style.display = 'none';
    downloadLinks.innerHTML = '';
    generateBtn.disabled = true;
    
    // Reset progress
    const initialMessage = sendEmail ? 
        'Starting payslip generation and email delivery...' : 
        'Starting bulk payslip generation...';
    updateProgress(0, initialMessage, '0/0 employees');

    try {
        // Start generation
        const response = await fetch('{{ route("bulk.payslips.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                period: period,
                download_method: downloadMethod,
                send_email: sendEmail
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            // Start polling for progress
            pollProgress(data.job_id, downloadMethod, sendEmail);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        alert('Error: ' + error.message);
        generateBtn.disabled = false;
        progressSection.style.display = 'none';
    }
});

function pollProgress(jobId, downloadMethod, sendEmail) {
    const progressInterval = setInterval(async function() {
        try {
            const response = await fetch(`{{ route('bulk.payslips.progress', '') }}/${jobId}`);
            const data = await response.json();

            if (data.status === 'success') {
                const progress = data.progress;

                // Build stats message
                let stats = `${progress.success} success, ${progress.failed} failed of ${progress.total} total`;
                if (sendEmail) {
                    stats += ` | Emailed: ${progress.emailed}, Email failed: ${progress.email_failed}`;
                }

                updateProgress(
                    progress.progress,
                    progress.message,
                    stats
                );

                if (progress.progress >= 100) {
                    clearInterval(progressInterval);
                    generateBtn.disabled = false;

                    // Show completion message
                    let completionMsg = '';
                    if (progress.failed > 0) {
                        completionMsg += `<br><span class="text-warning">⚠️ Completed with ${progress.failed} generation errors.</span>`;
                    }
                    if (sendEmail && progress.email_failed > 0) {
                        completionMsg += `<br><span class="text-warning">⚠️ ${progress.email_failed} emails failed to send.</span>`;
                    }
                    if (sendEmail && progress.emailed > 0) {
                        completionMsg += `<br><span class="text-success">✉️ ${progress.emailed} payslips emailed successfully!</span>`;
                    }
                    if (progress.failed === 0 && (!sendEmail || progress.email_failed === 0)) {
                        completionMsg += '<br><span class="text-success">✅ All payslips processed successfully!</span>';
                    }
                    
                    progressMessage.innerHTML += completionMsg;

                    // Trigger download based on method (only if not email-only)
                    if (!sendEmail || downloadMethod === 'zip' || downloadMethod === 'individual') {
                        if (downloadMethod === 'zip') {
                            downloadZipFile(jobId);
                        } else {
                            downloadIndividualFiles(jobId);
                        }
                    }
                }
            } else {
                clearInterval(progressInterval);
                alert('Error getting progress: ' + data.message);
                generateBtn.disabled = false;
            }
        } catch (error) {
            clearInterval(progressInterval);
            console.error('Progress polling error:', error);
            alert('Error polling progress: ' + error.message);
            generateBtn.disabled = false;
        }
    }, 2000);
}



// Download as ZIP file
async function downloadZipFile(jobId) {
    try {
        progressMessage.innerHTML += '<br>Preparing ZIP file...';
        
        const response = await fetch(`{{ route('bulk.payslips.download.zip', '') }}/${jobId}`);
        
        if (!response.ok) {
            throw new Error('Failed to download ZIP file');
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Payslips_${document.getElementById('period').value}.zip`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        progressMessage.innerHTML += '<br><span class="text-success">ZIP file downloaded successfully!</span>';
    } catch (error) {
        alert('Error downloading ZIP: ' + error.message);
    }
}

// Download individual files using File System Access API
async function downloadIndividualFiles(jobId) {
    try {
        progressMessage.innerHTML += '<br>Downloading files to selected folder...';
        
        // Get list of files
        const response = await fetch(`{{ route('bulk.payslips.list', '') }}/${jobId}`);
        const data = await response.json();

        if (data.status !== 'success') {
            throw new Error('Failed to get file list');
        }

        const files = data.files;
        let downloaded = 0;

        for (const file of files) {
            try {
                // Download file content
               
                const blob = await fileResponse.blob();

                // Create file in selected directory
                const fileHandle = await directoryHandle.getFileHandle(file.filename, { create: true });
                const writable = await fileHandle.createWritable();
                await writable.write(blob);
                await writable.close();

                downloaded++;
                updateProgress(
                    Math.round((downloaded / files.length) * 100),
                    `Saving files... ${downloaded}/${files.length}`,
                    `Downloaded to your selected folder`
                );
            } catch (err) {
                console.error(`Failed to save ${file.filename}:`, err);
            }
        }

        progressMessage.innerHTML += `<br><span class="text-success">${downloaded} files saved to your selected folder!</span>`;
    } catch (error) {
        alert('Error downloading files: ' + error.message);
    }
}

// Fallback: Show download links if File System Access API fails
async function showDownloadLinks(jobId) {
    try {
        const response = await fetch(`{{ route('bulk.payslips.list', '') }}/${jobId}`);
        const data = await response.json();

        if (data.status === 'success') {
            downloadLinksSection.style.display = 'block';
            
            data.files.forEach(file => {
                const link = document.createElement('a');
               
                link.className = 'list-group-item list-group-item-action';
                link.download = file.filename;
                link.textContent = file.filename;
                link.target = '_blank';
                downloadLinks.appendChild(link);
            });
        }
    } catch (error) {
        console.error('Error loading download links:', error);
    }
}
});
        </script>
</x-custom-admin-layout>