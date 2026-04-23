<x-custom-admin-layout>


 @vite(['resources/css/pages/pitems.css']) 

<div class="pitems-page">

    <div class="page-header">
        <div class="page-heading">
            <h1>Payroll Items</h1>
            <p>Manage payroll codes, deductions, and payment types.</p>
        </div>
        <button class="btn btn-primary-mod" data-action="open-modal" data-target="addModal">
    <span class="material-icons">add</span> New Item
</button>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <!-- Table card -->
    <div class="table-card">
        <div class="table-toolbar">
            <div class="toolbar-left">
                <div class="toolbar-icon">
                    <span class="material-icons">receipt_long</span>
                </div>
                <div>
                    <div class="toolbar-title">All Payroll Items</div>
                    <div class="toolbar-subtitle">Codes, types and deduction settings</div>
                </div>
            </div>
        </div>

        <div class="table-wrap">
            <table class="pitems-table stripe hover nowrap" id="payrollCodesTable">
                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th>Code</th>
                        <th>Process Type</th>
                        <th>Trans Type</th>
                        <th>Pay Type</th>
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
                        <td hidden>{{ $row->ID }}</td>
                        <td>
                            <div class="code-cell">
                                <span class="code-primary">{{ $row->code }}</span>
                                <span class="code-desc">{{ $row->cname }}</span>
                            </div>
                        </td>
                        <td>{{ $row->procctype }}</td>
                        <td>
                            @php $vof = strtolower($row->varorfixed); @endphp
                            <span class="type-badge {{ $vof }}">{{ $row->varorfixed }}</span>
                        </td>
                        <td>
                            @php $tax = $row->taxaornon === 'Non-taxable' ? 'nontaxable' : 'taxable'; @endphp
                            <span class="type-badge {{ $tax }}">{{ $row->taxaornon }}</span>
                        </td>
                        <td>
                            @php $cat = strtolower($row->category); @endphp
                            <span class="type-badge {{ $cat }}">{{ $row->category }}</span>
                        </td>
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
                            <div class="action-wrap">
                                <button class="action-trigger" data-action="toggle-menu">
                                    <span class="material-icons">more_horiz</span>
                                </button>
                                <div class="action-menu">
                                    <a href="#"
                                    data-action="open-edit"
                                    data-id="{{ $row->ID }}">
                                    <span class="material-icons">edit</span> Edit
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     ADD MODAL
══════════════════════════════════════════════════════ -->
<div class="modal-backdrop-custom" id="addModal">
    <div class="modal-card">

        <div class="modal-header">
            <div class="modal-header-icon">
                <span class="material-icons">add_circle_outline</span>
            </div>
            <div class="flex1">
                <div class="modal-header-title">New Payroll Item</div>
                <div class="modal-header-subtitle">Add a payroll code to the system</div>
            </div>
            <button class="modal-close-btn" onclick="document.getElementById('addModal').classList.remove('open')">
                <span class="material-icons">close</span>
            </button>
        </div>

        <div class="modal-body">
            <form id="payrollForm" method="post" data-newitem-url="{{ route('pitems.store') }}">
                @csrf
                

                <!-- Basic info -->
                <div class="form-section">
                    <p class="form-section-label">Basic Information</p>
                    <div class="form-grid">
                        <div class="field fc-2">
                            <label>Code <span class="req">*</span></label>
                            <input type="text" id="code" name="code" placeholder="e.g. E01" required autocomplete="off">
                        </div>
                        <div class="field fc-6">
                            <label>Description <span class="req">*</span></label>
                            <input type="text" id="description" name="description" placeholder="e.g. Basic Salary" required autocomplete="off">
                        </div>
                        <div class="field fc-4">
                            <label>Category <span class="req">*</span></label>
                            <div class="select-wrap">
                                <select id="category" name="category" required>
                                    <option value="">Select category</option>
                                    <option value="normal">Normal</option>
                                    <option value="balance">Balance</option>
                                    <option value="loan">Loan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Process type + conditional -->
                <div class="form-section">
                    <p class="form-section-label">Process Type</p>
                    <div class="form-grid">
                        <div class="field fc-4">
                            <label>Process Type</label>
                            <div class="seg-toggle">
                                <input type="radio" id="amount" name="processt" value="Amount" checked>
                                <label for="amount">Amount</label>
                                <input type="radio" id="calculationRadio" name="processt" value="calculation">
                                <label for="calculationRadio">Calculation</label>
                            </div>
                        </div>
                        <div class="field fc-4 hidden" id="balanceOptions" >
                            <label>Balance Type</label>
                            <div class="seg-toggle">
                                <input type="radio" id="increasing" name="balanceType" value="Increasing">
                                <label for="increasing">Increasing</label>
                                <input type="radio" id="reducing" name="balanceType" value="Reducing">
                                <label for="reducing">Reducing</label>
                            </div>
                        </div>
                        <div class="field fc-2 hidden" id="loanRateField" >
                            <label>Rate</label>
                            <input type="text" id="rate" name="rate" placeholder="0.00" autocomplete="off">
                        </div>
                        <div class="field fc-4 hidden" id="loanRate">
                            <label>Recovery &amp; Interest</label>
                            <div class="seg-toggle" id="recint-toggle">
                                <input type="radio" id="recintre" name="recintres" value="1" checked>
                                <label for="recintre">Recov &amp; Int</label>
                                <input type="radio" id="separate" name="recintres" value="0">
                                <label for="separate">Separate</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Code type + GL -->
                <div class="form-section">
                    <p class="form-section-label">Payroll Code Type &amp; GL Accounts</p>
                    <div class="form-grid">
                        <!-- Left: Code type -->
                        <div class="fc-6">
                            <div class="fieldset-box">
                                <span class="fieldset-legend">Code Type</span>
                                <div class="form-grid margintop" >
                                    <div class="field fc-12">
                                        <label>Type <span class="req">*</span></label>
                                        <div class="select-wrap">
                                            <select id="prossty" name="prossty" required>
                                                <option value="">Select type</option>
                                                <option value="Payment">Payment</option>
                                                <option value="Deduction">Deduction</option>
                                                <option value="Benefit">Benefit</option>
                                                <option value="Relief">Relief</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>Variable / Fixed</label>
                                        <div class="seg-toggle" id="varorfixed-toggle">
                                            <input type="radio" id="variable" name="varorfixed" value="Variable" checked>
                                            <label for="variable">Variable</label>
                                            <input type="radio" id="fixed" name="varorfixed" value="Fixed">
                                            <label for="fixed">Fixed</label>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>Tax Status</label>
                                        <div class="seg-toggle" id="taxaornon-toggle">
                                            <input type="radio" id="taxable" name="taxaornon" value="Taxable" checked>
                                            <label for="taxable">Taxable</label>
                                            <input type="radio" id="nontax" name="taxaornon" value="Non-taxable">
                                            <label for="nontax">Non-taxable</label>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <div class="chip-row">
                                            <div class="chip-check">
                                                <input type="checkbox" id="saccocheck" name="saccocheck" value="Yes">
                                                <label for="saccocheck">
                                                    <span class="material-icons">savings</span> Sacco Related
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field fc-12 hidden" id="sacconames" >
                                        <label>Staff List</label>
                                        <div class="select-wrap">
                                            <select id="staffSelect7" name="staffSelect7">
                                                <option value="">Select Staff</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: GL -->
                        <div class="fc-6">
                            <div class="fieldset-box">
                                <span class="fieldset-legend">GL Accounts</span>
                                <div class="form-grid margintop" >
                                    <div class="field fc-12">
                                        <div class="chip-row">
                                            <div class="chip-check">
                                                <input type="checkbox" id="pjornal">
                                                <label for="pjornal">
                                                    <span class="material-icons">link</span> Link to Payroll Journal
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>A/C Number</label>
                                        <input type="text" id="accountNumber" placeholder="Account number" autocomplete="off">
                                    </div>
                                    <div class="field fc-12">
                                        <label>Cost Centre</label>
                                        <input type="text" id="cc" placeholder="CC" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flags -->
                <div class="form-section">
                    <p class="form-section-label">Flags &amp; Relief</p>
                    <div class="form-grid">
                        <div class="field fc-6">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="exemptionBonuses">
                                    <label for="exemptionBonuses">Exemption / Bonuses / Overtime &amp; Retirement</label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-3">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="appearInP9">
                                    <label for="appearInP9">
                                        <span class="material-icons">description</span> Appear in P9
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-12">
                            <label>Relief Type</label>
                            <div class="seg-toggle seg-3" id="relief-toggle">
                                <input type="radio" id="none" name="relief" value="NONE" checked>
                                <label for="none">Not Relief</label>
                                <input type="radio" id="rnt" name="relief" value="RELIEF ON TAXABLE">
                                <label for="rnt">Relief on Taxable</label>
                                <input type="radio" id="rnp" name="relief" value="Relief on Paye">
                                <label for="rnp">Relief on PAYE</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calculation -->
                <div class="form-section">
                    <p class="form-section-label">Calculation</p>
                    <div class="form-grid">
                        <div class="field fc-12">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="calculation">
                                    <label for="calculation"><span class="material-icons">functions</span> Calculation</label>
                                </div>
                                <div class="chip-check">
                                    <input type="checkbox" id="cumulativeValue" name="calctype" value="cumulative">
                                    <label for="cumulativeValue"><span class="material-icons">stacked_line_chart</span> Cumulative Value</label>
                                </div>
                                <div class="chip-check">
                                    <input type="checkbox" id="casual" name="calctype" value="casual">
                                    <label for="casual"><span class="material-icons">person_outline</span> Casual</label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-12">
                            <label>Formula</label>
                            <input type="text" id="inputField" name="formularinpu" readonly placeholder="Formula will appear here" required>
                        </div>
                        <div class="field fc-3 hidden" id="loanhelper" >
                            <label>Interest Code</label>
                            <input type="text" id="interestcode" name="interestcode" autocomplete="off">
                        </div>
                        <div class="field fc-5 hidden" id="loanhelperDesc" >
                            <label>Interest Description</label>
                            <input type="text" id="interestdesc" name="interestdesc" autocomplete="off">
                        </div>
                        <div class="fc-12">
                            <div class="field-error" id="feedback"></div>
                        </div>
                    </div>
                </div>

                <!-- Priority section -->
                <div id="prioritySection" class="hidden" >
                    <div class="form-section-label marginbot" >Deduction Priority</div>
                    <div class="priority-info-banner">
                        <span class="material-icons">drag_indicator</span>
                        <span><strong>Drag and drop</strong> to set the deduction priority order.</span>
                    </div>
                    <div class="priors" >
                        <div>
                            <div class="priority-current-card">
                                <div>
                                    <div class="label-tiny">Current Deduction</div>
                                    <div class="item-name" id="currentItemName">—</div>
                                    <div class="item-code" id="currentItemCode">Code will appear here</div>
                                </div>
                                <span class="priority-badge-pill" id="currentPriorityBadge">
                                    Priority <span id="currentPriorityNumber">—</span>
                                </span>
                            </div>
                            <div class="priority-list-card">
                                <div class="priority-list-header">
                                    <span class="material-icons">format_list_numbered</span>
                                    Existing Deductions — drag to reorder
                                </div>
                                <ul id="sortableDeductions"></ul>
                            </div>
                            <input type="hidden" name="priority" id="priorityInput">
                        </div>
                        <div class="priority-guide-card">
                            <div class="guide-title">
                                <span class="material-icons">help_outline</span> Priority Guide
                            </div>
                            <ol>
                                <li>Lower number = Higher priority</li>
                                <li>Priority 1 deducted first</li>
                                <li>Drag items to change order</li>
                            </ol>
                            <div class="form-section-label fontz">Example Order</div>
                            <div class="example-item">1️⃣ Statutory</div>
                            <div class="example-item">2️⃣ Loans</div>
                            <div class="example-item">3️⃣ SACCO</div>
                            <div class="example-item">4️⃣ Welfare</div>
                            <div class="example-item">5️⃣ Other</div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="document.getElementById('addModal').classList.remove('open')">
                <span class="material-icons">close</span> Cancel
            </button>
            <button type="submit" form="payrollForm" class="btn btn-save">
                <span class="material-icons">save</span> Save Item
            </button>
        </div>
    </div>
