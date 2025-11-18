<x-custom-admin-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <style>
        .header-container{
    font-size: 1.5rem;
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center;

}
.border {
    border: 4px solid #ced4da;
            border-radius: 0.25rem;
            padding: 10px;
            position: relative;
            margin-top: 20px;
            margin-bottom: 15px;
        }
        .border legend {
            font-size: 1rem;
            font-weight: 400;
            width: auto;
            padding: 0 5px;
            margin-bottom: 0;
            position: absolute;
            top: -0.8rem;
            left: 1rem;
            background: white;
        }
        .card-box {
  font-size: 0.875rem;
}

.small-label {
  font-size: 0.75rem;
}
.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

legend.small {
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.no-gutters {
  margin-right: 0;
  margin-left: 0;
}

.no-gutters > .col,
.no-gutters > [class*="col-"] {
  padding-right: 0;
  padding-left: 0;
}

.content-table2 {
    border-collapse: collapse;
    margin: 5px auto;
    font-size: 0.8rem; /* Smaller font */
    min-width: 400px;
    border-radius: 2px 2px 0 0;
    overflow: hidden;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.08);
    cursor: pointer;
    width: 100%;
}

.content-table2 thead tr {
    background-color: deepskyblue;
    text-align: left;
    font-weight: bold;
    font-size: 0.75rem; /* Even smaller header font */
}

.content-table2 th,
.content-table2 td {
    padding: 1px 3px; /* Minimal padding */
    line-height: 1.1; /* Very tight line spacing */
    height: 20px; /* Fixed row height */
}

.content-table2 tbody tr {
    border-bottom: 0.5px solid #dddddd; /* Thinner border */
    font-size: 0.75rem; /* Smaller body font */
    height: 20px; /* Consistent row height */
}

.content-table2 tbody tr:nth-of-type(even) {
    background-color:rgb(207, 206, 206);
}

.content-table2 tbody tr:last-of-type {
    border-bottom: 0.5px solid deepskyblue;
}

.content-table2 tbody tr.highlight {
    background-color: #d4edda; /* Subtle green highlight */
}
.pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 20px 0 0;
}

.pagination .page-item {
    margin: 0 5px;
}

.pagination .page-item a {
    color: deepskyblue;
    text-decoration: none;
    padding: 8px 12px;
    border: 1px solid deepskyblue;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

.pagination .page-item.active a {
    background-color: deepskyblue;
    color: white;
}

.pagination .page-item a:hover {
    background-color: #007bff;
    color: white;
}

.button-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px; /* Adjust as needed */
}

.button-container .col-sm-6 {
    width: 48%; /* Adjust as needed */
}
fieldset {
    background-color: white;
    border-radius: 3px;
    margin-bottom: 0.5rem;
}
.modal-body {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}
legend {
  background-color: none;
    border: none;
    padding: 1px 3px;
    font-size: 12px;
    margin-bottom: 0;
}

.form-control {
    height: 30px;
    padding: 1px 2px;
}

.btn {
    padding: 3px 10px;
}

.modal-content {
    background-color: white;
}
.form-control-sm {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.1rem 0.3rem;
    font-size: 11x;
}
.compact-form .form-group {
            margin-bottom: 0.1rem;
        }
        .compact-form .col-form-label-sm {
            padding-top: 0;
            padding-bottom: 0;
            font-size: 11x;
        }
        .compact-form .form-control-sm {
            padding-top: 0.1rem;
            padding-bottom: 0.1rem;
        }
        .compact-form fieldset {
            margin-bottom: 0.1rem;
        }
        .compact-form legend {
            font-size: 0.85rem;
        }
        .compact-form .btn-sm {
            padding: 0.25rem 0.5rem;
        }
        .modal-content {
            font-size: 0.85rem;
        }
        .small-font {
  font-size: 0.75rem;
}
.is-invalid {
    border: 1px solid red;
}
#employeeListModal .modal-dialog {
    max-width: 400px;
    width: 90%;
}
#employeeListModal .modal-content {
    background-color: #f8f9fa; /* Light gray background */
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
#employeeListModal .modal-header {
    background-color: #007bff; /* Blue header */
    color: white;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

#employeeListModal .modal-title {
    font-weight: bold;
}


#employeeListModal .form-group label {
    font-weight: bold;
    color: #333;
}

#employeeListModal .custom-select {
    border: 1px solid #ced4da;
    border-radius: 5px;
}

#employeeListModal .content-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

#employeeListModal .content-table th,
#employeeListModal .content-table td {
    border: 1px solid #dee2e6;
    padding: 10px;
    text-align: left;
    height: 20px; /* Set a fixed height for the rows */
    line-height: 10px; /* Adjust line height for vertical centering */
}

#employeeListModal .content-table thead {
    background-color: #e9ecef;
}

/* Ensure the content doesn't overflow */
#employeeListModal .content-table td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.toggle-switch {
        position: relative;
        display: inline-flex;
        width: 30px;
        height: 17px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #2196F3;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 13px;
        width: 13px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #0cf24d;
    }

    input:checked + .slider:before {
        transform: translateX(13px);
    }

    .slider.round {
        border-radius: 17px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
   /* Modal Overlay */
/* Specific styles for progress modal */
#progress-modal {
  display: none; /* Hide by default */
}

#progress-modal .modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000; /* Ensure the modal is on top of other content */
}

#progress-modal .modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  text-align: center;
  width: 300px; /* Adjust width as needed */
}

#progress-modal #progress-bar-container {
  width: 100%;
  background-color: #f3f3f3;
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
  height: 20px;
  margin-top: 10px;
}

#progress-modal #progress-bar {
  height: 100%;
  background-color: #4caf50;
  width: 0%;
}
#otListModal .modal-dialog {
    max-width: 400px;
    width: 90%;
}
#otListModal .modal-content {
    background-color: #f8f9fa; /* Light gray background */
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
#otListModal .modal-header {
    background-color: #007bff; /* Blue header */
    color: white;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}
#otListModal .modal-title{
    font-weight: bold;
}
#otListModal .modal-body {
    padding: 20px;
}
#otListModal .form-group label {
    font-weight: bold;
    color: #333;
}
	.btn-enhanced {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    cursor: pointer;
    min-width: 80px;
}

.btn-enhanced:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
}

