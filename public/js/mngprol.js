  if (typeof Swal === 'undefined') {
    console.warn('Local SweetAlert2 not found, using CDN');
    // CDN will define Swal globally
}
        const currentMonth = $('#currentMonth').val();
const currentYear = $('#currentYear').val();
 
// Add event listener to the button
document.getElementById('preview-totals-btn').addEventListener('click', function() {
    // Get current month and year (make sure these variables are defined)
    const currentMonth = $('#currentMonth').val();
const currentYear = $('#currentYear').val();
    
    // Confirmation message
    const confirmationMessage = `Are you sure you want to process totals for ${currentMonth} ${currentYear}?`;

    // Use SweetAlert for confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: confirmationMessage,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, process totals!',
        cancelButtonText: 'No, cancel!',
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            processPayrollTotals(currentMonth, currentYear);
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Cancelled',
                text: 'Processing was cancelled.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
});

/**
 * Process payroll totals with SSE progress tracking
 */
function processPayrollTotals(month, year) {
    // Show progress modal
    Swal.fire({
        title: 'Processing Payroll',
        html: `
            <div class="progress" style="height: 25px;">
                <div id="swal-progress-bar" 
                     class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: 0%;" 
                     aria-valuenow="0" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    0%
                </div>
            </div>
            <p id="swal-progress-message" class="mt-3 mb-0">Initializing process...</p>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Create EventSource for SSE
     const url = autocalc + "?month=" + encodeURIComponent(month) + "&year=" + encodeURIComponent(year) + "&t=" + Date.now();
    const evtSource = new EventSource(url);
    
    let lastPercent = 0;
    
    // Progress update event
    evtSource.addEventListener('progress', function(event) {
        try {
            const data = JSON.parse(event.data);
            updateProgressBar(data.percent, data.message);
            lastPercent = data.percent;
        } catch (e) {
            console.error('Error parsing progress event:', e);
        }
    });
    
    // Completion event
    evtSource.addEventListener('complete', function(event) {
        try {
            const data = JSON.parse(event.data);
            
            // Update to 100%
            updateProgressBar(100, 'Processing complete!');
            
            // Close connection
            evtSource.close();
            
            // Show success message
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Processing Completed!',
                    html: `
                        <p>${data.message || 'Totals processed successfully!'}</p>
                        ${data.totalGrossPays ? `<p class="mb-0"><strong>Total Gross Pays:</strong> ${formatCurrency(data.totalGrossPays)}</p>` : ''}
                    `,
                    confirmButtonText: 'OK',
                    timer: 5000
                }).then(() => {
                    // Optionally reload or redirect
                    // window.location.reload();
                });
            }, 500);
        } catch (e) {
            console.error('Error parsing complete event:', e);
            evtSource.close();
            showError('Processing completed but response parsing failed.');
        }
    });
    
    // Error event from server
    evtSource.addEventListener('error', function(event) {
        console.error('SSE error event:', event);
        evtSource.close();
        
        let errorMessage = 'An error occurred while processing totals.';
        
        try {
            if (event.data) {
                const data = JSON.parse(event.data);
                if (data && data.message) {
                    errorMessage = data.message;
                }
            }
        } catch (e) {
            console.error('Error parsing error event data:', e);
        }
        
        showError(errorMessage);
    });
    
    // Connection error handler
    evtSource.onerror = function(error) {
        console.error('EventSource connection error:', error);
        
        // Only show error if we haven't completed successfully
        if (lastPercent < 100) {
            evtSource.close();
            
            // Check if it's a connection error vs server error
            if (evtSource.readyState === EventSource.CLOSED) {
                showError('Connection lost. Please check your internet connection and try again.');
            } else {
                showError('An unexpected error occurred. Please try again.');
            }
        }
    };
}

/**
 * Update progress bar and message
 */
function updateProgressBar(percent, message) {
    const progressBar = document.getElementById('swal-progress-bar');
    const progressMessage = document.getElementById('swal-progress-message');
    
    if (progressBar) {
        progressBar.style.width = percent + '%';
        progressBar.setAttribute('aria-valuenow', percent);
        progressBar.textContent = Math.round(percent) + '%';
    }
    
    if (progressMessage) {
        progressMessage.textContent = message;
    }
}

/**
 * Show error message
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Processing Error',
        text: message,
        confirmButtonText: 'OK'
    });
}
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES'
    }).format(amount);
}
    let currentPage = 1;
        let currentSearch = '';

        // Load deductions
        function loadDeductions(page = 1, search = '') {
            currentPage = page;
            currentSearch = search;

            $.ajax({
                url: getwuth,
                type: 'GET',
                data: {
                    page: page,
                    search: search,
                    per_page: 10
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#tableBody').html(`
                        <tr>
                            <td colspan="7" class="text-center">
                                <i class="fa fa-spinner fa-spin"></i> Loading...
                            </td>
                        </tr>
                    `);
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Update period info
                        $('#period-info').html(`
                            <i class="fa fa-calendar"></i> 
                            <strong>Active Period:</strong> ${response.period.month} ${response.period.year}
                        `);

                        // Update table
                        renderTable(response.data);

                        // Update pagination
                        renderPagination(response.pagination);

                        // Update record count
                        $('#total-records').text(`Total Records: ${response.pagination.total}`);
                        $('#showing-info').text(
                            `Showing ${response.pagination.from || 0} to ${response.pagination.to || 0} of ${response.pagination.total} entries`
                        );
                    } else {
                        $('#tableBody').html(`
                            <tr>
                                <td colspan="7" class="text-center text-danger">
                                    ${response.message}
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Load error:', error);
                    $('#tableBody').html(`
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                Error loading data. Please try again.
                            </td>
                        </tr>
                    `);
                }
            });
        }

        // Render table rows
        function renderTable(data) {
            if (data.length === 0) {
                $('#tableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center">No records found</td>
                    </tr>
                `);
                return;
            }

            let html = '';
            data.forEach(row => {
                html += `
                    <tr class="clickable-row" onclick="highlightRow(this)">
                        <td>${row.full_name}</td>
                        <td>${row.work_no}</td>
                        <td>${row.department}</td>
                        <td>${row.code}</td>
                        <td>${row.name}</td>
                        <td>${row.category}</td>
                        <td class="text-right">${row.amount}</td>
                    </tr>
                `;
            });
            $('#tableBody').html(html);
        }

        // Render pagination
        function renderPagination(pagination) {
            if (pagination.last_page <= 1) {
                $('#pagination').html('');
                return;
            }

            let html = '';
            
            // Previous button
            if (pagination.current_page > 1) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
                </li>`;
            }

            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                if (
                    i === 1 || 
                    i === pagination.last_page || 
                    (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)
                ) {
                    html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            // Next button
            if (pagination.current_page < pagination.last_page) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
                </li>`;
            }

            $('#pagination').html(html);
        }

        // Highlight row on click
        function highlightRow(row) {
            $(row).siblings().removeClass('table-active');
            $(row).addClass('table-active');
        }

        // Event handlers
        $(document).ready(function() {
            $('#contentTable2').DataTable({
        columnDefs: [
            { targets: 2, width: '80px' } // Department column
        ],
        autoWidth: false,
        data: [], // Start with empty data
        columns: [
            { title: "Name" },
            { title: "Work number" },
            { title: "Department" },
            { title: "Parameter Code" },
            { title: "Amount" }
        ]
    });

                $('#pitem').on('change', function() {
        var selectedParameter = $(this).val();
        if (selectedParameter) {
            fetchData(selectedParameter);
        } else {
            function clearTable() {
        $('#contentTable2 tbody').empty();
    }
        }
    });
            // Initial load
            loadDeductions();

            // Search button
            $('#searchBtn').on('click', function() {
                const search = $('#searchInput').val();
                loadDeductions(1, search);
            });

            // Clear button
            $('#clearBtn').on('click', function() {
                $('#searchInput').val('');
                loadDeductions(1, '');
            });

            // Search on Enter key
            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#searchBtn').click();
                }
            });

            // Pagination click
            $(document).on('click', '#pagination a', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) {
                    loadDeductions(page, currentSearch);
                }
            });
            $('.toggle-checkbox').on('change', function () {
    let checkbox = $(this);
    let isChecked = checkbox.is(':checked');
    let model = checkbox.data('model');
    let fieldName = checkbox.next('label').text();
    let action = isChecked ? 'activate' : 'deactivate';

    // Revert temporarily
    checkbox.prop('checked', !isChecked);

    if (confirm(`Are you sure you want to ${action} ${fieldName}?`)) {

        $.ajax({
            url: tstatus,
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                model: model,
                status: isChecked ? 'ACTIVE' : 'INACTIVE'
            },
            success: function (response) {
                checkbox.prop('checked', isChecked);
                showMessage(fieldName + " updated successfully.", false);
            },
            error: function () {
                alert("Error updating " + fieldName);
            }
        });

    }
});

$('#empmodal').on('click', function() {
   
    $.ajax({
        url: getcodes,
        type: "GET",
        success: function (response) {
            const dropdown = $('#pitem');
            dropdown.empty();
            dropdown.append('<option value="">Select Item</option>');
            response.data.forEach(function (pitem) {
                dropdown.append(
                    `<option data-code="${pitem.code}" data-category="${pitem.category}" data-increredu="${pitem.increREDU}" data-formular="${pitem.formularinpu}" value="${pitem.cname}">${pitem.code} - ${pitem.cname}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load streams. Please try again.');
        },
    });
 

    $('#pitem').select2({
        placeholder: "Select Item",
        allowClear: true,
        dropdownParent: $('#exampleModal'), // Ensures the dropdown is appended within the modal
        width: '100%'
    }).on('select2:open', function(e) {
        // Stop propagation of mousedown events on the Select2 dropdown to prevent modal closure
       
    });

     
    // Optional: Remove the mousedown event listener when the Select2 dropdown is closed
 

      // Check if Choices exists
    if (typeof Choices === 'undefined') {
        console.error('Choices.js is NOT loaded.');
        return;
    }

    // Initialize Choices
    const choices = new Choices('#searchValue', {
        searchEnabled: true,
        placeholderValue: 'Select Staff',
        searchPlaceholderValue: 'Search staff...',
        allowHTML: true,
        shouldSort: false,
        itemSelectText: '',
        noResultsText: 'No matching staff'
    });

    // --- Debounce helper ---
    function debounce(fn, delay) {
        let timer;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, arguments), delay);
        };
    }

    // --- AJAX search function ---
    function fetchStaff(term = '') {
        $.ajax({
            url: staffsearch,
            method: "GET",
            data: { term: term },
            dataType: 'json',
            success: function (data) {

                const results = data.results || [];

                const items = results.map(item => ({
                    value: item.emp_id,
                    label: item.label
                }));

                choices.clearChoices();
                choices.setChoices(items, 'value', 'label', true);
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
            }
        });
    }

    // We need to attach to Choices internal input field
    function attachSearchListener() {
        const internalInput = $('.choices__input');

        if (internalInput.length) {
            internalInput.on(
                'input',
                debounce(function () {
                    const term = $(this).val().trim();
                    fetchStaff(term);
                }, 300)
            );
        } else {
            // Choices builds DOM async, so retry
            setTimeout(attachSearchListener, 50);
        }
    }

    attachSearchListener();

    // Optional initial load
    fetchStaff('');
   
});

        });

        function populateCategory() {
    const amountField = document.getElementById('amount');
    const balanceField = document.getElementById('balance');
    var pitem = document.getElementById("pitem");
    var category = document.getElementById("category");
    var increREDU = document.getElementById("increREDU");
    var code = document.getElementById("codebal");
    var formular = document.getElementById("formular");
    var selectedOption = pitem.options[pitem.selectedIndex];

    amountField.readOnly = false; // Make the amount field read-only
    amountField.value = ''; 
    balanceField.readOnly = false; // Make the amount field read-only
    balanceField.value = ''; 
    
    // Set the hidden category input to the data-category attribute of the selected option
    category.value = selectedOption.getAttribute('data-category');

    // Set the hidden increREDU input to the data-increredu attribute of the selected option
    increREDU.value = selectedOption.getAttribute('data-increredu');
    code.value = selectedOption.getAttribute('data-code');
    formular.value = selectedOption.getAttribute('data-formular');

    toggleHiddenContainer();
    toggleHiddenContainer2();
    toggleHiddenContainer4();
    cleartxt2();
}
function toggleHiddenContainer() {
    var categoryValue = document.getElementById("category").value;
    var hiddenContainer = document.getElementById("hiddenContainer");
    var hiddenContainer2 = document.getElementById("hiddenContainer2");
    
    // Hide both containers by default
    hiddenContainer.style.display = 'none';
    hiddenContainer2.style.display = 'none';
    
    // Show the appropriate container based on the category
    if (categoryValue === 'loan') {
        hiddenContainer.style.display = 'block';
    } else if (categoryValue === 'balance') {
        hiddenContainer2.style.display = 'block';
    }
    // The else case is not needed as both containers are hidden by default
}
function toggleHiddenContainer2() {
    var categoryValue = document.getElementById("pitem").value;
    var hiddenContainer = document.getElementById("pensionContainer");

    if (categoryValue === 'Pension') {
        hiddenContainer.style.display = 'block';
    } else {
        hiddenContainer.style.display = 'none';
    }
}
function toggleHiddenContainer4() {
    var fValue = document.getElementById("formular").value.trim();
    var hiddenContainer = document.getElementById("otContainer");
    var balField = document.getElementById("balance");
    var amountField = document.getElementById("amount");
    var otdate = document.getElementById("otdate");
    

    if (fValue) {
        // When formular has value
        hiddenContainer.style.display = 'block';
        
        amountField.setAttribute('readonly', true);
        balField.setAttribute('readonly', true);
        
        otdate.setAttribute('required', true);
        
        
    } else {
        // When formular is empty
        $('#formular').val('');
        hiddenContainer.style.display = 'none';
        
       
    }
}
function cleartxt2(){
        $('#balance').val('');
        $('#balend').val('');
        $('#duration').val('');
        $('#quantity').val('');
        $('#camountf').val('');
    }
    function searchstaffdet(){ 
    performSearch();
}
function performSearch() {
    var searchCategory = $('#searchCategory').val();
    var searchValue    = $('#searchValue').val();
    var code           = $('#codebal').val();
    var category       = $('#category').val();
    var formula        = $('#formular').val();

    if (searchValue.trim() === '') {
        clearFields();
        return;
    }

    var codes = [];
    if (formula.trim() !== '') {
        codes = formula.match(/[A-Za-z]+\d+/g) || [];
    }

    $.ajax({
        url: staffdet,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            searchCategory: searchCategory,
            searchValue: searchValue,
            category: category,
            code: code,
            codes: codes
        },
        success: function(data) {
            if (!data.success) {
                clearFields();
                showMessage(data.message, true);
                return;
            }

            // set fields
            $('#surname').val(data.surname);
            $('#workNumber').val(data.workNumber);
            $('#othername').val(data.othername);
            $('#department').val(data.department);
            $('#departmentname').val(data.departmentname);

            $('#epmpenperce').val(data.epmpenperce);
            $('#emplopenperce').val(data.emplopenperce);
            $('#pensionable').val(data.totalPensionAmount);

            // Deduction amounts
            let amounts = [];
            codes.forEach(c => amounts.push(data.existingCodes[c] || ''));
            $('#camountf').val(amounts.join(','));

            if (category === 'balance') {
                $('#cbalance').val(data.balance);
                $('#balance').val(data.balance);
                $('#amount').val(data.Amount);

                let toggle = $('#activeinacToggle');
                let label  = $('#toggleLabel2');
                if (data.statdeduc === '1' || data.statdeduc === '') {
                    toggle.prop('checked', true);
                    label.text('Active');
                } else {
                    toggle.prop('checked', false);
                    label.text('Inactive');
                }
            }

            if (category === 'loan') {
                $('#balance').val(data.balance);
                $('#amount').val(data.Amount);

                let toggle = $('#activeinaclonToggle');
                let label  = $('#toggleLabel3');
                if (data.statdeduc === '1' || data.statdeduc === '') {
                    toggle.prop('checked', true);
                    label.text('Active');
                } else {
                    toggle.prop('checked', false);
                    label.text('Inactive');
                }
            }

            $('#amount').focus();
        },
        error: function(xhr, status, error) {
            showMessage("AJAX Error: " + status, true);
        }
    });
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
function fetchData(parameter) {

    $.ajax({
        url: fetchitems,
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            parameter: parameter
        },
        success: function(response) {
            if (!response.success) {
                console.log(response.message);
                return;
            }
         

            populateTable(response.data);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
function populateTable(data) {
    var table = $('#contentTable2').DataTable();
    table.clear();
    var totalAmount = 0;

    data.forEach(function(item) {
        var fullName = item.Surname + " " + item.othername;

        table.row.add([
            fullName,
            item.WorkNo,
            item.dept,
            item.PCode,
            item.Amount
        ]);

        totalAmount += parseFloat(item.Amount) || 0;
    });

    table.draw();
    $('#totalsvar').val(totalAmount.toFixed(2));
}






    document.addEventListener('DOMContentLoaded', function() {
        const toggleSwitch = document.getElementById('fixedOpenToggle');
        const toggleSwitch2 = document.getElementById('activeinacToggle');
        const toggleSwitch3 = document.getElementById('activeinaclonToggle');
        const toggleLabel = document.getElementById('toggleLabel');
        const toggleLabel2 = document.getElementById('toggleLabel2');
        const toggleLabel3 = document.getElementById('toggleLabel3');
        const fixedContainer = document.getElementById('Fixed');
        const openContainer = document.getElementById('Open');
        const amountField = document.getElementById('amount');
        const increREDUField = document.getElementById('increREDU'); // Reference to #increREDU
        function updateAmountFieldState() {
            if (increREDUField.value === 'Reducing') {
                amountField.readOnly = true; 
                amountField.value = '0'; 
            } else if (increREDUField.value === 'Increasing') {
                amountField.readOnly = false; 
                amountField.value = ''; 
            }
        }
        if (toggleSwitch) {
            toggleSwitch.addEventListener('change', function() {
                if (this.checked) {
                    toggleLabel.textContent = 'Open';
                    fixedContainer.style.display = 'none';
                    openContainer.style.display = 'block';
                } else {
                    toggleLabel.textContent = 'Fixed';
                    fixedContainer.style.display = 'block';
                    openContainer.style.display = 'none';
                }
                updateAmountFieldState(); 
                cleartxt2();
            });
        }
        if (toggleSwitch2) {
            toggleSwitch2.addEventListener('change', function() {
                if (this.checked) {
                    toggleLabel2.textContent = 'Active';
                } else {
                    toggleLabel2.textContent = 'Inactive';
                }
            });
        }
        if (toggleSwitch3) {
            toggleSwitch3.addEventListener('change', function() {
                if (this.checked) {
                    toggleLabel3.textContent = 'Active';
                } else {
                    toggleLabel3.textContent = 'Inactive';
                }
            });
        }
    });
    $('#viewp').on('click', function () {
    var staffid = $('#WorkNo').val();
    var selperiod = $('#periodPick').val();

    if (!staffid) {
        showMessage('Work Number cannot be empty', true);
        return;
    }

    if (!selperiod) {
        showMessage('Please select a period', true);
        return;
    }

    var [year, month] = selperiod.split('-');
    var monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    var monthName = monthNames[parseInt(month) - 1];
    var period = monthName + year;

    $.ajax({
        url: 'payview', // Your PHP route
        method: 'POST',
        data: { staffid: staffid, period: period },
        success: function (response) {
            if (response.pdf) {
                // Create PDF blob and show it in modal
                var pdfBlob = new Blob([Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))], { type: 'application/pdf' });
                var pdfUrl = URL.createObjectURL(pdfBlob);

                $('#pdfModal .modal-body').html(
                    `<iframe src="${pdfUrl}" width="100%" height="600px" style="border:none;"></iframe>`
                );
                $('#pdfModal').modal('show');
            } else {
                showMessage("PDF generation failed.", true);
            }
        },
        error: function () {
            showMessage("Error generating payslip.", true);
        }
    });
});



    document.getElementById('recalcButton').addEventListener('click', function() {
    var workNo = document.getElementById('WorkNo').value.trim();
    var month = document.getElementById('month').value;
    var year = document.getElementById('year').value;

    // Validate that WorkNo is not empty
    if (!workNo) {
        showMessage('Select a staff to process', true);
        return; // Exit the function if WorkNo is empty
    }

    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();

    // Configure it: POST-request to autocalc2.php
    xhr.open('POST', 'autocalc2.php', true);

    // Set the Content-Type header to JSON
    xhr.setRequestHeader('Content-Type', 'application/json');

    // Send the request with the data as JSON
    xhr.send(JSON.stringify({
        WorkNo: workNo,
        month: month,
        year: year
    }));

    // Define what happens on successful data submission
    xhr.onload = function() {
        if (xhr.status === 200) {
            showMessage('Recalculation successful');
            var response = JSON.parse(xhr.responseText);
            console.log(response);
            // You can add code here to handle the response from autocalc2.php
        } else {
            console.error('Recalculation failed. Status:', xhr.status);
            showMessage('Recalculation failed. Please try again.');
        }
    };
});
        $(document).ready(function() {
   
    $('#employeeModal').on('hidden.bs.modal', function () { 
        $('#employeeDataTable tbody').empty();
    });

    // Attach click event to table rows in employeeListTable
    /*$('#employeeListTable').on('click', 'tr', function() {
        var workNo = $(this).find('td:first').text(); // Get WorkNo from the first column of the clicked row
        $('#WorkNo').val(workNo); // Set the WorkNo input field
        performSearch2(); // Call the search function
        $('#employeeListModal').modal('hide'); // Hide the modal
    });*/

document.getElementById('inputPCode').addEventListener('keydown', function(event) {
    // Check if the key pressed is Tab (9) or Enter (13)
    if (event.key === 'Tab' || event.key === 'Enter') {
        var pCode = this.value;

        if (pCode.length > 0) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'sepcode.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        document.getElementById('inputname').value = response.cname;
                    } else {
                        document.getElementById('inputname').value = '';

                        // Populate the modal with the list of codes
                        var codeList = document.getElementById('codeList');
                        codeList.innerHTML = '';  // Clear previous list

                        response.codeList.forEach(function(codeObj) {
                            var li = document.createElement('li');
                            li.className = 'list-group-item';
                            li.textContent = codeObj.code + ' - ' + codeObj.cname;
                            li.setAttribute('data-code', codeObj.code);
                            li.setAttribute('data-cname', codeObj.cname);
                            codeList.appendChild(li);
                        });

                        // Show the modal
                        $('#codeSelectionModal').modal('show');
                    }
                }
            };
            xhr.send('PCode=' + encodeURIComponent(pCode));
        } else {
            document.getElementById('inputname').value = '';
        }

        // Prevent the default behavior of the Tab key (i.e., moving focus)
        if (event.key === 'Tab') {
            event.preventDefault();
        }
    }
});

