 /* ── Password visibility toggle ──────────────────────────── */
function togglePw (inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    if (icon) icon.textContent = isText ? 'visibility' : 'visibility_off';
}
 
/* Same function name kept for JS compatibility */
window.togglePasswordVisibility = togglePw;
 
/* ── Password strength ───────────────────────────────────── */
function checkStrength () {
    const pw     = document.getElementById('newPassword').value;
    const fill   = document.getElementById('strengthFill');
    const label  = document.getElementById('strengthLabel');
 
    let score = 0;
    if (pw.length >= 8)           score++;
    if (/[A-Z]/.test(pw))         score++;
    if (/[a-z]/.test(pw))         score++;
    if (/[0-9]/.test(pw))         score++;
    if (/[^A-Za-z0-9]/.test(pw))  score++;
 
    const levels = [
        { pct:0,   color:'',                    text:'' },
        { pct:20,  color:'#ef4444',             text:'Very weak' },
        { pct:40,  color:'#f97316',             text:'Weak' },
        { pct:60,  color:'#eab308',             text:'Fair' },
        { pct:80,  color:'#22c55e',             text:'Strong' },
        { pct:100, color:'var(--success)',       text:'Very strong' },
    ];
 
    const lvl  = levels[Math.min(score, 5)];
    fill.style.width      = lvl.pct + '%';
    fill.style.background = lvl.color;
    label.textContent     = lvl.text;
    label.style.color     = lvl.color || 'var(--muted)';
}
 
/* ── Auto-generate strong password ──────────────────────── */
function generatePassword () {
    const upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    const lower   = 'abcdefghjkmnpqrstuvwxyz';
    const digits  = '23456789';
    const symbols = '!@#$%^&*_-+=?';
    const all     = upper + lower + digits + symbols;
 
    // Ensure at least one of each character class
    let pw = [
        upper [Math.floor(Math.random() * upper.length)],
        lower [Math.floor(Math.random() * lower.length)],
        digits[Math.floor(Math.random() * digits.length)],
        symbols[Math.floor(Math.random() * symbols.length)],
    ];
 
    // Fill to 14 characters
    for (let i = pw.length; i < 14; i++) {
        pw.push(all[Math.floor(Math.random() * all.length)]);
    }
 
    // Shuffle
    pw = pw.sort(() => Math.random() - .5).join('');
 
    // Populate both fields
    const pwInput   = document.getElementById('newPassword');
    const cfmInput  = document.getElementById('confirmPassword');
    pwInput.value   = pw;
    cfmInput.value  = pw;
 
    // Show eye icons (make password visible briefly)
    pwInput.type    = 'text';
    cfmInput.type   = 'text';
    document.getElementById('eyeIcon1').textContent = 'visibility_off';
    document.getElementById('eyeIcon2').textContent = 'visibility_off';
 
    // Show badge with the generated password
    document.getElementById('genPwText').textContent = pw;
    document.getElementById('genPwBadge').classList.add('show');
 
    checkStrength();
 
    showToast('success', 'Password Generated', 'A strong password has been set in both fields.');
}
 
/* ── Copy generated password ─────────────────────────────── */
function copyGenPw () {
    const pw = document.getElementById('genPwText').textContent;
    navigator.clipboard?.writeText(pw).then(() => {
        showToast('success', 'Copied!', 'Password copied to clipboard.');
    }).catch(() => {
        // Fallback
        const el = document.createElement('textarea');
        el.value = pw;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        showToast('success', 'Copied!', 'Password copied to clipboard.');
    });
}
 
/* ── Avatar preview ──────────────────────────────────────── */
function previewAvatar (input) {
    if (!input.files.length) return;
    const file = input.files[0];
    if (file.size > 2 * 1024 * 1024) {
        showToast('danger', 'File too large', 'Please choose an image under 2 MB.');
        input.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = function (e) {
        const img  = document.getElementById('previewImg');
        const icon = document.querySelector('#avatarPreview .material-icons');
        img.src    = e.target.result;
        img.style.display  = 'block';
        if (icon) icon.style.display = 'none';
    };
    reader.readAsDataURL(file);
}
 
/* ── Reset form helper ────────────────────────────────────── */
function resetForm () {
    document.getElementById('genPwBadge').classList.remove('show');
    document.getElementById('genPwText').textContent = '';
    document.getElementById('strengthFill').style.width = '0%';
    document.getElementById('strengthLabel').textContent = '';
    const img = document.getElementById('previewImg');
    img.style.display = 'none'; img.src = '';
    const icon = document.querySelector('#avatarPreview .material-icons');
    if (icon) icon.style.display = '';
    // Clear all field errors
    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
}
 
/* ── Toast ────────────────────────────────────────────────── */
function showToast (type, title, message) {
    const wrap  = document.getElementById('toastWrap');
    const icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber' };
    const t = document.createElement('div');
    t.className = 'toast-msg ' + type;
    t.innerHTML = '<span class="material-icons">' + (icons[type]||'info') + '</span>'
                + '<div><strong>' + title + '</strong> ' + message + '</div>';
    wrap.appendChild(t);
    const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
    t.addEventListener('click', dismiss);
    setTimeout(dismiss, 5000);
}
 
window.showMessage = function (msg, isError) {
    showToast(isError ? 'danger' : 'success', isError ? 'Error' : 'Success', msg);
};
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
        url: App.routes.newuser,
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

     $('#profilepic').on('change', function() {
    
        previewAvatar(this);
    });
     $('#btn-reset').on('click', function () {
        resetForm();
     });
     $('#generatePwBtn').on('click', function () {
        generatePassword();
     });
     $('#pw-btn').on('click', function () {
        togglePw('newPassword','eyeIcon1','eyeIcon1');
     });
      $('#pw-btn2').on('click', function () {
        togglePw('confirmPassword','eyeIcon2');
     });
     $('#gen-pw-copy').on('click', function () {
        copyGenPw();
     });
      $('#newpass').on('input', function () {
        checkStrength();
     });
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


 


