<x-custom-admin-layout>

@vite(['resources/css/pages/areport.css'])
 
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
            <button class="btn btn-primary-report" id="openFullReport" hidden>
                <span class="material-icons">picture_as_pdf</span>
                Generate Report
            </button>

            <button class="btn btn-primary-report" id="wopenFullReport">
                <span class="material-icons">picture_as_pdf</span>
                Generate Report
            </button>
        </div>
 
        {{-- Additional report cards can be uncommented / added here following the same pattern --}}
        {{--
        
        --}}
 
    </div>
</div>

<!-- Full Staff Report PDF Preview Modal -->
<div id="staffReportModal" class="pdf-modal-overlay">
    <div class="pdf-modal-container">
        <div class="pdf-modal-header">
            <h3>Full Staff Report Preview</h3>
            <div class="pdf-modal-actions">
                <button id="downloadStaffReport" class="btn btn-primary btn-sm">
                    <span class="material-icons">download</span>
                </button>
                <button id="closeStaffModal" class="btn btn-secondary btn-sm">
                   <span class="material-icons">close</span>
                </button>
            </div>
        </div>

        <div id="staffPdfLoading">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Generating report...</span>
            </div>
            <span class="ml-2">Generating Report...</span>
        </div>

        <div id="staffPdfContainer"></div>
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
                <button class="btn-download-pdf hidden" id="downloadPdfBtn">
                    <span class="material-icons">download</span>
                    <span>Download</span>
                </button>
                <button class="btn-print-pdf hidden" id="printPdfBtn">
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
 
    
    @vite(['resources/js/areports.js'])
</x-custom-admin-layout>