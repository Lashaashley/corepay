(function ($) {
    const backdrop = document.getElementById('edituserModalBackdrop');

    function openCustomModal () {
        backdrop.classList.add('open');
        // Reset password section each open
        document.getElementById('enable_password_reset').checked = false;
        document.getElementById('password-reset-section').style.display = 'none';
    }

    function closeCustomModal () {
        backdrop.classList.remove('open');
    }

    // Patch $.fn.modal so musers.js calls work seamlessly
    $.fn.modal = function (action) {
        const el = this[0];
        if (!el) return this;

        if (action === 'show') {
            openCustomModal();
            // Fire expected Bootstrap events so any .on('shown.bs.modal') listeners work
            $(el).trigger('show.bs.modal').trigger('shown.bs.modal');
        } else if (action === 'hide') {
            closeCustomModal();
            $(el).trigger('hide.bs.modal').trigger('hidden.bs.modal');
        }
        return this;
    };

    // Close on backdrop click
    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) closeCustomModal();
    });

    // Expose globals so close buttons work
    window._closeEditModal = closeCustomModal;

})(jQuery);
document.addEventListener('DOMContentLoaded', function () {

    /* ── Close buttons ───────────────────────────────────── */
    ['modalCloseBtn','modalCancelBtn'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', function () {
            window._closeEditModal();
        });
    });

    /* ── Escape key ──────────────────────────────────────── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window._closeEditModal();
    });

    /* ── DataTable ───────────────────────────────────────── */
    

    /* ── Custom search ───────────────────────────────────── */
    let searchTimer;
    document.getElementById('dt-search').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => table.search(this.value).draw(), 350);
    });

    /* ── Page length ─────────────────────────────────────── */
    document.getElementById('dt-length').addEventListener('change', function () {
        table.page.len(parseInt(this.value)).draw();
    });

    /* ── Action dropdown ─────────────────────────────────── */
    // ── Action menu toggle (replaces inline onclick) ──────────────────
