<x-custom-admin-layout>
    <!-- Replace your current Highcharts scripts with these -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/highcharts.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/exporting.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/accessibility.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/series-label.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/export-data.min.js"></script>
<script src="https://code.highcharts.com/11.4.1/modules/csp.js"></script>
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
                    <button class="btn btn-sm btn-outline-primary" onclick="exportChart('paymentBreakdownChart', 'payment-breakdown')">
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
                    <button class="btn btn-sm btn-outline-primary" onclick="exportChart('deductionBreakdownChart', 'deduction-breakdown')">
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
                    <button class="btn btn-sm btn-outline-primary" onclick="exportChart('trendChart', 'monthly-trend')">
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
                    <button class="btn btn-sm btn-outline-primary" onclick="exportChart('departmentChart', 'department-overview')">
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
                        <button class="btn btn-sm btn-outline-success" onclick="exportTableToExcel('topEarnersTable', 'top-earners')">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="exportTableToPDF('topEarnersTable', 'top-earners')">
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
                        <button class="btn btn-sm btn-outline-success" onclick="exportTableToExcel('departmentTable', 'department-breakdown')">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="exportTableToPDF('departmentTable', 'department-breakdown')">
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

<script nonce="{{ $cspNonce }}">
// Global variables
let currentDashboardData = null;
let charts = {};

$(document).ready(function() {
    // Initialize
    initializeDashboard();
    
    // Event listeners
    $('#analysisMode').on('change', handleModeChange);
    $('#loadDashboardBtn').on('click', loadDashboard);
    
    // Load initial dashboard
    loadDashboard();
});

/**
 * Initialize dashboard
 */
function initializeDashboard() {
    console.log('Dashboard initialized');
}

/**
 * Handle analysis mode change
 */
function handleModeChange() {
    const mode = $('#analysisMode').val();
    
    // Hide all filter sections first
    $('#comparisonFilters').hide();
    $('#rangeFilters').hide();
    $('#monthSelector').show();
    $('#yearSelector').show();
    
    // Show relevant filters based on mode
    switch(mode) {
        case 'comparison':
            $('#comparisonFilters').slideDown();
            $('#monthSelector').hide();
            $('#yearSelector').hide();
            break;
        case 'range':
            $('#rangeFilters').slideDown();
            $('#monthSelector').hide();
            $('#yearSelector').hide();
            break;
        case 'trend':
            $('#monthSelector').hide();
            break;
        case 'single':
        default:
            // Single period mode - default filters shown
            break;
    }
}

/**
 * Load dashboard based on selected mode
 */
function loadDashboard() {
    const mode = $('#analysisMode').val();
    
    switch(mode) {
        case 'comparison':
            loadComparisonData();
            break;
        case 'range':
            loadRangeData();
            break;
        case 'trend':
            loadTrendData();
            break;
        case 'single':
        default:
            loadSinglePeriodData();
            break;
    }
}

/**
 * Load single period data
 */
function loadSinglePeriodData() {
    const month = $('#selectedMonth').val();
    const year = $('#selectedYear').val();
    
    if (!month || !year) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please select both month and year'
        });
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: '{{ route("analytics.dashboard.data") }}',
        method: 'POST',
        data: {
            month: month,
            year: year,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                currentDashboardData = response.data;
                updateDashboard(response.data);
               
            } else {
                showError(response.message);
            }
        },
        error: function(xhr) {
            showError('Failed to load dashboard data');
        },
        complete: function() {
            hideLoading();
        }
    });
}

/**
 * Load comparison data
 */
function loadComparisonData() {
    const period1 = {
        month: $('#month1').val(),
        year: $('#year1').val()
    };
    
    const period2 = {
        month: $('#month2').val(),
        year: $('#year2').val()
    };
    
    if (!period1.month || !period1.year || !period2.month || !period2.year) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please select both periods for comparison'
        });
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: '{{ route("analytics.compare.periods") }}',
        method: 'POST',
        data: {
            period1: period1,
            period2: period2,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                updateComparisonDashboard(response.data);
                updatePeriodBadge(period1.month + ' ' + period1.year + ' vs ' + period2.month + ' ' + period2.year);
            } else {
                showError(response.message);
            }
        },
        error: function(xhr) {
            showError('Failed to load comparison data');
        },
        complete: function() {
            hideLoading();
        }
    });
}

