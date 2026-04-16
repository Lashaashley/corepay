<x-custom-admin-layout>
 
@vite(['resources/css/pages/static.css']) 
 
<div class="static-page">
 
    <div class="page-heading">
        <h1>Static Information</h1>
        <p>Manage organisation details, branches, departments, banks and system settings.</p>
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
 
    {{-- Session flash --}}
    @if(session('success'))
        <div style="display:flex;align-items:center;gap:8px;padding:11px 15px;background:var(--success-lt);
                    border:1.5px solid #6ee7b7;border-radius:var(--radius-sm);margin-bottom:16px;
                    font-size:13.5px;color:#065f46;">
            <span class="material-icons" style="font-size:16px;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
 
    {{-- ── Unified tab navigation ── --}}
    <script nonce="{{ $cspNonce }}">
    function openTab(evt, tabId) {
        // Deactivate all tab buttons
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
 
        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
 
        // Activate clicked button
        if (evt && evt.currentTarget) {
            evt.currentTarget.classList.add('active');
        }
 
        // Show target panel
        const panel = document.getElementById(tabId);
        if (panel) panel.classList.add('active');
    }
    
    </script>
 
    {{-- Legacy alert (hidden, JS may use it) --}}
    <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display:none;">
        <strong id="alert-title"></strong> <span id="alert-message"></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
 
    {{-- ── Tab bar ──────────────────────────────────────────── --}}
    <div class="tab-bar">
        <button class="tab-btn active" id="tabactive">
            <span class="material-icons">business</span> Org Structure
        </button>
        <button class="tab-btn" id="tabbranches" >
            <span class="material-icons">location_city</span> Branches
        </button>
        <button class="tab-btn" id="tab-depts">
            <span class="material-icons">domain</span> Departments
        </button>
        <button class="tab-btn" id="tab-banks">
            <span class="material-icons">account_balance</span> Banks
        </button>
        <button class="tab-btn" id="tab-compbank">
            <span class="material-icons">account_balance_wallet</span> Company Bank
        </button>
        <button class="tab-btn" id="tab-econfig" >
            <span class="material-icons">email</span> Email Config
        </button>
        <button class="tab-btn" id="tabpaymodes">
            <span class="material-icons">payments</span> Payroll Types
        </button>
    </div>
 
    <div class="tab-body">
 
        {{-- ═══════════ ORG STRUCTURE ═══════════ --}}
        <div id="taborgstruct" class="tab-panel active">
            <div class="split-layout">
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon"><span class="material-icons">business</span></div>
                        <span class="s-card-title">Organisation Details</span>
                    </div>
                    <div class="s-card-body">
                        <form enctype="multipart/form-data" id="orgstrucf">
                            @csrf
                            <div class="field">
                                <label>Name</label>
                                <input name="sname" type="text" required autocomplete="off">
                            </div>
                            <div class="field">
                                <label>Slogan</label>
                                <input name="motto" type="text" required autocomplete="off">
                            </div>
                            <div class="field">
                                <label>Logo</label>
                                <div class="file-upload-wrap">
                                    <label class="file-upload-label" for="file">
                                        <span class="material-icons">upload</span> Choose
                                    </label>
                                    <input name="file" id="file" type="file" accept=".png,.jpg,.jpeg"
                                            onchange="validateFile('file'); this.nextElementSibling.textContent = this.files[0]?.name || 'No file'">
                                    <span class="file-name-display">No file chosen</span>
                                </div>
                                <span class="field-error" id="file-error"></span>
                            </div>
                            <div class="field">
                                <label>P.O Box</label>
                                <input name="pobox" type="text" required autocomplete="off">
                            </div>
                            <div class="field">
                                <label>Email</label>
                                <input name="email" type="email" required autocomplete="off">
                            </div>
                            <div class="field">
                                <label>Address</label>
                                <input name="Address" type="text" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-save">
                                <span class="material-icons">save</span> Save
                            </button>
                        </form>
                    </div>
                </div>
 
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon purple"><span class="material-icons">list</span></div>
                        <span class="s-card-title">Organisation Records</span>
                    </div>
                    <div class="s-card-body">
                        <div class="data-wrap">
                            <table class="s-table data-table table stripe hover nowrap">
                                <thead>
                                    <tr>
                                        <th hidden>ID</th>
                                        <th>Name</th>
                                        <th>Logo</th>
                                        <th>Slogan</th>
                                        <th hidden>P.O. Box</th>
                                        <th hidden>Email</th>
                                        <th hidden>Address</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody id="structure-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ═══════════ BRANCHES ═══════════ --}}
        <div id="tabstatcodes" class="tab-panel">
            <div class="split-layout">
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon green"><span class="material-icons">location_city</span></div>
                        <span class="s-card-title">New Branch</span>
                    </div>
                    <div class="s-card-body">
                        <form id="campusform">
                            @csrf
                            <div class="field">
                                <label>Branch Name</label>
                                <input name="branchname" id="branchname" type="text" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-save">
                                <span class="material-icons">save</span> Save
                            </button>
                        </form>
                    </div>
                </div>
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon purple"><span class="material-icons">list</span></div>
                        <span class="s-card-title">Branch List</span>
                    </div>
                    <div class="s-card-body">
                        <div class="data-wrap">
                            <table class="s-table data-table table stripe hover nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody id="campuses-table-body"></tbody>
                            </table>
                            <div id="pagination-controls" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ═══════════ DEPARTMENTS ═══════════ --}}
        <div id="tabdepts" class="tab-panel">
            <div class="split-layout">
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon orange"><span class="material-icons">domain</span></div>
                        <span class="s-card-title">New Department</span>
                    </div>
                    <div class="s-card-body">
                        <form id="deptsform">
                            @csrf
                            <div class="field">
                                <label>Branch</label>
                                <div class="select-wrap">
                                    <select name="brid" id="brid" required>
                                        <option value="">Select Branch</option>
                                    </select>
                                </div>
                                <span class="field-error" id="brid-error"></span>
                            </div>
                            <div class="field">
                                <label>Department Name</label>
                                <input name="deptname" id="deptname" type="text" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-save">
                                <span class="material-icons">save</span> Save
                            </button>
                        </form>
                    </div>
                </div>
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon purple"><span class="material-icons">list</span></div>
                        <span class="s-card-title">Departments</span>
                    </div>
                    <div class="s-card-body">
                        <div class="data-wrap">
                            <table class="s-table data-table table stripe hover nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Branch</th>
                                        <th>Department</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody id="depts-table-body"></tbody>
                            </table>
                            <div id="pagination-depts" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ═══════════ BANKS ═══════════ --}}
        <div id="tabstreams" class="tab-panel">
            <div class="split-layout">
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon teal"><span class="material-icons">account_balance</span></div>
                        <span class="s-card-title">New Bank</span>
                    </div>
                    <div class="s-card-body">
                        <form id="banksform">
                            @csrf
                            <div class="field">
                                <label>Bank Name</label>
                                <input name="Bank" id="Bank" type="text" required autocomplete="off">
                                <span class="field-error" id="Bank-error"></span>
                            </div>
                            <div class="field">
                                <label>Bank Code</label>
                                <input name="BankCode" id="BankCode" type="text" required autocomplete="off" >
                                <span class="field-error" id="BankCode-error"></span>
                            </div>
                            <div class="field">
                                <label>Branch Name</label>
                                <input name="Branch" id="Branch" type="text" required autocomplete="off">
                                <span class="field-error" id="Branch-error"></span>
                            </div>
                            <div class="field">
                                <label>Branch Code</label>
                                <input name="BranchCode" id="BranchCode" type="text" required autocomplete="off">
                                <span class="field-error" id="BranchCode-error"></span>
                            </div>
                            <div class="field">
                                <label>Swift Code</label>
                                <input name="swiftcode" id="swiftcode" type="text" autocomplete="off">
                                <span class="field-error" id="swiftcode-error"></span>
                            </div>
                            <button type="submit" class="btn btn-save">
                                <span class="material-icons">save</span> Save
                            </button>
                        </form>
                    </div>
                </div>
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon purple"><span class="material-icons">list</span></div>
                        <span class="s-card-title">Banks</span>
                    </div>
                    <div class="s-card-body">
                        <div class="data-wrap">
                            <table class="s-table data-table table stripe hover nowrap">
                                <thead>
                                    <tr>
                                        <th hidden>ID</th>
                                        <th>Bank</th>
                                        <th>Code</th>
                                        <th>Branch</th>
                                        <th>B.Code</th>
                                        <th>Swift</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody id="banks-table-body"></tbody>
                            </table>
                            <div id="pagination-banks" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ═══════════ COMPANY BANK ═══════════ --}}
        <div id="tabcompbank" class="tab-panel">
            <div class="split-layout">
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon teal"><span class="material-icons">account_balance_wallet</span></div>
                        <span class="s-card-title">Company Bank</span>
                    </div>
                    <div class="s-card-body">
                        <form id="compbanksform">
                            @csrf
                            <div class="field">
                                <label>Bank Name</label>
                                <input name="Bank" type="text" required autocomplete="off">
                            </div>
                            <div class="field">
                                <label>Bank Code</label>
                                <input class="bankcode" name="BankCode" type="text" required autocomplete="off" >
                            </div>
                            <div class="field">
                                <label>Branch Name</label>
                                <input name="Branch" type="text" required autocomplete="off">
                            </div>
                            <div class="field">
                                <label>Branch Code</label>
                                <input name="BranchCode" type="text" required autocomplete="off">
                            </div>
                            <div class="field">
                                <label>Swift Code</label>
                                <input name="swiftcode" type="text" autocomplete="off">
                            </div>
                            <div class="field">
                                <label>Account Number</label>
                                <input name="accno" id="accno" type="text" autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-save">
                                <span class="material-icons">save</span> Save
                            </button>
                        </form>
                    </div>
                </div>
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon purple"><span class="material-icons">list</span></div>
                        <span class="s-card-title">Company Bank Records</span>
                    </div>
                    <div class="s-card-body">
                        <div class="data-wrap">
                            <table class="s-table data-table table stripe hover nowrap">
                                <thead>
                                    <tr>
                                        <th hidden>ID</th>
                                        <th>Bank</th>
                                        <th>Code</th>
                                        <th>Branch</th>
                                        <th>B.Code</th>
                                        <th>Swift</th>
                                        <th>ACC NO.</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody id="compb-table-body"></tbody>
                            </table>
                            <div id="pagination-compb" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ═══════════ EMAIL CONFIG ═══════════ --}}
        <div id="tabfcategories" class="tab-panel full-panel">
            <div class="s-card">
                <div class="s-card-head">
                    <div class="s-icon"><span class="material-icons">email</span></div>
                    <span class="s-card-title">Email Configuration</span>
                </div>
                <div class="s-card-body">
                    <div class="data-wrap">
                        <table class="s-table data-table table stripe hover nowrap">
                            <thead>
                                <tr>
                                    <th hidden>ID</th>
                                    <th>Name</th>
                                    <th>Host</th>
                                    <th>Port</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Encryption</th>
                                    <th>Email</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody id="econfig-table-body"></tbody>
                        </table>
                        <div id="pagination-econfig" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ═══════════ PAYROLL TYPES ═══════════ --}}
        <div id="tabfpaymodes" class="tab-panel">
            <div class="split-layout">
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon green"><span class="material-icons">payments</span></div>
                        <span class="s-card-title">New Payroll Type</span>
                    </div>
                    <div class="s-card-body">
                        <form id="pmodesform">
                            @csrf
                            <div class="field">
                                <label>Name</label>
                                <input name="pname" id="pname" type="text" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-save">
                                <span class="material-icons">save</span> Save
                            </button>
                        </form>
                    </div>
                </div>
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="s-icon purple"><span class="material-icons">list</span></div>
                        <span class="s-card-title">Payroll Types</span>
                    </div>
                    <div class="s-card-body">
                        <div class="data-wrap">
                            <table class="s-table data-table table stripe hover nowrap">
                                <thead>
                                    <tr>
                                        <th hidden>ID</th>
                                        <th>Payroll</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody id="pmodes-table-body"></tbody>
                            </table>
                            <div id="pagination-pmodes" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
    </div>{{-- /tab-body --}}
