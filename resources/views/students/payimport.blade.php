<x-custom-admin-layout>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>


 @vite(['resources/css/pages/payimport.css']) 

<div class="import-page">

    <div class="page-heading">
        <h1>Import Payroll Data</h1>
        <p>Upload an Excel file to bulk-import payroll data for the period.</p>
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



@vite(['resources/js/payimport.js'])

</x-custom-admin-layout>