<x-custom-admin-layout>
@vite(['resources/css/pages/closep.css']) 
<div class="close-period-page">
 
    <div class="page-heading">
        <h1>Close Period</h1>
        <p>Finalise the current payroll period and distribute agent payslips.</p>
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
    <div id="successMessage" style="display:none;"></div>
 
    <div class="cp-layout">
 
        {{-- ── Left: Close period ──────────────────────────── --}}
        <div class="cp-card">
            <div class="cp-card-head">
                <div class="cp-icon red"><span class="material-icons">event_busy</span></div>
                <div>
                    <p class="cp-card-title">Current Period</p>
                    <p class="cp-card-subtitle">Close when payroll is finalised</p>
                </div>
            </div>
 
            <div class="cp-card-body">
                <div class="period-grid">
                    <div class="period-cell">
                        <div class="lbl">Month</div>
                        <div class="val">{{ $month }}</div>
                    </div>
                    <div class="period-cell">
                        <div class="lbl">Year</div>
                        <div class="val">{{ $year }}</div>
                    </div>
                </div>
 
                {{-- Hidden fields for JS compatibility --}}
                <input type="hidden" id="currentMonth" value="{{ $month }}">
                <input type="hidden" id="currentYear"  value="{{ $year }}">
 
                <div class="cp-alert">
                    <span class="material-icons">warning_amber</span>
                    <span>Closing a period is <strong>irreversible</strong>. Ensure all data is reviewed and approved before proceeding.</span>
                </div>
 
                <button id="load" type="button" class="btn btn-danger-action">
                    <span class="material-icons">lock</span>
                    Close Period
                </button>
            </div>
        </div>
 
        {{-- ── Right: Bulk payslip generation ──────────────── --}}
        <div class="cp-card">
            <div class="cp-card-head">
                <div class="cp-icon green"><span class="material-icons">mail</span></div>
                <div>
                    <p class="cp-card-title">Bulk Payslip Generation</p>
                    <p class="cp-card-subtitle">Generate &amp; optionally email payslips to agents</p>
                </div>
            </div>
 
            <div class="cp-card-body">
                <form id="bulkPayslipForm">
                    @csrf
 
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
 
                        <div class="cp-field">
                            <label>Period <span style="color:var(--danger)">*</span></label>
                            <div class="select-wrap">
                                <select id="period" name="period" required>
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
 
                        <div class="cp-field">
                            <label>Download Method <span style="color:var(--danger)">*</span></label>
                            <div class="select-wrap">
                                <select id="download_method" name="download_method" required>
                                    <option value="nogeneration">No Download</option>
                                    <option value="zip">ZIP — file</option>
                                    
                                </select>
                            </div>
                            <span class="field-hint">ZIP: all payslips in one archive · Individual: separate PDFs per agent</span>
                        </div>
 
                    </div>
 
                    {{-- Email chip --}}
                    <div class="cp-field">
                        <label>Email Delivery</label>
                        <label class="email-chip" id="emailChip">
                            <input type="checkbox" id="send_email" name="send_email" value="1">
                            <div class="ec-check">
                                <span class="material-icons">check</span>
                            </div>
                            <span class="material-icons mail-icon">email</span>
                            Send payslips via email
                        </label>
                        <span class="pwd-badge">
                            <span class="material-icons">lock</span>
                            Payslip password: Employee's KRA PIN
                        </span>
                    </div>
 
                    <button type="submit" class="btn btn-generate" id="generateBtn">
                        <span class="material-icons">download</span>
                        Generate &amp; Download Payslips
                    </button>
 
                </form>
 
                {{-- Inline progress — shown by JS --}}
                <div id="progressSection">
                    <div class="progress-inset">
                        <div class="progress-inset-title">
                            <span class="material-icons">sync</span>
                            Generation Progress
                        </div>
                        <div class="progress-track">
                            <div id="progressBar"></div>
                        </div>
                        <p id="progressMessage">Starting…</p>
                        <p id="progressStats"></p>
                    </div>
 
                    <div id="downloadLinksSection">
                        <h6>Download Files</h6>
                        <div id="downloadLinks"></div>
                    </div>
                </div>
 
            </div>
        </div>
 
    </div>
