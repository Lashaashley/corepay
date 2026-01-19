<x-custom-admin-layout>
   <style>
     .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: var(--modal-shadow);
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            overflow: hidden;
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem 2rem;
            border: none;
            position: relative;
        }

        .modal-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.3) 100%);
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.4rem;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .modal-title i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
           	.tab-container {
    display: flex;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
}

.tab-button {
    background-color: #f8f9fa;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 10px 20px;
    font-size: 12.5px;
    transition: background-color 0.3s;
}

.tab-button:hover {
    background-color: #e9ecef;
}

.tab-button.active {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff; /* Hide border bottom when active */
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}
.action-buttons {
            padding: 1px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-enhanced {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-draft {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1);
            color: white;
        }
        
        .btn-finalize {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .btn-cancel {
            background: linear-gradient(135deg, #e93a04ff, #d62f05ff);
            color: white;
        }  
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 9999;
            transform: translateX(400px);
            transition: all 0.5s ease;
        }
        
        .custom-alert.show {
            transform: translateX(0);
        }
        
        .alert-success {
            animation: successPulse 1s ease-in-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(0.95); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
   </style>
    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="card-box mb-30">
                
                
                <div class="pb-20 px-20">
                    <table id="agents-table" class="data-table table stripe hover nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th class="table-plus">Full Name</th>
                                <th>Work No</th>
                                <th>Staff Type</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>State</th>
                                <th class="datatable-nosort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editstaffModal"tabindex="-1" aria-labelledby="electiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="electiveModalLabel">
                        <i class="fas fa-user"></i>
                        Edit Agent
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="tab-container" style="margin-top: -30px;">
                <button class="tab-button active" onclick="openTab(event, 'contactInfo')">Staff Information</button>
                <button class="tab-button" id="tab-registration" onclick="openTab(event, 'registration')">Registration</button>
            </div>
                <div class="modal-body">
                    <div id="contactInfo" class="tab-content active" style="margin-top: -30px;">
                    
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4 class="text-blue h5">New Agent Form</h4>
                               
                            </div>
                        </div>
                        <div class="wizard-content">
                            <section>
                                <form method="post" name="staffForm" id="staffForm" enctype="multipart/form-data" >
                                   @csrf
                                    
                                    <div class="row">
                                        
                                        <div class="col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label >First Name :</label>
                                                <input name="firstname" id="firstname" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label >Last Name :</label>
                                                <input name="lastname" id="lastname" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label>Agent Number :</label>
                                                <input name="agentno" id="agentno" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label>Email Address :</label>
                                                <input name="email" id="email" type="email" class="form-control"  autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label>Phone Number :</label>
                                                <input name="phonenumber" id="phonenumber" type="text" class="form-control"  autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label>Branch:</label>
                                                <select name="brid" id="brid" class="custom-select form-control" required="true" autocomplete="off">
                                                    <option value="">Select Branch</option>
                                                </select>
                                                <small id="brid-error" class="text-danger"></small>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label>Department:</label>
                                                <select name="dept" id="dept" class="custom-select form-control" required="true" autocomplete="off">
                                                    <option value="">Select Department</option>
                                                </select>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label>Date Of Birth :</label>
                                                <input name="dob" type="text" class="form-control date-picker"  autocomplete="off">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label>Gender :</label>
                                                <select name="gender" id="gender" class="custom-select form-control"  autocomplete="off">
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                        </div>

									</div>
                                   
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label style="font-size:16px;"><b></b></label>
                                                <div class="modal-footer justify-content-center">
                                                    
                                                    <button id="add_staff" type="submit" class="btn btn-enhanced btn-finalize">
                                                        <i class="fas fa-save"></i> Save Agent
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </form>
                            </section>
                        
                    </div>
                    </div>
                    <div id="registration" class="tab-content" style="margin-top: -30px;">
    
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="text-blue h5">Registration Info Form</h4>
                
            </div>
        </div>
        <div class="wizard-content">
            <section>
                <form method="post" action="" id="registrationForm" enctype="multipart/form-data">
                    @csrf
                    <input name="aggentno" type="text" id="aggentno"  value="" readonly hidden>
                    
                    <div class="row">
                        
                        
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>Statutory Deductions:</label>
                                <div class="checkbox-container d-flex">
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="nhif_shif" name="nhif_shif" value="YES" class="form-check-input">
                                        <label for="nhif_shif" class="form-check-label">NHIF/SHIF</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="nssf" name="nssf" value="YES" class="form-check-input">
                                        <label for="nssf" class="form-check-label">NSSF</label>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Is Agent:</label>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" id="contractor" name="contractor" value="YES" class="form-check-input">
                                    <label for="contractor" class="form-check-label">Agent</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Union:</label>
                                <div class="checkbox-container d-flex">
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="unionized" name="unionized" value="YES" class="form-check-input">
                                        <label for="unionized" class="form-check-label">Unionized</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12" id="union-container" style="display:none;">
                            <div class="form-group">
                                <label>Union Number:</label>
                                <input name="unionno" id="unionno" type="text" class="form-control wizard-required"  autocomplete="off" value="N/A">
                            </div>
                        </div>

                        
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>ID NO:</label>
                                <input name="idno" id="idno" type="text" class="form-control wizard-required"  autocomplete="off" Placeholder="Type here..">
                               
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>SHIF No:</label>
                                <input name="nhifno" id="nhifno" type="text" class="form-control wizard-required"  autocomplete="off" Placeholder="Type here..">
                                
                            </div>
                        </div>
                        
                        
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>KRA pin:</label>
                                <input name="krapin" id="krapin" type="text" class="form-control wizard"  autocomplete="off" Placeholder="AQ..">
                                
                            </div>
                        </div>
                    </div>
					<div class="row">
                        
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>NSSF No.:
                                    <div class="form-check form-check-inline">
                                        <label for="nssfopt" class="form-check-label"><strong>Opt Out nssf:</strong></label>
                                        <input type="checkbox" id="nssfopt" name="nssfopt" value="YES" class="form-check-input">
                                    </div>
                                </label>
                                <input name="nssfno" id="nssfno" type="text" class="form-control wizard-required"  autocomplete="off" Placeholder="Type here..">                                
                            </div>
                        </div>
						

                        
                        
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label class="form-check-label" for="paymentMethod">Payment Method:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod" value="Etransfer" checked>
                                    <label class="form-check-label" for="etf">E-transfer</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod" value="Cheque">
                                    <label class="form-check-label" for="cheque">Cheque</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Payroll Type:</label>
                                <select name="proltype" id="proltype" class="custom-select form-control" required="true" autocomplete="off">
                                    <option value="">Select type</option>
                                    
                                    </select>
                                </div>
                        </div>
                    

                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Bank :</label>
                                <select name="bank" id="bank" class="custom-select form-control"  autocomplete="off" required>
                                    <option value="">Select Bank</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Bank Branch:</label>
                                    <select name="branch" id="branch" class="custom-select form-control"  autocomplete="off">
                                        <option value="">Select Bank Branch</option>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Branch Code:</label>
                                <input name="bcode" id="bcode" type="text" class="form-control"  autocomplete="off" readonly>
                               
                                <input name="bankcode" id="bankcode" type="text" class="form-control" autocomplete="off" hidden>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Swift Code:</label>
                                
                                <input name="swiftcode" id="swiftcode" type="text" class="form-control"  autocomplete="off">
                                
                            </div>
                        </div>
						<div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Account Number:</label>
                                <input name="account" id="account" type="text" class="form-control" required="true" autocomplete="off">
                            </div>
                        </div>
                    </div>
					

                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <div class="modal-footer justify-content-center">
                                    
                                
                                <button id="load" type="submit" class="btn btn-enhanced btn-finalize">
                                    <i class="fas fa-save"></i> Save
                                </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                </section>
        </div>
   
                </div>

            </div>
        </div>
    </div>

    <!-- Terminate Modal -->
    <div class="modal fade" id="terminatModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Terminate Staff</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to terminate this staff member?</p>
                    <input type="hidden" id="terminate-agent-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-terminate">Terminate</button>
                </div>
            </div>
        </div>
    </div>

    
    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        const amanage = '{{ route("agents.data") }}';
        const branches = '{{ route("branches.getDropdown") }}';
        const depts = '{{ route("depts.getDropdown") }}';
        const getbanks = '{{ route("banks.getDropdown") }}';
        const getbranches = '{{ route("brbranches.getDropdown") }}';
        const getuser = '{{ route("get.agent", ":id") }}';
        const getptypes = '{{ route("paytypes.getDropdown") }}';
        const getbybank = '{{ route("branches.getByBank") }}';
        const codebybank = '{{ route("codes.getByBank") }}';
    </script>
    <script src="{{ asset('js/amanage.js') }}"></script>
    
    <script> 
      $(document).ready(function() {
         $('#staffForm').on('submit', function (e) {
    e.preventDefault();
    
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    const id = $('#agentno').val();
    const formData = new FormData(this);

    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    
    formData.append('_method', 'POST');
    
    $.ajax({ 
        url: `{{ url('agent') }}/${id}`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            showAlert('success', 'Success!', response.message);
            $('#editstaffModal').modal('hide');
            
            // Optionally reload the data table or refresh the page
            if (typeof table !== 'undefined') {
                table.ajax.reload();
            }
        },
        error: function (xhr) {
            console.error('Error response:', xhr.responseJSON);
            
            if (xhr.status === 422) {
                // Validation errors
                let errors = xhr.responseJSON.errors;
                
                $.each(errors, function (key, messages) {
                    // Find the input field
                    let input = $(`[name="${key}"]`);
                    
                    // Add error class
                    input.addClass('is-invalid');
                    
                    // Add error message
                    input.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                });
                
                showAlert('danger', 'Validation Error!', 'Please check the form for errors.');
            } else if (xhr.status === 404) {
                showAlert('danger', 'Error!', 'Agent not found.');
            } else {
                let errorMessage = xhr.responseJSON?.message || 'Error updating agent.';
                showAlert('danger', 'Error!', errorMessage);
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});
 $('#registrationForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#aggentno').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: `{{ url('regagent') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        
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
            $('#bank').on('change', function() {
                const selectedCampusId = $(this).val();
                if (selectedCampusId) {
                    loadBranches2(selectedCampusId);
                } else {
                    const classDropdown = $('#branch');
                    classDropdown.empty();
                    classDropdown.append('<option value="">Select Branch</option>');
                }
        
            });
            $('#branch').on('change', function() {
                const branch = $(this).val();
                const bank = $('#bank').val();
                if (branch) {
                    fetchcodes2(bank,branch);
                } else {

                }
        
            });
      });
       function loadBranches2(campusId) {
        $.ajax({
          url: "{{ route('branches.getByBank') }}",
          type: "GET",
          data: { campusId: campusId },
          success: function (response) {
            const dropdown = $('#branch');
            dropdown.empty();
            dropdown.append('<option value="">Select Branch</option>');
            response.data.forEach(function (branches) {
              dropdown.append(
                `<option value="${branches.Branch}">${branches.Branch}</option>`
              );
              
            });
          },
          error: function () {
            alert('Failed to load classes. Please try again.');
          }
        });
      }
      function fetchcodes2(bank, branch) {
        $.ajax({
          url: "{{ route('codes.getByBank') }}",
          type: "GET",
          data: { bank: bank,
            branch: branch
           },
          success: function (response) {
            response.data.forEach(function (branches) {
              document.getElementById('bcode').value = branches.BranchCode;
              document.getElementById('swiftcode').value = branches.swiftcode;
              document.getElementById('bankcode').value = branches.BankCode;
              
            });
          },
          error: function () {
            alert('Failed to load classes. Please try again.');
          }
        });
      }
    </script>
    
   
</x-custom-admin-layout>