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
$('#approve').on('click', function (e) {
    e.preventDefault();

    const month = $('#currentMonth').val();
    const year  = $('#currentYear').val();

    if (!month || !year) {
        bsAlert({ icon: 'error', title: 'Error', message: 'Month and year are required' });
        return;
    }

    bsConfirm({
        icon:         'warning',
        title:        'Approve Payroll?',
        message:      `Are you sure you want to approve the payroll for ${month} ${year} This action cannot be undone.`,
        confirmText:  'Yes, Approve',
        cancelText:   'Cancel',
        confirmClass: 'btn-warning',
        onConfirm:    () => approvepayroll(month, year)
    });
});

function approvepayroll(month, year) {
    // Show a simple loading modal
    const loadingEl = document.getElementById('progressTotalsModal'); // reuse your existing progress modal
    const loadingModal = bootstrap.Modal.getOrCreateInstance(loadingEl);
    document.getElementById('bs-progress-message').textContent = 'Calculating totals and sending notification...';
    document.getElementById('bs-progress-bar').style.width = '100%';
    document.getElementById('bs-progress-bar').textContent  = '';
    document.getElementById('bs-progress-bar').classList.add('progress-bar-animated');
    loadingModal.show();

    $.ajax({
        url:    App.routes.payapprove,
        method: 'POST',
        data:   { month, year },
        success: function (response) {
            loadingModal.hide();
            bsAlert({
                icon:    'success',
                title:   'Approved!',
                message: `${response.message}`
            });
        },
        error: function (xhr) {
            loadingModal.hide();
            const errorMessage = xhr.responseJSON?.message || 'Failed to send notification';
            bsAlert({ icon: 'error', title: 'Error', message: errorMessage });
        }
    });
}

$('#approveBtn').on('click', function (e) {
    e.preventDefault();

    const month = $('#currentMonth').val();
    const year  = $('#currentYear').val();

    if (!month || !year) {
        bsAlert({ icon: 'error', title: 'Error', message: 'Month and year are required' });
        return;
    }

    bsConfirm({
        icon:         'warning',
        title:        'Approve Netpay?',
        message:      `Are you sure you want to approve the netpay for ${month} ${year} for Payment?`,
        confirmText:  'Yes, Approve',
        cancelText:   'Cancel',
        confirmClass: 'btn-warning',
        onConfirm:    () => submitApproval(month, year)
    });
});

function submitApproval(month, year) {
    const loadingModal = bootstrap.Modal.getOrCreateInstance(
        document.getElementById('progressTotalsModal')
    );
    document.getElementById('bs-progress-message').textContent = 'Approving...';
    document.getElementById('bs-progress-bar').style.width     = '100%';
    document.getElementById('bs-progress-bar').textContent     = '';
    loadingModal.show();

    $.ajax({
        url:    App.routes.netapprove,
        method: 'POST',
        data:   { month, year },
        success: function (response) {
            loadingModal.hide();
            bsAlert({ icon: 'success', title: 'Approved!', message: response.message });
        },
        error: function (xhr) {
            loadingModal.hide();
            bsAlert({
                icon:    'error',
                title:   'Error',
                message: xhr.responseJSON?.message || 'Failed to approve netpay'
            });
        }
    });
}

// ── Reject ────────────────────────────────────────────────────────────────────

$('#rejectBtn').on('click', function (e) {
    e.preventDefault();

    const month            = $('#currentMonth').val();
    const year             = $('#currentYear').val();
    const rejection_reason = $('#rejection_reason').val();

    if (!rejection_reason) {
        bsAlert({ icon: 'error', title: 'Error', message: 'Feedback is required' });
        return;
    }

    bsConfirm({
        icon:         'warning',
        title:        'Reject Netpay?',
        message:      `Are you sure you want to reject the netpay for ${month} ${year}?`,
        confirmText:  'Yes, Reject',
        cancelText:   'Cancel',
        confirmClass: 'btn-danger',
        onConfirm:    () => submitRejection(month, year, rejection_reason)
    });
});