// Handle code selection from the modal
document.getElementById('codeList').addEventListener('click', function(e) {
    if (e.target && e.target.nodeName == "LI") {
        var selectedCode = e.target.getAttribute('data-code');
        var selectedCname = e.target.getAttribute('data-cname');

        // Set the selected code and cname to the respective input fields
        document.getElementById('inputPCode').value = selectedCode;
        document.getElementById('inputname').value = selectedCname;

        // Hide the modal
        $('#codeSelectionModal').modal('hide');
    }
});


// Handle code selection from the modal
document.getElementById('codeList').addEventListener('click', function(e) {
    if (e.target && e.target.nodeName == "LI") {
        var selectedCode = e.target.getAttribute('data-code');
        var selectedCname = e.target.getAttribute('data-cname');

        // Set the selected code and cname to the respective input fields
        document.getElementById('inputPCode').value = selectedCode;
        document.getElementById('inputname').value = selectedCname;

        // Hide the modal
        $('#codeSelectionModal').modal('hide');
    }
});


// Search functionality


// Submit button functionality
document.getElementById('submitButton').addEventListener('click', function() {
    var id = document.getElementById('inputID').value;
    var pCode = document.getElementById('inputPCode').value;
    var name = document.getElementById('inputname').value;
    var amount = document.getElementById('inputAmount').value;
    var balance = document.getElementById('inputBalance').value;
    var WorkNo = document.getElementById('WorkNo').value;
    var month = document.getElementById('month').value;
    var year = document.getElementById('year').value;

    

    if (!pCode || !name || !amount || !balance || !WorkNo) {
        showErrorMessage('Please fill in the required inputs');
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'subEpcode.php', true); 
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                showMessage('Payroll Item submitted ', false);
                
                console.log(response);
                // Clear the form
                document.getElementById('inputID').value = '';
                document.getElementById('inputPCode').value = '';
                document.getElementById('inputname').value = '';
                document.getElementById('inputAmount').value = '';
                document.getElementById('inputBalance').value = '';
                // Refresh the table
                performSearch2(); // Use this instead of loadEmployeeData()
            } else {
                alert('Error submitting data: ' + response.message);
            }
        }
    };
    xhr.send('id=' + encodeURIComponent(id) +
         '&pCode=' + encodeURIComponent(pCode) +
         '&name=' + encodeURIComponent(name) +
         '&amount=' + encodeURIComponent(amount) +
         '&balance=' + encodeURIComponent(balance) +
         '&WorkNo=' + encodeURIComponent(WorkNo) +
         '&month=' + encodeURIComponent(month) +
         '&year=' + encodeURIComponent(year));
});

