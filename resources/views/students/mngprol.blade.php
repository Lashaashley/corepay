<x-custom-admin-layout>

    @vite(['resources/css/pages/mngprol.css']) 
    


    <div class="mobile-menu-overlay"></div>
    <h1 class="header-container"></h1>
    <div>
        <div class="pd-ltr-20 xs-pd-20-10">
            <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" >
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <div class="toast-wrap" id="toastWrap"></div>
    </div>
    <div class="card-box p-3 mb-3" >
        <div class="row align-items-center mb-2" hidden>
            <div class="col-md-4 pr-md-2">
                <div class="border p-2">
                    <legend class="small mb-1">Current Payroll Period</legend>
                    <div class="row no-gutters">
                        <div class="col-md-6 pr-md-1">
                            <div class="form-group mb-1">
                                <label for="currentMonth" class="small-label mb-0">Current Month</label>
                                <input type="text" class="form-control form-control-sm" id="currentMonth" value="{{ $month }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 pl-md-1">
                            <div class="form-group mb-1">
                                <label for="currentYear" class="small-label mb-0">Current Year</label>
                                <input type="text" class="form-control form-control-sm" id="currentYear" value="{{ $year }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        {{-- Active Period Display --}}
                <div class="alert alert-info" id="period-info">
                    <i class="fa fa-calendar"></i> Loading period...
                </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <button type="button" class="btn btn-sm btn-primary mr-1" id="empmodal" data-toggle="modal" data-target="#exampleModal">Post by Parameter</button>
                
                <!------<button type="button" class="btn btn-sm btn-secondary mr-1">Edit Mode</button>---->
                <button type="button" class="btn btn-sm btn-info" disabled>View loan schedule</button>
            </div>
        </div>
        
    </div>
    <div class="card-box mb-30">
        <div class="pd-20">
    
                
                

                {{-- Search Bar --}}
                <div class="row mb-0">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" 
                                   id="searchInput" 
                                   class="form-control" 
                                   placeholder="Search by name or work number...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" id="searchBtn">
                                    <i class="fa fa-search"></i> Search
                                </button>
                                <button class="btn btn-secondary" id="clearBtn">
                                    <i class="fa fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <span id="total-records" class="badge badge-info"></span>
                    </div>
                </div>

                {{-- Table Container --}}
                <div class="table-responsive">
    <table class="table table-striped table-hover deductions-table-compact" id="deductionsTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Work Number</th>
                <th>Department</th>
                <th>Parameter Code</th>
                <th>Parameter Name</th>
                <th>Parameter Category</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <tr>
                <td colspan="7" class="text-center">
                    <i class="fa fa-spinner fa-spin"></i> Loading data...
                </td>
            </tr>
        </tbody>
    </table>
</div>

                {{-- Pagination --}}
                <div class="row mt-0">
                    <div class="col-md-6">
                        <div id="showing-info"></div>
                    </div>
                    <div class="col-md-6">
                        <ul class="pagination justify-content-end" id="pagination"></ul>
                    </div>
                </div>
                
            </div>
            <div class="mt-0">
    <div class="d-flex">

        <div class="custom-control custom-checkbox mr-3">
            <input class="custom-control-input toggle-checkbox" 
                   type="checkbox" id="nhif" data-model="nhif"
                   {{ $nhif == 'ACTIVE' ? 'checked' : '' }}>
            <label class="custom-control-label" for="nhif">NHIF</label>
        </div>

        <div class="custom-control custom-checkbox mr-3">
            <input class="custom-control-input toggle-checkbox"
                   type="checkbox" id="nssf" data-model="nssf"
                   {{ $nssf == 'ACTIVE' ? 'checked' : '' }}>
            <label class="custom-control-label" for="nssf">NSSF</label>
        </div>

        <div class="custom-control custom-checkbox mr-3">
            <input class="custom-control-input toggle-checkbox"
                   type="checkbox" id="shif" data-model="shif"
                   {{ $shif == 'ACTIVE' ? 'checked' : '' }}>
            <label class="custom-control-label" for="shif">SHIF</label>
        </div>

        <div class="custom-control custom-checkbox mr-3">
            <input class="custom-control-input toggle-checkbox"
                   type="checkbox" id="pension" data-model="pension"
                   {{ $pension == 'ACTIVE' ? 'checked' : '' }}>
            <label class="custom-control-label" for="pension">Pension</label>
        </div>

        <div class="custom-control custom-checkbox mr-3">
            <input class="custom-control-input toggle-checkbox"
                   type="checkbox" id="hlevy" data-model="hlevy"
                   {{ $hlevy == 'ACTIVE' ? 'checked' : '' }}>
            <label class="custom-control-label" for="hlevy">Housing Levy</label>
        </div>

    </div>