</div>{{-- /static-page --}}
 
 
{{-- ═══════════ EDIT MODALS — all IDs and form names preserved ═══════════ --}}
 
{{-- Edit Org --}}
<div class="modal fade" id="editSchoolModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Organisation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editSchoolForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <input type="text" class="form-control" id="schoolName" name="name">
                            <span class="text-danger" id="name-error"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Slogan</label>
                            <input type="text" class="form-control" id="schoolMotto" name="motto">
                            <span class="text-danger" id="motto-error"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>P.O Box</label>
                        <input type="text" class="form-control" id="schoolPobox" name="pobox">
                        <span class="text-danger" id="pobox-error"></span>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" class="form-control" id="schoolEmail" name="email">
                            <span class="text-danger" id="email-error"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Address</label>
                            <input type="text" class="form-control" id="schoolPhysaddres" name="physaddres">
                            <span class="text-danger" id="physaddres-error"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Logo</label>
                        <input type="file" class="form-control" id="schoolLogo" name="logo">
                        <img id="schoolLogoPreview" src="" alt="Logo" >
                        <span class="text-danger" id="logo-error"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary btn" data-dismiss="modal">Cancel</button>
                <button type="submit" form="editSchoolForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
 
{{-- Edit Stream --}}
<div class="modal fade" id="editstreamModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Stream</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editstreamForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" id="editstrmname" name="strmname">
                        <span class="text-danger" id="strmname-error"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="editstreamForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
 
