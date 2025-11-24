
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
        formData.append('_token', '{{ csrf_token() }}');

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
        xhr.open('POST', autocalc, true);

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
                    
                    if (response.status === 'progress') {
                        progressBar.style.width = `${response.progress}%`;
                        progressBar.textContent = `${response.progress}%`;
                        progressMessage.textContent = response.message;
                        
                        if (response.success !== undefined) {
                            progressMessage.textContent += ` (✓ ${response.success} success, ✗ ${response.errors} errors)`;
                        }
                    }
                    
                    // Handle completion
                    if (response.status === 'completed') {
                        handleFinalResponse(response);
                    }
                } catch (e) {
                    if (line.trim().startsWith('{')) {
                        console.warn('Error parsing progress:', line);
                    }
                }
            });
        };

        // Request complete
        xhr.onload = () => {
            // Process any remaining buffer
            if (buffer.trim()) {
                try {
                    const response = JSON.parse(buffer);
                    handleFinalResponse(response);
                } catch (e) {
                    console.error('Error parsing final response:', e);
                    handleFinalResponse({
                        status: 'error',
                        message: 'Failed to parse server response'
                    });
                }
            } else {
                // If no buffer but request is complete, assume success
                handleFinalResponse({
                    status: 'completed',
                    message: 'Import process finished',
                    success: 0,
                    errors: 0
                });
            }
        };

        xhr.onerror = () => {
            progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
            progressBar.classList.add('bg-danger');
            progressMessage.textContent = 'Upload failed. Please try again.';
            closeModalBtn.style.display = 'block';
            uploadBtn.disabled = false;
        };

        xhr.send(formData);
    });

    function handleFinalResponse(response) {
        // Remove animation from progress bar
        progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
        
        if (response.status === 'completed' || response.status === 'success') {
            progressBar.classList.add('bg-success');
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            progressMessage.textContent = response.message || 'Import completed successfully!';
            
            resultMessage.innerHTML = `
                <div class="alert alert-success">
                    <strong>Success!</strong> ${response.message || 'Import completed successfully!'}
                </div>
            `;

            // Show errors if any
            if (response.errorDetails && response.errorDetails.length > 0) {
                errorList.style.display = 'block';
                response.errorDetails.forEach(error => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item text-danger';
                    li.textContent = `Row ${error.row}: ${error.message}`;
                    errorItems.appendChild(li);
                });
            }
            
            // Auto-close modal after 3 seconds if no errors
            if (!response.errorDetails || response.errorDetails.length === 0) {
                setTimeout(() => {
                    progressModal.style.display = 'none';
                    form.reset();
                    uploadBtn.disabled = false;
                }, 3000);
            }
            
        } else if (response.status === 'error') {
            progressBar.classList.add('bg-danger');
            progressMessage.textContent = 'Import failed!';
            
            resultMessage.innerHTML = `
                <div class="alert alert-danger">
                    <strong>Error!</strong> ${response.message}
                </div>
            `;
        }

        // Show close button
        closeModalBtn.style.display = 'block';
        uploadBtn.disabled = false;
    }
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