/**
 * Load range data
 */
function loadRangeData() {
    const startMonth = $('#startMonth').val();
    const startYear = $('#startYear').val();
    const endMonth = $('#endMonth').val();
    const endYear = $('#endYear').val();
    
    if (!startMonth || !startYear || !endMonth || !endYear) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please select start and end periods'
        });
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: '{{ route("analytics.date.range") }}',
        method: 'POST',
        data: {
            start_month: startMonth,
            start_year: startYear,
            end_month: endMonth,
            end_year: endYear,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                updateRangeDashboard(response.data);
                updatePeriodBadge(startMonth + ' ' + startYear + ' to ' + endMonth + ' ' + endYear);
            } else {
                showError(response.message);
            }
        },
        error: function(xhr) {
            showError('Failed to load range data');
        },
        complete: function() {
            hideLoading();
        }
    });
}

/**
 * Load trend data (yearly)
 */
function loadTrendData() {
    const year = $('#selectedYear').val();
    
    if (!year) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please select a year'
        });
        return;
    }
    
    // Use single period data but focus on trend chart
    loadSinglePeriodData();
}

/**
 * Update dashboard with data
 */
function updateDashboard(data) {
    // Update summary statistics
    updateSummaryStats(data.summary);
    
    // Update charts
    renderPaymentBreakdownChart(data.paymentBreakdown);
    renderDeductionBreakdownChart(data.deductionBreakdown);
    renderTrendChart(data.monthlyTrend);
    renderDepartmentChart(data.departmentBreakdown);
    
    // Update tables
    updateTopEarnersTable(data.topEarners);
    updateDepartmentTable(data.departmentBreakdown);
}

/**
 * Update summary statistics
 */
function updateSummaryStats(summary) {
    $('#totalGrossPay').text('KES ' + formatNumber(summary.total_gross_pay));
    $('#totalDeductions').text('KES ' + formatNumber(summary.total_deductions));
    $('#totalNetPay').text('KES ' + formatNumber(summary.total_net_pay));
    $('#totalEmployees').text(formatNumber(summary.employee_count));
    $('#deductionRate').text(summary.deduction_rate + '%');
    $('#averageNetPay').text('KES ' + formatNumber(summary.average_net_pay));
    $('#totalPayments').text('KES ' + formatNumber(summary.total_payments));
}

/**
 * Render Payment Breakdown Pie Chart
 */
function renderPaymentBreakdownChart(data) {
    const chartData = data.map(item => ({
        name: item.name,
        y: parseFloat(item.value)
    }));
    
    if (charts.paymentBreakdown) {
        charts.paymentBreakdown.destroy();
    }
    
    charts.paymentBreakdown = Highcharts.chart('paymentBreakdownChart', {
        chart: {
            type: 'pie',
            height: 400
        },
        title: {
            text: null
        },
        tooltip: {
            pointFormat: '<b>KES {point.y:,.2f}</b><br/>({point.percentage:.1f}%)'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f}%',
                    distance: 10
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Amount',
            colorByPoint: true,
            data: chartData
        }],
        credits: {
            enabled: false
        }
    });
}

/**
 * Render Deduction Breakdown Pie Chart
 */
function renderDeductionBreakdownChart(data) {
    const chartData = data.map(item => ({
        name: item.name,
        y: parseFloat(item.value),
        category: item.category
    }));
    
    if (charts.deductionBreakdown) {
        charts.deductionBreakdown.destroy();
    }
    
    charts.deductionBreakdown = Highcharts.chart('deductionBreakdownChart', {
        chart: {
            type: 'pie',
            height: 400
        },
        title: {
            text: null
        },
        tooltip: {
            pointFormat: '<b>KES {point.y:,.2f}</b><br/>({point.percentage:.1f}%)<br/>Category: {point.category}'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f}%',
                    distance: 10
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Amount',
            colorByPoint: true,
            data: chartData
        }],
        credits: {
            enabled: false
        }
    });
}

/**
 * Render Monthly Trend Chart
 */