{{-- Edit Branch --}}
<div class="modal fade" id="editcampusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Branch</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editcampuslForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    <div class="form-group">
                        <label>Branch Name</label>
                        <input type="text" class="form-control" id="editbranchname" name="branchname">
                        <span class="text-danger" id="branchname-error"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="editcampuslForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
 
{{-- Edit Department --}}
<div class="modal fade" id="edithouseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="edithouseForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    <div class="form-group">
                        <label>Branch</label>
                        <select name="brid" id="branch3" class="form-control custom-select" required>
                            <option value="">Select Branch</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" class="form-control" id="edithousename" name="DepartmentName">
                        <span class="text-danger" id="DepartmentName-error"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="edithouseForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
 
{{-- Edit Payroll Type --}}
<div class="modal fade" id="editpmodeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Payroll Type</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editpmodesForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    <div class="form-group">
                        <label>Name</label>
                        <input name="pname" id="epmoden" class="form-control" required>
                        <span class="text-danger" id="pname-error"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="editpmodesForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
 
{{-- Edit Bank --}}
<div class="modal fade" id="editBankModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Bank</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form name="editBankForm" id="editBankForm" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="ID" id="ID">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" id="bankName" name="bankName" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Bank Code</label>
                            <input type="text" class="form-control" id="bankCode" name="bankCode" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Branch Name</label>
                            <input type="text" class="form-control" id="branchName" name="branchName" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Branch Code</label>
                            <input type="text" class="form-control" id="branchCode" name="branchCode" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Swift Code</label>
                        <input type="text" class="form-control" id="swiftcode" name="swiftcode" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
 
