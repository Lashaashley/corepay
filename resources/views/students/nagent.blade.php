<x-custom-admin-layout>
    <style>
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
    </style>
    <div class="mobile-menu-overlay"></div>
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px" style="margin-top: -20px;">
            <div class="tab-container" style="margin-top: -30px;">
                <button class="tab-button active" onclick="openTab(event, 'contactInfo')">Staff Information</button>
                <button class="tab-button" id="tab-registration" onclick="openTab(event, 'registration')">Registration</button>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                    <div id="contactInfo" class="tab-content active" style="margin-top: -30px;">
                    <div class="pd-20 card-box mb-30">
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
                                                <input name="firstname" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label >Last Name :</label>
                                                <input name="lastname" type="text" class="form-control" required="true" autocomplete="off">
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
                                                <input name="email" type="email" class="form-control"  autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label>Phone Number :</label>
                                                <input name="phonenumber" type="text" class="form-control"  autocomplete="off">
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
                                                <select name="gender" class="custom-select form-control"  autocomplete="off">
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
                </div>
                <div id="registration" class="tab-content" style="margin-top: -30px;">
    <div class="pd-20 card-box mb-30">
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
                                    <div class="form-check form-check-inline" hidden>
                                        <input type="checkbox" id="pensyes" name="pensyes" value="YES" class="form-check-input">
                                        <label for="pensyes" class="form-check-label">Pension</label>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Is Contract:</label>
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
                                <input name="idno" id="idno" type="text" class="form-control wizard-required" autocomplete="off" Placeholder="Type here..">
                               
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
                                <input name="krapin" type="text" class="form-control wizard-required"  autocomplete="off" Placeholder="AQ..">
                                
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
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="etf" value="Etransfer" checked>
                                    <label class="form-check-label" for="etf">E-transfer</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="cheque" value="Cheque">
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
                                <select name="bank" id="bank" class="custom-select form-control"  autocomplete="off">
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
                                <input name="swiftcode" id="swiftcode" type="text" class="form-control"  autocomplete="off" hidden>
                                <input name="bankcode" id="bankcode" type="text" class="form-control" autocomplete="off" hidden>
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
        </div>
    </div>
    
    

    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            $('#staffForm').on('submit', function (e) { 
    e.preventDefault();

    var form = this; // Reference the form element
    var formData = new FormData(form); // Use FormData to handle file uploads
    
    const submitBtn = $(form).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
   url: "{{ route('agents.store') }}",
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (response) {
        // Remove JSON.parse - dataType: 'json' already parses it
        if (response.status === 'success') {
            var empid = response.empid;
            var empidElements = document.querySelectorAll('.empid');
            empidElements.forEach(function (element) {
                element.value = empid;
            });
            showMessage('Agent Records Successfully Added. ID: ' + empid, false);
            form.reset();
           
        } else if (response.status === 'error') {
            showMessage(response.message, true);
        }
    },
    error: function (xhr) {
    if (xhr.status === 422) {
        let errors = xhr.responseJSON.errors;
        let firstError = Object.values(errors)[0][0];
        showMessage(firstError, true);
    } else {
        showMessage('An error occurred. Please try again.', true);
    }
},
    complete: function () {
        submitBtn.html(originalText).prop('disabled', false);
    }
});
});

