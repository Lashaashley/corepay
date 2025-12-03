<x-custom-admin-layout>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <style>
   	.tab-container {
    display: flex;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
}

.tab-button {
    background-color: #f8f9fa;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 10px 20px;
    font-size: 14px;
    transition: background-color 0.3s;
}

.tab-button:hover {
    background-color: #e9ecef;
}

.tab-button.active {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff; /* Hide border bottom when active */
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
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

.toggle-container input {
  display: none;
}

.toggle-container label {
  display: inline-block;
  float: left;
  width: 50%;
  height: 100%;
  line-height: 34px;
  text-align: center;
  cursor: pointer;
  position: relative;
  z-index: 2;
  transition: color 0.3s;
  color: black;
}
.review-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .review-table th, .review-table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        .review-table th {
            background-color: #f4f4f4;
        }
        .review-controls {
            margin: 20px 0;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        #progressWrapper {
            width: 100%;
            background-color: #f3f3f3;
            border: 1px solid #ddd;
            margin-top: 10px;
            display: none;
        }

        #progressBar {
            width: 0%;
            height: 20px;
            background-color: #4caf50;
            text-align: center;
            color: white;
            line-height: 20px; /* Center text in progress bar */
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
	.btn-enhanced {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    cursor: pointer;
    min-width: 80px;
}

.btn-enhanced:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
}

