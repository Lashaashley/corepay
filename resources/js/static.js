


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
                        showAlert('success', 'Success!', response.message);
                        $('#orgstrucf')[0].reset();
                        loadTableData();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            

    

           
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
                        showAlert('success', 'Success!', response.message);
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
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
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
                        showAlert('success', 'Success!', response.message);
                        
                        hideModal('editmailForm');
                        loadeconfig(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
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
                        showAlert('success', 'Success!', response.message);
                        $('#campusform')[0].reset();
                        loadcampuses();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
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
                        showAlert('success', 'Success!', response.message);
                        $('#pmodesform')[0].reset();
                        loadptypes();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
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
            showAlert('success', 'Success!', response.message);
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
                showAlert('danger', 'Error!', 'Please check the form for errors.');
            } else {
                showAlert('danger', 'Error!', 'Error updating pay mode.');
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
                        showAlert('success', 'Success!', response.message);
                       
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
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
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
                        showAlert('success', 'Success!', response.message);
                        $('#deptsform')[0].reset();
                        loaddepts();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
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
                        showAlert('success', 'Success!', response.message);
                        $('#banksform')[0].reset();
                        loadbanks();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
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
                        showAlert('success', 'Success!', response.message);
                        $('#compbanksform')[0].reset();
                        loadcompb();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
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
                        showAlert('success', 'Success!', response.message);
                        
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
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
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
                        showAlert('success', 'Success!', response.message);
                       
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
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
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
                        showAlert('success', 'Success!', response.message);
                        
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
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
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


            // Populate with branches
            response.data.forEach(function (branch) {
                dropdown.append(
                    `<option value="${branch.ID}">${branch.branchname}</option>`
                );
                dropdown1.append(
                    `<option value="${branch.ID}">${branch.branchname}</option>`
                );
                dropdown2.append(
                    `<option value="${branch.ID}">${branch.branchname}</option>`
                );
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
           


            // Populate with branches
            response.data.forEach(function (branch) {
                dropdown.append(
                    `<option value="${branch.ID}">${branch.branchname}</option>`
                );
                
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
loadeconfig();
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
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td>${row.branchname}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
    <span class="material-icons">more_horiz</span>
</a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editcampusModal"
                                    data-id="${row.ID}"
                                    data-branchname="${row.branchname}">
                                    <span class="material-icons">edit_note</span> Edit
</a>
                                
                            </div>
                        </div>
                    </td>
                `);
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
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}

function loadTableData() {
    $.ajax({
        url: App.routes.getallstaticinfo,
        type: "GET",
        success: function(response) {
            const tableBody = $('#structure-table-body');
            tableBody.empty();
            
            response.data.forEach(function(row) {
                const tr = $('<tr>').attr({
                    
                });
                
                tr.append(`
                    <td hidden>${row.ID}</td>
                    <td>${row.name}</td>
                    <td><img src="${row.logo}" class="logotable" alt="School Logo"></td>
                    <td>${row.motto}</td>
                    <td hidden>${row.pobox}</td>
                    <td hidden>${row.email}</td>
                    <td hidden>${row.physaddres}</td>
                    <td>
                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow"
                           href="#"
                           data-toggle="modal"
                           data-target="#editSchoolModal"
                           data-id="${row.ID}"
                           data-name="${row.name}"
                           data-motto="${row.motto}"
                           data-pobox="${row.pobox}"
                           data-email="${row.email}"
                           data-physaddres="${row.physaddres}"
                           data-logo="${row.logo}">
                            <span class="material-icons">edit_note</span>
</a>
                        </a>
                    </td>
                `);
                
                tableBody.append(tr);
            });
        },
        error: function(xhr) {
            showAlert('danger', 'Error!', 'Failed to load table data');
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
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td hidden>${row.brid}</td>
                    <td>${row.branchname}</td> <!-- Display branchname instead of brid -->
                    <td>${row.DepartmentName}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
    <span class="material-icons">more_horiz</span>
</a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#edithouseModal"
                                    data-id="${row.ID}"
                                    data-brid="${row.brid}"
                                    data-departmentname="${row.DepartmentName}"> <!-- Include branchname -->
                                    <span class="material-icons">edit_note</span> Edit
</a>
                               
                            </div>
                        </div>
                    </td>
                `);
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
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
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
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td hidden>${row.ID}</td>
                    <td>${row.Bank}</td>
                    <td>${row.BankCode}</td> <!-- Display branchname instead of brid -->
                    <td>${row.Branch}</td>
                    <td>${row.BranchCode}</td>
                    <td>${row.swiftcode}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
    <span class="material-icons">more_horiz</span>
</a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editBankModal"
                                    data-id="${row.ID}"
                                    data-bank="${row.Bank}"
                                    data-bankcode="${row.BankCode}"
                                    data-branch="${row.Branch}"
                                    data-branchcode="${row.BranchCode}"
                                    data-swiftcode="${row.swiftcode}"> <!-- Include branchname -->
                                    <span class="material-icons">edit_note</span> Edit
</a>
                                
                            </div>
                        </div>
                    </td>
                `);
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
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}
function loadeconfig(page = 1) {
    $.ajax({
        url: `${App.routes.econfiggetall}?page=${page}`,
        type: "GET",
        success: function (response) {
            const tableBody = $('#econfig-table-body');
            const paginationControls = $('#pagination-econfig');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td hidden>${row.id}</td>
                    <td>${row.name}</td>
                    <td>${row.host}</td> <!-- Display branchname instead of brid -->
                    <td>${row.port}</td>
                    <td>${row.username}</td>
                    <td>${row.password}</td>
                    <td>${row.encryption}</td>
                    <td>${row.from_email}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
    <span class="material-icons">more_horiz</span>
</a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editemailModal"
                                    data-id="${row.id}"
                                    data-name="${row.name}"
                                    data-host="${row.host}"
                                    data-port="${row.port}"
                                    data-username="${row.username}"
                                    data-password="${row.password}"
                                    data-from_email="${row.from_email}"
                                    data-encryption="${row.encryption}"> <!-- Include branchname -->
                                    <span class="material-icons">edit_note</span> Edit
</a>
                                
                            </div>
                        </div>
                    </td>
                `);
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
                loadeconfig(page); // Load houses for the clicked page
            });
        },
        error: function () {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
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
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td hidden>${row.ID}</td>
                    <td>${row.Bank}</td>
                    <td>${row.Bankcode}</td> <!-- Display branchname instead of brid -->
                    <td>${row.Branch}</td>
                    <td>${row.Branchcode}</td>
                    <td>${row.swiftcode}</td>
                    <td>${row.accno}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
    <span class="material-icons">more_horiz</span>
</a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editcompBankModal"
                                    data-id="${row.ID}"
                                    data-bank="${row.Bank}"
                                    data-bankcode="${row.Bankcode}"
                                    data-branch="${row.Branch}"
                                    data-branchcode="${row.Branchcode}"
                                    data-swiftcode="${row.swiftcode}"
                                    data-account="${row.accno}"> <!-- Include branchname -->
                                    <span class="material-icons">edit_note</span> Edit
</a>
                                
                            </div>
                        </div>
                    </td>
                `);
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
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
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
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td hidden>${row.ID}</td>
                    <td>${row.pname}</td>
                    
                    <td>
<div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
    <span class="material-icons">more_horiz</span>
</a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editpmodeModal"
                                    data-id="${row.ID}"
                                    data-pname="${row.pname}">
                                    <span class="material-icons">edit_note</span> Edit
</a>
                                <a class="dropdown-item" href="#" onclick="confirmDeletion(${row.ID}, '${row.pname}')">
                                    <i class="dw dw-delete-3"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                `);
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
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
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