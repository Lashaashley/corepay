// resources/js/reports.js

export function initializeStaffReport() {
    $(document).on('click', '#openFullReport', function (e) {
        e.preventDefault();
        var branch = "Full Staff Report";
        var actionTaken = false;
        
        // Reset modal content before loading
        $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
        $('#staffreportModal').modal('show');
        
        $.ajax({
            url: window.routes.fullStaffReport, // ✅ Use window.routes object
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            success: function (response) {
                if (response.success && response.pdf) {
                    var pdfBlob = new Blob(
                        [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                        { type: 'application/pdf' }
                    );
                    var pdfUrl = URL.createObjectURL(pdfBlob);
                    
                    // Log that the user "opened" the report
                    if (typeof logaudit === 'function') {
                        logaudit(branch, 'OPEN', 'Full Staff Report');
                    }
                    
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
                        if (!actionTaken && typeof logaudit === 'function') {
                            actionTaken = true;
                            logaudit(branch, 'PRINT', 'Full Staff Report');
                        }
                    });
                    
                    // DOWNLOAD button handler
                    $('#downloadPdfBtn').on('click', function () {
                        var link = document.createElement('a');
                        link.href = pdfUrl;
                        link.download = 'Staff_Report_' + new Date().toISOString().split('T')[0] + '.pdf';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        if (!actionTaken && typeof logaudit === 'function') {
                            actionTaken = true;
                            logaudit(branch, 'DOWNLOAD', 'Combined Staff Report');
                        }
                    });
                } else {
                    $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Failed to generate PDF.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
                console.error("Response:", xhr.responseText);
                $('#staffrpt-pdf-container').html(
                    '<p class="text-danger text-center mt-3">Error fetching report: ' + 
                    (xhr.responseJSON?.message || error) + 
                    '</p>'
                );
            }
        });
    });
}

// Auto-initialize if jQuery is loaded
if (typeof jQuery !== 'undefined') {
    $(document).ready(function() {
        initializeStaffReport();
    });
}