<x-custom-admin-layout>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<style>
    /* ── Page-specific — tokens from corepay.css ─────────────── */
 
    .reports-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }
 
    .page-heading { margin-bottom: 24px; }
 
    .page-heading h1 {
        font-family: var(--font-head);
        font-size: 22px;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 4px;
    }
 
    .page-heading p { font-size: 13.5px; color: var(--muted); margin: 0; }
 
    /* ── Report card grid ────────────────────────────────────── */
    .report-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }
 
    /* ── Individual report card ──────────────────────────────── */
    .report-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
        gap: 16px;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
        transition: box-shadow .2s, transform .2s;
    }
 
    .report-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }
 
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
 
    .report-card:nth-child(2) { animation-delay: .07s; }
    .report-card:nth-child(3) { animation-delay: .14s; }
 
    .report-card-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
 
    .report-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
 
    .report-icon .material-icons { font-size: 22px; }
 
    .report-icon.blue   { background: var(--accent-lt);  }
    .report-icon.blue   .material-icons { color: var(--accent); }
    .report-icon.green  { background: var(--success-lt); }
    .report-icon.green  .material-icons { color: var(--success); }
    .report-icon.purple { background: #f3f0ff; }
    .report-icon.purple .material-icons { color: #7c3aed; }
 
    .report-card-title {
        font-family: var(--font-head);
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 4px;
    }
 
    .report-card-desc {
        font-size: 13px;
        color: var(--muted);
        line-height: 1.5;
        margin: 0;
    }
 
    /* ── Buttons ─────────────────────────────────────────────── */
    .btn {
        height: 40px;
        padding: 0 18px;
        border: none;
        border-radius: var(--radius-sm);
        font-family: var(--font-body);
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: transform .2s, box-shadow .2s, filter .2s;
        letter-spacing: .01em;
        text-decoration: none;
        width: 100%;
        justify-content: center;
    }
 
    .btn .material-icons { font-size: 17px; }
    .btn:hover { transform: translateY(-1px); }
    .btn:active { transform: translateY(0); }
 
    .btn-primary-report {
        background: linear-gradient(135deg, #1a56db, #4f46e5);
        color: #fff;
        box-shadow: 0 4px 14px rgba(26,86,219,.25);
    }
 
    .btn-primary-report:hover { box-shadow: 0 7px 20px rgba(26,86,219,.35); filter: brightness(1.05); }
 
    /* ── PDF viewer modal ────────────────────────────────────── */
    .pdf-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.55);
        backdrop-filter: blur(4px);
        z-index: 8000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
 
    .pdf-modal-backdrop.open { display: flex; }
 
    .pdf-modal-card {
        background: var(--surface);
        border-radius: 20px;
        width: 100%;
        max-width: 1000px;
        height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 24px 80px rgba(0,0,0,.25);
        overflow: hidden;
        animation: fadeUp .3s cubic-bezier(.22,.61,.36,1) both;
    }
 
    /* Modal header */
    .pdf-modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }
 
    .pdf-modal-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
 
    .pdf-modal-icon .material-icons { font-size: 18px; color: var(--accent); }
 
    .pdf-modal-title {
        font-family: var(--font-head);
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
        flex: 1;
    }
 
    /* Action buttons in header */
    .pdf-modal-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
 
    .btn-icon {
        width: 36px; height: 36px;
        border: 1.5px solid var(--border);
        border-radius: 9px;
        background: var(--surface);
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: var(--muted);
        transition: all .2s;
        flex-shrink: 0;
    }
 
    .btn-icon:hover { color: var(--ink); border-color: #9ca3af; background: var(--bg); }
    .btn-icon .material-icons { font-size: 18px; }
 
    .btn-download-pdf {
        height: 36px;
        padding: 0 14px;
        background: linear-gradient(135deg, #059669, #10b981);
        color: #fff;
        border: none;
        border-radius: 9px;
        font-family: var(--font-body);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 3px 10px rgba(5,150,105,.25);
    }
 
    .btn-download-pdf:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(5,150,105,.35); }
    .btn-download-pdf .material-icons { font-size: 15px; }
 
    .btn-print-pdf {
        height: 36px;
        padding: 0 14px;
        background: linear-gradient(135deg, #1a56db, #4f46e5);
        color: #fff;
        border: none;
        border-radius: 9px;
        font-family: var(--font-body);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 3px 10px rgba(26,86,219,.25);
    }
 
    .btn-print-pdf:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(26,86,219,.35); }
    .btn-print-pdf .material-icons { font-size: 15px; }
 
    /* PDF body */
    .pdf-modal-body {
        flex: 1;
        overflow: hidden;
        position: relative;
    }
 
    .pdf-modal-body iframe {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }
 
    /* Loading state */
    .pdf-loading {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        background: var(--surface);
        color: var(--muted);
        font-size: 14px;
    }
 
    .pdf-loading .material-icons {
        font-size: 36px;
        color: #d1d5db;
        animation: spin 1.5s linear infinite;
    }
 
    @keyframes spin { to { transform: rotate(360deg); } }
 
    /* Error state */
    .pdf-error {
        display: none;
        position: absolute;
        inset: 0;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 10px;
        background: var(--surface);
        color: var(--danger);
        font-size: 14px;
        text-align: center;
        padding: 24px;
    }
 
    .pdf-error .material-icons { font-size: 40px; opacity: .7; }
 
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
        animation: toastIn .35s cubic-bezier(.22,.61,.36,1) both;
        cursor: pointer;
    }
 
    .toast-msg.leaving { animation: toastOut .3s ease forwards; }
 
    @keyframes toastIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
    @keyframes toastOut { to { opacity:0; transform:translateX(40px); } }
 
    .toast-msg.success { background: var(--success-lt); color: #065f46; }
    .toast-msg.danger  { background: var(--danger-lt);  color: #991b1b; }
    .toast-msg .material-icons { font-size: 20px; flex-shrink: 0; }
 
    @media (max-width: 640px) {
        .reports-page { padding: 18px 14px; }
        .pdf-modal-card { height: 95vh; border-radius: 16px; }
        .pdf-modal-actions .btn-download-pdf span:last-child,
        .pdf-modal-actions .btn-print-pdf span:last-child { display: none; }
    }
</style>
 
<div class="reports-page">
 
    <div class="page-heading">
        <h1>Agent Reports</h1>
        <p>Generate and download reports for all registered agents.</p>
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
 
    <div class="report-grid">
 
        <!-- Full Staff List -->
        <div class="report-card">
            <div class="report-card-header">
                <div class="report-icon blue">
                    <span class="material-icons">groups</span>
                </div>
                <div>
                    <p class="report-card-title">Full Agent List</p>
                    <p class="report-card-desc">
                        A complete listing of all registered agents with their details.
                    </p>
                </div>
            </div>
            <button class="btn btn-primary-report" id="openFullReport">
                <span class="material-icons">picture_as_pdf</span>
                Generate Report
            </button>
        </div>
 
        {{-- Additional report cards can be uncommented / added here following the same pattern --}}
        {{--
        <div class="report-card" style="animation-delay:.07s">
            <div class="report-card-header">
                <div class="report-icon green">
                    <span class="material-icons">account_tree</span>
                </div>
                <div>
                    <p class="report-card-title">Departmental Summary</p>
                    <p class="report-card-desc">Agents grouped by department and section with headcount totals.</p>
                </div>
            </div>
            <button class="btn btn-primary-report" id="openDeptReport">
                <span class="material-icons">picture_as_pdf</span>
                Generate Report
            </button>
        </div>
 
        <div class="report-card" style="animation-delay:.14s">
            <div class="report-card-header">
                <div class="report-icon purple">
                    <span class="material-icons">payments</span>
                </div>
                <div>
                    <p class="report-card-title">Payroll Type Summary</p>
                    <p class="report-card-desc">Agents segmented by payroll type for payroll processing.</p>
                </div>
            </div>
            <button class="btn btn-primary-report" id="openPayrollReport">
                <span class="material-icons">picture_as_pdf</span>
                Generate Report
            </button>
        </div>
        --}}
 
    </div>
</div>
 
<!-- ── PDF viewer modal ───────────────────────────────────── -->
<div class="pdf-modal-backdrop" id="pdfModal">
    <div class="pdf-modal-card">
 
        <div class="pdf-modal-header">
            <div class="pdf-modal-icon">
                <span class="material-icons">picture_as_pdf</span>
            </div>
            <span class="pdf-modal-title" id="pdfModalTitle">Report Viewer</span>
 
            <div class="pdf-modal-actions">
                <button class="btn-download-pdf" id="downloadPdfBtn" style="display:none;">
                    <span class="material-icons">download</span>
                    <span>Download</span>
                </button>
                <button class="btn-print-pdf" id="printPdfBtn" style="display:none;">
                    <span class="material-icons">print</span>
                    <span>Print</span>
                </button>
                <button class="btn-icon" id="pdfModalClose">
                    <span class="material-icons">close</span>
                </button>
            </div>
        </div>
 
        <div class="pdf-modal-body" id="pdfModalBody">
            <!-- Loading state -->
            <div class="pdf-loading" id="pdfLoading">
                <span class="material-icons">sync</span>
                <span>Generating report…</span>
            </div>
 
            <!-- Error state -->
            <div class="pdf-error" id="pdfError">
                <span class="material-icons">error_outline</span>
                <span id="pdfErrorMsg">Failed to generate the report.</span>
            </div>
 
            <!-- The iframe is injected here once PDF is ready -->
        </div>
 
    </div>
</div>
 
<script>
    const amanage = '{{ route("reports.full-staff") }}';
</script>
    <script src="{{ asset('js/areports.js') }}"></script>
    
   
</x-custom-admin-layout>