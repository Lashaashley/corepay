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
form {
    font-size: 14px;
}
.form-group .row {
    margin-bottom: 5px;
}
label {
    margin-bottom: 2px;
}
.form-control {
    padding: 4px 8px;
    height: auto;
}
.form-check {
    margin-bottom: 3px;
}
.form-group-border {
    border: 2px solid #ced4da;
            border-radius: 0.25rem;
            padding: 10px;
            position: relative;
            margin-top: 20px;
            margin-bottom: 15px;
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
        .form-group {
            margin-bottom: 5px;
        }
        .form-check-input, .form-control {
            margin-bottom: 5px;
        }
        .horizontal-fields .field-group {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }
.checkbox-group {
  display: flex;
  align-items: center;
}

.checkbox-group input[type="checkbox"] {
  margin-right: 5px;
}

.form-actions {
  text-align: right;
  margin-top: 20px;
}
.horizontal-fields {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    margin-bottom: 5px;
}

.field-group {
    flex: 1;
    min-width: 200px;
    margin-right: 15px;
    margin-bottom: 5px;
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
.form-check-input {
    margin-top: 2px;
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
.modal-dialog {
        max-width: 800px; /* Adjust this value as needed */
    }

    /* Reduce vertical spacing */
.form-group {
        margin-bottom: 0.5rem;
    }

.container {
        margin-top: 0.5rem !important;
    }

legend {
        margin-bottom: 0.5rem;
    }

    /* Adjust border and padding for form groups */
.form-group-border {
        border: 1px solid #ced4da;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .content-table tbody tr.highlight {
    background-color: lightgreen;
}
.content-table-container {
    overflow-x: auto; /* Enable horizontal scrolling */
    white-space: nowrap; /* Prevent wrapping of table content */
}
.content-table{
	border-collapse: collapse;
	margin: 25px auto;
	font-size: 1.4rem;
	min-width: 200px;
	border-radius: 5px 5px 0 0;
	overflow: hidden;
	box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
	cursor: pointer;
	

}
.content-table thead tr{
	background-color: deepskyblue;
	text-align: left;
	font-weight: bold;
	font-size: 1.0rem;
}
.content-table th,
.content-table td{
	padding: 3px 5px;
}
.content-table tbody tr{
	border-bottom: 1px solid #ffffff;
	font-size: 1.0rem;
}
.content-table tbody tr:nth-of-type(even){
	background-color: #f3f3f3;
}
.content-table tbody tr:last-of-type{
	border-bottom: 2px solid deepskyblue;
}
.content-table tbody tr.highlight {
    background-color: lightgreen;
}
/* Modal styling */
#addpensionGroupModal .modal-dialog,
#addunionGroupModal .modal-dialog,
#addwhGroupModal .modal-dialog {
    max-width: 400px;
    width: 90%;
}
#addinsuranceGroupModal .modal-dialog {
    max-width: 400px;
    width: 90%;
}

#HlevyModal .modal-content,
#shifModal .modal-content,
#payeModal .modal-content,
#nssfModal .modal-content,
#nhifModal .modal-content,
#pensionModal .modal-content,
#addpensionGroupModal .modal-content {
    background-color: #f8f9fa; /* Light gray background */
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
#addpensionGroupModal .modal-header,
#addunionGroupModal .modal-header,
#addwhGroupModal .modal-header {
    background-color: #007bff; /* Blue header */
    color: white;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

#addpensionGroupModal .modal-title,
#addunionGroupModal .modal-title,
#addwhGroupModal .modal-title{
    font-weight: bold;
}

#addpensionGroupModal .modal-body,
#addunionGroupModal .modal-body,
#addwhGroupModal .modal-body {
    padding: 20px;
}

#addpensionGroupModal .form-group label,
#addunionGroupModal .form-group label,
#addwhGroupModal .form-group label {
    font-weight: bold;
    color: #333;
}