.btn-final {
            background: linear-gradient(135deg, #667eea 0%, #764ba2);
            color: white;
        }
@keyframes slideIn {
    from { right: -100px; opacity: 0; }
    to { right: 20px; opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
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
        <div class="pd-ltr-20">
            <h1 class="header-container">Import Agents</h1>
            <div class="tab-container" style="margin-top: -20px;">
                
                
            </div>
            <div class="card-box pd-20 height-100-p mb-30">
           <div class="card-body">
                    <form id="import-form" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="excelFile" class="form-label">Select Excel File</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="excelFile" 
                                   name="excelFile" 
                                   accept=".xlsx,.xls" 
                                   required>
                            <small class="form-text text-muted">Supported formats: .xlsx, .xls (Max: 10MB)</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Import Mode</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="importMode" 
                                           id="updateMode" 
                                           value="update" 
                                           checked>
                                    <label class="form-check-label" for="updateMode">
                                        <strong>Update Mode</strong>
                                        <br><small class="text-muted">Updates existing records and adds new ones</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="importMode" 
                                           id="freshMode" 
                                           value="fresh">
                                    <label class="form-check-label" for="freshMode">
                                        <strong>Fresh Import</strong>
                                        <br><small class="text-muted">Deletes all existing payroll records for the period first</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="upload-btn" class="btn btn-enhanced btn-final">
                            <i class="fa fa-upload mr-2"></i>Import Deductions
                        </button>
                    </form>

                    <div id="result-message" class="mt-3"></div>
                </div>
            </div>
            
            
            
        </div>
        <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
            <h5 class="text-center mb-4">Review</h5>
            <div id="reviewArea">
                <div class="alert alert-info" id="initialMessage">
                    Please select an Excel file to review its contents.
                </div>
                <div id="tableContainer"></div>
                <div class="review-controls" style="display: none;">
                    
                    <button class="btn btn-secondary" id="cancelUpload">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div id="progress-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div class="card" style="width: 500px; max-width: 90%;">
        <div class="card-body">
            <h5 class="card-title mb-3">Importing Deductions</h5>
            
            <div class="progress mb-2" style="height: 30px;">
                <div id="progress-bar" 
                     class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: 0%;">
                    0%
                </div>
            </div>
            
            <p id="progress-message" class="text-muted mb-0">Starting upload...</p>
            
            <div id="error-list" class="mt-3" style="display: none;">
                <h6 class="text-danger">Errors encountered:</h6>
                <ul id="error-items" class="text-sm"></ul>
            </div>
            
            <button id="close-modal-btn" class="btn btn-secondary mt-3">
                Close
            </button>
        </div>
    </div>
</div>
    
    

    
    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    
    <script>

    document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('import-form');
    const fileInput = document.getElementById('excelFile');
    const uploadBtn = document.getElementById('upload-btn');
    const progressModal = document.getElementById('progress-modal');
    const progressBar = document.getElementById('progress-bar');
    const progressMessage = document.getElementById('progress-message');
    const resultMessage = document.getElementById('result-message');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const errorList = document.getElementById('error-list');
    const errorItems = document.getElementById('error-items');

    function handleFinalResponse(response) {
        console.log('Final response received:', response);
        
        progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
        
        if (response.status === 'completed' || response.status === 'success') {
            progressBar.classList.add('bg-success');
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            progressMessage.textContent = response.message;
            
            let resultHtml = `
                <div class="alert alert-success">
                    <strong>Import Complete!</strong><br>
                    ✓ ${response.success || 0} records processed successfully<br>
                    ${response.errors ? `✗ ${response.errors} errors encountered` : ''}
                </div>
            `;
            
            // Show download button for missing employees
            if (response.hasMissingEmployees && response.downloadUrl && response.downloadToken) {
                resultHtml += `
                    <div class="alert alert-warning mt-3">
                        <strong>⚠️ Missing Employees Found!</strong><br>
                        ${response.missingEmployeesCount} employee(s) from your import file do not exist in the database.
                        <br><br>
                        <a href="${response.downloadUrl}" class="btn btn-warning btn-sm" download>
                            <i class="fas fa-download"></i> Download Missing Employees Report
                        </a>
                    </div>
                `;
            }
            
            resultMessage.innerHTML = resultHtml;
            
            // ✅ ADD: Auto-close modal after delay
            const hasMissingEmployees = response.hasMissingEmployees && response.missingEmployeesCount > 0;
            const hasErrors = response.errors && response.errors > 0;
            
            // Auto-close conditions:
            // - If no missing employees AND no errors: close quickly (3 seconds)
            // - If missing employees OR errors: close after longer delay (8 seconds)
            // - User can always close manually with the close button
            
            let autoCloseDelay = 2000; // 3 seconds default
            
            if (hasMissingEmployees || hasErrors) {
                autoCloseDelay = 2000; // 8 seconds if there are issues to review
            }
            
            console.log(`Auto-closing modal in ${autoCloseDelay/1000} seconds`);
            
            setTimeout(() => {
                progressModal.style.display = 'none';
                form.reset();
                uploadBtn.disabled = false;
                console.log('Modal auto-closed');
            }, autoCloseDelay);
            
        } else {
            progressBar.classList.add('bg-danger');
            progressMessage.textContent = response.message || 'Import failed';
            
            resultMessage.innerHTML = `
                <div class="alert alert-danger">
                    <strong>Import Failed!</strong><br>
                    ${response.message}
                </div>
            `;
            
            // ✅ ADD: Auto-close for errors too (after 5 seconds)
            setTimeout(() => {
                progressModal.style.display = 'none';
                form.reset();
                uploadBtn.disabled = false;
            }, 5000);
        }
        
        closeModalBtn.style.display = 'block';
        uploadBtn.disabled = false;
    }

    // Close modal button
    closeModalBtn.addEventListener('click', function() {
        progressModal.style.display = 'none';
        form.reset();
        uploadBtn.disabled = false;
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!fileInput.files.length) {
            alert('Please select a file');
            return;
        }

        const formData = new FormData();
        formData.append('excelFile', fileInput.files[0]);
        formData.append('importMode', document.querySelector('input[name="importMode"]:checked').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Reset UI
        progressModal.style.display = 'flex';
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
        progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated';
        progressMessage.textContent = 'Starting upload...';
        resultMessage.innerHTML = '';
        errorList.style.display = 'none';
        errorItems.innerHTML = '';
        closeModalBtn.style.display = 'none';
        uploadBtn.disabled = true;

        // Create XHR for streaming
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("deductions.import.process") }}', true);

        let isProcessing = false;
        let buffer = '';

        // Upload progress
        xhr.upload.addEventListener('progress', (event) => {
            if (event.lengthComputable && !isProcessing) {
                const percentComplete = Math.round((event.loaded / event.total) * 100);
                progressBar.style.width = `${percentComplete}%`;
                progressBar.textContent = `${percentComplete}%`;
                progressMessage.textContent = `Uploading file: ${percentComplete}%`;
            }
        });

        // Processing progress (streaming response)
        xhr.onprogress = () => {
            isProcessing = true;
            buffer += xhr.responseText.substring(buffer.length);

            const lines = buffer.split('\n');
            buffer = lines.pop(); // Keep incomplete line in buffer

            lines.forEach(line => {
                if (!line.trim()) return;

                try {
                    const response = JSON.parse(line);
                    console.log('Stream response:', response);
                    
                    if (response.status === 'progress') {
                        progressBar.style.width = `${response.progress}%`;
                        progressBar.textContent = `${response.progress}%`;
                        progressMessage.textContent = response.message;
                        
                        if (response.success !== undefined) {
                            progressMessage.textContent += ` (✓ ${response.success} success, ✗ ${response.errors} errors)`;
                        }
                    }
                    
                    // Handle completion
                    if (response.status === 'completed' || response.status === 'success') {
                        console.log('Completion detected, calling handleFinalResponse');
                        handleFinalResponse(response);
                    }
                } catch (e) {
                    if (line.trim().startsWith('{')) {
                        console.warn('Error parsing progress:', line, e);
                    }
                }
            });
        };

        // Request complete
        xhr.onload = () => {
            console.log('XHR onload triggered');
            // Process any remaining buffer
            if (buffer.trim()) {
                try {
                    const response = JSON.parse(buffer);
                    console.log('Final buffer response:', response);
                    handleFinalResponse(response);
                } catch (e) {
                    console.error('Error parsing final response:', e);
                    handleFinalResponse({
                        status: 'error',
                        message: 'Failed to parse server response'
                    });
                }
            } else {
                console.log('No buffer content');
            }
        };

        xhr.onerror = () => {
            console.error('XHR error occurred');
            progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
            progressBar.classList.add('bg-danger');
            progressMessage.textContent = 'Upload failed. Please try again.';
            closeModalBtn.style.display = 'block';
            uploadBtn.disabled = false;
        };

        xhr.send(formData);
    });
});


