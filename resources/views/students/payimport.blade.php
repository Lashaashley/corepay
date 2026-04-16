<x-custom-admin-layout>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

 @vite(['resources/css/pages/payimport.css']) 

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
                <div class="flex1">
                    <p class="card-title">
                        Preview
                        <span class="row-count hidden" id="rowCount">
                            <span class="material-icons font13">table_rows</span>
                            <span id="rowCountNum">0</span> rows
                        </span>
                    </p>
                    <p class="card-subtitle">Review data before importing</p>
                </div>
                <button class="btn btn-ghost" id="cancelUpload">
                    <span class="material-icons">close</span> Clear
                </button>
            </div>
            <div class="padding0">
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

<script nonce="{{ $cspNonce }}">
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