$('#registrationForm').on('submit', function (e) {
    e.preventDefault();

    const form = this;

    // Stop if HTML5 validation fails
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const submitBtn = $('#load');
    const originalText = submitBtn.html();

    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...')
             .prop('disabled', true);

    $.ajax({
        url: "{{ route('registration.store') }}",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",

        success: function (response) {
            if (response.status === 'success') {
                showMessage('Registration saved successfully', false);
                form.reset();
            } else {
                showMessage(response.message ?? 'Save failed', true);
            }
        },

        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                const firstError = Object.values(errors)[0][0];
                showMessage(firstError, true);
            } else {
                showMessage('An unexpected error occurred', true);
            }
        },

        complete: function () {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});

           $.ajax({
        url: "{{ route('branches.getDropdown') }}",
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
    $('#brid').on('change', function() {
          const selectedCampusId = $(this).val();
          if (selectedCampusId) {
            loadClassesByCampus(selectedCampusId);
          } else {
            // Clear classes dropdown if no campus is selected
          const classDropdown = $('#dept');
          classDropdown.empty();
          classDropdown.append('<option value="">Select Department</option>');
        }
        
      });
      $('#bank').on('change', function() {
          const selectedCampusId = $(this).val();
          if (selectedCampusId) {
            loadBranches(selectedCampusId);
          } else {
            // Clear classes dropdown if no campus is selected
          const classDropdown = $('#branch');
          classDropdown.empty();
          classDropdown.append('<option value="">Select Branch</option>');
        }
        
      });
      $('#branch').on('change', function() {
          const branch = $(this).val();
          const bank = $('#bank').val();
          if (branch) {
            fetchcodes(bank,branch);
          } else {

         
        }
        
      });
        });
      function loadClassesByCampus(campusId) {
        $.ajax({
          url: "{{ route('classes.getByCampus') }}",
          type: "GET",
          data: { campusId: campusId },
          success: function (response) {
            const dropdown = $('#dept');
            dropdown.empty();
            dropdown.append('<option value="">Select Department</option>');
            response.data.forEach(function (classes) {
              dropdown.append(
                `<option value="${classes.ID}">${classes.DepartmentName}</option>`
              );
            });
          },
          error: function () {
            alert('Failed to load classes. Please try again.');
          }
        });
      }
      function loadBranches(campusId) {
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
      function fetchcodes(bank, branch) {
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

       document.addEventListener('DOMContentLoaded', function() {
    var unionCheckbox = $('#unionized');
var unionContainer = $('#union-container');
var unionnoInput = $('#unionno');

var disaCheckbox = $('#isdisabled');
var disaContainer = $('#disabled-container');
var disanInput = $('#disabinfo');

// Initialize containers as hidden with sliding capability
unionContainer.hide();
disaContainer.hide();

// Disability checkbox handler
if (disaCheckbox.length && disaContainer.length && disanInput.length) {
    disaCheckbox.on('change', function() {
        if (this.checked) {
            disaContainer.slideDown(300, function() {
                disanInput.attr('required', true);
                disanInput.val('');
                disanInput.focus();
            });
        } else {
            disanInput.removeAttr('required');
            disaContainer.slideUp(300, function() {
                disanInput.val('N/A');
            });
        }
    });
}


// Union checkbox handler
if (unionCheckbox.length && unionContainer.length && unionnoInput.length) {
    unionCheckbox.on('change', function() {
        if (this.checked) {
            unionContainer.slideDown(300, function() {
                unionnoInput.attr('required', true);
                unionnoInput.val('');
                unionnoInput.focus();
            });
        } else {
            unionnoInput.removeAttr('required');
            unionContainer.slideUp(300, function() {
                unionnoInput.val('N/A');
            });
        }
    });
}



    var nhshcheck = document.getElementById('nhif_shif');
    var nhifnoInput = document.getElementById('nhifno');
    var nhiffile = document.getElementById('nhifno_proof');
    if (nhshcheck && nhifnoInput && nhiffile) {
        // Disable the nhifno input by default on page load
        nhifnoInput.disabled = true;
        nhiffile.disabled = true;

        nhshcheck.addEventListener('change', function() {
            if (this.checked) {
                nhifnoInput.disabled = false;
                nhiffile.disabled = false;
                nhifnoInput.setAttribute('required', true);
            } else {
                nhifnoInput.disabled = true;
                nhiffile.disabled = true;
                nhifnoInput.removeAttribute('required');
                $('#nhifno').val('N/A');
                
            }
        });
    }
    
    var nssfcheck = document.getElementById('nssf');
    var nssfnoInput = document.getElementById('nssfno');
    var nssffile = document.getElementById('nssf_proof');
    if (nssfcheck && nssfnoInput && nssffile) {
        // Disable the nhifno input by default on page load
        nssfnoInput.disabled = true;
        nssffile.disabled = true;

        nssfcheck.addEventListener('change', function() {
            if (this.checked) {
                nssfnoInput.disabled = false;
                nssffile.disabled = false;
                nssfnoInput.setAttribute('required', true);
            } else {
                nssfnoInput.disabled = true;
                nssffile.disabled = true;
                nssfnoInput.removeAttribute('required');
                $('#nssfno').val('N/A');
                
            }
        });
    }

    var pencheck = document.getElementById('pensyes');
    var pensionInput = document.getElementById('pension');
    var pensionfile = document.getElementById('pension_proof');
    if (pencheck && pensionInput && pensionfile) {
        // Disable the nhifno input by default on page load
        pensionInput.disabled = true;
        pensionfile.disabled = true;

        pencheck.addEventListener('change', function() {
            if (this.checked) {
                pensionInput.disabled = false;
                pensionfile.disabled = false;
                pensionInput.setAttribute('required', true);
            } else {
                pensionInput.disabled = true;
                pensionfile.disabled = true;
                pensionInput.removeAttribute('required');
                $('#pension').val('N/A');
                
            }
        });
    }
   $('#tab-registration').on('click', function() {
    $.ajax({
        url: "{{ route('paytypes.getDropdown') }}",
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
            alert('Failed to load branches. Please try again.');
        },
    });
    $.ajax({
        url: "{{ route('banks.getDropdown') }}",
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
            alert('Failed to load branches. Please try again.');
        },
    }); 
});
const source = document.getElementById('agentno');
    const target = document.getElementById('aggentno');

    source.addEventListener('input', function () {
        target.value = this.value;
    });
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
    </script>
   
    
     
   
</x-custom-admin-layout>