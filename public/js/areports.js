    $(document).ready(function() {
            $('#openFullReport').on('click', function (e) {
                e.preventDefault();
                
                var branch = "Full Staff Report";
                var actionTaken = false;
                
                // Reset modal content
                $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
                $('#staffreportModal').modal('show');
                
                $.ajax({
                    url: amanage, // ✅ This is the correct format
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function (response) {
                        if (response.success && response.pdf) {
                            var pdfBlob = new Blob(
                                [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                                { type: 'application/pdf' }
                            );
                            var pdfUrl = URL.createObjectURL(pdfBlob);
                            
                            var pdfViewerHTML = `
                                <div class="pdf-viewer-wrapper">
                                    <div class="pdf-actions mb-3 text-center">
                                        <button id="downloadPdfBtn" class="btn btn-primary mr-2">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                        <button id="printPdfBtn" class="btn btn-secondary">
                                            <i class="fa fa-print"></i> Print
                                        </button>
                                    </div>
                                    <iframe 
                                        id="staffrptPdfFrame" 
                                        src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                                        width="100%" 
                                        height="600px" 
                                        style="border:1px solid #ddd;"
                                    ></iframe>
                                </div>`;
                                
                            $('#staffrpt-pdf-container').html(pdfViewerHTML);
                            
                            // Print button
                            $('#printPdfBtn').on('click', function () {
                                var iframe = document.getElementById('staffrptPdfFrame');
                                iframe.contentWindow.focus();
                                iframe.contentWindow.print();
                            });
                            
                            // Download button
                            $('#downloadPdfBtn').on('click', function () {
                                var link = document.createElement('a');
                                link.href = pdfUrl;
                                link.download = 'Staff_Report_' + new Date().toISOString().split('T')[0] + '.pdf';
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            });
                        } else {
                            $('#staffrpt-pdf-container').html(
                                '<p class="text-danger text-center mt-3">Failed to generate PDF.</p>'
                            );
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", error);
                        console.error("Response:", xhr.responseText);
                        
                        var errorMsg = 'Error fetching report.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        $('#staffrpt-pdf-container').html(
                            '<p class="text-danger text-center mt-3">' + errorMsg + '</p>'
                        );
                    }
                });
            });
        });