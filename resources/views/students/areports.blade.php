<x-custom-admin-layout>
   <style>
       .filter-section {
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    .filter-section:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .select2 {
        width: 100% !important;
    }
    .btn {
        transition: all 0.3s;
         white-space: nowrap;
    }
    .form-group {
        margin-bottom: 1rem;
    }
/* --- Base Enhanced Button --- */
.btn-enhanced {
    padding: 8px 18px;
    border-radius: 6px;
    font-weight: 500;
    border: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    cursor: pointer;
    min-width: 100px;
    color: #fff;
}

/* Hover lift and shadow effect */
.btn-enhanced:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    text-decoration: none;
}

/* --- Download Button (Success Gradient) --- */
.btn-download {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}
.btn-download:hover {
    background: linear-gradient(135deg, #218838 0%, #17a589 100%);
}

/* --- Print Button (Info Gradient) --- */
.btn-print {
    background: linear-gradient(135deg, #007bff 0%, #00b4d8 100%);
}
.btn-print:hover {
    background: linear-gradient(135deg, #0069d9 0%, #0096c7 100%);
}
.btn-draft { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-draft:hover { background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%); color: white; }
/* Optional: Add subtle active click effect */
.btn-enhanced:active {
    transform: scale(0.98);
    box-shadow: none;
}

        #staffrpt-pdf-container iframe {
    width: 100%;
    height: 80vh;
    border: none;
}

.modal-xl {
    max-width: 90%;
}

.modal-body {
    padding: 0;
}
.custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 9999;
            transform: translateX(400px);
            transition: all 0.5s ease;
        }
        
        .custom-alert.show {
            transform: translateX(0);
        }
        
        .alert-success {
            animation: successPulse 1s ease-in-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(0.95); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
    </style>
    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px" >
        <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="card-box pd-20 mb-30" style="border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="text-blue h4">Agents Reports Query</h4>
                </div>
                <div class="filter-section mb-2 p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #1b00ff;">
                    <h5 class="mb-2 text-primary">Departmental & Payroll Type Reports</h5>
                    <div class="form-row align-items-end">
                        <form class="d-flex flex-wrap align-items-end">
                            <div class="form-group mr-2">
                                <label for="Department" class="font-weight-bold mb-1">Department:</label>
                                <select name="Department" id="Department" class="form-control select2" required onchange="fetchroles()" style="min-width: 180px;">
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <label for="role" class="font-weight-bold mb-1">Sections:</label>
                                <select name="role" id="role" class="form-control select2"required style="min-width: 180px;">
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <button class="btn btn-enhanced btn-draft" type="button" id="load">
                                    <i class="fa fa-filter"></i> Filter
                                </button>
                            </div>
                            <div class="form-group mr-2">
                                <label for="proltype" class="font-weight-bold mb-1">Payroll Type:</label>
                                <select name="proltype" id="proltype" class="form-control select2" style="min-width: 180px;">
                                    <option value="">Select Agent Type</option>
                                </select>
                            </div>
                        </form>
                        <div class="form-group">
                            <button class="btn btn-enhanced btn-draft" id="prolsums">
                                <i class="fa fa-table"></i> View Agents
                            </button>
                        </div>
                    </div>
                </div>
                <div class="filter-section p-4" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #00b894;">
                    <h5 class="mb-1 text-primary">Comprehensive Reports</h5>
                    
                        <div class="row align-items-end">
                            <div class="col-md-3 col-sm-12" hidden>
                                <div class="form-group">
                                    <label class="font-weight-bold">Branch:</label>
                                    <select name="branch" id="branch" class="form-control select2">
                                        <option value="">Select Branch</option>
                                        <option value="0">Overall</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-12">
                                <div class="form-group d-flex align-items-end">
                                    <button class="btn btn-enhanced btn-draft" id="combined" hidden>
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-outline-primary" name="flist" id="openFullReport" style="min-width: 120px;">
                                        <i class="fa fa-list"></i> Full List
                                    </button>
                                </div>
                            </div>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="staffreportModal" tabindex="-1" role="dialog" aria-labelledby="staffreportModalLabel" aria-hidden="true"> <div class="modal-dialog modal-lg" role="document"> <div class="modal-content"> <div class="modal-header"> <h5 class="modal-title">Report Viewer</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> </div> <div class="modal-body"> <div id="staffrpt-pdf-container" style="height: 600px; overflow: hidden;"> <p class="text-center">Loading report...</p> </div> </div> </div> </div> </div>
    

    
    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            $('#openFullReport').on('click', function (e) {
                e.preventDefault();
                
                var branch = "Full Staff Report";
                var actionTaken = false;
                
                // Reset modal content
                $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
                $('#staffreportModal').modal('show');
                
                $.ajax({
                    url: '{{ route("reports.full-staff") }}', // ✅ This is the correct format
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
    </script>
    
   
</x-custom-admin-layout>