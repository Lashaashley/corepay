  document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const fileInput = document.getElementById('excelFile');
    const uploadBtn = document.getElementById('uploadBtn');
    
    const filePath = fileInput.value;
    const maxFileSize = 10 * 1024 * 1024; // 10MB limit

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
        showMessage('File size exceeds 10MB limit.', true); // Fixed: was showing 2MB
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
    xhr.open('POST','{{ route("import.employees.upload") }}', true);

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