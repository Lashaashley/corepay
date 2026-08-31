document.addEventListener('DOMContentLoaded', function () {

    const tabBtns  = document.querySelectorAll('.tab-btn');
    const panels   = document.querySelectorAll('.tab-panel');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('panel-' + btn.dataset.tab).classList.add('active');
        });
    });

   $.ajax({
        url: App.routes.getagentslist,
        type: "GET",
        success: function (response) {
            const dropdown = $('#filterVendor');
            dropdown.empty();
            dropdown.append('<option value="">Select Agent</option>');
            response.data.forEach(function (staff) {
                const $option = $('<option>')
                .val(staff.WorkNo)           
                .text(staff.fullname); 
                dropdown.append($option);
            });
        },
        error: function () {
            alert('Failed to load Agents. Please try again.');
        },
    }); 
    $.ajax({
        url: App.routes.getagentslist,
        type: "GET",
        success: function (response) {
            const dropdown = $('#filterVendorded');
            dropdown.empty();
            dropdown.append('<option value="">Select Agent</option>');
            response.data.forEach(function (staff) {
                const $option = $('<option>')
                .val(staff.WorkNo)           
                .text(staff.fullname); 
                dropdown.append($option);
            });
        },
        error: function () {
            alert('Failed to load Agents. Please try again.');
        },
    }); 
});

