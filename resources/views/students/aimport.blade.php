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
            <div id="earnings" class="tab-content active" style="margin-top: -20px;">
        <div class="card-box pd-20 height-100-p mb-30">
            <div class="row align-items-center">
                <div class="col-md-4 user-icon">
                    <div class="mb-20">
                    <a href="{{ route('import.template') }}" class="btn btn-info">
                        <i class="fa fa-download"></i> Download Template
                    </a>
                </div>

                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Select Excel File <span class="text-danger">*</span></label>
                        <input type="file" 
                               name="excelFile" 
                               id="excelFile" 
                               class="form-control-file" 
                               accept=".xlsx,.xls" 
                               required>
                        <small class="form-text text-muted">
                            Accepted formats: .xlsx, .xls (Max: 10MB)
                        </small>
                    </div>

                    <button type="submit" class="btn btn-enhanced btn-final" id="uploadBtn">
                        <i class="fa fa-upload"></i> Upload and Import
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
            
            
            
        </div>
    </div>
    <div id="progress-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 10px; min-width: 400px;">
            <h5 class="mb-3">Importing Data...</h5>
            <div class="progress" style="height: 30px;">
                <div id="progress-bar" 
                     class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: 0%">0%</div>
            </div>
            <p id="progress-message" class="mt-2 mb-0">Starting upload...</p>
        </div>
    </div>
    
    

    
    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    
    <script>

      document.getElementById('agntsimport').addEventListener('submit', function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('excelFile');
            const uploadBtn = document.getElementById('uploadBtn');
            
           const filePath = fileInput.value;
    const maxFileSize = 10 * 1024 * 1024; // 2MB limit

    // Validation checks
    if (!fileInput.files[0]) {
        showMessage('Please select a file to upload.', true);
        return;
    }

    // File extension validation
    const allowedExtensions = /(\.xlsx|\.xls)$/i;
    if (!allowedExtensions.test(filePath)) {
        showMessage('Please upload a file with extensions .xlsx or .xls only.', true);
        fileInput.value = '';
        return;
    }

    // File size validation
    if (fileInput.files[0].size > maxFileSize) {
        showMessage('File size exceeds 2MB limit.', true);
        fileInput.value = '';
        return;
    }

            const formData = new FormData();
            formData.append('excelFile', fileInput.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            const progressModal = document.getElementById('progress-modal');
            const progressBar = document.getElementById('progress-bar');
            const progressMessage = document.getElementById('progress-message');
            const resultMessage = document.getElementById('result-message');

            // Show progress modal
            progressModal.style.display = 'flex';
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            progressMessage.textContent = 'Starting upload...';
            uploadBtn.disabled = true;

            // Create XHR
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("import.employees.upload") }}', true);

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

            // Processing progress
            xhr.onprogress = () => {
                isProcessing = true;
                buffer += xhr.responseText.substring(buffer.length);

                const lines = buffer.split('\n');
                buffer = lines.pop();

                lines.forEach(line => {
                    if (!line.trim()) return;

                    try {
                        const response = JSON.parse(line);
                        
                        if (response.status === 'progress') {
                            progressBar.style.width = `${response.progress}%`;
                            progressBar.textContent = `${response.progress}%`;
                            progressMessage.textContent = response.message;
                            
                            if (response.success !== undefined) {
                                progressMessage.textContent += ` (✓ ${response.success} success, ✗ ${response.errors} errors)`;
                            }
                        }
                    } catch (e) {
                        if (line.trim().startsWith('{')) {
                            console.warn('Error parsing progress:', line);
                        }
                    }
                });
            };

            // Complete
            xhr.onload = () => {
                uploadBtn.disabled = false;
                
                try {
                    const lines = xhr.responseText.trim().split('\n');
                    const lastLine = lines[lines.length - 1];
                    const response = JSON.parse(lastLine);

                    if (response.status === 'success') {
                        progressBar.classList.remove('progress-bar-animated');
                        progressBar.classList.add('bg-success');
                        progressMessage.textContent = response.message;
                        
                        resultMessage.innerHTML = `
                            <div class="alert alert-success">
                                <strong>Success!</strong> ${response.message}<br>
                                <small>Total: ${response.total} | Success: ${response.success} | Errors: ${response.errors}</small>
                            </div>
                        `;

                        setTimeout(() => {
                            progressModal.style.display = 'none';
                            document.getElementById('importForm').reset();
                        }, 2000);
                    } else {
                        progressBar.classList.add('bg-danger');
                        progressMessage.textContent = 'Import failed';
                        
                        resultMessage.innerHTML = `
                            <div class="alert alert-danger">
                                <strong>Error!</strong> ${response.message}
                            </div>
                        `;
                        
                        setTimeout(() => progressModal.style.display = 'none', 2000);
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    resultMessage.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Error!</strong> Failed to process response
                        </div>
                    `;
                    progressModal.style.display = 'none';
                }
            };

            // Error
            xhr.onerror = () => {
                uploadBtn.disabled = false;
                progressModal.style.display = 'none';
                resultMessage.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Error!</strong> Network error occurred
                    </div>
                `;
            };

            xhr.send(formData);
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