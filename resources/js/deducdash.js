(function () {
    const bridge = document.getElementById('deductions-data-bridge');
    if (!bridge) return;

    const endpoint = bridge.dataset.endpoint;
    let byTypeChart = null;
    let balancesChart = null;

    const fmt = (n) => (Number(n) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function getFilters() {
        return {
            portfolio_id: document.getElementById('filterPortfolioded').value,
            work_no: document.getElementById('filterVendorded').value,
            month: document.getElementById('filterMonthded').value,
            year: document.getElementById('filterYearded').value,
        };
    }

    function setLoadingState() {
        document.getElementById('deductionListingTableBody').innerHTML =
            '<tr><td colspan="7" class="table-empty">Loading…</td></tr>';
    }

    function fetchDashboardData() {
        setLoadingState();

        const params = new URLSearchParams(getFilters());

        fetch(`${endpoint}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then((res) => res.json())
            .then((res) => {
                if (res.status !== 'success') {
                    throw new Error(res.message || 'Failed to load dashboard data');
                }
                renderCharts(res.by_type, res.balances);
                renderListingTable(res.listing);
                renderStatCards(res.by_type, res.balances, res.listing);
            })
            .catch((err) => {
                console.error('Deductions dashboard load error:', err);
                document.getElementById('deductionListingTableBody').innerHTML =
                    '<tr><td colspan="7" class="table-empty table-error">Could not load data.</td></tr>';
            });
    }

    function renderCharts(byType, balances) {
    const byTypeOptions = {
        chart: { type: 'column' },
        title: { text: null },
        xAxis: { categories: byType.map(r => r.item_description), labels: { rotation: -20 } },
        yAxis: { title: { text: 'KES' } },
        series: [{
            name: 'Total Deducted',
            data: byType.map(r => Number(r.total_deducted) || 0),   // NEW — coerce defensively
            color: '#6c5ce7'
        }],
        credits: { enabled: false },
    };

    const balancesOptions = {
        chart: { type: 'column' },
        title: { text: null },
        xAxis: { categories: balances.map(r => r.item_description), labels: { rotation: -20 } },
        yAxis: { title: { text: 'KES' } },
        series: [{
            name: 'Outstanding Balance',
            data: balances.map(r => Number(r.total_balance) || 0),   // NEW
            color: '#f0ad4e'
        }],
        credits: { enabled: false },
    };

    if (byTypeChart) byTypeChart.destroy();
    if (balancesChart) balancesChart.destroy();

    byTypeChart = Highcharts.chart('deductionsByTypeContainer', byTypeOptions);
    balancesChart = Highcharts.chart('balancesByTypeContainer', balancesOptions);
}

    function renderListingTable(listing) {
        const tbody = document.getElementById('deductionListingTableBody');

        if (!listing || listing.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="table-empty">No deductions for this period.</td></tr>';
            return;
        }

        let totalDeducted = 0, totalBalance = 0;
        let rows = '';

        listing.forEach((row) => {
            totalDeducted += Number(row.amount_deducted) || 0;
            totalBalance += row.balance !== null ? Number(row.balance) : 0;

            rows += `
                <tr>
                    <td>${row.vendor_name ?? ''}</td>
                    <td>${row.pin_number ?? '—'}</td>
                    <td>${row.portfolio ?? ''}</td>
                    <td>${row.item_code ?? ''}</td>
                    <td>${row.item_description ?? ''}</td>
                    <td>${fmt(row.amount_deducted)}</td>
                    <td>${row.balance !== null ? fmt(row.balance) : '—'}</td>
                </tr>`;
        });

        rows += `
            <tr class="table-total-row">
                <td colspan="5">Total</td>
                <td>${fmt(totalDeducted)}</td>
                <td>${fmt(totalBalance)}</td>
            </tr>`;

        tbody.innerHTML = rows;
    }

    function renderStatCards(byType, balances, listing) {
        const totalDeducted = byType.reduce((s, r) => s + (Number(r.total_deducted) || 0), 0);
        const totalBalance = balances.reduce((s, r) => s + (Number(r.total_balance) || 0), 0);
        const uniqueEmployees = new Set((listing || []).map(r => r.vendor_name)).size;

        document.getElementById('statTotalDeducted').textContent = fmt(totalDeducted);
        document.getElementById('statDeductedCount').textContent = `${uniqueEmployees} employees`;
        document.getElementById('statTotalBalance').textContent = fmt(totalBalance);
        document.getElementById('statItemTypes').textContent = byType.length;
    }

    document.getElementById('filterApplyBtn').addEventListener('click', fetchDashboardData);

    fetchDashboardData();
})();