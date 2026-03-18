<x-custom-admin-layout>

<style>


    .btn {
        height: 42px;
        padding: 0 22px;
        border: none;
        border-radius: var(--radius-sm);
        font-family: var(--font-body);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: transform .2s, box-shadow .2s, filter .2s;
        letter-spacing: .01em;
    }

    .btn .material-icons { font-size: 17px; }

    .btn:hover { transform: translateY(-2px); }
    .btn:active { transform: translateY(0); }

    .btn-save {
        background: linear-gradient(135deg, #1a56db, #4f46e5);
        color: #fff;
        box-shadow: 0 4px 14px rgba(26,86,219,.3);
    }

    .btn-save:hover { box-shadow: 0 7px 20px rgba(26,86,219,.4); filter: brightness(1.05); }

    .btn-reset {
        background: var(--surface);
        color: var(--muted);
        border: 1.5px solid var(--border);
    }

    .btn-reset:hover { color: var(--ink); border-color: #9ca3af; box-shadow: 0 4px 10px rgba(0,0,0,.06); }

    /* ── Tab panels ──────────────────────────────────────────── */
    .tab-panel { display: none; }
    .tab-panel.active { display: block; animation: fadeUp .35s cubic-bezier(.22,.61,.36,1) both; }


</style>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>

<div class="agent-page">

    <!-- Tab bar -->
    <div class="tab-bar">
        <button class="tab-btn active" data-tab="staffInfo">
            <span class="material-icons">person_outline</span>
            Agent Information
            <span class="tab-badge" id="badge-staffInfo">✓</span>
        </button>
        <button class="tab-btn" id="tab-registration" data-tab="registration">
            <span class="material-icons">assignment_ind</span>
            Registration
            <span class="tab-badge" id="badge-registration">✓</span>
        </button>
    </div>
<div class="toast-wrap" id="toastWrap"></div>
    <!-- Form card -->
    <div class="form-card">

        <!-- ══════════════════════════════════════
             TAB 1 — Staff Information
        ══════════════════════════════════════ -->
        <div class="tab-panel active" id="panel-staffInfo">

            <div class="section-head">
                <div class="section-icon"><span class="material-icons">badge</span></div>
                <div>
                    <h2>Agent Details</h2>
                    <p>Complete both tabs to register a new agent in the system.</p>
                </div>
            </div>

            <form method="post" name="staffForm" id="staffForm" enctype="multipart/form-data">
                @csrf

                <p class="subsection-label">Personal</p>

                <div class="row">
                    <div class="field col-3">
                        <label>First Name <span class="req">*</span></label>
                        <input name="firstname" type="text" placeholder="e.g. John" required autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>Last Name <span class="req">*</span></label>
                        <input name="lastname" type="text" placeholder="e.g. Doe" required autocomplete="off">
                    </div>
                    <div class="field col-2">
                        <label>Date of Birth</label>
                        <input name="dob" type="text" class="date-picker" placeholder="DD/MM/YYYY" autocomplete="off">
                    </div>
                    <div class="field col-2">
                        <label>Gender</label>
                        <div class="select-wrap">
                            <select name="gender" autocomplete="off">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                </div>

                <p class="subsection-label">Work & Contact</p>

                <div class="row">
                    <div class="field col-3">
                        <label>Agent Number <span class="req">*</span></label>
                        <input name="agentno" id="agentno" type="text" placeholder="e.g. AGT-001" required autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>Email Address</label>
                        <input name="email" type="email" placeholder="agent@company.com" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>Phone Number</label>
                        <input name="phonenumber" type="text" placeholder="+254 7xx xxx xxx" autocomplete="off">
                    </div>
                </div>

                <p class="subsection-label">Assignment</p>

                <div class="row">
                    <div class="field col-4">
                        <label>Branch <span class="req">*</span></label>
                        <div class="select-wrap">
                            <select name="brid" id="brid" required autocomplete="off">
                                <option value="">Select Branch</option>
                            </select>
                        </div>
                        <span class="field-error" id="brid-error"></span>
                    </div>
                    <div class="field col-4">
                        <label>Department</label>
                        <div class="select-wrap">
                            <select name="dept" id="dept" autocomplete="off">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="action-bar">
                    <button type="reset" class="btn btn-reset">
                        <span class="material-icons">restart_alt</span> Clear
                    </button>
                    <button id="add_staff" type="submit" class="btn btn-save">
                        <span class="material-icons">save</span> Save Agent
                    </button>
                </div>
            </form>
        </div>

        <!-- ══════════════════════════════════════
             TAB 2 — Registration
        ══════════════════════════════════════ -->
        <div class="tab-panel" id="panel-registration">

            <div class="section-head">
                <div class="section-icon"><span class="material-icons">assignment_ind</span></div>
                <div>
                    <h2>Registration Info</h2>
                    <p>Statutory, banking and payroll details</p>
                </div>
            </div>

            <form method="post" action="" id="registrationForm" enctype="multipart/form-data">
                @csrf
                <input name="aggentno" type="text" id="aggentno" value="" readonly hidden>

                <!-- Statutory -->
                <p class="subsection-label">Statutory & Flags</p>

                <div class="row">
                    <div class="field col-4">
                        <label>Statutory Deductions</label>
                        <div class="chip-group">
                            <div class="chip">
                                <input type="checkbox" id="nhif_shif" name="nhif_shif" value="YES">
                                <label for="nhif_shif">
                                    <span class="material-icons">health_and_safety</span> NHIF/SHIF
                                </label>
                            </div>
                            <div class="chip">
                                <input type="checkbox" id="nssf" name="nssf" value="YES">
                                <label for="nssf">
                                    <span class="material-icons">account_balance</span> NSSF
                                </label>
                            </div>
                            <div class="chip" hidden>
                                <input type="checkbox" id="pensyes" name="pensyes" value="YES">
                                <label for="pensyes">Pension</label>
                            </div>
                        </div>
                    </div>

                    <div class="field col-3">
                        <label>Union</label>
                        <div class="chip-group">
                            <div class="chip">
                                <input type="checkbox" id="unionized" name="unionized" value="YES">
                                <label for="unionized">
                                    <span class="material-icons">groups</span> Unionized
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="field col-3" id="union-container" style="display:none;">
                        <label>Union Number</label>
                        <input name="unionno" id="unionno" type="text" autocomplete="off" value="N/A">
                    </div>

                    <div class="field col-2">
                        <label>Is Agent</label>
                        <div class="chip-group">
                            <div class="chip">
                                <input type="checkbox" id="contractor" name="contractor" value="YES" checked>
                                <label for="contractor">
                                    <span class="material-icons">work_outline</span> Agent
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="form-divider">

                <!-- IDs -->
                <p class="subsection-label">Identification Numbers</p>

                <div class="row">
                    <div class="field col-3">
                        <label>ID No. <span class="req">*</span></label>
                        <input name="idno" id="idno" type="number" min="0" placeholder="National ID" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>SHIF No.</label>
                        <input name="nhifno" id="nhifno" type="text" placeholder="SHIF number" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>KRA PIN</label>
                        <input name="krapin" type="text" placeholder="AQ..." autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>
                            NSSF No.
                            <span style="margin-left:8px; font-weight:400; color:var(--muted);">
                                <label style="display:inline-flex;align-items:center;gap:5px;cursor:pointer;font-size:12px;">
                                    <input type="checkbox" id="nssfopt" name="nssfopt" value="YES" style="accent-color:var(--accent);width:14px;height:14px;">
                                    Opt out
                                </label>
                            </span>
                        </label>
                        <input name="nssfno" id="nssfno" type="text" placeholder="NSSF number" autocomplete="off">
                    </div>
                </div>

                <hr class="form-divider">

                <!-- Payment -->
                <p class="subsection-label">Payment & Payroll</p>

                <div class="row">
                    <div class="field col-3">
                        <label>Payroll Type <span class="req">*</span></label>
                        <div class="select-wrap">
                            <select name="proltype" id="proltype" required autocomplete="off">
                                <option value="">Select type</option>
                            </select>
                        </div>
                    </div>

                    <div class="field col-3">
                        <label>Payment Method</label>
                        <div class="chip-group">
                            <div class="chip">
                                <input type="radio" name="paymentMethod" id="etf" value="Etransfer" checked>
                                <label for="etf">
                                    <span class="material-icons">swap_horiz</span> E-Transfer
                                </label>
                            </div>
                            <div class="chip">
                                <input type="radio" name="paymentMethod" id="cheque" value="Cheque">
                                <label for="cheque">
                                    <span class="material-icons">receipt_long</span> Cheque
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="form-divider">

                <!-- Banking -->
                <p class="subsection-label">Banking Details</p>

                <div class="row">
                    <div class="field col-3">
                        <label>Bank</label>
                        <div class="select-wrap">
                            <select name="bank" id="bank" autocomplete="off">
                                <option value="">Select Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="field col-3">
                        <label>Bank Branch</label>
                        <div class="select-wrap">
                            <select name="branch" id="branch" autocomplete="off">
                                <option value="">Select Branch</option>
                            </select>
                        </div>
                    </div>
                    <div class="field col-2">
                        <label>Branch Code</label>
                        <input name="bcode" id="bcode" type="text" autocomplete="off" readonly>
                        <input name="bankcode" id="bankcode" type="text" hidden>
                    </div>
                    <div class="field col-2">
                        <label>Swift Code</label>
                        <input name="swiftcode" id="swiftcode" type="text" placeholder="XXXXKENA" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>Account Number <span class="req">*</span></label>
                        <input name="account" id="account" type="text" placeholder="Account number" required autocomplete="off">
                    </div>
                </div>

                <div class="action-bar">
                    <button type="reset" class="btn btn-reset">
                        <span class="material-icons">restart_alt</span> Clear
                    </button>
                    <button id="load" type="submit" class="btn btn-save">
                        <span class="material-icons">save</span> Save Registration
                    </button>
                </div>
            </form>
        </div>

    </div><!-- /form-card -->
</div><!-- /agent-page -->
    
    

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
            showToast('success','Success!', 'Agent Records Successfully Added. ID: ' + empid);
            form.reset();
           
        } else if (response.status === 'error') {
            showToast('danger', 'Error!', response.message);
        }
    },
    error: function (xhr) {
    if (xhr.status === 422) {
        let errors = xhr.responseJSON.errors;
        let firstError = Object.values(errors)[0][0];
        showToast('danger', 'Error!', firstError);
    } else {
        showToast('danger', 'Error!', 'An error occurred. Please try again.');
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
        url: "{{ route('2registration.store') }}",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",

        success: function (response) {
            if (response.status === 'success') {
                showToast('success', 'Success!','Registration saved successfully');
                form.reset();
            } else {
                showToast ('danger', 'Error!', response.message ?? 'Save failed');
            }
        },

        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                const firstError = Object.values(errors)[0][0];
                showToast('danger', 'Error!', firstError);
            } else {
                showToast('danger', 'Error!', 'An unexpected error occurred');
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
            showToast('danger', 'Error!', 'Failed to load branches. Please try again.');
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
            showToast('danger', 'Error!','Failed to load classes. Please try again.');
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
            showToast('danger', 'Error!','Failed to load classes. Please try again.');
          }
        });
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
            showToast('danger', 'Error!', 'Failed to load branches. Please try again.');
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
            showToast('danger', 'Error!',  'Failed to load branches. Please try again.');
        },
    }); 
});
const source = document.getElementById('agentno');
    const target = document.getElementById('aggentno');

    source.addEventListener('input', function () {
        target.value = this.value;
    });

    const tabBtns  = document.querySelectorAll('.tab-btn');
    const panels   = document.querySelectorAll('.tab-panel');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('panel-' + btn.dataset.tab).classList.add('active');
        });
    });

    /* ── Union number toggle ───────────────────── */
    document.getElementById('unionized').addEventListener('change', function () {
        const container = document.getElementById('union-container');
        container.style.display = this.checked ? 'block' : 'none';
    });

    /* ── Auto-advance to Registration tab after staff save ── */
    document.getElementById('staffForm').addEventListener('submit', function () {
        // Mark staff tab complete
        document.getElementById('badge-staffInfo').classList.add('show');
        // Switch to registration tab after short delay
        setTimeout(() => {
            document.querySelector('[data-tab="registration"]').click();
        }, 400);
    });
});
function showToast(type, title, message) {
        const wrap = document.getElementById('toastWrap');
        const t = document.createElement('div');
        const icon = type === 'success' ? 'check_circle' : 'error_outline';
        t.className = `toast-msg ${type}`;
        t.innerHTML = `<span class="material-icons">${icon}</span><div><strong>${title}</strong> ${message}</div>`;
        wrap.appendChild(t);

        const dismiss = () => {
            t.classList.add('leaving');
            setTimeout(() => t.remove(), 300);
        };

        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }
    </script>
   
    
     
   
</x-custom-admin-layout>