 $(document).ready(function() {
       document.getElementById('profilepic')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagePreview').style.display = 'none';
    }
});

// Form submission
$('#createuser').on('submit', function(e) { 
    e.preventDefault();
    
    // Clear previous errors
    $('.text-danger').html('');
    
    let formData = new FormData(this);
    
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Creating...').prop('disabled', true);
    
    $.ajax({
        url: amanage,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            showAlert('success', 'Success!', response.message);
            $('#createuser')[0].reset();
            $('#imagePreview').hide();
            
            // Optionally reload users list or redirect
            // window.location.reload();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
                showAlert('danger', 'Validation Error!', 'Please check the form for errors.');
            } else {
                showAlert('danger', 'Error!', xhr.responseJSON?.message || 'Error creating user');
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});
    });
       
 document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const newPassError = document.getElementById('newpass-error');
    const confirmError = document.getElementById('confirm-error');

    // Initialize Bootstrap tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Function to check if password meets complexity requirements
    function isValidPassword(password) {
        if (password.length < 8) {
            return { valid: false, message: 'Password must be at least 8 characters long.' };
        }

        const rules = {
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            numbers: /[0-9]/.test(password),
            symbols: /[~!@#$%^*_\-+=`|(){}[\]:;"<>,.?/&]/.test(password)
        };

        const metRules = Object.values(rules).filter(Boolean).length;

        if (metRules < 3) {
            return { 
                valid: false, 
                message: 'Password must match at least 3 of 4 character rules (uppercase, lowercase, numbers, symbols).' 
            };
        }

        return { valid: true, message: '' };
    }

    // Function to check if passwords match
    function checkPasswordsMatch() {
        return newPassword.value === confirmPassword.value;
    }

    // Add event listener to both inputs to trigger validation
    newPassword.addEventListener('input', validateInputs);
    confirmPassword.addEventListener('input', validateInputs);

    // Validate inputs whenever either input changes
    function validateInputs() {
        const passwordValidation = isValidPassword(newPassword.value);
        const passwordsMatch = checkPasswordsMatch();

        // Validate new password
        if (newPassword.value.length > 0) {
            if (!passwordValidation.valid) {
                newPassword.classList.add('is-invalid');
                newPassword.classList.remove('is-valid');
                newPassError.textContent = passwordValidation.message;
                newPassword.setCustomValidity(passwordValidation.message);
            } else {
                newPassword.classList.remove('is-invalid');
                newPassword.classList.add('is-valid');
                newPassError.textContent = '';
                newPassword.setCustomValidity('');
            }
        } else {
            newPassword.classList.remove('is-invalid', 'is-valid');
            newPassError.textContent = '';
        }

        // Validate confirm password
        if (confirmPassword.value.length > 0) {
            if (!passwordsMatch) {
                confirmPassword.classList.add('is-invalid');
                confirmPassword.classList.remove('is-valid');
                confirmError.textContent = 'Passwords do not match.';
                confirmPassword.setCustomValidity('Passwords do not match.');
            } else if (passwordValidation.valid) {
                confirmPassword.classList.remove('is-invalid');
                confirmPassword.classList.add('is-valid');
                confirmError.textContent = '';
                confirmPassword.setCustomValidity('');
            } else {
                confirmPassword.classList.add('is-invalid');
                confirmPassword.classList.remove('is-valid');
                confirmError.textContent = 'Passwords do not match.';
                confirmPassword.setCustomValidity('Passwords do not match.');
            }
        } else {
            confirmPassword.classList.remove('is-invalid', 'is-valid');
            confirmError.textContent = '';
        }
    }
});
		function togglePasswordVisibility(passwordFieldId, iconId) {
    var passwordField = document.getElementById(passwordFieldId);
    var icon = document.getElementById(iconId);
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}function showAlert(type, title, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <strong>${title}</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    $('#alertContainer').html(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}