{{-- Edit Company Bank --}}
<div class="modal fade" id="editcompBankModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Company Bank</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form name="editcompBankForm" id="editcompBankForm" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="ID">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" id="bankName" name="Bank" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Bank Code</label>
                            <input type="text" class="form-control" id="bankCode" name="BankCode" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Branch Name</label>
                            <input type="text" class="form-control" id="branchName" name="Branch" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Branch Code</label>
                            <input type="text" class="form-control" id="branchCode" name="BranchCode" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Swift Code</label>
                            <input type="text" class="form-control" id="swiftcode" name="swiftcode">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Account Number</label>
                            <input type="text" class="form-control" id="accno1" name="accno">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
 
{{-- Edit Email Config --}}
<div class="modal fade" id="editemailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Configuration</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form name="editmailForm" id="editmailForm" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="ID">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <input type="text" class="form-control" id="eeName" name="name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Host</label>
                            <input type="text" class="form-control" id="ehost" name="host" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Port</label>
                            <input type="text" class="form-control" id="eport" name="port" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Username</label>
                            <input type="text" class="form-control" id="eusername" name="username" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Password</label>
                            <input type="text" class="form-control" id="epassword" name="password" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Encryption</label>
                            <input type="text" class="form-control" id="eencryption" name="encryption" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="text" class="form-control" id="eemailaddress" name="from_email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
 
{{-- Edit Class (kept for JS compatibility, hidden from UI) --}}
<div class="modal fade" id="editclassModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Class</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editclaForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Stream</label>
                            <select name="stid" id="streamd2" class="form-control custom-select" required></select>
                            <span class="text-danger" id="stid-error"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Class</label>
                            <input type="text" class="form-control" id="editcla" name="claname">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Rank</label>
                            <input type="text" class="form-control" id="editrank" name="clarank">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Class Teacher</label>
                            <input type="text" class="form-control" id="editclateach" name="clateach">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="editclaForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>

    <script nonce="{{ $cspNonce }}">
        // resources/js/tabs.js  (or add to app.js)
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

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('staticinfo.store') }}",
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
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({
                    url: `{{ url('static') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editSchoolModal').modal('hide');
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
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: `{{ url('econfig') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editmailForm').modal('hide');
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
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('branches.store') }}",
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
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('paytypes.store') }}",
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
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);

    $.ajax({
        url: `{{ url('pmodes') }}/${id}`, // Adjusted correctly
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
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: `{{ url('depts') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#edithouseForm').modal('hide');
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
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('depts.store') }}",
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
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('banks.store') }}",
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
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('compb.store') }}",
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
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: `{{ url('branches') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editcampusModal').modal('hide');
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
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: `{{ url('banks') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editBankForm').modal('hide');
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
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: `{{ url('compb') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editcompBankForm').modal('hide');
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
        url: "{{ route('branches.getDropdown') }}",
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
        url: "{{ route('branches.getall') }}?page=" + page,
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
        url: "{{ route('staticinfo.getall') }}",
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
                url: `{{ url('branches') }}/${ID}`,
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
        url: "{{ route('depts.getall') }}?page=" + page,
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
        url: "{{ route('banks.getall') }}?page=" + page,
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
        url: "{{ route('econfig.getall') }}?page=" + page,
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
        url: "{{ route('compb.getall') }}?page=" + page,
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
        url: "{{ route('paytypes.getall') }}?page=" + page,
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
    </script>
</x-custom-admin-layout>