#addpensionGroupModal .custom-select,
#addunionGroupModal .custom-select,
#addwhGroupModal .custom-select {
    border: 1px solid #ced4da;
    border-radius: 5px;
}

#addpensionGroupModal .content-table,
#addunionGroupModal .content-table,
#addwhGroupModal .content-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

#addpensionGroupModal .content-table th,
#addpensionGroupModal .content-table td {
    border: 1px solid #dee2e6;
    padding: 10px;
    text-align: left;
}

#addpensionGroupModal .content-table thead,
#addunionGroupModal .content-table thead,
#addwhGroupModal .content-table thead {
    background-color: #e9ecef;
}

#addpensionGroupModal .btn,
#addunionGroupModal .btn,
#addwhGroupModal .btn {
    border-radius: 5px;
}

#addpensionGroupModal .btn-primary,
#addunionGroupModal .btn-primary,
#addwhGroupModal .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

#addpensionGroupModal .btn-danger,
#addunionGroupModal .btn-danger,
#addwhGroupModal .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

#addpensionGroupModal .btn-secondary, 
#addunionGroupModal .btn-secondary,
#addwhGroupModal .btn-secondary{
    background-color: #6c757d;
    border-color: #6c757d;
}
.toggle-container#relief-houz,
.toggle-container#relief-pen,
.toggle-container#relief-nssf, 
.toggle-container#relief-nhif,
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
#relief-houz input,
#relief-pen input,
#relief-nssf input,
#relief-nhif input,
#relief-toggle input {
  display: none;
}
#relief-houz label,
#relief-pen label,
#relief-nssf label,
#relief-nhif label,
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
#relief-houz .slider,
#relief-pen .slider,
#relief-nssf .slider,
#relief-nhif .slider,
#relief-toggle .slider {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 98px;
  height: 30px;
  border-radius: 30px;
  transition: transform 0.3s, background-color 0.3s;
}

#relief-houz #houznone:checked ~ .slider,
#relief-pen #pennone:checked ~ .slider,
#relief-nssf #nssfnone:checked ~ .slider,
#relief-nhif #nhifnone:checked ~ .slider,
#relief-toggle #none:checked ~ .slider {
  transform: translateX(0);
  background-color: #3498db;
}

#relief-houz #houzrnt:checked ~ .slider,
#relief-pen #penrnt:checked ~ .slider,
#relief-nssf #nssfrnt:checked ~ .slider,
#relief-nhif #nhifrnt:checked ~ .slider,
#relief-toggle #rnt:checked ~ .slider {
  transform: translateX(100px);
  background-color: #2ecc71;
}

#relief-houz #houzrnp:checked ~ .slider,
#relief-pen #penrnp:checked ~ .slider,
#relief-nssf #nssfrnp:checked ~ .slider,
#relief-nhif #nhifrnp:checked ~ .slider,
#relief-toggle #rnp:checked ~ .slider {
  transform: translateX(200px);
  background-color: #e74c3c;
}
#relief-houz input:checked + label,
#relief-pen input:checked + label,
#relief-nssf input:checked + label,
#relief-nhif input:checked + label,
#relief-toggle input:checked + label {
  color: #fff;
}