.btn-draft {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-draft:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    color: white;
}
.btn-final {
            background: linear-gradient(135deg, #28a745, #20c997);
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
        .clickable-row {
            cursor: pointer;
        }
        .clickable-row:hover {
            background-color: #f8f9fa;
        }
        .table-active {
            background-color: #d1ecf1 !important;
        }
        .deductions-table-compact {
    font-size: 13px;
    margin-bottom: 0;
}

.deductions-table-compact thead th {
    padding: 6px 8px;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    vertical-align: middle;
    height: 36px;
}

.deductions-table-compact tbody td {
    padding: 4px 8px;
    vertical-align: middle;
    height: 32px;
}

.deductions-table-compact tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Make striped rows more subtle */
.deductions-table-compact.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.015);
}
    @keyframes slideIn {
    from { right: -100px; opacity: 0; }
    to { right: 20px; opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
    </style>
    
    <!-- Make sure CSS is loaded before content -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <div class="mobile-menu-overlay"></div>
    <h1 class="header-container">Manage Payroll</h1>
    <div>
        <div class="pd-ltr-20 xs-pd-20-10">
            <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
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
                <button type="button" class="btn btn-sm btn-secondary mr-1" data-toggle="modal" data-target="#employeeModal">Process by Employee</button>
                <!------<button type="button" class="btn btn-sm btn-secondary mr-1">Edit Mode</button>---->
                <button type="button" class="btn btn-sm btn-info">View loan schedule</button>
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
                        <span id="total-records" class="badge badge-info" style="font-size: 14px;"></span>
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
        
        <button id="preview-totals-btn" class="btn btn-enhanced btn-final">
                <i class="fas fa-bolt"></i> Auto Calculate
            </button>
    </div>
    </div>
    
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="exampleModalLabel">Post by Parameter</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-2">
                    <form id="payrollForm" class="compact-form">
                        <div class="row no-gutters">
                            <div class="col-md-6 pr-md-0">
                                <fieldset class="border p-1 mb-0">
                                    <legend class="w-auto small mb-0">Current Payroll Period</legend>
                                    <div class="form-group row no-gutters">
                                        <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-sm" id="month" value="{{ $month }}"  readonly>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control form-control-sm" id="year" value="{{ $year }}" readonly>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6 pl-md-1">
                                <fieldset class="border p-1 mb-0">
                                    <legend class="w-auto small mb-0">Payroll Items</legend>
                                    <div class="form-group row no-gutters">
                                        
                                        <div class="col-sm-12">
                                            <select name="pitem" id="pitem" class="custom-select form-control" required="true" autocomplete="off" onchange="populateCategory()">
                                                <option value="">Select Item</option>
                                                
                                            </select>
                                            <input name="category" id="category" type="text" class="form-control" required="true" autocomplete="off" hidden>
                                            <input name="increREDU" id="increREDU" type="text" class="form-control" required="true" autocomplete="off" hidden>
                                            <input name="codebal" id="codebal" type="text" class="form-control" required="true" autocomplete="off" hidden>
                                            
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <fieldset class="border p-1 mb-0">
                            <legend class="w-auto small mb-0">Specific Search</legend>
                            <div class="form-group row no-gutters">
                                
                                <label for="searchValue" class="col-sm-2 col-form-label col-form-label-sm">Select Staff</label>
                                <div class="col-sm-7">
                                    <select name="searchValue" id="searchValue" class="form-control" required onchange="searchstaffdet()">
                                        <option value="">Select Staff</option>
                                    </select>
                                   
                                </div>
                                 <div class="col-sm-4 pr-1" hidden>
                                    <select class="form-control form-control-sm" id="searchCategory" >
                                        <option value="WorkNumber">Work number</option>
                                        <option value="Surname">Name</option>
                                    </select>
                                </div>
                                
                            </div>
                            <div class="form-group row no-gutters">
                                <label for="surname" class="col-sm-2 col-form-label col-form-label-sm">SURNAME</label>
                                <div class="col-sm-4 pr-1">
                                    <input type="text" class="form-control form-control-sm" id="surname" readonly>
                                </div>
                                <label for="workNumber" class="col-sm-2 col-form-label col-form-label-sm">OTHER NAME</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control form-control-sm" id="othername" readonly>
                                </div>
                            </div>
                            <div class="form-group row no-gutters">
                                <label for="workNumber" class="col-sm-2 col-form-label col-form-label-sm">WORK NUMBER</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control form-control-sm" id="workNumber" readonly>
                                </div>
                                <label for="department" class="col-sm-2 col-form-label col-form-label-sm">DEPARTMENT</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control form-control-sm" id="department" hidden>
                                    <input type="text" class="form-control form-control-sm" id="departmentname" readonly>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-group row no-gutters">
                            <label for="amount" class="col-sm-2 col-form-label col-form-label-sm">AMOUNT:</label>
                            <div class="col-sm-3 pr-1">
                                <input type="text" class="form-control form-control-sm" id="amount" placeholder="ENTER AMOUNT" >
                            </div>
                            <label for="balance" class="col-sm-2 col-form-label col-form-label-sm">BALANCE:</label>
                            <div class="col-sm-3 pr-1">
                                <input type="text" class="form-control form-control-sm" id="balance" placeholder="ENTER BALANCE">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" id="submitBtn" class="btn btn-primary btn-sm">Post</button>
                            </div>
                        </div>
                        <fieldset class="border p-1 mb-2" id="hiddenContainer" style="display: none;">
                            <div class="d-flex flex-row align-items-center">
                                <div class="form-group row no-gutters mr-3 flex-grow-1">
                                    <label for="months" class="col-auto col-form-label col-form-label-sm mr-2">MONTHS</label>
                                    <div class="col-sm-3 pr-2">
                                        <input type="text" class="form-control form-control-sm" id="months">
                                    </div>
                                    <label for="enddate" class="col-auto col-form-label col-form-label-sm mx-2">END DATE</label>
                                    <div class="col-sm-3 pr-2">
                                        <input type="text" class="form-control form-control-sm" id="enddate" readonly>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text p-0 bg-transparent border-0">
                                                <span class="toggle-switch mb-0 mr-2">
                                                    <input type="checkbox" id="activeinaclonToggle" checked>
                                                    <span class="slider round"></span>
                                                </span>
                                                <span id="toggleLabel3">Active</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="border p-3 mb-3" id="hiddenContainer2" style="display: none;">
                            <div class="form-group row align-items-center no-gutters">
                                <div class="col-auto mr-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text p-0 bg-transparent border-0">
                                                <span class="toggle-switch mb-0 mr-2">
                                                    <input type="checkbox" id="fixedOpenToggle" checked>
                                                    <span class="slider round"></span>
                                                </span>
                                                <span id="toggleLabel">Open</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2 mr-3">
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="cbalance" name="duration" placeholder="Current balance" readonly>
                                    </div>
                                </div>
                                <div id="Fixed" class="col-sm-8" style="display: none;">
                                    <div class="row no-gutters">
                                        <div class="col-sm-6 pr-2">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Duration:</span>
                                                </div>
                                                <input type="text" class="form-control" id="duration" name="duration" placeholder="months">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Ends In:</span>
                                                </div>
                                                <input type="text" class="form-control" id="balend" name="balend" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="Open">
                                <div class="col-auto mr-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text p-0 bg-transparent border-0">
                                                <span class="toggle-switch mb-0 mr-2">
                                                    <input type="checkbox" id="activeinacToggle" checked>
                                                    <span class="slider round"></span>
                                                </span>
                                                <span id="toggleLabel2">Active</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="border p-1 mb-2" id="pensionContainer" style="display: none;">
                            <div class="form-group row no-gutters">
                                <label for="months" class="col-sm-2 col-form-label col-form-label-sm">Employee %</label>
                                <div class="col-sm-1 pr-1">
                                    <input type="text" class="form-control form-control-sm" id="epmpenperce">
                                </div>
                                <label for="enddate" class="col-sm-2 col-form-label col-form-label-sm">Employer %</label>
                                <div class="col-sm-1 pr-1">
                                    <input type="text" class="form-control form-control-sm" id="emplopenperce">
                                </div>
                                <label for="enddate" class="col-sm-2 col-form-label col-form-label-sm">Pensionable</label>
                                <div class="col-sm-3 pr-1">
                                    <input type="text" class="form-control form-control-sm" id="pensionable" readonly>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="border p-1 mb-2" id="otContainer" style="display: none;">
                            <div class="form-group row no-gutters">
                                <label for="enddate" class="col-sm-1 col-form-label col-form-label-sm">Formula:</label>
                                <div class="col-sm-3 pr-1">
                                    <input name="formular" id="formular" type="text" class="form-control" required="true" autocomplete="off" readonly>
                                </div>
                                <label for="enddate" class="col-sm-0.5 col-form-label col-form-label-sm">Date:</label>
                                <div class="col-sm-3 pr-1">
                                    <input type="date" class="form-control" id="otdate">
                                </div>
                                <label for="enddate" class="col-sm-1 col-form-label col-form-label-sm">Quantity:</label>
                                <div class="col-sm-1 pr-1">
                                    <input type="text" class="form-control" id="quantity">
                                </div>
                                <button type="button" id="btnopenot" class="btn btn-info btn-sm">Open</button>
                                <input type="text" class="form-control form-control-sm" id="camountf" hidden>
                            </div>
                        </fieldset>
                        <div class="form-group" style="margin-top: 10px;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-container">
                                        <div style="padding: 20px;">
                                            <table id="contentTable2" class="content-table2">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Work number</th>
                                                        <th >Department</th>
                                                        <th>Parameter Code</th>
                                                        
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row no-gutters" style="margin-left: 300px;">
                            
                            <label for="totals" class="col-sm-2 col-form-label col-form-label-sm text-right">Total:</label>
                            <div class="col-sm-3 pr-1">
                                <input type="text" class="form-control form-control-sm" id="totalsvar" readonly>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="exampleModalLabel">Process by Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-2">
                    <form id="payrollempForm" class="compact-form">
                        <div class="row no-gutters">
                            <div class="col-md-6 pr-md-1">
                                <fieldset class="border p-1 mb-2">
                                    <legend class="w-auto small mb-0">Current Payroll Period</legend>
                                    <div class="form-group row no-gutters">
                                       
                                        <label for="month" class="col-sm-3 col-form-label col-form-label-sm">MONTH</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="month" value="<?php echo htmlspecialchars($month); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row no-gutters">
                                        <label for="year" class="col-sm-3 col-form-label col-form-label-sm">YEAR</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="year" value="<?php echo htmlspecialchars($year); ?>" readonly>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6 pl-md-1">
                                <fieldset class="border p-1 mb-2">
                                    <legend class="w-auto small mb-0">Employee</legend>
                                    <div class="form-group row no-gutters">
                                        
                                        <div class="col-sm-12">
                                           
                                            <select name="WorkNo" id="WorkNo" class="custom-select form-control" required="true" autocomplete="off" onchange="searchstaffdet2()">
                                            <option value="">Select Staff</option>
                                            
                                        </select>
                                        </div>
                                    </div>
                                    <div class="form-group row no-gutters">
                                        <label for="year" class="col-sm-3 col-form-label col-form-label-sm">Name</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control form-control-sm" id="empname"  readonly>
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control form-control-sm" id="empname1"  readonly>
                                        </div>
                                    </div>
                                    
                                </fieldset>
                            </div>
                        </div>
                        <input type="text" class="form-control form-control-sm" id="empdept"  readonly hidden>
                    </form>
                    <div class="mt-3">
    <div class="row mb-2">
        <div class="col" hidden>
            <input type="text" class="form-control form-control-sm" id="inputID" placeholder="ID">
        </div>
        <div class="col">
            <label for="inputPCode" class="col-form-label col-form-label-sm">Code</label>
            <input type="text" class="form-control form-control-sm" id="inputPCode" placeholder="PCode">  
        </div>
        <div class="col">
            <label for="inputname" class="col-form-label col-form-label-sm">Parameter Name</label>
            <input type="text" class="form-control form-control-sm" id="inputname" placeholder="Category" readonly>
        </div>
        <div class="col">
            <label for="inputAmount" class="col-form-label col-form-label-sm">Amount</label>
            <input type="text" class="form-control form-control-sm" id="inputAmount" placeholder="Amount">
        </div>
        <div class="col">
            <label for="inputBalance" class="col-form-label col-form-label-sm">Balance</label>
            <input type="text" class="form-control form-control-sm" id="inputBalance" placeholder="Balance">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <button id="submitButton" class="btn btn-primary btn-sm">Submit</button>
        </div>
    </div>
    <table id="employeeDataTable" class="content-table">
        <thead>
            <tr>
                <th hidden>ID</th>
                <th>PCode</th>
                <th>Item Name</th>
                
                <th>Amount</th>
                <th>Balance</th>
                <th>Date Posted</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div class="row mb-3">
    <div class="col-md-6 d-flex align-items-end">
        <button id="recalcButton" class="btn btn-primary btn-sm me-2">Recalculate</button>
    </div>
    <div class="col-md-3">
        <label for="monthPicker" class="form-label">Period:</label>
        <input type="month" id="periodPick" class="form-control" name="monthPicker">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button id="viewp" class="btn btn-info btn-sm">View Payslip</button>
    </div>
</div>
</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="codeSelectionModal" tabindex="-1" role="dialog" aria-labelledby="codeSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="codeSelectionModalLabel">Select Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
    <p>Double click to select a code from the table:</p>
    <table class="content-table" >
        <thead>
            <tr>
                <th>Code</th>
            </tr>
        </thead>
        <tbody id="codeList" style="padding: 3px 5px; font-size: 12px;">
            <!-- Codes will be dynamically inserted here -->
        </tbody>
    </table>
</div>
        </div>
    </div>
</div>


    
<div id="successMessage" style="display:none;"></div>
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

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- 2. Then DataTables core and styles -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    
    <!-- 3. SweetAlert Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    

    
    <!-- 4. Your custom scripts -->
    <script>
        if (typeof Swal === 'undefined') {
    console.warn('Local SweetAlert2 not found, using CDN');
    // CDN will define Swal globally
}
        const currentMonth = $('#currentMonth').val();
const currentYear = $('#currentYear').val();
 
// Add event listener to the button
document.getElementById('preview-totals-btn').addEventListener('click', function() {
    // Get current month and year (make sure these variables are defined)
    const currentMonth = $('#currentMonth').val();
const currentYear = $('#currentYear').val();
    
    // Confirmation message
    const confirmationMessage = `Are you sure you want to process totals for ${currentMonth} ${currentYear}?`;

    // Use SweetAlert for confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: confirmationMessage,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, process totals!',
        cancelButtonText: 'No, cancel!',
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            processPayrollTotals(currentMonth, currentYear);
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Cancelled',
                text: 'Processing was cancelled.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
});

/**
 * Process payroll totals with SSE progress tracking
 */
function processPayrollTotals(month, year) {
    // Show progress modal
    Swal.fire({
        title: 'Processing Payroll',
        html: `
            <div class="progress" style="height: 25px;">
                <div id="swal-progress-bar" 
                     class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: 0%;" 
                     aria-valuenow="0" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    0%
                </div>
            </div>
            <p id="swal-progress-message" class="mt-3 mb-0">Initializing process...</p>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Create EventSource for SSE
     const url = "{{ route('autocalc.process') }}?month=" + encodeURIComponent(month) + "&year=" + encodeURIComponent(year) + "&t=" + Date.now();
    const evtSource = new EventSource(url);
    
    let lastPercent = 0;
    
    // Progress update event
    evtSource.addEventListener('progress', function(event) {
        try {
            const data = JSON.parse(event.data);
            updateProgressBar(data.percent, data.message);
            lastPercent = data.percent;
        } catch (e) {
            console.error('Error parsing progress event:', e);
        }
    });
    
    // Completion event
    evtSource.addEventListener('complete', function(event) {
        try {
            const data = JSON.parse(event.data);
            
            // Update to 100%
            updateProgressBar(100, 'Processing complete!');
            
            // Close connection
            evtSource.close();
            
            // Show success message
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Processing Completed!',
                    html: `
                        <p>${data.message || 'Totals processed successfully!'}</p>
                        ${data.totalGrossPays ? `<p class="mb-0"><strong>Total Gross Pays:</strong> ${formatCurrency(data.totalGrossPays)}</p>` : ''}
                    `,
                    confirmButtonText: 'OK',
                    timer: 5000
                }).then(() => {
                    // Optionally reload or redirect
                    // window.location.reload();
                });
            }, 500);
        } catch (e) {
            console.error('Error parsing complete event:', e);
            evtSource.close();
            showError('Processing completed but response parsing failed.');
        }
    });
    
    // Error event from server
    evtSource.addEventListener('error', function(event) {
        console.error('SSE error event:', event);
        evtSource.close();
        
        let errorMessage = 'An error occurred while processing totals.';
        
        try {
            if (event.data) {
                const data = JSON.parse(event.data);
                if (data && data.message) {
                    errorMessage = data.message;
                }
            }
        } catch (e) {
            console.error('Error parsing error event data:', e);
        }
        
        showError(errorMessage);
    });
    
    // Connection error handler
    evtSource.onerror = function(error) {
        console.error('EventSource connection error:', error);
        
        // Only show error if we haven't completed successfully
        if (lastPercent < 100) {
            evtSource.close();
            
            // Check if it's a connection error vs server error
            if (evtSource.readyState === EventSource.CLOSED) {
                showError('Connection lost. Please check your internet connection and try again.');
            } else {
                showError('An unexpected error occurred. Please try again.');
            }
        }
    };
}

/**
 * Update progress bar and message
 */
function updateProgressBar(percent, message) {
    const progressBar = document.getElementById('swal-progress-bar');
    const progressMessage = document.getElementById('swal-progress-message');
    
    if (progressBar) {
        progressBar.style.width = percent + '%';
        progressBar.setAttribute('aria-valuenow', percent);
        progressBar.textContent = Math.round(percent) + '%';
    }
    
    if (progressMessage) {
        progressMessage.textContent = message;
    }
}

/**
 * Show error message
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Processing Error',
        text: message,
        confirmButtonText: 'OK'
    });
}
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES'
    }).format(amount);
}
    let currentPage = 1;
        let currentSearch = '';

        // Load deductions
        function loadDeductions(page = 1, search = '') {
            currentPage = page;
            currentSearch = search;

            $.ajax({
                url: '{{ route("payroll.deductions.data") }}',
                type: 'GET',
                data: {
                    page: page,
                    search: search,
                    per_page: 10
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#tableBody').html(`
                        <tr>
                            <td colspan="7" class="text-center">
                                <i class="fa fa-spinner fa-spin"></i> Loading...
                            </td>
                        </tr>
                    `);
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Update period info
                        $('#period-info').html(`
                            <i class="fa fa-calendar"></i> 
                            <strong>Active Period:</strong> ${response.period.month} ${response.period.year}
                        `);

                        // Update table
                        renderTable(response.data);

                        // Update pagination
                        renderPagination(response.pagination);

                        // Update record count
                        $('#total-records').text(`Total Records: ${response.pagination.total}`);
                        $('#showing-info').text(
                            `Showing ${response.pagination.from || 0} to ${response.pagination.to || 0} of ${response.pagination.total} entries`
                        );
                    } else {
                        $('#tableBody').html(`
                            <tr>
                                <td colspan="7" class="text-center text-danger">
                                    ${response.message}
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Load error:', error);
                    $('#tableBody').html(`
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                Error loading data. Please try again.
                            </td>
                        </tr>
                    `);
                }
            });
        }

        // Render table rows
        function renderTable(data) {
            if (data.length === 0) {
                $('#tableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center">No records found</td>
                    </tr>
                `);
                return;
            }

            let html = '';
            data.forEach(row => {
                html += `
                    <tr class="clickable-row" onclick="highlightRow(this)">
                        <td>${row.full_name}</td>
                        <td>${row.work_no}</td>
                        <td>${row.department}</td>
                        <td>${row.code}</td>
                        <td>${row.name}</td>
                        <td>${row.category}</td>
                        <td class="text-right">${row.amount}</td>
                    </tr>
                `;
            });
            $('#tableBody').html(html);
        }

        // Render pagination
        function renderPagination(pagination) {
            if (pagination.last_page <= 1) {
                $('#pagination').html('');
                return;
            }

            let html = '';
            
            // Previous button
            if (pagination.current_page > 1) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
                </li>`;
            }

            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                if (
                    i === 1 || 
                    i === pagination.last_page || 
                    (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)
                ) {
                    html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            // Next button
            if (pagination.current_page < pagination.last_page) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
                </li>`;
            }

            $('#pagination').html(html);
        }

        // Highlight row on click
        function highlightRow(row) {
            $(row).siblings().removeClass('table-active');
            $(row).addClass('table-active');
        }

        // Event handlers
        $(document).ready(function() {
            $('#contentTable2').DataTable({
        columnDefs: [
            { targets: 2, width: '80px' } // Department column
        ],
        autoWidth: false,
        data: [], // Start with empty data
        columns: [
            { title: "Name" },
            { title: "Work number" },
            { title: "Department" },
            { title: "Parameter Code" },
            { title: "Amount" }
        ]
    });

                $('#pitem').on('change', function() {
        var selectedParameter = $(this).val();
        if (selectedParameter) {
            fetchData(selectedParameter);
        } else {
            function clearTable() {
        $('#contentTable2 tbody').empty();
    }
        }
    });
            // Initial load
            loadDeductions();

            // Search button
            $('#searchBtn').on('click', function() {
                const search = $('#searchInput').val();
                loadDeductions(1, search);
            });

            // Clear button
            $('#clearBtn').on('click', function() {
                $('#searchInput').val('');
                loadDeductions(1, '');
            });

            // Search on Enter key
            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#searchBtn').click();
                }
            });

            // Pagination click
            $(document).on('click', '#pagination a', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) {
                    loadDeductions(page, currentSearch);
                }
            });
            $('.toggle-checkbox').on('change', function () {
    let checkbox = $(this);
    let isChecked = checkbox.is(':checked');
    let model = checkbox.data('model');
    let fieldName = checkbox.next('label').text();
    let action = isChecked ? 'activate' : 'deactivate';

    // Revert temporarily
    checkbox.prop('checked', !isChecked);

    if (confirm(`Are you sure you want to ${action} ${fieldName}?`)) {

        $.ajax({
            url: "{{ route('toggle.status') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                model: model,
                status: isChecked ? 'ACTIVE' : 'INACTIVE'
            },
            success: function (response) {
                checkbox.prop('checked', isChecked);
                showMessage(fieldName + " updated successfully.", false);
            },
            error: function () {
                alert("Error updating " + fieldName);
            }
        });

    }
});

$('#empmodal').on('click', function() {
   
    $.ajax({
        url: "{{ route('mngprol.getcodes') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#pitem');
            dropdown.empty();
            dropdown.append('<option value="">Select Item</option>');
            response.data.forEach(function (pitem) {
                dropdown.append(
                    `<option data-code="${pitem.code}" data-category="${pitem.category}" data-increredu="${pitem.increREDU}" data-formular="${pitem.formularinpu}" value="${pitem.cname}">${pitem.code} - ${pitem.cname}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load streams. Please try again.');
        },
    });
 

    $('#pitem').select2({
        placeholder: "Select Item",
        allowClear: true,
        dropdownParent: $('#exampleModal'), // Ensures the dropdown is appended within the modal
        width: '100%'
    }).on('select2:open', function(e) {
        // Stop propagation of mousedown events on the Select2 dropdown to prevent modal closure
       
    });

     
    // Optional: Remove the mousedown event listener when the Select2 dropdown is closed
 

      // Check if Choices exists
    if (typeof Choices === 'undefined') {
        console.error('Choices.js is NOT loaded.');
        return;
    }

    // Initialize Choices
    const choices = new Choices('#searchValue', {
        searchEnabled: true,
        placeholderValue: 'Select Staff',
        searchPlaceholderValue: 'Search staff...',
        allowHTML: true,
        shouldSort: false,
        itemSelectText: '',
        noResultsText: 'No matching staff'
    });

    // --- Debounce helper ---
    function debounce(fn, delay) {
        let timer;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, arguments), delay);
        };
    }

    // --- AJAX search function ---
    function fetchStaff(term = '') {
        $.ajax({
            url: "{{ route('payroll.staff.search') }}",
            method: "GET",
            data: { term: term },
            dataType: 'json',
            success: function (data) {

                const results = data.results || [];

                const items = results.map(item => ({
                    value: item.emp_id,
                    label: item.label
                }));

                choices.clearChoices();
                choices.setChoices(items, 'value', 'label', true);
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
            }
        });
    }

    // We need to attach to Choices internal input field
    function attachSearchListener() {
        const internalInput = $('.choices__input');

        if (internalInput.length) {
            internalInput.on(
                'input',
                debounce(function () {
                    const term = $(this).val().trim();
                    fetchStaff(term);
                }, 300)
            );
        } else {
            // Choices builds DOM async, so retry
            setTimeout(attachSearchListener, 50);
        }
    }

    attachSearchListener();

    // Optional initial load
    fetchStaff('');
   
});

        });

        function populateCategory() {
    const amountField = document.getElementById('amount');
    const balanceField = document.getElementById('balance');
    var pitem = document.getElementById("pitem");
    var category = document.getElementById("category");
    var increREDU = document.getElementById("increREDU");
    var code = document.getElementById("codebal");
    var formular = document.getElementById("formular");
    var selectedOption = pitem.options[pitem.selectedIndex];

    amountField.readOnly = false; // Make the amount field read-only
    amountField.value = ''; 
    balanceField.readOnly = false; // Make the amount field read-only
    balanceField.value = ''; 
    
    // Set the hidden category input to the data-category attribute of the selected option
    category.value = selectedOption.getAttribute('data-category');

    // Set the hidden increREDU input to the data-increredu attribute of the selected option
    increREDU.value = selectedOption.getAttribute('data-increredu');
    code.value = selectedOption.getAttribute('data-code');
    formular.value = selectedOption.getAttribute('data-formular');

    toggleHiddenContainer();
    toggleHiddenContainer2();
    toggleHiddenContainer4();
    cleartxt2();
}
function toggleHiddenContainer() {
    var categoryValue = document.getElementById("category").value;
    var hiddenContainer = document.getElementById("hiddenContainer");
    var hiddenContainer2 = document.getElementById("hiddenContainer2");
    
    // Hide both containers by default
    hiddenContainer.style.display = 'none';
    hiddenContainer2.style.display = 'none';
    
    // Show the appropriate container based on the category
    if (categoryValue === 'loan') {
        hiddenContainer.style.display = 'block';
    } else if (categoryValue === 'balance') {
        hiddenContainer2.style.display = 'block';
    }
    // The else case is not needed as both containers are hidden by default
}
function toggleHiddenContainer2() {
    var categoryValue = document.getElementById("pitem").value;
    var hiddenContainer = document.getElementById("pensionContainer");

    if (categoryValue === 'Pension') {
        hiddenContainer.style.display = 'block';
    } else {
        hiddenContainer.style.display = 'none';
    }
}
function toggleHiddenContainer4() {
    var fValue = document.getElementById("formular").value.trim();
    var hiddenContainer = document.getElementById("otContainer");
    var balField = document.getElementById("balance");
    var amountField = document.getElementById("amount");
    var otdate = document.getElementById("otdate");
    

    if (fValue) {
        // When formular has value
        hiddenContainer.style.display = 'block';
        
        amountField.setAttribute('readonly', true);
        balField.setAttribute('readonly', true);
        
        otdate.setAttribute('required', true);
        
        
    } else {
        // When formular is empty
        $('#formular').val('');
        hiddenContainer.style.display = 'none';
        
       
    }
}
function cleartxt2(){
        $('#balance').val('');
        $('#balend').val('');
        $('#duration').val('');
        $('#quantity').val('');
        $('#camountf').val('');
    }
    function searchstaffdet(){ 
    performSearch();
}
function performSearch() {
    var searchCategory = $('#searchCategory').val();
    var searchValue    = $('#searchValue').val();
    var code           = $('#codebal').val();
    var category       = $('#category').val();
    var formula        = $('#formular').val();

    if (searchValue.trim() === '') {
        clearFields();
        return;
    }

    var codes = [];
    if (formula.trim() !== '') {
        codes = formula.match(/[A-Za-z]+\d+/g) || [];
    }

    $.ajax({
        url: "{{ route('staff.search.details') }}",
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            searchCategory: searchCategory,
            searchValue: searchValue,
            category: category,
            code: code,
            codes: codes
        },
        success: function(data) {
            if (!data.success) {
                clearFields();
                showMessage(data.message, true);
                return;
            }

            // set fields
            $('#surname').val(data.surname);
            $('#workNumber').val(data.workNumber);
            $('#othername').val(data.othername);
            $('#department').val(data.department);
            $('#departmentname').val(data.departmentname);

            $('#epmpenperce').val(data.epmpenperce);
            $('#emplopenperce').val(data.emplopenperce);
            $('#pensionable').val(data.totalPensionAmount);

            // Deduction amounts
            let amounts = [];
            codes.forEach(c => amounts.push(data.existingCodes[c] || ''));
            $('#camountf').val(amounts.join(','));

            if (category === 'balance') {
                $('#cbalance').val(data.balance);
                $('#balance').val(data.balance);
                $('#amount').val(data.Amount);

                let toggle = $('#activeinacToggle');
                let label  = $('#toggleLabel2');
                if (data.statdeduc === '1' || data.statdeduc === '') {
                    toggle.prop('checked', true);
                    label.text('Active');
                } else {
                    toggle.prop('checked', false);
                    label.text('Inactive');
                }
            }

            if (category === 'loan') {
                $('#balance').val(data.balance);
                $('#amount').val(data.Amount);

                let toggle = $('#activeinaclonToggle');
                let label  = $('#toggleLabel3');
                if (data.statdeduc === '1' || data.statdeduc === '') {
                    toggle.prop('checked', true);
                    label.text('Active');
                } else {
                    toggle.prop('checked', false);
                    label.text('Inactive');
                }
            }

            $('#amount').focus();
        },
        error: function(xhr, status, error) {
            showMessage("AJAX Error: " + status, true);
        }
    });
}

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
function fetchData(parameter) {

    $.ajax({
        url: "{{ route('fetch.items') }}",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            parameter: parameter
        },
        success: function(response) {
            if (!response.success) {
                console.log(response.message);
                return;
            }
         

            populateTable(response.data);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
function populateTable(data) {
    var table = $('#contentTable2').DataTable();
    table.clear();
    var totalAmount = 0;

    data.forEach(function(item) {
        var fullName = item.Surname + " " + item.othername;

        table.row.add([
            fullName,
            item.WorkNo,
            item.dept,
            item.PCode,
            item.Amount
        ]);

        totalAmount += parseFloat(item.Amount) || 0;
    });

    table.draw();
    $('#totalsvar').val(totalAmount.toFixed(2));
}






    document.addEventListener('DOMContentLoaded', function() {
        const toggleSwitch = document.getElementById('fixedOpenToggle');
        const toggleSwitch2 = document.getElementById('activeinacToggle');
        const toggleSwitch3 = document.getElementById('activeinaclonToggle');
        const toggleLabel = document.getElementById('toggleLabel');
        const toggleLabel2 = document.getElementById('toggleLabel2');
        const toggleLabel3 = document.getElementById('toggleLabel3');
        const fixedContainer = document.getElementById('Fixed');
        const openContainer = document.getElementById('Open');
        const amountField = document.getElementById('amount');
        const increREDUField = document.getElementById('increREDU'); // Reference to #increREDU
        function updateAmountFieldState() {
            if (increREDUField.value === 'Reducing') {
                amountField.readOnly = true; 
                amountField.value = '0'; 
            } else if (increREDUField.value === 'Increasing') {
                amountField.readOnly = false; 
                amountField.value = ''; 
            }
        }
        if (toggleSwitch) {
            toggleSwitch.addEventListener('change', function() {
                if (this.checked) {
                    toggleLabel.textContent = 'Open';
                    fixedContainer.style.display = 'none';
                    openContainer.style.display = 'block';
                } else {
                    toggleLabel.textContent = 'Fixed';
                    fixedContainer.style.display = 'block';
                    openContainer.style.display = 'none';
                }
                updateAmountFieldState(); 
                cleartxt2();
            });
        }
        if (toggleSwitch2) {
            toggleSwitch2.addEventListener('change', function() {
                if (this.checked) {
                    toggleLabel2.textContent = 'Active';
                } else {
                    toggleLabel2.textContent = 'Inactive';
                }
            });
        }
        if (toggleSwitch3) {
            toggleSwitch3.addEventListener('change', function() {
                if (this.checked) {
                    toggleLabel3.textContent = 'Active';
                } else {
                    toggleLabel3.textContent = 'Inactive';
                }
            });
        }
    });
    $('#viewp').on('click', function () {
    var staffid = $('#WorkNo').val();
    var selperiod = $('#periodPick').val();

    if (!staffid) {
        showMessage('Work Number cannot be empty', true);
        return;
    }

    if (!selperiod) {
        showMessage('Please select a period', true);
        return;
    }

    var [year, month] = selperiod.split('-');
    var monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    var monthName = monthNames[parseInt(month) - 1];
    var period = monthName + year;

    $.ajax({
        url: 'payview', // Your PHP route
        method: 'POST',
        data: { staffid: staffid, period: period },
        success: function (response) {
            if (response.pdf) {
                // Create PDF blob and show it in modal
                var pdfBlob = new Blob([Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))], { type: 'application/pdf' });
                var pdfUrl = URL.createObjectURL(pdfBlob);

                $('#pdfModal .modal-body').html(
                    `<iframe src="${pdfUrl}" width="100%" height="600px" style="border:none;"></iframe>`
                );
                $('#pdfModal').modal('show');
            } else {
                showMessage("PDF generation failed.", true);
            }
        },
        error: function () {
            showMessage("Error generating payslip.", true);
        }
    });
});



    document.getElementById('recalcButton').addEventListener('click', function() {
    var workNo = document.getElementById('WorkNo').value.trim();
    var month = document.getElementById('month').value;
    var year = document.getElementById('year').value;

    // Validate that WorkNo is not empty
    if (!workNo) {
        showMessage('Select a staff to process', true);
        return; // Exit the function if WorkNo is empty
    }

    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();

    // Configure it: POST-request to autocalc2.php
    xhr.open('POST', 'autocalc2.php', true);

    // Set the Content-Type header to JSON
    xhr.setRequestHeader('Content-Type', 'application/json');

    // Send the request with the data as JSON
    xhr.send(JSON.stringify({
        WorkNo: workNo,
        month: month,
        year: year
    }));

    // Define what happens on successful data submission
    xhr.onload = function() {
        if (xhr.status === 200) {
            showMessage('Recalculation successful');
            var response = JSON.parse(xhr.responseText);
            console.log(response);
            // You can add code here to handle the response from autocalc2.php
        } else {
            console.error('Recalculation failed. Status:', xhr.status);
            showMessage('Recalculation failed. Please try again.');
        }
    };
});
        $(document).ready(function() {
   
    $('#employeeModal').on('hidden.bs.modal', function () { 
        $('#employeeDataTable tbody').empty();
    });

    // Attach click event to table rows in employeeListTable
    /*$('#employeeListTable').on('click', 'tr', function() {
        var workNo = $(this).find('td:first').text(); // Get WorkNo from the first column of the clicked row
        $('#WorkNo').val(workNo); // Set the WorkNo input field
        performSearch2(); // Call the search function
        $('#employeeListModal').modal('hide'); // Hide the modal
    });*/

document.getElementById('inputPCode').addEventListener('keydown', function(event) {
    // Check if the key pressed is Tab (9) or Enter (13)
    if (event.key === 'Tab' || event.key === 'Enter') {
        var pCode = this.value;

        if (pCode.length > 0) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'sepcode.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        document.getElementById('inputname').value = response.cname;
                    } else {
                        document.getElementById('inputname').value = '';

                        // Populate the modal with the list of codes
                        var codeList = document.getElementById('codeList');
                        codeList.innerHTML = '';  // Clear previous list

                        response.codeList.forEach(function(codeObj) {
                            var li = document.createElement('li');
                            li.className = 'list-group-item';
                            li.textContent = codeObj.code + ' - ' + codeObj.cname;
                            li.setAttribute('data-code', codeObj.code);
                            li.setAttribute('data-cname', codeObj.cname);
                            codeList.appendChild(li);
                        });

                        // Show the modal
                        $('#codeSelectionModal').modal('show');
                    }
                }
            };
            xhr.send('PCode=' + encodeURIComponent(pCode));
        } else {
            document.getElementById('inputname').value = '';
        }

        // Prevent the default behavior of the Tab key (i.e., moving focus)
        if (event.key === 'Tab') {
            event.preventDefault();
        }
    }
});

