<x-custom-admin-layout>
    <!-- Replace your current Highcharts scripts with these -->

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

    <!-- Header -->
    

    <!-- Filters -->
    <div class="filter-card">
        <h5><i class="fas fa-filter mr-2"></i>Dashboard Filters</h5>
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
                        <i class="fas fa-sync-alt mr-2"></i>Load Dashboard
                    </button>
                </div>
            </div>
        </div>

        <!-- Comparison Mode Filters (Hidden by default) -->
        <div id="comparisonFilters" class="hidden">
            <div class="comparison-mode">
                <h6><i class="fas fa-exchange-alt mr-2"></i>Period Comparison Mode</h6>
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

        <!-- Range Mode Filters (Hidden by default) -->
        <div id="rangeFilters" class="hidden">
            <div class="comparison-mode backcolor">
                <h6><i class="fas fa-calendar-range mr-2"></i>Date Range Analysis</h6>
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
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-label">Total Gross Pay</div>
                <div class="stat-value" id="totalGrossPay">KES 0.00</div>
                <div class="stat-change positive" id="grossPayChange">
                    <i class="fas fa-arrow-up mr-1"></i>0%
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-minus-circle"></i>
                </div>
                <div class="stat-label">Total Deductions</div>
                <div class="stat-value" id="totalDeductions">KES 0.00</div>
                <div class="stat-change negative" id="deductionsChange">
                    <i class="fas fa-arrow-down mr-1"></i>0%
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-label">Total Net Pay</div>
                <div class="stat-value" id="totalNetPay">KES 0.00</div>
                <div class="stat-change positive" id="netPayChange">
                    <i class="fas fa-arrow-up mr-1"></i>0%
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-label">Total AGENTS</div>
                <div class="stat-value" id="totalEmployees">0</div>
                <div class="stat-change" id="employeesChange">
                    <i class="fas fa-equals mr-1"></i>0
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-label">Deduction Rate</div>
                <div class="stat-value" id="deductionRate">0%</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-label">Average Net Pay</div>
                <div class="stat-value" id="averageNetPay">KES 0.00</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hand-holding-usd"></i>
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
                    <h5 class="mb-0"><i class="fas fa-chart-pie mr-2"></i>Payment Breakdown</h5>
                    <button class="btn btn-sm btn-outline-primary" id="exppaybreak" >
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                </div>
                <div class="chart-container" id="paymentBreakdownChart"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-chart-pie mr-2"></i>Deduction Breakdown</h5>
                    <button class="btn btn-sm btn-outline-primary" id="expdedcu">
                        <i class="fas fa-download mr-1"></i>Export
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
                    <h5 class="mb-0"><i class="fas fa-chart-line mr-2"></i>Monthly Trend Analysis</h5>
                    <button class="btn btn-sm btn-outline-primary" id="exptrendch">
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                </div>
                <div class="chart-container" id="trendChart"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Department Overview</h5>
                    <button class="btn btn-sm btn-outline-primary" id="expdeptcha">
                        <i class="fas fa-download mr-1"></i>Export
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
                    <h5 class="mb-0"><i class="fas fa-trophy mr-2"></i>Top 10 Earners</h5>
                    <div class="export-buttons">
                        <button class="btn btn-sm btn-outline-success" id="exptopearn">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger" id="exptoppdf">
                            <i class="fas fa-file-pdf mr-1"></i>PDF
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
                                    <i class="fas fa-info-circle mr-2"></i>Load dashboard data to view top earners
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
                    <h5 class="mb-0"><i class="fas fa-building mr-2"></i>Department Breakdown</h5>
                    <div class="export-buttons">
                        <button class="btn btn-sm btn-outline-success" id="expdeptex">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger" id="expdeptpdf" >
                            <i class="fas fa-file-pdf mr-1"></i>PDF
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
                                    <i class="fas fa-info-circle mr-2"></i>Load dashboard data to view department breakdown
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Highcharts -->

    
    <!-- 3. SweetAlert Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



@vite(['resources/js/analysis.js'])

</x-custom-admin-layout>