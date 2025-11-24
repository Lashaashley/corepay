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

    <script>
        const autocalc = '{{ route("autocalc.process") }}';
        const getwuth = '{{ route("payroll.deductions.data") }}';
        const tstatus = '{{ route("toggle.status") }}';
         const getcodes = '{{ route("mngprol.getcodes") }}';
         const staffsearch = '{{ route("payroll.staff.search") }}';
         const staffdet = '{{ route("staff.search.details") }}';
         const fetchitems = '{{ route("fetch.items") }}';
         const paysubmit = '{{ route("payroll.submit") }}';
    </script>
    <script src="{{ asset('js/mngprol.js') }}"></script>

    
    <!-- 4. Your custom scripts -->
    <script>
      
    </script>
</x-custom-admin-layout>