// Handle code selection from the modal
document.getElementById('codeList').addEventListener('click', function(e) {
    if (e.target && e.target.nodeName == "LI") {
        var selectedCode = e.target.getAttribute('data-code');
        var selectedCname = e.target.getAttribute('data-cname');

        // Set the selected code and cname to the respective input fields
        document.getElementById('inputPCode').value = selectedCode;
        document.getElementById('inputname').value = selectedCname;

        // Hide the modal
        $('#codeSelectionModal').modal('hide');
    }
});


// Handle code selection from the modal
document.getElementById('codeList').addEventListener('click', function(e) {
    if (e.target && e.target.nodeName == "LI") {
        var selectedCode = e.target.getAttribute('data-code');
        var selectedCname = e.target.getAttribute('data-cname');

        // Set the selected code and cname to the respective input fields
        document.getElementById('inputPCode').value = selectedCode;
        document.getElementById('inputname').value = selectedCname;

        // Hide the modal
        $('#codeSelectionModal').modal('hide');
    }
});


// Search functionality


// Submit button functionality
document.getElementById('submitButton').addEventListener('click', function() {
    var id = document.getElementById('inputID').value;
    var pCode = document.getElementById('inputPCode').value;
    var name = document.getElementById('inputname').value;
    var amount = document.getElementById('inputAmount').value;
    var balance = document.getElementById('inputBalance').value;
    var WorkNo = document.getElementById('WorkNo').value;
    var month = document.getElementById('month').value;
    var year = document.getElementById('year').value;

    

    if (!pCode || !name || !amount || !balance || !WorkNo) {
        showErrorMessage('Please fill in the required inputs');
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'subEpcode.php', true); 
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                showMessage('Payroll Item submitted ', false);
                
                console.log(response);
                // Clear the form
                document.getElementById('inputID').value = '';
                document.getElementById('inputPCode').value = '';
                document.getElementById('inputname').value = '';
                document.getElementById('inputAmount').value = '';
                document.getElementById('inputBalance').value = '';
                // Refresh the table
                performSearch2(); // Use this instead of loadEmployeeData()
            } else {
                alert('Error submitting data: ' + response.message);
            }
        }
    };
    xhr.send('id=' + encodeURIComponent(id) +
         '&pCode=' + encodeURIComponent(pCode) +
         '&name=' + encodeURIComponent(name) +
         '&amount=' + encodeURIComponent(amount) +
         '&balance=' + encodeURIComponent(balance) +
         '&WorkNo=' + encodeURIComponent(WorkNo) +
         '&month=' + encodeURIComponent(month) +
         '&year=' + encodeURIComponent(year));
});

