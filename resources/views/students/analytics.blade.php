<x-custom-admin-layout>

@vite(['resources/css/pages/analytics.css'])

<div class="analytics-container">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-3 mb-0">Loading analytics data...</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <h5><span class="material-icons mr-2">filter_alt</span>Dashboard Filters</h5>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="analysisMode">Analysis Mode</label>
                    <select class="form-control" id="analysisMode">
                        <option value="single">Single Period</option>
                        <option value="comparison">Period Comparison</option>
                        <option value="range">Date Range</option>
                        <option value="trend">Yearly Trend</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3" id="monthSelector">
                <div class="form-group">
                    <label for="selectedMonth">Month</label>
                    <select class="form-control" id="selectedMonth">
                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                            <option value="{{ $month }}" {{ ($activePeriod->mmonth ?? '') == $month ? 'selected' : '' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3" id="yearSelector">
                <div class="form-group">
                    <label for="selectedYear">Year</label>
                    <select class="form-control" id="selectedYear">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ ($activePeriod->yyear ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary btn-block btn-analytics" id="loadDashboardBtn">
                        <span class="material-icons mr-2">sync</span>Load Dashboard
                    </button>
                </div>
            </div>
        </div>

        <!-- Comparison Mode Filters -->
        <div id="comparisonFilters" class="hidden">
            <div class="comparison-mode">
                <h6><span class="material-icons mr-2">compare_arrows</span>Period Comparison Mode</h6>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6>Period 1</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Month</label>
                                <select class="form-control" id="month1">
                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Year</label>
                                <select class="form-control" id="year1">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Period 2</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Month</label>
                                <select class="form-control" id="month2">
                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Year</label>
                                <select class="form-control" id="year2">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Range Mode Filters -->
        <div id="rangeFilters" class="hidden">
            <div class="comparison-mode backcolor">
                <h6><span class="material-icons mr-2">date_range</span>Date Range Analysis</h6>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6>Start Period</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Month</label>
                                <select class="form-control" id="startMonth">
                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Year</label>
                                <select class="form-control" id="startYear">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>End Period</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Month</label>
                                <select class="form-control" id="endMonth">
                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Year</label>
                                <select class="form-control" id="endYear">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row" id="summaryStats">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card success">
                <div class="stat-icon">
                    <span class="material-icons">payments</span>
                </div>
                <div class="stat-label">Total Gross Pay</div>
                <div class="stat-value" id="totalGrossPay">KES 0.00</div>
                <div class="stat-change positive" id="grossPayChange">
                    <span class="material-icons mr-1" style="font-size:14px;">arrow_upward</span>0%
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card danger">
                <div class="stat-icon">
                    <span class="material-icons">remove_circle</span>
                </div>
                <div class="stat-label">Total Deductions</div>
                <div class="stat-value" id="totalDeductions">KES 0.00</div>
                <div class="stat-change negative" id="deductionsChange">
                    <span class="material-icons mr-1" style="font-size:14px;">arrow_downward</span>0%
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card info">
                <div class="stat-icon">
                    <span class="material-icons">account_balance_wallet</span>
                </div>
                <div class="stat-label">Total Net Pay</div>
                <div class="stat-value" id="totalNetPay">KES 0.00</div>
                <div class="stat-change positive" id="netPayChange">
                    <span class="material-icons mr-1" style="font-size:14px;">arrow_upward</span>0%
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card warning">
                <div class="stat-icon">
                    <span class="material-icons">groups</span>
                </div>
                <div class="stat-label">Total AGENTS</div>
                <div class="stat-value" id="totalEmployees">0</div>
                <div class="stat-change" id="employeesChange">
                    <span class="material-icons mr-1" style="font-size:14px;">drag_handle</span>0
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <span class="material-icons">donut_large</span>
                </div>
                <div class="stat-label">Deduction Rate</div>
                <div class="stat-value" id="deductionRate">0%</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <span class="material-icons">calculate</span>
                </div>
                <div class="stat-label">Average Net Pay</div>
                <div class="stat-value" id="averageNetPay">KES 0.00</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <span class="material-icons">volunteer_activism</span>
                </div>
                <div class="stat-label">Total Payments</div>
                <div class="stat-value" id="totalPayments">KES 0.00</div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><span class="material-icons mr-2">pie_chart</span>Payment Breakdown</h5>
                    <button class="btn btn-sm btn-outline-primary" id="exppaybreak">
                        <span class="material-icons mr-1" style="font-size:16px;">download</span>Export
                    </button>
                </div>
                <div class="chart-container" id="paymentBreakdownChart"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><span class="material-icons mr-2">pie_chart</span>Deduction Breakdown</h5>
                    <button class="btn btn-sm btn-outline-primary" id="expdedcu">
                        <span class="material-icons mr-1" style="font-size:16px;">download</span>Export
                    </button>
                </div>
                <div class="chart-container" id="deductionBreakdownChart"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><span class="material-icons mr-2">show_chart</span>Monthly Trend Analysis</h5>
                    <button class="btn btn-sm btn-outline-primary" id="exptrendch">
                        <span class="material-icons mr-1" style="font-size:16px;">download</span>Export
                    </button>
                </div>
                <div class="chart-container" id="trendChart"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><span class="material-icons mr-2">bar_chart</span>Department Overview</h5>
                    <button class="btn btn-sm btn-outline-primary" id="expdeptcha">
                        <span class="material-icons mr-1" style="font-size:16px;">download</span>Export
                    </button>
                </div>
                <div class="chart-container" id="departmentChart"></div>
            </div>
        </div>
    </div>

    <!-- Top Earners Table -->
    <div class="row">
        <div class="col-lg-12">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><span class="material-icons mr-2">emoji_events</span>Top 10 Earners</h5>
                    <div class="export-buttons">
                        <button class="btn btn-sm btn-outline-success" id="exptopearn">
                            <span class="material-icons mr-1" style="font-size:16px;">table_view</span>Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger" id="exptoppdf">
                            <span class="material-icons mr-1" style="font-size:16px;">picture_as_pdf</span>PDF
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-analytics" id="topEarnersTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Agent ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th class="text-right">Gross Pay</th>
                                <th class="text-right">Net Pay</th>
                            </tr>
                        </thead>
                        <tbody id="topEarnersBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    <span class="material-icons mr-2" style="font-size:16px;vertical-align:middle;">info</span>Load dashboard data to view top earners
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Breakdown Table -->
    <div class="row">
        <div class="col-lg-12">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><span class="material-icons mr-2">corporate_fare</span>Department Breakdown</h5>
                    <div class="export-buttons">
                        <button class="btn btn-sm btn-outline-success" id="expdeptex">
                            <span class="material-icons mr-1" style="font-size:16px;">table_view</span>Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger" id="expdeptpdf">
                            <span class="material-icons mr-1" style="font-size:16px;">picture_as_pdf</span>PDF
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-analytics" id="departmentTable">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th class="text-center">Agents</th>
                                <th class="text-right">Total Gross Pay</th>
                                <th class="text-right">Total Net Pay</th>
                                <th class="text-right">Average Net Pay</th>
                            </tr>
                        </thead>
                        <tbody id="departmentBody">
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <span class="material-icons mr-2" style="font-size:16px;vertical-align:middle;">info</span>Load dashboard data to view department breakdown
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@vite(['resources/js/analysis.js'])

</x-custom-admin-layout>