(function () {
    const bridge = document.getElementById('commissions-data-bridge');
    if (!bridge) return;

    const endpoint = bridge.dataset.endpoint;
    let netPayChart = null;
    let notPaidChart = null;

    const fmt = (n) => (Number(n) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function getFilters() {
        return {
            portfolio_id: document.getElementById('filterPortfolio').value,
            work_no: document.getElementById('filterVendor').value,
            status: document.getElementById('filterStatus').value,
            month: document.getElementById('filterMonth').value,
            year: document.getElementById('filterYear').value,
        };
    }

    function setLoadingState() {
        document.getElementById('agingTableBody').innerHTML = '<tr><td colspan="4" class="table-empty">Loading…</td></tr>';
        document.getElementById('listingTableBody').innerHTML = '<tr><td colspan="7" class="table-empty">Loading…</td></tr>';
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
                renderTrendCharts(res.net_pay_trend, res.not_paid_trend);
                renderAgingTable(res.aging);
                renderListingTable(res.listing);
                renderStatCards(res.listing, res.aging);
            })
            .catch((err) => {
                console.error('Commissions dashboard load error:', err);
                document.getElementById('agingTableBody').innerHTML =
                    '<tr><td colspan="4" class="table-empty table-error">Could not load data.</td></tr>';
                document.getElementById('listingTableBody').innerHTML =
                    '<tr><td colspan="7" class="table-empty table-error">Could not load data.</td></tr>';
            });
    }

    function renderTrendCharts(netPayTrend, notPaidTrend) {
        const months = Object.keys(netPayTrend);

        const netPayOptions = {
            chart: { type: 'column' },
            title: { text: null },
            xAxis: { categories: months },
            yAxis: { title: { text: 'KES' } },
            series: [{ name: 'Net Pay', data: Object.values(netPayTrend), color: '#6c5ce7' }],
            credits: { enabled: false },
        };

        const notPaidOptions = {
            chart: { type: 'column' },
            title: { text: null },
            xAxis: { categories: Object.keys(notPaidTrend) },
            yAxis: { title: { text: 'KES' } },
            series: [{ name: 'Not Paid', data: Object.values(notPaidTrend), color: '#e74c3c' }],
            credits: { enabled: false },
        };

        if (netPayChart) netPayChart.destroy();
        if (notPaidChart) notPaidChart.destroy();

        netPayChart = Highcharts.chart('netPayTrendContainer', netPayOptions);
        notPaidChart = Highcharts.chart('notPaidTrendContainer', notPaidOptions);
    }

    function renderAgingTable(aging) {
        const tbody = document.getElementById('agingTableBody');
        const labels = Object.keys(aging);

        if (labels.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="table-empty">No outstanding balances.</td></tr>';
            return;
        }

        let totalInvoices = 0, totalGross = 0, totalNet = 0;
        let rows = '';

        labels.forEach((label) => {
            const b = aging[label];
            totalInvoices += b.invoices;
            totalGross += b.gross;
            totalNet += b.net;

            const rowClass = label === '0-3 months' ? '' : (label === '9-12+ months' ? 'aging-critical' : 'aging-warning');

            rows += `
                <tr class="${rowClass}">
                    <td>${label}</td>
                    <td>${b.invoices}</td>
                    <td>${fmt(b.gross)}</td>
                    <td>${fmt(b.net)}</td>
                </tr>`;
        });

        rows += `
            <tr class="table-total-row">
                <td>Total</td>
                <td>${totalInvoices}</td>
                <td>${fmt(totalGross)}</td>
                <td>${fmt(totalNet)}</td>
            </tr>`;

        tbody.innerHTML = rows;
    }

    function renderListingTable(listing) {
    const tbody = document.getElementById('listingTableBody');
    const COLSPAN = 14;

    if (!listing || listing.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${COLSPAN}" class="table-empty">No invoiced payments for this period.</td></tr>`;
        return;
    }

    let totalGross = 0, totalWhtax = 0, totalCommAdv = 0, totalNet = 0, totalPaid = 0;
    let rows = '';

    listing.forEach((row) => {
        totalGross += Number(row.gross_amount) || 0;
        totalWhtax += Number(row.WHTAX) || 0;
        totalCommAdv += Number(row.COMM_ADV) || 0;
        totalNet += Number(row.net_amount) || 0;
        totalPaid += Number(row.amountpaid) || 0;

        const statusClass = row.payment_status === 'PAID' ? 'status-paid'
            : row.payment_status === 'TO BE PAID' ? 'status-tobepaid'
            : 'status-notpaid';

        // Aging only applies to unpaid rows — PAID rows carry null for both
        const agingClass = row.Agingcategory === '9-12+ months' ? 'aging-critical'
            : (row.Agingcategory && row.Agingcategory !== '0-3 months') ? 'aging-warning'
            : '';

        rows += `
            <tr class="${agingClass}">
                <td>${row.vendor_name ?? ''}</td>
                <td>${row.PIN_NUMBER ?? '—'}</td>
                <td>${row.invoice_num ?? '—'}</td>
                <td>${row.invoice_date ? new Date(row.invoice_date).toLocaleDateString() : '—'}</td>
                <td>${row.portfolio ?? ''}</td>
                <td>${fmt(row.gross_amount)}</td>
                <td>${fmt(row.WHTAX)}</td>
                <td>${fmt(row.COMM_ADV)}</td>
                <td>${fmt(row.net_amount)}</td>
                <td>${fmt(row.amountpaid)}</td>
                <td>${row.PAYMENT_DATE ? new Date(row.PAYMENT_DATE).toLocaleDateString() : '—'}</td>
                <td><span class="status-pill ${statusClass}">${row.payment_status}</span></td>
                <td>${row.Age_days ?? '—'}</td>
                <td>${row.Agingcategory ?? '—'}</td>
            </tr>`;
    });

    rows += `
        <tr class="table-total-row">
            <td colspan="5">Total</td>
            <td>${fmt(totalGross)}</td>
            <td>${fmt(totalWhtax)}</td>
            <td>${fmt(totalCommAdv)}</td>
            <td>${fmt(totalNet)}</td>
            <td>${fmt(totalPaid)}</td>
            <td colspan="4"></td>
        </tr>`;

    tbody.innerHTML = rows;
}

    function renderStatCards(listing, aging) {
        const invoicedRows = (listing || []).filter(r => r.payment_status !== 'UNPAID');
        const paidRows = (listing || []).filter(r => r.payment_status === 'PAID');
        const notInvoicedRows = (listing || []).filter(r => r.payment_status === 'UNPAID');

        const invoicedTotal = invoicedRows.reduce((s, r) => s + (Number(r.net_amount) || 0), 0);
        const paidTotal = paidRows.reduce((s, r) => s + (Number(r.net_amount) || 0), 0);
        const notInvoicedTotal = notInvoicedRows.reduce((s, r) => s + (Number(r.net_amount) || 0), 0);

        const outstandingTotal = Object.values(aging || {}).reduce((s, b) => s + (Number(b.net) || 0), 0);

        document.getElementById('statInvoiced').textContent = fmt(invoicedTotal);
        document.getElementById('statInvoicedCount').textContent = `${invoicedRows.length} agents`;

        document.getElementById('statNotInvoiced').textContent = fmt(notInvoicedTotal);
        document.getElementById('statNotInvoicedCount').textContent = `${notInvoicedRows.length} agents`;

        document.getElementById('statOutstanding').textContent = fmt(outstandingTotal);

        document.getElementById('statPaid').textContent = fmt(paidTotal);
        document.getElementById('statPaidCount').textContent = `${paidRows.length} agents`;
    }

    document.getElementById('filterApplyBtn').addEventListener('click', fetchDashboardData);

    // Initial load
    fetchDashboardData();
})();