document.getElementById('excelFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });

        // Get the first worksheet
        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];

        // Convert to JSON
        const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

        // Create table
        const table = document.createElement('table');
        table.className = 'review-table';

        // Create header row
        const header = table.createTHead();
        const headerRow = header.insertRow();
        jsonData[0].forEach(cell => {
            const th = document.createElement('th');
            th.textContent = cell;
            headerRow.appendChild(th);
        });

        // Create data rows (Limit to first 10 records)
        const tbody = table.createTBody();
        for (let i = 1; i < Math.min(jsonData.length, 11); i++) { // Show up to 10 records
            const row = tbody.insertRow();
            jsonData[i].forEach(cell => {
                const td = row.insertCell();
                td.textContent = cell;
            });
        }

        // Clear previous content and show new table
        document.getElementById('initialMessage').style.display = 'none';
        const tableContainer = document.getElementById('tableContainer');
        tableContainer.innerHTML = '';
        tableContainer.appendChild(table);

        // Show controls
        document.querySelector('.review-controls').style.display = 'block';
    };

    reader.readAsArrayBuffer(file);
});


        document.getElementById('cancelUpload').addEventListener('click', function() {
            document.getElementById('excelFile').value = '';
            document.getElementById('tableContainer').innerHTML = '';
            document.getElementById('initialMessage').style.display = 'block';
            document.querySelector('.review-controls').style.display = 'none';
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