function highlightAndPopulateRow(row) {
    $('#employeeDataTable tbody tr').removeClass('highlighted');
    $(row).addClass('highlighted');
    
    $('#inputID').val($(row).find('td:eq(0)').text());
    $('#inputPCode').val($(row).find('td:eq(1)').text());
    $('#inputname').val($(row).find('td:eq(2)').text());
    $('#inputAmount').val($(row).find('td:eq(3)').text());
    $('#inputBalance').val($(row).find('td:eq(4)').text());
}

function highlightRow(row) {
    $('#employeeListTable tbody tr').removeClass('highlighted');
    $(row).addClass('highlighted');
    
    $('#WorkNo').val($(row).find('td:eq(0)').text());
    $('#employeeListModal').modal('hide');
    
    performSearch2();
}

$('<style>')
    .prop("type", "text/css")
    .html(`
        .highlighted {
            background-color: #e6f3ff !important;
        }
    `)
    .appendTo("head");

$('#searchButton').on('click', performSearch2);




// Add event listener for selecting an employee from the list
/*$(document).on('click', '.select-employee', function(e) {
    e.preventDefault();
    var workNo = $(this).data('workno');
    $('#WorkNo').val(workNo);
    $('#employeeListModal').modal('hide');
    performSearch2();
});*/
});
function highlightAndPopulateRow(row) {
    // Remove highlight from all rows
    $('#employeeDataTable tbody tr').removeClass('highlight');
    
    // Add highlight to clicked row
    $(row).addClass('highlight');
    
    // Populate input fields
    $('#inputID').val($(row).find('td:eq(0)').text());
    $('#inputPCode').val($(row).find('td:eq(1)').text());
    $('#inputname').val($(row).find('td:eq(2)').text());
    $('#inputAmount').val($(row).find('td:eq(3)').text());
    $('#inputBalance').val($(row).find('td:eq(4)').text());
}

