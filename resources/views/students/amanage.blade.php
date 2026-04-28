<x-custom-admin-layout>
    @vite(['resources/css/pages/amanage.css'])
<!-- Use the correct paired CSS + JS versions -->


    <div class="agents-page">

    <!-- Page header 
    <div class="page-header">
        <div class="page-heading">
            <h1>Agents</h1>
            <p>Manage all registered agents and their details.</p>
        </div>
        <a href="" class="btn btn-primary">
            <span class="material-icons">person_add</span> New Agent
        </a>
    </div>-->
    <div id="agentsdata"
     data-agentsdata-url="{{ route('agents.data') }}"></div>

     <div id="branchesdata"
     data-branchesdata-url="{{ route('branches.getDropdown') }}"></div>

     <div id="deptsdata"
     data-deptsdata-url="{{ route('depts.getDropdown') }}"></div>

     <div id="bybankdata"
     data-bybankdata-url="{{ route('branches.getByBank') }}"></div>

     <div id="codebybankdata"
     data-codebybankdata-url="{{ route('codes.getByBank') }}"></div>
      <div id="banksdata"
     data-banksdata-url="{{ route('banks.getDropdown') }}"></div>

     <div id="brbranchesdata"
     data-brbranchesdata-url="{{ route('brbranches.getDropdown') }}"></div>

     <div id="paytypesdata"
     data-paytypesdata-url="{{ route('paytypes.getDropdown') }}"></div>
    <!-- Toast -->
    <div class="toast-wrap" id="toastWrap"></div>

    <!-- Table card -->
    <div class="table-card">

        <!-- Toolbar -->
        <div class="table-toolbar">
            <div class="toolbar-left">
                <div class="toolbar-icon">
                    <span class="material-icons">group</span>
                </div>
                <div>
                    <div class="toolbar-title">All Agents</div>
                    <div class="toolbar-subtitle" id="recordCount">Loading…</div>
                </div>
            </div>

            <div class="toolbar-right">
                <!-- Custom search wired to DataTable -->
                <div class="search-box">
                    <span class="material-icons">search</span>
                    <input type="text" id="dt-search" placeholder="Search agents…">
                </div>

                <!-- Page-length selector -->
                <div class="select-wrap">
                    <select id="dt-length">
                        <option value="10">10 / page</option>
                        <option value="25" selected>25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-wrap">
            <table id="agents-table" class="stripe hover nowrap">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Work No</th>
                        <th>Staff Type</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>State</th>
                        <th class="datatable-nosort">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div><!-- /table-card -->
</div><!-- /agents-page -->

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editstaffModal"tabindex="-1" aria-labelledby="electiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="electiveModalLabel">
                         <span class="material-icons">person</span>
                        Edit Agent
                    </h5>
                    <button type="button" id="closemodal" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span class="material-icons">close</span>
                    </button>
                </div>
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
                <div class="modal-body">
                    <div class="tab-panel active" id="panel-staffInfo">

            <div class="section-head">
                <div class="section-icon"><span class="material-icons">badge</span></div>
                <div>
                    <h2>Update Agent Details</h2>
                    
                </div>
            </div>

            <form method="post" name="staffForm" id="staffForm" enctype="multipart/form-data" data-update-url="{{ url('agent') }}">
                @csrf

                <p class="subsection-label">Personal</p>

                <div class="row">
                    <div class="field col-3">
                        <label>First Name <span class="req">*</span></label>
                        <input name="firstname" id="firstname" type="text" placeholder="e.g. John" required autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>Last Name <span class="req">*</span></label>
                        <input name="lastname" id="lastname" type="text" placeholder="e.g. Doe" required autocomplete="off">
                    </div>
                    <div class="field col-2">
                        <label>Date of Birth</label>
                        <input name="dob" id="dob" type="text" class="date-picker" placeholder="DD/MM/YYYY" autocomplete="off">
                    </div>
                    <div class="field col-2">
                        <label>Gender</label>
                        <div class="select-wrap">
                            <select name="gender" id="gender" autocomplete="off">
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
                        <input name="email" id="email" type="email" placeholder="agent@company.com" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>Phone Number</label>
                        <input name="phonenumber" id="phonenumber"  type="text" placeholder="+254 7xx xxx xxx" autocomplete="off">
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
        <div class="tab-panel" id="panel-registration">

            <div class="section-head">
                <div class="section-icon"><span class="material-icons">assignment_ind</span></div>
                <div>
                    <h2>Registration Info</h2>
                    <p>Statutory, banking and payroll details</p>
                </div>
            </div>

            <form method="post" action="" id="registrationForm" enctype="multipart/form-data" data-regupdate-url="{{ url('regagent') }}">
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

                    <div class="field col-3" id="union-container">
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
                        <input name="idno" id="idno" type="number" placeholder="National ID" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>SHIF No.</label>
                        <input name="nhifno" id="nhifno" type="text" placeholder="SHIF number" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>KRA PIN <span class="req">*</span></label>
                        <input name="krapin" id="krapin" type="text" placeholder="AQ..." autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>
                            NSSF No.
                            <span class="nssfspan">
                                <label class="nssflabel">
                                    <input type="checkbox" id="nssfopt" name="nssfopt" value="YES">
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
                        <label>Bank <span class="req">*</span></label>
                        <div class="select-wrap">
                            <select name="bank" id="bank" autocomplete="off">
                                <option value="">Select Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="field col-3">
                        <label>Bank Branch <span class="req">*</span></label>
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
                        <label>Swift Code <span class="req">*</span></label>
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

    
<!-- Use ALL local assets -->



   
    
    @vite(['resources/js/app.js', 'resources/js/amanage.js'])
   
</x-custom-admin-layout>