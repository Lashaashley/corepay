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
    font-size: 14px;
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

label {
    margin-bottom: 2px;
}
.form-control {
    padding: 2px 4px;
    height: auto;
}
.form-group-border {
    border: 2px solid #ced4da;
            border-radius: 0.25rem;
            padding: 5px;
            position: relative;
            margin-top: -20px;
            margin-bottom: 10px;
        }
        .form-group-border legend {
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
        

.form-actions {
  text-align: right;
  margin-top: -10px;
}
.toggle-container {
  position: relative;
  display: inline-block;
  width: 200px;
  height: 34px;
  background-color: #f0f0f0;
  border-radius: 34px;
  overflow: hidden;
  border: 1px solid #ccc;
  margin-bottom: 5px;
}

.toggle-container input {
  display: none;
}

.toggle-container label {
  display: inline-block;
  float: left;
  width: 50%;
  height: 100%;
  line-height: 34px;
  text-align: center;
  cursor: pointer;
  position: relative;
  z-index: 2;
  transition: color 0.3s;
  color: #666;
}

.slider {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 98px;
  height: 30px;
  border-radius: 30px;
  transition: transform 0.3s, background-color 0.3s;
}

/* Styles for the first toggle (varorfixed) */
#varorfixed-toggle #variable:checked ~ .slider {
  transform: translateX(0);
  background-color: #3498db; /* Blue color for Variable */
}

#varorfixed-toggle #fixed:checked ~ .slider {
  transform: translateX(100px);
  background-color: #2ecc71; /* Green color for Fixed */
}
#recint-toggle #recintre:checked ~ .slider {
  transform: translateX(0);
  background-color: #800080; /* Blue color for Variable */
}

#recint-toggle #separate:checked ~ .slider {
  transform: translateX(100px);
  background-color: #fa2007; /* Green color for Fixed */
}
/* Styles for the second toggle (taxaornon) */
#taxaornon-toggle #taxable:checked ~ .slider {
  transform: translateX(0);
  background-color: #2ecc71; /* Blue color for Taxable */
}

#taxaornon-toggle #nontax:checked ~ .slider {
  transform: translateX(100px);
  background-color: #3498db; /* Green color for Non-taxable */
}

/* Common styles for both toggles */
.toggle-container input:checked ~ label {
  color: #fff;
}

.toggle-container input:not(:checked) ~ label {
  color: #333;
}
.horizontal-fields {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    margin-bottom: 3px;
}

.field-group {
    flex: 1;
    min-width: 200px;
    margin-right: 15px;
    margin-bottom: 3px;
}

.field-group:last-child {
    margin-right: 0;
}

.field-group label {
    display: block;
    margin-bottom: 5px;
}

.field-group input {
    width: 100%;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}


/* Reduce spacing between columns */
.col-sm-6 {
    padding-right: 10px;
    padding-left: 10px;
}

/* Adjust spacing for the last form group */
form > .form-group:last-child {
    margin-bottom: 0;
}
.toggle-container#relief-toggle {
  width: 300px; /* Increased width to accommodate three options */
  height: 34px;
  background-color: #f0f0f0;
  border-radius: 34px;
  overflow: hidden;
  border: 1px solid #ccc;
  margin-bottom: 5px;
  position: relative;
  display: inline-block;
}

.toggle-container#relief-toggle {
  width: 300px;
  height: 34px;
  background-color: #f0f0f0;
  border-radius: 34px;
  overflow: hidden;
  border: 1px solid #ccc;
  margin-bottom: 5px;
  position: relative;
  display: inline-block;
}

#relief-toggle input {
  display: none;
}

#relief-toggle label {
  display: inline-block;
  float: left;
  width: 33.33%;
  height: 100%;
  line-height: 34px;
  text-align: center;
  cursor: pointer;
  position: relative;
  z-index: 2;
  transition: color 0.3s;
  color: #666;
  font-size: 0.8rem;
}

#relief-toggle .slider {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 98px;
  height: 30px;
  border-radius: 30px;
  transition: transform 0.3s, background-color 0.3s;
}

#relief-toggle #none:checked ~ .slider {
  transform: translateX(0);
  background-color: #3498db;
}

