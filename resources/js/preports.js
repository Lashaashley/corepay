document.addEventListener('DOMContentLoaded', function () {

    
    /* ── Tab switching ─────────────────────────────────────── */
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const tabId    = this.dataset.tab;
            const approved = this.dataset.netpayApproved;
 
            // Block if disabled
            if (this.classList.contains('disabled')) {
                showToast('warning', 'Access Restricted',
                    'Bank Interface requires netpay approval (' + (this.dataset.netpayStatus || '') + ').');
                return;
            }
 
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
 
            this.classList.add('active');
            const panel = document.getElementById('panel-' + tabId);
            if (panel) panel.classList.add('active');
        });
    });
 
    /* ── Expose openTab for any legacy inline calls ────────── */
    window.openTab = function (event, tabId) {
        const btn = document.querySelector('[data-tab="' + tabId + '"]');
        if (btn) btn.click();
    };
 
    window.showNetpayNotApprovedWarning = function (e) {
        if (e) e.preventDefault();
        showToast('warning', 'Access Restricted', 'Netpay must be approved before accessing Bank Interface.');
    };
 
    /* ── PDF modal helpers ─────────────────────────────────── */
    const pdfModal     = document.getElementById('staffreportModal');
    const pdfContainer = document.getElementById('staffrpt-pdf-container');
    const pdfLoading   = document.getElementById('pdfLoading');
    const pdfDlBtn     = document.getElementById('pdfDownloadBtn');
    const pdfPrintBtn  = document.getElementById('pdfPrintBtn');
    let   currentPdfUrl = null;
 
    window.openPdfModal = function (title) {
        document.getElementById('pdfModalTitle').textContent = title || 'Report Viewer';
        pdfLoading.style.display = 'flex';
        pdfDlBtn.style.display   = 'none';
        pdfPrintBtn.style.display = 'none';
        const old = pdfContainer.querySelector('iframe');
        if (old) old.remove();
        if (currentPdfUrl) { URL.revokeObjectURL(currentPdfUrl); currentPdfUrl = null; }
        pdfModal.classList.add('open');
    };
 
    window.renderPdfInModal = function (base64, filename) {
        const bytes   = Uint8Array.from(atob(base64), c => c.charCodeAt(0));
        const blob    = new Blob([bytes], { type: 'application/pdf' });
        currentPdfUrl = URL.createObjectURL(blob);
        const iframe  = document.createElement('iframe');
        iframe.id     = 'pdfFrame';
        iframe.src    = currentPdfUrl + '#toolbar=0&navpanes=0';
        iframe.style.cssText = 'width:100%;height:100%;border:none;display:block;';
        pdfLoading.style.display = 'none';
        pdfContainer.appendChild(iframe);
        pdfDlBtn.style.display   = '';
        pdfPrintBtn.style.display = '';
        pdfDlBtn.onclick = () => {
            const a = document.createElement('a');
            a.href = currentPdfUrl; a.download = filename || 'report.pdf';
            document.body.appendChild(a); a.click(); document.body.removeChild(a);
        };
        pdfPrintBtn.onclick = () => {
            const f = document.getElementById('pdfFrame');
            if (f) { f.contentWindow.focus(); f.contentWindow.print(); }
        };
    };
 
    // Close
    document.getElementById('closemodal').addEventListener('click', closePdfModal);
    pdfModal.addEventListener('click', e => { if (e.target === pdfModal) closePdfModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closePdfModal(); });
 
    function closePdfModal () {
        pdfModal.classList.remove('open');
        if (currentPdfUrl) { URL.revokeObjectURL(currentPdfUrl); currentPdfUrl = null; }
    }
 
    /* ── Progress modal helpers ────────────────────────────── */
    window.openProgressModal  = function (msg) {
        document.getElementById('progress-message').textContent = msg || 'Downloading…';
        document.getElementById('progress-bar').style.width = '5%';
        document.getElementById('progress-modal').classList.add('open');
    };
 
    window.updateProgressBar  = function (pct) {
        document.getElementById('progress-bar').style.width = pct + '%';
    };
 
    window.closeProgressModal = function () {
        document.getElementById('progress-modal').classList.remove('open');
        document.getElementById('progress-bar').style.width = '0%';
    };
 
    /* ── Toast ─────────────────────────────────────────────── */
    function showToast (type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber' };
        const t = document.createElement('div');
        t.className = 'toast-msg ' + type;
        t.innerHTML = '<span class="material-icons">' + (icons[type]||'info') + '</span>'
                    + '<div><strong>' + title + '</strong> ' + message + '</div>';
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }
 
    window.showMessage = function (msg, isError) {
        showToast(isError ? 'danger' : 'success', isError ? 'Error' : 'Success', msg);
    };
 
    /* ── jQuery Bootstrap modal shim ───────────────────────────
       Your existing JS calls:
         $('#staffreportModal').modal('show')   — to open
         $('#staffreportModal').modal('hide')   — to close
         $('#staffrpt-pdf-container').html(...)  — to insert PDF content
 
       The shim intercepts these jQuery calls and routes them
       to our custom modal, preventing the Bootstrap 5
       "Illegal invocation" / selector-engine error entirely.
    ─────────────────────────────────────────────────────────── */
    (function ($) {
        if (!$ || !$.fn) return;   // guard: jQuery not present
 
        /* Patch .modal() so Bootstrap never touches our custom div */
        const origModal = $.fn.modal;
 
        $.fn.modal = function (action, options) {
            // Only intercept calls on #staffreportModal
            if (this.is && this.is('#staffreportModal')) {
                if (action === 'show' || action === undefined) {
                    openPdfModal();
                } else if (action === 'hide' || action === 'dispose') {
                    closePdfModal();
                }
                // Return jQuery object for chaining
                return this;
            }
            // All other modals: delegate to original Bootstrap handler
            if (origModal) return origModal.apply(this, arguments);
            return this;
        };
 
        /* Patch .html() on #staffrpt-pdf-container so your JS can
           still do $('#staffrpt-pdf-container').html(pdfViewerHTML)
           We intercept it, extract the base64 / src from the injected
           HTML and render it properly. */
        const origHtml = $.fn.html;
 
        $.fn.html = function (value) {
            // Read mode — don't intercept
            if (value === undefined) return origHtml.call(this);
 
            // Write mode on the PDF container
            if (this.is && this.is('#staffrpt-pdf-container')) {
 
                // Case 1: loading placeholder text
                if (typeof value === 'string' && !value.includes('<iframe') && !value.includes('pdfBlob')) {
                    pdfLoading.style.display = 'flex';
                    // Remove any existing iframe
                    const old = pdfContainer.querySelector('iframe');
                    if (old) old.remove();
                    pdfDlBtn.style.display    = 'none';
                    pdfPrintBtn.style.display = 'none';
                    return this;
                }
 
                // Case 2: contains a Blob URL in an <iframe src="blob:...">
                //   Your JS does: URL.createObjectURL(pdfBlob) then builds an <iframe src="${pdfUrl}">
                const iframeSrcMatch = typeof value === 'string'
                    ? value.match(/src=["']([^"']+)["']/)
                    : null;
 
                if (iframeSrcMatch) {
                    const src = iframeSrcMatch[1];
                    pdfLoading.style.display = 'none';
 
                    const old = pdfContainer.querySelector('iframe');
                    if (old) old.remove();
 
                    const iframe  = document.createElement('iframe');
                    iframe.id     = 'pdfFrame';
                    iframe.src    = src;
                    iframe.style.cssText = 'width:100%;height:100%;border:none;display:block;';
                    pdfContainer.appendChild(iframe);
 
                    // Wire action buttons from the injected HTML
                    pdfDlBtn.style.display    = '';
                    pdfPrintBtn.style.display = '';
 
                    // Extract download URL stored by caller (best-effort)
                    currentPdfUrl = src.split('#')[0];
 
                    pdfDlBtn.onclick = function () {
                        const a = document.createElement('a');
                        a.href = currentPdfUrl;
                        a.download = 'Report_' + new Date().toISOString().split('T')[0] + '.pdf';
                        document.body.appendChild(a); a.click(); document.body.removeChild(a);
                    };
 
                    pdfPrintBtn.onclick = function () {
                        const f = document.getElementById('pdfFrame');
                        if (f) { f.contentWindow.focus(); f.contentWindow.print(); }
                    };
 
                    return this;
                }
 
                // Case 3: error / fallback text — show in loading area
                pdfLoading.style.display = 'flex';
                pdfLoading.innerHTML = '<span class="material-icons font36">error_outline</span>'
                                     + '<span class="colordanger" >' + (value || 'Failed to load report.') + '</span>';
                return this;
            }
 
            // All other elements: delegate to original jQuery .html()
            return origHtml.apply(this, arguments);
        };
 
        /* Also patch the inline download/print button events that
           your JS wires AFTER inserting the HTML — they target
           #downloadPdfBtn / #printPdfBtn by the old IDs.
           Map them to our buttons. */
        $(document).on('click', '#downloadPdfBtn', function () {
            if (pdfDlBtn) pdfDlBtn.click();
        });
 
        $(document).on('click', '#printPdfBtn', function () {
            if (pdfPrintBtn) pdfPrintBtn.click();
        });
 
    }(window.jQuery));
 
});
        $(document).ready(function() {
            $('#closemodal').on('click', function(e) {
                 $('#staffreportModal').modal('hide');
             });
            $('#staffid').select2({
    placeholder: "Select Agent",
    allowClear: true,
    ajax: { 
        url: App.routes.searchagent, // Use Laravel route
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term, // search term
                page: params.page || 1
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;
            
            return {
                results: data.results,
                pagination: {
                    more: data.pagination && data.pagination.more
                }
            };
        },
        cache: true
    },
    minimumInputLength: 0,
    templateResult: formatStaff,
    templateSelection: formatStaffSelection
});

// Format the staff option display
function formatStaff(staff) {
    if (staff.loading) {
        return staff.text;
    }
    return $('<span>' + staff.text + '</span>');
}

// Format the selected staff display
function formatStaffSelection(staff) {
    return staff.text || staff.id;
}
$('#period')
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
                    showMessage('Your session has expired. Please login again.', true);
                    window.location.href = App.routes.login;
                    return;
                }
                
                showMessage('Error loading data: ' + data.error, true);
            } else if (data.success) {
                // Populate period dropdowns
                const periodHtml = '<option value="">Select Period</option>' + 
                    data.periodOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#period').html(periodHtml);
               
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
            
            if (xhr.status === 403) {
                showMessage('Security token expired. Please refresh the page.', true);
                location.reload();
            } else if (xhr.status === 401) {
                showMessage('Your session has expired. Please login again.', true);
                window.location.href = App.routes.login;
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
    $(document).on('click', '#openovral', function (e) {
    e.preventDefault();

   var period = $('#periodoveral').val();
   if (!period) {
        showMessage('Please select a Period', true);
        return;
    }
    var actionTaken = false;

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
    url: App.routes.overalsumm,
    method: 'POST',
    dataType: 'json',
    data: { 
        period: period
    },
    success: function (response) {
        if (response.pdf) {
            var pdfBlob = new Blob(
                [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                { type: 'application/pdf' }
            );
            var pdfUrl = URL.createObjectURL(pdfBlob);

            // Log "OPEN"
            //logaudit(period, 'OPEN', `Company Summary ${period}`);

            var pdfViewerHTML = `
                <div class="pdf-viewer-wrapper">
                    <div class="pdf-actions mb-1">
                       <button id="downloadPdfBtn" class="btn btn-enhanced btn-cancel btn-sm">
        <i class="fas fa-file-pdf"></i> Download
    </button>

    <button id="printPdfBtn" class="btn btn-enhanced btn-draft btn-sm">
        <i class="fas fa-print"></i> Print
    </button>
                    </div>
                    <iframe 
                        id="staffrptPdfFrame" 
                        src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                        width="100%" 
                        height="80vh" 
                        class="siframe"
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
                   // logaudit(period, 'PRINT', `Company Summary ${period}`);
                }
            });

            // DOWNLOAD button handler
            $('#downloadPdfBtn').on('click', function () {
                var link = document.createElement('a');
                link.href = pdfUrl;
                link.download = `Company Summary_${period}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                if (!actionTaken) {
                    actionTaken = true;
                   // logaudit(period, 'DOWNLOAD', `Company Summary ${period}`);
                }
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
$(document).on('click', '#prolsum', function (e) {
    e.preventDefault();

    var period = $('#periodoveral4').val();
    var staff3 = $('#staffSelect7').val(); 
    var staff4 = $('#staffSelect8').val();
   
    if (!period) {
        showMessage('Please select a Period', true);
        return;
    }
    
    var actionTaken = false;
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');
    
    $.ajax({
        url: App.routes.paysummary,
        method: 'POST',
        dataType: 'json',
        data: { 
            period: period,
            staff3: staff3,
            staff4: staff4
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);
                
                //logaudit(staff3, 'OPEN', `Payroll_Summary_Report_${period}`);
                
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
                            class="siframe"
                        ></iframe>
                    </div>`;
                    
                $('#staffrpt-pdf-container').html(pdfViewerHTML);
                
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    
                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(staff3, 'PRINT', `Payroll_Summary_Report_${period}`);
                    }
                });
                
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `Payroll_Summary_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                      //  logaudit(staff3, 'DOWNLOAD', `Payroll_Summary_Report_${period}`);
                    }
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
$('#excelsum').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
   var period = $('#periodoveral4').val();
    var staff3 = $('#staffSelect7').val(); 
    var staff4 = $('#staffSelect8').val();
    
    if (!period) {
        showMessage('Please select a period', true);
        return false;
    }

    // Show progress modal
    $('#progress-modal').show();
    $('#progress-bar').css('width', '0%');
    $('#progress-message').text('Generating Payroll Summary report...');

    // Simulate progress
    var progress = 0;
    var progressInterval = setInterval(function() {
        progress += 5;
        if (progress <= 90) {
            $('#progress-bar').css('width', progress + '%');
        }
    }, 100);

    // Use jQuery AJAX
    $.ajax({
        url:  App.routes.excelsummary,
        method: 'POST',
        data: {
             period: period,
            staff3: staff3,
            staff4: staff4
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob, status, xhr) {
            clearInterval(progressInterval);
            
            $('#progress-bar').css('width', '100%');
            $('#progress-message').text('Payroll Summary generated successfully!');
            
            // Get filename from Content-Disposition header if available
            var filename = `IFT${period}.csv`;
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Create download link
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            // Cleanup
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }, 100);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 1000);
        },
        error: function(xhr, status, error) {
            clearInterval(progressInterval);
            
            var errorMessage = 'An error occurred while generating the IFT report';
            
            // Try to parse error response
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    var errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            $('#progress-message').text(errorMessage);
            console.error('Error:', error);
            console.error('Status:', status);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 3000);
        }
    });
    
    return false;
});
$(document).on('click', '#banktrans', function (e) {
    e.preventDefault();

    var period = $('#periodoveral6').val(); 
    var recintres = $('input[name="recintres"]:checked').val(); 

    if (!period) {
        showMessage('Please select a Period', true);
        return;
    }
    
    var actionTaken = false;
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');
    
    $.ajax({
        url:  App.routes.bankadvice,
        method: 'POST',
        dataType: 'json',
        data: { 
            period: period,
            recintres: recintres
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);
                
                //logaudit(recintres, 'OPEN', `${recintres}_Bank_Advice_Report_${period}`);
                
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
                            class="siframe"
                        ></iframe>
                    </div>`;
                    
                $('#staffrpt-pdf-container').html(pdfViewerHTML);
                
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    
                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(recintres, 'PRINT', `${recintres}_Bank_Advice_Report_${period}`);
                    }
                });
                
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `${recintres}_Bank_Advice_Report_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(recintres, 'DOWNLOAD', `${recintres}_Bank_Advice_Report_${period}`);
                    }
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

    var period = $('#periodoveral2').val();
    var pname = $('#pname').val();
    var staff3 = $('#staffSelect3').val();
    var staff4 = $('#staffSelect4').val();
   
    var actionTaken = false;
    
    if (!pname) {
        showMessage('Please select a Payroll item', true);
        return;
    }
    
    if (!period) {
        showMessage('Please select a Period', true);
        return;
    }

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
        url:  App.routes.reportpayitems, // Laravel route
        method: 'POST',
        dataType: 'json',
        data: { 
            period: period,
            pname: pname,
            staff3: staff3,
            staff4: staff4 // CSRF token
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);

                // Log "OPEN"
                //logaudit(staff3, 'OPEN', `${pname}_Listing_${period}`);

                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper">
                        <div class="pdf-actions mb-1">
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
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                            width="100%" 
                            height="80vh" 
                            class="siframe"
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

    let period = $('#periodoveral2').val();
    let pname = $('#pname').val();
    let staff3 = $('#staffSelect3').val();
    let staff4 = $('#staffSelect4').val();
    
    // Extract month and year from period
    // Assuming period format is like "January2024" (month name directly followed by year)
    let month = period.substring(0, period.length - 4); // Gets everything except last 4 chars
    let year = period.substring(period.length - 4);     // Gets last 4 chars
    
    let url =  App.routes.earnreportsexcel +
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
$(document).on('click', '#varitem', function (e) {
    e.preventDefault();

    var stperiod = $('#1stperiod').val();
    var ndperiod = $('#2ndperiod').val();
    var pname = $('#p2name').val();
    var staff3 = $('#staffSelectst').val();
    var staff4 = $('#staffSelectnd').val();
    
    if (!pname) {
        showMessage('Please select a Payroll item', true);
        return;
    }
    
    if (!stperiod) {
        showMessage('Please select a 1st Period', true);
        return;
    }
    
    if (!ndperiod) {
        showMessage('Please select a 2nd Period', true);
        return;
    }
    
    if (stperiod === ndperiod) {
        showMessage('Sorry The 1st and 2nd period cannot be same', true);
        return; 
    }
    
    var actionTaken = false;
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');
    
    $.ajax({
        url:  App.routes.variancereport,
        method: 'POST',
        dataType: 'json',
        data: { 
            stperiod: stperiod,
            ndperiod: ndperiod,
            pname: pname,
            staff3: staff3,
            staff4: staff4
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);
                
               // logaudit(staff4, 'OPEN', `${pname}_Variance_Report_${stperiod}_to_${ndperiod}`);
                
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
                            class="siframe"
                        ></iframe>
                    </div>`;
                    
                $('#staffrpt-pdf-container').html(pdfViewerHTML);
                
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    
                    if (!actionTaken) {
                        actionTaken = true;
                      //  logaudit(staff4, 'PRINT', `${pname}_Variance_Report_${stperiod}_to_${ndperiod}`);
                    }
                });
                
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `${pname}_Variance_Report_${stperiod}_to_${ndperiod}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(staff4, 'DOWNLOAD', `${pname}_Variance_Report_${stperiod}_to_${ndperiod}`);
                    }
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
$(document).on('click', '#varsitem', function (e) {
    e.preventDefault();

    var stperiod = $('#s1stperiod').val();
    var ndperiod = $('#s2ndperiod').val();
    
    if (!stperiod) {
        showMessage('Please select a 1st Period', true);
        return;
    }
    
    if (!ndperiod) {
        showMessage('Please select a 2nd Period', true);
        return;
    }
    
    if (stperiod === ndperiod) {
        showMessage('Sorry The 1st and 2nd period cannot be same', true);
        return; 
    }
    
    var actionTaken = false;
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');
    
    $.ajax({
        url:  App.routes.payrolvariance,
        method: 'POST',
        dataType: 'json',
        data: { 
            stperiod: stperiod,
            ndperiod: ndperiod
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);
                
               // logaudit(ndperiod, 'OPEN', `Payroll_Variance_Report_${stperiod}_to_${ndperiod}`);
                
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
                            class="siframe"
                        ></iframe>
                    </div>`;
                    
                $('#staffrpt-pdf-container').html(pdfViewerHTML);
                
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    
                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(ndperiod, 'PRINT', `Payroll_Variance_Report_${stperiod}_to_${ndperiod}`);
                    }
                });
                
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `Payroll_Variance_Report_${stperiod}_to_${ndperiod}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(ndperiod, 'DOWNLOAD', `Payroll_Variance_Report_${stperiod}_to_${ndperiod}`);
                    }
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
$('#eftgen').on('click', function(e) {
    e.preventDefault(); // ✅ Prevent default form submission
    e.stopPropagation(); // ✅ Stop event bubbling
    
    var period = $('#periodoveral8').val();
    
    if (!period) {
        showMessage('Please select a period', true);
        return;
    }

    // Show progress modal
    $('#progress-modal').show();
    $('#progress-bar').css('width', '0%');
    $('#progress-message').text('Generating EFT report...');

    // Simulate progress
    var progress = 0;
    var progressInterval = setInterval(function() {
        progress += 5;
        if (progress <= 90) {
            $('#progress-bar').css('width', progress + '%');
        }
    }, 100);

    // Use jQuery AJAX for better compatibility
    $.ajax({
        url: App.routes.eftreport,
        method: 'POST',
        data: {
            period: period,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob' // Important for file download
        },
        success: function(blob, status, xhr) {
            clearInterval(progressInterval);
            
            $('#progress-bar').css('width', '100%');
            $('#progress-message').text('EFT report generated successfully!');
            
            // Get filename from Content-Disposition header if available
            var filename = `EFT${period}.csv`;
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Create download link
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            // Cleanup
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }, 100);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 1000);
        },
        error: function(xhr, status, error) {
            clearInterval(progressInterval);
            
            var errorMessage = 'An error occurred while generating the EFT report';
            
            // Try to parse error response
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    var errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            $('#progress-message').text(errorMessage);
            console.error('Error:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 3000);
        }
    });
});
// RTGS Report Generation
$('#rtgsgen').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var period = $('#periodoveral9').val();
    
    if (!period) {
        showMessage('Please select a period', true);
        return false;
    }

    // Show progress modal
    $('#progress-modal').show();
    $('#progress-bar').css('width', '0%');
    $('#progress-message').text('Generating RTGS report...');

    // Simulate progress
    var progress = 0;
    var progressInterval = setInterval(function() {
        progress += 5;
        if (progress <= 90) {
            $('#progress-bar').css('width', progress + '%');
        }
    }, 100);

    // Use jQuery AJAX
    $.ajax({
        url:  App.routes.rtgsreport,
        method: 'POST',
        data: {
            period: period
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob, status, xhr) {
            clearInterval(progressInterval);
            
            $('#progress-bar').css('width', '100%');
            $('#progress-message').text('RTGS report generated successfully!');
            
            // Get filename from Content-Disposition header if available
            var filename = `RTGS${period}.csv`;
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Create download link
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            // Cleanup
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }, 100);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 1000);
        },
        error: function(xhr, status, error) {
            clearInterval(progressInterval);
            
            var errorMessage = 'An error occurred while generating the RTGS report';
            
            // Try to parse error response
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    var errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            $('#progress-message').text(errorMessage);
            console.error('Error:', error);
            console.error('Status:', status);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 3000);
        }
    });
    
    return false;
});

// IFT Report Generation
$('#iftgen').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var period = $('#periodoveral7').val();
    
    if (!period) {
        showMessage('Please select a period', true);
        return false;
    }

    // Show progress modal
    $('#progress-modal').show();
    $('#progress-bar').css('width', '0%');
    $('#progress-message').text('Generating IFT report...');

    // Simulate progress
    var progress = 0;
    var progressInterval = setInterval(function() {
        progress += 5;
        if (progress <= 90) {
            $('#progress-bar').css('width', progress + '%');
        }
    }, 100);

    // Use jQuery AJAX
    $.ajax({
        url:  App.routes.iftreport,
        method: 'POST',
        data: {
            period: period
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob, status, xhr) {
            clearInterval(progressInterval);
            
            $('#progress-bar').css('width', '100%');
            $('#progress-message').text('IFT report generated successfully!');
            
            // Get filename from Content-Disposition header if available
            var filename = `IFT${period}.csv`;
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Create download link
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            // Cleanup
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }, 100);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 1000);
        },
        error: function(xhr, status, error) {
            clearInterval(progressInterval);
            
            var errorMessage = 'An error occurred while generating the IFT report';
            
            // Try to parse error response
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    var errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            $('#progress-message').text(errorMessage);
            console.error('Error:', error);
            console.error('Status:', status);
            
            setTimeout(function() {
                $('#progress-modal').hide();
                $('#progress-bar').css('width', '0%');
            }, 3000);
        }
    });
    
    return false;
});
$(document).on('click', '.view-slip', function (e) {
    e.preventDefault();

    const staffid = $('#staffid').val();
    const period  = $('#period').val();

    if (!staffid) {
        showMessage('Please select a Staff', true);
        return;
    }

    if (!period) {
        showMessage('Please select a Period', true);
        return;
    }

    // Create POST form → open in new tab
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = App.routes.genpayslip;
    form.target = '_blank';

    // CSRF
    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = document.querySelector('meta[name="csrf-token"]').content;

    // staffid
    const staffInput = document.createElement('input');
    staffInput.type = 'hidden';
    staffInput.name = 'staffid';
    staffInput.value = staffid;

    // period
    const periodInput = document.createElement('input');
    periodInput.type = 'hidden';
    periodInput.name = 'period';
    periodInput.value = period;

    form.appendChild(token);
    form.appendChild(staffInput);
    form.appendChild(periodInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
});

 document.querySelectorAll('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]')
    .forEach(el => new bootstrap.Tooltip(el, {
        html: true,
        trigger: 'hover focus',
        boundary: 'window'
    }));
    
    // Check if bank interface tab should be disabled
 const binterfaceTab = document.getElementById('binterface-tab');
if (binterfaceTab) {
    const isNetpayApproved = binterfaceTab.dataset.netpayApproved;

    if (!isNetpayApproved) {
        binterfaceTab.classList.add('disabled');

        // Bootstrap 5 tooltip on the tab itself
        const tabTooltip = new bootstrap.Tooltip(binterfaceTab, {
            html: true,
            trigger: 'hover focus',
            boundary: 'window'
        });

        document.getElementById('binterface')?.style.setProperty('display', 'none');
    }
}
         });

         function showNetpayNotApprovedWarning(event) {
    event.preventDefault();
    event.stopPropagation();
    
    var netpayStatus = $('#binterface-tab').data('netpay-status');
    var month = '{{ $month }}';
    var year = '{{ $year }}';
    
    Swal.fire({
        icon: 'warning',
        title: 'Bank Interface Unavailable',
        html: `
            <div class="textleft" >
                <p><strong>The Bank Interface is currently unavailable.</strong></p>
                <hr>
                <p><strong>Period:</strong> ${month} ${year}</p>
                <p><strong>Netpay Status:</strong> <span class="badge badge-warning">${netpayStatus}</span></p>
                <hr>
                <p>Please ensure the following steps are completed:</p>
                <ol id="olid">
                    <li>Run <strong>Auto Calculate</strong> in Manage Payroll</li>
                    <li>Click <strong>Notify Approver</strong></li>
                    <li>Wait for netpay approval from authorized personnel</li>
                </ol>
                <p><small><em>The Bank Interface will be automatically enabled once netpay is approved.</em></small></p>
            </div>
        `,
        confirmButtonColor: '#f39c12',
        confirmButtonText: 'Understood',
        width: '600px'
    });
    
    return false;
}
        $('#summaries-tab').on('click', function() {
    // Show loading state
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
                    showMessage('Your session has expired. Please login again.', true);
                    window.location.href = App.routes.login;
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
                    data.pnameOptions.map(opt => 
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
                window.location.href = App.routes.login;
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
});

$('#variance-tab').on('click', function() {
    // Show loading state
    $('#1stperiod, #staffSelectnd, #staffSelectst, #p2name, #2ndperiod, #s1stperiod, #s2ndperiod')
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
                    showMessage('Your session has expired. Please login again.', true);
                    window.location.href = App.routes.login;
                    return;
                }
                
                showMessage('Error loading data: ' + data.error, true);
            } else if (data.success) {
                // Populate period dropdowns
                const periodHtml = '<option value="">Select Period</option>' + 
                    data.periodOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#1stperiod, #2ndperiod, #s1stperiod, #s2ndperiod').html(periodHtml);
                
                // Populate pname dropdown
                const pnameHtml = '<option value="">Select Item</option>' + 
                    data.pnameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#p2name').html(pnameHtml);
                
                // Populate staff dropdowns
                const staffHtml = '<option value="">Select Agent</option>' + 
                    data.snameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#staffSelectnd, #staffSelectst').html(staffHtml);
                // Initialize Select2 for staff selects
                ['#staffSelectst', '#staffSelectnd'].forEach(function(selector) {
                    if (!$(selector).hasClass("select2-hidden-accessible")) {
                        $(selector).select2({
                            placeholder: selector.includes('3') || selector.includes('6') ? "Select Agent" : "Search",
                            allowClear: true
                        });
                    }
                });
                
                // Auto-select first and last staff for range selections
                var options3 = $('#staffSelectst option:not([value=""])');
                if (options3.length > 0) {
                    $('#staffSelectst').val(options3.first().val()).trigger('change');
                    $('#staffSelectnd').val(options3.last().val()).trigger('change');
                }
                
                
                // Initialize Select2 for pname
                if (!$('#p2name').hasClass("select2-hidden-accessible")) {
                    $('#p2name').select2({
                        placeholder: "Select Item",
                        allowClear: true
                    });
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
                window.location.href = App.routes.login;
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
});
  $('#overview-tab').on('click', function() {
    // Show loading state
    $('#staffSelect7, #staffSelect8, #periodoveral4, #periodoveral5, #periodoveral6')
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
                    showMessage('Your session has expired. Please login again.', true);
                    window.location.href = App.routes.login;
                    return;
                }
                
                showMessage('Error loading data: ' + data.error, true);
            } else if (data.success) {
                // Populate period dropdowns
                const periodHtml = '<option value="">Select Period</option>' + 
                    data.periodOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#periodoveral4, #periodoveral5, #periodoveral6').html(periodHtml);
                
                // Populate pname dropdown
                const pnameHtml = '<option value="">Select Item</option>' + 
                    data.pnameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#pname').html(pnameHtml);
                
                // Populate staff dropdowns
                const staffHtml = '<option value="">Select Agent</option>' + 
                    data.snameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#staffSelect7, #staffSelect8').html(staffHtml);
                
                
                
                // Initialize Select2 for staff selects
                ['#staffSelect7', '#staffSelect8'].forEach(function(selector) {
                    if (!$(selector).hasClass("select2-hidden-accessible")) {
                        $(selector).select2({
                            placeholder: selector.includes('3') || selector.includes('6') ? "Select Agent" : "Search",
                            allowClear: true
                        });
                    }
                });
                
                // Auto-select first and last staff for range selections
                var options3 = $('#staffSelect7 option:not([value=""])');
                if (options3.length > 0) {
                    $('#staffSelect7').val(options3.first().val()).trigger('change');
                    $('#staffSelect8').val(options3.last().val()).trigger('change');
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
                window.location.href = App.routes.login;
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
});
$('#binterface-tab').on('click', function() {
    // Show loading state
    $('#periodoveral7, #periodoveral8, #periodoveral9')
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
                    showMessage('Your session has expired. Please login again.', true);
                    window.location.href = App.routes.login;
                    return;
                }
                
                showMessage('Error loading data: ' + data.error, true);
            } else if (data.success) {
                // Populate period dropdowns
                const periodHtml = '<option value="">Select Period</option>' + 
                    data.periodOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#periodoveral7, #periodoveral8, #periodoveral9').html(periodHtml);
                
                
                
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
            
            if (xhr.status === 403) {
                showMessage('Security token expired. Please refresh the page.', true);
                location.reload();
            } else if (xhr.status === 401) {
                showMessage('Your session has expired. Please login again.', true);
                window.location.href = App.routes.login;
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
});


function showMessage(message, isError) {
    let messageDiv = $('#messageDiv');
    const backgroundColor = isError ? '#f44336' : '#4CAF50';
    
    if (messageDiv.length === 0) {
        // Create new message div with proper background color
        messageDiv = $(`
            <div id="messageDiv">
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