function highlightAndPopulateRow(row) {
    $('#employeeDataTable tbody tr').removeClass('highlighted');
    $(row).addClass('highlighted');
    
    $('#inputID').val($(row).find('td:eq(0)').text());
    $('#inputPCode').val($(row).find('td:eq(1)').text());
    $('#inputname').val($(row).find('td:eq(2)').text());
    $('#inputAmount').val($(row).find('td:eq(3)').text());
    $('#inputBalance').val($(row).find('td:eq(4)').text());
}

function highlightRow(row) {
    $('#employeeListTable tbody tr').removeClass('highlighted');
    $(row).addClass('highlighted');
    
    $('#WorkNo').val($(row).find('td:eq(0)').text());
    $('#employeeListModal').modal('hide');
    
    performSearch2();
}

$('<style>')
    .prop("type", "text/css")
    .html(`
        .highlighted {
            background-color: #e6f3ff !important;
        }
    `)
    .appendTo("head");

$('#searchButton').on('click', performSearch2);




// Add event listener for selecting an employee from the list
/*$(document).on('click', '.select-employee', function(e) {
    e.preventDefault();
    var workNo = $(this).data('workno');
    $('#WorkNo').val(workNo);
    $('#employeeListModal').modal('hide');
    performSearch2();
});*/
});
function highlightAndPopulateRow(row) {
    // Remove highlight from all rows
    $('#employeeDataTable tbody tr').removeClass('highlight');
    
    // Add highlight to clicked row
    $(row).addClass('highlight');
    
    // Populate input fields
    $('#inputID').val($(row).find('td:eq(0)').text());
    $('#inputPCode').val($(row).find('td:eq(1)').text());
    $('#inputname').val($(row).find('td:eq(2)').text());
    $('#inputAmount').val($(row).find('td:eq(3)').text());
    $('#inputBalance').val($(row).find('td:eq(4)').text());
}

