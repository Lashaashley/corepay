<x-custom-admin-layout>
@vite(['resources/css/pages/nagent.css']) 


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

            <form method="post" name="staffForm" id="staffForm" enctype="multipart/form-data" data-storeagent-url="{{ route('agents.store') }}">
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

            <form method="post" action="" id="registrationForm" enctype="multipart/form-data" data-regagent-url="{{ route('2registration.store') }}">
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

                    <div class="field col-3 hidden" id="union-container">
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
                        <label>KRA PIN <span class="req">*</span></label>
                        <input name="krapin" type="text" placeholder="AQ..." autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>
                            NSSF No.
                            <span class="nssfspan">
                                <label class="nssflabel" >
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

    </div><!-- /form-card -->
</div><!-- /agent-page -->
    
    

    
    
    
    
   
    @vite(['resources/js/nagent.js'])
     
   
</x-custom-admin-layout>