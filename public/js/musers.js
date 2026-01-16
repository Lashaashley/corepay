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
                        showMessage('Error loading data', 'danger');
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: true,
                        render: function (data, type, row) {

    let photoUrl;

    if (row.profile_photo) {
        photoUrl = `${STORAGE_URL}/${row.profile_photo}`;
    } else {
        photoUrl = `${UPLOADS_URL}/NO-IMAGE-AVAILABLE.jpg`;
    }

    return `
        <div class="name-avatar d-flex align-items-center">
            <div class="avatar mr-2 flex-shrink-0">
                <img src="${photoUrl}"
                     width="40"
                     height="40"
                     class="border-radius-100 shadow"
                     alt="${row.full_name}"
                     onerror="this.src='${UPLOADS_URL}/NO-IMAGE-AVAILABLE.jpg'">
            </div>
            <div class="txt">
                <div class="weight-600">${row.full_name}</div>
            </div>
        </div>
    `;
}

                    },
                    { data: 'id', orderable: true },
                    { data: 'email', orderable: true },
                    { data: 'password_expires_at', orderable: true },
                    { data: 'allowedprol', orderable: true },
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

            $(document).ready(function() {
    
    // Load payroll types on page load
    loadPayrollTypes();
    
    // Edit agent button click
    $('#agents-table').on('click', '.edit-agent', function(e) {
        e.preventDefault();
        var agentId = $(this).data('id');
        loadUserData(agentId);
    });
    
    // Enable/disable password reset section
    $('#enable_password_reset').change(function() {
        if ($(this).is(':checked')) {
            $('#password-reset-section').slideDown();
            $('#newpass, #newpass_confirmation').attr('required', true);
        } else {
            $('#password-reset-section').slideUp();
            $('#newpass, #newpass_confirmation').attr('required', false).val('');
            $('#password-strength, #password-match-message').html('');
        }
    });
    
    // Toggle password visibility
    $('#togglePassword').click(function() {
        const passwordField = $('#newpass');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Generate strong password
    $('#generate-password').click(function() {
        const password = generateStrongPassword();
        $('#newpass, #newpass_confirmation').val(password).attr('type', 'text');
        checkPasswordStrength(password);
        showMessage('Password generated and copied to both fields', 'success');
    });
    
    // Check password strength on input
    $('#newpass').on('input', function() {
        const password = $(this).val();
        if (password.length > 0) {
            checkPasswordStrength(password);
        } else {
            $('#password-strength').html('');
        }
    });
    
    // Check password match
    $('#newpass_confirmation').on('input', function() {
        const password = $('#newpass').val();
        const confirmation = $(this).val();
        
        if (confirmation.length > 0) {
            if (password === confirmation) {
                $('#password-match-message').html('<small class="text-success"><i class="fa fa-check"></i> Passwords match</small>');
            } else {
                $('#password-match-message').html('<small class="text-danger"><i class="fa fa-times"></i> Passwords do not match</small>');
            }
        } else {
            $('#password-match-message').html('');
        }
    });
    
    // Custom file input label update
    $('#profilepic').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
    
    // Form submission
    $('#edituserForm').submit(function(e) {
        e.preventDefault();
        
        // Validate password if enabled
        if ($('#enable_password_reset').is(':checked')) {
            const password = $('#newpass').val();
            const confirmation = $('#newpass_confirmation').val();
            
            if (password !== confirmation) {
                showMessage('Passwords do not match', 'danger');
                return false;
            }
            
            if (!validatePassword(password)) {
                showMessage('Password does not meet requirements', 'danger');
                return false;
            }
        }
        
        const userId = $('#edit_user_id').val();
        const formData = new FormData(this);
        
        // Remove password fields if not changing password
        if (!$('#enable_password_reset').is(':checked')) {
            formData.delete('newpass');
            formData.delete('newpass_confirmation');
        }
        
        $('#save-user-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: updateuser.replace(':id', userId),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.status === 'success') {
                    showMessage(response.message, 'success');
                    $('#edituserModal').modal('hide');
                    table.ajax.reload(null, false); // Reload table without resetting pagination
                    $('#edituserForm')[0].reset();
                    $('#enable_password_reset').prop('checked', false);
                    $('#password-reset-section').hide();
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to update user';
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showMessage(errorMessage, 'danger');
            },
            complete: function() {
                $('#save-user-btn').prop('disabled', false).html('<i class="fa fa-save"></i> Save Changes');
            }
        });
    });
    
    // Reset form when modal is closed
    $('#edituserModal').on('hidden.bs.modal', function() {
        $('#edituserForm')[0].reset();
        $('#enable_password_reset').prop('checked', false);
        $('#password-reset-section').hide();
        $('#password-strength, #password-match-message').html('');
        $('#current-photo-preview').hide();
        $('.custom-file-label').html('Choose file');
    });
});