</div>
 
{{-- ── Sending progress modal (#progress-modal — ID preserved) ── --}}
<div class="pm-backdrop" id="progress-modal">
    <div class="pm-card">
        <div class="pm-spin-icon">
            <span class="material-icons">sync</span>
        </div>
        <h3>Sending</h3>
        <p id="progress-message">Preparing payslips…</p>
        <div class="pm-track">
            <div id="progress-bar"></div>
        </div>
    </div>
</div>


    
    <!-- Proper order of script loading -->
    <!-- 1. First jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- 3. SweetAlert Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- 4. Your custom scripts -->
    <script nonce="{{ $cspNonce }}">
        
        document.addEventListener('DOMContentLoaded', function () {
 
    /* ── Email chip toggle ────────────────────────────────── */
    const emailChip = document.getElementById('emailChip');
    const emailCb   = document.getElementById('send_email');
 
    emailChip.addEventListener('click', function (e) {
        // Prevent double-firing from label's default checkbox behaviour
        e.preventDefault();
        emailCb.checked = !emailCb.checked;
        emailChip.classList.toggle('on', emailCb.checked);
    });
 
    /* ── Progress modal global helpers (existing JS uses these) */
    window.openProgressModal = function (msg) {
        document.getElementById('progress-message').textContent = msg || 'Processing…';
        document.getElementById('progress-bar').style.width = '5%';
        document.getElementById('progress-modal').classList.add('open');
    };
 
    window.updateProgressBar = function (pct) {
        document.getElementById('progress-bar').style.width = pct + '%';
    };
 
    window.closeProgressModal = function () {
        document.getElementById('progress-modal').classList.remove('open');
        document.getElementById('progress-bar').style.width = '0%';
    };
 
    /* ── Legacy show/hide for #progress-modal (old inline style) */
    // Some old JS does: document.getElementById('progress-modal').style.display = 'flex'
    // We intercept with a MutationObserver so both approaches work.
    const pmEl = document.getElementById('progress-modal');
    const observer = new MutationObserver(function () {
        if (pmEl.style.display === 'flex' || pmEl.style.display === 'block') {
            pmEl.style.display = '';
            pmEl.classList.add('open');
        } else if (pmEl.style.display === 'none') {
            pmEl.style.display = '';
            pmEl.classList.remove('open');
        }
    });
    observer.observe(pmEl, { attributes: true, attributeFilter: ['style'] });
 
    /* ── Toast ────────────────────────────────────────────── */
    window.showMessage = function (msg, isError) {
        showToast(isError ? 'danger' : 'success', isError ? 'Error' : 'Success', msg);
    };
 
    function showToast (type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber' };
        const t = document.createElement('div');
        t.className = 'toast-msg ' + type;
        t.innerHTML = '<span class="material-icons">' + (icons[type] || 'info') + '</span>'
                    + '<div><strong>' + title + '</strong> ' + message + '</div>';
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 6000);
    }
 
});
        function updateProgress(percent, message, stats) {
    progressBar.style.width = percent + '%';
    progressBar.textContent = percent + '%';
    progressMessage.textContent = message;
    progressStats.textContent = stats;
}
        document.addEventListener('DOMContentLoaded', function() {
            $('#period')
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
                window.location.href = '{{ route("login") }}';
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
    // Function to format month number to month name
    function getMonthName(monthNumber) {
        const monthNames = ["January", "February", "March", "April", "May", "June",
                            "July", "August", "September", "October", "November", "December"];
        return monthNames[monthNumber - 1];
    }

    // Add click event listener to the #load button
    document.getElementById('load').addEventListener('click', function(event) {
    event.preventDefault();
    
    var selectedYear = $('#currentYear').val();
    var selectedMonthName = $('#currentMonth').val();
    const submitBtn = this; // Store reference to the button
    const originalText = submitBtn.innerHTML; // Store original HTML

    // Use SweetAlert for confirmation 
    Swal.fire({
        title: 'Are you sure?',
        text: `Are you sure you want to close the period ${selectedMonthName} ${selectedYear}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, close period!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show spinner on the button immediately
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Closing...';
            submitBtn.disabled = true;
            
            // Show progress modal using SweetAlert
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 5;
                if (progress <= 90) {
                    updateProgress(progress, 'Processing period closing...');
                }
            }, 200);

            // AJAX request to Laravel backend
            fetch('{{ route("period.close") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: new URLSearchParams({
                    'month': selectedMonthName,
                    'year': selectedYear
                })
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(progressInterval);
                
                // Reset button text BEFORE showing success message
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (data.status === 'success') {
                    updateProgress(100, 'Period closed successfully!');
                    
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Period Closed',
                            text: 'Period closed successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }, 500);
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                clearInterval(progressInterval);
                
                // Reset button text on error too
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred while closing the period.',
                    footer: `Error: ${error.message}`
                });
            });
        } else {
            // Canceled
            Swal.fire({
                icon: 'info',
                title: 'Cancelled',
                text: 'Closing period was cancelled.'
            });
        }
    });
});
});
document.addEventListener('DOMContentLoaded', function() {
   const form = document.getElementById('bulkPayslipForm');
const progressSection = document.getElementById('progressSection');
const progressBar = document.getElementById('progressBar');
const progressMessage = document.getElementById('progressMessage');
const progressStats = document.getElementById('progressStats');
const generateBtn = document.getElementById('generateBtn');
const downloadLinksSection = document.getElementById('downloadLinksSection');
const downloadLinks = document.getElementById('downloadLinks');

let directoryHandle = null; // For File System Access API

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const period = document.getElementById('period').value;
    const downloadMethod = document.getElementById('download_method').value;
    const sendEmail = document.getElementById('send_email').checked;
    
    if (!period) {
        alert('Please select a period');
        return;
    }

    // Confirm if sending emails
    if (sendEmail) {
        const confirmEmail = confirm(
            'You are about to send payslips via email to all employees.\n\n' +
            '• PDFs will be password-protected with KRA PIN\n' +
            '• Only employees with valid email addresses will receive payslips\n\n' +
            'Continue?'
        );
        
        if (!confirmEmail) {
            return;
        }
    }

    // For individual downloads, request directory access first
    if (downloadMethod === 'individual') {
        if (!('showDirectoryPicker' in window)) {
            alert('Your browser does not support folder selection. Please use ZIP download method.');
            return;
        }
        
        try {
            directoryHandle = await window.showDirectoryPicker();
        } catch (err) {
            if (err.name !== 'AbortError') {
                alert('Failed to select directory: ' + err.message);
            }
            return;
        }
    }

    // Show progress section
    progressSection.style.display = 'block';
    downloadLinksSection.style.display = 'none';
    downloadLinks.innerHTML = '';
    generateBtn.disabled = true;
    
    // Reset progress
    const initialMessage = sendEmail ? 
        'Starting payslip generation and email delivery...' : 
        'Starting bulk payslip generation...';
    updateProgress(0, initialMessage, '0/0 employees');

    try {
        // Start generation
        const response = await fetch('{{ route("bulk.payslips.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                period: period,
                download_method: downloadMethod,
                send_email: sendEmail
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            // Start polling for progress
            pollProgress(data.job_id, downloadMethod, sendEmail);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        alert('Error: ' + error.message);
        generateBtn.disabled = false;
        progressSection.style.display = 'none';
    }
});

function pollProgress(jobId, downloadMethod, sendEmail) {
    const progressInterval = setInterval(async function() {
        try {
            const response = await fetch(`{{ route('bulk.payslips.progress', '') }}/${jobId}`);
            const data = await response.json();

            if (data.status === 'success') {
                const progress = data.progress;

                // Build stats message
                let stats = `${progress.success} success, ${progress.failed} failed of ${progress.total} total`;
                if (sendEmail) {
                    stats += ` | Emailed: ${progress.emailed}, Email failed: ${progress.email_failed}`;
                }

                updateProgress(
                    progress.progress,
                    progress.message,
                    stats
                );

                if (progress.progress >= 100) {
                    clearInterval(progressInterval);
                    generateBtn.disabled = false;

                    // Show completion message
                    let completionMsg = '';
                    if (progress.failed > 0) {
                        completionMsg += `<br><span class="text-warning">⚠️ Completed with ${progress.failed} generation errors.</span>`;
                    }
                    if (sendEmail && progress.email_failed > 0) {
                        completionMsg += `<br><span class="text-warning">⚠️ ${progress.email_failed} emails failed to send.</span>`;
                    }
                    if (sendEmail && progress.emailed > 0) {
                        completionMsg += `<br><span class="text-success">✉️ ${progress.emailed} payslips emailed successfully!</span>`;
                    }
                    if (progress.failed === 0 && (!sendEmail || progress.email_failed === 0)) {
                        completionMsg += '<br><span class="text-success">✅ All payslips processed successfully!</span>';
                    }
                    
                    progressMessage.innerHTML += completionMsg;

                    // Trigger download based on method (only if not email-only)
                    if (!sendEmail || downloadMethod === 'zip' || downloadMethod === 'individual') {
                        if (downloadMethod === 'zip') {
                            downloadZipFile(jobId);
                        } else {
                            downloadIndividualFiles(jobId);
                        }
                    }
                }
            } else {
                clearInterval(progressInterval);
                alert('Error getting progress: ' + data.message);
                generateBtn.disabled = false;
            }
        } catch (error) {
            clearInterval(progressInterval);
            console.error('Progress polling error:', error);
            alert('Error polling progress: ' + error.message);
            generateBtn.disabled = false;
        }
    }, 2000);
}



// Download as ZIP file
async function downloadZipFile(jobId) {
    try {
        progressMessage.innerHTML += '<br>Preparing ZIP file...';
        
        const response = await fetch(`{{ route('bulk.payslips.download.zip', '') }}/${jobId}`);
        
        if (!response.ok) {
            throw new Error('Failed to download ZIP file');
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Payslips_${document.getElementById('period').value}.zip`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        progressMessage.innerHTML += '<br><span class="text-success">ZIP file downloaded successfully!</span>';
    } catch (error) {
        alert('Error downloading ZIP: ' + error.message);
    }
}

// Download individual files using File System Access API
async function downloadIndividualFiles(jobId) {
    try {
        progressMessage.innerHTML += '<br>Downloading files to selected folder...';
        
        // Get list of files
        const response = await fetch(`{{ route('bulk.payslips.list', '') }}/${jobId}`);
        const data = await response.json();

        if (data.status !== 'success') {
            throw new Error('Failed to get file list');
        }

        const files = data.files;
        let downloaded = 0;

        for (const file of files) {
            try {
                // Download file content
               
                const blob = await fileResponse.blob();

                // Create file in selected directory
                const fileHandle = await directoryHandle.getFileHandle(file.filename, { create: true });
                const writable = await fileHandle.createWritable();
                await writable.write(blob);
                await writable.close();

                downloaded++;
                updateProgress(
                    Math.round((downloaded / files.length) * 100),
                    `Saving files... ${downloaded}/${files.length}`,
                    `Downloaded to your selected folder`
                );
            } catch (err) {
                console.error(`Failed to save ${file.filename}:`, err);
            }
        }

        progressMessage.innerHTML += `<br><span class="text-success">${downloaded} files saved to your selected folder!</span>`;
    } catch (error) {
        alert('Error downloading files: ' + error.message);
    }
}

// Fallback: Show download links if File System Access API fails
async function showDownloadLinks(jobId) {
    try {
        const response = await fetch(`{{ route('bulk.payslips.list', '') }}/${jobId}`);
        const data = await response.json();

        if (data.status === 'success') {
            downloadLinksSection.style.display = 'block';
            
            data.files.forEach(file => {
                const link = document.createElement('a');
               
                link.className = 'list-group-item list-group-item-action';
                link.download = file.filename;
                link.textContent = file.filename;
                link.target = '_blank';
                downloadLinks.appendChild(link);
            });
        }
    } catch (error) {
        console.error('Error loading download links:', error);
    }
}
});
        </script>
</x-custom-admin-layout>    