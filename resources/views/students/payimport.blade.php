<x-custom-admin-layout>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>

<style>
    .import-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }

    

    .import-layout {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 960px) { .import-layout { grid-template-columns: 1fr; } }

    .import-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
    }

    .import-card:nth-child(2) { animation-delay: .08s; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .card-top {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
    }

    .card-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .card-icon .material-icons { font-size: 18px; color: var(--accent); }
    .card-icon.preview-icon { background: var(--success-lt); }
    .card-icon.preview-icon .material-icons { color: var(--success); }

    .card-title { font-family: var(--font-head); font-size: 15px; font-weight: 700; color: var(--ink); margin: 0 0 2px; }
    .card-subtitle { font-size: 12px; color: var(--muted); margin: 0; }
    .card-body { padding: 22px; }

    /* Drop zone */
    .drop-zone {
        border: 2px dashed var(--border);
        border-radius: var(--radius-sm);
        padding: 28px 20px;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        margin-bottom: 20px;
        position: relative;
    }

    .drop-zone:hover, .drop-zone.drag-over { border-color: var(--accent); background: var(--accent-lt); }

    .drop-zone input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }

    .drop-zone .dz-icon {
        width: 48px; height: 48px; border-radius: 14px; background: #f3f4f8;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 12px; transition: background .2s;
    }

    .drop-zone:hover .dz-icon, .drop-zone.drag-over .dz-icon { background: #dbeafe; }
    .drop-zone .dz-icon .material-icons { font-size: 24px; color: var(--muted); transition: color .2s; }
    .drop-zone:hover .dz-icon .material-icons, .drop-zone.drag-over .dz-icon .material-icons { color: var(--accent); }

    .dz-label { font-size: 14px; font-weight: 600; color: var(--ink); margin-bottom: 4px; }
    .dz-hint  { font-size: 12px; color: var(--muted); }

    .file-pill {
        display: none; align-items: center; gap: 8px;
        padding: 8px 12px; background: var(--success-lt);
        border: 1.5px solid #6ee7b7; border-radius: 100px;
        font-size: 13px; font-weight: 500; color: var(--success); margin-bottom: 20px;
    }

    .file-pill.show { display: flex; }
    .file-pill .material-icons { font-size: 16px; }
    .remove-file { margin-left: auto; cursor: pointer; opacity: .7; transition: opacity .2s; }
    .remove-file:hover { opacity: 1; }

    /* Import mode */
    .mode-section { margin-bottom: 22px; }

    .mode-label { font-size: 12.5px; font-weight: 500; color: #374151; margin-bottom: 10px; display: block; }

    .mode-options { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

    .mode-chip { position: relative; }
    .mode-chip input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }

    .mode-chip label {
        display: flex; flex-direction: column; gap: 4px;
        padding: 12px 14px; border: 1.5px solid var(--border);
        border-radius: var(--radius-sm); cursor: pointer; background: #fafafa; transition: all .2s;
    }

    .mode-chip label:hover { border-color: #9ca3af; background: var(--surface); }
    .mode-chip input:checked + label { border-color: var(--accent); background: var(--accent-lt); }

    .mode-chip-header {
        display: flex; align-items: center; gap: 6px;
        font-size: 13.5px; font-weight: 600; color: var(--ink);
    }

    .mode-chip input:checked + label .mode-chip-header { color: var(--accent); }
    .mode-chip-header .material-icons { font-size: 16px; color: var(--muted); }
    .mode-chip input:checked + label .mode-chip-header .material-icons { color: var(--accent); }
    .mode-chip-desc { font-size: 12px; color: var(--muted); line-height: 1.5; padding-left: 22px; }

    .mode-warning {
        display: none; align-items: flex-start; gap: 8px;
        padding: 11px 13px; background: #fffbeb; border: 1.5px solid #fde68a;
        border-radius: var(--radius-sm); font-size: 12.5px; color: #92400e; margin-top: 10px;
    }

    .mode-warning.show { display: flex; }
    .mode-warning .material-icons { font-size: 16px; color: var(--warning); flex-shrink: 0; margin-top: 1px; }

    /* Buttons */
    .btn {
        height: 42px; padding: 0 20px; border: none; border-radius: var(--radius-sm);
        font-family: var(--font-body); font-size: 14px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 7px;
        transition: transform .2s, box-shadow .2s, filter .2s;
        letter-spacing: .01em; text-decoration: none;
    }

    .btn .material-icons { font-size: 17px; }
    .btn:hover { transform: translateY(-1px); }
    .btn:active { transform: translateY(0); }

    .btn-import {
        width: 100%; justify-content: center;
        background: linear-gradient(135deg, #1a56db, #4f46e5);
        color: #fff; box-shadow: 0 4px 14px rgba(26,86,219,.28);
    }

    .btn-import:hover { box-shadow: 0 7px 20px rgba(26,86,219,.38); filter: brightness(1.05); }
    .btn-import:disabled { opacity: .55; cursor: not-allowed; transform: none; filter: none; }

    .btn-ghost {
        height: 36px; padding: 0 14px; background: var(--surface);
        color: var(--muted); border: 1.5px solid var(--border); font-size: 13px;
    }

    .btn-ghost:hover { color: var(--ink); border-color: #9ca3af; }

    .btn-warning-dl {
        display: inline-flex; align-items: center; gap: 6px;
        height: 34px; padding: 0 14px;
        background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff;
        border: none; border-radius: var(--radius-sm);
        font-family: var(--font-body); font-size: 13px; font-weight: 600;
        cursor: pointer; text-decoration: none;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 3px 10px rgba(217,119,6,.3);
    }

    .btn-warning-dl:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(217,119,6,.4); }
    .btn-warning-dl .material-icons { font-size: 15px; }

    /* Preview */
    .empty-review {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: 52px 24px; text-align: center; color: var(--muted);
    }

    .empty-review .material-icons { font-size: 40px; color: #d1d5db; margin-bottom: 12px; }
    .empty-review p { font-size: 14px; margin: 0; }

    #tableContainer { overflow-x: auto; }

    .review-table { width: 100%; border-collapse: collapse; font-size: 13px; font-family: var(--font-body); }

    .review-table thead th {
        background: #f9fafb; color: var(--muted); font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: .06em;
        padding: 10px 14px; border-bottom: 1px solid var(--border); white-space: nowrap;
    }

    .review-table tbody td {
        padding: 10px 14px; border-bottom: 1px solid #f3f4f8; color: var(--ink); vertical-align: middle;
    }

    .review-table tbody tr:last-child td { border-bottom: none; }
    .review-table tbody tr:hover td { background: #f8faff; }

    .row-count {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; background: var(--accent-lt);
        border-radius: 100px; font-size: 12px; font-weight: 600; color: var(--accent); margin-left: 8px;
    }

    /* Progress modal */
    .progress-modal-backdrop {
        position: fixed; inset: 0; background: rgba(0,0,0,.45);
        backdrop-filter: blur(4px); z-index: 9000;
        display: none; align-items: center; justify-content: center;
    }

    .progress-modal-backdrop.open { display: flex; }

    .progress-modal-card {
        background: var(--surface); border-radius: 20px; padding: 32px;
        width: 100%; max-width: 460px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        animation: fadeUp .3s cubic-bezier(.22,.61,.36,1) both;
    }

    .progress-modal-icon {
        width: 52px; height: 52px; border-radius: 14px; background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 16px; transition: background .3s;
    }

    .progress-modal-icon .material-icons { font-size: 26px; color: var(--accent); transition: color .3s; }
    .progress-modal-icon.spinning .material-icons { animation: spin 1.2s linear infinite; }
    .progress-modal-icon.done-success { background: var(--success-lt); }
    .progress-modal-icon.done-success .material-icons { color: var(--success); animation: none; }
    .progress-modal-icon.done-error { background: var(--danger-lt); }
    .progress-modal-icon.done-error .material-icons { color: var(--danger); animation: none; }

    @keyframes spin { to { transform: rotate(360deg); } }

    .progress-modal-card h3 {
        font-family: var(--font-head); font-size: 17px; font-weight: 700; color: var(--ink); margin: 0 0 4px;
    }

    .prog-sub { font-size: 13px; color: var(--muted); margin: 0 0 20px; }

    .progress-track { height: 8px; background: #e5e7eb; border-radius: 100px; overflow: hidden; margin-bottom: 8px; }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #1a56db, #6366f1);
        border-radius: 100px; width: 0%; transition: width .4s ease, background .4s;
    }

    .progress-fill.fill-success { background: linear-gradient(90deg, #059669, #10b981); }
    .progress-fill.fill-danger  { background: linear-gradient(90deg, #dc2626, #ef4444); }

    .progress-pct { font-size: 13px; font-weight: 600; color: var(--accent); }

    /* Live stat chips */
    .import-stats { display: none; gap: 10px; margin: 16px 0 0; }
    .import-stats.show { display: flex; }

    .stat-chip {
        flex: 1; display: flex; align-items: center; gap: 7px;
        padding: 10px 12px; border-radius: var(--radius-sm);
        font-size: 13px; font-weight: 600;
    }

    .stat-chip .material-icons { font-size: 16px; }
    .stat-chip.success-stat { background: var(--success-lt); color: var(--success); }
    .stat-chip.error-stat   { background: var(--danger-lt);  color: var(--danger); }

    /* Missing employees */
    .missing-banner {
        display: none; align-items: flex-start; gap: 10px;
        padding: 13px 15px; background: #fffbeb; border: 1.5px solid #fde68a;
        border-radius: var(--radius-sm); margin-top: 14px; font-size: 13px; color: #92400e;
    }

    .missing-banner.show { display: flex; }
    .missing-banner .material-icons { font-size: 18px; color: var(--warning); flex-shrink: 0; margin-top: 1px; }
    .missing-banner-body strong { display: block; margin-bottom: 8px; }

    /* Error list */
    .error-list-wrap {
        display: none; margin-top: 14px; background: var(--danger-lt);
        border: 1px solid #fecaca; border-radius: var(--radius-sm);
        padding: 14px 16px; max-height: 160px; overflow-y: auto;
    }

    .error-list-wrap.show { display: block; }
    .error-list-title { font-size: 13px; font-weight: 600; color: var(--danger); margin: 0 0 8px; display: flex; align-items: center; gap: 5px; }
    .error-list-title .material-icons { font-size: 15px; }
    .error-list { margin: 0; padding: 0 0 0 16px; font-size: 12.5px; color: #7f1d1d; }
    .error-list li { margin-bottom: 4px; }

    .modal-close-btn {
        display: none; margin-top: 20px; width: 100%; height: 40px;
        background: var(--surface); border: 1.5px solid var(--border);
        border-radius: var(--radius-sm); font-family: var(--font-body);
        font-size: 13.5px; font-weight: 600; color: var(--muted);
        cursor: pointer; transition: color .2s, border-color .2s;
    }

    .modal-close-btn:hover { color: var(--ink); border-color: #9ca3af; }

    /* Toast */
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

    @media (max-width: 640px) {
        .import-page { padding: 18px 14px; }
        .mode-options { grid-template-columns: 1fr; }
        .import-stats { flex-direction: column; }
    }
</style>

<div class="import-page">

    <div class="page-heading">
        <h1>Import Deductions</h1>
        <p>Upload an Excel file to bulk-import payroll deductions for the period.</p>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <div class="import-layout">

        <!-- Upload card -->
        <div class="import-card">
            <div class="card-top">
                <div class="card-icon">
                    <span class="material-icons">upload_file</span>
                </div>
                <div>
                    <p class="card-title">Upload File</p>
                    <p class="card-subtitle">Excel (.xlsx / .xls) · Max 10 MB</p>
                </div>
            </div>

            <div class="card-body">
                <form id="import-form" enctype="multipart/form-data">
                    @csrf

                    <div class="drop-zone" id="dropZone">
                        <input type="file" name="excelFile" id="excelFile" accept=".xlsx,.xls" required>
                        <div class="dz-icon"><span class="material-icons">cloud_upload</span></div>
                        <p class="dz-label">Drop your file here</p>
                        <p class="dz-hint">or click to browse · .xlsx / .xls</p>
                    </div>

                    <div class="file-pill" id="filePill">
                        <span class="material-icons">description</span>
                        <span id="fileName">file.xlsx</span>
                        <span class="material-icons remove-file" id="removeFile">close</span>
                    </div>

                    <div class="mode-section">
                        <span class="mode-label">Import Mode</span>
                        <div class="mode-options">
                            <div class="mode-chip">
                                <input type="radio" name="importMode" id="updateMode" value="update" checked>
                                <label for="updateMode">
                                    <div class="mode-chip-header">
                                        <span class="material-icons">sync</span> Update
                                    </div>
                                    <span class="mode-chip-desc">Updates existing records and adds new ones</span>
                                </label>
                            </div>
                            <div class="mode-chip">
                                <input type="radio" name="importMode" id="freshMode" value="fresh">
                                <label for="freshMode">
                                    <div class="mode-chip-header">
                                        <span class="material-icons">delete_sweep</span> Fresh Import
                                    </div>
                                    <span class="mode-chip-desc">Clears all existing records for the period first</span>
                                </label>
                            </div>
                        </div>
                        <div class="mode-warning" id="freshWarning">
                            <span class="material-icons">warning_amber</span>
                            <span><strong>Caution:</strong> This will permanently delete all existing transactions for the selected period before importing.</span>
                        </div>
                    </div>

                    <button type="submit" id="upload-btn" class="btn btn-import" disabled>
                        <span class="material-icons">upload</span>
                        <span>Import Deductions</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Preview card -->
        <div class="import-card">
            <div class="card-top">
                <div class="card-icon preview-icon">
                    <span class="material-icons">preview</span>
                </div>
                <div style="flex:1;">
                    <p class="card-title">
                        Preview
                        <span class="row-count" id="rowCount" style="display:none;">
                            <span class="material-icons" style="font-size:13px;">table_rows</span>
                            <span id="rowCountNum">0</span> rows
                        </span>
                    </p>
                    <p class="card-subtitle">Review data before importing</p>
                </div>
                <button class="btn btn-ghost" id="cancelUpload" style="display:none;">
                    <span class="material-icons">close</span> Clear
                </button>
            </div>
            <div style="padding:0;">
                <div class="empty-review" id="emptyReview">
                    <span class="material-icons">table_view</span>
                    <p>Select an Excel file to preview its contents here.</p>
                </div>
                <div id="tableContainer"></div>
            </div>
        </div>

    </div>
</div>

<!-- Progress modal -->
<div class="progress-modal-backdrop" id="progressModal">
    <div class="progress-modal-card">

        <div class="progress-modal-icon spinning" id="modalIcon">
            <span class="material-icons" id="modalIconGlyph">sync</span>
        </div>

        <h3 id="modalTitle">Importing Deductions…</h3>
        <p class="prog-sub" id="progressMessage">Preparing your file, please wait.</p>

        <div class="progress-track">
            <div class="progress-fill" id="progressFill"></div>
        </div>
        <span class="progress-pct" id="progressPct">0%</span>

        <!-- Live counts — shown as soon as streaming data arrives -->
        <div class="import-stats" id="importStats">
            <div class="stat-chip success-stat">
                <span class="material-icons">check_circle</span>
                <span id="statSuccess">0</span> processed
            </div>
            <div class="stat-chip error-stat">
                <span class="material-icons">error_outline</span>
                <span id="statErrors">0</span> errors
            </div>
        </div>

        <!-- Missing employees (shown only when server reports them) -->
        <div class="missing-banner" id="missingBanner">
            <span class="material-icons">group_off</span>
            <div class="missing-banner-body">
                <strong id="missingTitle">Missing employees found</strong>
                <a id="missingDownloadBtn" href="#" class="btn-warning-dl" download>
                    <span class="material-icons">download</span> Download Report
                </a>
            </div>
        </div>

        <!-- Error list -->
        <div class="error-list-wrap" id="errorListWrap">
            <p class="error-list-title">
                <span class="material-icons">error_outline</span> Errors encountered
            </p>
            <ul class="error-list" id="errorItems"></ul>
        </div>

        <button class="modal-close-btn" id="close-modal-btn">Close</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Refs ────────────────────────────────────────── */
    const form           = document.getElementById('import-form');
    const fileInput      = document.getElementById('excelFile');
    const uploadBtn      = document.getElementById('upload-btn');
    const dropZone       = document.getElementById('dropZone');
    const filePill       = document.getElementById('filePill');
    const fileNameEl     = document.getElementById('fileName');
    const removeFileBtn  = document.getElementById('removeFile');
    const cancelBtn      = document.getElementById('cancelUpload');
    const emptyReview    = document.getElementById('emptyReview');
    const tableContainer = document.getElementById('tableContainer');
    const rowCount       = document.getElementById('rowCount');
    const rowCountNum    = document.getElementById('rowCountNum');
    const freshWarning   = document.getElementById('freshWarning');

    const progressModal  = document.getElementById('progressModal');
    const progressFill   = document.getElementById('progressFill');
    const progressPct    = document.getElementById('progressPct');
    const progressMsg    = document.getElementById('progressMessage');
    const modalTitle     = document.getElementById('modalTitle');
    const modalIcon      = document.getElementById('modalIcon');
    const modalIconGlyph = document.getElementById('modalIconGlyph');
    const importStats    = document.getElementById('importStats');
    const statSuccess    = document.getElementById('statSuccess');
    const statErrors     = document.getElementById('statErrors');
    const missingBanner  = document.getElementById('missingBanner');
    const missingTitle   = document.getElementById('missingTitle');
    const missingDl      = document.getElementById('missingDownloadBtn');
    const errorListWrap  = document.getElementById('errorListWrap');
    const errorItems     = document.getElementById('errorItems');
    const closeModalBtn  = document.getElementById('close-modal-btn');

    /* ── Mode warning ────────────────────────────────── */
    document.querySelectorAll('input[name="importMode"]').forEach(r => {
        r.addEventListener('change', () =>
            freshWarning.classList.toggle('show', r.value === 'fresh' && r.checked)
        );
    });

    /* ── Drag & drop ─────────────────────────────────── */
    dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', ()  => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        if (e.dataTransfer.files.length) applyFile(e.dataTransfer.files[0]);
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) applyFile(fileInput.files[0]);
    });

    removeFileBtn.addEventListener('click', clearFile);
    cancelBtn.addEventListener('click', clearFile);

    function clearFile() {
        fileInput.value = '';
        filePill.classList.remove('show');
        uploadBtn.disabled = true;
        tableContainer.innerHTML = '';
        emptyReview.style.display = '';
        rowCount.style.display = 'none';
        cancelBtn.style.display = 'none';
    }

    function applyFile(file) {
        if (!file.name.match(/\.(xlsx|xls)$/i)) {
            showToast('danger', 'Invalid file', 'Please select an .xlsx or .xls file.');
            return;
        }
        fileNameEl.textContent = file.name;
        filePill.classList.add('show');
        uploadBtn.disabled = false;
        previewFile(file);
    }

    /* ── Preview (SheetJS — matches original: 10 rows) ── */
    function previewFile(file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            try {
                const data = new Uint8Array(e.target.result);
                const wb   = XLSX.read(data, { type: 'array' });
                const ws   = wb.Sheets[wb.SheetNames[0]];
                const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '' });
                if (!rows.length) return;

                emptyReview.style.display = 'none';
                cancelBtn.style.display   = '';
                rowCountNum.textContent   = rows.length - 1;
                rowCount.style.display    = '';

                const table = document.createElement('table');
                table.className = 'review-table';

                const thead = table.createTHead();
                const hRow  = thead.insertRow();
                rows[0].forEach(cell => {
                    const th = document.createElement('th');
                    th.textContent = cell;
                    hRow.appendChild(th);
                });

                const tbody = table.createTBody();
                // Original showed 10 rows; preserved here
                for (let i = 1; i < Math.min(rows.length, 11); i++) {
                    const tr = tbody.insertRow();
                    rows[i].forEach(cell => {
                        const td = tr.insertCell();
                        td.textContent = cell;
                    });
                }

                tableContainer.innerHTML = '';
                tableContainer.appendChild(table);

                if (rows.length > 11) {
                    const note = document.createElement('p');
                    note.style.cssText = 'font-size:12px;color:var(--muted);padding:10px 14px 14px;';
                    note.textContent = `Showing first 10 of ${rows.length - 1} rows.`;
                    tableContainer.appendChild(note);
                }
            } catch {
                showToast('danger', 'Parse error', 'Could not read the file. Please check the format.');
            }
        };
        reader.readAsArrayBuffer(file);
    }

    /* ── Modal helpers ───────────────────────────────── */
    function openModal() {
        setProgress(0, 'Preparing your file, please wait.');
        modalTitle.textContent     = 'Importing Deductions…';
        progressFill.className     = 'progress-fill';
        modalIcon.className        = 'progress-modal-icon spinning';
        modalIconGlyph.textContent = 'sync';
        importStats.classList.remove('show');
        missingBanner.classList.remove('show');
        errorListWrap.classList.remove('show');
        errorItems.innerHTML       = '';
        closeModalBtn.style.display = 'none';
        statSuccess.textContent    = '0';
        statErrors.textContent     = '0';
        progressModal.classList.add('open');
    }

    function setProgress(pct, msg) {
        progressFill.style.width = pct + '%';
        progressPct.textContent  = pct + '%';
        if (msg) progressMsg.textContent = msg;
    }

    function updateLiveCounts(successCount, errorCount) {
        if (successCount !== undefined || errorCount !== undefined) {
            importStats.classList.add('show');
            if (successCount !== undefined) statSuccess.textContent = successCount;
            if (errorCount   !== undefined) statErrors.textContent  = errorCount;
        }
    }

    function handleFinalResponse(response) {
        const isSuccess = response.status === 'completed' || response.status === 'success';

        setProgress(100, response.message || (isSuccess ? 'Import complete!' : 'Import failed.'));

        if (isSuccess) {
            modalTitle.textContent     = 'Import Complete!';
            modalIcon.className        = 'progress-modal-icon done-success';
            modalIconGlyph.textContent = 'check_circle';
            progressFill.classList.add('fill-success');

            updateLiveCounts(response.success || 0, response.errors || 0);

            if (response.hasMissingEmployees || response.downloadUrl) {
                missingTitle.textContent = `${response.missingEmployeesCount} employee(s) not found in the database.`;
                missingDl.href = response.downloadUrl;
                missingBanner.classList.add('show');
                showToast('warning', 'Missing Agents', `${response.missingEmployeesCount} employee(s) were not matched.`);
            }

            showToast('success', 'Import complete!', response.message || `${response.success || 0} records processed.`);

            // Auto-close: 2.5s if clean, 8s if issues to review (matches original logic)
            const hasIssues = (response.errors > 0) || response.hasMissingEmployees;
            setTimeout(() => closeModal(), hasIssues ? 800000 : 2500);

        } else {
            modalTitle.textContent     = 'Import Failed';
            modalIcon.className        = 'progress-modal-icon done-error';
            modalIconGlyph.textContent = 'error';
            progressFill.classList.add('fill-danger');
            showToast('danger', 'Import failed', response.message || 'Something went wrong.');
            setTimeout(() => closeModal(), 5000); // matches original 5s
        }

        closeModalBtn.style.display = 'block';
        uploadBtn.disabled = false;
    }

    function closeModal() {
        progressModal.classList.remove('open');
        form.reset();
        clearFile();
    }

    closeModalBtn.addEventListener('click', closeModal);

    /* ── Form submit — streaming XHR ─────────────────── */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!fileInput.files.length) {
            showToast('danger', 'No file', 'Please select a file first.');
            return;
        }

        const formData = new FormData();
        formData.append('excelFile', fileInput.files[0]);
        formData.append('importMode', document.querySelector('input[name="importMode"]:checked').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        uploadBtn.disabled = true;
        openModal();

        const xhr    = new XMLHttpRequest();
        let isProcessing = false;
        let buffer   = '';

        /* Upload progress → 0–60% */
        xhr.upload.addEventListener('progress', evt => {
            if (evt.lengthComputable && !isProcessing) {
                const pct = Math.round(evt.loaded / evt.total * 60);
                setProgress(pct, `Uploading file: ${pct}%`);
            }
        });

        /* Streaming NDJSON lines from server */
        xhr.onprogress = () => {
            isProcessing = true;
            buffer += xhr.responseText.substring(buffer.length);

            const lines = buffer.split('\n');
            buffer = lines.pop(); // hold incomplete line

            lines.forEach(line => {
                if (!line.trim()) return;
                try {
                    const res = JSON.parse(line);

                    if (res.status === 'progress') {
                        setProgress(res.progress, res.message);
                        // Live count display (matches original logic)
                        if (res.success !== undefined || res.errors !== undefined) {
                            updateLiveCounts(res.success, res.errors);
                            progressMsg.textContent = res.message + ` (✓ ${res.success ?? 0} success, ✗ ${res.errors ?? 0} errors)`;
                        }
                    }

                    if (res.status === 'completed' || res.status === 'success') {
                        handleFinalResponse(res);
                    }
                } catch {
                    // Ignore partial/non-JSON lines
                }
            });
        };

        /* Flush remaining buffer on complete */
        xhr.onload = () => {
            if (buffer.trim()) {
                try {
                    handleFinalResponse(JSON.parse(buffer));
                } catch {
                    handleFinalResponse({ status: 'error', message: 'Failed to parse server response.' });
                }
            }
        };

        xhr.onerror = () => {
            setProgress(100, 'Upload failed. Please try again.');
            progressFill.classList.add('fill-danger');
            modalIcon.className        = 'progress-modal-icon done-error';
            modalIconGlyph.textContent = 'error';
            closeModalBtn.style.display = 'block';
            uploadBtn.disabled = false;
            showToast('danger', 'Upload error', 'Connection failed. Please try again.');
        };

        xhr.open('POST', '{{ route("deductions.import.process") }}', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    });

    /* ── Toast ───────────────────────────────────────── */
    function showToast(type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success: 'check_circle', danger: 'error_outline', warning: 'warning_amber' };
        const t = document.createElement('div');
        t.className = `toast-msg ${type}`;
        t.innerHTML = `<span class="material-icons">${icons[type] || 'info'}</span>
                       <div><strong>${title}</strong> ${message}</div>`;
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 6000);
    }

});
</script>

</x-custom-admin-layout>