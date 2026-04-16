<x-custom-admin-layout>
@vite(['resources/css/pages/vaudit.css']) 

<div class="audit-page">

    <div class="page-heading">
        <h1>Audit Trail</h1>
        <p>Track and review all system activity and data changes.</p>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    {{-- Legacy alert shell kept for JS compatibility --}}
    <div id="status-message" class="alert alert-dismissible fade" role="alert" style="display:none;">
        <strong id="alert-title"></strong> <span id="alert-message"></span>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>

    {{-- ── Filter card ──────────────────────────────────────── --}}
    <div class="a-card">
        <div class="a-card-head">
            <div class="a-card-icon"><span class="material-icons">filter_list</span></div>
            <span class="a-card-title">Audit Trail Filters</span>
        </div>
        <div class="filter-body">
            <form id="auditFilterForm">

                <div class="filter-grid">

                    {{-- Report type --}}
                    <div class="field fg-3">
                        <label>Report Type <span style="color:var(--danger)">*</span></label>
                        <div class="select-wrap">
                            <select name="report_type" required>
                                <option value="">Select Type</option>
                                <option value="user_activity">User Activity</option>
                                <option value="action_type">Action Type</option>
                                <option value="record_history">Record History</option>
                                <option value="table_activity">Table Activity</option>
                                <option value="comprehensive">Comprehensive</option>
                            </select>
                        </div>
                    </div>

                    {{-- User (conditional) --}}
                    <div class="field fg-3" id="user_filter">
                        <label>User</label>
                        <div class="select-wrap">
                            <select name="user_id">
                                <option value="">All Users</option>
                            </select>
                        </div>
                    </div>

                    {{-- Action (conditional) --}}
                    <div class="field fg-3" id="action_filter">
                        <label>Action</label>
                        <div class="select-wrap">
                            <select name="action">
                                <option value="">All Actions</option>
                                <option value="INSERT">INSERT</option>
                                <option value="UPDATE">UPDATE</option>
                                <option value="DELETE">DELETE</option>
                                <option value="LOGIN">LOGIN</option>
                                <option value="LOGOUT">LOGOUT</option>
                                <option value="ERROR">ERROR</option>
                                <option value="VIEW">VIEW</option>
                            </select>
                        </div>
                    </div>

                    {{-- Table (conditional) --}}
                    <div class="field fg-3" id="table_filter">
                        <label>Table</label>
                        <div class="select-wrap">
                            <select name="table_name">
                                <option value="">All Tables</option>
                                <option value="users">Users</option>
                                <option value="prolltypes">Payroll Types</option>
                            </select>
                        </div>
                    </div>

                    {{-- Record ID (conditional) --}}
                    <div class="field fg-3" id="record_filter">
                        <label>Record ID</label>
                        <input type="text" name="record_id" placeholder="Enter Record ID">
                    </div>

                    {{-- From date --}}
                    <div class="field fg-3">
                        <label>From Date <span style="color:var(--danger)">*</span></label>
                        <input type="date" name="from_date" required>
                    </div>

                    {{-- To date --}}
                    <div class="field fg-3">
                        <label>To Date <span style="color:var(--danger)">*</span></label>
                        <input type="date" name="to_date" required>
                    </div>

                    {{-- Quick range --}}
                    <div class="field fg-3">
                        <label>Quick Range</label>
                        <div class="select-wrap">
                            <select id="quick_range">
                                <option value="">Custom</option>
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="last7">Last 7 Days</option>
                                <option value="last30">Last 30 Days</option>
                                <option value="thismonth">This Month</option>
                                <option value="lastmonth">Last Month</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-search">
                        <span class="material-icons">search</span> View Report
                    </button>
                    <button type="button" class="btn btn-excel" id="export-excel">
                        <span class="material-icons">table_view</span> Export Excel
                    </button>
                    <button type="button" class="btn btn-pdf-btn" id="export-pdf">
                        <span class="material-icons">picture_as_pdf</span> Export PDF
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ── Results card ─────────────────────────────────────── --}}
    <div class="a-card">
        <div class="a-card-head">
            <div class="a-card-icon purple"><span class="material-icons">history</span></div>
            <span class="a-card-title">Audit Results</span>
            <span class="record-count" id="recordCount" style="margin-left:auto;"></span>
        </div>

        {{-- Custom toolbar --}}
        <div class="table-toolbar">
            <div class="toolbar-left">
                <div class="search-box">
                    <span class="material-icons">search</span>
                    <input type="text" id="dt-search" placeholder="Search results…">
                </div>
            </div>
            <div class="toolbar-right">
                <select id="dt-length" class="page-length-select">
                    <option value="25">25 / page</option>
                    <option value="50" selected>50 / page</option>
                    <option value="100">100 / page</option>
                    <option value="-1">All</option>
                </select>
            </div>
        </div>

        <div class="table-wrap">
            <table id="audit-table" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Date &amp; Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Record ID</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>{{-- /audit-page --}}

