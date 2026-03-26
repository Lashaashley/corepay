<x-custom-admin-layout>
   <style nonce="{{ $cspNonce }}">
     .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: var(--modal-shadow);
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            overflow: hidden;
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem 2rem;
            border: none;
            position: relative;
        }

        .modal-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.3) 100%);
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.4rem;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .modal-title i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
           	.tab-container {
    display: flex;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
}

.tab-button {
    background-color: #f8f9fa;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 10px 20px;
    font-size: 12.5px;
    transition: background-color 0.3s;
}

.tab-button:hover {
    background-color: #e9ecef;
}

.tab-button.active {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff; /* Hide border bottom when active */
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}
.action-buttons {
            padding: 1px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-enhanced {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-draft {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1);
            color: white;
        }
        
        .btn-finalize {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .btn-cancel {
            background: linear-gradient(135deg, #e93a04ff, #d62f05ff);
            color: white;
        }  
        

         .agents-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }

    /* ── Page header row ──────────────────────────────────────── */
    .page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-heading h1 {
        font-family: var(--font-head);
        font-size: 22px;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 4px;
    }

    .page-heading p {
        font-size: 13.5px;
        color: var(--muted);
        margin: 0;
    }

    /* ── Table card ───────────────────────────────────────────── */
    .table-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Table toolbar ────────────────────────────────────────── */
    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 12px;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .toolbar-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: var(--accent-lt);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .toolbar-icon .material-icons { font-size: 18px; color: var(--accent); }

    .toolbar-title {
        font-family: var(--font-head);
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
    }

    .toolbar-subtitle {
        font-size: 12px;
        color: var(--muted);
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* ── Search box ───────────────────────────────────────────── */
    .search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-box .material-icons {
        position: absolute;
        left: 11px;
        font-size: 17px;
        color: var(--muted);
        pointer-events: none;
    }

    .search-box input {
        height: 38px;
        padding: 0 13px 0 36px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        background: #fafafa;
        font-family: var(--font-body);
        font-size: 13.5px;
        color: var(--ink);
        outline: none;
        width: 220px;
        transition: border-color .2s, box-shadow .2s, width .3s;
    }

    .search-box input:focus {
        border-color: var(--border-focus);
        background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(26,86,219,.1);
        width: 260px;
    }

    .search-box input::placeholder { color: #adb5bd; }

    /* ── Toolbar buttons ──────────────────────────────────────── */
    .btn {
        height: 38px;
        padding: 0 16px;
        border: none;
        border-radius: var(--radius-sm);
        font-family: var(--font-body);
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: transform .2s, box-shadow .2s, filter .2s;
        letter-spacing: .01em;
        text-decoration: none;
    }

    .btn .material-icons { font-size: 16px; }
    .btn:hover { transform: translateY(-1px); }
    .btn:active { transform: translateY(0); }

    .btn-primary {
        background: linear-gradient(135deg, #1a56db, #4f46e5);
        color: #fff;
        box-shadow: 0 4px 14px rgba(26,86,219,.28);
    }

    .btn-primary:hover { box-shadow: 0 7px 20px rgba(26,86,219,.38); filter: brightness(1.05); }

    .btn-outline {
        background: var(--surface);
        color: var(--muted);
        border: 1.5px solid var(--border);
    }

    .btn-outline:hover { color: var(--ink); border-color: #9ca3af; }

    /* ── DataTable overrides ──────────────────────────────────── */
    .table-wrap {
        padding: 0 4px 4px;
        overflow-x: auto;
    }

    /* Hide DT's own search & length — we use our own */
    #agents-table_wrapper .dataTables_filter,
    #agents-table_wrapper .dataTables_length { display: none !important; }

    /* DT info + pagination row */
    #agents-table_wrapper .dataTables_info {
        font-size: 12.5px;
        color: var(--muted);
        padding: 14px 24px;
    }

    #agents-table_wrapper .dataTables_paginate {
        padding: 10px 20px 16px;
        display: flex;
        justify-content: flex-end;
        gap: 4px;
    }

    #agents-table_wrapper .paginate_button {
        height: 32px;
        min-width: 32px;
        padding: 0 10px !important;
        border-radius: 8px !important;
        border: 1.5px solid var(--border) !important;
        background: var(--surface) !important;
        color: var(--muted) !important;
        font-size: 13px !important;
        font-family: var(--font-body) !important;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        transition: all .2s;
    }

    #agents-table_wrapper .paginate_button:hover:not(.disabled) {
        background: var(--accent-lt) !important;
        border-color: var(--accent) !important;
        color: var(--accent) !important;
    }

    #agents-table_wrapper .paginate_button.current {
        background: linear-gradient(135deg, #1a56db, #4f46e5) !important;
        border-color: transparent !important;
        color: #fff !important;
        box-shadow: 0 3px 10px rgba(26,86,219,.3);
    }

    #agents-table_wrapper .paginate_button.disabled {
        opacity: .4;
        cursor: not-allowed;
    }

    /* Table itself */
    table#agents-table {
        width: 100% !important;
        border-collapse: collapse;
        font-size: 13.5px;
        font-family: var(--font-body);
    }

    table#agents-table thead th {
        background: #f9fafb;
        color: var(--muted);
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        border-top: none;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
    }

    table#agents-table thead th:first-child { padding-left: 24px; border-radius: 0; }
    table#agents-table thead th:last-child  { padding-right: 24px; }

    /* Sort arrows */
    table#agents-table thead th.sorting::after,
    table#agents-table thead th.sorting_asc::after,
    table#agents-table thead th.sorting_desc::after {
        font-family: 'Material Icons';
        font-size: 14px;
        vertical-align: middle;
        margin-left: 4px;
        opacity: .5;
        display: none !important;

    }

    table#agents-table thead th.sorting::after       { content: 'unfold_more'; }
    table#agents-table thead th.sorting_asc::after   { content: 'expand_less'; opacity: 1; color: var(--accent); }
    table#agents-table thead th.sorting_desc::after  { content: 'expand_more'; opacity: 1; color: var(--accent); }

    table#agents-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f3f4f8;
        vertical-align: middle;
        color: var(--ink);
    }

    table#agents-table tbody td:first-child { padding-left: 24px; }
    table#agents-table tbody td:last-child  { padding-right: 24px; }

    table#agents-table tbody tr:last-child td { border-bottom: none; }

    table#agents-table tbody tr {
        transition: background .15s;
    }

    table#agents-table tbody tr:hover td {
        background: #f8faff;
    }

    /* ── Avatar cell ──────────────────────────────────────────── */
    .agent-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .agent-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        border: 2px solid var(--border);
    }

    .agent-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--ink);
    }

    /* ── Status badge ─────────────────────────────────────────── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .02em;
    }

    .status-badge .dot {
        width: 6px; height: 6px;
        border-radius: 50%;
    }

    .status-badge.active {
        background: var(--success-lt);
        color: var(--success);
    }

    .status-badge.active .dot { background: var(--success); }

    .status-badge.inactive {
        background: var(--danger-lt);
        color: var(--danger);
    }

    .status-badge.inactive .dot { background: var(--danger); }

    /* ── Action dropdown ──────────────────────────────────────── */
    .action-wrap { position: relative; }

    .action-trigger {
        width: 32px; height: 32px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        background: var(--surface);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s;
        color: var(--muted);
    }

    .action-trigger:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-lt);
    }

    .action-trigger .material-icons { font-size: 18px; }

    .action-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-lg);
        min-width: 160px;
        z-index: 100;
        overflow: hidden;
        display: none;
        animation: menuIn .15s ease;
    }

    @keyframes menuIn {
        from { opacity: 0; transform: translateY(-6px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .action-menu.open { display: block; }

    .action-menu a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        font-size: 13px;
        color: var(--ink);
        text-decoration: none;
        transition: background .15s;
    }

    .action-menu a .material-icons { font-size: 16px; color: var(--muted); }
    .action-menu a:hover { background: var(--accent-lt); color: var(--accent); }
    .action-menu a:hover .material-icons { color: var(--accent); }

    /* ── DT processing overlay ────────────────────────────────── */
    #agents-table_processing {
        background: rgba(255,255,255,.85) !important;
        border: none !important;
        box-shadow: none !important;
        color: var(--muted) !important;
        font-family: var(--font-body) !important;
        font-size: 13.5px !important;
        display: flex !important;
        align-items: center;
        gap: 8px;
    }

    /* ── Empty / zero state ───────────────────────────────────── */
    td.dataTables_empty {
        text-align: center;
        padding: 48px 24px !important;
        color: var(--muted);
        font-size: 14px;
    }

    /* ── Bottom info+pagination bar ───────────────────────────── */
    .dt-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 24px 16px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 8px;
    }

    /* ── Responsive ───────────────────────────────────────────── */
    @media (max-width: 640px) {
        .page-header { flex-direction: column; align-items: flex-start; }
        .toolbar-right { width: 100%; }
        .search-box input { width: 100%; }
        .search-box { flex: 1; }
        .agents-page { padding: 18px 14px; }
    }
   </style>
   <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<!-- Use the correct paired CSS + JS versions -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <div class="agents-page">

    <!-- Page header 
    <div class="page-header">
        <div class="page-heading">
            <h1>Agents</h1>
            <p>Manage all registered agents and their details.</p>
        </div>
        <a href="" class="btn btn-primary">
            <span class="material-icons">person_add</span> New Agent
        </a>
    </div>-->

    <!-- Toast -->
    <div class="toast-wrap" id="toastWrap"></div>

    <!-- Table card -->
    <div class="table-card">

        <!-- Toolbar -->
        <div class="table-toolbar">
            <div class="toolbar-left">
                <div class="toolbar-icon">
                    <span class="material-icons">group</span>
                </div>
                <div>
                    <div class="toolbar-title">All Agents</div>
                    <div class="toolbar-subtitle" id="recordCount">Loading…</div>
                </div>
            </div>

            <div class="toolbar-right">
                <!-- Custom search wired to DataTable -->
                <div class="search-box">
                    <span class="material-icons">search</span>
                    <input type="text" id="dt-search" placeholder="Search agents…">
                </div>

                <!-- Page-length selector -->
                <div class="select-wrap">
                    <select id="dt-length" style="height:38px;padding:0 32px 0 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:#fafafa;font-family:var(--font-body);font-size:13.5px;color:var(--ink);outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;">
                        <option value="10">10 / page</option>
                        <option value="25" selected>25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-wrap">
            <table id="agents-table" class="stripe hover nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Work No</th>
                        <th>Staff Type</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>State</th>
                        <th class="datatable-nosort">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div><!-- /table-card -->