function cleartxt(){
                    $('#inputID').val('');
                    $('#inputPCode').val('');
                    $('#inputname').val('');
                    $('#inputAmount').val('');
                    $('#inputBalance').val('');
}
    // Get the current date, month, and year










     function highlightRow(row) {
    var tableRows = document.getElementById('contentTable').getElementsByTagName('tr');
    for (var i = 0; i < tableRows.length; i++) {
        tableRows[i].classList.remove('highlight');
    }
    row.classList.add('highlight');
}

function showErrorMessage(message) {
    let errorDiv = document.getElementById('errorMessage');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'errorMessage';
        document.body.appendChild(errorDiv);
    }
    
    errorDiv.textContent = message;
    errorDiv.style.position = 'fixed';
    errorDiv.style.top = '20px';
    errorDiv.style.left = '20px';
    errorDiv.style.backgroundColor = '#f44336';
    errorDiv.style.color = 'white';
    errorDiv.style.padding = '15px';
    errorDiv.style.borderRadius = '5px';
    errorDiv.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
    errorDiv.style.zIndex = '1051'; 
    errorDiv.style.display = 'block';
    
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 2000);
}

document.addEventListener('DOMContentLoaded', function() {
    var rows = document.querySelectorAll("#contentTable tbody tr");
    var viewButton = document.querySelector('.btn-info');

    rows.forEach(function(row) {
        row.addEventListener("click", function() {
            highlightRow(this);
        });
    });

    viewButton.addEventListener("click", function() {
        var selectedRow = document.querySelector("#contentTable tbody tr.highlight");
        if (selectedRow) {
            var loanshares = selectedRow.cells[5].textContent; 
            var workNo = selectedRow.cells[1].textContent; 
            var pCode = selectedRow.cells[3].textContent; 

            if (loanshares.toLowerCase() === 'loan') {
                window.location.href = 'loansched.php?empid=' + encodeURIComponent(workNo) + '&loantype=' + encodeURIComponent(pCode);
            } else {
                showErrorMessage('Please select a loan category.');
            }
        } else {
            showErrorMessage('Please select a row first.');
        }
    });
});
function calcbalancedates(){

    const balanceInput = document.getElementById('balance');
    const durationInput = document.getElementById('duration');
    const amountInput = document.getElementById('amount');

    const balance = parseFloat(balanceInput.value);
    const duration = parseFloat(durationInput.value);

    if (!isNaN(balance) && !isNaN(duration)) {
        const amount = balance / duration;
        amountInput.value = amount.toFixed(2);
        updateEndDate2(duration);
    }

}
function updateEndDate2(duration) {
    const endDateInput = document.getElementById('balend');
    const currentDate = new Date();
    
    // Calculate the end date and subtract one month
    const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + Math.ceil(duration), 0);
    
    const endMonth = endDate.toLocaleString('default', { month: 'long' });
    const endYear = endDate.getFullYear();
    
    endDateInput.value = `${endMonth} ${endYear}`;
}
       function calculateMonthsAndEndDate() {
    const balanceInput = document.getElementById('balance');
    const amountInput = document.getElementById('amount');
    const monthsInput = document.getElementById('months');
    const endDateInput = document.getElementById('enddate');

    const balance = parseFloat(balanceInput.value);
    const amount = parseFloat(amountInput.value);

    if (!isNaN(balance) && !isNaN(amount) && amount !== 0) {
        const months = balance / amount;
        monthsInput.value = months.toFixed(2);
        updateEndDate(months);
    }
}

