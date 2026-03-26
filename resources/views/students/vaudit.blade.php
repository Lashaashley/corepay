<x-custom-admin-layout>

<style nonce="{{ $cspNonce }}">
    /* ── Page ────────────────────────────────────────────────── */
    .audit-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }

    .page-heading { margin-bottom: 20px; }

    .page-heading h1 {
        font-family: var(--font-head);
        font-size: 22px; font-weight: 700; color: var(--ink); margin: 0 0 4px;
    }

    .page-heading p { font-size: 13.5px; color: var(--muted); margin: 0; }

    /* ── Shared card shell ───────────────────────────────────── */
    .a-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
        margin-bottom: 20px;
    }

    .a-card:nth-child(2) { animation-delay: .08s; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .a-card-head {
        display: flex; align-items: center; gap: 10px;
        padding: 14px 22px; border-bottom: 1px solid var(--border);
    }

    .a-card-icon {
        width: 32px; height: 32px; border-radius: 9px;
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .a-card-icon .material-icons { font-size: 16px; color: var(--accent); }
    .a-card-icon.purple { background: #f3f0ff; }
    .a-card-icon.purple .material-icons { color: #7c3aed; }

    .a-card-title { font-family: var(--font-head); font-size: 14px; font-weight: 700; color: var(--ink); }

    /* ── Filter body ─────────────────────────────────────────── */
    .filter-body { padding: 20px 22px; }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 14px 16px;
        margin-bottom: 18px;
    }

    .fg-3  { grid-column: span 3; }

    @media (max-width: 1024px) { .fg-3 { grid-column: span 6; } }
    @media (max-width: 600px)  { .fg-3 { grid-column: span 12; } }

    /* ── Fields ──────────────────────────────────────────────── */
    .field { display: flex; flex-direction: column; gap: 4px; }

    .field label { font-size: 12px; font-weight: 500; color: #374151; }

    .field input, .field select {
        height: 38px; padding: 0 11px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body);
        font-size: 13.5px; color: var(--ink); outline: none; width: 100%;
        appearance: none; -webkit-appearance: none;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }

    .field input[type="date"] { padding: 0 10px; }

    .field input:focus, .field select:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3px rgba(26,86,219,.1);
    }

    .select-wrap { position: relative; }
    .select-wrap::after {
        content: 'expand_more'; font-family: 'Material Icons'; font-size: 17px;
        position: absolute; right: 9px; top: 50%; transform: translateY(-50%);
        color: var(--muted); pointer-events: none;
    }
    .select-wrap select { padding-right: 28px; }

    /* Conditional filter fields — hidden by default */
    #user_filter, #action_filter, #table_filter, #record_filter { display: none; }

    /* ── Filter action row ───────────────────────────────────── */
    .filter-actions {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        padding-top: 16px; border-top: 1px dashed var(--border);
    }

    /* ── Buttons ─────────────────────────────────────────────── */
    .btn {
        height: 38px; padding: 0 16px; border: none; border-radius: var(--radius-sm);
        font-family: var(--font-body); font-size: 13.5px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: transform .2s, box-shadow .2s, filter .2s; letter-spacing: .01em;
        text-decoration: none; white-space: nowrap;
    }

    .btn .material-icons { font-size: 16px; }
    .btn:hover:not(:disabled) { transform: translateY(-1px); }
    .btn:active:not(:disabled) { transform: translateY(0); }

    .btn-search { background: linear-gradient(135deg, #1a56db, #4f46e5); color: #fff; box-shadow: 0 3px 10px rgba(26,86,219,.22); }
    .btn-search:hover { box-shadow: 0 6px 16px rgba(26,86,219,.32); filter: brightness(1.05); }

    .btn-excel { background: linear-gradient(135deg, #059669, #10b981); color: #fff; box-shadow: 0 3px 10px rgba(5,150,105,.2); }
    .btn-excel:hover { box-shadow: 0 6px 16px rgba(5,150,105,.3); filter: brightness(1.05); }

    .btn-pdf-btn { background: linear-gradient(135deg, #dc2626, #ef4444); color: #fff; box-shadow: 0 3px 10px rgba(220,38,38,.2); }
    .btn-pdf-btn:hover { box-shadow: 0 6px 16px rgba(220,38,38,.3); filter: brightness(1.05); }

    /* ── Table toolbar ───────────────────────────────────────── */
    .table-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 22px; flex-wrap: wrap; gap: 10px;
    }

    .toolbar-left  { display: flex; align-items: center; gap: 10px; }
    .toolbar-right { display: flex; align-items: center; gap: 10px; }

    .record-count { font-size: 12px; color: var(--muted); }

    /* Search */
    .search-box { position: relative; display: flex; align-items: center; }

    .search-box .material-icons {
        position: absolute; left: 9px; font-size: 16px; color: var(--muted); pointer-events: none;
    }

    .search-box input {
        height: 34px; padding: 0 12px 0 32px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body); font-size: 13px;
        color: var(--ink); outline: none; width: 200px;
        transition: border-color .2s, box-shadow .2s, width .3s;
    }

    .search-box input:focus {
        border-color: var(--border-focus); background: var(--surface);
        box-shadow: 0 0 0 3px rgba(26,86,219,.1); width: 240px;
    }

    /* Page length */
    .page-length-select {
        height: 34px; padding: 0 26px 0 10px;
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        background: #fafafa; font-family: var(--font-body); font-size: 13px;
        color: var(--ink); outline: none; appearance: none; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236b7280'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 4px center; background-size: 17px;
    }

    /* ── DataTable overrides ─────────────────────────────────── */
    .table-wrap { overflow-x: auto; }

    #audit-table_wrapper .dataTables_filter,
    #audit-table_wrapper .dataTables_length { display: none !important; }

    #audit-table_wrapper .dataTables_info {
        font-size: 12px; color: var(--muted); padding: 12px 22px;
    }

    #audit-table_wrapper .dataTables_paginate {
        padding: 10px 18px 14px; display: flex; justify-content: flex-end; gap: 4px;
    }

    #audit-table_wrapper .paginate_button {
        height: 30px; min-width: 30px; padding: 0 9px !important;
        border-radius: 7px !important; border: 1.5px solid var(--border) !important;
        background: var(--surface) !important; color: var(--muted) !important;
        font-size: 12.5px !important; font-family: var(--font-body) !important;
        cursor: pointer; display: inline-flex !important; align-items: center;
        justify-content: center; transition: all .2s;
    }

    #audit-table_wrapper .paginate_button:hover:not(.disabled) {
        background: var(--accent-lt) !important; border-color: var(--accent) !important; color: var(--accent) !important;
    }

    #audit-table_wrapper .paginate_button.current {
        background: linear-gradient(135deg, #1a56db, #4f46e5) !important;
        border-color: transparent !important; color: #fff !important;
        box-shadow: 0 3px 10px rgba(26,86,219,.3);
    }

    #audit-table_wrapper .paginate_button.disabled { opacity: .4; cursor: not-allowed; }

    table#audit-table {
        width: 100% !important; border-collapse: collapse;
        font-size: 13px; font-family: var(--font-body);
    }

    table#audit-table thead th {
        background: #f9fafb; color: var(--muted); font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: .06em;
        padding: 10px 14px; border-bottom: 1px solid var(--border); white-space: nowrap;
    }

    table#audit-table thead th:first-child { padding-left: 22px; }

    table#audit-table tbody td {
        padding: 11px 14px; border-bottom: 1px solid #f3f4f8;
        vertical-align: middle; color: var(--ink);
    }

    table#audit-table tbody td:first-child { padding-left: 22px; }
    table#audit-table tbody tr:last-child td { border-bottom: none; }
    table#audit-table tbody tr:hover td { background: #f8faff; }

    /* Expand control */
    td.details-control { cursor: pointer; width: 36px; text-align: center; }
    td.details-control .material-icons { font-size: 18px; color: var(--accent); transition: transform .25s; }
    tr.shown td.details-control .material-icons { transform: rotate(45deg); color: var(--danger); }

    /* Action badges */
    .action-badge {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 2px 8px; border-radius: 100px;
        font-size: 10.5px; font-weight: 700; letter-spacing: .04em;
        text-transform: uppercase;
    }

    .action-badge.INSERT  { background: var(--success-lt); color: var(--success); }
    .action-badge.UPDATE  { background: var(--accent-lt);  color: var(--accent); }
    .action-badge.DELETE  { background: var(--danger-lt);  color: var(--danger); }
    .action-badge.LOGIN   { background: #f3f0ff; color: #7c3aed; }
    .action-badge.LOGOUT  { background: #f3f4f8; color: var(--muted); }
    .action-badge.ERROR   { background: var(--danger-lt);  color: var(--danger); }
    .action-badge.VIEW    { background: #fef9c3; color: #854d0e; }

    /* ── Expandable child row ─────────────────────────────────── */
    .detail-wrap {
        background: #fafbff; padding: 18px 22px;
    }

    .detail-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
    }

    .detail-section { }

    .detail-section-title {
        font-size: 10.5px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; color: var(--muted); margin-bottom: 10px;
        display: flex; align-items: center; gap: 7px;
    }

    .detail-section-title::after { content: ''; flex: 1; height: 1px; background: var(--border); }

    .diff-table {
        width: 100%; border-collapse: collapse; font-size: 12.5px;
        border-radius: var(--radius-sm); overflow: hidden;
        border: 1px solid var(--border);
    }

    .diff-table thead th {
        background: #f3f4f8; color: var(--muted); font-size: 10.5px; font-weight: 600;
        text-transform: uppercase; letter-spacing: .05em;
        padding: 7px 10px; border-bottom: 1px solid var(--border);
    }

    .diff-table tbody td {
        padding: 7px 10px; border-bottom: 1px solid #f3f4f8; font-size: 12.5px;
    }

    .diff-table tbody tr.changed td { background: #fffbeb; }
    .diff-table tbody tr:last-child td { border-bottom: none; }
    .diff-old  { color: var(--danger); }
    .diff-new  { color: var(--success); font-weight: 600; }

    .context-pre {
        background: #f3f4f8; border: 1px solid var(--border); border-radius: 8px;
        padding: 10px 12px; font-size: 12px; font-family: monospace;
        overflow-x: auto; max-height: 160px; overflow-y: auto;
        color: var(--ink); margin: 0; white-space: pre-wrap; word-break: break-word;
    }

    .user-agent-txt { font-size: 12px; color: var(--muted); line-height: 1.6; }

    /* Full-span for changes section */
    .detail-full { grid-column: 1 / -1; }

    /* ── PDF Modal ────────────────────────────────────────────── */
    .pdf-backdrop {
        position: fixed; inset: 0; background: rgba(0,0,0,.55);
        backdrop-filter: blur(4px); z-index: 8000;
        display: none; align-items: center; justify-content: center; padding: 20px;
    }

    .pdf-backdrop.open { display: flex; }

    .pdf-modal {
        background: var(--surface); border-radius: 20px;
        width: 100%; max-width: 1000px; height: 90vh;
        display: flex; flex-direction: column;
        box-shadow: 0 24px 80px rgba(0,0,0,.25); overflow: hidden;
        animation: fadeUp .3s cubic-bezier(.22,.61,.36,1) both;
    }

    .pdf-modal-head {
        display: flex; align-items: center; gap: 10px;
        padding: 14px 20px; border-bottom: 1px solid var(--border); flex-shrink: 0;
    }

    .pdf-modal-title { font-family: var(--font-head); font-size: 15px; font-weight: 700; color: var(--ink); flex: 1; }

    .btn-icon {
        width: 32px; height: 32px; border: 1.5px solid var(--border); border-radius: 8px;
        background: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: var(--muted); transition: all .2s;
    }

    .btn-icon:hover { color: var(--ink); border-color: #9ca3af; background: var(--bg); }
    .btn-icon .material-icons { font-size: 17px; }

    .btn-dl {
        height: 32px; padding: 0 14px; border: none; border-radius: 8px;
        background: linear-gradient(135deg, #059669, #10b981); color: #fff;
        font-family: var(--font-body); font-size: 13px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 5px;
        box-shadow: 0 3px 10px rgba(5,150,105,.25); transition: transform .2s;
    }

    .btn-dl:hover { transform: translateY(-1px); }
    .btn-dl .material-icons { font-size: 15px; }

    .pdf-modal-body { flex: 1; overflow: hidden; position: relative; }
    .pdf-modal-body iframe { width: 100%; height: 100%; border: none; display: block; }

    .pdf-loading {
        position: absolute; inset: 0; display: flex;
        flex-direction: column; align-items: center; justify-content: center;
        gap: 10px; background: var(--surface); color: var(--muted); font-size: 14px;
    }

    .pdf-loading .material-icons { font-size: 34px; color: #d1d5db; animation: spin 1.4s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Toast ───────────────────────────────────────────────── */
    .toast-wrap {
        position: fixed; top: 20px; right: 20px; z-index: 9999;
        display: flex; flex-direction: column; gap: 10px;
    }

    .toast-msg {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 18px; border-radius: 14px;
        min-width: 280px; max-width: 360px; font-size: 14px; font-weight: 500;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        animation: toastIn .35s cubic-bezier(.22,.61,.36,1) both; cursor: pointer;
    }

    .toast-msg.leaving { animation: toastOut .3s ease forwards; }
    @keyframes toastIn  { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
    @keyframes toastOut { to   { opacity:0; transform:translateX(40px); } }

    .toast-msg.success { background: var(--success-lt); color: #065f46; }
    .toast-msg.danger  { background: var(--danger-lt);  color: #991b1b; }
    .toast-msg .material-icons { font-size: 20px; flex-shrink: 0; }

    @media (max-width: 768px) {
        .audit-page  { padding: 18px 14px; }
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>

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