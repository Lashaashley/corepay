<x-custom-admin-layout>
@vite(['resources/css/pages/vaudit.css']) 

<div class="audit-page">

    <div class="page-heading">
        <h1>Audit Trail</h1>
        <p>Track and review all system activity and data changes.</p>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    {{-- Legacy alert shell kept for JS compatibility --}}
    <div id="status-message" class="alert alert-dismissible fade hidden" role="alert" >
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
                        <label>Report Type <span class="spandang" >*</span></label>
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
                        <label>From Date <span class="spandang">*</span></label>
                        <input type="date" name="from_date" required>
                    </div>

                    {{-- To date --}}
                    <div class="field fg-3">
                        <label>To Date <span class="spandang">*</span></label>
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
            <span class="record-count marginl" id="recordCount" ></span>
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
            <table id="audit-table" >
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
            <div class="a-card-icon backver" >
                <span class="material-icons" class="spandang">picture_as_pdf</span>
            </div>
            <span class="pdf-modal-title">Audit Trail — PDF Preview</span>
            <button class="btn-dl" id="downloadPdfBtn">
                <span class="material-icons">download</span> Download
            </button>
            <button class="btn-icon" id="closePdfModal" >
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





@vite(['resources/js/vaudit.js'])

</x-custom-admin-layout>