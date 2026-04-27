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

    async function getExcelJS() {
    if (window.ExcelJS) return window.ExcelJS;  // already loaded
    const { default: ExcelJS } = await import('exceljs');
    window.ExcelJS = ExcelJS;
    return ExcelJS;
}
    

    /* ── Preview (SheetJS — matches original: 10 rows) ── */
   async function previewFile(file) {
    if (file.name.match(/\.xls$/i)) {
        showToast('danger', 'Unsupported format', 'Please save the file as .xlsx and try again.');
        return;
    }
    try {
        const ExcelJS  = await getExcelJS(); 
        const workbook  = new ExcelJS.Workbook();
        const buffer    = await file.arrayBuffer();
        await workbook.xlsx.load(buffer);

        const worksheet = workbook.worksheets[0];

         if (!worksheet) {
            showToast('danger', 'Empty file', 'No sheets found in this workbook.');
            return;
        }

        // Build 2D array — same as XLSX.utils.sheet_to_json(ws, { header: 1 })
        const rows = [];
        worksheet.eachRow({ includeEmpty: true }, function(row) {
            rows.push(row.values.slice(1)); // slice(1) drops ExcelJS's undefined index 0
        });

        if (!rows.length) return;

        emptyReview.style.display = 'none';
        cancelBtn.style.display   = '';
        rowCountNum.textContent   = rows.length - 1;
        rowCount.style.display    = '';

        const table = document.createElement('table');
        table.className = 'review-table';

        // Header row
        const thead = table.createTHead();
        const hRow  = thead.insertRow();
        rows[0].forEach(cell => {
            const th = document.createElement('th');
            th.textContent = cellValue(cell);
            hRow.appendChild(th);
        });

        // Data rows — first 10 only
        const tbody = table.createTBody();
        for (let i = 1; i < Math.min(rows.length, 11); i++) {
            const tr = tbody.insertRow();
            rows[i].forEach(cell => {
                const td = tr.insertCell();
                td.textContent = cellValue(cell);
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

    } catch (err) {
        console.error(err);
        showToast('danger', 'Parse error', 'Could not read the file. Please check the format.');
    }
}

// Normalize ExcelJS cell values (dates, formulas, rich text)
function cellValue(cell) {
    if (cell === null || cell === undefined) return '';
    if (typeof cell === 'object') {
        if (cell instanceof Date)          return cell.toLocaleDateString();
        if (cell.result !== undefined)     return cell.result;      // formula
        if (cell.richText)                 return cell.richText.map(r => r.text).join(''); // rich text
        return '';
    }
    return cell;
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

        xhr.open('POST', App.routes.payimport, true);
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