function updateEndDate(months) {
    const endDateInput = document.getElementById('enddate');
    const currentDate = new Date();
    
    // Calculate the end date and subtract one month
    const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + Math.ceil(months), 0);
    
    const endMonth = endDate.toLocaleString('default', { month: 'long' });
    const endYear = endDate.getFullYear();
    
    endDateInput.value = `${endMonth} ${endYear}`;
}

function recalculateAmount() {
    const balanceInput = document.getElementById('balance');
    const amountInput = document.getElementById('amount');
    const monthsInput = document.getElementById('months');

    const balance = parseFloat(balanceInput.value);
    const months = parseFloat(monthsInput.value);

    if (!isNaN(balance) && !isNaN(months) && months !== 0) {
        const newAmount = balance / months;
        amountInput.value = newAmount.toFixed(2);
        updateEndDate(months);
    }
}

document.getElementById('amount').addEventListener('input', calculateMonthsAndEndDate);
document.getElementById('balance').addEventListener('input', calculateMonthsAndEndDate);
document.getElementById('months').addEventListener('input', recalculateAmount);
document.getElementById('duration').addEventListener('input', calcbalancedates);


function clearFields() {
    $('#surname').val('');
    $('#workNumber').val('');
    $('#othername').val('');
    $('#department').val('');
    $('#departmentname').val('');
    $('#epmpenperce').val('');
    $('#emplopenperce').val('');
    $('#pensionable').val('');
    $('#balance').val('');
    $('#activeinacToggle').prop('checked', false);
    $('#toggleLabel2').text('Inactive');
}
function clearFields2() {     
    $('#empname').val('');
    $('#empname1').val('');
    $('#inputPCode').val('');
    $('#inputname').val('');
    $('#inputAmount').val('');
    $('#inputBalance').val('');
}
function searchstaffdet(){ 
    performSearch();
}
function searchstaffdet2(){ 
    performSearch2();
}
function performSearch2() {
    var workNo = $('#WorkNo').val();
    var month = $('#month').val();
    var year = $('#year').val();
    cleartxt();
    
    $.ajax({
        url: 'search2.php',
        method: 'POST',
        data: {
            workNo: workNo,
            month: month,
            year: year
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#empname1').val(response.employee.Surname);
                $('#empname').val(response.employee.othername);
                $('#empdept').val(response.employee.dept);
                $('#WorkNo').val(workNo);
                
                var tableBody = $('#employeeDataTable tbody');
                tableBody.empty();
                
                $.each(response.deductions, function(index, deduction) {
                    var formattedDate = new Date(deduction.dateposted);
                    var options = { year: 'numeric', month: 'short', day: '2-digit' };
                    var formattedDateString = formattedDate.toLocaleDateString('en-US', options);
                    var row = '<tr onclick="highlightAndPopulateRow(this)">' +
                        '<td hidden>' + deduction.ID + '</td>' +
                        '<td>' + deduction.PCode + '</td>' +
                        '<td>' + deduction.pcate + '</td>' +
                        
                        '<td>' + deduction.Amount + '</td>' +
                        '<td>' + deduction.balance + '</td>' +
                        '<td>' + formattedDateString + '</td>' +
                        '<td hidden>' + deduction.loanshares + '</td>' +
                        '</tr>';
                    tableBody.append(row);
                });
            } else {
                var tableBody = $('#employeeListTable tbody');
                tableBody.empty();
                
                $.each(response.employeeList, function(index, employee) {
                    var row = '<tr onclick="highlightRow(this)">' +
                        '<td>' + employee.WorkNo + '</td>' +
                        '<td>' + employee.Surname + ' ' + employee.othername + '</td>' +
                        '</tr>';
                    tableBody.append(row);
                });
                
                $('#employeeListModal').modal('show');
            }
        },
        error: function() {
            alert('An error occurred while searching');
        }
    });
}




       
function executeFormula() {
    var formula = $('#formular').val(); // Get the formula
    var amounts = $('#camountf').val().split(','); // Get amounts from #camountf
    var quantity = parseFloat($('#quantity').val()); // Get the quantity value
    var codes = formula.match(/[A-Za-z]+\d+/g); // Extract codes from the formula

    // Replace codes in the formula with corresponding amounts or quantity
    codes.forEach(function(code, index) {
        var amount = amounts[index];
        if (amount === '') {
            amount = quantity; // Use quantity if the amount is missing
        }
        formula = formula.replace(code, amount);
    });

    try {
        // Evaluate the formula safely
        var result = eval(formula);
        // Format the result to 2 decimal places
        $('#amount').val(result.toFixed(2)); // Display the result with 2 decimal places
    } catch (error) {
        console.error('Error evaluating formula:', error);
        $('#amount').val('Error'); // Indicate an error if evaluation fails
    }
}
$(document).ready(function() {
     // Simple implementation for your dropdown

  const choices2 = new Choices('#WorkNo', {
    searchEnabled: true,
    placeholderValue: 'Select Staff',
     searchPlaceholderValue: 'Search staff...',
    allowHTML: true
}); 
    document.getElementById('quantity').addEventListener('input', executeFormula);

    $('#pitem').on('change', function() {
        var selectedParameter = $(this).val();
        if (selectedParameter) {
            fetchData(selectedParameter);
        } else {
            function clearTable() {
        $('#contentTable2 tbody').empty();
    }
        }
    });

   

   
function validateForm() {
    var requiredFields = ['month', 'year', 'pitem', 'surname', 'othername', 'workNumber', 'department', 'amount', 'balance'];
    var isValid = true;

    // Check if pension fields are visible
    var isPensionVisible = $('#pensionContainer').is(':visible');
    if (isPensionVisible) {
        requiredFields.push('epmpenperce', 'emplopenperce');
    }

    var isOTVisible = $('#otContainer').is(':visible');
    if (isOTVisible) {
        requiredFields.push('quantity', 'otdate', 'formular');
    }


    requiredFields.forEach(function(field) {
        var $field = $('#' + field);
        var value = $field.val();

        if (field === 'pitem') {
            // Special handling for Select2 dropdown
            if (!value || value.length === 0) {
                isValid = false;
                $field.next('.select2-container').addClass('is-invalid');
            } else {
                $field.next('.select2-container').removeClass('is-invalid');
            }
        } else {
            // Standard field validation
            if (!value || value.trim() === '') {
                isValid = false;
                $field.addClass('is-invalid');
            } else {
                $field.removeClass('is-invalid');
            }
        }
    });

    if (!isValid) {
        showMessage('Please fill in all required fields', true);
    }

    return isValid;
}

function submitForm() {
    // Collect form data
    var formData = {
        month: $('#month').val(),
        year: $('#year').val(),
        parameter: $('#pitem').val(),
        surname: $('#surname').val(),
        othername: $('#othername').val(),
        workNumber: $('#workNumber').val(),
        department: $('#department').val(),
        amount: $('#amount').val(),
        category: $('#category').val(),
        enddate: $('#enddate').val(),
        months: $('#months').val(),
        balance: $('#balance').val()
    };

    // Check if pension fields are visible and add them to formData if they are
    if ($('#pensionContainer').is(':visible')) {
        formData.epmpenperce = $('#epmpenperce').val();
        formData.emplopenperce = $('#emplopenperce').val();
    }
    if ($('#otContainer').is(':visible')) {
        formData.quantity = $('#quantity').val();
        formData.otdate = $('#otdate').val();
        formData.formular = $('#formular').val();
    }
    if ($('#Open').is(':visible')) {
    // Check the state of the toggle and set the value accordingly
    var isActive = $('#activeinacToggle').prop('checked');
    formData.openvalue = isActive ? '1' : '0';
}

if ($('#hiddenContainer').is(':visible')) {
    // Check the state of the toggle and set the value accordingly
    var isActive = $('#activeinaclonToggle').prop('checked');
    formData.openvalue = isActive ? '1' : '0';
}

if ($('#category').val() === 'normal') {
    formData.openvalue = '1';
}
   const selectedParameter = $('#pitem').val();

   const submitBtn = $('#submitBtn');  // jQuery object
const originalText = submitBtn.html();

submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Posting...').prop('disabled', true);


    // Send the AJAX request
    $.ajax({
        url: paysubmit, 
        type: 'POST',
        data: formData,
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
        dataType: 'json',
        success: function(response) {
            try {
                // Check if response is already an object
                var result = typeof response === 'object' ? response : JSON.parse(response);
                if (result.success) {
                    showMessage('Payroll Item Saved ', false);

                    setTimeout(function() {
                        fetchData(selectedParameter);
                    }, 100);
                   
                    $('#surname, #workNumber, #othername, #department, #departmentname, #epmpenperce, #emplopenperce, #pensionable, #balance, #searchValue, #amount, #quantity, #otdate').val('');
                    
                } else {
                    showMessage(result.message || 'An error occurred', true);
                }
            } catch (e) {
                showMessage('Invalid response from server: ' + JSON.stringify(response), true);
            }
        },
        error: function(xhr, status, error) {
            showMessage('An error occurred: ' + error, true);
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
            
        }
    });
}
$('#submitBtn').on('click', function(e) {
    e.preventDefault();  // Prevent the default form submission
    if (validateForm()) {
        submitForm();    // Call the function to submit the form via AJAX
    }
});
    function clearTable() {
        $('#contentTable2 tbody').empty();
    }
function clearFields() {
    $('#surname').val('');
    $('#workNumber').val('');
    $('#othername').val('');
    $('#department').val('');
    $('#departmentname').val('');
    $('#epmpenperce').val('');
    $('#emplopenperce').val('');
    $('#pensionable').val('');
    $('#balance').val('');
    $('#activeinacToggle').prop('checked', false);
    $('#toggleLabel2').text('Inactive');
}
    

    function showErrorMessage(message) {
        let errorDiv = document.getElementById('errorMessage');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'errorMessage';
            document.body.appendChild(errorDiv);
        }

        errorDiv.textContent = message;
        errorDiv.style.position = 'fixed';
        errorDiv.style.top = '20px';
        errorDiv.style.left = '20px';
        errorDiv.style.backgroundColor = '#f44336';
        errorDiv.style.color = 'white';
        errorDiv.style.padding = '15px';
        errorDiv.style.borderRadius = '5px';
        errorDiv.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
        errorDiv.style.zIndex = '1051'; // Higher than the modal's z-index
        errorDiv.style.display = 'block';

        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 2000);
    }
    
});

