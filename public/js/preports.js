   $(document).ready(function() {
            $('#staffid').select2({
    placeholder: "Select Agent",
    allowClear: true,
    ajax: { 
        url: search, // Use Laravel route
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
        url: summad,
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
                    window.location.href = login;
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
                window.location.href = login;
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
    url: overallrep,
    method: 'POST',
    dataType: 'json',
    data: { 
        period: period,
        _token: '{{ csrf_token() }}'
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
        url: overallrep,
        method: 'POST',
        dataType: 'json',
        data: { 
            period: period,
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
                            style="border:1px solid #ddd;"
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
        url: badvice,
        method: 'POST',
        dataType: 'json',
        data: { 
            period: period,
            recintres: recintres,
            _token: '{{ csrf_token() }}'
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
                            style="border:1px solid #ddd;"
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
        url: payitems, // Laravel route
        method: 'POST',
        dataType: 'json',
        data: { 
            period: period,
            pname: pname,
            staff3: staff3,
            staff4: staff4,
            _token: '{{ csrf_token() }}' // CSRF token
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
        url: rvariance,
        method: 'POST',
        dataType: 'json',
        data: { 
            stperiod: stperiod,
            ndperiod: ndperiod,
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
                            style="border:1px solid #ddd;"
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
        url: rpvariance,
        method: 'POST',
        dataType: 'json',
        data: { 
            stperiod: stperiod,
            ndperiod: ndperiod,
            _token: '{{ csrf_token() }}'
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
                            style="border:1px solid #ddd;"
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
$('#eftgen').on('click', function() {
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

    // Use fetch API for better handling
    fetch(eftrep, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            period: period,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
    })
    .then(response => {
        clearInterval(progressInterval);
        
        if (response.ok) {
            return response.blob();
        } else {
            return response.json().then(error => { throw error; });
        }
    })
    .then(blob => {
        $('#progress-bar').css('width', '100%');
        $('#progress-message').text('EFT report generated successfully!');
        
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `EFT${period}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        
        setTimeout(function() {
            $('#progress-modal').hide();
            $('#progress-bar').css('width', '0%');
        }, 1000);
    })
    .catch(error => {
        clearInterval(progressInterval);
        $('#progress-message').text('An error occurred while generating the EFT report');
        console.error('Error:', error);
        
        setTimeout(function() {
            $('#progress-modal').hide();
            $('#progress-bar').css('width', '0%');
        }, 3000);
    });
});
$('#rtgsgen').on('click', function() {
    var period = $('#periodoveral9').val();
    
    if (!period) {
        showMessage('Please select a period', true);
        return;
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

    // Use fetch API for better handling
    fetch(rtgsrep, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            period: period,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
    })
    .then(response => {
        clearInterval(progressInterval);
        
        if (response.ok) {
            return response.blob();
        } else {
            return response.json().then(error => { throw error; });
        }
    })
    .then(blob => {
        $('#progress-bar').css('width', '100%');
        $('#progress-message').text('RTGS report generated successfully!');
        
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `RTGS${period}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        
        setTimeout(function() {
            $('#progress-modal').hide();
            $('#progress-bar').css('width', '0%');
        }, 1000);
    })
    .catch(error => {
        clearInterval(progressInterval);
        $('#progress-message').text('An error occurred while generating the RTGS report');
        console.error('Error:', error);
        
        setTimeout(function() {
            $('#progress-modal').hide();
            $('#progress-bar').css('width', '0%');
        }, 3000);
    });
});

$('#iftgen').on('click', function() {
    var period = $('#periodoveral7').val();
    
    if (!period) {
        showMessage('Please select a period', true);
        return;
    }

    // Show progress modal
    $('#progress-modal').show();
    $('#progress-bar').css('width', '0%');
    $('#progress-message').text('Generating report...');

    // Simulate progress
    var progress = 0;
    var progressInterval = setInterval(function() {
        progress += 5;
        if (progress <= 90) {
            $('#progress-bar').css('width', progress + '%');
        }
    }, 100);

    // Use fetch API for better handling
    fetch(iftrep, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            period: period,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
    })
    .then(response => {
        clearInterval(progressInterval);
        
        if (response.ok) {
            return response.blob();
        } else {
            return response.json().then(error => { throw error; });
        }
    })
    .then(blob => {
        $('#progress-bar').css('width', '100%');
        $('#progress-message').text('Report generated successfully!');
        
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `IFT${period}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        
        setTimeout(function() {
            $('#progress-modal').hide();
            $('#progress-bar').css('width', '0%');
        }, 1000);
    })
    .catch(error => {
        clearInterval(progressInterval);
        $('#progress-message').text('An error occurred while generating the report');
        console.error('Error:', error);
        
        setTimeout(function() {
            $('#progress-modal').hide();
            $('#progress-bar').css('width', '0%');
        }, 3000);
    });
});
    $(document).on('click', '.view-slip', function (e) {
    e.preventDefault();

    var staffid = $('#staffid').val();
    var period = $('#period').val();
    var actionTaken = false;
    
    if (!staffid) {
        showMessage('Please select a Staff', true);
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
        url: payslip, // Laravel route
        method: 'POST',
        dataType: 'json',
        data: { 
            staffid: staffid, 
            period: period,
            _token: '{{ csrf_token() }}' // CSRF token
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);

                // Log "OPEN"
                //logaudit(staffid, 'OPEN', `Payslip for ${period}`);

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

                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(staffid, 'PRINT', `Payslip for ${period}`);
                    }
                });

                // DOWNLOAD button handler
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `payslip_${staffid}_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staffid, 'DOWNLOAD', `Payslip for ${period}`);
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
         });
        $('#summaries-tab').on('click', function() {
    // Show loading state
    $('#periodoveral, #pname, #staffSelect3, #staffSelect4, #staffSelect5, #staffSelect6, #periodoveral2, #periodoveral3, #statutory')
        .html('<option value="">Loading...</option>');
    
    $.ajax({
        url: summdata,
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
                    window.location.href = login;
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
                window.location.href = login;
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
        url: summdata,
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
                    window.location.href = login;
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
                window.location.href = login;
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
        url: summdata,
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
                    window.location.href = login;
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
                window.location.href = login;
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
        url: summdata,
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
                    window.location.href = login;
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
                window.location.href = login;
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
});
function openTab(evt, tabName) { 
    var i, tabContent, tabButton;

    // Hide all tab content
    tabContent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].style.display = "none";
    }

    // Remove the "active" class from all tab buttons
    tabButton = document.getElementsByClassName("tab-button");
    for (i = 0; i < tabButton.length; i++) {
        tabButton[i].className = tabButton[i].className.replace(" active", "");
    }

    // Show the current tab and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

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