// Load user data into modal
function loadUserData(userId) {
    $.ajax({
          url: getuser.replace(':id', userId),
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                const user = response.user;
                
                // Populate form fields
                $('#edit_user_id').val(user.id);
                $('#edit_userId').val(user.id);
                $('#eusername').val(user.name);
                $('#email').val(user.email);
                
                // Show current profile photo
                if (user.profile_photo) {
                    $('#current-photo').attr('src', `${STORAGE_URL}/${user.profile_photo}`);
                    $('#current-photo-preview').show();
                }
                
                // Check allowed payrolls
                const allowedPayrolls = user.allowedprol ? user.allowedprol.split(',') : [];
                $('input[name="allowedPayroll[]"]').prop('checked', false);
                allowedPayrolls.forEach(function(payrollId) {
                    $(`#payroll${payrollId}`).prop('checked', true);
                });
                
                $('#edituserModal').modal('show');
            }
        },
        error: function(xhr) {
            showMessage('Failed to load user data', 'danger');
        }
    });
}

// Load payroll types
function loadPayrollTypes() {
    $.ajax({
        url: getpayroll,
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                let html = '';
                response.payrollTypes.forEach(function(payroll) {
                    html += `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="allowedPayroll[]" 
                                   id="payroll${payroll.ID}" 
                                   value="${payroll.ID}">
                            <label class="form-check-label" for="payroll${payroll.ID}">
                                ${payroll.pname}
                            </label>
                        </div>
                    `;
                });
                $('#payroll-checkboxes').html(html);
            }
        }
    });
}

// Generate strong password
function generateStrongPassword() {
    const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const lowercase = 'abcdefghijklmnopqrstuvwxyz';
    const numbers = '0123456789';
    const symbols = '!@#$%^&*_-+=';
    
    const allChars = uppercase + lowercase + numbers + symbols;
    let password = '';
    
    // Ensure at least one of each type
    password += uppercase[Math.floor(Math.random() * uppercase.length)];
    password += lowercase[Math.floor(Math.random() * lowercase.length)];
    password += numbers[Math.floor(Math.random() * numbers.length)];
    password += symbols[Math.floor(Math.random() * symbols.length)];
    
    // Fill the rest randomly (total length 12-16 characters)
    const length = Math.floor(Math.random() * 5) + 12;
    for (let i = password.length; i < length; i++) {
        password += allChars[Math.floor(Math.random() * allChars.length)];
    }
    
    // Shuffle the password
    return password.split('').sort(() => Math.random() - 0.5).join('');
}

// Validate password
function validatePassword(password) {
    if (password.length < 8) return false;
    
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSymbol = /[~!@#$%^*_\-+=`|(){}[\]:;"'<>,.?/]/.test(password);
    
    const rulesMatched = [hasUppercase, hasLowercase, hasNumber, hasSymbol].filter(Boolean).length;
    
    return rulesMatched >= 3;
}

// Check password strength
function checkPasswordStrength(password) {
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSymbol = /[~!@#$%^*_\-+=`|(){}[\]:;"'<>,.?/]/.test(password);
    
    const rulesMatched = [hasUppercase, hasLowercase, hasNumber, hasSymbol].filter(Boolean).length;
    const length = password.length;
    
    let strength = '';
    let strengthClass = '';
    let requirements = [];
    
    if (length < 8) {
        strength = 'Too Short';
        strengthClass = 'text-danger';
    } else if (rulesMatched < 3) {
        strength = 'Weak';
        strengthClass = 'text-warning';
    } else if (rulesMatched === 3) {
        strength = 'Good';
        strengthClass = 'text-info';
    } else {
        strength = 'Strong';
        strengthClass = 'text-success';
    }
    
    requirements.push(`<small>${hasUppercase ? '✓' : '✗'} Uppercase</small>`);
    requirements.push(`<small>${hasLowercase ? '✓' : '✗'} Lowercase</small>`);
    requirements.push(`<small>${hasNumber ? '✓' : '✗'} Number</small>`);
    requirements.push(`<small>${hasSymbol ? '✓' : '✗'} Symbol</small>`);
    requirements.push(`<small>${length >= 8 ? '✓' : '✗'} 8+ characters</small>`);
    
    $('#password-strength').html(`
        <div class="${strengthClass}">
            <strong>Strength: ${strength}</strong><br>
            ${requirements.join(' | ')}
        </div>
    `);
}

            // Show message function
            function showMessage(message, type) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                $('#status-message')
                    .removeClass('alert-success alert-danger')
                    .addClass(alertClass)
                    .find('#alert-message').text(message);
                $('#status-message').fadeIn().delay(3000).fadeOut();
            }
        });