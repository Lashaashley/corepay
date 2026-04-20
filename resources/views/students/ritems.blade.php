<x-custom-admin-layout>
    @vite(['resources/css/pages/ritems.css']) 
    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px">

        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="toast-wrap" id="toastWrap"></div>
            <div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				
                <div class="tab-container margintop" >
                    <button class="tab-button active" >Statutories</button>
                    <button class="tab-button"  disabled>Reliefs</button>
                    
                </div>
                <div id="deductions" class="tab-content active" >
                    <div class="pd-20 card-box mb-30">
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4 class="text-blue h4">Statutories</h4>
                            </div>
                        </div>
                        <div class="btn-group mt-3">
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#nhifModal" disabled>NHIF</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#shifModal" disabled>SHIF</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#nssfModal" disabled>NSSF</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#pensionModal" disabled>Pension</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#payeModal" disabled>PAYE Rates</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" id="openwitho" data-target="#withholding">Withholding</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#HlevyModal" disabled>Housing Levy</button>
                            <button type="button" class="btn btn-primary deduction-btn" data-toggle="modal" data-target="#unionmodal" disabled>Union </button>
                        </div>
                    </div>
                </div>
                <div id="relief" class="tab-content" >
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
                        <select name="whitempen" id="whitempen" class="custom-select form-control" required="true" autocomplete="off" >
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
        <div class="modal-dialog maxwidth4" role="document" >
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
    <div class="modal-dialog maxwidth4" role="document" >
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
    <div class="modal-dialog maxwidth4" role="document" >
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
                        <table id="insuranceCodesTable" class="content-table hidden" >
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="mt-3 hidden" id="insuranceButtons" >
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
    @vite([
    'resources/js/ritems.js'
])
    
    
</x-custom-admin-layout>