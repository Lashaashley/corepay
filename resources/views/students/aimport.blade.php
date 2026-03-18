<x-custom-admin-layout>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<!-- Use the correct paired CSS + JS versions -->


<style>
    /* ── Page-specific styles only — tokens come from corepay.css ── */


    /* ── Buttons ─────────────────────────────────────────────── */
    .btn {
        height: 42px;
        padding: 0 20px;
        border: none;
        border-radius: var(--radius-sm);
        font-family: var(--font-body);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: transform .2s, box-shadow .2s, filter .2s;
        letter-spacing: .01em;
        text-decoration: none;
    }

    .btn .material-icons { font-size: 17px; }
    .btn:hover { transform: translateY(-1px); }
    .btn:active { transform: translateY(0); }

    .btn-upload {
        width: 100%;
        justify-content: center;
        background: linear-gradient(135deg, #1a56db, #4f46e5);
        color: #fff;
        box-shadow: 0 4px 14px rgba(26,86,219,.28);
    }

    .btn-upload:hover { box-shadow: 0 7px 20px rgba(26,86,219,.38); filter: brightness(1.05); }
    .btn-upload:disabled { opacity: .55; cursor: not-allowed; transform: none; }

    .btn-outline {
        background: var(--surface);
        color: var(--muted);
        border: 1.5px solid var(--border);
        height: 38px;
        padding: 0 16px;
    }

    .btn-outline:hover { color: var(--ink); border-color: #9ca3af; }

   

 
</style>

<div class="import-page">

    <!-- Page heading -->
    <div class="page-heading">
        <h1>Import Agents</h1>
        <p>Upload an Excel file to bulk-import agent records into the system.</p>
    </div>

    <!-- Toast -->
    <div class="toast-wrap" id="toastWrap"></div>

    <!-- Two-column layout -->
    <div class="import-layout">

        <!-- ── Left: Upload card ────────────────────────────── -->
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

                <!-- Template download -->
                <a href="{{ route('import.template') }}" class="template-banner">
                    <span class="material-icons">table_chart</span>
                    <div class="template-banner-text">
                        <strong>Download Template</strong>
                        <span>Get the correct column structure</span>
                    </div>
                    <span class="material-icons" style="margin-left:auto;font-size:18px;opacity:.6;">download</span>
                </a>

                <form id="importForm" enctype="multipart/form-data">
                    @csrf

                    <!-- Drop zone -->
                    <div class="drop-zone" id="dropZone">
                        <input type="file" name="excelFile" id="excelFile"
                               accept=".xlsx,.xls" required>
                        <div class="dz-icon">
                            <span class="material-icons">cloud_upload</span>
                        </div>
                        <p class="dz-label">Drop your file here</p>
                        <p class="dz-hint">or click to browse</p>
                    </div>

                    <!-- Selected file pill -->
                    <div class="file-pill" id="filePill">
                        <span class="material-icons">description</span>
                        <span id="fileName">file.xlsx</span>
                        <span class="material-icons remove-file" id="removeFile">close</span>
                    </div>

                    <button type="submit" class="btn btn-upload" id="uploadBtn" disabled>
                        <span class="material-icons">upload</span>
                        <span id="uploadBtnLabel">Upload and Import</span>
                    </button>

                    <div id="resultMsg" class="result-msg" style="display:none;">
                        <div class="result-header">
                            <span class="material-icons result-icon"></span>
                            <span class="result-title"></span>
                        </div>
                        <div class="result-details"></div>
                        <div class="result-actions"></div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Right: Review card ───────────────────────────── -->
        <div class="import-card">
            <div class="card-top">
                <div class="card-icon success-icon">
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
                    <p class="card-subtitle">Review data before it is imported</p>
                </div>
                <button class="btn btn-outline" id="cancelUpload" style="display:none;">
                    <span class="material-icons">close</span> Clear
                </button>
            </div>

            <div class="card-body" style="padding: 0;">

                <!-- Empty state -->
                <div class="empty-review" id="emptyReview">
                    <span class="material-icons">table_view</span>
                    <p>Select an Excel file to preview its contents here.</p>
                </div>

                <!-- Table -->
                <div id="tableContainer"></div>

            </div>
        </div>

    </div><!-- /import-layout -->
</div><!-- /import-page -->

<!-- Progress modal -->
<div class="progress-modal-backdrop" id="progressModal">
    <div class="progress-modal-card">
        <div class="progress-modal-icon">
            <span class="material-icons">sync</span>
        </div>
        <h3>Importing Data…</h3>
        <p id="progressMessage">Preparing your file, please wait.</p>
        <div class="progress-track">
            <div class="progress-fill" id="progressFill"></div>
        </div>
        <span class="progress-pct" id="progressPct">0%</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {



    const duplicateReportUrl = "{{ route('import.duplicate.report') }}";
    const dropZone   = document.getElementById('dropZone');
    const fileInput  = document.getElementById('excelFile');
    const filePill   = document.getElementById('filePill');
    const fileName   = document.getElementById('fileName');
    const removeFile = document.getElementById('removeFile');
    const uploadBtn  = document.getElementById('uploadBtn');
    const cancelBtn  = document.getElementById('cancelUpload');
    const rowCount   = document.getElementById('rowCount');
    const rowCountNum= document.getElementById('rowCountNum');
    const emptyReview= document.getElementById('emptyReview');
    const tableContainer = document.getElementById('tableContainer');
    const resultMsg  = document.getElementById('resultMsg');

    /* ── Drag & drop visual ──────────────────────────── */
    dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) handleFile(fileInput.files[0]);
    });

    /* ── Remove file ─────────────────────────────────── */
    removeFile.addEventListener('click', clearFile);

    cancelBtn.addEventListener('click', clearFile);

    function clearFile() {
        fileInput.value = '';
        filePill.classList.remove('show');
        uploadBtn.disabled = true;
        tableContainer.innerHTML = '';
        emptyReview.style.display = '';
        rowCount.style.display = 'none';
        cancelBtn.style.display = 'none';
        resultMsg.style.display = 'none';
    }

    /* ── Handle selected file ────────────────────────── */
    function handleFile(file) {
        if (!file.name.match(/\.(xlsx|xls)$/i)) {
            showToast('danger', 'Invalid file', 'Please select an .xlsx or .xls file.');
            return;
        }

        fileName.textContent = file.name;
        filePill.classList.add('show');
        uploadBtn.disabled = false;

        // Preview via SheetJS
        const reader = new FileReader();
        reader.onload = function (e) {
            try {
                const wb   = XLSX.read(e.target.result, { type: 'binary' });
                const ws   = wb.Sheets[wb.SheetNames[0]];
                const data = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '' });

                if (!data.length) {
                    emptyReview.style.display = '';
                    tableContainer.innerHTML = '';
                    return;
                }

                emptyReview.style.display = 'none';
                cancelBtn.style.display = '';
                rowCountNum.textContent = data.length - 1;
                rowCount.style.display = '';

                let html = '<table class="review-table"><thead><tr>';
                data[0].forEach(h => html += `<th>${h}</th>`);
                html += '</tr></thead><tbody>';
                data.slice(1, 51).forEach(row => {          // preview max 50 rows
                    html += '<tr>';
                    row.forEach(cell => html += `<td>${cell}</td>`);
                    html += '</tr>';
                });
                html += '</tbody></table>';
                tableContainer.innerHTML = html;

                if (data.length > 51) {
                    tableContainer.innerHTML += `<p style="font-size:12px;color:var(--muted);padding:10px 14px 14px;">
                        Showing first 50 of ${data.length - 1} rows.</p>`;
                }
            } catch (err) {
                showToast('danger', 'Parse error', 'Could not read the file. Please check the format.');
            }
        };
        reader.readAsBinaryString(file);
    }

    /* ── Form submit ─────────────────────────────────── */
    document.getElementById('importForm').addEventListener('submit', function (e) {

    e.preventDefault();

    const formData = new FormData(this);
    openProgress('Uploading file…');

    const xhr = new XMLHttpRequest();

    /* Upload Progress (0–30%) */
    xhr.upload.onprogress = function (evt) {
        if (evt.lengthComputable) {
            const percent = Math.round((evt.loaded / evt.total) * 30);
            updateProgress(percent, 'Uploading file…');
        }
    };

    /* Processing Progress (30–95%) */
    xhr.onprogress = function () {

        const lines = xhr.responseText
            .split('\n')
            .map(l => l.trim())
            .filter(l => l.length > 0);

        try {

            const last = JSON.parse(lines[lines.length - 1]);

            if (last.status === 'progress') {

                const mappedProgress = 30 + Math.round((last.progress / 100) * 65);

                updateProgress(
                    mappedProgress,
                    `${last.message} (${last.success} saved, ${last.errors} errors)`
                );

            }

        } catch (err) {}

    };

    xhr.onreadystatechange = function () {

        if (xhr.readyState !== 4) return;

        updateProgress(100, 'Finalizing…');

        try {

            const lines = xhr.responseText
                .split('\n')
                .map(l => l.trim())
                .filter(l => l.length > 0);

            const res = JSON.parse(lines[lines.length - 1]);

            if (xhr.status >= 200 && xhr.status < 300 && res.status === 'success') {

                if (res.has_duplicate_report) {

                    showToast('warning', 'Import Complete', res.message);

                    document.getElementById('progressMessage').textContent = res.message;
                    document.getElementById('progressFill').style.width = '100%';
                    document.getElementById('progressPct').textContent = '100%';

                    document.querySelector('.progress-modal-icon .material-icons').textContent = 'warning_amber';
                    document.querySelector('#progressModal h3').textContent = 'Import Complete';

                    const modalCard = document.querySelector('.progress-modal-card');

                    modalCard.querySelectorAll('.modal-download-btn').forEach(el => el.remove());

                    const dlBtn = document.createElement('a');
                    dlBtn.href = duplicateReportUrl;
                    dlBtn.download = true;
                    dlBtn.className = 'btn btn-upload';
                    dlBtn.style.cssText = 'margin-top:16px;height:38px;font-size:13px;justify-content:center;';
                    dlBtn.innerHTML = '<span class="material-icons">download</span> Download Exception Report';

                    modalCard.appendChild(dlBtn);

                    const closeBtn = document.createElement('button');

                    closeBtn.className = 'btn btn-outline modal-download-btn';
                    closeBtn.style.cssText = 'margin-top:8px;height:38px;font-size:13px;justify-content:center;color:var(--muted);';

                    closeBtn.innerHTML = '<span class="material-icons">close</span> Close';

                    closeBtn.onclick = () => {

                        closeProgress();

                        modalCard.querySelectorAll('.modal-download-btn').forEach(el => el.remove());

                        document.querySelector('.progress-modal-icon .material-icons').textContent = 'sync';
                        document.querySelector('#progressModal h3').textContent = 'Importing Data…';

                    };

                    modalCard.appendChild(closeBtn);

                } else {

                    showToast('success', 'Imported!', res.message || 'Agents imported successfully.');

                    showResult(
                        'success',
                        `${res.message} — ${res.success} saved, ${res.errors} skipped.`
                    );

                    setTimeout(closeProgress, 600);

                }

                clearFile();

            } else {

                showToast('danger', 'Import failed', res.message || 'Something went wrong.');
                showResult('danger', res.message || 'Import failed.');
                closeProgress();

            }

        } catch (err) {

            console.error('Parse error:', err, xhr.responseText);
            showToast('danger', 'Error', 'Could not read server response.');
            closeProgress();

        }

    };

    xhr.open('POST', '{{ route("import.employees.upload") }}');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send(formData);

});

    /* ── Progress helpers ────────────────────────────── */
    function openProgress(msg) {
        document.getElementById('progressMessage').textContent = msg;
        document.getElementById('progressModal').classList.add('open');
        updateProgress(5, msg);
    }

    function updateProgress(pct, msg) {
        document.getElementById('progressFill').style.width = pct + '%';
        document.getElementById('progressPct').textContent  = pct + '%';
        if (msg) document.getElementById('progressMessage').textContent = msg;
    }

    function closeProgress() {
        document.getElementById('progressModal').classList.remove('open');
        updateProgress(0, '');
    }

    /* ── Inline result ───────────────────────────────── */
    function showResult(type, msg) {
    const icon = type === 'success' ? 'check_circle' : 
                 type === 'warning' ? 'warning_amber' : 'error_outline';
    resultMsg.className = `result-msg ${type}`;
    resultMsg.innerHTML = `<span class="material-icons">${icon}</span>${msg}`; // ← must be innerHTML
    resultMsg.style.display = 'block';
}

    /* ── Toast ───────────────────────────────────────── */
    function showToast(type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success: 'check_circle', danger: 'error_outline', warning: 'warning_amber' };
        const t = document.createElement('div');
        t.className = `toast-msg ${type}`;
        t.innerHTML = `<span class="material-icons">${icons[type]}</span>
                       <div><strong>${title}</strong> ${message}</div>`;
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }

});
</script>

</x-custom-admin-layout>