 function toggleApproval() {
    var track = document.getElementById('toggleTrack');
    var text  = document.getElementById('toggleText');
    var appBtn = document.getElementById('approveBtn');
    var rejBtn = document.getElementById('rejectBtn');
    var feed   = document.getElementById('feedbackSection');
    var icon   = document.querySelector('#netpayCardIcon .material-icons');
    var cb     = document.getElementById('approvalToggle');
 
    var isApprove = track.classList.toggle('on');
    text.classList.toggle('on', isApprove);
    cb.checked = isApprove;
 
    text.textContent      = isApprove ? 'Approve' : 'Reject';
    appBtn.style.display  = isApprove ? '' : 'none';
    rejBtn.style.display  = isApprove ? 'none' : '';
    feed.style.display    = isApprove ? 'none' : 'block';
 
    // Update card icon colour
    if (icon) {
        document.getElementById('netpayCardIcon').className =
            'a-card-icon ' + (isApprove ? 'green' : 'red');
        icon.textContent = isApprove ? 'verified' : 'cancel';
    }
}

// ✅ Helpers that match your CSS (.open class)
function openPdfModal(title) {
    const modal = document.getElementById('staffreportModal');
    if (title) document.getElementById('pdfModalTitle').textContent = title;
    modal.classList.add('open');
}

function closePdfModal() {
    document.getElementById('staffreportModal').classList.remove('open');
}

// ✅ Wire up close button and backdrop click — run once at page load
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('closemodal')?.addEventListener('click', closePdfModal);

    // Click outside the card to close
    document.getElementById('staffreportModal')?.addEventListener('click', function (e) {
        if (e.target === this) closePdfModal();
    });

    // ESC key to close
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePdfModal();
    });
});
 
 /* ── PDF modal helpers ─────────────────────────────────── */
  
 
/* ── Toast ───────────────────────────────────────────────── */
function showToast(type, title, message) {
    var icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber' };
    var t = document.createElement('div');
    t.className = 'toast-msg ' + type;
    t.innerHTML = '<span class="material-icons">' + (icons[type]||'info') + '</span>'
                + '<div><strong>' + title + '</strong> ' + message + '</div>';
    document.getElementById('toastWrap').appendChild(t);
    var dismiss = function() { t.classList.add('leaving'); setTimeout(function() { t.remove(); }, 300); };
    t.addEventListener('click', dismiss);
    setTimeout(dismiss, 5000);
}
 