function cleartxt(){
                    $('#inputID').val('');
                    $('#inputPCode').val('');
                    $('#inputname').val('');
                    $('#inputAmount').val('');
                    $('#inputBalance').val('');
}
    // Get the current date, month, and year










     function highlightRow(row) {
    var tableRows = document.getElementById('contentTable').getElementsByTagName('tr');
    for (var i = 0; i < tableRows.length; i++) {
        tableRows[i].classList.remove('highlight');
    }
    row.classList.add('highlight');
}

function showErrorMessage(message) {
    let errorDiv = document.getElementById('errorMessage');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'errorMessage';
        document.body.appendChild(errorDiv);
    }
    
    errorDiv.textContent = message;
    errorDiv.style.position = 'fixed';
    errorDiv.style.top = '20px';
    errorDiv.style.left = '20px';
    errorDiv.style.backgroundColor = '#f44336';
    errorDiv.style.color = 'white';
    errorDiv.style.padding = '15px';
    errorDiv.style.borderRadius = '5px';
    errorDiv.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
    errorDiv.style.zIndex = '1051'; 
    errorDiv.style.display = 'block';
    
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 2000);
}

document.addEventListener('DOMContentLoaded', function() {
    var rows = document.querySelectorAll("#contentTable tbody tr");
    var viewButton = document.querySelector('.btn-info');

    rows.forEach(function(row) {
        row.addEventListener("click", function() {
            highlightRow(this);
        });
    });

    viewButton.addEventListener("click", function() {
        var selectedRow = document.querySelector("#contentTable tbody tr.highlight");
        if (selectedRow) {
            var loanshares = selectedRow.cells[5].textContent; 
            var workNo = selectedRow.cells[1].textContent; 
            var pCode = selectedRow.cells[3].textContent; 

            if (loanshares.toLowerCase() === 'loan') {
                window.location.href = 'loansched.php?empid=' + encodeURIComponent(workNo) + '&loantype=' + encodeURIComponent(pCode);
            } else {
                showErrorMessage('Please select a loan category.');
            }
        } else {
            showErrorMessage('Please select a row first.');
        }
    });
});
function calcbalancedates(){

    const balanceInput = document.getElementById('balance');
    const durationInput = document.getElementById('duration');
    const amountInput = document.getElementById('amount');

    const balance = parseFloat(balanceInput.value);
    const duration = parseFloat(durationInput.value);

    if (!isNaN(balance) && !isNaN(duration)) {
        const amount = balance / duration;
        amountInput.value = amount.toFixed(2);
        updateEndDate2(duration);
    }

}
function updateEndDate2(duration) {
    const endDateInput = document.getElementById('balend');
    const currentDate = new Date();
    
    // Calculate the end date and subtract one month
    const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + Math.ceil(duration), 0);
    
    const endMonth = endDate.toLocaleString('default', { month: 'long' });
    const endYear = endDate.getFullYear();
    
    endDateInput.value = `${endMonth} ${endYear}`;
}
       function calculateMonthsAndEndDate() {
    const balanceInput = document.getElementById('balance');
    const amountInput = document.getElementById('amount');
    const monthsInput = document.getElementById('months');
    const endDateInput = document.getElementById('enddate');

    const balance = parseFloat(balanceInput.value);
    const amount = parseFloat(amountInput.value);

    if (!isNaN(balance) && !isNaN(amount) && amount !== 0) {
        const months = balance / amount;
        monthsInput.value = months.toFixed(2);
        updateEndDate(months);
    }
}