#relief-toggle #rnt:checked ~ .slider {
  transform: translateX(100px);
  background-color: #2ecc71;
}

#relief-toggle #rnp:checked ~ .slider {
  transform: translateX(200px);
  background-color: #e74c3c;
}

#relief-toggle input:checked + label {
  color: #fff;
}

#relief-toggle input:not(:checked) + label {
  color: #333;
}
#editVarOrFixedToggle #editVariable:checked ~ .slider {
    transform: translateX(0);
    background-color: #3498db; /* Blue color for Variable */
}

#editVarOrFixedToggle #editFixed:checked ~ .slider {
    transform: translateX(100px);
    background-color: #2ecc71; /* Green color for Fixed */
}

/* Styles for the Taxable/Non-taxable toggle */
#editTaxableToggle #editTaxable:checked ~ .slider {
    transform: translateX(0);
    background-color: #e74c3c; /* Red color for Taxable */
}

#editTaxableToggle #editNonTax:checked ~ .slider {
    transform: translateX(100px);
    background-color: #f39c12; /* Orange color for Non-taxable */
}

#recint-toggleedit #recintredit:checked ~ .slider {
    transform: translateX(0);
    background-color: #3d0678; /* Red color for Taxable */
}

#recint-toggleedit #separatedit:checked ~ .slider {
    transform: translateX(100px);
    background-color: #fa2007; /* Orange color for Non-taxable */
}
#editReliefToggle {
    width: 300px; /* Increased width for three options */
}

#editReliefToggle label {
    width: 33.33%; /* Each label takes up 1/3 of the container */
    font-size: 0.8em; /* Smaller font size to fit text */
}

#editReliefToggle .slider {
    width: 98px; /* Approximately 1/3 of the container width minus borders */
}

#editReliefToggle #editNone:checked ~ .slider {
    transform: translateX(0);
    background-color: #3498db; /* Blue for Not Relief */
}

#editReliefToggle #editRNT:checked ~ .slider {
    transform: translateX(100px);
    background-color: #2ecc71; /* Green for Relief on Taxable */
}

#editReliefToggle #editRNP:checked ~ .slider {
    transform: translateX(200px);
    background-color: #e74c3c; /* Red for Relief on Paye */
}
/* Hide default HTML checkbox */
#loanRate .toggle-switch {
  position: relative;
  display: inline-block;
  width: 30px;
  height: 17px;
  vertical-align: middle;
}

#loanRate .toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

#loanRate .toggle-switch .slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 17px;
}

#loanRate .toggle-switch .slider:before {
  position: absolute;
  content: "";
  height: 13px;
  width: 13px;
  left: 2px;
  bottom: 2px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

#loanRate .toggle-switch input:checked + .slider {
  background-color: #2196F3;
}

#loanRate .toggle-switch input:checked + .slider:before {
  transform: translateX(26px);
}

#loanRate #toggleLabel2 {
  margin-left: 5px;
  vertical-align: middle;
}

@keyframes slideIn {
    from { right: -100px; opacity: 0; }
    to { right: 20px; opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
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
        #sortableDeductions .list-group-item {
    cursor: move;
    transition: all 0.3s ease;
}

#sortableDeductions .list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

#sortableDeductions .list-group-item.sortable-ghost {
    opacity: 0.4;
    background-color: #e3f2fd;
}

#sortableDeductions .list-group-item.sortable-drag {
    opacity: 0.8;
    transform: rotate(2deg);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* Priority Badge */
.priority-badge {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
}

/* Drag Handle */
.drag-handle {
    cursor: grab;
    color: #6c757d;
}

.drag-handle:active {
    cursor: grabbing;
}

