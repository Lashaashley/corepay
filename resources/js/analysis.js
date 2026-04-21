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

    $('#exppaybreak').on('click', function () {
        exportChart('paymentBreakdownChart', 'payment-breakdown');
    });

    $('#expdedcu').on('click', function () {
       exportChart('deductionBreakdownChart', 'deduction-breakdown');
    });

     $('#exptrendch').on('click', function () {
       exportChart('trendChart', 'monthly-trend');
    });

     $('#expdeptcha').on('click', function () {
       exportChart('departmentChart', 'department-overview');
    });

    $('#exptopearn').on('click', function () {
       exportTableToExcel('topEarnersTable', 'top-earners');
    });

    $('#exptoppdf').on('click', function () {
      exportTableToPDF('topEarnersTable', 'top-earners');
    });

    $('#expdeptex').on('click', function () {
      exportTableToExcel('departmentTable', 'department-breakdown');
    });

    $('#expdeptpdf').on('click', function () {
      exportTableToPDF('departmentTable', 'department-breakdown');
    });
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
        url: App.routes.analdash,
        method: 'POST',
        data: {
            month: month,
            year: year
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
        url: App.routes.analcompperiod, 
        method: 'POST',
        data: {
            period1: period1,
            period2: period2
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
        url: App.routes.analdaterange,
        method: 'POST',
        data: {
            start_month: startMonth,
            start_year: startYear,
            end_month: endMonth,
            end_year: endYear
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