function updateEndDate(months) {
    const endDateInput = document.getElementById('enddate');
    const currentDate = new Date();
    
    // Calculate the end date and subtract one month
    const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + Math.ceil(months), 0);
    
    const endMonth = endDate.toLocaleString('default', { month: 'long' });
    const endYear = endDate.getFullYear();
    
    endDateInput.value = `${endMonth} ${endYear}`;
}

function recalculateAmount() {
    const balanceInput = document.getElementById('balance');
    const amountInput = document.getElementById('amount');
    const monthsInput = document.getElementById('months');

    const balance = parseFloat(balanceInput.value);
    const months = parseFloat(monthsInput.value);

    if (!isNaN(balance) && !isNaN(months) && months !== 0) {
        const newAmount = balance / months;
        amountInput.value = newAmount.toFixed(2);
        updateEndDate(months);
    }
}

document.getElementById('amount').addEventListener('input', calculateMonthsAndEndDate);
document.getElementById('balance').addEventListener('input', calculateMonthsAndEndDate);
document.getElementById('months').addEventListener('input', recalculateAmount);
document.getElementById('duration').addEventListener('input', calcbalancedates);


function clearFields() {
    $('#surname').val('');
    $('#workNumber').val('');
    $('#othername').val('');
    $('#department').val('');
    $('#departmentname').val('');
    $('#epmpenperce').val('');
    $('#emplopenperce').val('');
    $('#pensionable').val('');
    $('#balance').val('');
    $('#activeinacToggle').prop('checked', false);
    $('#toggleLabel2').text('Inactive');
}
function clearFields2() {     
    $('#empname').val('');
    $('#empname1').val('');
    $('#inputPCode').val('');
    $('#inputname').val('');
    $('#inputAmount').val('');
    $('#inputBalance').val('');
}
function searchstaffdet(){ 
    performSearch();
}
function searchstaffdet2(){ 
    performSearch2();
}
function performSearch2() {
    var workNo = $('#WorkNo').val();
    var month = $('#month').val();
    var year = $('#year').val();
    cleartxt();
    
    $.ajax({
        url: 'search2.php',
        method: 'POST',
        data: {
            workNo: workNo,
            month: month,
            year: year
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#empname1').val(response.employee.Surname);
                $('#empname').val(response.employee.othername);
                $('#empdept').val(response.employee.dept);
                $('#WorkNo').val(workNo);
                
                var tableBody = $('#employeeDataTable tbody');
                tableBody.empty();
                
                $.each(response.deductions, function(index, deduction) {
                    var formattedDate = new Date(deduction.dateposted);
                    var options = { year: 'numeric', month: 'short', day: '2-digit' };
                    var formattedDateString = formattedDate.toLocaleDateString('en-US', options);
                    var row = '<tr onclick="highlightAndPopulateRow(this)">' +
                        '<td hidden>' + deduction.ID + '</td>' +
                        '<td>' + deduction.PCode + '</td>' +
                        '<td>' + deduction.pcate + '</td>' +
                        
                        '<td>' + deduction.Amount + '</td>' +
                        '<td>' + deduction.balance + '</td>' +
                        '<td>' + formattedDateString + '</td>' +
                        '<td hidden>' + deduction.loanshares + '</td>' +
                        '</tr>';
                    tableBody.append(row);
                });
            } else {
                var tableBody = $('#employeeListTable tbody');
                tableBody.empty();
                
                $.each(response.employeeList, function(index, employee) {
                    var row = '<tr onclick="highlightRow(this)">' +
                        '<td>' + employee.WorkNo + '</td>' +
                        '<td>' + employee.Surname + ' ' + employee.othername + '</td>' +
                        '</tr>';
                    tableBody.append(row);
                });
                
                $('#employeeListModal').modal('show');
            }
        },
        error: function() {
            alert('An error occurred while searching');
        }
    });
}




       
function executeFormula() {
    var formula = $('#formular').val(); // Get the formula
    var amounts = $('#camountf').val().split(','); // Get amounts from #camountf
    var quantity = parseFloat($('#quantity').val()); // Get the quantity value
    var codes = formula.match(/[A-Za-z]+\d+/g); // Extract codes from the formula

    // Replace codes in the formula with corresponding amounts or quantity
    codes.forEach(function(code, index) {
        var amount = amounts[index];
        if (amount === '') {
            amount = quantity; // Use quantity if the amount is missing
        }
        formula = formula.replace(code, amount);
    });

    try {
        // Evaluate the formula safely
        var result = eval(formula);
        // Format the result to 2 decimal places
        $('#amount').val(result.toFixed(2)); // Display the result with 2 decimal places
    } catch (error) {
        console.error('Error evaluating formula:', error);
        $('#amount').val('Error'); // Indicate an error if evaluation fails
    }
}
$(document).ready(function() {
     // Simple implementation for your dropdown

  const choices2 = new Choices('#WorkNo', {
    searchEnabled: true,
    placeholderValue: 'Select Staff',
     searchPlaceholderValue: 'Search staff...',
    allowHTML: true
}); 
    document.getElementById('quantity').addEventListener('input', executeFormula);

    $('#pitem').on('change', function() {
        var selectedParameter = $(this).val();
        if (selectedParameter) {
            fetchData(selectedParameter);
        } else {
            function clearTable() {
        $('#contentTable2 tbody').empty();
    }
        }
    });

   

   
function validateForm() {
    var requiredFields = ['month', 'year', 'pitem', 'surname', 'othername', 'workNumber', 'department', 'amount', 'balance'];
    var isValid = true;

    // Check if pension fields are visible
    var isPensionVisible = $('#pensionContainer').is(':visible');
    if (isPensionVisible) {
        requiredFields.push('epmpenperce', 'emplopenperce');
    }

    var isOTVisible = $('#otContainer').is(':visible');
    if (isOTVisible) {
        requiredFields.push('quantity', 'otdate', 'formular');
    }


    requiredFields.forEach(function(field) {
        var $field = $('#' + field);
        var value = $field.val();

        if (field === 'pitem') {
            // Special handling for Select2 dropdown
            if (!value || value.length === 0) {
                isValid = false;
                $field.next('.select2-container').addClass('is-invalid');
            } else {
                $field.next('.select2-container').removeClass('is-invalid');
            }
        } else {
            // Standard field validation
            if (!value || value.trim() === '') {
                isValid = false;
                $field.addClass('is-invalid');
            } else {
                $field.removeClass('is-invalid');
            }
        }
    });

    if (!isValid) {
        showMessage('Please fill in all required fields', true);
    }

    return isValid;
}

function submitForm() {
    // Collect form data
    var formData = {
        month: $('#month').val(),
        year: $('#year').val(),
        parameter: $('#pitem').val(),
        surname: $('#surname').val(),
        othername: $('#othername').val(),
        workNumber: $('#workNumber').val(),
        department: $('#department').val(),
        amount: $('#amount').val(),
        category: $('#category').val(),
        enddate: $('#enddate').val(),
        months: $('#months').val(),
        balance: $('#balance').val()
    };

    // Check if pension fields are visible and add them to formData if they are
    if ($('#pensionContainer').is(':visible')) {
        formData.epmpenperce = $('#epmpenperce').val();
        formData.emplopenperce = $('#emplopenperce').val();
    }
    if ($('#otContainer').is(':visible')) {
        formData.quantity = $('#quantity').val();
        formData.otdate = $('#otdate').val();
        formData.formular = $('#formular').val();
    }
    if ($('#Open').is(':visible')) {
    // Check the state of the toggle and set the value accordingly
    var isActive = $('#activeinacToggle').prop('checked');
    formData.openvalue = isActive ? '1' : '0';
}

if ($('#hiddenContainer').is(':visible')) {
    // Check the state of the toggle and set the value accordingly
    var isActive = $('#activeinaclonToggle').prop('checked');
    formData.openvalue = isActive ? '1' : '0';
}

if ($('#category').val() === 'normal') {
    formData.openvalue = '1';
}
   const selectedParameter = $('#pitem').val();

   const submitBtn = $('#submitBtn');  // jQuery object
const originalText = submitBtn.html();

submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Posting...').prop('disabled', true);


    // Send the AJAX request
    $.ajax({
        url: '{{ route("payroll.submit") }}', 
        type: 'POST',
        data: formData,
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
        dataType: 'json',
        success: function(response) {
            try {
                // Check if response is already an object
                var result = typeof response === 'object' ? response : JSON.parse(response);
                if (result.success) {
                    showMessage('Payroll Item Saved ', false);

                    setTimeout(function() {
                        fetchData(selectedParameter);
                    }, 100);
                   
                    $('#surname, #workNumber, #othername, #department, #departmentname, #epmpenperce, #emplopenperce, #pensionable, #balance, #searchValue, #amount, #quantity, #otdate').val('');
                    
                } else {
                    showMessage(result.message || 'An error occurred', true);
                }
            } catch (e) {
                showMessage('Invalid response from server: ' + JSON.stringify(response), true);
            }
        },
        error: function(xhr, status, error) {
            showMessage('An error occurred: ' + error, true);
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
            
        }
    });
}
$('#submitBtn').on('click', function(e) {
    e.preventDefault();  // Prevent the default form submission
    if (validateForm()) {
        submitForm();    // Call the function to submit the form via AJAX
    }
});
    function clearTable() {
        $('#contentTable2 tbody').empty();
    }
function clearFields() {
    $('#surname').val('');
    $('#workNumber').val('');
    $('#othername').val('');
    $('#department').val('');
    $('#departmentname').val('');
    $('#epmpenperce').val('');
    $('#emplopenperce').val('');
    $('#pensionable').val('');
    $('#balance').val('');
    $('#activeinacToggle').prop('checked', false);
    $('#toggleLabel2').text('Inactive');
}
    

    function showErrorMessage(message) {
        let errorDiv = document.getElementById('errorMessage');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'errorMessage';
            document.body.appendChild(errorDiv);
        }

        errorDiv.textContent = message;
        errorDiv.style.position = 'fixed';
        errorDiv.style.top = '20px';
        errorDiv.style.left = '20px';
        errorDiv.style.backgroundColor = '#f44336';
        errorDiv.style.color = 'white';
        errorDiv.style.padding = '15px';
        errorDiv.style.borderRadius = '5px';
        errorDiv.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
        errorDiv.style.zIndex = '1051'; // Higher than the modal's z-index
        errorDiv.style.display = 'block';

        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 2000);
    }
    
});