{{-- ── PDF Preview Modal ────────────────────────────────────── --}}
<div class="pdf-backdrop" id="pdfPreviewModal">
    <div class="pdf-modal">
        <div class="pdf-modal-head">
            <div class="a-card-icon" style="background:var(--danger-lt);">
                <span class="material-icons" style="color:var(--danger)">picture_as_pdf</span>
            </div>
            <span class="pdf-modal-title">Audit Trail — PDF Preview</span>
            <button class="btn-dl" id="downloadPdfBtn">
                <span class="material-icons">download</span> Download
            </button>
            <button class="btn-icon" id="closePdfModal" style="margin-left:6px;">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="pdf-modal-body" id="pdfContainer">
            <div class="pdf-loading" id="pdfLoading">
                <span class="material-icons">sync</span>
                <span>Loading PDF preview…</span>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>

<script nonce="{{ $cspNonce }}">
/* ── DataTable ───────────────────────────────────────────── */
var auditTable = $('#audit-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("audit.getData") }}',
        type: 'GET',
        data: function(d) {
            d.report_type = $('select[name="report_type"]').val();
            d.user_id     = $('select[name="user_id"]').val();
            d.action      = $('select[name="action"]').val();
            d.table_name  = $('select[name="table_name"]').val();
            d.record_id   = $('input[name="record_id"]').val();
            d.from_date   = $('input[name="from_date"]').val();
            d.to_date     = $('input[name="to_date"]').val();
        }
    },
    columns: [
        {
            data: null, orderable: false,
            className: 'details-control',
            defaultContent: '<span class="material-icons">add_circle_outline</span>',
            width: '36px'
        },
        { data: 'id', title: 'ID' },
        {
            data: 'created_at', title: 'Date & Time',
            render: function(data) {
                return moment ? moment(data).format('YYYY-MM-DD HH:mm:ss') : data;
            }
        },
        {
            data: 'user_name', title: 'User',
            render: function(data, type, row) {
                return data + ' <span style="color:var(--muted);font-size:11.5px;">(#' + row.user_id + ')</span>';
            }
        },
        {
            data: 'action', title: 'Action',
            render: function(data) {
                return '<span class="action-badge ' + data + '">' + data + '</span>';
            }
        },
        { data: 'table_name', title: 'Table' },
        { data: 'record_id',  title: 'Record ID' },
        { data: 'ip_address', title: 'IP' }
    ],
    order: [[1, 'desc']],
    pageLength: 50,
    dom: 'rtp',
    language: {
        processing: '<span style="color:var(--muted);font-size:13px;padding:20px;display:block;">Loading…</span>',
        emptyTable:  'No audit records found. Apply filters and click View Report.',
        zeroRecords: 'No records match your search.'
    },
    drawCallback: function() {
        var info    = this.api().page.info();
        var total   = info.recordsTotal.toLocaleString();
        var display = info.recordsDisplay.toLocaleString();
        document.getElementById('recordCount').textContent =
            info.recordsTotal === info.recordsDisplay
                ? total + ' records'
                : display + ' of ' + total + ' records';
    }
});

/* ── Custom search + page length ─────────────────────────── */
var searchTimer;
document.getElementById('dt-search').addEventListener('input', function() {
    clearTimeout(searchTimer);
    var val = this.value;
    searchTimer = setTimeout(function() { auditTable.search(val).draw(); }, 350);
});

document.getElementById('dt-length').addEventListener('change', function() {
    auditTable.page.len(parseInt(this.value)).draw();
});

/* ── Form submit → reload ────────────────────────────────── */
$('#auditFilterForm').on('submit', function(e) {
    e.preventDefault();
    auditTable.ajax.reload();
});

/* ── Expandable row ──────────────────────────────────────── */
$('#audit-table tbody').on('click', 'td.details-control', function() {
    var tr   = $(this).closest('tr');
    var row  = auditTable.row(tr);
    var icon = $(this).find('.material-icons');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
        icon.text('add_circle_outline');
    } else {
        row.child(formatDetails(row.data())).show();
        tr.addClass('shown');
        icon.text('remove_circle_outline');
    }
});