#relief-houz  input:not(:checked) + label,
#relief-pen  input:not(:checked) + label,
#relief-nssf  input:not(:checked) + label,
#relief-nhif  input:not(:checked) + label,
#relief-toggle input:not(:checked) + label {
  color: #333;
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
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        } 
        @keyframes slideIn {
    from { right: -100px; opacity: 0; }
    to { right: 20px; opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
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
    <div class="min-height-200px">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>Payroll</h4>
							</div>
							
						</div>
					</div>
				</div>
                <div class="tab-container" style="margin-top: -20px;">
                    <button class="tab-button active" onclick="openTab(event, 'deductions')">Deductions</button>
                    <button class="tab-button" onclick="openTab(event, 'relief')" disabled>Reliefs</button>
                    
                </div>
                <div id="deductions" class="tab-content active" style="margin-top: -20px;">
                    <div class="pd-20 card-box mb-30">
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4 class="text-blue h4">Deductions</h4>
                            </div>
                        </div>
                        <div class="btn-group mt-3">
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#nhifModal" disabled>NHIF</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#shifModal" disabled>SHIF</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#nssfModal" disabled>NSSF</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#pensionModal" disabled>Pension</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#payeModal" disabled>PAYE Rates</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#withholding">Withholding</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#HlevyModal" disabled>Housing Levy</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#unionmodal" disabled>Union </button>
                        </div>
                    </div>
                </div>
                <div id="relief" class="tab-content" style="margin-top: -20px;">
                    <div class="pd-20 card-box mb-30">
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4 class="text-blue h4">Reliefs</h4>
                                <p class="mb-20"></p>
                            </div>
                        </div>
                        <div class="btn-group mt-3">
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#preliefModal">Personal Relief</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#IreliefModal">Insurance Relief</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#DefinedModal">Defined Relief</button>
                            
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    <div class="modal fade" id="nhifModal" tabindex="-1" role="dialog" aria-labelledby="nhifModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nhifModalLabel">NHIF Rates Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="nhifForm">
                        <input type="hidden" name="formType" value="nhif">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="lowerlimitamount">Lower Limit Amount</label>
                                <input type="text" class="form-control" id="lowerlimitamount" name="lowerlimitamount" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="upperlimitamount">Upper Limit Amount</label>
                                <input type="text" class="form-control" id="upperlimitamount" name="upperlimitamount" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="amountcharged">Amount Charged</label>
                                <input type="text" class="form-control" id="amountcharged" name="amountcharged" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="code">Code</label>
                                <input type="text" class="form-control" id="nhifcode" name="nhifcode" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <button type="submit" class="btn btn-primary">Add Entry</button>
                            <div class="field-group">
                                <div class="col-sm-6">
                                    <div class="toggle-container" id="relief-nhif">
                                        <input type="radio" id="nhifnone" name="nhifrelief" value="NONE" required checked>
                                        <label for="nhifnone">Not Relief</label>
                                        <input type="radio" id="nhifrnt" name="nhifrelief" value="RELIEF ON TAXABLE">
                                        <label for="nhifrnt">Relief taxable</label>
                                        <input type="radio" id="nhifrnp" name="nhifrelief" value="Relief on Paye">
                                        <label for="nhifrnp">Relief on Paye</label>
                                        <span class="slider"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="mt-4">
                        <h6>Existing Entries</h6>
                        <div class="pb-20" id="nhifbrack-container">

                        </div>
                        <div id="nhifbrack-pagination-links">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="saveall" class="btn btn-primary">Save All</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="shifModal" tabindex="-1" role="dialog" aria-labelledby="shifModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shifModalLabel">SHIF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form id="shifForm">
                <input type="hidden" name="formType" value="shif">
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Description</label>
                                                <input type="text" class="form-control" id="description" name="description">
                                                <input type="text" class="form-control" id="ID" name="ID" hidden>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Code</label>
                                                <input type="text" class="form-control" id="code" name="code" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>BALANCES</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="cummulativeB">
                                                    <label class="form-check-label" for="pjornal">Cumulative Balance</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <select class="form-control" id="balances">
                                                    <option>Employee</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>SHIF Rates</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Employee Percentage</label>
                                                <input type="text" class="form-control" id="employeePercentage" name="employeePercentage">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label for="maxContribution">Minimum Contribution</label>
                                                <input type="text" class="form-control" id="minContribution" name="minContribution">
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
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="pjornal">
                                                <label class="form-check-label" for="pjornal">Include employer's contribution</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-border">
                            <div class="horizontal-fields">
                                <!---<div class="field-group">
                                    <label for="percentageTaxed" class="col-form-label">Rounding</label>
                                    <input type="text" class="form-control" id="rounding">
                                </div>
                                <div class="field-group">
                                    <label for="taxGrossUp" class="col-form-label">Tax Relief</label>
                                    <input type="text" class="form-control" id="taxrelief">
                                </div>
                                <div class="field-group">
                                    <label for="sortCode" class="col-form-label">Sort Code</label>
                                    <input type="text" class="form-control" id="sortCode" placeholder="0">
                                </div>--->
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
                        <div class="horizontal-fields">
                            <div class="field-group">
                                <label for="employerContributionCode">Voluntary contribution code</label>
                                <input type="text" class="form-control" id="employerContributionCode" value="D34C">
                            </div>
                            <div class="field-group">
                                <label for="voluntaryNSSFContribution">Employer Contribution Code</label>
                                <input type="text" class="form-control" id="voluntaryNSSFContribution" value="D04">
                            </div>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-enhanced btn-finalize" >
                    <i class="fas fa-check-circle"></i>
                    Update
                </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="nssfModal" tabindex="-1" role="dialog" aria-labelledby="nssfModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nssfModalLabel">Reserved NSSF - Changes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="nssfForm">
                    <input type="hidden" name="formType" value="nssf">
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Description</label>
                                                <input type="text" class="form-control" id="description1" name="description1" value="">
                                                <input type="text" class="form-control" id="ID1" name="ID1" hidden>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Code</label>
                                                <input type="text" class="form-control" id="code1" name="code1" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>BALANCES</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="cummulativeB">
                                                    <label class="form-check-label" for="pjornal">Cumulative Balance</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <select class="form-control" id="balances">
                                                    <option>Employee</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>NSSF Rates</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Employee Percentage</label>
                                                <input type="text" class="form-control" id="emppercentage" name="emppercentage" value="6">
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Employer Percentage</label>
                                                <input type="text" class="form-control" id="emplopercentage" name="emplopercentage" value="6">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <label for="maxContribution">Maximum Contribution</label>
                                                <input type="text" class="form-control" id="maxcont" name="maxcont">
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
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="pjornal">
                                                <label class="form-check-label" for="pjornal">Include employer's contribution</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="glAccount">Distribute Payroll Journal By:</label>
                                            <select class="form-control" id="glAccount">
                                                <option>Division</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-border">
                            <div class="horizontal-fields">
                                <div class="field-group">
                                    <label for="LEL" class="col-form-label">Lower Earn Limit:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="LEL" name="LEL">
                                    </div>
                                
                                </div>
                                <div class="field-group">
                                    <label for="taxGrossUp" class="col-form-label">Upper Earn Limit</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="UEL" name="UEL">
                                    </div>
                                </div>
                                <div class="field-group">
                                    <label for="employerContributionCode">Employer code</label>
                                    <div class="col-sm-6">
                                    <input type="text" class="form-control" id="employerContributionCode" value="D34C">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="horizontal-fields">
                            
                            <div class="field-group">
                                    <div class="col-sm-8">
                                        <div class="toggle-container" id="relief-nssf">
                                            <input type="radio" id="nssfnone" name="reliefnssf" value="NONE" required checked>
                                            <label for="nssfnone">Not Relief</label>
                                            <input type="radio" id="nssfrnt" name="reliefnssf" value="RELIEF ON TAXABLE">
                                            <label for="nssfrnt">Relief taxable</label>
                                            <input type="radio" id="nssfrnp" name="reliefnssf" value="Relief on Paye">
                                            <label for="nssfrnp">Relief on Paye</label>
                                            <span class="slider"></span>
                                        </div>
                                    </div>
                                </div>
                            <div class="field-group">
                                <label for="voluntaryNSSFContribution">Voluntary NSSF Contribution</label>
                                <input type="text" class="form-control" id="voluntaryNSSF" value="" readonly>
                            </div>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="pensionModal" tabindex="-1" role="dialog" aria-labelledby="pensionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pensionModalLabel">PENSION</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form id="pensionForm">
                <input type="hidden" name="formType" value="pension">
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Description</label>
                                                <input type="text" class="form-control" id="cname" name="cname" value="">
                                                <input type="text" class="form-control" id="ID2" name="ID2" hidden>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Code</label>
                                                <input type="text" class="form-control" id="code2" name="code2" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>BALANCES</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="cummulativeB">
                                                    <label class="form-check-label" for="pjornal">Cumulative Balance</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <select class="form-control" id="balances">
                                                    <option>Employee</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>Pension Rates</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Employee Percentage</label>
                                                <input type="text" class="form-control" id="emppercentage2" name="emppercentage2" value="">
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Employer Percentage</label>
                                                <input type="text" class="form-control" id="emplopercentage2" name="emplopercentage2" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label for="maxContribution">Maximum Tax Relief</label>
                                                <input type="text" class="form-control" id="maxcont2" name="maxcont2" value="">
                                            </div>
                                            <div class="col-sm-6">
                                                <input class="form-check-input" type="checkbox" id="etaxed">
                                                <label class="form-check-label" for="pjornal">Excess taxed</label>
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
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="pjornal">
                                                <label class="form-check-label" for="pjornal">Include employer's contribution</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group-border">
                            <div class="horizontal-fields">
                                <div class="field-group">
                                    
                                    <button type="button" id="pensioncodes" class="btn btn-sm btn-info" data-toggle="modal" data-target="#addpensionGroupModal">Code(s) Selection</button>
                                </div>
                                <div class="field-group">
                                    <div class="col-sm-16">
                                        <div class="toggle-container" id="relief-pen">
                                            <input type="radio" id="pennone" name="penrelief" value="NONE" required checked>
                                            <label for="pennone">Not Relief</label>
                                            <input type="radio" id="penrnt" name="penrelief" value="RELIEF ON TAXABLE">
                                            <label for="penrnt">Relief taxable</label>
                                            <input type="radio" id="penrnp" name="penrelief" value="Relief on Paye">
                                            <label for="penrnp">Relief on Paye</label>
                                            <span class="slider"></span>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="horizontal-fields">
                            <div class="field-group">
                                <label for="employerContributionCode">Voluntary contribution code</label>
                                <input type="text" class="form-control" id="employerContributionCode" value="D34C">
                            </div>
                            <div class="field-group">
                                <label for="voluntaryNSSFContribution">Employer Contribution Code</label>
                                <input type="text" class="form-control" id="voluntaryNSSFContribution" value="D04">
                            </div>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" >Update</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="withholding" tabindex="-1" role="dialog" aria-labelledby="shifModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="whModalLabel">Withholding Tax</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form id="withholdingForm">
                    @csrf
                <input type="hidden" name="formType" value="withholding">
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Description</label>
                                                <input type="text" class="form-control" id="cnamewh" name="cnamewh">
                                                <input type="text" class="form-control" id="IDwh" name="IDwh" hidden>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Code</label>
                                                <input type="text" class="form-control" id="codewh" name="codewh">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>BALANCES</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="cummulativeB">
                                                    <label class="form-check-label" for="pjornal">Cumulative Balance</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <select class="form-control" id="balances">
                                                    <option>Employee</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>Withholding Tax %</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Employee Percentage</label>
                                                <input type="text" class="form-control" id="Percentagewl" name="Percentagewl">
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
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="pjornal">
                                                <label class="form-check-label" for="pjornal">Include employer's contribution</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-border">
                            <div class="horizontal-fields">
                                <div class="field-group">
                                    
                                    <button type="button" id="wholdingcodes" class="btn btn-sm btn-info" data-toggle="modal" data-target="#addwhGroupModal">Code(s) Selection</button>
                                </div>
                                <div class="field-group">
                                    <label for="taxGrossUp" class="col-form-label">Tax Relief</label>
                                    <input type="text" class="form-control" id="taxrelief">
                                </div>
                                <div class="field-group">
                                    <label for="sortCode" class="col-form-label">Sort Code</label>
                                    <input type="text" class="form-control" id="sortCode" placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="horizontal-fields">
                            <div class="field-group">
                                <label for="employerContributionCode">Voluntary contribution code</label>
                                <input type="text" class="form-control" id="employerContributionCode" value="D34C">
                            </div>
                            <div class="field-group">
                                <label for="voluntaryNSSFContribution">Employer Contribution Code</label>
                                <input type="text" class="form-control" id="voluntaryNSSFContribution" value="D04">
                            </div>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    
                    <button type="submit" class="btn btn-enhanced btn-draft">
                                        <i class="fas fa-check-circle"></i>Save
                                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="unionmodal" tabindex="-1" role="dialog" aria-labelledby="unionmodal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unionmodal">Union</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container mt-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <form id="unionduesForm">
                                        <input type="hidden" name="formType" value="uniondues">
                                        <legend>Union Dues</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Description</label>
                                                <input type="text" class="form-control" id="udesc" name="udesc" required>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="maxContribution">Code</label>
                                                <input type="text" class="form-control" id="ucode" name="ucode" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Employee Percentage</label>
                                                <input type="text" class="form-control" id="uemployeePercentage" name="employeePercentage" required>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="maxContribution">Max Contribution</label>
                                                <input type="text" class="form-control" id="maxContr" name="maxContr" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="form-check">
                                                <input type="checkbox" id="unionact" name="unionact" value="YES" class="form-check-input">
                                                <label for="unionized" class="form-check-label">Active</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <form id="cotuForm">
                                        <input type="hidden" name="formType" value="cotu">
                                        <legend>COTU</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Description</label>
                                                <input type="text" class="form-control" id="ctdesc" name="udesc" required>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="maxContribution">Code</label>
                                                <input type="text" class="form-control" id="ctcode" name="ucode" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Amount</label>
                                                <input type="text" class="form-control" id="ctamount" name="cotuamount">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-4">
                                                <input type="checkbox" id="ctact" name="ctact" value="YES" class="form-check-input">
                                                <label for="unionized" class="form-check-label">Active</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="form-group-border">
                            <div class="horizontal-fields">
                                <div class="field-group">
                                    
                                    <button type="button" id="unioncodes" class="btn btn-sm btn-info" data-toggle="modal" data-target="#addunionGroupModal">Code(s) Selection</button>
                                </div>
                                <!---<div class="field-group">
                                    <label for="taxGrossUp" class="col-form-label">Tax Relief</label>
                                    <input type="text" class="form-control" id="taxrelief">
                                </div>
                                <div class="field-group">
                                    <label for="sortCode" class="col-form-label">Sort Code</label>
                                    <input type="text" class="form-control" id="sortCode" placeholder="0">
                                </div>--->
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    
                </div>
                
            </div>
        </div>
    </div>

    <div class="modal fade" id="payeModal" tabindex="-1" role="dialog" aria-labelledby="taxBracketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taxBracketModalLabel">Tax Bracket Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="taxBracketForm">
                <input type="hidden" name="formType" value="taxbracket">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>TaxBrand</th>
                                <th>MinAmount</th>
                                <th>MaxAmount</th>
                                <th>TaxRate</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="taxBracketTableBody">
                            <!-- Existing rows will be modified to include the "+" button -->
                        </tbody>
                    </table>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" >Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="addunionGroupModal" tabindex="-1" role="dialog" aria-labelledby="addpensionGroupModalLabel" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addpensionGroupModalLabel">Add Union Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addunionGroupForm">
                    <div class="form-group">
                        <label for="pitem">Select Item</label>
                        <select name="uitempen" id="uitempen" class="custom-select form-control" required="true" autocomplete="off" onchange="populateCategory3()">
                            <option value="">Select Item</option>

                        </select>
                        <input name="codeun" id="codeun" type="text" class="form-control" required="true" autocomplete="off" hidden >
                    </div>
                </form>
                <div class="container mt-3">
                        <table id="unionCodesTable" class="content-table" >
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="mt-3"  >
                            
                            <button id="deleteunionGroup" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveunionGroup">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addwhGroupModal" tabindex="-1" role="dialog" aria-labelledby="addpensionGroupModalLabel" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addpensionGroupModalLabel">Add Withholding Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addpensionGroupForm">
                    <div class="form-group">
                        <label for="pitem">Select Item</label> 
                        <select name="whitempen" id="whitempen" class="custom-select form-control" required="true" autocomplete="off" onchange="populateCategory4()">
                            <option value="">Select Item</option>
                        
                        </select>
                        <input name="codewhg" id="codewhg" type="text" class="form-control" required="true" autocomplete="off" hidden>
                    </div>
                </form>
                <div class="container mt-3">
                        <table id="whCodesTable" class="content-table" >
                            <thead>
                                <tr>
                                    <th hidden>ID</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="mt-3"  >
                            
                            <button id="deletewhGroup" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savewhGroup">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="preliefModal" tabindex="-1" role="dialog" aria-labelledby="nssfModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 400px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nssfModalLabel">Personal Relief - Changes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form id="preliefForm">
                   <input type="hidden" name="formType" value="prelief">
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <label>Description</label>
                                                <input type="text" class="form-control" id="cnamepr" name="cnamepr">
                                                <input type="hidden" class="form-control" id="IDpr" name="IDpr">
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>Relief Period</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-10">
                                                <select class="form-control" id="rPeriod" name="rPeriod">
                                                    <option>Monthly</option>
                                                    <option>Annually</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>Relief Amount</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-10">
                                                
                                                <input type="number" class="form-control" id="pramount" name="pramount">
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="DefinedModal" tabindex="-1" role="dialog" aria-labelledby="nssfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nssfModalLabel">Defined Relief - Changes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="definedForm">
                    <input type="hidden" name="formType" value="Drelief">
                    <div class="container mt-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label>Description</label>
                                            <input type="text" class="form-control" id="DnameIr" name="DnameIr">
                                            <input type="hidden" class="form-control" id="DIDIr" name="DIDIr">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container mt-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <legend>%</legend>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="DIpercentage" name="DIpercentage">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <legend>Maximum Amount</legend>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="Dmaxamount" name="Dmaxamount">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
    </div>
</div>
    <div class="modal fade" id="IreliefModal" tabindex="-1" role="dialog" aria-labelledby="nssfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nssfModalLabel">Insurance Relief - Changes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="IreliefForm">
                    <input type="hidden" name="formType" value="Irelief">
                    <div class="container mt-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label>Description</label>
                                            <input type="text" class="form-control" id="cnameIr" name="cnameIr">
                                            <input type="hidden" class="form-control" id="IDIr" name="IDpr">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <button type="button" id="insurancecodes" class="btn btn-sm btn-info">Code(s) Selection</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container mt-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <legend>%</legend>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="Ipercentage" name="pntage">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group-border">
                                    <legend>Maximum Amount</legend>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="Imaxamount" name="maxamount">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container mt-3">
                        <table id="insuranceCodesTable" class="content-table" style="display:none;">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="mt-3" id="insuranceButtons" style="display:none;">
                            <button type="button" id="addInsuranceGroup" class="btn btn-primary" data-toggle="modal" data-target="#addInsuranceGroupModal">Add</button>
                            <button id="deleteInsuranceGroup" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="addpensionGroupModal" tabindex="-1" role="dialog" aria-labelledby="addpensionGroupModalLabel" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addpensionGroupModalLabel">Add Pension Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addpensionGroupForm">
                    <div class="form-group">
                        <label for="pitem">Select Item</label> 
                        <select name="pitempen" id="pitempen" class="custom-select form-control" required="true" autocomplete="off" onchange="populateCategory1()">
                            <option value="">Select Item</option>
                        
                        </select>
                        <input name="codepen" id="codepen" type="text" class="form-control" required="true" autocomplete="off" hidden>
                    </div>
                </form>
                <div class="container mt-3">
                        <table id="pensionCodesTable" class="content-table" >
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="mt-3"  >
                            
                            <button id="deletepensionGroup" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savepensionGroup">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addInsuranceGroupModal" tabindex="-1" role="dialog" aria-labelledby="addInsuranceGroupModalLabel" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInsuranceGroupModalLabel">Add Insurance Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addInsuranceGroupForm">
                    <div class="form-group">
                        <label for="pitem">Select Item</label>
                        <select name="pitem" id="pitem" class="custom-select form-control" required="true" autocomplete="off" onchange="populateCategory()">
                            <option value="">Select Item</option>
                            
                        </select>
                        <input name="codeinsu" id="codeinsu" type="text" class="form-control" required="true" autocomplete="off" hidden>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveInsuranceGroup">Save</button>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="HlevyModal" tabindex="-1" role="dialog" aria-labelledby="shifModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shifModalLabel">Housing Levy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form id="hlevyForm">
                <input type="hidden" name="formType" value="hlevy">
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Description</label>
                                                <input type="text" class="form-control" id="cnamehl" name="cnamehl">
                                                <input type="text" class="form-control" id="IDhl" name="IDhl" hidden>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Code</label>
                                                <input type="text" class="form-control" id="codehl" name="codehl" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>BALANCES</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="cummulativeB">
                                                    <label class="form-check-label" for="pjornal">Cumulative Balance</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <select class="form-control" id="balances">
                                                    <option>Employee</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group-border">
                                        <legend>Housing Levy Rate</legend>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Employee Percentage</label>
                                                <input type="text" class="form-control" id="Percentagehl" name="Percentagehl">
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
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="pjornal">
                                                <label class="form-check-label" for="pjornal">Include employer's contribution</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-border">
                            <div class="horizontal-fields">
                                <div class="field-group">
                                    <div class="col-sm-16">
                                        <div class="toggle-container" id="relief-houz">
                                            <input type="radio" id="houznone" name="houzrelief" value="NONE" required checked>
                                            <label for="houznone">Not Relief</label>
                                            <input type="radio" id="houzrnt" name="houzrelief" value="RELIEF ON TAXABLE">
                                            <label for="houzrnt">Relief taxable</label>
                                            <input type="radio" id="houzrnp" name="houzrelief" value="Relief on Paye">
                                            <label for="houzrnp">Relief on Paye</label>
                                            <span class="slider"></span>
                                        </div>
                                    </div>
                                </div>
                                <!----<div class="field-group">
                                    <label for="taxGrossUp" class="col-form-label">Tax Relief</label>
                                    <input type="text" class="form-control" id="taxrelief">
                                </div>
                                <div class="field-group">
                                    <label for="sortCode" class="col-form-label">Sort Code</label>
                                    <input type="text" class="form-control" id="sortCode" placeholder="0">
                                </div>---->
                            </div>
                        </div>
                        <div class="horizontal-fields">
                            <div class="field-group">
                                <label for="employerContributionCode">Voluntary contribution code</label>
                                <input type="text" class="form-control" id="employerContributionCode" value="D34C">
                            </div>
                            <div class="field-group">
                                <label for="voluntaryNSSFContribution">Employer Contribution Code</label>
                                <input type="text" class="form-control" id="voluntaryNSSFContribution" value="D04">
                            </div>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                </form>
            </div>
        </div>
    </div>
            
            
        </div>
    </div>
    <!---<script src="{{ asset('js/custom-dropdown.js') }}"></script>--->

    <script>
        const amanage = '{{ route("ritems.update") }}';
        const getwuth = '{{ route("ritems.getwithholding") }}';
        const storewith = '{{ route("whgroups.store") }}';
         const delwith = '{{ route("whgroups.delete") }}';
         const getcodes = '{{ route("ritems.getcodes") }}';
    </script>
    <script src="{{ asset('js/ritems.js') }}"></script>
    
    
</x-custom-admin-layout>