$(document).ready(function() {
    $('#exampleModal').on('hidden.bs.modal', function () {
        // Reset the form
        clearFields();
        //$('#payrollForm')[0].reset();
        $('#pitem').val('').trigger('change');
        //$('#searchValue').val('').trigger('change');
       choicesSearchValue.removeActiveItems();


    });
    $('#employeeModal').on('hidden.bs.modal', function () {
        // Reset the form
        clearFields2();
    });
    

  
    
$('#btnopenot').on('click', function(e) {
    // Get values
    const workNumber = $('#workNumber').val();
    const otdate = $('#otdate').val();
    const codebal = $('#codebal').val();

    var yea2r = document.getElementById('otdate').value;

    if (!yea2r) {
        showErrorMessage('Please select a date');
        return;
    }
    
    // Parse the month and year from the otdate
    const dateObj = new Date(otdate);
    const month = dateObj.getMonth() + 1; // Months are 0-indexed
    const year = dateObj.getFullYear();

    // Send data to the server via AJAX
    $.ajax({
        url: 'searchot.php', // Update with the actual server-side URL
        type: 'POST',
        data: {
            workNumber: workNumber,
            codebal: codebal,
            month: month,
            year: year
        },
        success: function(response) {
            // Assuming the response is JSON containing records
            const records = JSON.parse(response);

            // Populate the table in the modal
            let tableBody = $('#otListTable tbody');
            tableBody.empty(); // Clear any existing rows
            
            records.forEach(record => {
                const row = `<tr>
                                <td>${record.odate}</td>
                                <td>${record.quantity}</td>
                                <td>${record.tamount}</td>
                             </tr>`;
                tableBody.append(row);
            });

            // Show the modal
            $('#otListModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error("Error fetching records: " + error);
        }
    });
});




    
});

    </script>
</x-custom-admin-layout>