document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-action="toggle-menu"]');

    if (trigger) {
        e.stopPropagation();
        const menu = trigger.closest('.action-wrap').querySelector('.action-menu');
        const isOpen = menu.classList.contains('open');

        // Close all open menus first
        document.querySelectorAll('.action-menu.open').forEach(function (m) {
            m.classList.remove('open');
        });

        // Toggle the clicked one
        if (!isOpen) menu.classList.add('open');
        return;
    }

    // Click outside — close all menus
    document.querySelectorAll('.action-menu.open').forEach(function (m) {
        m.classList.remove('open');
    });
});

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.action-wrap'))
            document.querySelectorAll('.action-menu.open').forEach(m => m.classList.remove('open'));
    });

    /* ── Password reset toggle ───────────────────────────── */
    document.getElementById('enable_password_reset').addEventListener('change', function () {
        document.getElementById('password-reset-section').style.display = this.checked ? 'block' : 'none';
    });

    /* ── Password visibility toggle ─────────────────────── */
    window.toggleEditPw = function (inputId, iconId) {
        const inp  = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isTxt = inp.type === 'text';
        inp.type = isTxt ? 'password' : 'text';
        if (icon) icon.textContent = isTxt ? 'visibility' : 'visibility_off';
    };

    /* ── Password strength ───────────────────────────────── */
    window.editPwStrength = function () {
        const pw    = document.getElementById('newpass').value;
        const fill  = document.getElementById('editStrengthFill');
        const label = document.getElementById('editStrengthLabel');
        let score = 0;
        if (pw.length >= 8)           score++;
        if (/[A-Z]/.test(pw))         score++;
        if (/[a-z]/.test(pw))         score++;
        if (/[0-9]/.test(pw))         score++;
        if (/[^A-Za-z0-9]/.test(pw))  score++;
        const levels = [
            {pct:0,color:'',text:''},
            {pct:20,color:'#ef4444',text:'Very weak'},
            {pct:40,color:'#f97316',text:'Weak'},
            {pct:60,color:'#eab308',text:'Fair'},
            {pct:80,color:'#22c55e',text:'Strong'},
            {pct:100,color:'var(--success)',text:'Very strong'}
        ];
        const lvl = levels[Math.min(score, 5)];
        fill.style.width      = lvl.pct + '%';
        fill.style.background = lvl.color;
        label.textContent     = lvl.text;
        label.style.color     = lvl.color || 'var(--muted)';
        checkEditPwMatch();
    };

    window.checkEditPwMatch = function () {
        const pw  = document.getElementById('newpass').value;
        const cfm = document.getElementById('newpass_confirmation').value;
        const el  = document.getElementById('password-match-message');
        if (!cfm) { el.textContent = ''; return; }
        if (pw === cfm) { el.className = 'pw-match ok';  el.textContent = '✓ Passwords match'; }
        else            { el.className = 'pw-match err'; el.textContent = '✗ Passwords do not match'; }
    };

    /* ── Generate password ───────────────────────────────── */
    window.generateEditPassword = function () {
        const upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        const lower = 'abcdefghjkmnpqrstuvwxyz';
        const nums  = '23456789';
        const syms  = '!@#$%^&*_-+=?';
        const all   = upper + lower + nums + syms;
        let pw = [
            upper[Math.floor(Math.random() * upper.length)],
            lower[Math.floor(Math.random() * lower.length)],
            nums [Math.floor(Math.random() * nums.length)],
            syms [Math.floor(Math.random() * syms.length)],
        ];
        for (let i = pw.length; i < 14; i++) pw.push(all[Math.floor(Math.random() * all.length)]);
        pw = pw.sort(() => Math.random() - .5).join('');
        document.getElementById('newpass').value = pw;
        document.getElementById('newpass_confirmation').value = pw;
        document.getElementById('newpass').type = 'text';
        document.getElementById('newpass_confirmation').type = 'text';
        document.getElementById('editEye1').textContent = 'visibility_off';
        document.getElementById('editEye2').textContent = 'visibility_off';
        editPwStrength();
        showToast('success', 'Password Generated', 'A strong password has been set.');
    };

    /* ── Profile photo preview ───────────────────────────── */
    window.previewEditPhoto = function (input) {
        if (!input.files.length) return;
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            showToast('danger', 'File too large', 'Please choose an image under 2 MB.');
            input.value = ''; return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById('current-photo');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(file);
    };

    /* ── Toast ───────────────────────────────────────────── */
    function showToast (type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success:'check_circle', danger:'error_outline', warning:'warning_amber' };
        const t = document.createElement('div');
        t.className = `toast-msg ${type}`;
        t.innerHTML = `<span class="material-icons">${icons[type]||'info'}</span>
                       <div><strong>${title}</strong> ${message}</div>`;
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }

    window.showMessage = function (msg, type) {
        showToast(type || 'info', 'Notice', msg);
    };

});
            $(document).ready(function() {

                loadtable();
    
    // Load payroll types on page load
    loadPayrollTypes();
    
    // Edit agent button click
    $('#users-table').on('click', '.edit-agent', function(e) {
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
            editPwStrength();
        } else {
            $('#password-strength').html('');
        }
    });
    
    // Check password match
    $('#newpass_confirmation').on('input', function() {
        const password = $('#newpass').val();
        const confirmation = $(this).val();
        checkEditPwMatch();
        
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
        previewEditPhoto(this);
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
    
    // Handle mfa checkbox - if not checked, set value to 'OFF'
    if ($('#mfa').is(':checked')) {
        formData.set('mfa', 'ON');
    } else {
        formData.set('mfa', 'OFF');
    }
    
    // Handle activeacc checkbox - if not checked, set value to 'INACTIVE'
    if ($('#activeacc').is(':checked')) {
        formData.set('activeacc', 'ACTIVE');
    } else {
        formData.set('activeacc', 'INACTIVE');
    }
    
    // Remove password fields if not changing password
    if (!$('#enable_password_reset').is(':checked')) {
        formData.delete('newpass');
        formData.delete('newpass_confirmation');
    }
    
    $('#save-user-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    const updateuser = window.App.routes.updateuser.replace('__id__', userId);

    $.ajax({
        
        url: updateuser,
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
                loadtable();
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
    const url = window.App.routes.getuserman.replace('__id__', userId);
    $.ajax({
          url: url,
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                const user = response.user;
                
                // Populate form fields
                $('#edit_user_id').val(user.id);
                $('#edit_userId').val(user.id);
                $('#eusername').val(user.name);
                $('#email').val(user.email);
                
                setCheckboxValue('#approvelvl', user.approvelvl);
                setCheckboxValue2('#mfa', user.MFA);
                setCheckboxValue3('#activeacc', user.Status);
                
                // Show current profile photo
                if (user.profile_photo) {
                    $('#current-photo').attr('src', `${window.App.routes.storage_url}/${user.profile_photo}`);
                    $('#current-photo-preview').show();
                }
                
                // Check allowed payrolls
                const allowedPayrolls = user.allowedprol ? user.allowedprol.split(',') : [];
                $('input[name="allowedPayroll[]"]').prop('checked', false);
                allowedPayrolls.forEach(function(payrollId) {
                    $(`#payroll${payrollId}`).prop('checked', true);
                });
                
               $('#edituserModalBackdrop').addClass('open');
            }
        },
        error: function(xhr) {
            showMessage('Failed to load user data', 'danger');
        }
    });
}

function setCheckboxValue(selector, value) {
    const checkbox = $(selector);
    if (value === 'YES') {
        checkbox.prop('checked', true);
    } else {
        checkbox.prop('checked', false);
    }
}
function setCheckboxValue2(selector, value) {
    const checkbox = $(selector);
    if (value === 'ON') {
        checkbox.prop('checked', true);
    } else {
        checkbox.prop('checked', false);
    }
}
function setCheckboxValue3(selector, value) {
    const checkbox = $(selector);
    if (value === 'ACTIVE') {
        checkbox.prop('checked', true);
    } else {
        checkbox.prop('checked', false);
    }
}

// Load payroll types
function loadPayrollTypes() {
    $.ajax({
        url: App.routes.getpayroll,
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {

                const $container = $('#payroll-checkboxes');
                $container.empty(); // ✅ Clear safely before rebuilding

                response.payrollTypes.forEach(function(payroll) {

                    // ✅ Create elements via DOM — no string interpolation
                    const $wrapper = $('<div>').addClass('form-check');

                    const $input = $('<input>')
                        .addClass('form-check-input')
                        .attr('type', 'checkbox')
                        .attr('name', 'allowedPayroll[]')
                        .attr('id', 'payroll' + payroll.ID)  // ✅ .attr() escapes automatically
                        .val(payroll.ID);                     // ✅ .val() escapes automatically

                    const $label = $('<label>')
                        .addClass('form-check-label')
                        .attr('for', 'payroll' + payroll.ID)
                        .text(payroll.pname);                 // ✅ .text() never renders HTML tags

                    $wrapper.append($input, $label); //line 323
                    $container.append($wrapper);
                });
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
    
            
          let usersTable = null; // Global variable to store DataTable instance

function loadtable() {
    // Check if DataTable already exists
    if (usersTable) {
        // Just reload the data without reinitializing
        usersTable.ajax.reload(null, false);
        return;
    }
    
    // Initialize DataTable only once
    usersTable = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: App.routes.manageusers,
            type: 'GET',
            error: function () { 
                showToast('danger', 'Error', 'Failed to load user data.'); 
            }
        },
        columns: [
            {
                data: null, orderable: true,
                render: function (data, type, row) {
    const photoUrl = row.profile_photo
        ? `${window.App.routes.storage_url}/${row.profile_photo}`
        : `${window.App.routes.uploads_url}/NO-IMAGE-AVAILABLE.jpg`;

    return `
        <div class="user-cell">
            <img class="user-avatar-img"
                 src="${photoUrl}"
                 alt="${row.full_name}">
            <span class="user-name">${row.full_name}</span>
        </div>`;
}
            },
            { data: 'id', orderable: true },
            { data: 'email', orderable: true },
            {
                data: 'password_expires_at', orderable: true,
                render: function (data) {
                    if (!data) return '<span class="status-badge standard"><span class="dot"></span>—</span>';
                    const expired = new Date(data) < new Date();
                    return `<span class="status-badge ${expired ? 'expired' : 'active'}">
                                <span class="dot"></span>${data}
                            </span>`;
                }
            },
            { data: 'allowedprol', orderable: true },
            {
                data: 'approvelvl', orderable: true,
                render: function (data) {
                    return data === 'YES'
                        ? '<span class="status-badge approver"><span class="dot"></span>Approver</span>'
                        : '<span class="status-badge standard"><span class="dot"></span>Standard</span>';
                }
            },
            {
                data: 'actions', orderable: false, searchable: false,
                render: function (data) {
                    return `
                        <div class="action-wrap">
                            <button class="action-trigger" data-action="toggle-menu">
                                <span class="material-icons">more_horiz</span>
                            </button>
                            <div class="action-menu">
                                <a href="#" class="edit-agent" data-id="${data}">
                                    <span class="material-icons">edit</span> Edit User
                                </a>
                            </div>
                        </div>`;
                }
            }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: 'rtp',
        language: {
            processing: '<span style="color:var(--muted);font-size:13px;">Loading…</span>',
            emptyTable: 'No users found.',
            zeroRecords: 'No users match your search.'
        },
        drawCallback: function () {
            const info = this.api().page.info();
            const total = info.recordsTotal.toLocaleString();
            const display = info.recordsDisplay.toLocaleString();
            document.getElementById('recordCount').textContent =
                info.recordsTotal === info.recordsDisplay
                    ? `${total} users`
                    : `${display} of ${total} users`;
        }
    });
}