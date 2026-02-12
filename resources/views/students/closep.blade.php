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
        .header-container{
    font-size: 1.5rem;
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center;

}
.load-button-container {
    margin-top: 20px;
   
}
#progress-modal {
  display: none; /* Hide by default */
}

#progress-modal .modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000; /* Ensure the modal is on top of other content */
}

#progress-modal .modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  text-align: center;
  width: 300px; /* Adjust width as needed */
}

#progress-modal #progress-bar-container {
  width: 100%;
  background-color: #f3f3f3;
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
  height: 20px;
  margin-top: 10px;
}

#progress-modal #progress-bar {
  height: 100%;
  background-color: #4caf50;
  width: 0%;
}
.toggle-container {
  position: relative;
  display: inline-block;
  width: 200px;
  height: 34px;
  background-color: #f0f0f0;
  border-radius: 34px;
  overflow: hidden;
  border: 1px solid #ccc;
  margin-bottom: 5px;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #f2140c;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
}

.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #0cf24d;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

    .list-container {
    transition: all 0.3s ease-in-out;
    max-height: 2000px; /* Adjust based on your content */
    opacity: 1;
    overflow: hidden;
}

.list-container[data-status="Off"] {
    max-height: 0;
    opacity: 0;
    padding: 0;
    margin: 0;
}

/* Additional Responsive Styles */
@media (max-width: 768px) {
    .toggle-switch {
        width: 50px;
        height: 28px;
    }
    
    .slider:before {
        height: 20px;
        width: 20px;
    }
}
   /* Email Progress Styles */
.email-progress-container {
    padding: 10px;
}

.email-stats {
    font-size: 14px;
    font-weight: 500;
}

.email-stats .stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

.email-stats i {
    font-size: 20px;
    margin-bottom: 5px;
}

#swal-progress-bar {
    transition: width 0.3s ease;
    font-weight: bold;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.completion-summary .stats-grid {
    animation: fadeInUp 0.5s ease;
}

.completion-summary .stat-card {
    transition: transform 0.2s ease;
}

.completion-summary .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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
    </style>
    
    <!-- Make sure CSS is loaded before content -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <div class="mobile-menu-overlay"></div>
    <div class="pd-ltr-20" style="margin-top: -35px;">
        <h1 class="header-container">Close Period</h1>
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
                                        
                                    <button id="load" type="submit" class="btn btn-enhanced btn-finalize">
                                    <i class="fas fa-save"></i> Close period
                                </button></div>
                                </div>
                            </div>

                        </div>
                        
                    </div>
                    
                    

                </div>
            </div>
            <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">Notify Agents</h5>
                    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bulk Payslip Generation</h3>
                </div>
                <div class="card-body">
                    <form id="bulkPayslipForm">
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="period">Select Period *</label>
                <select class="form-control" id="period" name="period" required>
                    <option value="">Select Period</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label>Download Method *</label>
                <select class="form-control" id="download_method" name="download_method" required>
                    <option value="zip">Download as ZIP file</option>
                    <option value="individual">Download individually (select folder)</option>
                </select>
                <small class="form-text text-muted">
                    ZIP: Single file with all payslips<br>
                    Individual: Choose folder to save each PDF separately
                </small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="send_email" name="send_email" value="1">
                    <label class="form-check-label" for="send_email">
                        <strong>Send via Email</strong>
                    </label>
                </div>
                <small class="form-text text-muted">
                    🔒 Payslip password: Employee's KRA PIN
                </small>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary" id="generateBtn">
            <i class="fas fa-download"></i> Generate & Download Payslips
        </button>
    </div>
</form>
                    
                    <!-- Progress Section -->
                    <div id="progressSection" style="display: none;">
    <hr>
    <h5>Generation Progress</h5>
    <div class="progress mb-3">
        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
             role="progressbar" style="width: 0%"></div>
    </div>
    <div id="progressDetails">
        <p id="progressMessage">Starting...</p>
        <p id="progressStats"></p>
    </div>
    
    <!-- Download Links Section (for individual downloads) -->
    <div id="downloadLinksSection" style="display: none;">
        <hr>
        <h5>Download Files</h5>
        <div id="downloadLinks" class="list-group"></div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
</div>
                </div>
                
        </div>
    
    <div id="successMessage" style="display:none;"></div>
<!-- Modal Structure -->
<!-- Modal Structure -->
<div id="progress-modal">
  <div class="modal-overlay">
    <div class="modal-content">
      <h4>Sending</h4>
      <div id="progress-bar-container">
        <div id="progress-bar"></div>
      </div>
      <p id="progress-message">sending...</p>
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