/* Current Item Pulse Animation */
.current-item-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4); }
    50% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
}
   
    </style>
    <div class="mobile-menu-overlay"></div>
   
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height 50px">
				
                <div class="tab-container" >
                    <button class="tab-button active" onclick="openTab(event, 'contactInfo')">Payroll Items</button>
                    
                    
                </div>
                <div id="contactInfo" class="tab-content active" >
                    <div class="pd-20 card-box mb-30">
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4 class="text-blue h4">Items</h4>
                                
                            </div>
                        </div>
                        <div class="pb-20">
                        <table class="data-table table stripe hover nowrap" id="payrollCodesTable">
    <thead>
        <tr>
            <th class="table-plus datatable-nosort" hidden>ID</th>
            <th>Code</th>
            <th>Description</th>
            <th>Process type</th>
            <th>Trans type</th>
            <th>pay_type</th>
            <th>Category</th>
            <th hidden>Relief T</th>
            <th hidden>prossty</th>
            <th hidden>rate</th>
            <th hidden>incre</th>
            <th hidden>prossty</th>
            <th hidden>rate</th>
            <th hidden>incre</th>
            <th hidden>incre</th>
            <th hidden>incre</th>
            <th hidden>incre</th>
            <th hidden>incre</th>
            <th class="datatable-nosort">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payrollItems as $row)
        <tr>
            <td class="table-plus" hidden>{{ $row->ID }}</td>
            <td>{{ $row->code }}</td>
            <td>{{ $row->cname }}</td>
            <td>{{ $row->procctype }}</td>
            <td>{{ $row->varorfixed }}</td>
            <td>{{ $row->taxaornon }}</td>
            <td>{{ $row->category }}</td>
            <td hidden>{{ $row->relief }}</td>
            <td hidden>{{ $row->prossty }}</td>
            <td hidden>{{ $row->rate }}</td>
            <td hidden>{{ $row->increREDU }}</td>
            <td hidden>{{ $row->recintres }}</td>
            <td hidden>{{ $row->formularinpu }}</td>
            <td hidden>{{ $row->cumcas }}</td>
            <td hidden>{{ $row->intrestcode }}</td>
            <td hidden>{{ $row->codename }}</td>
            <td hidden>{{ $row->issaccorel }}</td>
            <td hidden>{{ $row->sposter }}</td>
            <td>
                <div class="dropdown">
                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                       href="#" role="button" data-toggle="dropdown">
                        <i class="dw dw-more"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                        <a class="dropdown-item"
                           href="#"
                           data-toggle="modal"
                           data-target="#editpitemsModal"
                           data-id="{{ $row->ID }}"
                           onclick="openEditModal(this)">
                            <i class="dw dw-edit2"></i> Edit
                        </a>
                        <a class="dropdown-item"
                           href="#"
                           data-id="{{ $row->ID }}"
                           data-code="{{ $row->code }}"
                           onclick="deletePayrollCode(this)">
                            <i class="dw dw-delete-3"></i> Delete
                        </a>
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
                <div class="mt-3">
        <button type="button" class="btn btn-enhanced btn-draft" data-toggle="modal" data-target="#payrollModal"><i class="fas fa-plus-square"></i>New
            </button>
    </div>

			   </div>
                    </div>
                </div>
                
            </div>
        </section>
    </div>
    <div class="modal fade" id="payrollModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Payroll Codes - Add</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                <form id="payrollForm">
                    @csrf
                     <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <div class="form-group row">
                        <label for="code" class="col-sm-2 col-form-label">Code</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <label for="description" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                    </div>
                    <div class="form-group row" >
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Process Type:</label>
                                <div>
                                    <input type="radio" id="amount" name="processt" value="Amount" required checked>
                                    <label for="amount">Amount</label>
                                    <input type="radio" id="calculationRadio" name="processt" value="calculation">
                                    <label for="calculation">Calculation</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Category:</label>
                                <select class="form-control" name="category" id="category" required>
                                    <option value="">Select</option>
                                    <option value="normal">Normal</option>
                                    <option value="balance">Balance</option>
                                    <option value="loan">Loan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4" id="balanceOptions" style="display: none;">
                            <div class="form-group">
                                <label>Balance Type:</label>
                                <div>
                                    <input type="radio" id="increasing" name="balanceType" value="Increasing">
                                    <label for="increasing">Increasing</label>
                                    <input type="radio" id="reducing" name="balanceType" value="Reducing">
                                    <label for="reducing">Reducing</label>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="loanRate" style="display: none;">
                            <div class="col-auto">
                                <div class="form-group mb-0">
                                    <label for="rate" class="mr-2">Rate:</label>
                                    <input type="text" class="form-control d-inline-block" id="rate" name="rate" style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group mb-0"> <!---here--->
                                    <div class="toggle-container" id="recint-toggle">
                                        <input type="radio" id="recintre" name="recintres" value="1" required checked>
                                        <label for="recintre">Recov&Int</label>
                                        <input type="radio" id="separate" name="recintres" value="0">
                                        <label for="separate">Separate</label>
                                        <span class="slider"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container mt-3" >
                        <div class="row" >
                            <div class="col-sm-6">
                                <div class="form-group-border" style="margin-bottom: 10px;">
                                    <legend>Payroll Code Type</legend>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <select class="form-control" name="prossty" id="prossty" required>
                                                <option value="">Select</option>
                                                <option value="Payment">Payment</option>
                                                <option value="Deduction">Deduction</option>
                                                <option value="Benefit">Benefit</option>
                                                <option value="Relief">Relief</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row"  >
                                        <div class="col-sm-12">
                                            <div class="toggle-container" id="varorfixed-toggle">
                                                <input type="radio" id="variable" name="varorfixed" value="Variable" required checked>
                                                <label for="variable">Variable</label>
                                                <input type="radio" id="fixed" name="varorfixed" value="Fixed">
                                                <label for="fixed">Fixed</label>
                                                <span class="slider"></span>
                                            </div> 
                                            <div class="form-check form-check-inline">
                                                
                                                
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="form-group row" >
                                        <div class="col-sm-12">
                                            <div class="toggle-container" id="taxaornon-toggle">
                                                <input type="radio" id="taxable" name="taxaornon" value="Taxable" required checked>
                                                <label for="taxable">Taxable</label>
                                                <input type="radio" id="nontax" name="taxaornon" value="Non-taxable">
                                                <label for="nontax">Non-taxable</label>
                                                <span class="slider"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container mt-4">
                                        <div class="form-group row" style="margin-top: -15px;">
                                            <div class="col-sm-5 d-flex align-items-center">
                                                <input type="checkbox" id="saccocheck" name="saccocheck" value="Yes" class="form-check-input me-2">
                                                <label for="saccocheck" class="form-check-label">Sacco related</label>
                                            </div>
                                            <div class="col-sm-7" id="sacconames" hidden>
                                                <label>Staff List:</label>
                                            <select name="staffSelect7" id="staffSelect7" class="custom-select form-control" autocomplete="off">
                                                <option value="">Select Staff</option>
                                                
                                            </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <legend>GL Accounts</legend>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="pjornal">
                                            <label class="form-check-label" for="pjornal">Link to Payroll Journal</label>
                                        </div>
                                    </div>
                                    <div class="form-group" >
                                        
                                        <input type="text" class="form-control" id="accountNumber" placeholder="A/C Number">
                                    </div>
                                    <div class="form-group" >
                                        <label for="cc">CC</label>
                                        <input type="text" class="form-control" id="cc" placeholder="CC">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row" style="margin-left: 55px;">
                        <div class="col-sm-8">
                            <input class="form-check-input" type="checkbox" id="exemptionBonuses">
                            <label class="form-check-label" for="exemptionBonuses">Exemption Bonuses / Overtime and Retirements Benefits</label>
                        </div>
                    </div>
                    <div class="form-group-border" style="margin-bottom: 10px;">
                        <div class="horizontal-fields">
                            <div class="form-check">
                                <label for="appearInP9" class="col-form-label">Appear in P9</label>
                                <input type="checkbox" class="form-control" id="appearInP9">
                            </div>
                            
                            <div class="field-group">
                                <div class="col-sm-16">
                                    <div class="toggle-container" id="relief-toggle">
                                        <input type="radio" id="none" name="relief" value="NONE" required checked>
                                        <label for="none">Not Relief</label>
                                        <input type="radio" id="rnt" name="relief" value="RELIEF ON TAXABLE">
                                        <label for="rnt">Relief taxable</label>
                                        <input type="radio" id="rnp" name="relief" value="Relief on Paye">
                                        <label for="rnp">Relief on Paye</label>
                                        <span class="slider"></span>
                                    </div>
                                </div>
                            </div>
                           
                               
                        </div>
                    </div>
                    <div class="form-group-border">
                        <div class="horizontal-fields">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="calculation">
                                <label class="form-check-label" for="calculation">Calculation</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="calctype" id="cumulativeValue" value="cumulative">
                                <label class="form-check-label" for="cumulativeValue">Cumulative Value</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="calctype" id="casual" value="casual">
                                <label class="form-check-label" for="casual">Casual</label>
                            </div>
                        </div>
                        </div>
                        <div class="form-group">
                            <input type="text" id="inputField" name="formularinpu" class="form-control" readonly placeholder="Formula Input" required>
                        </div>
                        <div class="row align-items-center" id="loanhelper" style="display: none;">
                            <div class="col-auto">
                                <div class="form-group mb-0">
                                    <label for="rate" class="mr-2">Interest Code:</label>
                                    <input type="text" class="form-control d-inline-block" id="interestcode" name="interestcode" style="width: 100px;">
                                    <label for="rate" class="mr-2">Description:</label>
                                    <input type="text" class="form-control d-inline-block" id="interestdesc" name="interestdesc" style="width: 150px;">
                                </div>
                            </div>
                        </div>
                        <div id="feedback" style="color: red;"></div>
                        
                    </div>
                    <div id="prioritySection" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Drag and drop</strong> to set the deduction priority order. 
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Current Item Card -->
                                 <div class="card mb-1 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-star"></i> Current Deduction
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0" id="currentItemName">Item Code</h6>
                                                <small class="text-muted" id="currentItemCode">Code will appear here</small>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-primary badge-lg" id="currentPriorityBadge">
                                                    Priority: <span id="currentPriorityNumber">-</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <i class="fas fa-list-ol"></i> Existing Deductions (Drag to Reorder)
                                    </div>
                                    <div class="card-body p-0">
                                        <ul id="sortableDeductions" class="list-group list-group-flush">
                                            <!-- Items will be loaded here via AJAX -->
                                             <li class="list-group-item text-center text-muted py-1">
                                                <i class="fas fa-spinner fa-spin"></i> Loading deductions...
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <input type="hidden" name="priority" id="priorityInput">
                            </div>
                            <div class="col-md-4">
                                <!-- Priority Legend -->
                                 <div class="card bg-light">
                                    <div class="card-header">
                                        <i class="fas fa-question-circle"></i> Priority Guide
                                    </div>
                                    <div class="card-body">
                                        <p class="small mb-2"><strong>How Priority Works:</strong></p>
                                        <ol class="small pl-3 mb-3">
                                            <li>Lower numbers = Higher priority</li>
                                            <li>Priority 1 is deducted first</li>
                                            <li>Drag items to change order</li>
                                            <li>Your new item shown above</li>
                                        </ol>
                                        <p class="small mb-2"><strong>Example Order:</strong></p>
                                        <div class="small">
                                            <div class="mb-1">1️⃣ Statutory</div>
                                            <div class="mb-1">2️⃣ Loans</div>
                                            <div class="mb-1">3️⃣ SACCO Contributions</div>
                                            <div class="mb-1">4️⃣ Welfare</div>
                                            <div>5️⃣ Other Deductions</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    
                    <button type="submit" class="btn btn-enhanced btn-finalize">
                <i class="fas fa-save"></i> Save
            </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editpitemsModal" tabindex="-1" role="dialog" aria-labelledby="payrollModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="payrollModalLabel">Payroll Codes - Change</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editpayrollForm">
                         @csrf
                        <div class="form-group row">
                            <label for="editCode" class="col-sm-2 col-form-label">Code</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="editCode" name="editCode" >
                                <input type="text" class="form-control" id="editid" name="editid" hidden>
                            </div>
                            <label for="editDescription" class="col-sm-2 col-form-label">Description</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="editDescription" name="editDescription" readonly>
                            </div>
                        </div>
                        <div class="form-group row" >
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Process Type:</label>
                                    <div>
                                        <input type="radio" id="editAmount" name="editProcessType" value="Amount" required>
                                        <label for="editAmount">Amount</label>
                                        <input type="radio" id="editCalculation" name="editProcessType" value="Calculation">
                                        <label for="editCalculation">Calculation</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Category:</label>
                                    <select class="form-control" name="editCategory" id="editCategory">
                                        <option value="">Select</option>
                                        <option value="normal">Normal</option>
                                        <option value="balance">Balance</option>
                                        <option value="loan">Loan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4" id="editBalanceOptions" style="display: none;">
                                <div class="form-group">
                                    <label>Balance Type:</label>
                                    <div>
                                        <input type="radio" id="editIncreasing" name="editBalanceType" value="Increasing">
                                        <label for="editIncreasing">Increasing</label>
                                        <input type="radio" id="editReducing" name="editBalanceType" value="Reducing">
                                        <label for="editReducing">Reducing</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3" id="editLoanRate" style="display: none;">
                                <div class="form-group">
                                    <label for="editRate">Rate:</label>
                                    <input type="text" class="form-control" id="editRate" name="editRate">
                                </div>
                                <div class="form-group d-flex align-items-center" >
                                    <div class="toggle-container" id="recint-toggleedit">
                                        <input type="radio" id="recintredit" name="editrecintres" value="1" required checked>
                                        <label for="recintredit">Recov&Int</label>
                                        <input type="radio" id="separatedit" name="editrecintres" value="0">
                                        <label for="separatedit">Separate</label>
                                        <span class="slider"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border" style="margin-bottom: 20px;">
                                        <legend>Payroll Code Type</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <select class="form-control" name="editProcessSty" id="editProcessSty">
                                                    <option value="">Select</option>
                                                    <option value="Payment">Payment</option>
                                                    <option value="Deduction">Deduction</option>
                                                    <option value="Benefit">Benefit</option>
                                                    <option value="Relief">Relief</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row" >
                                            <div class="col-sm-12">
                                                <div class="toggle-container" id="editVarOrFixedToggle">
                                                    <input type="radio" id="editVariable" name="editVarOrFixed" value="Variable" required checked>
                                                    <label for="editVariable">Variable</label>
                                                    <input type="radio" id="editFixed" name="editVarOrFixed" value="Fixed">
                                                    <label for="editFixed">Fixed</label>
                                                    <span class="slider"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row" >
                                            <div class="col-sm-12">
                                                <div class="toggle-container" id="editTaxableToggle">
                                                    <input type="radio" id="editTaxable" name="editTaxOrNon" value="Taxable" required checked>
                                                    <label for="editTaxable">Taxable</label>
                                                    <input type="radio" id="editNonTax" name="editTaxOrNon" value="Non-taxable">
                                                    <label for="editNonTax">Non-taxable</label>
                                                    <span class="slider"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="container mt-4">
                                        <div class="form-group row" style="margin-top: -25px;">
                                            <div class="col-sm-5 d-flex align-items-center">
                                                 <input type="checkbox" id="saccoeditcheck" name="saccoeditcheck" value="Yes" class="form-check-input me-2">
                                                <label for="saccoeditcheck" class="form-check-label">Sacco related</label>
                                            </div>
                                            <div class="col-sm-7" id="saccoeditnames" hidden>
                                                <label>Staff List:</label>
                                            <select name="staffSelect8" id="staffSelect8" class="custom-select form-control" autocomplete="off">
                                                <option value="">Select Staff</option>
                                                
                                            </select>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                <div class="form-group-border">
                                    <legend>GL Accounts</legend>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="pjornal">
                                            <label class="form-check-label" for="pjornal">Link to Payroll Journal</label>
                                        </div>
                                    </div>
                                    <div class="form-group" >
                                        
                                        <input type="text" class="form-control" id="accountNumber" placeholder="A/C Number">
                                    </div>
                                    <div class="form-group" >
                                        <label for="cc">CC</label>
                                        <input type="text" class="form-control" id="cc" placeholder="CC">
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="form-group row" style="margin-left: 55px;">
                            <div class="col-sm-8">
                                <input class="form-check-input" type="checkbox" id="exemptionBonuses">
                                <label class="form-check-label" for="exemptionBonuses">Exemption Bonuses / Overtime and Retirements Benefits</label>
                            </div>
                        </div>
                        <div class="form-group-border" style="margin-bottom: 20px;">
                            <div class="horizontal-fields">
                                <div class="form-check">
                                    <label for="editAppearInP9" class="col-form-label">Appear in P9</label>
                                    <input type="checkbox" class="form-control" id="editAppearInP9" name="editAppearInP9">
                                </div>
                                <div class="field-group">
                                    <div class="col-sm-16">
                                        <div class="toggle-container" id="editReliefToggle">
                                            <input type="radio" id="editNone" name="editRelief" value="NONE" required checked>
                                            <label for="editNone">Not Relief</label>
                                            <input type="radio" id="editRNT" name="editRelief" value="RELIEF ON TAXABLE">
                                            <label for="editRNT">Relief taxable</label>
                                            <input type="radio" id="editRNP" name="editRelief" value="Relief on Paye">
                                            <label for="editRNP">Relief on Paye</label>
                                            <span class="slider"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-border">
                        <div class="horizontal-fields">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="calculation">
                                <label class="form-check-label" for="calculation">Calculation</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="editcalctype" id="editcumulative" value="cumulative">
                                <label class="form-check-label" for="cumulativeValue">Cumulative Value</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="editcalctype" id="editcasual" value="casual">
                                <label class="form-check-label" for="casual">Casual</label>
                            </div>
                        </div>
                        </div>
                        <div class="form-group">
                            <input type="text" id="editinputField" class="form-control" readonly placeholder="Formula Input" required>
                        </div>
                        <div class="row align-items-center" id="editloanhelper" style="display: none;">
                            <div class="col-auto">
                                <div class="form-group mb-0">
                                    <label for="rate" class="mr-2">Interest Code:</label>
                                    <input type="text" class="form-control d-inline-block" id="editinterestcode" name="interestcode" style="width: 100px;" >
                                    <label for="rate" class="mr-2">Description:</label>
                                    <input type="text" class="form-control d-inline-block" id="editinterestdesc" name="interestdesc" style="width: 150px;" >
                                </div>
                            </div>
                        </div>
                        <div id="feedback" style="color: red;"></div>
                        
                    </div>
                    <div id="prioreSection" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Drag and drop</strong> to set the deduction priority order. 
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Current Item Card -->
                                 <div class="card mb-1 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-star"></i> Current Deduction
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0" id="editItemName">Item Code</h6>
                                                <small class="text-muted" id="eItemCode">Code will appear here</small>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-primary badge-lg" id="editPriorityBadge">
                                                    Priority: <span id="editPriorityNumber">-</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <i class="fas fa-list-ol"></i> Existing Deductions (Drag to Reorder)
                                    </div>
                                    <div class="card-body p-0">
                                        <ul id="editsortableDeductions" class="list-group list-group-flush">
                                            <!-- Items will be loaded here via AJAX -->
                                             <li class="editlist-group-item text-center text-muted py-1">
                                                <i class="fas fa-spinner fa-spin"></i> Loading deductions...
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <input type="hidden" name="priority" id="editpriorityInput">
                            </div>
                            <div class="col-md-4">
                                <!-- Priority Legend -->
                                 <div class="card bg-light">
                                    <div class="card-header">
                                        <i class="fas fa-question-circle"></i> Priority Guide
                                    </div>
                                    <div class="card-body">
                                        <p class="small mb-2"><strong>How Priority Works:</strong></p>
                                        <ol class="small pl-3 mb-3">
                                            <li>Lower numbers = Higher priority</li>
                                            <li>Priority 1 is deducted first</li>
                                            <li>Drag items to change order</li>
                                            <li>Your new item shown above</li>
                                        </ol>
                                        <p class="small mb-2"><strong>Example Order:</strong></p>
                                        <div class="small">
                                            <div class="mb-1">1️⃣ Statutory</div>
                                            <div class="mb-1">2️⃣ Loans</div>
                                            <div class="mb-1">3️⃣ SACCO Contributions</div>
                                            <div class="mb-1">4️⃣ Welfare</div>
                                            <div>5️⃣ Other Deductions</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveChangesButton" class="btn btn-enhanced btn-finalize"><i class="fas fa-save"></i> Save Changes</button>
                         
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    

    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <script>
        const amanage = '{{ route("pitems.store") }}';
        const update = '{{ route("pitems.update") }}';
         const updateorder = '{{ route("payroll.deductions.update-priorities") }}';
          const loadpriori = '{{ route("payroll.deductions.priorities") }}';
    </script>
    <script src="{{ asset('js/pitems.js') }}"></script>
     
    <script>

  
    </script>
</x-custom-admin-layout>