window.showMessage = function(msg, type) {
    showToast(type || 'info', type === 'success' ? 'Success' : 'Notice', msg);
};

        $(document).ready(function() {
            $('#periodoveral, #pname, #staffSelect3, #staffSelect4, #staffSelect5, #staffSelect6, #periodoveral2, #periodoveral3, #statutory')
        .html('<option value="">Loading...</option>');
    
    $.ajax({
        url: App.routes.summarydata,
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
                      showToast('danger', 'Error', 'Your session has expired. Please login again.');
                    window.location.href = App.routes.login;
                    return;
                }
                
                  showToast('danger', 'Error loading data', + data.error);
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
                  showToast('danger', 'Error','Security token expired. Please refresh the page.');
                location.reload();
            } else if (xhr.status === 401) {
                  showToast('danger', 'Error','Your session has expired. Please login again.');
                window.location.href = App.routes.login;
            } else {
                  showToast('danger', 'Error', 'Failed to load data. Please refresh the page.', true);
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
                url: App.routes.payapprove,
                method: 'POST',
                data: {
                    month: month,
                    year: year
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
                    url: App.routes.netapprove,
                    method: 'POST',
                    data: {
                        month: month,
                        year: year
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
                    url: App.routes.netreject,
                    method: 'POST',
                    data: {
                        month: month,
                        year: year,
                        rejection_reason: rejection_reason
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
          showToast('danger', 'Error', 'Please select a Payroll item');
        return;
    }

    const container = document.getElementById('staffrpt-pdf-container');

    // ✅ Show loading state inside modal body
    container.innerHTML = `
        <div class="pdf-loading" id="pdfLoading">
            <span class="material-icons">sync</span>
            <span>Loading report…</span>
        </div>`;

    // ✅ Open modal — uses .open class matching your CSS
    openPdfModal(`${pname} — ${month}/${year}`);

    $.ajax({
        url: App.routes.netreports,
        method: 'POST',
        dataType: 'json',
        data: {
            month: month,
            year: year,
            pname: pname,
            staff3: staff3,
            staff4: staff4
        },
        success: function (response) {
             if (!response.pdf) {
                container.innerHTML = '<p class="text-center text-danger m-4">No report data returned.</p>';
                return;
            }
                const pdfBlob = new Blob(
                [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                { type: 'application/pdf' }
            );
            const pdfUrl  = URL.createObjectURL(pdfBlob);
            const period  = `${month}_${year}`;

                var pdfViewerHTML = `
            <div class="pdf-viewer-wrapper">
                <div class="pdf-actions d-flex gap-2">
                    <button id="downloadPdfBtn" class="btn btn-enhanced btn-info btn-sm">
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
                    class="pdf-iframe"
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
    let url = App.routes.netreportsexcel +
        "?month=" + encodeURIComponent(month) +
        "&year=" + encodeURIComponent(year) +
        "&pname=" + encodeURIComponent(pname) + 
        "&staff3=" + encodeURIComponent(staff3) +
        "&staff4=" + encodeURIComponent(staff4);

    window.location.href = url; // triggers download
});
           
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
          showToast('danger', 'Error', 'Please select a Payroll item');
        return;
    }

   const container = document.getElementById('staffrpt-pdf-container');

    // ✅ Show loading state inside modal body
    container.innerHTML = `
        <div class="pdf-loading" id="pdfLoading">
            <span class="material-icons">sync</span>
            <span>Loading report…</span>
        </div>`;

    // ✅ Open modal — uses .open class matching your CSS
    openPdfModal(`${pname} — ${month}/${year}`);
   

    $.ajax({
        url: App.routes.earnreports,
        method: 'POST',
        dataType: 'json',
        data: {
            month: month,
            year: year,
            pname: pname,
            staff3: staff3,
            staff4: staff4
        },
        success: function (response) {
            if (response.pdf) {
                if (!response.pdf) {
                container.innerHTML = '<p class="text-center text-danger m-4">No report data returned.</p>';
                return;
            }

            const pdfBlob = new Blob(
                [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                { type: 'application/pdf' }
            );
            const pdfUrl  = URL.createObjectURL(pdfBlob);
            const period  = `${month}_${year}`;

               

              container.innerHTML = `
                <div class="pdf-viewer-wrapper">
                    <div class="pdf-actions d-flex gap-2 p-2">
                        <button id="downloadPdfBtn" class="btn btn-enhanced btn-info btn-sm">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </button>
                        <button id="Exportexcell2" class="btn btn-enhanced btn-finalize btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                    <iframe 
                        id="staffrptPdfFrame" 
                        src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=1&view=FitH" 
                        class="pdf-iframe"
                        style="width:100%; height:70vh; border:none;"
                    ></iframe>
                </div>`;

            $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `${pname}_Listing_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    if (!actionTaken) {
                        actionTaken = true;
                       
                    }
                });

            document.getElementById('pdfPrintBtn').onclick = function () {
                document.getElementById('staffrptPdfFrame')?.contentWindow?.print();
            };
                

                 $('#Exportexcell2').on('click', function () {

    let month = $('#currentMonth').val();
    let year = $('#currentYear').val();
    var pname = $('#pname').val();
    let staff3 = $('#staffSelect3').val();
    let staff4 = $('#staffSelect4').val();
    let url = App.routes.earnreportsexcel +
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
    const messageClass = isError ? 'message-error' : 'message-success';
    
    if (messageDiv.length === 0) {
        messageDiv = $(`<div id="messageDiv" class="message-toast ${messageClass}">${message}</div>`);
        $('body').append(messageDiv);
    } else {
        messageDiv.text(message)
                 .removeClass('message-success message-error')
                 .addClass(messageClass)
                 .show();
    }
    
    if (messageDiv.data('timeout')) {
        clearTimeout(messageDiv.data('timeout'));
    }
    
    const timeoutId = setTimeout(() => {
        messageDiv.fadeOut();
    }, 3000);
    
    messageDiv.data('timeout', timeoutId);
}