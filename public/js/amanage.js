  $(document).ready(function() {
            // Initialize DataTable with server-side processing
            var table = $('#agents-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: amanage,
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('DataTable Ajax error:', error);
                        showAlert('danger', 'Error!', 'Error loading data');
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: true,
                        render: function(data, type, row) {
                            return `
                                <div class="name-avatar d-flex align-items-center">
                                    <div class="avatar mr-2 flex-shrink-0">
                                        <img src="${row.profile_photo}" 
                                             class="border-radius-100 shadow" 
                                             width="40" 
                                             height="40" 
                                             alt="${row.full_name}"
                                             onerror="this.src='{{ asset('uploads/NO-IMAGE-AVAILABLE.jpg') }}'">
                                    </div>
                                    <div class="txt">
                                        <div class="weight-600">${row.full_name}</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { data: 'emp_id', orderable: true },
                    { data: 'stafftype', orderable: true },
                    { data: 'department', orderable: true },
                    { data: 'designation', orderable: true },
                    {
                        data: 'status',
                        orderable: true,
                        render: function(data, type, row) {
                            var color = data === 'ACTIVE' ? 'green' : 'red';
                            return `<span style="color: ${color}; font-weight: bold;">${data}</span>`;
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" 
                                       href="#" 
                                       role="button" 
                                       data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item edit-agent" 
                                           href="#" 
                                           data-id="${data}">
                                            <i class="dw dw-edit2"></i> Edit
                                        </a>
                                        
                                    </div>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[1, 'asc']], // Order by emp_id
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                    emptyTable: "No staff members found",
                    zeroRecords: "No matching staff members found"
                }
            });

            // Edit agent
            $('#agents-table').on('click', '.edit-agent', function(e) {
                e.preventDefault();
                var agentId = $(this).data('id');
               loadUserData(agentId);
            });

            // Terminate agent
            $('#agents-table').on('click', '.terminate-agent', function(e) {
                e.preventDefault();
                var agentId = $(this).data('id');
                $('#terminate-agent-id').val(agentId);
                $('#terminatModal').modal('show');
            });

            // Confirm termination
            $('#confirm-terminate').on('click', function() {
                var agentId = $('#terminate-agent-id').val();
                
                $.ajax({
                    url: `/agents/${agentId}/terminate`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', 'Success!', response.message);
                            $('#terminatModal').modal('hide');
                            table.ajax.reload(null, false); // Reload table without resetting pagination
                        } else {
                           
                            showAlert('danger', 'Error!', response.message);
                        }
                    },
                    error: function(xhr) {
                       
                        showAlert('danger', 'Error!', 'Error terminating staff member');
                    }
                });
            });

            
            
        });
       
        function loadUserData(userId) {
    // Show loading state
   
    showAlert('success', 'Please Wait!', 'Loading user data...');
    // Load all dropdowns and user data in parallel
    Promise.all([
        loadBranches(),
        loadDepartments(),
        loadBanks(),
        loadBankBranches(),
        loadPayrollTypes(),
        loadUserDetails(userId)
    ]).then(() => {
       
        showAlert('success', 'Success!', 'User data loaded successfully');
    }).catch(error => {
       
        showAlert('danger', 'Error!', 'Failed to load some data: ' + error.message);
    });
}

// Separate functions for each dropdown
function loadBranches() {
    return $.ajax({
        url: branches,
        type: "GET",
        success: function (response) {
            const dropdown = $('#brid');
            dropdown.empty();
            dropdown.append('<option value="">Select Branch</option>');
            dropdown.append('<option value="0">Overall</option>');
            response.data.forEach(function (branch) {
                dropdown.append(
                    `<option value="${branch.ID}">${branch.branchname}</option>`
                );
            });
        },
        error: function () {
            throw new Error('Failed to load branches');
        },
    });
}

function loadDepartments() {
    return $.ajax({
        url: depts,
        type: "GET",
        success: function (response) {
            const dropdown = $('#dept');
            dropdown.empty();
            dropdown.append('<option value="">Select Department</option>');
            response.data.forEach(function (dept) {
                dropdown.append(
                    `<option value="${dept.ID}">${dept.DepartmentName}</option>`
                );
            });
        },
        error: function () {
            throw new Error('Failed to load departments');
        },
    });
}

function loadBanks() {
    return $.ajax({
        url: getbanks,
        type: "GET",
        success: function (response) {
            const dropdown = $('#bank');
            dropdown.empty();
            dropdown.append('<option value="">Select Bank</option>');
            response.data.forEach(function (bank) {
                dropdown.append(
                    `<option value="${bank.Bank}">${bank.Bank}</option>`
                );
            });
        },
        error: function () {
            throw new Error('Failed to load banks');
        },
    });
}

function loadBankBranches() {
    return $.ajax({
        url: getbranches,
        type: "GET",
        success: function (response) {
            const dropdown = $('#branch');
            dropdown.empty();
            dropdown.append('<option value="">Select Branch</option>');
            response.data.forEach(function (branch) {
                dropdown.append(
                    `<option value="${branch.Branch}">${branch.Branch}</option>`
                );
            });
        },
        error: function () {
            throw new Error('Failed to load bank branches');
        },
    });
}

function loadPayrollTypes() {
    return $.ajax({
        url: getptypes,
        type: "GET",
        success: function (response) {
            const dropdown = $('#proltype');
            dropdown.empty();
            dropdown.append('<option value="">Select Payroll</option>');
            response.data.forEach(function (paytype) {
                dropdown.append(
                    `<option value="${paytype.ID}">${paytype.pname}</option>`
                );
            });
        },
        error: function () {
            throw new Error('Failed to load payroll types');
        },
    });
}

function loadUserDetails(userId) {
    return $.ajax({
        url: getuser.replace(':id', userId),
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                const agent = response.agent;
                
                
                // Populate basic fields
                $('#agentno').val(agent.emp_id || '');
                $('#firstname').val(agent.FirstName || '');
                $('#lastname').val(agent.LastName || '');
                $('#email').val(agent.EmailId || '');
                $('#phonenumber').val(agent.Phonenumber || '');
                $('#dob').val(agent.Dob || '');
                $('#gender').val(agent.Gender || '');
                $('#brid').val(agent.brid || '');
                $('#dept').val(agent.Department || '');
                
                // Handle checkbox fields (YES/NO values)
                setCheckboxValue('#nhif_shif', agent.nhif);
                setCheckboxValue('#nssf', agent.nssf);
                setCheckboxValue('#contractor', agent.contractor);
                setCheckboxValue('#unionized', agent.unionized);
                setCheckboxValue('#nssfopt', agent.nssfopt);
                
                // Populate other fields
                $('#unionno').val(agent.unionno || '');
                $('#idno').val(agent.idno || '');
                $('#nhifno').val(agent.nhifno || '');
                $('#krapin').val(agent.kra || '');
                $('#nssfno').val(agent.nssfno || '');
                $('#paymentMethod').val(agent.paymode || '');
                $('#proltype').val(agent.payrolty || '');
                
                // Bank details
                $('#bank').val(agent.Bank || '');
                $('#branch').val(agent.Branch || '');
                $('#bcode').val(agent.BranchCode || '');
                $('#swiftcode').val(agent.swiftcode || '');
                $('#bankcode').val(agent.BankCode || '');
                $('#account').val(agent.AccountNo || '');
                $('#aggentno').val(agent.emp_id || '');
                
                // Show modal
                $('#editstaffModal').modal('show');
            }
        },
        error: function(xhr) {
            throw new Error('Failed to load user data');
        }
    });
}

// Helper function to set checkbox based on YES/NO value
function setCheckboxValue(selector, value) {
    const checkbox = $(selector);
    if (value === 'YES') {
        checkbox.prop('checked', true);
    } else {
        checkbox.prop('checked', false);
    }
}

// Helper function to get checkbox value as YES/NO
function getCheckboxValue(selector) {
    return $(selector).is(':checked') ? 'YES' : 'NO';
}

// Function to collect form data for update
function getFormData() {
    return {
        emp_id: $('#agentno').val(),
        FirstName: $('#firstname').val(),
        LastName: $('#lastname').val(),
        EmailId: $('#email').val(),
        Phonenumber: $('#phonenumber').val(),
        Dob: $('#dob').val(),
        Gender: $('#gender').val(),
        brid: $('#brid').val(),
        Department: $('#dept').val(),
        nhif: getCheckboxValue('#nhif_shif'),
        nssf: getCheckboxValue('#nssf'),
        contractor: getCheckboxValue('#contractor'),
        unionized: getCheckboxValue('#unionized'),
        nssfopt: getCheckboxValue('#nssfopt'),
        unionno: $('#unionno').val(),
        idno: $('#idno').val(),
        nhifno: $('#nhifno').val(),
        kra: $('#krapin').val(),
        nssfno: $('#nssfno').val(),
        paymode: $('#paymentMethod').val(),
        payrolty: $('#proltype').val(),
        Bank: $('#bank').val(),
        Branch: $('#branch').val(),
        BranchCode: $('#bcode').val(),
        swiftcode: $('#swiftcode').val(),
        BankCode: $('#bankcode').val(),
        AccountNo: $('#account').val()
    };
}

// Placeholder for showMessage function if it doesn't exist
function showMessage(message, type) {
    // Implement your notification system here
    console.log(`[${type.toUpperCase()}] ${message}`);
}

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

function showAlert(type, title, message) {
                const statusMessage = $('#status-message');
                $('#alert-title').html(title);
                $('#alert-message').html(message);
                
                statusMessage
                    .removeClass('alert-success alert-danger')
                    .addClass(`alert-${type}`)
                    .css('display', 'block')
                    .addClass('show');
                
                // Auto hide after 5 seconds if not manually closed
                setTimeout(() => {
                    if (statusMessage.hasClass('show')) {
                        statusMessage.removeClass('show');
                        setTimeout(() => {
                            statusMessage.hide();
                        }, 500);
                    }
                }, 5000);
            }
            $('.close').on('click', function() {
                const alert = $(this).closest('.custom-alert');
                alert.removeClass('show');
                setTimeout(() => {
                    alert.hide();
                }, 500);
            });