</div><!-- /agents-page -->

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editstaffModal"tabindex="-1" aria-labelledby="electiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="electiveModalLabel">
                        <i class="fas fa-user"></i>
                        Edit Agent
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="tab-bar">
        <button class="tab-btn active" data-tab="staffInfo">
            <span class="material-icons">person_outline</span>
            Agent Information
            <span class="tab-badge" id="badge-staffInfo">✓</span>
        </button>
        <button class="tab-btn" id="tab-registration" data-tab="registration">
            <span class="material-icons">assignment_ind</span>
            Registration
            <span class="tab-badge" id="badge-registration">✓</span>
        </button>
    </div>
                <div class="modal-body">
                    <div class="tab-panel active" id="panel-staffInfo">

            <div class="section-head">
                <div class="section-icon"><span class="material-icons">badge</span></div>
                <div>
                    <h2>Update Agent Details</h2>
                    
                </div>
            </div>

            <form method="post" name="staffForm" id="staffForm" enctype="multipart/form-data">
                @csrf

                <p class="subsection-label">Personal</p>

                <div class="row">
                    <div class="field col-3">
                        <label>First Name <span class="req">*</span></label>
                        <input name="firstname" type="text" placeholder="e.g. John" required autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>Last Name <span class="req">*</span></label>
                        <input name="lastname" type="text" placeholder="e.g. Doe" required autocomplete="off">
                    </div>
                    <div class="field col-2">
                        <label>Date of Birth</label>
                        <input name="dob" type="text" class="date-picker" placeholder="DD/MM/YYYY" autocomplete="off">
                    </div>
                    <div class="field col-2">
                        <label>Gender</label>
                        <div class="select-wrap">
                            <select name="gender" autocomplete="off">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                </div>

                <p class="subsection-label">Work & Contact</p>

                <div class="row">
                    <div class="field col-3">
                        <label>Agent Number <span class="req">*</span></label>
                        <input name="agentno" id="agentno" type="text" placeholder="e.g. AGT-001" required autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>Email Address</label>
                        <input name="email" type="email" placeholder="agent@company.com" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>Phone Number</label>
                        <input name="phonenumber" type="text" placeholder="+254 7xx xxx xxx" autocomplete="off">
                    </div>
                </div>

                <p class="subsection-label">Assignment</p>

                <div class="row">
                    <div class="field col-4">
                        <label>Branch <span class="req">*</span></label>
                        <div class="select-wrap">
                            <select name="brid" id="brid" required autocomplete="off">
                                <option value="">Select Branch</option>
                            </select>
                        </div>
                        <span class="field-error" id="brid-error"></span>
                    </div>
                    <div class="field col-4">
                        <label>Department</label>
                        <div class="select-wrap">
                            <select name="dept" id="dept" autocomplete="off">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="action-bar">
                    <button type="reset" class="btn btn-reset">
                        <span class="material-icons">restart_alt</span> Clear
                    </button>
                    <button id="add_staff" type="submit" class="btn btn-save">
                        <span class="material-icons">save</span> Save Agent
                    </button>
                </div>
            </form>
        </div>
        <div class="tab-panel" id="panel-registration">

            <div class="section-head">
                <div class="section-icon"><span class="material-icons">assignment_ind</span></div>
                <div>
                    <h2>Registration Info</h2>
                    <p>Statutory, banking and payroll details</p>
                </div>
            </div>

            <form method="post" action="" id="registrationForm" enctype="multipart/form-data">
                @csrf
                <input name="aggentno" type="text" id="aggentno" value="" readonly hidden>

                <!-- Statutory -->
                <p class="subsection-label">Statutory & Flags</p>

                <div class="row">
                    <div class="field col-4">
                        <label>Statutory Deductions</label>
                        <div class="chip-group">
                            <div class="chip">
                                <input type="checkbox" id="nhif_shif" name="nhif_shif" value="YES">
                                <label for="nhif_shif">
                                    <span class="material-icons">health_and_safety</span> NHIF/SHIF
                                </label>
                            </div>
                            <div class="chip">
                                <input type="checkbox" id="nssf" name="nssf" value="YES">
                                <label for="nssf">
                                    <span class="material-icons">account_balance</span> NSSF
                                </label>
                            </div>
                            <div class="chip" hidden>
                                <input type="checkbox" id="pensyes" name="pensyes" value="YES">
                                <label for="pensyes">Pension</label>
                            </div>
                        </div>
                    </div>

                    <div class="field col-3">
                        <label>Union</label>
                        <div class="chip-group">
                            <div class="chip">
                                <input type="checkbox" id="unionized" name="unionized" value="YES">
                                <label for="unionized">
                                    <span class="material-icons">groups</span> Unionized
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="field col-3" id="union-container" style="display:none;">
                        <label>Union Number</label>
                        <input name="unionno" id="unionno" type="text" autocomplete="off" value="N/A">
                    </div>

                    <div class="field col-2">
                        <label>Is Agent</label>
                        <div class="chip-group">
                            <div class="chip">
                                <input type="checkbox" id="contractor" name="contractor" value="YES" checked>
                                <label for="contractor">
                                    <span class="material-icons">work_outline</span> Agent
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="form-divider">

                <!-- IDs -->
                <p class="subsection-label">Identification Numbers</p>

                <div class="row">
                    <div class="field col-3">
                        <label>ID No. <span class="req">*</span></label>
                        <input name="idno" id="idno" type="number" placeholder="National ID" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>SHIF No.</label>
                        <input name="nhifno" id="nhifno" type="text" placeholder="SHIF number" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>KRA PIN</label>
                        <input name="krapin" type="text" placeholder="AQ..." autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>
                            NSSF No.
                            <span style="margin-left:8px; font-weight:400; color:var(--muted);">
                                <label style="display:inline-flex;align-items:center;gap:5px;cursor:pointer;font-size:12px;">
                                    <input type="checkbox" id="nssfopt" name="nssfopt" value="YES" style="accent-color:var(--accent);width:14px;height:14px;">
                                    Opt out
                                </label>
                            </span>
                        </label>
                        <input name="nssfno" id="nssfno" type="text" placeholder="NSSF number" autocomplete="off">
                    </div>
                </div>

                <hr class="form-divider">

                <!-- Payment -->
                <p class="subsection-label">Payment & Payroll</p>

                <div class="row">
                    <div class="field col-3">
                        <label>Payroll Type <span class="req">*</span></label>
                        <div class="select-wrap">
                            <select name="proltype" id="proltype" required autocomplete="off">
                                <option value="">Select type</option>
                            </select>
                        </div>
                    </div>

                    <div class="field col-3">
                        <label>Payment Method</label>
                        <div class="chip-group">
                            <div class="chip">
                                <input type="radio" name="paymentMethod" id="etf" value="Etransfer" checked>
                                <label for="etf">
                                    <span class="material-icons">swap_horiz</span> E-Transfer
                                </label>
                            </div>
                            <div class="chip">
                                <input type="radio" name="paymentMethod" id="cheque" value="Cheque">
                                <label for="cheque">
                                    <span class="material-icons">receipt_long</span> Cheque
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="form-divider">

                <!-- Banking -->
                <p class="subsection-label">Banking Details</p>

                <div class="row">
                    <div class="field col-3">
                        <label>Bank</label>
                        <div class="select-wrap">
                            <select name="bank" id="bank" autocomplete="off">
                                <option value="">Select Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="field col-3">
                        <label>Bank Branch</label>
                        <div class="select-wrap">
                            <select name="branch" id="branch" autocomplete="off">
                                <option value="">Select Branch</option>
                            </select>
                        </div>
                    </div>
                    <div class="field col-2">
                        <label>Branch Code</label>
                        <input name="bcode" id="bcode" type="text" autocomplete="off" readonly>
                        <input name="bankcode" id="bankcode" type="text" hidden>
                    </div>
                    <div class="field col-2">
                        <label>Swift Code</label>
                        <input name="swiftcode" id="swiftcode" type="text" placeholder="XXXXKENA" autocomplete="off">
                    </div>
                    <div class="field col-3">
                        <label>Account Number <span class="req">*</span></label>
                        <input name="account" id="account" type="text" placeholder="Account number" required autocomplete="off">
                    </div>
                </div>

                <div class="action-bar">
                    <button type="reset" class="btn btn-reset">
                        <span class="material-icons">restart_alt</span> Clear
                    </button>
                    <button id="load" type="submit" class="btn btn-save">
                        <span class="material-icons">save</span> Save Registration
                    </button>
                </div>
            </form>
        </div>
                    

            </div>
        </div>
    </div>

    <!-- Terminate Modal -->
    <div class="modal fade" id="terminatModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Terminate Staff</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to terminate this staff member?</p>
                    <input type="hidden" id="terminate-agent-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-terminate">Terminate</button>
                </div>
            </div>
        </div>
    </div>

    
    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script nonce="{{ $cspNonce }}">
        const amanage = '{{ route("agents.data") }}';
        const branches = '{{ route("branches.getDropdown") }}';
        const depts = '{{ route("depts.getDropdown") }}';
        const getbanks = '{{ route("banks.getDropdown") }}';
        const getbranches = '{{ route("brbranches.getDropdown") }}';
        const getuser = '{{ route("get.agent", ":id") }}';
        const getptypes = '{{ route("paytypes.getDropdown") }}';
        const getbybank = '{{ route("branches.getByBank") }}';
        const codebybank = '{{ route("codes.getByBank") }}';
    </script>
    <script src="{{ asset('js/amanage.js') }}"></script>
    
    <script nonce="{{ $cspNonce }}"> 
      $(document).ready(function() {
         $('#staffForm').on('submit', function (e) {
    e.preventDefault();
    
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    const id = $('#agentno').val();
    const formData = new FormData(this);

    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    
    formData.append('_method', 'POST');
    
    $.ajax({ 
        url: `{{ url('agent') }}/${id}`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            showToast('success', 'Success!', response.message);
            $('#editstaffModal').modal('hide');
            
            // Optionally reload the data table or refresh the page
            if (typeof table !== 'undefined') {
                table.ajax.reload();
            }
        },
        error: function (xhr) {
            console.error('Error response:', xhr.responseJSON);
            
            if (xhr.status === 422) {
                // Validation errors
                let errors = xhr.responseJSON.errors;
                
                $.each(errors, function (key, messages) {
                    // Find the input field
                    let input = $(`[name="${key}"]`);
                    
                    // Add error class
                    input.addClass('is-invalid');
                    
                    // Add error message
                    input.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                });
                
                showToast('danger', 'Validation Error!', 'Please check the form for errors.');
            } else if (xhr.status === 404) {
                showToast('danger', 'Error!', 'Agent not found.');
            } else {
                let errorMessage = xhr.responseJSON?.message || 'Error updating agent.';
                showToast('danger', 'Error!', errorMessage);
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});
 $('#registrationForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#aggentno').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: `{{ url('regagent') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showToast('success', 'Success!', response.message);
                        
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showToast('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showToast('danger', 'Error!', 'Error updating Agent.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#bank').on('change', function() {
                const selectedCampusId = $(this).val();
                if (selectedCampusId) {
                    loadBranches2(selectedCampusId);
                } else {
                    const classDropdown = $('#branch');
                    classDropdown.empty();
                    classDropdown.append('<option value="">Select Branch</option>');
                }
        
            });
            $('#branch').on('change', function() {
                const branch = $(this).val();
                const bank = $('#bank').val();
                if (branch) {
                    fetchcodes2(bank,branch);
                } else {

                }
        
            });
      });
       function loadBranches2(campusId) {
        $.ajax({
          url: "{{ route('branches.getByBank') }}",
          type: "GET",
          data: { campusId: campusId },
          success: function (response) {
            const dropdown = $('#branch');
            dropdown.empty();
            dropdown.append('<option value="">Select Branch</option>');
            response.data.forEach(function (branches) {
              dropdown.append(
                `<option value="${branches.Branch}">${branches.Branch}</option>`
              );
              
            });
          },
          error: function () {
            alert('Failed to load classes. Please try again.');
          }
        });
      }
      function fetchcodes2(bank, branch) {
        $.ajax({
          url: "{{ route('codes.getByBank') }}",
          type: "GET",
          data: { bank: bank,
            branch: branch
           },
          success: function (response) {
            response.data.forEach(function (branches) {
              document.getElementById('bcode').value = branches.BranchCode;
              document.getElementById('swiftcode').value = branches.swiftcode;
              document.getElementById('bankcode').value = branches.BankCode;
              
            });
          },
          error: function () {
            alert('Failed to load classes. Please try again.');
          }
        });
      }
    </script>
    
   
</x-custom-admin-layout>