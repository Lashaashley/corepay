<x-custom-admin-layout>
    <style>
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
        
.action-buttons {
            padding: 1px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-enhanced {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-draft {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1);
            color: white;
        }
        
        .btn-finalize {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
         .btn-cancel {
            background: linear-gradient(135deg, #e93a04ff, #d62f05ff);
            color: white;
        }  

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
.modal-xl {
    max-width: 90%;
}

.modal-body {
    padding: 0;
}
.custom-toggle-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
}

.custom-toggle-wrapper input {
    display: none;
}

.toggle-switch {
    width: 55px;
    height: 28px;
    background: #dc3545;
    border-radius: 30px;
    position: relative;
    cursor: pointer;
    transition: 0.3s;
}

.toggle-switch::before {
    content: "";
    width: 22px;
    height: 22px;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 4px;
    transition: 0.3s;
}

.custom-toggle-wrapper input:checked + .toggle-switch {
    background: #28a745;
}

.custom-toggle-wrapper input:checked + .toggle-switch::before {
    transform: translateX(26px);
}

.toggle-text {
    font-weight: 600;
    font-size: 14px;
}

    </style>
    
    <!-- Make sure CSS is loaded before content -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <div class="mobile-menu-overlay"></div>
    <div class="pd-ltr-20" style="margin-top: -20px;">
       

        <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">Approve Earnings</h5>
                    <form id="forecastForm">
                        <div class="row align-items-center">
                            <div class="col-md-12 user-icon">
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <label id="periodLabel">Earnings</label>
                                        <select name="pname" id="pname" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Item</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                            <label>Agent List:</label>
                                            <select name="staffSelect3" id="staffSelect3" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Agent</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Agent List:</label>
                                            <select name="staffSelect4" id="staffSelect4" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Agent</option>
                                                
                                            </select>
                                        </div>
                                        
                                    <div class="col-md-2">
                                        <button class="btn btn-enhanced btn-draft" id="openitems">
                                                <i class="fas fas fa-table"></i> Review
                                            </button>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            <div class="card-box pd-20 height-100-p mb-30">
                <div class="row align-items-center">
                    <div class="col-md-12 user-icon">
                        <div class="col-md-4 pr-md-2">
                            <div class="border p-2">
                                <legend class="small mb-1">Current Payroll Period</legend>
                                <div class="row no-gutters">
                                    <div class="col-md-6 pr-md-1">
                                        <div class="form-group mb-1">
                                            <label for="currentMonth" class="small-label mb-0">Current Month</label>
                                            <input type="text" class="form-control form-control-sm" id="currentMonth" value="{{ $month }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-1">
                                        <div class="form-group mb-1">
                                            <label for="currentYear" class="small-label mb-0">Current Year</label> 
                                            <input type="text" class="form-control form-control-sm" id="currentYear" value="{{ $year }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12 load-button-container">
                                        
                                    <button id="approve" type="submit" class="btn btn-enhanced btn-finalize">
                                    <i class="fas fa-check-double"></i> Approve Earnings
                                </button></div>
                                </div>
                            </div>

                        </div>
                        
                    </div>
                    
                    

                </div>
            </div>
            <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
    <h5 class="text-center mb-4">Approve NetPay</h5>

    <form id="forecastForm">
        <div class="row align-items-end">

            <!-- Staff From -->
            <div class="col-md-2">
                <label>Agent From:</label>
                <select name="staffSelect5" id="staffSelect5" class="custom-select form-control" autocomplete="off">
                    <option value="">Select Agent</option>
                </select>
            </div>

            <!-- Staff To -->
            <div class="col-md-2">
                <label>Agent To:</label>
                <select name="staffSelect6" id="staffSelect6" class="custom-select form-control" autocomplete="off">
                    <option value="">Select Agent</option>
                </select>
            </div>

            <!-- Review Button -->
            <div class="col-md-2">
                <label style="visibility:hidden;">Review</label>
                <button class="btn btn-enhanced btn-draft btn-block" id="openitems2">
                    <i class="fas fa-table"></i> Review
                </button>
            </div>

            <!-- Toggle -->
            <div class="col-md-2">
                <label>Approval:</label>
                <div class="custom-toggle-wrapper">
                    <input type="checkbox" id="approvalToggle" checked>
                    <label for="approvalToggle" class="toggle-switch"></label>
                    <span id="toggleText" class="toggle-text">Approve</span>
                </div>
            </div>

            <!-- Process Button -->
            <div class="col-md-2">
    <label style="visibility:hidden;">Action</label>

    <!-- Approve Button -->
    <button type="submit" id="approveBtn" class="btn btn-enhanced btn-finalize btn-block">
        <i class="fas fa-check-double"></i> Approve
    </button>

    <!-- Reject Button -->
    <button type="submit" id="rejectBtn" class="btn btn-enhanced btn-cancel btn-block" style="display:none;">
        <i class="fas fa-window-close"></i> Reject
    </button>
</div>


        </div>

        <!-- Feedback Section -->
        <div class="row mt-3" id="feedbackSection" style="display:none;">
            <div class="col-md-12">
                <label>Feedback (Reason for Decline)</label>
                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"></textarea>
            </div>
        </div>

    </form>
</div>

            
                
        </div>

        <div class="modal fade" id="staffreportModal" tabindex="-1" role="dialog" aria-labelledby="staffreportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Viewer</h5>
                <button type="button" id="closemodal" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0; height: 85vh; overflow: hidden;">
                <div id="staffrpt-pdf-container" style="height: 100%; overflow: hidden;">
                    <p class="text-center">Loading report...</p>
                </div>
            </div>
        </div>
    </div>
</div>

    
    <!-- Proper order of script loading -->
    <!-- 1. First jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- 3. SweetAlert Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- 4. Your custom scripts -->
    <script>
        $(document).ready(function() {
            $('#periodoveral, #pname, #staffSelect3, #staffSelect4, #staffSelect5, #staffSelect6, #periodoveral2, #periodoveral3, #statutory')
        .html('<option value="">Loading...</option>');
    
    $.ajax({
        url: '{{ route("summary.data") }}',
        type: 'GET',
        dataType: 'json',
        cache: true,
        timeout: 30000,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.error) {
                console.error("Error: " + data.error);
                
                // Handle session expiration
                if (data.error === 'Session expired' || data.error === 'Unauthorized access') {
                    showMessage('Your session has expired. Please login again.', true);
                    window.location.href = '{{ route("login") }}';
                    return;
                }
                
                showMessage('Error loading data: ' + data.error, true);
            } else if (data.success) {
                // Populate period dropdowns
                const periodHtml = '<option value="">Select Period</option>' + 
                    data.periodOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#periodoveral, #periodoveral2, #periodoveral3').html(periodHtml);
                
                // Populate pname dropdown
                const pnameHtml = '<option value="">Select Item</option>' + 
                    data.EarningsOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#pname').html(pnameHtml);
                
                // Populate staff dropdowns
                const staffHtml = '<option value="">Select Agent</option>' + 
                    data.snameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#staffSelect3, #staffSelect4, #staffSelect5, #staffSelect6').html(staffHtml);
                
                // Populate statutory dropdown
                const statutoryHtml = '<option value="">Select Item</option>' + 
                    data.statutoryOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#statutory').html(statutoryHtml);
                
                // Initialize Select2 for pname
                if (!$('#pname').hasClass("select2-hidden-accessible")) {
                    $('#pname').select2({
                        placeholder: "Select Item",
                        allowClear: true
                    });
                }
                
                // Initialize Select2 for staff selects
                ['#staffSelect3', '#staffSelect4', '#staffSelect5', '#staffSelect6'].forEach(function(selector) {
                    if (!$(selector).hasClass("select2-hidden-accessible")) {
                        $(selector).select2({
                            placeholder: selector.includes('3') || selector.includes('6') ? "Select Agent" : "Search",
                            allowClear: true
                        });
                    }
                });
                
                // Auto-select first and last staff for range selections
                var options3 = $('#staffSelect3 option:not([value=""])');
                if (options3.length > 0) {
                    $('#staffSelect3').val(options3.first().val()).trigger('change');
                    $('#staffSelect4').val(options3.last().val()).trigger('change');
                }
                
                var options5 = $('#staffSelect5 option:not([value=""])');
                if (options5.length > 0) {
                    $('#staffSelect5').val(options5.first().val()).trigger('change');
                    $('#staffSelect6').val(options5.last().val()).trigger('change');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
            
            if (xhr.status === 403) {
                showMessage('Security token expired. Please refresh the page.', true);
                location.reload();
            } else if (xhr.status === 401) {
                showMessage('Your session has expired. Please login again.', true);
                window.location.href = '{{ route("login") }}';
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
$(document).on('click', '#approve', function(e) {
    e.preventDefault();
    
    var month = $('#currentMonth').val();
    var year = $('#currentYear').val();
    
    if (!month || !year) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Month and year are required'
        });
        return;
    }
    
    // Confirmation dialog
    Swal.fire({
        title: 'Approve Payroll?',
        html: `Are you sure you want to approve the payroll for <strong>${month} ${year}</strong>?<br><br>This action cannot be undone.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4CAF50',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Processing...',
                html: 'Approving payroll and sending notifications',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit approval
            $.ajax({
                url: '{{ route("payroll.approve") }}',
                method: 'POST',
                data: {
                    month: month,
                    year: year,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: response.message,
                        confirmButtonColor: '#4CAF50'
                    }).then(() => {
                        // Optionally reload the page or update UI
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var errorMessage = 'Failed to approve payroll';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    });
});

$('#approveBtn').on('click', function(e) {
        e.preventDefault();
        
        var month = $('#currentMonth').val();
        var year = $('#currentYear').val();
        
        if (!month || !year) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Month and year are required'
            });
            return;
        }
        
        // Confirmation dialog
        Swal.fire({
            title: 'Approver Netpay?',
            html: `Are you sure you want to approve the netpay for <strong>${month} ${year}</strong> for Payment?<br>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e67e22',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Yes, Approve',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    html: 'Approving...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit notification
                $.ajax({
                    url: '{{ route("netpay.approve") }}',
                    method: 'POST',
                    data: {
                        month: month,
                        year: year,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Notification Sent!',
                            html: response.message + '<br><br>Approved ',
                            confirmButtonColor: '#4CAF50'
                        });
                    },
                    error: function(xhr) {
                        var errorMessage = 'Failed to send notification';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });

    $('#rejectBtn').on('click', function(e) {
        e.preventDefault();
        
        var month = $('#currentMonth').val();
        var year = $('#currentYear').val();
        var rejection_reason = $('#rejection_reason').val();

        
        
        if (!rejection_reason) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Feedback is required'
            });
            return;
        }
        
        // Confirmation dialog
        Swal.fire({
            title: 'Reject Netpay?',
            html: `Are you sure you want to Reject the netpay for <strong>${month} ${year}</strong><br>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e67e22',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Yes, Reject',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    html: 'Rejecting...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit notification
                $.ajax({
                    url: '{{ route("netpay.reject") }}',
                    method: 'POST',
                    data: {
                        month: month,
                        year: year,
                        rejection_reason: rejection_reason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Notification Sent!',
                            html: response.message + '<br><br>Rejected ',
                            confirmButtonColor: '#4CAF50'
                        });
                    },
                    error: function(xhr) {
                        var errorMessage = 'Failed to send notification';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });

$(document).on('click', '#openitems2', function (e) {
    e.preventDefault();
    var month = $('#currentMonth').val();
    var year = $('#currentYear').val();
    var pname = 'NET PAY';
    var staff3 = $('#staffSelect5').val();
    var staff4 = $('#staffSelect6').val();
    var actionTaken = false;

    if (!pname) {
        showMessage('Please select a Payroll item', true);
        return;
    }

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
        url: '{{ route("reports.netpay") }}',
        method: 'POST',
        dataType: 'json',
        data: {
            month: month,
            year: year,
            pname: pname,
            staff3: staff3,
            staff4: staff4,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);
                
                var period = `${month}_${year}`;

                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper" style="height: 100%; display: flex; flex-direction: column;">
                        <div class="pdf-actions d-flex gap-2" 
     style="padding: 10px; background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
     
    <button id="downloadPdfBtn" class="btn btn-enhanced btn-cancel btn-sm">
        <i class="fas fa-file-pdf"></i> Download
    </button>

    <button id="printPdfBtn" class="btn btn-enhanced btn-draft btn-sm">
        <i class="fas fa-print"></i> Print
    </button>

    <button id="Exportexcell" class="btn btn-enhanced btn-finalize btn-sm">
        <i class="fas fa-file-excel"></i> Download
    </button>
</div>

                        <iframe 
                            id="staffrptPdfFrame" 
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=1&view=FitH" 
                            style="flex: 1; width: 100%; border: none; display: block;"
                        ></iframe>
                    </div>`;

                $('#staffrpt-pdf-container').html(pdfViewerHTML);

                // PRINT button handler
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staff3, 'PRINT', `${pname}_Listing_${period}`);
                    }
                });

                // DOWNLOAD button handler
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `${pname}_Listing_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staff3, 'DOWNLOAD', `${pname}_Listing_${period}`);
                    }
                });
                $('#Exportexcell').on('click', function () {

    let month = $('#currentMonth').val();
    let year = $('#currentYear').val();
    let pname = 'NET PAY';
    let staff3 = $('#staffSelect5').val();
    let staff4 = $('#staffSelect6').val();
    let url = "{{ route('reports.netpay.excel') }}" +
        "?month=" + encodeURIComponent(month) +
        "&year=" + encodeURIComponent(year) +
        "&pname=" + encodeURIComponent(pname) +
        "&staff3=" + encodeURIComponent(staff3) +
        "&staff4=" + encodeURIComponent(staff4);

    window.location.href = url; // triggers download
});
            } else {
                $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Failed to generate PDF.</p>');
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX error:", error);
            $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Error fetching report.</p>');
        }
    });
});




$(document).on('click', '#openitems', function (e) {
    e.preventDefault();
    var month = $('#currentMonth').val();
    var year = $('#currentYear').val();
    var pname = $('#pname').val();
    var staff3 = $('#staffSelect3').val();
    var staff4 = $('#staffSelect4').val();
    var actionTaken = false;

    if (!pname) {
        showMessage('Please select a Payroll item', true);
        return;
    }

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
        url: '{{ route("reports.earnings") }}',
        method: 'POST',
        dataType: 'json',
        data: {
            month: month,
            year: year,
            pname: pname,
            staff3: staff3,
            staff4: staff4,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);
                
                var period = `${month}_${year}`;

                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper" style="height: 100%; display: flex; flex-direction: column;">
                        <div class="pdf-actions d-flex gap-2" 
     style="padding: 10px; background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
     
    <button id="downloadPdfBtn" class="btn btn-enhanced btn-cancel btn-sm">
        <i class="fas fa-file-pdf"></i> Download
    </button>

    <button id="printPdfBtn" class="btn btn-enhanced btn-draft btn-sm">
        <i class="fas fa-print"></i> Print
    </button>

    <button id="Exportexcell2" class="btn btn-enhanced btn-finalize btn-sm">
        <i class="fas fa-file-excel"></i> Download
    </button>
</div>

                        <iframe 
                            id="staffrptPdfFrame" 
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=1&view=FitH" 
                            style="flex: 1; width: 100%; border: none; display: block;"
                        ></iframe>
                    </div>`;

                $('#staffrpt-pdf-container').html(pdfViewerHTML);

                // PRINT button handler
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staff3, 'PRINT', `${pname}_Listing_${period}`);
                    }
                });

                // DOWNLOAD button handler
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `${pname}_Listing_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staff3, 'DOWNLOAD', `${pname}_Listing_${period}`);
                    }
                });

                 $('#Exportexcell2').on('click', function () {

    let month = $('#currentMonth').val();
    let year = $('#currentYear').val();
    var pname = $('#pname').val();
    let staff3 = $('#staffSelect3').val();
    let staff4 = $('#staffSelect4').val();
    let url = "{{ route('reports.earnings.excel') }}" +
        "?month=" + encodeURIComponent(month) +
        "&year=" + encodeURIComponent(year) +
        "&pname=" + encodeURIComponent(pname) +
        "&staff3=" + encodeURIComponent(staff3) +
        "&staff4=" + encodeURIComponent(staff4);

    window.location.href = url; // triggers download
});
            } else {
                $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Failed to generate PDF.</p>');
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX error:", error);
            $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Error fetching report.</p>');
        }
    });
});

document.getElementById('approvalToggle').addEventListener('change', function () {
    var feedbackSection = document.getElementById('feedbackSection');
    var toggleText = document.getElementById('toggleText');

    var approveBtn = document.getElementById('approveBtn');
    var rejectBtn = document.getElementById('rejectBtn');

    if (this.checked) {
        toggleText.innerText = "Approve";
        feedbackSection.style.display = "none";

        approveBtn.style.display = "block";
        rejectBtn.style.display = "none";
    } else {
        toggleText.innerText = "Reject";
        feedbackSection.style.display = "block";

        approveBtn.style.display = "none";
        rejectBtn.style.display = "block";
    }
});



// Optional: Add animation for feedback section
document.getElementById('feedbackSection').style.transition = 'all 0.3s ease';
    
        });

        
function showMessage(message, isError) {
    let messageDiv = $('#messageDiv');
    const backgroundColor = isError ? '#f44336' : '#4CAF50';
    
    if (messageDiv.length === 0) {
        // Create new message div with proper background color
        messageDiv = $(`
            <div id="messageDiv" style="
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 5px;
                color: white;
                z-index: 1051;
                display: block;
                font-weight: bold;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                animation: slideIn 0.5s, fadeOut 0.5s 2.5s;
                background-color: ${backgroundColor};
            ">
                ${message}
            </div>
        `);
        $('body').append(messageDiv);
    } else {
        // Update existing message div
        messageDiv.text(message)
                 .show()
                 .css('background-color', backgroundColor);
    }
    
    // Clear any existing timeout
    if (messageDiv.data('timeout')) {
        clearTimeout(messageDiv.data('timeout'));
    }
    
    // Set new timeout and store reference
    const timeoutId = setTimeout(() => {
        messageDiv.fadeOut();
    }, 3000);
    
    messageDiv.data('timeout', timeoutId);
}
     
        </script>
</x-custom-admin-layout>