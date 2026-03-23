<x-custom-admin-layout>
 
<style>
    /* ── Page ────────────────────────────────────────────────── */
    .approve-page {
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
 
    /* ── Section card ────────────────────────────────────────── */
    .a-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 20px;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
    }
 
    .a-card:nth-child(2) { animation-delay: .06s; }
    .a-card:nth-child(3) { animation-delay: .12s; }
 
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
 
    .a-card-head {
        display: flex; align-items: center; gap: 10px;
        padding: 14px 22px; border-bottom: 1px solid var(--border);
    }
 
    .a-card-icon {
        width: 32px; height: 32px; border-radius: 9px;
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
 
    .a-card-icon .material-icons { font-size: 16px; color: var(--accent); }
    .a-card-icon.green  { background: var(--success-lt); }
    .a-card-icon.green  .material-icons { color: var(--success); }
    .a-card-icon.red    { background: var(--danger-lt); }
    .a-card-icon.red    .material-icons { color: var(--danger); }
 
    .a-card-title { font-family: var(--font-head); font-size: 14px; font-weight: 700; color: var(--ink); }
 
    .a-card-body { padding: 20px 22px; }
 
    /* ── Filter row ──────────────────────────────────────────── */
    .filter-row {
        display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap;
    }
 
    /* ── Field ────────────────────────────────────────────────── */
    .field { display: flex; flex-direction: column; gap: 4px; }
 
    .field label { font-size: 12px; font-weight: 500; color: #374151; }
 
    .field select {
        height: 40px; padding: 0 32px 0 12px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body);
        font-size: 13.5px; color: var(--ink); outline: none; width: 100%;
        appearance: none; -webkit-appearance: none;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }
 
    .field select:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3px rgba(26,86,219,.1);
    }
 
    .select-wrap { position: relative; }
    .select-wrap::after {
        content: 'expand_more'; font-family: 'Material Icons'; font-size: 17px;
        position: absolute; right: 9px; top: 50%; transform: translateY(-50%);
        color: var(--muted); pointer-events: none;
    }
 
    /* ── Period display ──────────────────────────────────────── */
    .period-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px;
    }
 
    .period-cell {
        background: var(--bg); border: 1px solid var(--border);
        border-radius: var(--radius-sm); padding: 10px 14px;
    }
 
    .period-cell .lbl {
        font-size: 10.5px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .07em; color: var(--muted); margin-bottom: 3px;
    }
 
    .period-cell .val {
        font-family: var(--font-head);
        font-size: 18px; font-weight: 700; color: var(--ink);
    }
 
    /* ── Approval toggle ─────────────────────────────────────── */
    .toggle-field { display: flex; flex-direction: column; gap: 4px; }
    .toggle-field label.lbl { font-size: 12px; font-weight: 500; color: #374151; }
 
    .approval-toggle {
        display: flex; align-items: center; gap: 10px; height: 40px;
    }
 
    .toggle-track {
        width: 52px; height: 26px; border-radius: 100px;
        background: var(--danger); cursor: pointer;
        position: relative; transition: background .3s; flex-shrink: 0;
        border: none; outline: none; padding: 0;
    }
 
    .toggle-track::before {
        content: ''; width: 20px; height: 20px; border-radius: 50%;
        background: #fff; position: absolute; top: 3px; left: 3px;
        transition: transform .3s; box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }
 
    .toggle-track.on { background: var(--success); }
    .toggle-track.on::before { transform: translateX(26px); }
 
    #approvalToggle { display: none; }
 
    .toggle-text {
        font-size: 13.5px; font-weight: 600;
        color: var(--danger); transition: color .3s;
    }
 
    .toggle-text.on { color: var(--success); }
 
    /* ── Feedback textarea ───────────────────────────────────── */
    .feedback-block {
        margin-top: 16px; display: none;
    }
 
    .feedback-block label { font-size: 12.5px; font-weight: 500; color: #374151; display: block; margin-bottom: 5px; }
 
    .feedback-block textarea {
        width: 100%; padding: 10px 12px; min-height: 80px; resize: vertical;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: var(--surface); font-family: var(--font-body);
        font-size: 13.5px; color: var(--ink); outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
 
    .feedback-block textarea:focus {
        border-color: var(--danger); box-shadow: 0 0 0 3px rgba(220,38,38,.1);
    }
 
    /* ── Buttons ─────────────────────────────────────────────── */
    .btn {
        height: 40px; padding: 0 18px; border: none; border-radius: var(--radius-sm);
        font-family: var(--font-body); font-size: 13.5px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: transform .2s, box-shadow .2s, filter .2s; letter-spacing: .01em;
        white-space: nowrap;
    }
 
    .btn .material-icons { font-size: 16px; }
    .btn:hover:not(:disabled) { transform: translateY(-1px); }
    .btn:active:not(:disabled) { transform: translateY(0); }
    .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }
 
    .btn-review {
        background: linear-gradient(135deg, #1a56db, #4f46e5); color: #fff;
        box-shadow: 0 3px 10px rgba(26,86,219,.22);
    }
 
    .btn-review:hover:not(:disabled) { box-shadow: 0 6px 16px rgba(26,86,219,.32); filter: brightness(1.05); }
 
    .btn-approve {
        background: linear-gradient(135deg, #059669, #10b981); color: #fff;
        box-shadow: 0 3px 10px rgba(5,150,105,.22);
    }
 
    .btn-approve:hover:not(:disabled) { box-shadow: 0 6px 16px rgba(5,150,105,.32); filter: brightness(1.05); }
 
    .btn-reject {
        background: linear-gradient(135deg, #dc2626, #ef4444); color: #fff;
        box-shadow: 0 3px 10px rgba(220,38,38,.22);
        display: none;
    }
 
    .btn-reject:hover:not(:disabled) { box-shadow: 0 6px 16px rgba(220,38,38,.32); filter: brightness(1.05); }
 
    /* ── Warning callout ─────────────────────────────────────── */
    .callout {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 11px 14px; border-radius: var(--radius-sm);
        font-size: 13px; margin-bottom: 16px; line-height: 1.5;
    }
 
    .callout .material-icons { font-size: 17px; flex-shrink: 0; margin-top: 1px; }
    .callout.warning { background: #fffbeb; border: 1.5px solid #fde68a; color: #92400e; }
    .callout.warning .material-icons { color: var(--warning); }
 
    /* ── PDF Viewer modal ─────────────────────────────────────── */
      .pdf-modal-backdrop {
        position: fixed; inset: 0; background: rgba(0,0,0,.55);
        backdrop-filter: blur(4px); z-index: 8000;
        display: none; align-items: center; justify-content: center; padding: 20px;
    }
 
    .pdf-modal-backdrop.open { display: flex; }
 
    .pdf-modal-card {
        background: var(--surface); border-radius: 20px;
        width: 100%; max-width: 1020px; height: 90vh;
        display: flex; flex-direction: column;
        box-shadow: 0 24px 80px rgba(0,0,0,.25);
        overflow: hidden;
        animation: fadeUp .3s cubic-bezier(.22,.61,.36,1) both;
    }
 
    .pdf-modal-header {
        display: flex; align-items: center; gap: 10px;
        padding: 16px 20px; border-bottom: 1px solid var(--border); flex-shrink: 0;
    }
 
    .pdf-modal-icon {
        width: 34px; height: 34px; border-radius: 9px;
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center;
    }
 
    .pdf-modal-icon .material-icons { font-size: 17px; color: var(--accent); }
    .pdf-modal-title { font-family: var(--font-head); font-size: 15px; font-weight: 700; color: var(--ink); flex: 1; }
 
    .pdf-modal-actions { display: flex; align-items: center; gap: 8px; }
 
    .btn-icon {
        width: 34px; height: 34px; border: 1.5px solid var(--border);
        border-radius: 8px; background: var(--surface); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: var(--muted); transition: all .2s;
    }
 
    .btn-icon:hover { color: var(--ink); border-color: #9ca3af; background: var(--bg); }
    .btn-icon .material-icons { font-size: 17px; }
 
    .pdf-modal-body { flex: 1; overflow: hidden; position: relative; }
    .pdf-modal-body iframe { width: 100%; height: 100%; border: none; display: block; }
 
    .pdf-loading {
        position: absolute; inset: 0; display: flex;
        flex-direction: column; align-items: center; justify-content: center;
        gap: 12px; background: var(--surface); color: var(--muted); font-size: 14px;
    }
 
    .pdf-loading .material-icons {
        font-size: 36px; color: #d1d5db;
        animation: spin 1.5s linear infinite;
    }
 
    @keyframes spin { to { transform: rotate(360deg); } }
 
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
 
    @media (max-width: 768px) {
        .approve-page { padding: 18px 14px; }
        .filter-row { gap: 10px; }
    }
</style>
 
<div class="approve-page">
 
    <div class="page-heading">
        <h1>Payroll Approvals</h1>
        <p>Review and approve earnings and net pay for the current payroll period.</p>
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
 
    {{-- ═══════════════════════════════
         SECTION 1 — APPROVE EARNINGS
    ═══════════════════════════════ --}}
    <div class="a-card">
        <div class="a-card-head">
            <div class="a-card-icon green"><span class="material-icons">payments</span></div>
            <span class="a-card-title">Approve Earnings</span>
        </div>
        <div class="a-card-body">
            <form id="forecastForm">
                <div class="filter-row">
                    <div class="field" style="min-width:180px;">
                        <label>Earnings Item</label>
                        <div class="select-wrap">
                            <select name="pname" id="pname" required autocomplete="off">
                                <option value="">Select Item</option>
                            </select>
                        </div>
                    </div>
                    <div class="field" style="min-width:160px;">
                        <label>Agent (From)</label>
                        <div class="select-wrap">
                            <select name="staffSelect3" id="staffSelect3" autocomplete="off">
                                <option value="">Select Agent</option>
                            </select>
                        </div>
                    </div>
                    <div class="field" style="min-width:160px;">
                        <label>Agent (To)</label>
                        <div class="select-wrap">
                            <select name="staffSelect4" id="staffSelect4" autocomplete="off">
                                <option value="">Select Agent</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-review" id="openitems">
                        <span class="material-icons">table_view</span> Review
                    </button>
                </div>
            </form>
        </div>
    </div>
 
    {{-- ═══════════════════════════════
         SECTION 2 — PERIOD + APPROVE EARNINGS
    ═══════════════════════════════ --}}
    <div class="a-card">
        <div class="a-card-head">
            <div class="a-card-icon green"><span class="material-icons">event_available</span></div>
            <span class="a-card-title">Current Payroll Period</span>
        </div>
        <div class="a-card-body">
            <div class="period-grid" style="max-width:340px;">
                <div class="period-cell">
                    <div class="lbl">Month</div>
                    <div class="val">{{ $month }}</div>
                    <input type="hidden" id="currentMonth" value="{{ $month }}">
                </div>
                <div class="period-cell">
                    <div class="lbl">Year</div>
                    <div class="val">{{ $year }}</div>
                    <input type="hidden" id="currentYear" value="{{ $year }}">
                </div>
            </div>
 
            <div class="callout warning">
                <span class="material-icons">warning_amber</span>
                <span>Approving earnings is <strong>final</strong> for this period. Ensure all data has been reviewed before proceeding.</span>
            </div>
 
            <button id="approve" type="button" class="btn btn-approve">
                <span class="material-icons">done_all</span> Approve Earnings
            </button>
        </div>
    </div>
 
    {{-- ═══════════════════════════════
         SECTION 3 — APPROVE NET PAY
    ═══════════════════════════════ --}}
    <div class="a-card">
        <div class="a-card-head">
            <div class="a-card-icon" id="netpayCardIcon">
                <span class="material-icons">verified</span>
            </div>
            <span class="a-card-title">Approve Net Pay</span>
        </div>
        <div class="a-card-body">
            <form id="netpayForm">
 
                <div class="filter-row" style="margin-bottom:16px;">
                    <div class="field" style="min-width:160px;">
                        <label>Agent (From)</label>
                        <div class="select-wrap">
                            <select name="staffSelect5" id="staffSelect5" autocomplete="off">
                                <option value="">Select Agent</option>
                            </select>
                        </div>
                    </div>
                    <div class="field" style="min-width:160px;">
                        <label>Agent (To)</label>
                        <div class="select-wrap">
                            <select name="staffSelect6" id="staffSelect6" autocomplete="off">
                                <option value="">Select Agent</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-review" id="openitems2">
                        <span class="material-icons">table_view</span> Review
                    </button>
                </div>
 
                {{-- Approval toggle row --}}
                <div class="filter-row">
                    <div class="toggle-field">
                        <span class="lbl" style="font-size:12px;font-weight:500;color:#374151;">Approval Mode</span>
                        <div class="approval-toggle">
                            <input type="checkbox" id="approvalToggle" checked>
                            <button type="button" class="toggle-track on" id="toggleTrack"
                                    onclick="toggleApproval()" aria-label="Toggle approval mode">
                            </button>
                            <span id="toggleText" class="toggle-text on">Approve</span>
                        </div>
                    </div>
 
                    <button type="submit" id="approveBtn" class="btn btn-approve">
                        <span class="material-icons">done_all</span> Approve
                    </button>
 
                    <button type="submit" id="rejectBtn" class="btn btn-reject">
                        <span class="material-icons">cancel</span> Reject
                    </button>
                </div>
 
                {{-- Feedback (shown when rejecting) --}}
                <div class="feedback-block" id="feedbackSection">
                    <label>Reason for Rejection</label>
                    <textarea id="rejection_reason" name="rejection_reason"
                              rows="3" placeholder="Enter reason…"></textarea>
                </div>
 
            </form>
        </div>
    </div>
 
</div>{{-- /approve-page --}}
 
{{-- ── PDF Report Modal ─────────────────────────────────────── --}}
<div class="pdf-modal-backdrop" id="staffreportModal">
    <div class="pdf-modal-card" id="staffreportModalCard">
        <div class="pdf-modal-header">
            <div class="pdf-modal-icon"><span class="material-icons">picture_as_pdf</span></div>
            <span class="pdf-modal-title" id="pdfModalTitle">Report Viewer</span>
            <div class="pdf-modal-actions">
                <button class="btn btn-download" id="pdfDownloadBtn" style="height:34px;padding:0 14px;font-size:13px;display:none;">
                    <span class="material-icons" style="font-size:15px;">download</span> Download
                </button>
                <button class="btn-icon" id="pdfPrintBtn" style="display:none;" title="Print">
                    <span class="material-icons">print</span>
                </button>
                <button class="btn-icon" id="closemodal">
                    <span class="material-icons">close</span>
                </button>
            </div>
        </div>
        <div class="pdf-modal-body" id="staffrpt-pdf-container">
            <div class="pdf-loading" id="pdfLoading">
                <span class="material-icons">sync</span>
                <span>Loading report…</span>
            </div>
        </div>
    </div>
</div>

    
    <!-- Proper order of script loading -->
    <!-- 1. First jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- 3. SweetAlert Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- 4. Your custom scripts -->
    <script>
        function toggleApproval() {
    var track = document.getElementById('toggleTrack');
    var text  = document.getElementById('toggleText');
    var appBtn = document.getElementById('approveBtn');
    var rejBtn = document.getElementById('rejectBtn');
    var feed   = document.getElementById('feedbackSection');
    var icon   = document.querySelector('#netpayCardIcon .material-icons');
    var cb     = document.getElementById('approvalToggle');
 
    var isApprove = track.classList.toggle('on');
    text.classList.toggle('on', isApprove);
    cb.checked = isApprove;
 
    text.textContent      = isApprove ? 'Approve' : 'Reject';
    appBtn.style.display  = isApprove ? '' : 'none';
    rejBtn.style.display  = isApprove ? 'none' : '';
    feed.style.display    = isApprove ? 'none' : 'block';
 
    // Update card icon colour
    if (icon) {
        document.getElementById('netpayCardIcon').className =
            'a-card-icon ' + (isApprove ? 'green' : 'red');
        icon.textContent = isApprove ? 'verified' : 'cancel';
    }
}
 
 /* ── PDF modal helpers ─────────────────────────────────── */
    const pdfModal     = document.getElementById('staffreportModal');
    const pdfContainer = document.getElementById('staffrpt-pdf-container');
    const pdfLoading   = document.getElementById('pdfLoading');
    const pdfDlBtn     = document.getElementById('pdfDownloadBtn');
    const pdfPrintBtn  = document.getElementById('pdfPrintBtn');
    let   currentPdfUrl = null;
 
    window.openPdfModal = function (title) {
        document.getElementById('pdfModalTitle').textContent = title || 'Report Viewer';
        pdfLoading.style.display = 'flex';
        pdfDlBtn.style.display   = 'none';
        pdfPrintBtn.style.display = 'none';
        const old = pdfContainer.querySelector('iframe');
        if (old) old.remove();
        if (currentPdfUrl) { URL.revokeObjectURL(currentPdfUrl); currentPdfUrl = null; }
        pdfModal.classList.add('open');
    };
 
    window.renderPdfInModal = function (base64, filename) {
        const bytes   = Uint8Array.from(atob(base64), c => c.charCodeAt(0));
        const blob    = new Blob([bytes], { type: 'application/pdf' });
        currentPdfUrl = URL.createObjectURL(blob);
        const iframe  = document.createElement('iframe');
        iframe.id     = 'pdfFrame';
        iframe.src    = currentPdfUrl + '#toolbar=0&navpanes=0';
        iframe.style.cssText = 'width:100%;height:100%;border:none;display:block;';
        pdfLoading.style.display = 'none';
        pdfContainer.appendChild(iframe);
        pdfDlBtn.style.display   = '';
        pdfPrintBtn.style.display = '';
        pdfDlBtn.onclick = () => {
            const a = document.createElement('a');
            a.href = currentPdfUrl; a.download = filename || 'report.pdf';
            document.body.appendChild(a); a.click(); document.body.removeChild(a);
        };
        pdfPrintBtn.onclick = () => {
            const f = document.getElementById('pdfFrame');
            if (f) { f.contentWindow.focus(); f.contentWindow.print(); }
        };
    };
 
    // Close
    document.getElementById('closemodal').addEventListener('click', closePdfModal);
    pdfModal.addEventListener('click', e => { if (e.target === pdfModal) closePdfModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closePdfModal(); });
 
    function closePdfModal () {
        pdfModal.classList.remove('open');
        if (currentPdfUrl) { URL.revokeObjectURL(currentPdfUrl); currentPdfUrl = null; }
    }
 
/* ── Toast ───────────────────────────────────────────────── */
function showToast(type, title, message) {
    var icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber' };
    var t = document.createElement('div');
    t.className = 'toast-msg ' + type;
    t.innerHTML = '<span class="material-icons">' + (icons[type]||'info') + '</span>'
                + '<div><strong>' + title + '</strong> ' + message + '</div>';
    document.getElementById('toastWrap').appendChild(t);
    var dismiss = function() { t.classList.add('leaving'); setTimeout(function() { t.remove(); }, 300); };
    t.addEventListener('click', dismiss);
    setTimeout(dismiss, 5000);
}
 
window.showMessage = function(msg, type) {
    showToast(type || 'info', type === 'success' ? 'Success' : 'Notice', msg);
};

        $(document).ready(function() {
            $('#periodoveral, #pname, #staffSelect3, #staffSelect4, #staffSelect5, #staffSelect6, #periodoveral2, #periodoveral3, #statutory')
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
                      showToast('danger', 'Error', 'Your session has expired. Please login again.');
                    window.location.href = '{{ route("login") }}';
                    return;
                }
                
                  showToast('danger', 'Error loading data', + data.error);
            } else if (data.success) {
                // Populate period dropdowns
                const periodHtml = '<option value="">Select Period</option>' + 
                    data.periodOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#periodoveral, #periodoveral2, #periodoveral3').html(periodHtml);
                
                // Populate pname dropdown
                const pnameHtml = '<option value="">Select Item</option>' + 
                    data.EarningsOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#pname').html(pnameHtml);
                
                // Populate staff dropdowns
                const staffHtml = '<option value="">Select Agent</option>' + 
                    data.snameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#staffSelect3, #staffSelect4, #staffSelect5, #staffSelect6').html(staffHtml);
                
                // Populate statutory dropdown
                const statutoryHtml = '<option value="">Select Item</option>' + 
                    data.statutoryOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#statutory').html(statutoryHtml);
                
                // Initialize Select2 for pname
                if (!$('#pname').hasClass("select2-hidden-accessible")) {
                    $('#pname').select2({
                        placeholder: "Select Item",
                        allowClear: true
                    });
                }
                
                // Initialize Select2 for staff selects
                ['#staffSelect3', '#staffSelect4', '#staffSelect5', '#staffSelect6'].forEach(function(selector) {
                    if (!$(selector).hasClass("select2-hidden-accessible")) {
                        $(selector).select2({
                            placeholder: selector.includes('3') || selector.includes('6') ? "Select Agent" : "Search",
                            allowClear: true
                        });
                    }
                });
                
                // Auto-select first and last staff for range selections
                var options3 = $('#staffSelect3 option:not([value=""])');
                if (options3.length > 0) {
                    $('#staffSelect3').val(options3.first().val()).trigger('change');
                    $('#staffSelect4').val(options3.last().val()).trigger('change');
                }
                
                var options5 = $('#staffSelect5 option:not([value=""])');
                if (options5.length > 0) {
                    $('#staffSelect5').val(options5.first().val()).trigger('change');
                    $('#staffSelect6').val(options5.last().val()).trigger('change');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
            
            if (xhr.status === 403) {
                  showToast('danger', 'Error','Security token expired. Please refresh the page.');
                location.reload();
            } else if (xhr.status === 401) {
                  showToast('danger', 'Error','Your session has expired. Please login again.');
                window.location.href = '{{ route("login") }}';
            } else {
                  showToast('danger', 'Error', 'Failed to load data. Please refresh the page.', true);
            }
        }
    });
$(document).on('click', '#approve', function(e) {
    e.preventDefault();
    
    var month = $('#currentMonth').val();
    var year = $('#currentYear').val();
    
    if (!month || !year) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Month and year are required'
        });
        return;
    }
    
    // Confirmation dialog
    Swal.fire({
        title: 'Approve Payroll?',
        html: `Are you sure you want to approve the payroll for <strong>${month} ${year}</strong>?<br><br>This action cannot be undone.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4CAF50',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Processing...',
                html: 'Approving payroll and sending notifications',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit approval
            $.ajax({
                url: '{{ route("payroll.approve") }}',
                method: 'POST',
                data: {
                    month: month,
                    year: year,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: response.message,
                        confirmButtonColor: '#4CAF50'
                    }).then(() => {
                        // Optionally reload the page or update UI
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var errorMessage = 'Failed to approve payroll';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    });
});

$('#approveBtn').on('click', function(e) {
        e.preventDefault();
        
        var month = $('#currentMonth').val();
        var year = $('#currentYear').val();
        
        if (!month || !year) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Month and year are required'
            });
            return;
        }
        
        // Confirmation dialog
        Swal.fire({
            title: 'Approver Netpay?',
            html: `Are you sure you want to approve the netpay for <strong>${month} ${year}</strong> for Payment?<br>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e67e22',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Yes, Approve',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    html: 'Approving...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit notification
                $.ajax({
                    url: '{{ route("netpay.approve") }}',
                    method: 'POST',
                    data: {
                        month: month,
                        year: year,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Notification Sent!',
                            html: response.message + '<br><br>Approved ',
                            confirmButtonColor: '#4CAF50'
                        });
                    },
                    error: function(xhr) {
                        var errorMessage = 'Failed to send notification';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });

    $('#rejectBtn').on('click', function(e) {
        e.preventDefault();
        
        var month = $('#currentMonth').val();
        var year = $('#currentYear').val();
        var rejection_reason = $('#rejection_reason').val();

        
        
        if (!rejection_reason) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Feedback is required'
            });
            return;
        }
        
        // Confirmation dialog
        Swal.fire({
            title: 'Reject Netpay?',
            html: `Are you sure you want to Reject the netpay for <strong>${month} ${year}</strong><br>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e67e22',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Yes, Reject',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    html: 'Rejecting...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit notification
                $.ajax({
                    url: '{{ route("netpay.reject") }}',
                    method: 'POST',
                    data: {
                        month: month,
                        year: year,
                        rejection_reason: rejection_reason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Notification Sent!',
                            html: response.message + '<br><br>Rejected ',
                            confirmButtonColor: '#4CAF50'
                        });
                    },
                    error: function(xhr) {
                        var errorMessage = 'Failed to send notification';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });

$(document).on('click', '#openitems2', function (e) {
    e.preventDefault();
    var month = $('#currentMonth').val();
    var year = $('#currentYear').val();
    var pname = 'NET PAY';
    var staff3 = $('#staffSelect5').val();
    var staff4 = $('#staffSelect6').val();
    var actionTaken = false;

    if (!pname) {
          showToast('danger', 'Error', 'Please select a Payroll item');
        return;
    }

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
        url: '{{ route("reports.netpay") }}',
        method: 'POST',
        dataType: 'json',
        data: {
            month: month,
            year: year,
            pname: pname,
            staff3: staff3,
            staff4: staff4,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);
                
                var period = `${month}_${year}`;

                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper" style="height: 100%; display: flex; flex-direction: column;">
                        <div class="pdf-actions d-flex gap-2" 
     style="padding: 10px; background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
     
    <button id="downloadPdfBtn" class="btn btn-enhanced btn-cancel btn-sm">
        <i class="fas fa-file-pdf"></i> Download
    </button>

    <button id="printPdfBtn" class="btn btn-enhanced btn-draft btn-sm">
        <i class="fas fa-print"></i> Print
    </button>

    <button id="Exportexcell" class="btn btn-enhanced btn-finalize btn-sm">
        <i class="fas fa-file-excel"></i> Download
    </button>
</div>

                        <iframe 
                            id="staffrptPdfFrame" 
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=1&view=FitH" 
                            style="flex: 1; width: 100%; border: none; display: block;"
                        ></iframe>
                    </div>`;

                $('#staffrpt-pdf-container').html(pdfViewerHTML);

                // PRINT button handler
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staff3, 'PRINT', `${pname}_Listing_${period}`);
                    }
                });

                // DOWNLOAD button handler
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `${pname}_Listing_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staff3, 'DOWNLOAD', `${pname}_Listing_${period}`);
                    }
                });
                $('#Exportexcell').on('click', function () {

    let month = $('#currentMonth').val();
    let year = $('#currentYear').val();
    let pname = 'NET PAY';
    let staff3 = $('#staffSelect5').val();
    let staff4 = $('#staffSelect6').val();
    let url = "{{ route('reports.netpay.excel') }}" +
        "?month=" + encodeURIComponent(month) +
        "&year=" + encodeURIComponent(year) +
        "&pname=" + encodeURIComponent(pname) + 
        "&staff3=" + encodeURIComponent(staff3) +
        "&staff4=" + encodeURIComponent(staff4);

    window.location.href = url; // triggers download
});
            } else {
                $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Failed to generate PDF.</p>');
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX error:", error);
            $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Error fetching report.</p>');
        }
    });
});




$(document).on('click', '#openitems', function (e) {
    e.preventDefault();
    var month = $('#currentMonth').val();
    var year = $('#currentYear').val();
    var pname = $('#pname').val();
    var staff3 = $('#staffSelect3').val();
    var staff4 = $('#staffSelect4').val();
    var actionTaken = false;

    if (!pname) {
          showToast('danger', 'Error', 'Please select a Payroll item');
        return;
    }

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
        url: '{{ route("reports.earnings") }}',
        method: 'POST',
        dataType: 'json',
        data: {
            month: month,
            year: year,
            pname: pname,
            staff3: staff3,
            staff4: staff4,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);
                
                var period = `${month}_${year}`;

                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper" style="height: 100%; display: flex; flex-direction: column;">
                        <div class="pdf-actions d-flex gap-2" 
     style="padding: 10px; background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
     
    <button id="downloadPdfBtn" class="btn btn-enhanced btn-cancel btn-sm">
        <i class="fas fa-file-pdf"></i> Download
    </button>

    <button id="printPdfBtn" class="btn btn-enhanced btn-draft btn-sm">
        <i class="fas fa-print"></i> Print
    </button>

    <button id="Exportexcell2" class="btn btn-enhanced btn-finalize btn-sm">
        <i class="fas fa-file-excel"></i> Download
    </button>
</div>

                        <iframe 
                            id="staffrptPdfFrame" 
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=1&view=FitH" 
                            style="flex: 1; width: 100%; border: none; display: block;"
                        ></iframe>
                    </div>`;

                $('#staffrpt-pdf-container').html(pdfViewerHTML);

                // PRINT button handler
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staff3, 'PRINT', `${pname}_Listing_${period}`);
                    }
                });

                // DOWNLOAD button handler
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `${pname}_Listing_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staff3, 'DOWNLOAD', `${pname}_Listing_${period}`);
                    }
                });

                 $('#Exportexcell2').on('click', function () {

    let month = $('#currentMonth').val();
    let year = $('#currentYear').val();
    var pname = $('#pname').val();
    let staff3 = $('#staffSelect3').val();
    let staff4 = $('#staffSelect4').val();
    let url = "{{ route('reports.earnings.excel') }}" +
        "?month=" + encodeURIComponent(month) +
        "&year=" + encodeURIComponent(year) +
        "&pname=" + encodeURIComponent(pname) +
        "&staff3=" + encodeURIComponent(staff3) +
        "&staff4=" + encodeURIComponent(staff4);

    window.location.href = url; // triggers download
});
            } else {
                $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Failed to generate PDF.</p>');
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX error:", error);
            $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Error fetching report.</p>');
        }
    });
});

document.getElementById('approvalToggle').addEventListener('change', function () {
    var feedbackSection = document.getElementById('feedbackSection');
    var toggleText = document.getElementById('toggleText');

    var approveBtn = document.getElementById('approveBtn');
    var rejectBtn = document.getElementById('rejectBtn');

    if (this.checked) {
        toggleText.innerText = "Approve";
        feedbackSection.style.display = "none";

        approveBtn.style.display = "block";
        rejectBtn.style.display = "none";
    } else {
        toggleText.innerText = "Reject";
        feedbackSection.style.display = "block";

        approveBtn.style.display = "none";
        rejectBtn.style.display = "block";
    }
});



// Optional: Add animation for feedback section
document.getElementById('feedbackSection').style.transition = 'all 0.3s ease';
    
        });

        
function showMessage(message, isError) {
    let messageDiv = $('#messageDiv');
    const backgroundColor = isError ? '#f44336' : '#4CAF50';
    
    if (messageDiv.length === 0) {
        // Create new message div with proper background color
        messageDiv = $(`
            <div id="messageDiv" style="
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 5px;
                color: white;
                z-index: 1051;
                display: block;
                font-weight: bold;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                animation: slideIn 0.5s, fadeOut 0.5s 2.5s;
                background-color: ${backgroundColor};
            ">
                ${message}
            </div>
        `);
        $('body').append(messageDiv);
    } else {
        // Update existing message div
        messageDiv.text(message)
                 .show()
                 .css('background-color', backgroundColor);
    }
    
    // Clear any existing timeout
    if (messageDiv.data('timeout')) {
        clearTimeout(messageDiv.data('timeout'));
    }
    
    // Set new timeout and store reference
    const timeoutId = setTimeout(() => {
        messageDiv.fadeOut();
    }, 3000);
    
    messageDiv.data('timeout', timeoutId);
}
     
        </script>
</x-custom-admin-layout>