function renderTrendChart(data) {
    const months = data.map(item => item.month);
    const payments = data.map(item => parseFloat(item.payments));
    const deductions = data.map(item => parseFloat(item.deductions));
    const netPay = data.map(item => parseFloat(item.net_pay));
    
    if (charts.trend) {
        charts.trend.destroy();
    }
    
    charts.trend = Highcharts.chart('trendChart', {
        chart: {
            type: 'line',
            height: 400
        },
        title: {
            text: null
        },
        xAxis: {
            categories: months
        },
        yAxis: {
            title: {
                text: 'Amount (KES)'
            },
            labels: {
                formatter: function() {
                    return 'KES ' + Highcharts.numberFormat(this.value, 0, '.', ',');
                }
            }
        },
        tooltip: {
            shared: true,
            valuePrefix: 'KES ',
            valueSuffix: '',
            valueDecimals: 2
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: false
                },
                enableMouseTracking: true
            }
        },
        series: [{
            name: 'Payments',
            data: payments,
            color: '#2ecc71'
        }, {
            name: 'Deductions',
            data: deductions,
            color: '#e74c3c'
        }, {
            name: 'Net Pay',
            data: netPay,
            color: '#3498db'
        }],
        credits: {
            enabled: false
        }
    });
}

/**
 * Render Department Chart
 */
function renderDepartmentChart(data) {
    const departments = data.map(item => item.department);
    const netPays = data.map(item => parseFloat(item.total_net_pay));
    
    if (charts.department) {
        charts.department.destroy();
    }
    
    charts.department = Highcharts.chart('departmentChart', {
        chart: {
            type: 'bar',
            height: 400
        },
        title: {
            text: null
        },
        xAxis: {
            categories: departments,
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total Net Pay (KES)',
                align: 'high'
            },
            labels: {
                overflow: 'justify',
                formatter: function() {
                    return 'KES ' + Highcharts.numberFormat(this.value, 0, '.', ',');
                }
            }
        },
        tooltip: {
            valueSuffix: ' KES',
            valueDecimals: 2
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return 'KES ' + Highcharts.numberFormat(this.y, 0, '.', ',');
                    }
                }
            }
        },
        legend: {
            enabled: false
        },
        series: [{
            name: 'Net Pay',
            data: netPays,
            color: '#9b59b6'
        }],
        credits: {
            enabled: false
        }
    });
}

/**
 * Update Top Earners Table
 */
function updateTopEarnersTable(data) {
    const tbody = $('#topEarnersBody');
    tbody.empty();
    
    if (data.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="6" class="text-center text-muted">
                    <i class="fas fa-inbox mr-2"></i>No data available
                </td>
            </tr>
        `);
        return;
    }
    
    data.forEach((item, index) => {
        tbody.append(`
            <tr>
                <td><span class="badge badge-primary">${index + 1}</span></td>
                <td>${item.work_no}</td>
                <td><strong>${item.name}</strong></td>
                <td>${item.department || 'N/A'}</td>
                <td class="text-right">KES ${formatNumber(item.gross_pay)}</td>
                <td class="text-right"><strong>KES ${formatNumber(item.net_pay)}</strong></td>
            </tr>
        `);
    });
}

/**
 * Update Department Table
 */
function updateDepartmentTable(data) {
    const tbody = $('#departmentBody');
    tbody.empty();
    
    if (data.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="5" class="text-center text-muted">
                    <i class="fas fa-inbox mr-2"></i>No data available
                </td>
            </tr>
        `);
        return;
    }
    
    data.forEach(item => {
        tbody.append(`
            <tr>
                <td><strong>${item.department}</strong></td>
                <td class="text-center"><span class="badge badge-info">${item.employee_count}</span></td>
                <td class="text-right">KES ${formatNumber(item.total_gross_pay)}</td>
                <td class="text-right">KES ${formatNumber(item.total_net_pay)}</td>
                <td class="text-right">KES ${formatNumber(item.average_net_pay)}</td>
            </tr>
        `);
    });
    
    // Add total row
    const totalEmployees = data.reduce((sum, item) => sum + item.employee_count, 0);
    const totalGross = data.reduce((sum, item) => sum + parseFloat(item.total_gross_pay), 0);
    const totalNet = data.reduce((sum, item) => sum + parseFloat(item.total_net_pay), 0);
    
    tbody.append(`
        <tr class="table-active font-weight-bold">
            <td>TOTAL</td>
            <td class="text-center">${totalEmployees}</td>
            <td class="text-right">KES ${formatNumber(totalGross)}</td>
            <td class="text-right">KES ${formatNumber(totalNet)}</td>
            <td class="text-right">-</td>
        </tr>
    `);
}

