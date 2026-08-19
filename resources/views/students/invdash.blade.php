<x-custom-admin-layout>

@vite(['resources/css/pages/dashboard.css'])

<div class="dashboard-page commissions-page">

<div class="tab-bar">
        <button class="tab-btn active" data-tab="staffInfo">
            <span class="material-icons">looks</span>
            Commissions
            <span class="tab-badge" id="badge-staffInfo">✓</span>
        </button>
        <button class="tab-btn" id="tab-registration" data-tab="registration">
            <span class="material-icons">filter_list</span>
            Deductions
            <span class="tab-badge" id="badge-registration">✓</span>
        </button>
    </div>

    {{-- ── Page heading ─────────────────────────────────────── --}}
    <div class="dash-heading">
        <div>
            
            <p>Track invoiced, outstanding, deductions&balances and aged net pay across periods.</p>
        </div>
        <div class="dash-date">
            <span class="material-icons">calendar_today</span>
            <span id="dashDate"></span>
        </div>
    </div>
    <div class="tab-panel active" id="panel-staffInfo">
    {{-- ── Filter panel ─────────────────────────────────────── --}}
    <div class="filter-panel">
        <div class="filter-field">
            <label for="filterPortfolio">Portfolio</label>
            <select id="filterPortfolio" class="filter-select">
                <option value="">All</option>
                @foreach($portfolios as $portfolio)
                    <option value="{{ $portfolio['ID'] }}">{{ $portfolio['pname'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-field">
            <label for="filterVendor">Vendor Name</label>
            {{-- Populated separately --}}
            <select id="filterVendor" class="filter-select">
                <option value="">All</option>
            </select>
        </div>

        <div class="filter-field">
            <label for="filterStatus">Payment Status</label>
            <select id="filterStatus" class="filter-select">
                <option value="">All</option>
                <option value="PAID">PAID</option>
                <option value="UNPAID">UNPAID</option>
            </select>
        </div>

        <div class="filter-field">
            <label for="filterMonth">Month</label>
            <select id="filterMonth" class="filter-select">
                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                    <option value="{{ $m }}" @selected($m === $defaultMonth)>{{ $m }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-field">
            <label for="filterYear">Year</label>
            <select id="filterYear" class="filter-select">
                @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" @selected($y == $defaultYear)>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <button id="filterApplyBtn" class="btn btn-upload filter-apply-btn">
            <span class="material-icons">filter_alt</span> Apply
        </button>
    </div>

    {{-- ── Stat cards ───────────────────────────────────────── --}}
    <div class="stat-grid commissions-stat-grid">
        <div class="stat-card blue">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Invoiced Net Pay</div>
                    <div class="stat-card-value" id="statInvoiced">—</div>
                    <div class="stat-card-sub" id="statInvoicedCount">0 employees</div>
                </div>
                <div class="stat-icon blue"><span class="material-icons">receipt_long</span></div>
            </div>
        </div>

        <div class="stat-card red">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Not Invoiced</div>
                    <div class="stat-card-value" id="statNotInvoiced">—</div>
                    <div class="stat-card-sub" id="statNotInvoicedCount">0 employees</div>
                </div>
                <div class="stat-icon red"><span class="material-icons">error_outline</span></div>
            </div>
        </div>

        <div class="stat-card amber">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Outstanding (All Periods)</div>
                    <div class="stat-card-value" id="statOutstanding">—</div>
                    <div class="stat-card-sub">Not yet paid</div>
                </div>
                <div class="stat-icon amber"><span class="material-icons">hourglass_bottom</span></div>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Paid</div>
                    <div class="stat-card-value" id="statPaid">—</div>
                    <div class="stat-card-sub" id="statPaidCount">0 employees</div>
                </div>
                <div class="stat-icon green"><span class="material-icons">task_alt</span></div>
            </div>
        </div>
    </div>

    {{-- ── Charts ────────────────────────────────────────────── --}}
    <div class="charts-grid">

        <div class="chart-card">
            <div class="chart-card-head">
                <div class="chart-head-icon purple"><span class="material-icons">payments</span></div>
                <span class="chart-title">Net Pay Trend</span>
            </div>
            <div class="chart-body">
                <div id="netPayTrendContainer" class="chart-container-inner"></div>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-card-head">
                <div class="chart-head-icon red"><span class="material-icons">trending_down</span></div>
                <span class="chart-title">Not Paid Trend</span>
            </div>
            <div class="chart-body">
                <div id="notPaidTrendContainer" class="chart-container-inner"></div>
            </div>
        </div>

    </div>

    {{-- ── Aging table ──────────────────────────────────────── --}}
    <div class="section-card">
        <div class="section-card-head">
            <div class="chart-head-icon amber"><span class="material-icons">schedule</span></div>
            <span class="chart-title">Aging of Outstanding Net Pay</span>
        </div>
        <div class="table-scroll">
            <table class="data-table aging-table" id="agingTable">
                <thead>
                    <tr>
                        <th>Aging Category</th>
                        <th>Invoices</th>
                        <th>Gross Amount</th>
                        <th>Net Amount</th>
                    </tr>
                </thead>
                <tbody id="agingTableBody">
                    <tr><td colspan="4" class="table-empty">Loading…</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Invoiced listing table ───────────────────────────── --}}
    <div class="section-card">
    <div class="section-card-head">
        <div class="chart-head-icon blue"><span class="material-icons">list_alt</span></div>
        <span class="chart-title">Invoiced Payments — Current Period</span>
    </div>
    <div class="table-scroll">
        <table class="data-table listing-table" id="listingTable">
            <thead>
                <tr>
                    <th>Vendor Name</th>
                    <th>PIN</th>
                    <th>Invoice Num</th>
                    <th>Invoice Date</th>
                    <th>Portfolio</th>
                    <th>Gross Amount</th>
                    <th>WHTAX</th>
                    <th>Comm/Adv Ded.</th>
                    <th>Net Amount</th>
                    <th>Amount Paid</th>
                    <th>Payment Date</th>
                    <th>Payment Status</th>
                    <th>Age (Days)</th>
                    <th>Aging Category</th>
                </tr>
            </thead>
            <tbody id="listingTableBody">
                <tr><td colspan="14" class="table-empty">Loading…</td></tr>
            </tbody>
        </table>
    </div>
</div>

</div>
<div class="tab-panel" id="panel-registration">

<div class="filter-panel">
        <div class="filter-field">
            <label for="filterPortfolio">Portfolio</label>
            <select id="filterPortfolioded" class="filter-select">
                <option value="">All</option>
                @foreach($portfolios as $portfolio)
                    <option value="{{ $portfolio['ID'] }}">{{ $portfolio['pname'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-field">
            <label for="filterVendor">Vendor Name</label>
            <select id="filterVendorded" class="filter-select">
                <option value="">All</option>
            </select>
        </div>

        <div class="filter-field">
            <label for="filterMonth">Month</label>
            <select id="filterMonthded" class="filter-select">
                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                    <option value="{{ $m }}" @selected($m === $defaultMonth)>{{ $m }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-field">
            <label for="filterYear">Year</label>
            <select id="filterYearded" class="filter-select">
                @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" @selected($y == $defaultYear)>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <button id="filterApplyBtn" class="btn btn-upload filter-apply-btn">
            <span class="material-icons">filter_alt</span> Apply
        </button>
    </div>

    {{-- ── Stat cards ───────────────────────────────────────── --}}
    <div class="stat-grid commissions-stat-grid">
        <div class="stat-card blue">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Total Deducted</div>
                    <div class="stat-card-value" id="statTotalDeducted">—</div>
                    <div class="stat-card-sub" id="statDeductedCount">0 employees</div>
                </div>
                <div class="stat-icon blue"><span class="material-icons">remove_circle_outline</span></div>
            </div>
        </div>

        <div class="stat-card amber">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Total Outstanding Balance</div>
                    <div class="stat-card-value" id="statTotalBalance">—</div>
                    <div class="stat-card-sub">Across all loan/savings items</div>
                </div>
                <div class="stat-icon amber"><span class="material-icons">account_balance_wallet</span></div>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Item Types</div>
                    <div class="stat-card-value" id="statItemTypes">—</div>
                    <div class="stat-card-sub">Distinct deduction types this period</div>
                </div>
                <div class="stat-icon green"><span class="material-icons">category</span></div>
            </div>
        </div>
    </div>

    {{-- ── Charts ────────────────────────────────────────────── --}}
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-card-head">
                <div class="chart-head-icon purple"><span class="material-icons">bar_chart</span></div>
                <span class="chart-title">Total Deductions by Type</span>
            </div>
            <div class="chart-body">
                <div id="deductionsByTypeContainer" class="chart-container-inner"></div>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-card-head">
                <div class="chart-head-icon amber"><span class="material-icons">account_balance_wallet</span></div>
                <span class="chart-title">Outstanding Balances by Type</span>
            </div>
            <div class="chart-body">
                <div id="balancesByTypeContainer" class="chart-container-inner"></div>
            </div>
        </div>
    </div>

    {{-- ── Listing table ────────────────────────────────────── --}}
    <div class="section-card">
        <div class="section-card-head">
            <div class="chart-head-icon blue"><span class="material-icons">list_alt</span></div>
            <span class="chart-title">Deductions — Current Period</span>
        </div>
        <div class="table-scroll">
            <table class="data-table listing-table" id="deductionListingTable">
                <thead>
                    <tr>
                        <th>Vendor Name</th>
                        <th>PIN</th>
                        <th>Portfolio</th>
                        <th>Item Code</th>
                        <th>Item Description</th>
                        <th>Amount Deducted</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody id="deductionListingTableBody">
                    <tr><td colspan="7" class="table-empty">Loading…</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>{{-- /commissions-page --}}

<div id="commissions-data-bridge"
     data-endpoint="{{ route('dashboard.commissions.data') }}"
     data-default-month="{{ $defaultMonth }}"
     data-default-year="{{ $defaultYear }}">
</div>

<div id="deductions-data-bridge"
     data-endpoint="{{ route('dashboard.deductions.data') }}"
     data-default-month="{{ $defaultMonth }}"
     data-default-year="{{ $defaultYear }}">
</div>

@vite(['resources/js/highcharts-init.js', 'resources/js/invdash.js', 'resources/js/deducdash.js'])

</x-custom-admin-layout>