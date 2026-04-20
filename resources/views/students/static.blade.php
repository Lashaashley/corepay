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
        <div class="divfex" >
            <span class="material-icons font16" >check_circle</span>
            {{ session('success') }}
        </div>
    @endif
 
    {{-- ── Unified tab navigation ── --}}
    
 
    {{-- Legacy alert (hidden, JS may use it) --}}
    <div id="status-message" class="alert alert-dismissible fade custom-alert hidden" role="alert" >
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
                        <form enctype="multipart/form-data" id="orgstrucf" method="post" data-storestaticinfo-url="{{ route('staticinfo.store') }}">
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
                                           >
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
                        <form id="campusform" method="post"  data-storebranches-url="{{ route('branches.store') }}">
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
                        <form id="deptsform" method="post" data-storedepts-url="{{ route('depts.store') }}">
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
                        <form id="banksform" method="post" data-storebanks-url="{{ route('banks.store') }}">
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
                        <form id="compbanksform" method="post"  data-storecompb-url="{{ route('compb.store') }}">
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
                        <form id="pmodesform" method="post" data-storepaytypes-url="{{ route('paytypes.store') }}">
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

@vite([
    'resources/js/static.js'
])
</x-custom-admin-layout>
