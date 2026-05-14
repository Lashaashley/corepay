


    function openTab(evt, tabId) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    if (evt && evt.currentTarget) evt.currentTarget.classList.add('active');
    const panel = document.getElementById(tabId);
    if (panel) panel.classList.add('active');
}


        $(document).ready(function() {
           
            loadTableData();
            loadcampuses();
        
            loadptypes();

            $('#orgstrucf').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);

                var form = this;

                const storestaticinfoUrl = form.dataset.storestaticinfoUrl;

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Saving…').prop('disabled', true);
                
                $.ajax({
                    url: storestaticinfoUrl,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showToast('success', 'Success!', response.message);
                        $('#orgstrucf')[0].reset();
                        loadTableData();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            

    

           
            function showToast(type, title, message) {
    const icons = { 
        success: 'check_circle', 
        danger: 'error_outline', 
        warning: 'warning_amber', 
        info: 'info' 
    };

    // Sanitize all remote inputs at entry point
    const safeType    = sanitize(type);
    const safeTitle   = sanitize(title);
    const safeMessage = sanitize(message);

    const iconSpan = $('<span>')
        .addClass('material-icons')
        .text(icons[safeType] || 'info');

    const strong = $('<strong>').text(safeTitle);

    const messageDiv = $('<div>')
        .append(strong)
        .append(document.createTextNode(' ' + safeMessage));

    const t = $('<div>')
        .addClass('toast-msg ' + safeType)
        .append(iconSpan)
        .append(messageDiv);

    $('#toastWrap').append(t);

    const dismiss = () => { t.addClass('leaving'); setTimeout(() => t.remove(), 300); };
    t.on('click', dismiss);
    setTimeout(dismiss, 5000);
}
            $('.close').on('click', function() {
                const alert = $(this).closest('.custom-alert');
                alert.removeClass('show');
                setTimeout(() => {
                    alert.hide();
                }, 500);
            });
            $(document).on('click', '[data-target="#editSchoolModal"]', function () { 
    const id = $(this).data('id');
    const name = $(this).data('name');
    const motto = $(this).data('motto');
    const pobox = $(this).data('pobox');
    const email = $(this).data('email');
    const physaddres = $(this).data('physaddres');
    const logo = $(this).data('logo');

    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#editSchoolForm');
    form.find('#ID').val(id);
    form.find('#schoolName').val(name);
    form.find('#schoolMotto').val(motto);
    form.find('#schoolPobox').val(pobox);
    form.find('#schoolEmail').val(email);
    form.find('#schoolPhysaddres').val(physaddres);
    form.find('#schoolLogoPreview').attr('src', logo);
});
$(document).on('click', '[data-target="#editemailModal"]', function () {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const host = $(this).data('host');
    const port = $(this).data('port');
    const username = $(this).data('username');
    const password = $(this).data('password');
     const from_email = $(this).data('from_email');
     const encryption = $(this).data('encryption');
    
    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#editmailForm');
    form.find('#ID').val(id);
    form.find('#eeName').val(name);
    form.find('#ehost').val(host);
    form.find('#eport').val(port);
    form.find('#eusername').val(username);
    form.find('#epassword').val(password);
    form.find('#eemailaddress').val(from_email);
     form.find('#eencryption').val(encryption);
   
});
         
            
            $('#editSchoolForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editSchoolModal #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Posting…').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({
                    url: App.routes.static.replace('__id__', id), // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showToast('success', 'Success!', response.message);
                        hideModal('editSchoolModal');
                        loadTableData(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            }); 
            $('#editmailForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editmailForm #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Posting…').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: App.routes.econfig.replace('__id__', id), // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showToast('success', 'Success!', response.message);
                        
                        hideModal('editmailForm');
                       
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });



  
            $('#campusform').on('submit', function(e) { 
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);

                var form = this; // Reference the form element
                const storebranchesUrl = form.dataset.storebranchesUrl;
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Saving…').prop('disabled', true);
                
                $.ajax({
                    url: storebranchesUrl,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showToast('success', 'Success!', response.message);
                        $('#campusform')[0].reset();
                        loadcampuses();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#pmodesform').on('submit', function(e) { 
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);

                var form = this; // Reference the form element
                const storepaytypesUrl = form.dataset.storepaytypesUrl;
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Saving…').prop('disabled', true);
                
                $.ajax({
                    url: storepaytypesUrl,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showToast('success', 'Success!', response.message);
                        $('#pmodesform')[0].reset();
                        loadptypes();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#editpmodesForm').on('submit', function (e) {  
    e.preventDefault();
    const form = $(this);
    const id = $('#editpmodeModal #ID').val(); // Fetch the ID value

    const formData = new FormData(this);
    formData.append('_method', 'POST'); // Simulating PUT

    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Posting…').prop('disabled', true);

    $.ajax({
        url: App.routes.pmodes.replace('__id__', id), // Adjusted correctly
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            showToast('success', 'Success!', response.message);
            $('#editpmodeModal').modal('hide');
            form[0].reset();
            loadptypes(); // Reload the table
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    $(`#${key}-error`).html(value[0]);
                });
                showToast('danger', 'Error!', 'Please check the form for errors.');
            } else {
                showToast('danger', 'Error!', 'Error updating pay mode.');
            }
        },
        complete: function () {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});
$('#edithouseForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#edithouseForm #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Posting…').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: App.routes.deptsup.replace('__id__', id), // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showToast('success', 'Success!', response.message);
                       
                        hideModal('edithouseForm');
                        loaddepts(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#deptsform').on('submit', function(e) { 
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);

                var form = this; // Reference the form element
                const storedeptsUrl = form.dataset.storedeptsUrl;
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Saving…').prop('disabled', true);
                
                $.ajax({
                    url: storedeptsUrl,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showToast('success', 'Success!', response.message);
                        $('#deptsform')[0].reset();
                        loaddepts();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#banksform').on('submit', function(e) { 
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);

                 var form = this; // Reference the form element
                 const storebanksUrl = form.dataset.storebanksUrl;
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Saving…').prop('disabled', true);
                
                $.ajax({
                    url: storebanksUrl,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showToast('success', 'Success!', response.message);
                        $('#banksform')[0].reset();
                        loadbanks();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#compbanksform').on('submit', function(e) { 
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);

                var form = this; // Reference the form element
                const storecompbUrl = form.dataset.storecompbUrl;
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Saving…').prop('disabled', true);
                
                $.ajax({
                    url: storecompbUrl,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showToast('success', 'Success!', response.message);
                        $('#compbanksform')[0].reset();
                        loadcompb();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#editcampuslForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editcampusModal #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Posting…').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: App.routes.branchesup.replace('__id__', id), // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showToast('success', 'Success!', response.message);
                        
                        hideModal('editcampusModal');
                        loadcampuses(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#editBankForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editBankForm #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Posting…').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: App.routes.banksup.replace('__id__', id), // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showToast('success', 'Success!', response.message);
                       
                        hideModal('editBankForm');
                        loadbanks(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#editcompBankForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editcompBankForm #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Posting…').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: App.routes.compbup.replace('__id__', id), // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showToast('success', 'Success!', response.message);
                        
                        hideModal('editcompBankForm');
                        loadcompb(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
             $.ajax({
        url: App.routes.branches,
        type: "GET",
        success: function (response) {
            const dropdown = $('#branch2');
            const dropdown1 = $('#branch3'); // The dropdown element
            const dropdown2 = $('#branch4');
            dropdown.empty();
            dropdown1.empty(); // Clear existing options
            dropdown2.empty();

            // Add default options
            dropdown.append('<option value="">Select campus</option>');
            dropdown.append('<option value="0">Overall</option>');
            dropdown1.append('<option value="">Select campus</option>');
            dropdown1.append('<option value="0">Overall</option>');
            dropdown2.append('<option value="">Select campus</option>');
            dropdown2.append('<option value="0">Overall</option>');
            

            response.data.forEach(function (branch) {
                const $option = $('<option>')
                .val(branch.ID)           // ✅ .val() automatically escapes
                .text(branch.branchname); // ✅ .text() never renders HTML
                dropdown.append($option);
                dropdown1.append($option);
                dropdown2.append($option);
            });
        },
        error: function () {
            alert('Failed to load branches. Please try again.');
        },
    });

    $(document).on('click', '[data-target="#editcampusModal"]', function () {
    const id = $(this).data('id');
    const branchname = $(this).data('branchname');
    

    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#editcampuslForm');
    form.find('#ID').val(id);
    form.find('#editbranchname').val(branchname);
   
});
$(document).on('click', '[data-target="#edithouseModal"]', function () {
    const id = $(this).data('id');
    const branch = $(this).data('brid');
    const department = $(this).data('departmentname');
    

    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#edithouseForm');
    form.find('#ID').val(id);
    form.find('#branch3').val(branch);
    form.find('#edithousename').val(department);

   
   
});
$(document).on('click', '[data-target="#editBankModal"]', function () {
    const id = $(this).data('id');
    const Bank = $(this).data('bank');
    const BankCode = $(this).data('bankcode');
    const Branch = $(this).data('branch');
    const BranchCode = $(this).data('branchcode');
    const swiftcode = $(this).data('swiftcode');
   
    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#editBankForm');
    form.find('#ID').val(id);
    form.find('#bankName').val(Bank);
    form.find('#bankCode').val(BankCode);
    form.find('#branchName').val(Branch);
    form.find('#branchCode').val(BranchCode);
    form.find('#swiftcode').val(swiftcode);
   
});
$(document).on('click', '[data-target="#editcompBankModal"]', function () {
    const id = $(this).data('id');
    const Bank = $(this).data('bank');
    const BankCode = $(this).data('bankcode');
    const Branch = $(this).data('branch');
    const BranchCode = $(this).data('branchcode');
    const swiftcode = $(this).data('swiftcode');
     const account = $(this).data('account');
    
    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#editcompBankForm');
    form.find('#ID').val(id);
    form.find('#bankName').val(Bank);
    form.find('#bankCode').val(BankCode);
    form.find('#branchName').val(Branch);
    form.find('#branchCode').val(BranchCode);
    form.find('#swiftcode').val(swiftcode);
    form.find('#accno1').val(account);
   
});
$(document).on('click', '[data-target="#editpmodeModal"]', function () { 
    const id = $(this).data('id');
    const pname = $(this).data('pname');
    

    // Clear previous errors
    $('.text-danger').html('');

    // Set form values
    const form = $('#editpmodesForm');
    form.find('#ID').val(id);
    form.find('#epmoden').val(pname);

});
$('#tab-depts').on('click', function() {

    $.ajax({
        url: App.routes.branches,
        type: "GET",
        success: function (response) {
            const dropdown = $('#brid');
           
            dropdown.empty();
           

            // Add default options
            dropdown.append('<option value="">Select Branch</option>');
            dropdown.append('<option value="0">Overall</option>');
           

            response.data.forEach(function (branch) {
                const $option = $('<option>')
                .val(branch.ID)           
                .text(branch.branchname); 
                dropdown.append($option);
            });
        },
        error: function () {
            alert('Failed to load branches. Please try again.');
        },
    });
    loaddepts();


});
$('#tabactive').on('click', function () {
        openTab(event,'taborgstruct');
    });

    $('#tabbranches').on('click', function () {
       openTab(event,'tabstatcodes');
    });

     $('#tab-depts').on('click', function () {
       openTab(event,'tabdepts');
    });

     $('#tab-banks').on('click', function () {
       openTab(event,'tabstreams');
    });

     $('#tab-compbank').on('click', function () {
       openTab(event,'tabcompbank');
    });

    $('#tab-econfig').on('click', function () {
       openTab(event,'tabfcategories');
    });

    $('#tabpaymodes').on('click', function () {
       openTab(event,'tabfpaymodes');
    });
$('#tab-banks').on('click', function() {
loadbanks();
});loadcompb
$('#tab-compbank').on('click', function() {
loadcompb();
});
 $('#tab-econfig').on('click', function() {
});
 $('#file').on('change', function() {
validateFile(file);
});  
});
        function validateFile(inputId) {
    const fileInput = document.getElementById(inputId);
    const file = fileInput.files[0];
    const allowedTypes = ['image/png', 'image/jpeg'];
    const maxSize = 2 * 1024 * 1024; // 2 MB

    if (!allowedTypes.includes(file.type)) {
        alert('Only PNG and JPEG files are allowed.');
        fileInput.value = ''; // Reset the input
        return false;
    }

    if (file.size > maxSize) {
        alert('File size should not exceed 2 MB.');
        fileInput.value = ''; // Reset the input
        return false;
    }
    return true;
}
function loadcampuses(page = 1) {
    $.ajax({
       url: `${App.routes.branchesgetall}?page=${page}`,
        type: "GET",
        success: function (response) {
            const tableBody = $('#campuses-table-body');
            const paginationControls = $('#pagination-controls');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function(row) {
    const tr = $('<tr>');
    
    // ✅ ID cell
    tr.append($('<td>').text(row.ID));
    
    // ✅ Branch name cell
    tr.append($('<td>').text(row.branchname));
    
    // ✅ Actions cell with dropdown
    const actionsTd = $('<td>');
    
    // Dropdown wrapper
    const dropdownDiv = $('<div>').addClass('dropdown');
    
    // Dropdown toggle button
    const dropdownToggle = $('<a>')
        .addClass('btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle')
        .attr('href', '#')
        .attr('role', 'button')
        .attr('data-toggle', 'dropdown');
    
    const toggleIcon = $('<span>')
        .addClass('material-icons')
        .text('more_horiz');
    
    dropdownToggle.append(toggleIcon);
    dropdownDiv.append(dropdownToggle);
    
    // Dropdown menu
    const dropdownMenu = $('<div>')
        .addClass('dropdown-menu dropdown-menu-right dropdown-menu-icon-list');
    
    // Edit menu item
    const editItem = $('<a>')
        .addClass('dropdown-item')
        .attr('href', '#')
        .attr('data-toggle', 'modal')
        .attr('data-target', '#editcampusModal')
        .attr('data-id', row.ID)
        .attr('data-branchname', row.branchname);
    
    const editIcon = $('<span>')
        .addClass('material-icons')
        .text('edit_note');
    
    // ✅ Use DOM text node for "Edit" text
    editItem.append(editIcon);
    editItem.append(document.createTextNode(' Edit'));
    
    dropdownMenu.append(editItem);
    dropdownDiv.append(dropdownMenu);
    actionsTd.append(dropdownDiv);
    tr.append(actionsTd);
    
    tableBody.append(tr);
});

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

           
        },
        error: function () {
            showToast('danger', 'Error!', 'Failed to load table data');
        }
    });
}
// Add once at top of static.js
function sanitize(str) {
    return $('<div>').text(String(str ?? '')).html();
}

function sanitizeRow(row) {
    return {
        ID:         sanitize(row.ID),
        name:       sanitize(row.name),
        motto:      sanitize(row.motto),
        pobox:      sanitize(row.pobox),
        email:      sanitize(row.email),
        physaddres: sanitize(row.physaddres),
        logo:       sanitize(row.logo || ''),
    };
}
function loadTableData() {
    $.ajax({
        url: App.routes.getallstaticinfo,
        type: "GET",
        success: function(response) {
            const tableBody = $('#structure-table-body');
            tableBody.empty();
            
            response.data.forEach(function(rawRow) {
    const row = sanitizeRow(rawRow); // ← sanitize everything at entry
    const tr = $('<tr>');

    tr.append($('<td>').attr('hidden', true).text(row.ID));
    tr.append($('<td>').text(row.name));

    const logoTd = $('<td>');
    const logoImg = $('<img>').addClass('logotable').attr('alt', 'Logo');
    if (row.logo) logoImg.attr('src', row.logo);
    logoTd.append(logoImg);
    tr.append(logoTd);

    tr.append($('<td>').text(row.motto));
    tr.append($('<td>').attr('hidden', true).text(row.pobox));
    tr.append($('<td>').attr('hidden', true).text(row.email));
    tr.append($('<td>').attr('hidden', true).text(row.physaddres));

    const actionsTd = $('<td>');
    const editLink = $('<a>')
        .addClass('btn btn-link font-24 p-0 line-height-1 no-arrow')
        .attr('href', '#')
        .attr('data-toggle', 'modal')
        .attr('data-target', '#editSchoolModal')
        .attr('data-id',        row.ID)
        .attr('data-name',      row.name)
        .attr('data-motto',     row.motto)
        .attr('data-pobox',     row.pobox)
        .attr('data-email',     row.email)
        .attr('data-physaddres',row.physaddres)
        .attr('data-logo',      row.logo);

    editLink.append($('<span>').addClass('material-icons').text('edit_note'));
    actionsTd.append(editLink); // line 1012
    tr.append(actionsTd);
    tableBody.append(tr);
});
        },
        error: function(xhr) {
            showToast('danger', 'Error!', 'Failed to load table data');
        }
    });
}

$(document).on('click', '#pagination-controls button', function () {
    const page = $(this).data('page');
    loadcampuses(page);
});

function confirmDeletion(ID, branchname) {
    swal({
        title: 'Are you sure?',
        text: `Are you sure you want to delete: "${branchname}"?`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        // Only proceed if the user clicked confirm
        if (result.value === true) {  // Check specifically for true
            // Perform the AJAX request for deletion
            $.ajax({
                url: App.routes.static.replace('__id__', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            title: 'Deleted!',
                            text: 'Branch deleted successfully!',
                            icon: 'success',
                            buttons: false,
                            timer: 2000
                        });
                        loadcampuses();
                    } else {
                        swal({
                            title: 'Error!',
                            text: response.message || 'Failed to delete.',
                            icon: 'error',
                            buttons: true
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    swal({
                        title: 'Failed!',
                        text: 'An error occurred while deleting the branch. Please try again.',
                        icon: 'error',
                        buttons: true
                    });
                }
            });
        }
    });
}

function sanitizedeptRow(row) {
    return {
        ID:    sanitize(row.ID),
        brid: sanitize(row.brid),
        branchname: sanitize(row.branchname),
        DepartmentName: sanitize(row.DepartmentName),
    };
}
function loaddepts(page = 1) {
    $.ajax({
        url: `${App.routes.deptsgetall}?page=${page}`,
        type: "GET",
        success: function (response) {
            const tableBody = $('#depts-table-body');
            const paginationControls = $('#pagination-depts');
            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
           response.data.forEach(function(rawRow) {
    const row = sanitizedeptRow(rawRow); // ← sanitize at entry
    const tr = $('<tr>');

    tr.append($('<td hidden>').text(row.ID));
    tr.append($('<td>').text(row.brid));
    tr.append($('<td>').text(row.branchname));
    tr.append($('<td>').text(row.DepartmentName));

    const actionsTd = $('<td>');
    const dropdownDiv = $('<div>').addClass('dropdown');

    const dropdownToggle = $('<a>')
        .addClass('btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle')
        .attr('href', '#')
        .attr('role', 'button')
        .attr('data-toggle', 'dropdown')
        .append($('<span>').addClass('material-icons').text('more_horiz'));

    dropdownDiv.append(dropdownToggle);

    const editItem = $('<a>')
        .addClass('dropdown-item')
        .attr('href', '#')
        .attr('data-toggle', 'modal')
        .attr('data-target', '#edithouseModal')
        .attr('data-id',    row.ID)
        .attr('data-brid', row.brid)
        .attr('data-departmentname', row.DepartmentName)
        .append($('<span>').addClass('material-icons').text('edit_note'))
        .append(document.createTextNode(' Edit'));

    const dropdownMenu = $('<div>')
    .addClass('dropdown-menu dropdown-menu-right dropdown-menu-icon-list');

// ✅ Explicitly validate editItem is a safe jQuery object before appending
if (editItem instanceof $ || editItem.jquery) {
    // Verify it contains no unsafe content
    const itemHtml = editItem[0].outerHTML;
    if (!/<script|<img|<svg|<iframe|<object|<embed/i.test(itemHtml)) {
        dropdownMenu.append(editItem);
    } else {
        console.warn('Blocked potentially unsafe element');
    }
}

    dropdownDiv.append(dropdownMenu); //line 1123
    actionsTd.append(dropdownDiv);
    tr.append(actionsTd);
    tableBody.append(tr);
});

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

            // Add click event for pagination buttons
            paginationControls.find('button').on('click', function () {
                const page = $(this).data('page');
                loaddepts(page); // Load houses for the clicked page
            });
        },
        error: function () {
            showToast('danger', 'Error!', 'Failed to load table data');
        }
    });
}
function sanitizebankRow(row) {
    return {
        ID:    sanitize(row.ID),
        Bank: sanitize(row.Bank),
        BankCode: sanitize(row.BankCode),
        Branch: sanitize(row.Branch),
        BranchCode: sanitize(row.BranchCode),
        swiftcode: sanitize(row.swiftcode),
    };
}
function loadbanks(page = 1) {
    $.ajax({
        url: `${App.routes.banksgetall}?page=${page}`,
        type: "GET",
        success: function (response) {
            const tableBody = $('#banks-table-body');
            const paginationControls = $('#pagination-banks');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
           response.data.forEach(function(rawRow) {
    const row = sanitizebankRow(rawRow); // ← sanitize at entry
    const tr = $('<tr>');

    tr.append($('<td hidden>').text(row.ID));
    tr.append($('<td>').text(row.Bank));
    tr.append($('<td>').text(row.BankCode));
    tr.append($('<td>').text(row.Branch));
    tr.append($('<td>').text(row.BranchCode));
    tr.append($('<td>').text(row.swiftcode));

    const actionsTd = $('<td>');
    const dropdownDiv = $('<div>').addClass('dropdown');

    const dropdownToggle = $('<a>')
        .addClass('btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle')
        .attr('href', '#')
        .attr('role', 'button')
        .attr('data-toggle', 'dropdown')
        .append($('<span>').addClass('material-icons').text('more_horiz'));

    dropdownDiv.append(dropdownToggle);

    const editItem = $('<a>')
        .addClass('dropdown-item')
        .attr('href', '#')
        .attr('data-toggle', 'modal')
        .attr('data-target', '#editBankModal')
        .attr('data-id', row.ID)
         .attr('data-bank', row.Bank)
         .attr('data-bankcode', row.BankCode)
         .attr('data-branch', row.Branch)
         .attr('data-branchcode', row.BranchCode)
        .attr('data-swiftcode', row.swiftcode)
        .append($('<span>').addClass('material-icons').text('edit_note'))
        .append(document.createTextNode(' Edit'));

    const dropdownMenu = $('<div>')
    .addClass('dropdown-menu dropdown-menu-right dropdown-menu-icon-list');

// ✅ Direct append - editItem was built with safe jQuery methods (.attr(), .text())
// No HTML strings were used in its construction
dropdownMenu.append(editItem);

    dropdownDiv.append(dropdownMenu);
    actionsTd.append(dropdownDiv);
    tr.append(actionsTd);
    tableBody.append(tr);
});

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

            // Add click event for pagination buttons
            paginationControls.find('button').on('click', function () {
                const page = $(this).data('page');
                loadbanks(page); // Load houses for the clicked page
            });
        },
        error: function () {
            showToast('danger', 'Error!', 'Failed to load table data');
        }
    });
}
function sanitizeempbankRow(row) {
    return {
        ID:    sanitize(row.ID),
        Bank: sanitize(row.Bank),
        BankCode: sanitize(row.BankCode),
        Branch: sanitize(row.Branch),
        BranchCode: sanitize(row.BranchCode),
        swiftcode: sanitize(row.swiftcode),
        swiftcode: sanitize(row.swiftcode),
    };
}
function loadcompb(page = 1) {
    $.ajax({
        url: `${App.routes.compbgetall}?page=${page}`,
        type: "GET",
        success: function (response) {
            const tableBody = $('#compb-table-body');
            const paginationControls = $('#pagination-compb');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function(rawRow) {
    const row = sanitizeempbankRow(rawRow); // ← sanitize at entry
    const tr = $('<tr>');

    ttr.append($('<td hidden>').text(row.ID));
    tr.append($('<td>').text(row.Bank));
    tr.append($('<td>').text(row.BankCode));
    tr.append($('<td>').text(row.Branch));
    tr.append($('<td>').text(row.BranchCode));
    tr.append($('<td>').text(row.swiftcode));
    tr.append($('<td>').text(row.accno));

    const actionsTd = $('<td>');
    const dropdownDiv = $('<div>').addClass('dropdown');

    const dropdownToggle = $('<a>')
        .addClass('btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle')
        .attr('href', '#')
        .attr('role', 'button')
        .attr('data-toggle', 'dropdown')
        .append($('<span>').addClass('material-icons').text('more_horiz'));

    dropdownDiv.append(dropdownToggle);

    const editItem = $('<a>')
        .addClass('dropdown-item')
        .attr('href', '#')
        .attr('data-toggle', 'modal')
        .attr('data-target', '#editcompBankModal')
        .attr('data-id', row.ID)
         .attr('data-bank', row.Bank)
         .attr('data-bankcode', row.BankCode)
         .attr('data-branch', row.Branch)
         .attr('data-branchcode', row.BranchCode)
        .attr('data-swiftcode', row.swiftcode)
        .attr('data-account', row.accno)
        .append($('<span>').addClass('material-icons').text('edit_note'))
        .append(document.createTextNode(' Edit'));

    const dropdownMenu = $('<div>')
    .addClass('dropdown-menu dropdown-menu-right dropdown-menu-icon-list');

// ✅ Explicitly validate editItem is a safe jQuery object before appending
if (editItem instanceof $ || editItem.jquery) {
    // Verify it contains no unsafe content
    const itemHtml = editItem[0].outerHTML;
    if (!/<script|<img|<svg|<iframe|<object|<embed/i.test(itemHtml)) {
        dropdownMenu.append(editItem);
    } else {
        console.warn('Blocked potentially unsafe element');
    }
}

    dropdownDiv.append(dropdownMenu);
    actionsTd.append(dropdownDiv);
    tr.append(actionsTd);
    tableBody.append(tr);
});

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

            // Add click event for pagination buttons
            paginationControls.find('button').on('click', function () {
                const page = $(this).data('page');
                loadcompb(page); // Load houses for the clicked page
            });
        },
        error: function () {
            showToast('danger', 'Error!', 'Failed to load table data');
        }
    });
}
function sanitizePaymentRow(row) {
    return {
        ID:    sanitize(row.ID),
        pname: sanitize(row.pname),
    };
}
function loadptypes(page = 1) {
    $.ajax({
        url: `${App.routes.paytypesgetall}?page=${page}`,
        type: "GET",
        success: function (response) {
            const tableBody = $('#pmodes-table-body');
            const paginationControls = $('#pagination-pmodes');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function(rawRow) {
    const row = sanitizePaymentRow(rawRow); // ← sanitize at entry
    const tr = $('<tr>');

    tr.append($('<td hidden>').text(row.ID));
    tr.append($('<td>').text(row.pname));

    const actionsTd = $('<td>');
    const dropdownDiv = $('<div>').addClass('dropdown');

    const dropdownToggle = $('<a>')
        .addClass('btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle')
        .attr('href', '#')
        .attr('role', 'button')
        .attr('data-toggle', 'dropdown')
        .append($('<span>').addClass('material-icons').text('more_horiz'));

    dropdownDiv.append(dropdownToggle);

    const editItem = $('<a>')
        .addClass('dropdown-item')
        .attr('href', '#')
        .attr('data-toggle', 'modal')
        .attr('data-target', '#editpmodeModal')
        .attr('data-id',    row.ID)
        .attr('data-pname', row.pname)
        .append($('<span>').addClass('material-icons').text('edit_note'))
        .append(document.createTextNode(' Edit'));

    const dropdownMenu = $('<div>')
    .addClass('dropdown-menu dropdown-menu-right dropdown-menu-icon-list');

// ✅ Explicitly validate editItem is a safe jQuery object before appending
if (editItem instanceof $ || editItem.jquery) {
    // Verify it contains no unsafe content
    const itemHtml = editItem[0].outerHTML;
    if (!/<script|<img|<svg|<iframe|<object|<embed/i.test(itemHtml)) {
        dropdownMenu.append(editItem);
    } else {
        console.warn('Blocked potentially unsafe element');
    }
}

    dropdownDiv.append(dropdownMenu);
    actionsTd.append(dropdownDiv);
    tr.append(actionsTd);
    tableBody.append(tr);
});

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

            // Add click event for pagination buttons
            paginationControls.find('button').on('click', function () {
                const page = $(this).data('page');
                loadptypes(page); // Load houses for the clicked page
            });
        },
        error: function () {
            showToast('danger', 'Error!', 'Failed to load table data');
        }
    });
}
// Add this helper once at the top of your file
function sanitize2(str) {
    return $('<div>').text(String(str)).html();
}

function showToast(type, title, message) {
    const icons = { 
        success: 'check_circle', 
        danger: 'error_outline', 
        warning: 'warning_amber', 
        info: 'info' 
    };

    // Sanitize all remote inputs at entry point
    const safeType    = sanitize2(type);
    const safeTitle   = sanitize2(title);
    const safeMessage = sanitize2(message);

    const iconSpan = $('<span>')
        .addClass('material-icons')
        .text(icons[safeType] || 'info');

    const strong = $('<strong>').text(safeTitle);

    const messageDiv = $('<div>')
        .append(strong)
        .append(document.createTextNode(' ' + safeMessage));

    const t = $('<div>')
        .addClass('toast-msg ' + safeType)
        .append(iconSpan)
        .append(messageDiv);

    $('#toastWrap').append(t);

    const dismiss = () => { t.addClass('leaving'); setTimeout(() => t.remove(), 300); };
    t.on('click', dismiss);
    setTimeout(dismiss, 5000);
}
            $('.close').on('click', function() {
                const alert = $(this).closest('.custom-alert');
                alert.removeClass('show');
                setTimeout(() => {
                    alert.hide();
                }, 500);
            });