</div>
<div class="mt-3">
        
        <button 
    id="preview-totals-btn" 
    class="btn btn-enhanced btn-final {{ !$isApproved ? 'disabled' : '' }}"
    {{ !$isApproved ? 'disabled' : '' }}
    data-bs-toggle="tooltip" 
    data-placement="top" 
    data-html="true"
    title="{{ !$isApproved ? '<strong>Action Required:</strong><br>Payments are pending approval.' : 'Click to auto-calculate payroll totals' }}"
>
    <span class="material-icons">bolt</span> Auto Calculate
</button>


@if(!$isApproved)
    <div class="alert alert-warning mt-2" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Pending Approval:</strong> 
        The payments for {{ $month }} {{ $year }} are currently <span class="badge badge-warning">{{ $approvalStatus }}</span>. 
        Auto-calculation will be available after approval.
    </div>
@endif

 <button class="btn btn-enhanced btn-draft" id="NofityApprover">
   
     <span class="material-icons">send</span> Notify Approver
</button>
    </div>
    </div>
    
   <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
 
            <div class="modal-header">
                <div class="pp-modal-icon"><span class="material-icons">post_add</span></div>
                <h5 class="modal-title" id="exampleModalLabel">Post by Parameter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="material-icons closeicon">close</span>
                </button>
            </div>
 
            <div class="modal-body">
                <form id="payrollForm" class="compact-form">
 
                    {{-- ── Row 1: Period + Payroll Item ──────────── --}}
                    <div class="rowone">
 
                        {{-- Period --}}
                        <div class="pp-panel">
                            <div class="pp-panel-head">
                                <span class="material-icons">calendar_month</span> Payroll Period
                            </div>
                            <div class="pp-panel-body">
                                <div class="pp-grid">
                                    <div class="pp-field ppc-6">
                                        <label>Month</label>
                                        <input type="text" id="month" value="{{ $month }}" readonly>
                                    </div>
                                    <div class="pp-field ppc-6">
                                        <label>Year</label>
                                        <input type="text" id="year" value="{{ $year }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
 
                        {{-- Payroll Item --}}
                        <div class="pp-panel">
                            <div class="pp-panel-head">
                                <span class="material-icons">receipt_long</span> Payroll Item
                            </div>
                            <div class="pp-panel-body">
                                <div class="pp-field">
                                    <label>Select Item <span class="spansel">*</span></label>
                                    {{-- Select2 is initialised by JS on modal open --}}
                                    <select name="pitem" id="pitem" required autocomplete="off">
                                        <option value="">Select Item</option>
                                    </select>
                                    <input name="category"  id="category"  type="text" hidden>
                                    <input name="increREDU" id="increREDU" type="text" hidden>
                                    <input name="codebal"   id="codebal"   type="text" hidden>
                                </div>
                            </div>
                        </div>
 
                    </div>
 
                    {{-- ── Staff search ────────────────────────── --}}
                    <div class="pp-panel">
                        <div class="pp-panel-head">
                            <span class="material-icons">manage_search</span> Staff Search
                        </div>
                        <div class="pp-panel-body">
 
                            <div class="pp-grid marginbot">
                                <div class="pp-field ppc-8">
                                    <label>Select Staff <span class="spansel">*</span></label>
                                    {{-- Choices.js is initialised by JS on modal open --}}
                                    <select name="searchValue" id="searchValue" required>
                                        <option value="">Search staff…</option>
                                    </select>
                                </div>
                                {{-- Hidden category select (used by JS) --}}
                                <div hidden>
                                    <select id="searchCategory">
                                        <option value="WorkNumber">Work number</option>
                                        <option value="Surname">Name</option>
                                    </select>
                                </div>
                            </div>
 
                            <div class="pp-grid">
                                <div class="pp-field ppc-3">
                                    <label>Surname</label>
                                    <input type="text" id="surname" readonly>
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Other Name</label>
                                    <input type="text" id="othername" readonly>
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Work Number</label>
                                    <input type="text" id="workNumber" readonly>
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Department</label>
                                    <input type="text" id="department" hidden>
                                    <input type="text" id="departmentname" readonly>
                                </div>
                            </div>
 
                        </div>
                    </div>
 
                    {{-- ── Amount / Balance / Post ─────────────── --}}
                    <div class="pp-post-row">
                        <div class="pp-field">
                            <label>Amount</label>
                            <input type="text" id="amount" placeholder="Enter amount">
                        </div>
                        <div class="pp-field">
                            <label>Balance</label>
                            <input type="text" id="balance" placeholder="Enter balance">
                        </div>
                        <button type="button" id="submitBtn">
                            <span class="material-icons">send</span> Post
                        </button>
                    </div>
 
                    {{-- ── Loan (hiddenContainer) ───────────────── --}}
                    <div class="pp-panel hiddencont" id="hiddenContainer" >
                        <div class="pp-panel-head">
                            <span class="material-icons">account_balance_wallet</span> Loan Details
                        </div>
                        <div class="pp-panel-body">
                            <div class="pp-grid itemscent" >
                                <div class="pp-field ppc-3">
                                    <label>Months</label>
                                    <input type="text" id="months">
                                </div>
                                <div class="pp-field ppc-4">
                                    <label>End Date</label>
                                    <input type="text" id="enddate" readonly>
                                </div>
                                <div class="ppc-5 dispfleal">
                                    <div class="pp-toggle-wrap">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="activeinaclonToggle" checked>
                                            <span class="slider round"></span>
                                        </label>
                                        <span class="pp-toggle-label" id="toggleLabel3">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    {{-- ── Balance (hiddenContainer2) ───────────── --}}
                    <div class="pp-panel hiddencont" id="hiddenContainer2" >
                        <div class="pp-panel-head">
                            <span class="material-icons">balance</span> Balance Details
                        </div>
                        <div class="pp-panel-body">
                            <div class="pp-grid itemscent" >
                                <div class="ppc-3 dispflex">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="fixedOpenToggle" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="pp-toggle-label" id="toggleLabel">Open</span>
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Current Balance</label>
                                    <input type="number" id="cbalance" name="duration" placeholder="—" readonly>
                                </div>
                                <div id="Fixed" class="ppc-6 hidden">
                                    <div class="pp-grid">
                                        <div class="ppc-6">
                                            <div class="pp-input-group">
                                                <span class="pp-ig-label">Duration</span>
                                                <input type="text" id="duration" name="duration" placeholder="months">
                                            </div>
                                        </div>
                                        <div class="ppc-6">
                                            <div class="pp-input-group">
                                                <span class="pp-ig-label">Ends In</span>
                                                <input type="text" id="balend" name="balend" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="Open" class="ppc-3 dispgap">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="activeinacToggle" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="pp-toggle-label" id="toggleLabel2">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    {{-- ── Pension (pensionContainer) ───────────── --}}
                    <div class="pp-panel hiddencont" id="pensionContainer" >
                        <div class="pp-panel-head">
                            <span class="material-icons">savings</span> Pension
                        </div>
                        <div class="pp-panel-body">
                            <div class="pp-grid">
                                <div class="pp-field ppc-3">
                                    <label>Employee %</label>
                                    <input type="text" id="epmpenperce">
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Employer %</label>
                                    <input type="text" id="emplopenperce">
                                </div>
                                <div class="pp-field ppc-4">
                                    <label>Pensionable</label>
                                    <input type="text" id="pensionable" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    {{-- ── OT / Formula (otContainer) ──────────── --}}
                    <div class="pp-panel hiddencont" id="otContainer" >
                        <div class="pp-panel-head">
                            <span class="material-icons">functions</span> Calculation / OT
                        </div>
                        <div class="pp-panel-body">
                            <div class="pp-grid flexend">
                                <div class="pp-field ppc-3">
                                    <label>Formula</label>
                                    <input name="formular" id="formular" type="text" autocomplete="off" readonly>
                                </div>
                                <div class="pp-field ppc-3">
                                    <label>Date</label>
                                    <input type="date" id="otdate">
                                </div>
                                <div class="pp-field ppc-2">
                                    <label>Quantity</label>
                                    <input type="text" id="quantity">
                                </div>
                                <div class="ppc-2 paddingbot" >
                                    <button type="button" id="btnopenot">
                                        <span class="material-icons">open_in_new</span> Open
                                    </button>
                                </div>
                                <input type="text" id="camountf" hidden>
                            </div>
                        </div>
                    </div>
 
                    {{-- ── Posted items table ───────────────────── --}}
                    <div class="pp-panel">
                        <div class="pp-panel-head">
                            <span class="material-icons">table_view</span> Posted Items
                        </div>
                        <div class="dpadding">
                            <div class="pp-table-wrap">
                                <table id="contentTable2" class="content-table2">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Work Number</th>
                                            <th>Department</th>
                                            <th>Parameter Code</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="pp-totals-row">
                                <label for="totalsvar">Total:</label>
                                <input type="text" id="totalsvar" readonly>
                            </div>
                        </div>
                    </div>
 
                </form>
            </div>{{-- /modal-body --}}
        </div>{{-- /modal-content --}}
    </div>{{-- /modal-dialog --}}
</div>{{-- /modal --}}
    


    
<div class="hidden" id="successMessage"></div>
<div id="progress-modal">
  <div class="modal-overlay">
    <div class="modal-content">
      <h4>Processing</h4>
      <div id="progress-bar-container">
        <div id="progress-bar"></div>
      </div>
      <p id="progress-message">Processing totals...</p>
    </div>
  </div>
</div>

<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Payslip Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
    </div>
  </div>
</div>

    
    <!-- Proper order of script loading -->
    <!-- 1. First jQuery -->

   
    
    <!-- 2. Then DataTables core and styles -->
    
    
    <!-- 3. SweetAlert Scripts -->

    
  

     @vite(['resources/js/mngprol.js'])

    
    <!-- 4. Your custom scripts -->
  
</x-custom-admin-layout>