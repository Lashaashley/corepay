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
            <th>Relief T</th>
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
            <td>{{ $row->relief }}</td>
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
                        <div class="row align-items-center" id="loanRate" style="display: none;">
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
                                <div class="form-group-border" style="margin-bottom: 20px;">
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
                                        <div class="form-group row" style="margin-top: -25px;">
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
                    <div class="form-group-border" style="margin-bottom: 20px;">
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
    
    
    <script>
    
       
        $(document).ready(function() {
    $('#payrollCodesTable').DataTable();
});
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
function initializeFormBehavior() {
  const categorySelect = document.getElementById('category');
  const balanceOptions = document.getElementById('balanceOptions');
  const loanRate = document.getElementById('loanRate'); 
  const loanhelper = document.getElementById('loanhelper');

  if (categorySelect) {
    categorySelect.addEventListener('change', function() {
      if (this.value === 'balance') {
        balanceOptions.style.display = 'block';
        loanRate.style.display = 'none';
        loanhelper.style.display = 'none';
      } else if (this.value === 'loan') {
        balanceOptions.style.display = 'none';
        loanRate.style.display = 'block';
        loanhelper.style.display = 'block';
      } else {
        balanceOptions.style.display = 'none';
        loanRate.style.display = 'none';
        loanhelper.style.display = 'none';
      }
    });
  }
}

// Call this function when the modal is opened or when the DOM is ready
document.addEventListener('DOMContentLoaded', initializeFormBehavior);

// If using Bootstrap modal, you can also use its events
// Initialize form behavior when modal is shown
$('#payrollModal').on('shown.bs.modal', initializeFormBehavior);