/**
 * Update comparison dashboard
 */
function updateComparisonDashboard(data) {
    // Update summary with comparison data
    const period1 = data.period1.data;
    const period2 = data.period2.data;
    const variance = data.variance;
    const percentChange = data.percentage_change;
    
    // Update stat cards with comparison
    $('#totalGrossPay').html(`
        <div>Period 1: KES ${formatNumber(period1.total_gross_pay)}</div>
        <div>Period 2: KES ${formatNumber(period2.total_gross_pay)}</div>
    `);
    $('#grossPayChange').html(`
        <i class="fas fa-${percentChange.gross_pay >= 0 ? 'arrow-up' : 'arrow-down'} mr-1"></i>
        ${percentChange.gross_pay}%
    `).removeClass('positive negative').addClass(percentChange.gross_pay >= 0 ? 'positive' : 'negative');
    
    $('#totalDeductions').html(`
        <div>Period 1: KES ${formatNumber(period1.total_deductions)}</div>
        <div>Period 2: KES ${formatNumber(period2.total_deductions)}</div>
    `);
    $('#deductionsChange').html(`
        <i class="fas fa-${percentChange.deductions >= 0 ? 'arrow-up' : 'arrow-down'} mr-1"></i>
        ${percentChange.deductions}%
    `).removeClass('positive negative').addClass(percentChange.deductions >= 0 ? 'positive' : 'negative');
    
    $('#totalNetPay').html(`
        <div>Period 1: KES ${formatNumber(period1.total_net_pay)}</div>
        <div>Period 2: KES ${formatNumber(period2.total_net_pay)}</div>
    `);
    $('#netPayChange').html(`
        <i class="fas fa-${percentChange.net_pay >= 0 ? 'arrow-up' : 'arrow-down'} mr-1"></i>
        ${percentChange.net_pay}%
    `).removeClass('positive negative').addClass(percentChange.net_pay >= 0 ? 'positive' : 'negative');
    
    $('#totalEmployees').html(`
        <div>Period 1: ${formatNumber(period1.employee_count)}</div>
        <div>Period 2: ${formatNumber(period2.employee_count)}</div>
    `);
    $('#employeesChange').html(`
        <i class="fas fa-${variance.employee_count >= 0 ? 'arrow-up' : 'arrow-down'} mr-1"></i>
        ${variance.employee_count}
    `);
    
    // Render comparison chart
    renderComparisonChart(data);
}

/**
 * Render comparison chart
 */
function renderComparisonChart(data) {
    const period1 = data.period1.data;
    const period2 = data.period2.data;
    
    if (charts.comparison) {
        charts.comparison.destroy();
    }
    
    charts.comparison = Highcharts.chart('trendChart', {
        chart: {
            type: 'column',
            height: 400
        },
        title: {
            text: 'Period Comparison'
        },
        xAxis: {
            categories: ['Gross Pay', 'Deductions', 'Net Pay', 'Employees (x1000)']
        },
        yAxis: {
            title: {
                text: 'Amount (KES) / Count'
            }
        },
        tooltip: {
            shared: true
        },
        plotOptions: {
            column: {
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return Highcharts.numberFormat(this.y, 0, '.', ',');
                    }
                }
            }
        },
        series: [{
            name: data.period1.label,
            data: [
                period1.total_gross_pay,
                period1.total_deductions,
                period1.total_net_pay,
                period1.employee_count * 1000
            ],
            color: '#3498db'
        }, {
            name: data.period2.label,
            data: [
                period2.total_gross_pay,
                period2.total_deductions,
                period2.total_net_pay,
                period2.employee_count * 1000
            ],
            color: '#e74c3c'
        }],
        credits: {
            enabled: false
        }
    });
    
    // Clear other charts that don't apply to comparison
    $('#paymentBreakdownChart').html('<div class="text-center text-muted p-5">Not available in comparison mode</div>');
    $('#deductionBreakdownChart').html('<div class="text-center text-muted p-5">Not available in comparison mode</div>');
    $('#departmentChart').html('<div class="text-center text-muted p-5">Not available in comparison mode</div>');
}