function formatDetails(d) {
    var oldValues   = d.old_values   ? JSON.parse(d.old_values)   : {};
    var newValues   = d.new_values   ? JSON.parse(d.new_values)   : {};
    var contextData = d.context_data ? JSON.parse(d.context_data) : {};
    var allKeys     = new Set(Object.keys(oldValues).concat(Object.keys(newValues)));

    var html = '<div class="detail-wrap"><div class="detail-grid">';

    // Changes table
    if (allKeys.size > 0) {
        html += '<div class="detail-section detail-full">';
        html += '<p class="detail-section-title">Changes</p>';
        html += '<table class="diff-table"><thead><tr><th>Field</th><th>Old Value</th><th>New Value</th></tr></thead><tbody>';

        allKeys.forEach(function(key) {
            var oldV    = oldValues[key] !== undefined ? oldValues[key] : '<em style="opacity:.45">—</em>';
            var newV    = newValues[key] !== undefined ? newValues[key] : '<em style="opacity:.45">—</em>';
            var changed = String(oldValues[key]) !== String(newValues[key]) ? 'changed' : '';
            html += '<tr class="' + changed + '"><td><strong>' + key + '</strong></td>'
                  + '<td class="diff-old">' + oldV + '</td>'
                  + '<td class="diff-new">' + newV + '</td></tr>';
        });

        html += '</tbody></table></div>';
    }

    // Context
    if (Object.keys(contextData).length > 0) {
        html += '<div class="detail-section">';
        html += '<p class="detail-section-title">Context</p>';
        html += '<pre class="context-pre">' + JSON.stringify(contextData, null, 2) + '</pre>';
        html += '</div>';
    }

    // User agent
    if (d.user_agent) {
        html += '<div class="detail-section">';
        html += '<p class="detail-section-title">User Agent</p>';
        html += '<p class="user-agent-txt">' + d.user_agent + '</p>';
        html += '</div>';
    }

    html += '</div></div>';
    return html;
}

/* ── Quick date range ────────────────────────────────────── */
$('#quick_range').on('change', function() {
    var val   = $(this).val();
    var today = new Date();
    var fmt   = function(d) { return d.toISOString().split('T')[0]; };
    var from  = $('input[name="from_date"]');
    var to    = $('input[name="to_date"]');

    if (val === 'today') {
        from.val(fmt(today)); to.val(fmt(today));
    } else if (val === 'yesterday') {
        var y = new Date(today); y.setDate(y.getDate() - 1);
        from.val(fmt(y)); to.val(fmt(y));
    } else if (val === 'last7') {
        var d7 = new Date(today); d7.setDate(d7.getDate() - 6);
        from.val(fmt(d7)); to.val(fmt(today));
    } else if (val === 'last30') {
        var d30 = new Date(today); d30.setDate(d30.getDate() - 29);
        from.val(fmt(d30)); to.val(fmt(today));
    } else if (val === 'thismonth') {
        from.val(fmt(new Date(today.getFullYear(), today.getMonth(), 1)));
        to.val(fmt(today));
    } else if (val === 'lastmonth') {
        from.val(fmt(new Date(today.getFullYear(), today.getMonth() - 1, 1)));
        to.val(fmt(new Date(today.getFullYear(), today.getMonth(), 0)));
    }
});

/* ── Conditional filter show/hide ────────────────────────── */
$('select[name="report_type"]').on('change', function() {
    var val = $(this).val();
    $('#user_filter').toggle(val === 'user_activity' || val === 'comprehensive');
    $('#action_filter').toggle(val === 'action_type'  || val === 'comprehensive');
    $('#table_filter').toggle(val === 'table_activity' || val === 'comprehensive');
    $('#record_filter').toggle(val === 'record_history');
});

/* ── Export Excel ────────────────────────────────────────── */
$('#export-excel').on('click', function() {
    window.location.href = '{{ route("audit.exportExcel") }}?' + $('#auditFilterForm').serialize();
});

/* ── PDF preview modal ───────────────────────────────────── */
var currentFilterData = '';

$('#export-pdf').on('click', function(e) {
    e.preventDefault();
    currentFilterData = $('#auditFilterForm').serialize();

    document.getElementById('pdfPreviewModal').classList.add('open');
    document.getElementById('pdfLoading').style.display = 'flex';

    var old = document.getElementById('pdfContainer').querySelector('iframe');
    if (old) old.remove();

    var iframe = document.createElement('iframe');
    iframe.src = '{{ route("audit.viewPdf") }}?' + currentFilterData + '#toolbar=0&navpanes=0';
    iframe.style.cssText = 'width:100%;height:100%;border:none;display:block;';
    iframe.onload = function() {
        document.getElementById('pdfLoading').style.display = 'none';
    };
    document.getElementById('pdfContainer').appendChild(iframe);
});

$('#downloadPdfBtn').on('click', function() {
    window.location.href = '{{ route("audit.exportPdf") }}?' + currentFilterData;
});

/* Close PDF modal */
function closePdfModal() {
    document.getElementById('pdfPreviewModal').classList.remove('open');
    var old = document.getElementById('pdfContainer').querySelector('iframe');
    if (old) old.remove();
    document.getElementById('pdfLoading').style.display = 'flex';
}

document.getElementById('closePdfModal').addEventListener('click', closePdfModal);

document.getElementById('pdfPreviewModal').addEventListener('click', function(e) {
    if (e.target === this) closePdfModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePdfModal();
});
</script>

</x-custom-admin-layout>