document.addEventListener('DOMContentLoaded', function() {
    const calculationRadio = document.getElementById('calculationRadio');
    const amountRadio = document.getElementById('amount');
    const inputField = document.getElementById('inputField');
    function toggleReadOnly() {
        if (calculationRadio.checked) {
            inputField.removeAttribute('readonly');
        } else {
            inputField.setAttribute('readonly', true);
        }
    }
    toggleReadOnly();
    calculationRadio.addEventListener('change', toggleReadOnly);
    amountRadio.addEventListener('change', toggleReadOnly);

    const inputField2 = document.getElementById('inputField');
    const feedback = document.getElementById('feedback');
    // Function to validate the code with the database
    function checkCode(code) {
        $.ajax({
            type: 'POST',
            url: '../admin/chcode',
            data: { code: code },
            success: function(response) {
                try {
                    var jsonResponse = JSON.parse(response); 
                    if (jsonResponse.exists) {
                        feedback.innerHTML = '';
                    } else {
                        feedback.innerHTML = `Code ${code} is invalid.`;
                    }
                } catch (e) {
                    console.error("Error parsing response:", e);
                    feedback.innerHTML = 'Error in response from server.';
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    }
    // Event listener for keydown on the input field
    inputField2.addEventListener('keydown', function(event) {
        const terminalSymbols = ['+', '/', '*', '-', '=']; // Add more terminals if needed
        const key = event.key;
        // If the key is a terminal symbol, validate the preceding code
        if (terminalSymbols.includes(key)) {
            const formula = inputField2.value;
            const codes = formula.match(/[A-Za-z]+\d+/g); // Matches codes like 'D998', 'C67', etc.
            if (codes && codes.length > 0) {
                const lastCode = codes[codes.length - 1];
                checkCode(lastCode); // Check the last entered code
            }
        }
    });
    const cumulativeValueCheckbox = document.getElementById('cumulativeValue');
    const casualCheckbox = document.getElementById('casual');

    // Function to handle the checkbox state
    function handleCheckboxChange(checkedCheckbox) {
        if (checkedCheckbox === cumulativeValueCheckbox && cumulativeValueCheckbox.checked) {
            casualCheckbox.checked = false;
        } else if (checkedCheckbox === casualCheckbox && casualCheckbox.checked) {
            cumulativeValueCheckbox.checked = false;
        }
    }

    // Event listeners for the checkboxes
    cumulativeValueCheckbox.addEventListener('change', function() {
        handleCheckboxChange(cumulativeValueCheckbox);
    });

    casualCheckbox.addEventListener('change', function() {
        handleCheckboxChange(casualCheckbox);
    }); 

    $('#payrollForm').on('submit', function(e) {
        e.preventDefault();
    
    
    if (!validateFormFields()) {
      
        return; // If validation fails, stop further execution
    }
    
    
    var formData = $('#payrollForm').serialize();
    
    const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Adding...').prop('disabled', true);
    $.ajax({
    type: 'POST',
     url: "{{ route('pitems.store') }}",
    data: formData,
    dataType: 'json', // Add this line - jQuery will auto-parse JSON
    success: function(response) {
        // Now 'response' is already parsed, no need for JSON.parse()
        if (response.status === 'success') {
            showMessage('Payroll Item added successfully!', false);
            $('#payrollForm')[0].reset();
            $('#sacconames').attr('hidden', true);
            $('#staffSelect7').removeAttr('required').val('');
        } else if (response.status === 'duplicate') {
            showMessage(response.message, true);
        } else {
            showMessage('Form submission failed: ' + response.message, true);
        }
    },
    error: function(xhr, status, error) {
        console.error('XHR:', xhr.responseText); // See actual error
        showMessage('Form submission failed: ' + error, true);
    },
    complete: function() {
        submitBtn.html(originalText).prop('disabled', false);
    }
});
});

});
$(document).ready(function(){
$('#saccocheck').on('change', function () {
    if ($(this).is(':checked')) {
        $('#sacconames').val('Yes');       
        // Show the vehicle reg no field
        $('#sacconames').removeAttr('hidden');
        $('#staffSelect7').attr('required', true);
        
    } else {
        // Hide the vehicle reg no field
        $('#sacconames').attr('hidden', true);
        $('#staffSelect7').removeAttr('required');
        $('#sacconames').val('No');
        $('#staffSelect7').val('').trigger('change');
    }
});
$('#saccoeditcheck').on('change', function () {
    if ($(this).is(':checked')) {
        $('#saccoeditcheck').val('Yes');  
        // Show the vehicle reg no field
        $('#saccoeditnames').removeAttr('hidden');
        $('#staffSelect8').attr('required', true);
        
    } else {
        // Hide the vehicle reg no field
        $('#saccoeditnames').attr('hidden', true);
        $('#staffSelect8').removeAttr('required');
        $('#saccoeditcheck').val('No');
        $('#staffSelect8').val('').trigger('change');
    }
});
 $.ajax({ 
            url: '../admin/summaris', // The PHP file that will handle the query and return data
            type: 'GET',
            dataType: 'json', // Expect JSON response
            success: function(data) { 
                if (data.error) {
                    console.error("Error: " + data.error);
                } else {
                    $('#staffSelect7').html(data.snameOptions);
                     $('#staffSelect8').html(data.snameOptions);
                    $('#staffSelect7').select2({
                        placeholder: "search",
                        allowClear: true,
                        width: '100%'
                    });
                     $('#staffSelect8').select2({
                        placeholder: "search",
                        allowClear: true,
                        width: '100%'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error); // Log any errors
            }
        });
 });
// Validate form fields
function validateFormFields() {
    let isValid = true; // Variable to track form validity

    // Check for required fields
    $('#payrollForm').find('input, select').each(function() {
        const isReadOnly = $(this).prop('readonly');
        
        // If the field is not readonly and required, validate it
        if (!isReadOnly && $(this).prop('required') && $(this).val().trim() === '') {
            $(this).addClass('is-invalid'); // Add invalid class for styling
            isValid = false;
        } else {
            $(this).removeClass('is-invalid'); // Remove invalid class if valid
        }
    });

    // Check for specific input validation (like numeric fields)
    if (!validateNumericFields()) {
        isValid = false;
    }

    return isValid;
}

// Validate numeric fields
function validateNumericFields() {
    let isNumericValid = true;
    
    // Example: Check if 'rate' is a valid number if displayed and not readonly
    const rateField = $('#rate');
    if (rateField.is(':visible') && !rateField.prop('readonly') && isNaN(rateField.val())) {
        rateField.addClass('is-invalid');
        isNumericValid = false;
    } else {
        rateField.removeClass('is-invalid');
    }
    
    // Check if recintres is 0, and if so, validate interestcode and interestdesc fields
    
        const loanhelperDiv = $('#loanhelper');
        
        if (loanhelperDiv.is(':visible')) {
            const recintresValue = $('input[name="recintres"]').val();
    const recintresValueByID = $('#separate').val();
    const recintresRadioValue = $('input[name="recintres"]:checked').val();
    
    console.log("recintresValue by name:", recintresValue);
    console.log("recintresValue by ID:", recintresValueByID);
    console.log("recintresValue by radio checked:", recintresRadioValue);
    
    // Check if any of these are "0"
    if (recintresValue === '0' || recintresValueByID === '0' || recintresRadioValue === '0') {
        console.log("Detected recintres = 0, validating interest fields");
        
        const interestcodeField = $('#interestcode');
        const interestdescField = $('#interestdesc');
        
        // Check if these fields exist
        console.log("interestcode field exists:", interestcodeField.length > 0);
        console.log("interestdesc field exists:", interestdescField.length > 0);
        
        // Dynamically set these fields as required
        interestcodeField.prop('required', true);
        interestdescField.prop('required', true);
        
        // Validate interestcode
        console.log("interestcode value:", interestcodeField.val());
        if (!interestcodeField.val() || !interestcodeField.val().trim()) {
            interestcodeField.addClass('is-invalid');
            isNumericValid = false;
            console.log("interestcode invalid");
        } else {
            interestcodeField.removeClass('is-invalid');
        }
        
        // Validate interestdesc
        console.log("interestdesc value:", interestdescField.val());
        if (!interestdescField.val() || !interestdescField.val().trim()) {
            interestdescField.addClass('is-invalid');
            isNumericValid = false;
            console.log("interestdesc invalid");
        } else {
            interestdescField.removeClass('is-invalid');
        }
    }
        } else {
        // If recintres is not 0, remove the required attribute
        $('#interestcode').prop('required', false);
        $('#interestdesc').prop('required', false);
        console.log("recintres is not 0, no validation needed for interest fields");
    }
    
    
    return isNumericValid;
}



// Clear form fields after successful submission
function clearFormFields() {
    $('#payrollForm')[0].reset();
    $('.required').removeClass('is-invalid');
    $('.help-block').hide();
}

// Event listeners
$('#payrollModal').on('hidden.bs.modal', function () {
    clearFormFields();
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

$(document).ready(function() {
    $('#payrollCodesTable').DataTable();
});
function openEditModal(element) {
    const row = $(element).closest('tr');
    const id = row.find('td:eq(0)').text();
    const code = row.find('td:eq(1)').text();
    const description = row.find('td:eq(2)').text();
    const processType = row.find('td:eq(3)').text();
    const varorfixed = row.find('td:eq(4)').text();
    const taxornontax = row.find('td:eq(5)').text();
    const category = row.find('td:eq(6)').text();
    const relief = row.find('td:eq(7)').text();
    const prossty = row.find('td:eq(8)').text();
    const rate = row.find('td:eq(9)').text();
    const incredu = row.find('td:eq(10)').text();
    const recintres = row.find('td:eq(11)').text();
    const formularinpu = row.find('td:eq(12)').text();
    const cumcas = row.find('td:eq(13)').text();
    const intrestcode = row.find('td:eq(14)').text();
    const codename = row.find('td:eq(15)').text();
    const issaccorel = row.find('td:eq(16)').text();
    const sposter = row.find('td:eq(17)').text();
    $('#editCode').val(code);
    $('#editid').val(id);
    $('#editDescription').val(description);
    if (processType === 'Amount') {
        $('#editAmount').prop('checked', true);
        $('#editinputField').prop('readonly', true);
    } else {
        $('#editCalculation').prop('checked', true);
        $('#editinputField').prop('readonly', false);
    }
    if (cumcas === 'cumulative') {
        $('#editcumulative').prop('checked', true);
    } else if (cumcas === 'casual') {
        $('#editcasual').prop('checked', true);
    }else{
        $('#editcumulative').prop('checked', false);
        $('#editcasual').prop('checked', false);
    }

    $('#editinputField').val(formularinpu);
    $('#editCategory').val(category);
    
    $('#editProcessSty').val(prossty);
    if (category === 'balance') {
        $('#editBalanceOptions').show();
        if (incredu === 'Increasing') {
        $('#editIncreasing').prop('checked', true);
    } else {
        $('#editReducing').prop('checked', true);
    }
    } else {
        $('#editBalanceOptions').hide();
    }
    if (category === 'loan') {
        $('#editLoanRate').show();
        $('#editloanhelper').show();
        $('#editRate').val(rate);
        $('#editinterestcode').val(intrestcode);
        $('#editinterestdesc').val(codename);
        recintToggle(recintres);
        $('#recint-toggleedit input[type="radio"]').on('change', function() {
            recintToggle($(this).val());
        });
    } else {
        $('#editLoanRate').hide();
        $('#editloanhelper').hide();
    }

    setTimeout(function() {
        if (issaccorel === 'Yes') {
            $('#saccoeditcheck').prop('checked', true);
            $('#saccoeditnames').removeAttr('hidden');
            $('#staffSelect8').attr('required', true);
            $('#staffSelect8').val(sposter).trigger('change');
        } else {
            $('#saccoeditcheck').prop('checked', false);
            $('#saccoeditnames').attr('hidden', true);
             $('#saccoeditcheck').val('No');
            $('#staffSelect8').removeAttr('required');
            $('#staffSelect8').val('').trigger('change');
        }
    }, 200);
    updateReliefToggle(relief);
    $('#editReliefToggle input[type="radio"]').on('change', function() {
        updateReliefToggle($(this).val());
    });
    updatetaxableToggle(taxornontax);
    $('#editTaxableToggle input[type="radio"]').on('change', function() {
        updatetaxableToggle($(this).val());
    });
    updatevarfixToggle(varorfixed);
    $('#editVarOrFixedToggle input[type="radio"]').on('change', function() {
        updatevarfixToggle($(this).val());
    });
    $('#editpitemsModal').modal('show');
}
function updatevarfixToggle(varorfixed) {
    const slider = $('#editVarOrFixedToggle .slider');
    let transform, backgroundColor;

    switch(varorfixed) {
        case 'Variable':
            $('#editVariable').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3498db';
            break;
        case 'Fixed':
            $('#editFixed').prop('checked', true);
            transform = 'translateX(100px)';
            backgroundColor = '#2ecc71';
            break;
        default:
            $('#editVariable').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3498db';
    }

    slider.css({ transform, backgroundColor });

    // Update label colors
    $('#editVarOrFixedToggle label').css('color', function() {
        return $(this).prev('input').is(':checked') ? '#fff' : '#333';
    });
}
function recintToggle(recintres) {
    const slider = $('#recint-toggleedit .slider');
    let transform, backgroundColor;

    switch(recintres) {
        case '1':
            $('#recintredit').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3d0678';
            break;
        case '0':
            $('#separatedit').prop('checked', true);
            transform = 'translateX(100px)';
            backgroundColor = '#fa2007';
            break;
        default:
            $('#recintredit').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3d0678';
    }

    slider.css({ transform, backgroundColor });

    // Update label colors
    $('#recint-toggleedit label').css('color', function() {
        return $(this).prev('input').is(':checked') ? '#fff' : '#333';
    });
}
function updatetaxableToggle(taxornontax) {
    const slider = $('#editTaxableToggle .slider');
    let transform, backgroundColor;

    switch(taxornontax) {
        case 'Taxable':
            $('#editTaxable').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#e74c3c';
            break;
        case 'Non-taxable':
            $('#editNonTax').prop('checked', true);
            transform = 'translateX(100px)';
            backgroundColor = '#f39c12';
            break;
        default:
            $('#editNonTax').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#f39c12';
    }

    slider.css({ transform, backgroundColor });

    // Update label colors
    $('#editTaxableToggle label').css('color', function() {
        return $(this).prev('input').is(':checked') ? '#fff' : '#333';
    });
}
function updateReliefToggle(relief) {
    const slider = $('#editReliefToggle .slider');
    let transform, backgroundColor;

    switch(relief) {
        case 'NONE':
            $('#editNone').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3498db';
            break;
        case 'RELIEF ON TAXABLE':
            $('#editRNT').prop('checked', true);
            transform = 'translateX(100px)';
            backgroundColor = '#2ecc71';
            break;
        case 'Relief on Paye':
            $('#editRNP').prop('checked', true);
            transform = 'translateX(200px)';
            backgroundColor = '#e74c3c';
            break;
        default:
            $('#editNone').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3498db';
    }

    slider.css({ transform, backgroundColor });

    // Update label colors
    $('#editReliefToggle label').css('color', function() {
        return $(this).prev('input').is(':checked') ? '#fff' : '#333';
    });
}

        // Handle Category Change to Show/Hide Balance and Loan Fields
        $('#editCategory').on('change', function() {
            const selectedCategory = $(this).val();

            if (selectedCategory === 'balance') {
                $('#editBalanceOptions').slideDown();
                $('#editLoanRate').slideUp();
                $('#editloanhelper').slideUp();
            } else if (selectedCategory === 'loan') {
                $('#editBalanceOptions').slideUp();
                $('#editLoanRate').slideDown();
                $('#editloanhelper').slideDown();
            } else {
                $('#editBalanceOptions').slideUp();
                $('#editLoanRate').slideUp();
                $('#editloanhelper').slideUp();
            }
        });

        function updateToggle(toggleId, firstOptionId, firstColor, secondColor) {
    $(`#${toggleId} input[type="radio"]`).on('change', function() {
        const slider = $(`#${toggleId} .slider`);
        const isFirstOption = $(`#${firstOptionId}`).is(':checked');
        
        slider.css({
            'transform': isFirstOption ? 'translateX(0)' : 'translateX(100px)',
            'background-color': isFirstOption ? firstColor : secondColor
        });

        $(`#${toggleId} label`).css('color', function() {
            return $(this).prev('input').is(':checked') ? '#fff' : '#333';
        });
    });
}
$(document).ready(function() {
    // Attach the click event to the button with id
    $('#saveChangesButton').on('click', function() {
        submitEditForm(); // Call the function to handle AJAX submission
    });
});

function submitEditForm() {
    // Gather form data
    const formData = {
    id: $('#editid').val(),
    code: $('#editCode').val(),
    cname: $('#editDescription').val(),
    formula: $('#editinputField').val(),
    procctype: $('input[name="editProcessType"]:checked').val(),
    cumcas: $('input[name="editcalctype"]:checked').val(),
    varorfixed: $('input[name="editVarOrFixed"]:checked').val(),
    taxaornon: $('input[name="editTaxOrNon"]:checked').val(),
    category: $('#editCategory').val(),
    increREDU: $('#editCategory').val() === 'balance' ? $('input[name="editBalanceType"]:checked').val() : null,
    rate: $('#editCategory').val() === 'loan' ? $('#editRate').val() : null, 
    intrestcode: $('#editCategory').val() === 'loan' ? $('#editinterestcode').val() : null,
    codename: $('#editCategory').val() === 'loan' ? $('#editinterestdesc').val() : null,
    recintres: $('#editCategory').val() === 'loan' ? $('input[name="editrecintres"]:checked').val() : null, // Added condition
    prossty: $('#editProcessSty').val(),
    relief: $('input[name="editRelief"]:checked').val(),
    saccocheck :  $('#saccoeditcheck').val(),
    poster : $('#staffSelect8').val()
};


    

    $.ajax({
        url: "{{ route('pitems.update') }}",
        type: 'POST',
        data: formData,
        dataType: 'json', // Expected response format
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            
            if (response.success) {
                showMessage('Payroll code updated successfully!', false);
                // Optionally close the modal or perform other UI updates
                $('#editPayrollModal').modal('hide'); // Close modal
                // Refresh table data
                updateTableRow(formData);
            } else {
                alert('Error updating payroll code: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            alert('An error occurred while updating the payroll code. ' + error);
        }
    });
}

function updateTableRow(formData) {
    // Locate and update the table row with matching ID
    const row = $('#payrollCodesTable tbody tr').filter(function() {
        return $(this).find('td:first').text() === formData.id;
    });

    // Update each column with new form data
    row.find('td:eq(1)').text(formData.code);
    row.find('td:eq(2)').text(formData.cname);
    row.find('td:eq(3)').text(formData.procctype);
    row.find('td:eq(4)').text(formData.varorfixed);
    row.find('td:eq(5)').text(formData.taxaornon);
    row.find('td:eq(6)').text(formData.category);
    row.find('td:eq(7)').text(formData.relief);
    row.find('td:eq(8)').text(formData.prossty);
    row.find('td:eq(9)').text(formData.rate);
    row.find('td:eq(10)').text(formData.increREDU);
    row.find('td:eq(11)').text(formData.recintres);
    row.find('td:eq(12)').text(formData.formula);
    row.find('td:eq(13)').text(formData.cumcas);
    row.find('td:eq(14)').text(formData.intrestcode);
    row.find('td:eq(15)').text(formData.codename);
    row.find('td:eq(16)').text(formData.saccocheck);
    row.find('td:eq(17)').text(formData.poster);
}

function deletePayrollCode(element) {
    const id = $(element).data('id');
    const code = $(element).data('code');
    swal({
        title: 'Are you sure?',
        text: `Are you sure you want to delete the payroll item "${code}"? Some Staff may be Currently Assigned to It.`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.value) {
            // Perform the AJAX request for deletion
            $.ajax({
                url: '../admin/delete', // URL to your server-side script
                type: 'POST',
                data: {
                    action: 'ptypes', // Action parameter to indicate deletion
                    id: id,
                    code: code
                },
                dataType: 'json', // Expecting JSON response
                success: function(response) {
                    if (response.success) {
                        // Show success SweetAlert
                        swal({
                            title: 'Deleted!',
                            text: 'Payroll Item deleted!',
                            icon: 'success',
                            buttons: false,
                            timer: 2000
                        });

                        // Optionally reload data or refresh the table
                         $(element).closest('tr').remove(); // Uncomment if needed
                    } else {
                        // Show error SweetAlert
                        swal({
                            title: 'Error!',
                            text: response.message || 'Failed to delete, Try again later.',
                            icon: 'error',
                            buttons: true
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error); // Log any AJAX error
                    swal({
                        title: 'Failed!',
                        text: 'An error occurred while deleting the leave type. Please try again.',
                        icon: 'error',
                        buttons: true
                    });
                }
            });
        }
    });
}
      
    </script>
</x-custom-admin-layout>