/**
 * Update range dashboard
 */
function updateRangeDashboard(data) {
    const totals = data.totals;
    
    // Update summary stats with totals
    $('#totalGrossPay').text('KES ' + formatNumber(totals.total_payments));
    $('#totalDeductions').text('KES ' + formatNumber(totals.total_deductions));
    $('#totalNetPay').text('KES ' + formatNumber(totals.total_net_pay));
    $('#totalEmployees').text(formatNumber(totals.average_employees) + ' avg');
    
    // Render range trend chart
    renderRangeTrendChart(data.periods);
}

/**
 * Render range trend chart
 */
function renderRangeTrendChart(periods) {
    const periodLabels = periods.map(p => p.period);
    const grossPays = periods.map(p => parseFloat(p.data.total_gross_pay));
    const deductions = periods.map(p => parseFloat(p.data.total_deductions));
    const netPays = periods.map(p => parseFloat(p.data.total_net_pay));
    
    if (charts.rangeTrend) {
        charts.rangeTrend.destroy();
    }
    
    charts.rangeTrend = Highcharts.chart('trendChart', {
        chart: {
            type: 'area',
            height: 400
        },
        title: {
            text: 'Date Range Analysis'
        },
        xAxis: {
            categories: periodLabels,
            tickmarkPlacement: 'on',
            title: {
                enabled: false
            }
        },
        yAxis: {
            title: {
                text: 'Amount (KES)'
            },
            labels: {
                formatter: function() {
                    return 'KES ' + Highcharts.numberFormat(this.value, 0, '.', ',');
                }
            }
        },
        tooltip: {
            shared: true,
            valuePrefix: 'KES ',
            valueDecimals: 2
        },
        plotOptions: {
            area: {
                stacking: 'normal',
                lineColor: '#666666',
                lineWidth: 1,
                marker: {
                    lineWidth: 1,
                    lineColor: '#666666'
                }
            }
        },
        series: [{
            name: 'Gross Pay',
            data: grossPays,
            color: '#2ecc71'
        }, {
            name: 'Deductions',
            data: deductions,
            color: '#e74c3c'
        }, {
            name: 'Net Pay',
            data: netPays,
            color: '#3498db'
        }],
        credits: {
            enabled: false
        }
    });
    
    // Clear charts not applicable to range
    $('#paymentBreakdownChart').html('<div class="text-center text-muted p-5">Not available in range mode</div>');
    $('#deductionBreakdownChart').html('<div class="text-center text-muted p-5">Not available in range mode</div>');
    $('#departmentChart').html('<div class="text-center text-muted p-5">Not available in range mode</div>');
}

/**
 * Export chart as image
 */
function exportChart(chartId, filename) {
    const chartElement = document.getElementById(chartId);
    
    if (!chartElement) {
        Swal.fire({
            icon: 'error',
            title: 'Export Failed',
            text: 'Chart not found'
        });
        return;
    }
    
    // Find the Highcharts instance
    const chart = Highcharts.charts.find(c => c && c.renderTo.id === chartId);
    
    if (chart) {
        chart.exportChart({
            type: 'image/png',
            filename: filename
        });
    }
}

/**
 * Export table to Excel
 */
function exportTableToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    
    if (!table) {
        Swal.fire({
            icon: 'error',
            title: 'Export Failed',
            text: 'Table not found'
        });
        return;
    }
    
    // Simple Excel export using HTML table
    const html = table.outerHTML;
    const blob = new Blob([html], {
        type: 'application/vnd.ms-excel'
    });
    
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename + '.xls';
    link.click();
}

/**
 * Export table to PDF
 */
function exportTableToPDF(tableId, filename) {
    Swal.fire({
        icon: 'info',
        title: 'PDF Export',
        text: 'PDF export feature coming soon! Please use Excel export for now.'
    });
}

/**
 * Update period badge
 */


/**
 * Format number with commas
 */
function formatNumber(num) {
    if (num === null || num === undefined) return '0.00';
    return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

/**
 * Show loading overlay
 */
function showLoading() {
    $('#loadingOverlay').addClass('active');
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    $('#loadingOverlay').removeClass('active');
}

/**
 * Show error message
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message
    });
}
</script>
</x-custom-admin-layout>