$(document).ready(function() {
    $('#exampleModal').on('hidden.bs.modal', function () {
        // Reset the form
        clearFields();
        //$('#payrollForm')[0].reset();
        $('#pitem').val('').trigger('change');
        //$('#searchValue').val('').trigger('change');
       //choicesSearchValue.removeActiveItems();


    });
    $('#employeeModal').on('hidden.bs.modal', function () {
        // Reset the form
        clearFields2();
    });
    

  
    
$('#btnopenot').on('click', function(e) {
    // Get values
    const workNumber = $('#workNumber').val();
    const otdate = $('#otdate').val();
    const codebal = $('#codebal').val();

    var yea2r = document.getElementById('otdate').value;

    if (!yea2r) {
        showErrorMessage('Please select a date');
        return;
    }
    
    // Parse the month and year from the otdate
    const dateObj = new Date(otdate);
    const month = dateObj.getMonth() + 1; // Months are 0-indexed
    const year = dateObj.getFullYear();

    // Send data to the server via AJAX
    $.ajax({
        url: 'searchot.php', // Update with the actual server-side URL
        type: 'POST',
        data: {
            workNumber: workNumber,
            codebal: codebal,
            month: month,
            year: year
        },
        success: function(response) {
            // Assuming the response is JSON containing records
            const records = JSON.parse(response);

            // Populate the table in the modal
            let tableBody = $('#otListTable tbody');
            tableBody.empty(); // Clear any existing rows
            
            records.forEach(record => {
                const row = `<tr>
                                <td>${record.odate}</td>
                                <td>${record.quantity}</td>
                                <td>${record.tamount}</td>
                             </tr>`;
                tableBody.append(row);
            });

            // Show the modal
            $('#otListModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error("Error fetching records: " + error);
        }
    });
});




    
});