function submitRejection(month, year, rejection_reason) {
    const loadingModal = bootstrap.Modal.getOrCreateInstance(
        document.getElementById('progressTotalsModal')
    );
    document.getElementById('bs-progress-message').textContent = 'Rejecting...';
    document.getElementById('bs-progress-bar').style.width     = '100%';
    document.getElementById('bs-progress-bar').textContent     = '';
    loadingModal.show();

    $.ajax({
        url:    App.routes.netreject,
        method: 'POST',
        data:   { month, year, rejection_reason },
        success: function (response) {
            loadingModal.hide();
            bsAlert({ icon: 'success', title: 'Rejected!', message: response.message });
        },
        error: function (xhr) {
            loadingModal.hide();
            bsAlert({
                icon:    'error',
                title:   'Error',
                message: xhr.responseJSON?.message || 'Failed to reject netpay'
            });
        }
    });
}

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

 document.getElementById('toggleTrack').onclick = function () {
               toggleApproval();
            };


$(document).on('click', '#openitems', function (e) {
    e.preventDefault();

    var month  = $('#currentMonth').val();
    var year   = $('#currentYear').val();
    var pname  = $('#pname').val();
    var staff3 = $('#staffSelect3').val();
    var staff4 = $('#staffSelect4').val();

    // Validation
    if (!pname) {
        showToast('danger', 'Error', 'Please select a Payroll item');
        return;
    }

    // Open modal + show loader
    openPdfModal(`${pname} — ${month}/${year}`);
    document.getElementById('pdfLoading').style.display = 'flex';

    // Clear previous iframe
    var container = document.getElementById('staffrpt-pdf-container');
    var old = container.querySelector('iframe');
    if (old) old.remove();

    // Create named iframe
    const iframeName = 'earningsReportFrame';
    var iframe = document.createElement('iframe');
    iframe.name = iframeName;
    iframe.id   = 'staffrptPdfFrame';
    iframe.style.cssText = 'width:100%;height:100%;border:none;display:block;';
    iframe.onload = function () {
        document.getElementById('pdfLoading').style.display = 'none';
    };
    container.appendChild(iframe);

    // Build fields once — reused by preview, download, print
    const fields = {
        '_token' : document.querySelector('meta[name="csrf-token"]').content,
        'month'  : month,
        'year'   : year,
        'pname'  : pname,
        'staff3' : staff3 ?? '',
        'staff4' : staff4 ?? ''
    };

    // Helper: build and submit a form
    function submitForm(target) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = App.routes.earnreports;
        form.target = target;

        Object.entries(fields).forEach(([name, value]) => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = name;
            input.value = value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    // POST into iframe for preview
    submitForm(iframeName);

    // ── Button handlers (.off first to prevent duplicate binds) ──

    // Print
    $('#pdfPrintBtn').off('click').on('click', function () {
        var frame = document.getElementById('staffrptPdfFrame');
        if (frame) {
            frame.contentWindow.focus();
            frame.contentWindow.print();
        }
    });

    // Download — POST into new tab (browser saves it)
    $('#pdfDownloadBtn').off('click').on('click', function () {
        submitForm('_blank');
    });

    // Excel export — GET with query params
    $('#Exportexcell2').off('click').on('click', function () {
        var url = App.routes.earnreportsexcel +
            '?month='  + encodeURIComponent(month) +
            '&year='   + encodeURIComponent(year) +
            '&pname='  + encodeURIComponent(pname) +
            '&staff3=' + encodeURIComponent(staff3 ?? '') +
            '&staff4=' + encodeURIComponent(staff4 ?? '');

        window.location.href = url;
    });
});

// Close modal
$('#closemodal').off('click').on('click', function () {
    document.getElementById('staffreportModal').classList.remove('open');
    var old = document.getElementById('staffrpt-pdf-container').querySelector('iframe');
    if (old) old.remove();
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