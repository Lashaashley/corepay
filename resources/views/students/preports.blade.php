<x-custom-admin-layout>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/pages/preports.css'])
</head>
 
<div class="reports-page">
 
    <div class="page-heading">
        <h1>Payroll Reports</h1>
        
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
 
    @if(session('success'))
        <div class="locked-banner" style="background:var(--success-lt);border-color:#6ee7b7;color:#065f46;margin-bottom:16px;">
            <span class="material-icons" style="color:var(--success)">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
 
    {{-- ── Locked banner (netpay not approved) ─────────────── --}}
    @if(!$netpayApproved)
    <div class="locked-banner" id="netpay-warning-alert">
        <span class="material-icons">warning_amber</span>
        <div>
            <strong>Bank Interface Unavailable</strong>
            Netpay for {{ $month }} {{ $year }} is currently
            <span style="font-weight:700;background:#fde68a;padding:1px 6px;border-radius:4px;">{{ $netpayStatus }}</span>.
            The Bank Interface tab will be enabled once netpay is approved.
        </div>
    </div>
    @endif
 
    {{-- ── Tab bar ──────────────────────────────────────────── --}}
    <div class="tab-bar" id="tabBar">
        <button class="tab-btn active" data-tab="deductions">
            <span class="material-icons">receipt</span> Agent Payslips
        </button>
        <button class="tab-btn" data-tab="summaries" id="summaries-tab">
            <span class="material-icons">summarize</span> Summaries
        </button>
        <button class="tab-btn" data-tab="overview" id="overview-tab">
            <span class="material-icons">bar_chart</span> Overview
        </button>
        <button class="tab-btn" data-tab="variance" id="variance-tab">
            <span class="material-icons">compare_arrows</span> Variance Reports
        </button>
        <button class="tab-btn {{ !$netpayApproved ? 'disabled' : '' }}"
                data-tab="binterface"
                id="binterface-tab"
                data-netpay-approved="{{ $netpayApproved ? 'true' : 'false' }}"
                data-netpay-status="{{ $netpayStatus }}">
            @if(!$netpayApproved)
                <span class="material-icons lock-icon">lock</span>
            @else
                <span class="material-icons">account_balance</span>
            @endif
            Bank Interface
        </button>
    </div>
 
    {{-- ── Tab body ─────────────────────────────────────────── --}}
    <div class="tab-body">
 
        {{-- ═══════════════════════════════════════
             AGENT PAYSLIPS
        ═══════════════════════════════════════ --}}
        <div class="tab-panel active" id="panel-deductions">
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">person</span></div>
                    Agent Payslip Viewer
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field" style="min-width:200px;">
                            <label>Agent</label>
                            <div class="select-wrap">
                                <select name="staffid" id="staffid" required autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="period" id="period" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view view-slip" id="vpslip">
                            <span class="material-icons">visibility</span> View
                        </button>
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ═══════════════════════════════════════
             SUMMARIES
        ═══════════════════════════════════════ --}}
        <div class="tab-panel" id="panel-summaries">
 
            {{-- Overall summary --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">summarize</span></div>
                    Overall Summary
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto" id="periodoveral" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="openovral">
                            <span class="material-icons">visibility</span> View
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- Items listing --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">list_alt</span></div>
                    Items Listing
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field" style="min-width:180px;">
                            <label>Payroll Item</label>
                            <div class="select-wrap">
                                <select name="pname" id="pname" required autocomplete="off">
                                    <option value="">Select Item</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field" style="min-width:160px;">
                            <label>Agent (From)</label>
                            <div class="select-wrap">
                                <select name="staffSelect3" id="staffSelect3" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field" style="min-width:160px;">
                            <label>Agent (To)</label>
                            <div class="select-wrap">
                                <select name="staffSelect4" id="staffSelect4" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto2" id="periodoveral2" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="openitems">
                            <span class="material-icons">table_view</span> Open
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- Statutory returns --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon" style="background:var(--success-lt);">
                        <span class="material-icons" style="color:var(--success);">account_balance</span>
                    </div>
                    Statutory Returns
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Statutory Item</label>
                            <div class="select-wrap">
                                <select name="statutory" id="statutory" required autocomplete="off">
                                    <option value="">Select Item</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field" style="min-width:160px;">
                            <label>Agent (From)</label>
                            <div class="select-wrap">
                                <select name="staffSelect5" id="staffSelect5" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field" style="min-width:160px;">
                            <label>Agent (To)</label>
                            <div class="select-wrap">
                                <select name="staffSelect6" id="staffSelect6" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto3" id="periodoveral3" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="openstatutory" disabled>
                            <span class="material-icons">visibility</span> Open
                        </button>
                        <button class="btn btn-download" id="downstatutory" disabled>
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
        </div>
 
        {{-- ═══════════════════════════════════════
             OVERVIEW
        ═══════════════════════════════════════ --}}
        <div class="tab-panel" id="panel-overview">
 
            {{-- Payroll summary --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">bar_chart</span></div>
                    Payroll Summary
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field" style="min-width:160px;">
                            <label>Agent (From)</label>
                            <div class="select-wrap">
                                <select name="staffSelect7" id="staffSelect7" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field" style="min-width:160px;">
                            <label>Agent (To)</label>
                            <div class="select-wrap">
                                <select name="staffSelect8" id="staffSelect8" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto4" id="periodoveral4" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="prolsum">
                            <span class="material-icons">table_view</span> Open
                        </button>
                        <button class="btn btn-download" id="excelsum">
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- P10 KRA --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon" style="background:#fef3c7;">
                        <span class="material-icons" style="color:#d97706;">description</span>
                    </div>
                    P10 KRA
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto5" id="periodoveral5" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-download" id="p10kra" disabled>
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- Payment Advices --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon" style="background:#f0fdf4;">
                        <span class="material-icons" style="color:#059669;">payment</span>
                    </div>
                    Payment Advices
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto6" id="periodoveral6" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>Payment Method</label>
                            <div class="chip-group">
                                <div class="chip">
                                    <input type="radio" id="recintre" name="recintres" value="Etransfer" checked>
                                    <label for="recintre">Bank</label>
                                </div>
                                <div class="chip">
                                    <input type="radio" id="separate" name="recintres" value="cheque">
                                    <label for="separate">Cheque</label>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-view" id="banktrans">
                            <span class="material-icons">visibility</span> View
                        </button>
                        <button class="btn btn-download" id="banktransexce" disabled>
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
        </div>
 
        {{-- ═══════════════════════════════════════
             VARIANCE REPORTS
        ═══════════════════════════════════════ --}}
        <div class="tab-panel" id="panel-variance">
 
            {{-- Payroll items variance --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">compare_arrows</span></div>
                    Payroll Items Variance
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field" style="min-width:180px;">
                            <label>Payroll Item</label>
                            <div class="select-wrap">
                                <select name="p2name" id="p2name" required autocomplete="off">
                                    <option value="">Select Item</option>
                                </select>
                            </div>
                        </div>
                        {{-- Hidden agent selects — required by JS --}}
                        <select name="staffSelectst" id="staffSelectst" hidden autocomplete="off">
                            <option value="">Select Agent</option>
                        </select>
                        <select name="staffSelectnd" id="staffSelectnd" hidden autocomplete="off">
                            <option value="">Select Agent</option>
                        </select>
                        <div class="filter-field">
                            <label>1st Period</label>
                            <div class="select-wrap">
                                <select name="1stperiod" id="1stperiod" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>2nd Period</label>
                            <div class="select-wrap">
                                <select name="2ndperiod" id="2ndperiod" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="varitem">
                            <span class="material-icons">compare_arrows</span> Compare
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- Summary variance --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">trending_up</span></div>
                    Summary Variance
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>1st Period</label>
                            <div class="select-wrap">
                                <select name="s1stperiod" id="s1stperiod" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>2nd Period</label>
                            <div class="select-wrap">
                                <select name="s2ndperiod" id="s2ndperiod" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="varsitem">
                            <span class="material-icons">compare_arrows</span> Compare
                        </button>
                    </div>
                </div>
            </div>
 
        </div>
 
        {{-- ═══════════════════════════════════════
             BANK INTERFACE
        ═══════════════════════════════════════ --}}
        <div class="tab-panel" id="panel-binterface">
 
            @if(!$netpayApproved)
            <div class="locked-banner">
                <span class="material-icons">lock</span>
                <div>
                    <strong>Access Restricted</strong>
                    Bank interface files cannot be generated until netpay for {{ $month }} {{ $year }} is approved.
                    Current status: <strong>{{ $netpayStatus }}</strong>
                </div>
            </div>
            @endif
 
            {{-- IFT --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon" style="background:#eff6ff;">
                        <span class="material-icons" style="color:#1a56db;">swap_horiz</span>
                    </div>
                    Immediate Fund Transfer (IFT)
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto7" id="periodoveral7" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-download" id="iftgen">
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- EFT --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon" style="background:#eff6ff;">
                        <span class="material-icons" style="color:#1a56db;">account_balance</span>
                    </div>
                    Electronic Fund Transfer (EFT)
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto8" id="periodoveral8" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-download" id="eftgen">
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- RTGS --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon" style="background:#eff6ff;">
                        <span class="material-icons" style="color:#1a56db;">currency_exchange</span>
                    </div>
                    Real-Time Gross Settlement (RTGS)
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto9" id="periodoveral9" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-download" id="rtgsgen">
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
        </div>
 
    </div>{{-- /tab-body --}}
</div>{{-- /reports-page --}}
 
{{-- ── PDF Viewer modal ─────────────────────────────────────── --}}
{{-- Replaces Bootstrap #staffreportModal --}}
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
 
{{-- ── Progress modal (download) ────────────────────────────── --}}
<div class="progress-modal-backdrop" id="progress-modal">
    <div class="progress-modal-card">
        <h3>Downloading…</h3>
        <p id="progress-message">Preparing your file.</p>
        <div class="progress-track">
            <div class="progress-fill" id="progress-bar"></div>
        </div>
    </div>
</div>
    
                

    

<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>

<!--<script src="{{ asset('js/custom-dropdown.js') }}"></script>--->
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function () {
 
    /* ── Tab switching ─────────────────────────────────────── */
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const tabId    = this.dataset.tab;
            const approved = this.dataset.netpayApproved;
 
            // Block if disabled
            if (this.classList.contains('disabled')) {
                showToast('warning', 'Access Restricted',
                    'Bank Interface requires netpay approval (' + (this.dataset.netpayStatus || '') + ').');
                return;
            }
 
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
 
            this.classList.add('active');
            const panel = document.getElementById('panel-' + tabId);
            if (panel) panel.classList.add('active');
        });
    });
 
    /* ── Expose openTab for any legacy inline calls ────────── */
    window.openTab = function (event, tabId) {
        const btn = document.querySelector('[data-tab="' + tabId + '"]');
        if (btn) btn.click();
    };
 
    window.showNetpayNotApprovedWarning = function (e) {
        if (e) e.preventDefault();
        showToast('warning', 'Access Restricted', 'Netpay must be approved before accessing Bank Interface.');
    };
 
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
 
    /* ── Progress modal helpers ────────────────────────────── */
    window.openProgressModal  = function (msg) {
        document.getElementById('progress-message').textContent = msg || 'Downloading…';
        document.getElementById('progress-bar').style.width = '5%';
        document.getElementById('progress-modal').classList.add('open');
    };
 
    window.updateProgressBar  = function (pct) {
        document.getElementById('progress-bar').style.width = pct + '%';
    };
 
    window.closeProgressModal = function () {
        document.getElementById('progress-modal').classList.remove('open');
        document.getElementById('progress-bar').style.width = '0%';
    };
 
    /* ── Toast ─────────────────────────────────────────────── */
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
 
    window.showMessage = function (msg, isError) {
        showToast(isError ? 'danger' : 'success', isError ? 'Error' : 'Success', msg);
    };
 
    /* ── jQuery Bootstrap modal shim ───────────────────────────
       Your existing JS calls:
         $('#staffreportModal').modal('show')   — to open
         $('#staffreportModal').modal('hide')   — to close
         $('#staffrpt-pdf-container').html(...)  — to insert PDF content
 
       The shim intercepts these jQuery calls and routes them
       to our custom modal, preventing the Bootstrap 5
       "Illegal invocation" / selector-engine error entirely.
    ─────────────────────────────────────────────────────────── */
    (function ($) {
        if (!$ || !$.fn) return;   // guard: jQuery not present
 
        /* Patch .modal() so Bootstrap never touches our custom div */
        const origModal = $.fn.modal;
 
        $.fn.modal = function (action, options) {
            // Only intercept calls on #staffreportModal
            if (this.is && this.is('#staffreportModal')) {
                if (action === 'show' || action === undefined) {
                    openPdfModal();
                } else if (action === 'hide' || action === 'dispose') {
                    closePdfModal();
                }
                // Return jQuery object for chaining
                return this;
            }
            // All other modals: delegate to original Bootstrap handler
            if (origModal) return origModal.apply(this, arguments);
            return this;
        };
 
        /* Patch .html() on #staffrpt-pdf-container so your JS can
           still do $('#staffrpt-pdf-container').html(pdfViewerHTML)
           We intercept it, extract the base64 / src from the injected
           HTML and render it properly. */
        const origHtml = $.fn.html;
 
        $.fn.html = function (value) {
            // Read mode — don't intercept
            if (value === undefined) return origHtml.call(this);
 
            // Write mode on the PDF container
            if (this.is && this.is('#staffrpt-pdf-container')) {
 
                // Case 1: loading placeholder text
                if (typeof value === 'string' && !value.includes('<iframe') && !value.includes('pdfBlob')) {
                    pdfLoading.style.display = 'flex';
                    // Remove any existing iframe
                    const old = pdfContainer.querySelector('iframe');
                    if (old) old.remove();
                    pdfDlBtn.style.display    = 'none';
                    pdfPrintBtn.style.display = 'none';
                    return this;
                }
 
                // Case 2: contains a Blob URL in an <iframe src="blob:...">
                //   Your JS does: URL.createObjectURL(pdfBlob) then builds an <iframe src="${pdfUrl}">
                const iframeSrcMatch = typeof value === 'string'
                    ? value.match(/src=["']([^"']+)["']/)
                    : null;
 
                if (iframeSrcMatch) {
                    const src = iframeSrcMatch[1];
                    pdfLoading.style.display = 'none';
 
                    const old = pdfContainer.querySelector('iframe');
                    if (old) old.remove();
 
                    const iframe  = document.createElement('iframe');
                    iframe.id     = 'pdfFrame';
                    iframe.src    = src;
                    iframe.style.cssText = 'width:100%;height:100%;border:none;display:block;';
                    pdfContainer.appendChild(iframe);
 
                    // Wire action buttons from the injected HTML
                    pdfDlBtn.style.display    = '';
                    pdfPrintBtn.style.display = '';
 
                    // Extract download URL stored by caller (best-effort)
                    currentPdfUrl = src.split('#')[0];
 
                    pdfDlBtn.onclick = function () {
                        const a = document.createElement('a');
                        a.href = currentPdfUrl;
                        a.download = 'Report_' + new Date().toISOString().split('T')[0] + '.pdf';
                        document.body.appendChild(a); a.click(); document.body.removeChild(a);
                    };
 
                    pdfPrintBtn.onclick = function () {
                        const f = document.getElementById('pdfFrame');
                        if (f) { f.contentWindow.focus(); f.contentWindow.print(); }
                    };
 
                    return this;
                }
 
                // Case 3: error / fallback text — show in loading area
                pdfLoading.style.display = 'flex';
                pdfLoading.innerHTML = '<span class="material-icons" style="font-size:36px;color:var(--danger)">error_outline</span>'
                                     + '<span style="color:var(--danger)">' + (value || 'Failed to load report.') + '</span>';
                return this;
            }
 
            // All other elements: delegate to original jQuery .html()
            return origHtml.apply(this, arguments);
        };
 
        /* Also patch the inline download/print button events that
           your JS wires AFTER inserting the HTML — they target
           #downloadPdfBtn / #printPdfBtn by the old IDs.
           Map them to our buttons. */
        $(document).on('click', '#downloadPdfBtn', function () {
            if (pdfDlBtn) pdfDlBtn.click();
        });
 
        $(document).on('click', '#printPdfBtn', function () {
            if (pdfPrintBtn) pdfPrintBtn.click();
        });
 
    }(window.jQuery));
 
});
        $(document).ready(function() {
            $('#closemodal').on('click', function(e) {
                 $('#staffreportModal').modal('hide');
             });
            $('#staffid').select2({
    placeholder: "Select Agent",
    allowClear: true,
    ajax: { 
        url: '{{ route("preports.search") }}', // Use Laravel route
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term, // search term
                page: params.page || 1
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;
            
            return {
                results: data.results,
                pagination: {
                    more: data.pagination && data.pagination.more
                }
            };
        },
        cache: true
    },
    minimumInputLength: 0,
    templateResult: formatStaff,
    templateSelection: formatStaffSelection
});

// Format the staff option display
function formatStaff(staff) {
    if (staff.loading) {
        return staff.text;
    }
    return $('<span>' + staff.text + '</span>');
}

// Format the selected staff display
function formatStaffSelection(staff) {
    return staff.text || staff.id;
}
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
    $(document).on('click', '#openovral', function (e) {
    e.preventDefault();

   var period = $('#periodoveral').val();
   if (!period) {
        showMessage('Please select a Period', true);
        return;
    }
    var actionTaken = false;

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
    url: '{{ route("reports.overall-summary") }}',
    method: 'POST',
    dataType: 'json',
    data: { 
        period: period,
        _token: '{{ csrf_token() }}'
    },
    success: function (response) {
        if (response.pdf) {
            var pdfBlob = new Blob(
                [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                { type: 'application/pdf' }
            );
            var pdfUrl = URL.createObjectURL(pdfBlob);

            // Log "OPEN"
            //logaudit(period, 'OPEN', `Company Summary ${period}`);

            var pdfViewerHTML = `
                <div class="pdf-viewer-wrapper">
                    <div class="pdf-actions mb-1">
                       <button id="downloadPdfBtn" class="btn btn-enhanced btn-cancel btn-sm">
        <i class="fas fa-file-pdf"></i> Download
    </button>

    <button id="printPdfBtn" class="btn btn-enhanced btn-draft btn-sm">
        <i class="fas fa-print"></i> Print
    </button>
                    </div>
                    <iframe 
                        id="staffrptPdfFrame" 
                        src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                        width="100%" 
                        height="80vh" 
                        style="border:1px solid #ddd;"
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
                   // logaudit(period, 'PRINT', `Company Summary ${period}`);
                }
            });

            // DOWNLOAD button handler
            $('#downloadPdfBtn').on('click', function () {
                var link = document.createElement('a');
                link.href = pdfUrl;
                link.download = `Company Summary_${period}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                if (!actionTaken) {
                    actionTaken = true;
                   // logaudit(period, 'DOWNLOAD', `Company Summary ${period}`);
                }
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
$(document).on('click', '#prolsum', function (e) {
    e.preventDefault();

    var period = $('#periodoveral4').val();
    var staff3 = $('#staffSelect7').val(); 
    var staff4 = $('#staffSelect8').val();
   
    if (!period) {
        showMessage('Please select a Period', true);
        return;
    }
    
    var actionTaken = false;
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');
    
    $.ajax({
        url: '{{ route("reports.payroll-summary") }}',
        method: 'POST',
        dataType: 'json',
        data: { 
            period: period,
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
                
                //logaudit(staff3, 'OPEN', `Payroll_Summary_Report_${period}`);
                
                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper">
                        <div class="pdf-actions mb-1">
                            <button id="downloadPdfBtn" class="btn btn-enhanced btn-download">
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button id="printPdfBtn" class="btn btn-enhanced btn-print">
                                <i class="icon-copy fa fa-print"></i> Print
                            </button>
                        </div>
                        <iframe 
                            id="staffrptPdfFrame" 
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                            width="100%" 
                            height="80vh" 
                            style="border:1px solid #ddd;"
                        ></iframe>
                    </div>`;
                    
                $('#staffrpt-pdf-container').html(pdfViewerHTML);
                
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    
                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(staff3, 'PRINT', `Payroll_Summary_Report_${period}`);
                    }
                });
                
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `Payroll_Summary_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                      //  logaudit(staff3, 'DOWNLOAD', `Payroll_Summary_Report_${period}`);
                    }
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
$('#excelsum').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
   var period = $('#periodoveral4').val();
    var staff3 = $('#staffSelect7').val(); 
    var staff4 = $('#staffSelect8').val();
    
    if (!period) {
        showMessage('Please select a period', true);
        return false;
    }

    // Show progress modal
    $('#progress-modal').show();
    $('#progress-bar').css('width', '0%');
    $('#progress-message').text('Generating Payroll Summary report...');

    // Simulate progress
    var progress = 0;
    var progressInterval = setInterval(function() {
        progress += 5;
        if (progress <= 90) {
            $('#progress-bar').css('width', progress + '%');
        }
    }, 100);

    // Use jQuery AJAX
    $.ajax({
        url: '{{ route("payroll.summary.excel") }}',
        method: 'POST',
        data: {
             period: period,
            staff3: staff3,
            staff4: staff4,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob, status, xhr) {
            clearInterval(progressInterval);
            
            $('#progress-bar').css('width', '100%');
            $('#progress-message').text('Payroll Summary generated successfully!');
            
            // Get filename from Content-Disposition header if available
            var filename = `IFT${period}.csv`;
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Create download link
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            // Cleanup
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }, 100);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 1000);
        },
        error: function(xhr, status, error) {
            clearInterval(progressInterval);
            
            var errorMessage = 'An error occurred while generating the IFT report';
            
            // Try to parse error response
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    var errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            $('#progress-message').text(errorMessage);
            console.error('Error:', error);
            console.error('Status:', status);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 3000);
        }
    });
    
    return false;
});
$(document).on('click', '#banktrans', function (e) {
    e.preventDefault();

    var period = $('#periodoveral6').val(); 
    var recintres = $('input[name="recintres"]:checked').val(); 

    if (!period) {
        showMessage('Please select a Period', true);
        return;
    }
    
    var actionTaken = false;
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');
    
    $.ajax({
        url: '{{ route("reports.bank-advice") }}',
        method: 'POST',
        dataType: 'json',
        data: { 
            period: period,
            recintres: recintres,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);
                
                //logaudit(recintres, 'OPEN', `${recintres}_Bank_Advice_Report_${period}`);
                
                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper">
                        <div class="pdf-actions mb-1">
                            <button id="downloadPdfBtn" class="btn btn-enhanced btn-download">
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button id="printPdfBtn" class="btn btn-enhanced btn-print">
                                <i class="icon-copy fa fa-print"></i> Print
                            </button>
                        </div>
                        <iframe 
                            id="staffrptPdfFrame" 
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                            width="100%" 
                            height="80vh" 
                            style="border:1px solid #ddd;"
                        ></iframe>
                    </div>`;
                    
                $('#staffrpt-pdf-container').html(pdfViewerHTML);
                
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    
                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(recintres, 'PRINT', `${recintres}_Bank_Advice_Report_${period}`);
                    }
                });
                
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `${recintres}_Bank_Advice_Report_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(recintres, 'DOWNLOAD', `${recintres}_Bank_Advice_Report_${period}`);
                    }
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

    var period = $('#periodoveral2').val();
    var pname = $('#pname').val();
    var staff3 = $('#staffSelect3').val();
    var staff4 = $('#staffSelect4').val();
   
    var actionTaken = false;
    
    if (!pname) {
        showMessage('Please select a Payroll item', true);
        return;
    }
    
    if (!period) {
        showMessage('Please select a Period', true);
        return;
    }

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
        url: '{{ route("reports.payroll-items") }}', // Laravel route
        method: 'POST',
        dataType: 'json',
        data: { 
            period: period,
            pname: pname,
            staff3: staff3,
            staff4: staff4,
            _token: '{{ csrf_token() }}' // CSRF token
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);

                // Log "OPEN"
                //logaudit(staff3, 'OPEN', `${pname}_Listing_${period}`);

                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper">
                        <div class="pdf-actions mb-1">
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
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                            width="100%" 
                            height="80vh" 
                            style="border:1px solid #ddd;"
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

    let period = $('#periodoveral2').val();
    let pname = $('#pname').val();
    let staff3 = $('#staffSelect3').val();
    let staff4 = $('#staffSelect4').val();
    
    // Extract month and year from period
    // Assuming period format is like "January2024" (month name directly followed by year)
    let month = period.substring(0, period.length - 4); // Gets everything except last 4 chars
    let year = period.substring(period.length - 4);     // Gets last 4 chars
    
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
$(document).on('click', '#varitem', function (e) {
    e.preventDefault();

    var stperiod = $('#1stperiod').val();
    var ndperiod = $('#2ndperiod').val();
    var pname = $('#p2name').val();
    var staff3 = $('#staffSelectst').val();
    var staff4 = $('#staffSelectnd').val();
    
    if (!pname) {
        showMessage('Please select a Payroll item', true);
        return;
    }
    
    if (!stperiod) {
        showMessage('Please select a 1st Period', true);
        return;
    }
    
    if (!ndperiod) {
        showMessage('Please select a 2nd Period', true);
        return;
    }
    
    if (stperiod === ndperiod) {
        showMessage('Sorry The 1st and 2nd period cannot be same', true);
        return; 
    }
    
    var actionTaken = false;
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');
    
    $.ajax({
        url: '{{ route("reports.variance") }}',
        method: 'POST',
        dataType: 'json',
        data: { 
            stperiod: stperiod,
            ndperiod: ndperiod,
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
                
               // logaudit(staff4, 'OPEN', `${pname}_Variance_Report_${stperiod}_to_${ndperiod}`);
                
                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper">
                        <div class="pdf-actions mb-1">
                            <button id="downloadPdfBtn" class="btn btn-enhanced btn-download">
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button id="printPdfBtn" class="btn btn-enhanced btn-print">
                                <i class="icon-copy fa fa-print"></i> Print
                            </button>
                        </div>
                        <iframe 
                            id="staffrptPdfFrame" 
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                            width="100%" 
                            height="80vh" 
                            style="border:1px solid #ddd;"
                        ></iframe>
                    </div>`;
                    
                $('#staffrpt-pdf-container').html(pdfViewerHTML);
                
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    
                    if (!actionTaken) {
                        actionTaken = true;
                      //  logaudit(staff4, 'PRINT', `${pname}_Variance_Report_${stperiod}_to_${ndperiod}`);
                    }
                });
                
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `${pname}_Variance_Report_${stperiod}_to_${ndperiod}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(staff4, 'DOWNLOAD', `${pname}_Variance_Report_${stperiod}_to_${ndperiod}`);
                    }
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
$(document).on('click', '#varsitem', function (e) {
    e.preventDefault();

    var stperiod = $('#s1stperiod').val();
    var ndperiod = $('#s2ndperiod').val();
    
    if (!stperiod) {
        showMessage('Please select a 1st Period', true);
        return;
    }
    
    if (!ndperiod) {
        showMessage('Please select a 2nd Period', true);
        return;
    }
    
    if (stperiod === ndperiod) {
        showMessage('Sorry The 1st and 2nd period cannot be same', true);
        return; 
    }
    
    var actionTaken = false;
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');
    
    $.ajax({
        url: '{{ route("reports.payroll-variance") }}',
        method: 'POST',
        dataType: 'json',
        data: { 
            stperiod: stperiod,
            ndperiod: ndperiod,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);
                
               // logaudit(ndperiod, 'OPEN', `Payroll_Variance_Report_${stperiod}_to_${ndperiod}`);
                
                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper">
                        <div class="pdf-actions mb-1">
                            <button id="downloadPdfBtn" class="btn btn-enhanced btn-download">
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button id="printPdfBtn" class="btn btn-enhanced btn-print">
                                <i class="icon-copy fa fa-print"></i> Print
                            </button>
                        </div>
                        <iframe 
                            id="staffrptPdfFrame" 
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                            width="100%" 
                            height="80vh" 
                            style="border:1px solid #ddd;"
                        ></iframe>
                    </div>`;
                    
                $('#staffrpt-pdf-container').html(pdfViewerHTML);
                
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    
                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(ndperiod, 'PRINT', `Payroll_Variance_Report_${stperiod}_to_${ndperiod}`);
                    }
                });
                
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `Payroll_Variance_Report_${stperiod}_to_${ndperiod}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(ndperiod, 'DOWNLOAD', `Payroll_Variance_Report_${stperiod}_to_${ndperiod}`);
                    }
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
$('#eftgen').on('click', function(e) {
    e.preventDefault(); // ✅ Prevent default form submission
    e.stopPropagation(); // ✅ Stop event bubbling
    
    var period = $('#periodoveral8').val();
    
    if (!period) {
        showMessage('Please select a period', true);
        return;
    }

    // Show progress modal
    $('#progress-modal').show();
    $('#progress-bar').css('width', '0%');
    $('#progress-message').text('Generating EFT report...');

    // Simulate progress
    var progress = 0;
    var progressInterval = setInterval(function() {
        progress += 5;
        if (progress <= 90) {
            $('#progress-bar').css('width', progress + '%');
        }
    }, 100);

    // Use jQuery AJAX for better compatibility
    $.ajax({
        url: '{{ route("generate.eft.report") }}',
        method: 'POST',
        data: {
            period: period,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob' // Important for file download
        },
        success: function(blob, status, xhr) {
            clearInterval(progressInterval);
            
            $('#progress-bar').css('width', '100%');
            $('#progress-message').text('EFT report generated successfully!');
            
            // Get filename from Content-Disposition header if available
            var filename = `EFT${period}.csv`;
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Create download link
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            // Cleanup
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }, 100);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 1000);
        },
        error: function(xhr, status, error) {
            clearInterval(progressInterval);
            
            var errorMessage = 'An error occurred while generating the EFT report';
            
            // Try to parse error response
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    var errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            $('#progress-message').text(errorMessage);
            console.error('Error:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 3000);
        }
    });
});
// RTGS Report Generation
$('#rtgsgen').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var period = $('#periodoveral9').val();
    
    if (!period) {
        showMessage('Please select a period', true);
        return false;
    }

    // Show progress modal
    $('#progress-modal').show();
    $('#progress-bar').css('width', '0%');
    $('#progress-message').text('Generating RTGS report...');

    // Simulate progress
    var progress = 0;
    var progressInterval = setInterval(function() {
        progress += 5;
        if (progress <= 90) {
            $('#progress-bar').css('width', progress + '%');
        }
    }, 100);

    // Use jQuery AJAX
    $.ajax({
        url: '{{ route("generate.rtgs.report") }}',
        method: 'POST',
        data: {
            period: period,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob, status, xhr) {
            clearInterval(progressInterval);
            
            $('#progress-bar').css('width', '100%');
            $('#progress-message').text('RTGS report generated successfully!');
            
            // Get filename from Content-Disposition header if available
            var filename = `RTGS${period}.csv`;
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Create download link
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            // Cleanup
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }, 100);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 1000);
        },
        error: function(xhr, status, error) {
            clearInterval(progressInterval);
            
            var errorMessage = 'An error occurred while generating the RTGS report';
            
            // Try to parse error response
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    var errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            $('#progress-message').text(errorMessage);
            console.error('Error:', error);
            console.error('Status:', status);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 3000);
        }
    });
    
    return false;
});

// IFT Report Generation
$('#iftgen').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var period = $('#periodoveral7').val();
    
    if (!period) {
        showMessage('Please select a period', true);
        return false;
    }

    // Show progress modal
    $('#progress-modal').show();
    $('#progress-bar').css('width', '0%');
    $('#progress-message').text('Generating IFT report...');

    // Simulate progress
    var progress = 0;
    var progressInterval = setInterval(function() {
        progress += 5;
        if (progress <= 90) {
            $('#progress-bar').css('width', progress + '%');
        }
    }, 100);

    // Use jQuery AJAX
    $.ajax({
        url: '{{ route("generate.ift.report") }}',
        method: 'POST',
        data: {
            period: period,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob, status, xhr) {
            clearInterval(progressInterval);
            
            $('#progress-bar').css('width', '100%');
            $('#progress-message').text('IFT report generated successfully!');
            
            // Get filename from Content-Disposition header if available
            var filename = `IFT${period}.csv`;
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Create download link
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            // Cleanup
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }, 100);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 1000);
        },
        error: function(xhr, status, error) {
            clearInterval(progressInterval);
            
            var errorMessage = 'An error occurred while generating the IFT report';
            
            // Try to parse error response
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    var errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            $('#progress-message').text(errorMessage);
            console.error('Error:', error);
            console.error('Status:', status);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 3000);
        }
    });
    
    return false;
});
    $(document).on('click', '.view-slip', function (e) {
    e.preventDefault();

    var staffid = $('#staffid').val();
    var period = $('#period').val();
    var actionTaken = false;
    
    if (!staffid) {
        showMessage('Please select a Staff', true);
        return;
    }
    
    if (!period) {
        showMessage('Please select a Period', true);
        return;
    }

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
        url: '{{ route("payslip.generate") }}', // Laravel route
        method: 'POST',
        dataType: 'json',
        data: { 
            staffid: staffid, 
            period: period,
            _token: '{{ csrf_token() }}' // CSRF token
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);

                // Log "OPEN"
                //logaudit(staffid, 'OPEN', `Payslip for ${period}`);

                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper">
                        <div class="pdf-actions mb-1">
                            <button id="downloadPdfBtn" class="btn btn-enhanced btn-download">
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button id="printPdfBtn" class="btn btn-enhanced btn-print">
                                <i class="icon-copy fa fa-print"></i> Print
                            </button>
                        </div>
                        <iframe 
                            id="staffrptPdfFrame" 
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                            width="100%" 
                            height="80vh" 
                            style="border:1px solid #ddd;"
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
                       // logaudit(staffid, 'PRINT', `Payslip for ${period}`);
                    }
                });

                // DOWNLOAD button handler
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `payslip_${staffid}_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staffid, 'DOWNLOAD', `Payslip for ${period}`);
                    }
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

 $('[data-toggle="tooltip"]').tooltip({
        html: true,
        trigger: 'hover focus',
        boundary: 'window'
    });
    
    // Check if bank interface tab should be disabled
    var binterfaceTab = $('#binterface-tab');
    var isNetpayApproved = binterfaceTab.data('netpay-approved');
    
    if (!isNetpayApproved) {
        // Ensure tab is disabled
        binterfaceTab.addClass('disabled');
        
        // Show tooltip on hover
        binterfaceTab.on('mouseenter', function() {
            $(this).tooltip('show');
        });
        
        // Hide the bank interface content tab if it exists
        $('#binterface').hide();
        
        
    }
         });

         function showNetpayNotApprovedWarning(event) {
    event.preventDefault();
    event.stopPropagation();
    
    var netpayStatus = $('#binterface-tab').data('netpay-status');
    var month = '{{ $month }}';
    var year = '{{ $year }}';
    
    Swal.fire({
        icon: 'warning',
        title: 'Bank Interface Unavailable',
        html: `
            <div style="text-align: left;">
                <p><strong>The Bank Interface is currently unavailable.</strong></p>
                <hr>
                <p><strong>Period:</strong> ${month} ${year}</p>
                <p><strong>Netpay Status:</strong> <span class="badge badge-warning">${netpayStatus}</span></p>
                <hr>
                <p>Please ensure the following steps are completed:</p>
                <ol style="text-align: left; padding-left: 20px;">
                    <li>Run <strong>Auto Calculate</strong> in Manage Payroll</li>
                    <li>Click <strong>Notify Approver</strong></li>
                    <li>Wait for netpay approval from authorized personnel</li>
                </ol>
                <p><small><em>The Bank Interface will be automatically enabled once netpay is approved.</em></small></p>
            </div>
        `,
        confirmButtonColor: '#f39c12',
        confirmButtonText: 'Understood',
        width: '600px'
    });
    
    return false;
}
        $('#summaries-tab').on('click', function() {
    // Show loading state
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
                $('#periodoveral, #periodoveral2, #periodoveral3').html(periodHtml);
                
                // Populate pname dropdown
                const pnameHtml = '<option value="">Select Item</option>' + 
                    data.pnameOptions.map(opt => 
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
});

$('#variance-tab').on('click', function() {
    // Show loading state
    $('#1stperiod, #staffSelectnd, #staffSelectst, #p2name, #2ndperiod, #s1stperiod, #s2ndperiod')
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
                $('#1stperiod, #2ndperiod, #s1stperiod, #s2ndperiod').html(periodHtml);
                
                // Populate pname dropdown
                const pnameHtml = '<option value="">Select Item</option>' + 
                    data.pnameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#p2name').html(pnameHtml);
                
                // Populate staff dropdowns
                const staffHtml = '<option value="">Select Agent</option>' + 
                    data.snameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#staffSelectnd, #staffSelectst').html(staffHtml);
                // Initialize Select2 for staff selects
                ['#staffSelectst', '#staffSelectnd'].forEach(function(selector) {
                    if (!$(selector).hasClass("select2-hidden-accessible")) {
                        $(selector).select2({
                            placeholder: selector.includes('3') || selector.includes('6') ? "Select Agent" : "Search",
                            allowClear: true
                        });
                    }
                });
                
                // Auto-select first and last staff for range selections
                var options3 = $('#staffSelectst option:not([value=""])');
                if (options3.length > 0) {
                    $('#staffSelectst').val(options3.first().val()).trigger('change');
                    $('#staffSelectnd').val(options3.last().val()).trigger('change');
                }
                
                
                // Initialize Select2 for pname
                if (!$('#p2name').hasClass("select2-hidden-accessible")) {
                    $('#p2name').select2({
                        placeholder: "Select Item",
                        allowClear: true
                    });
                }
                
                
                
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
});
  $('#overview-tab').on('click', function() {
    // Show loading state
    $('#staffSelect7, #staffSelect8, #periodoveral4, #periodoveral5, #periodoveral6')
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
                $('#periodoveral4, #periodoveral5, #periodoveral6').html(periodHtml);
                
                // Populate pname dropdown
                const pnameHtml = '<option value="">Select Item</option>' + 
                    data.pnameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#pname').html(pnameHtml);
                
                // Populate staff dropdowns
                const staffHtml = '<option value="">Select Agent</option>' + 
                    data.snameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#staffSelect7, #staffSelect8').html(staffHtml);
                
                
                
                // Initialize Select2 for staff selects
                ['#staffSelect7', '#staffSelect8'].forEach(function(selector) {
                    if (!$(selector).hasClass("select2-hidden-accessible")) {
                        $(selector).select2({
                            placeholder: selector.includes('3') || selector.includes('6') ? "Select Agent" : "Search",
                            allowClear: true
                        });
                    }
                });
                
                // Auto-select first and last staff for range selections
                var options3 = $('#staffSelect7 option:not([value=""])');
                if (options3.length > 0) {
                    $('#staffSelect7').val(options3.first().val()).trigger('change');
                    $('#staffSelect8').val(options3.last().val()).trigger('change');
                }
                
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
});
$('#binterface-tab').on('click', function() {
    // Show loading state
    $('#periodoveral7, #periodoveral8, #periodoveral9')
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
                $('#periodoveral7, #periodoveral8, #periodoveral9').html(periodHtml);
                
                
                
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