</div>
<span class="material-icons spin">sync</span>
<!-- ══════════════════════════════════════════════════════
     EDIT MODAL
══════════════════════════════════════════════════════ -->
<div class="modal-backdrop-custom" id="editModal">
    <div class="modal-card">

        <div class="modal-header">
            <div class="modal-header-icon">
                <span class="material-icons">edit</span>
            </div>
            <div class="flex1">
                <div class="modal-header-title">Edit Payroll Item</div>
                <div class="modal-header-subtitle" id="editModalSubtitle">Loading…</div>
            </div>
            <button class="modal-close-btn">
                <span class="material-icons">close</span>
            </button>
        </div>

        <div class="modal-body">
            <form id="editpayrollForm">
                @csrf
                <input type="hidden" id="editid" name="editid">

                <div class="form-section">
                    <p class="form-section-label">Basic Information</p>
                    <div class="form-grid">
                        <div class="field fc-2">
                            <label>Code</label>
                            <input type="text" id="editCode" name="editCode" autocomplete="off">
                        </div>
                        <div class="field fc-6">
                            <label>Description</label>
                            <input type="text" id="editDescription" name="editDescription" readonly>
                        </div>
                        <div class="field fc-4">
                            <label>Category</label>
                            <div class="select-wrap">
                                <select id="editCategory" name="editCategory">
                                    <option value="">Select category</option>
                                    <option value="normal">Normal</option>
                                    <option value="balance">Balance</option>
                                    <option value="loan">Loan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-label">Process Type</p>
                    <div class="form-grid">
                        <div class="field fc-4">
                            <label>Process Type</label>
                            <div class="seg-toggle">
                                <input type="radio" id="editAmount" name="editProcessType" value="Amount">
                                <label for="editAmount">Amount</label>
                                <input type="radio" id="editCalculation" name="editProcessType" value="Calculation">
                                <label for="editCalculation">Calculation</label>
                            </div>
                        </div>
                        <div class="field fc-4 hidden" id="editBalanceOptions" >
                            <label>Balance Type</label>
                            <div class="seg-toggle">
                                <input type="radio" id="editIncreasing" name="editBalanceType" value="Increasing">
                                <label for="editIncreasing">Increasing</label>
                                <input type="radio" id="editReducing" name="editBalanceType" value="Reducing">
                                <label for="editReducing">Reducing</label>
                            </div>
                        </div>
                        <div class="field fc-2 hidden" id="editLoanRateField" >
                            <label>Rate</label>
                            <input type="text" id="editRate" name="editRate" placeholder="0.00" autocomplete="off">
                        </div>
                        <div class="field fc-4 hidden" id="editLoanRate" >
                            <label>Recovery &amp; Interest</label>
                            <div class="seg-toggle" id="recint-toggleedit">
                                <input type="radio" id="recintredit" name="editrecintres" value="1" checked>
                                <label for="recintredit">Recov &amp; Int</label>
                                <input type="radio" id="separatedit" name="editrecintres" value="0">
                                <label for="separatedit">Separate</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-label">Payroll Code Type &amp; GL Accounts</p>
                    <div class="form-grid">
                        <div class="fc-6">
                            <div class="fieldset-box">
                                <span class="fieldset-legend">Code Type</span>
                                <div class="form-grid margintop" >
                                    <div class="field fc-12">
                                        <label>Type</label>
                                        <div class="select-wrap">
                                            <select id="editProcessSty" name="editProcessSty">
                                                <option value="">Select type</option>
                                                <option value="Payment">Payment</option>
                                                <option value="Deduction">Deduction</option>
                                                <option value="Benefit">Benefit</option>
                                                <option value="Relief">Relief</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>Variable / Fixed</label>
                                        <div class="seg-toggle" id="editVarOrFixedToggle">
                                            <input type="radio" id="editVariable" name="editVarOrFixed" value="Variable" checked>
                                            <label for="editVariable">Variable</label>
                                            <input type="radio" id="editFixed" name="editVarOrFixed" value="Fixed">
                                            <label for="editFixed">Fixed</label>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>Tax Status</label>
                                        <div class="seg-toggle" id="editTaxableToggle">
                                            <input type="radio" id="editTaxable" name="editTaxOrNon" value="Taxable" checked>
                                            <label for="editTaxable">Taxable</label>
                                            <input type="radio" id="editNonTax" name="editTaxOrNon" value="Non-taxable">
                                            <label for="editNonTax">Non-taxable</label>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <div class="chip-row">
                                            <div class="chip-check">
                                                <input type="checkbox" id="saccoeditcheck" name="saccoeditcheck" value="Yes">
                                                <label for="saccoeditcheck">
                                                    <span class="material-icons">savings</span> Sacco Related
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field fc-12 hidden" id="saccoeditnames" >
                                        <label>Staff List</label>
                                        <div class="select-wrap">
                                            <select id="staffSelect8" name="staffSelect8">
                                                <option value="">Select Staff</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fc-6">
                            <div class="fieldset-box">
                                <span class="fieldset-legend">GL Accounts</span>
                                <div class="form-grid margintop" >
                                    <div class="field fc-12">
                                        <div class="chip-row">
                                            <div class="chip-check">
                                                <input type="checkbox" id="editPjornal">
                                                <label for="editPjornal">
                                                    <span class="material-icons">link</span> Link to Payroll Journal
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>A/C Number</label>
                                        <input type="text" id="editAccountNumber" placeholder="Account number" autocomplete="off">
                                    </div>
                                    <div class="field fc-12">
                                        <label>Cost Centre</label>
                                        <input type="text" id="editCc" placeholder="CC" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-label">Flags &amp; Relief</p>
                    <div class="form-grid">
                        <div class="field fc-6">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="editExemptionBonuses">
                                    <label for="editExemptionBonuses">Exemption / Bonuses / Overtime &amp; Retirement</label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-3">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="editAppearInP9" name="editAppearInP9">
                                    <label for="editAppearInP9">
                                        <span class="material-icons">description</span> Appear in P9
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-12">
                            <label>Relief Type</label>
                            <div class="seg-toggle seg-3" id="editReliefToggle">
                                <input type="radio" id="editNone" name="editRelief" value="NONE" checked>
                                <label for="editNone">Not Relief</label>
                                <input type="radio" id="editRNT" name="editRelief" value="RELIEF ON TAXABLE">
                                <label for="editRNT">Relief on Taxable</label>
                                <input type="radio" id="editRNP" name="editRelief" value="Relief on Paye">
                                <label for="editRNP">Relief on PAYE</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-label">Calculation</p>
                    <div class="form-grid">
                        <div class="field fc-12">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="editCalculationCheck">
                                    <label for="editCalculationCheck"><span class="material-icons">functions</span> Calculation</label>
                                </div>
                                <div class="chip-check">
                                    <input type="checkbox" id="editcumulative" name="editcalctype" value="cumulative">
                                    <label for="editcumulative"><span class="material-icons">stacked_line_chart</span> Cumulative Value</label>
                                </div>
                                <div class="chip-check">
                                    <input type="checkbox" id="editcasual" name="editcalctype" value="casual">
                                    <label for="editcasual"><span class="material-icons">person_outline</span> Casual</label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-12">
                            <label>Formula</label>
                            <input type="text" id="editinputField" readonly placeholder="Formula will appear here" required>
                        </div>
                        <div class="field fc-3 hidden" id="editloanhelper" >
                            <label>Interest Code</label>
                            <input type="text hidden" id="editinterestcode" name="interestcode" autocomplete="off">
                        </div>
                        <div class="field fc-5 hidden" id="editloanhelperDesc" >
                            <label>Interest Description</label>
                            <input type="text" id="editinterestdesc" name="interestdesc" autocomplete="off">
                        </div>
                        <div class="fc-12">
                            <div class="field-error" id="editfeedback"></div>
                        </div>
                    </div>
                </div>

                <!-- Edit priority section -->
                <div class="fc-12 hidden" id="prioreSection" >
                    <div class="form-section-label" >Deduction Priority</div>
                    <div class="priority-info-banner">
                        <span class="material-icons">drag_indicator</span>
                        <span><strong>Drag and drop</strong> to set the deduction priority order.</span>
                    </div>
                    <div class="priors">
                        <div>
                            <div class="priority-current-card">
                                <div>
                                    <div class="label-tiny">Current Deduction</div>
                                    <div class="item-name" id="editItemName">—</div>
                                    <div class="item-code" id="eItemCode">Code will appear here</div>
                                </div>
                                <span class="priority-badge-pill" id="editPriorityBadge">
                                    Priority <span id="editPriorityNumber">—</span>
                                </span>
                            </div>
                            <div class="priority-list-card">
                                <div class="priority-list-header">
                                    <span class="material-icons">format_list_numbered</span>
                                    Existing Deductions — drag to reorder
                                </div>
                                <ul id="editsortableDeductions"></ul>
                            </div>
                            <input type="hidden" name="priority" id="editpriorityInput">
                        </div>
                        <div class="priority-guide-card">
                            <div class="guide-title">
                                <span class="material-icons">help_outline</span> Priority Guide
                            </div>
                            <ol>
                                <li>Lower number = Higher priority</li>
                                <li>Priority 1 deducted first</li>
                                <li>Drag items to change order</li>
                            </ol>
                            <div class="form-section-label fontz">Example Order</div>
                            <div class="example-item">1️⃣ Statutory</div>
                            <div class="example-item">2️⃣ Loans</div>
                            <div class="example-item">3️⃣ SACCO</div>
                            <div class="example-item">4️⃣ Welfare</div>
                            <div class="example-item">5️⃣ Other</div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="document.getElementById('editModal').classList.remove('open')">
                <span class="material-icons">close</span> Cancel
            </button>
            <button type="button" id="saveChangesButton" class="btn btn-save">
                <span class="material-icons">save</span> Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Keep all your original scripts exactly as-is -->






@vite(['resources/js/pitems.js'])

</x-custom-admin-layout>