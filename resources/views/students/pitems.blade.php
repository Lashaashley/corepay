<x-custom-admin-layout>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>

<style nonce="{{ $cspNonce }}">

/* ── Page wrapper ─────────────────────────────────────── */
.pitems-page {
    padding: 28px 24px;
    background: var(--bg);
    min-height: calc(100vh - 60px);
}

/* ── Page header ──────────────────────────────────────── */
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

.page-heading p { font-size: 13.5px; color: var(--muted); margin: 0; }

/* ── Table card ───────────────────────────────────────── */
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

/* Toolbar */
.table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}

.toolbar-left { display: flex; align-items: center; gap: 12px; }

.toolbar-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: var(--accent-lt);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.toolbar-icon .material-icons { font-size: 18px; color: var(--accent); }
.toolbar-title { font-family: var(--font-head); font-size: 15px; font-weight: 700; color: var(--ink); }
.toolbar-subtitle { font-size: 12px; color: var(--muted); }

/* Table */
.table-wrap { overflow-x: auto; }

table.pitems-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
    font-family: var(--font-body);
}

table.pitems-table thead th {
    background: #f9fafb;
    color: var(--muted);
    font-size: 11.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}

table.pitems-table thead th:first-child { padding-left: 24px; }
table.pitems-table thead th:last-child  { padding-right: 24px; }

table.pitems-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f8;
    vertical-align: middle;
    color: var(--ink);
}

table.pitems-table tbody td:first-child { padding-left: 24px; }
table.pitems-table tbody td:last-child  { padding-right: 24px; }
table.pitems-table tbody tr:last-child td { border-bottom: none; }
table.pitems-table tbody tr { transition: background .15s; }
table.pitems-table tbody tr:hover td { background: #f8faff; }

/* Code cell */
.code-cell { display: flex; flex-direction: column; gap: 2px; }
.code-primary { font-weight: 700; color: var(--ink); font-size: 13.5px; }
.code-desc    { font-size: 12px; color: var(--muted); }

/* Category / type badges */
.type-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 100px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
}

.type-badge.payment    { background: #ecfdf5; color: #059669; }
.type-badge.deduction  { background: #fef2f2; color: #dc2626; }
.type-badge.benefit    { background: #eff6ff; color: #1a56db; }
.type-badge.relief     { background: #fdf4ff; color: #9333ea; }
.type-badge.normal     { background: #f9fafb; color: #6b7280; }
.type-badge.loan       { background: #fff7ed; color: #d97706; }
.type-badge.balance    { background: #f0fdf4; color: #16a34a; }
.type-badge.variable   { background: #eff6ff; color: #3b82f6; }
.type-badge.fixed      { background: #f0fdf4; color: #10b981; }
.type-badge.taxable    { background: #fef3c7; color: #d97706; }
.type-badge.nontaxable { background: #eff6ff; color: #1a56db; }

/* Action menu */
.action-wrap { position: relative; display: inline-block; }

.action-trigger {
    width: 32px; height: 32px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    background: none;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted);
    transition: all .2s;
}

.action-trigger:hover { color: var(--ink); border-color: #9ca3af; background: var(--bg); }
.action-trigger .material-icons { font-size: 18px; }

.action-menu {
    position: absolute;
    right: 0; top: calc(100% + 6px);
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: var(--shadow-lg);
    min-width: 160px;
    z-index: 100;
    overflow: hidden;
    display: none;
}

.action-menu.open { display: block; }

.action-menu a {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    font-size: 13.5px;
    color: var(--ink);
    text-decoration: none;
    transition: background .15s;
}

.action-menu a:hover { background: var(--bg); }
.action-menu a .material-icons { font-size: 16px; color: var(--muted); }
.action-menu a.danger { color: var(--danger); }
.action-menu a.danger .material-icons { color: var(--danger); }

/* Buttons */
.btn {
    height: 40px;
    padding: 0 20px;
    border: none;
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: transform .2s, box-shadow .2s, filter .2s;
    text-decoration: none;
}

.btn .material-icons { font-size: 16px; }
.btn:hover { transform: translateY(-1px); }
.btn:active { transform: translateY(0); }

.btn-primary-mod {
    background: linear-gradient(135deg, #1a56db, #4f46e5);
    color: #fff;
    box-shadow: 0 4px 14px rgba(26,86,219,.28);
}

.btn-primary-mod:hover { box-shadow: 0 7px 20px rgba(26,86,219,.38); filter: brightness(1.05); }

.btn-save {
    background: linear-gradient(135deg, #059669, #10b981);
    color: #fff;
    box-shadow: 0 4px 14px rgba(5,150,105,.25);
}

.btn-save:hover { box-shadow: 0 7px 18px rgba(5,150,105,.35); filter: brightness(1.05); }

.btn-ghost {
    background: var(--surface);
    color: var(--muted);
    border: 1.5px solid var(--border);
}

.btn-ghost:hover { color: var(--ink); border-color: #9ca3af; }

/* Toast */
.toast-wrap {
    position: fixed; top: 20px; right: 20px; z-index: 9999;
    display: flex; flex-direction: column; gap: 10px;
}

.toast-msg {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px; border-radius: 14px;
    min-width: 280px; max-width: 360px;
    font-size: 14px; font-weight: 500;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    animation: toastIn .35s cubic-bezier(.22,.61,.36,1) both;
    cursor: pointer;
}

.toast-msg.leaving { animation: toastOut .3s ease forwards; }

@keyframes toastIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
@keyframes toastOut { to { opacity:0; transform:translateX(40px); } }

.toast-msg.success { background: var(--success-lt); color: #065f46; }
.toast-msg.danger  { background: var(--danger-lt);  color: #991b1b; }
.toast-msg .material-icons { font-size: 20px; flex-shrink: 0; }

/* ── Modal ────────────────────────────────────────────── */
.modal-backdrop-custom {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(4px);
    z-index: 8000;
    display: none;
    align-items: flex-start;
    justify-content: center;
    padding: 32px 20px;
    overflow-y: auto;
}

.modal-backdrop-custom.open { display: flex; }

.modal-card {
    background: var(--surface);
    border-radius: 20px;
    width: 100%;
    max-width: 780px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: fadeUp .3s cubic-bezier(.22,.61,.36,1) both;
    margin: auto;
}

.modal-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 22px 26px;
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}

.modal-header-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: var(--accent-lt);
    display: flex; align-items: center; justify-content: center;
}

.modal-header-icon .material-icons { font-size: 19px; color: var(--accent); }

.modal-header-title {
    flex: 1;
    font-family: var(--font-head);
    font-size: 16px; font-weight: 700; color: var(--ink);
    margin: 0 0 2px;
}

.modal-header-subtitle { font-size: 12px; color: var(--muted); }

.modal-close-btn {
    width: 32px; height: 32px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    background: none;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted);
    transition: all .2s;
}

.modal-close-btn:hover { color: var(--ink); border-color: #9ca3af; background: var(--bg); }
.modal-close-btn .material-icons { font-size: 18px; }

.modal-body { padding: 26px 26px 8px; }

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 26px;
    border-top: 1px solid var(--border);
    background: #fafafa;
    border-radius: 0 0 20px 20px;
}

/* ── Form sections inside modal ───────────────────────── */
.form-section {
    margin-bottom: 22px;
}

.form-section-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--muted);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}

/* ── Form grid ────────────────────────────────────────── */
.form-grid {
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    gap: 14px 16px;
}

.fc-2  { grid-column: span 2; }
.fc-3  { grid-column: span 3; }
.fc-4  { grid-column: span 4; }
.fc-6  { grid-column: span 6; }
.fc-12 { grid-column: span 12; }

@media (max-width: 640px) {
    .fc-2, .fc-3, .fc-4, .fc-6 { grid-column: span 12; }
}

/* ── Field ────────────────────────────────────────────── */
.field { display: flex; flex-direction: column; gap: 5px; }

.field label {
    font-size: 12px;
    font-weight: 500;
    color: #374151;
    letter-spacing: .01em;
}

.field label .req { color: var(--danger); margin-left: 2px; }

.field input:not([type="radio"]):not([type="checkbox"]),
.field select {
    height: 40px;
    padding: 0 12px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: #fafafa;
    font-family: var(--font-body);
    font-size: 13.5px;
    color: var(--ink);
    outline: none;
    width: 100%;
    transition: border-color .2s, box-shadow .2s;
    appearance: none;
    -webkit-appearance: none;
}

.field input::placeholder { color: #adb5bd; }

.field input:focus,
.field select:focus {
    border-color: var(--border-focus);
    background: var(--surface);
    box-shadow: 0 0 0 3.5px rgba(26,86,219,.1);
}

.field input[readonly] {
    background: #f3f4f8;
    color: var(--muted);
    cursor: not-allowed;
}

/* Select arrow */
.select-wrap { position: relative; }

.select-wrap::after {
    content: 'expand_more';
    font-family: 'Material Icons';
    font-size: 18px;
    position: absolute;
    right: 10px; top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    pointer-events: none;
}

.select-wrap select { padding-right: 34px; }

/* ── Segmented toggle ─────────────────────────────────── */
.seg-toggle {
    display: inline-flex;
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: 100px;
    padding: 3px;
    gap: 2px;
}

.seg-toggle input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0; height: 0;
}

.seg-toggle label {
    padding: 6px 14px;
    border-radius: 100px;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--muted);
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
    user-select: none;
    margin: 0;
}

.seg-toggle input[type="radio"]:checked + label {
    background: var(--surface);
    color: var(--accent);
    font-weight: 600;
    box-shadow: 0 1px 4px rgba(0,0,0,.1);
}

/* Three-option toggle wider */
.seg-toggle.seg-3 label { padding: 6px 11px; font-size: 12px; }

/* ── Chip checkboxes ──────────────────────────────────── */
.chip-row { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 2px; }

.chip-check { position: relative; }

.chip-check input[type="checkbox"] {
    position: absolute;
    opacity: 0; width: 0; height: 0;
}

.chip-check label {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 14px;
    border: 1.5px solid var(--border);
    border-radius: 100px;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--muted);
    cursor: pointer;
    background: #fafafa;
    transition: all .2s;
    white-space: nowrap;
    margin: 0;
}

.chip-check label .material-icons { font-size: 14px; }

.chip-check input:checked + label {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--accent-lt);
}

/* ── Bordered section group ───────────────────────────── */
.fieldset-box {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 16px 16px 12px;
    position: relative;
    margin-top: 8px;
}

.fieldset-box .fieldset-legend {
    position: absolute;
    top: -10px; left: 12px;
    background: var(--surface);
    padding: 0 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--muted);
}

/* ── Priority section ─────────────────────────────────── */
.priority-info-banner {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: var(--accent-lt);
    border: 1px solid #bfdbfe;
    border-radius: var(--radius-sm);
    margin-bottom: 16px;
    font-size: 13px;
    color: var(--accent);
}

.priority-info-banner .material-icons { font-size: 18px; flex-shrink: 0; }

.priority-current-card {
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.priority-current-card .label-tiny {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--muted);
    margin-bottom: 3px;
}

.priority-current-card .item-name { font-weight: 700; font-size: 14px; color: var(--ink); }
.priority-current-card .item-code { font-size: 12px; color: var(--muted); }

.priority-badge-pill {
    padding: 4px 12px;
    background: var(--accent-lt);
    border: 1px solid #bfdbfe;
    border-radius: 100px;
    font-size: 12px;
    font-weight: 700;
    color: var(--accent);
}

.priority-list-card {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    overflow: hidden;
}

.priority-list-header {
    padding: 10px 16px;
    background: #f9fafb;
    border-bottom: 1px solid var(--border);
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 7px;
}

.priority-list-header .material-icons { font-size: 15px; }

#sortableDeductions,
#editsortableDeductions {
    list-style: none;
    padding: 0;
    margin: 0;
}

#sortableDeductions .list-group-item,
#editsortableDeductions .list-group-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-bottom: 1px solid #f3f4f8;
    font-size: 13px;
    color: var(--ink);
    cursor: move;
    transition: background .15s;
}

#sortableDeductions .list-group-item:last-child,
#editsortableDeductions .list-group-item:last-child { border-bottom: none; }

#sortableDeductions .list-group-item:hover,
#editsortableDeductions .list-group-item:hover { background: #f8faff; }

#sortableDeductions .list-group-item.sortable-ghost,
#editsortableDeductions .list-group-item.sortable-ghost {
    opacity: .4;
    background: var(--accent-lt);
}

.drag-handle { color: var(--muted); display: flex; cursor: grab; }
.drag-handle:active { cursor: grabbing; }
.drag-handle .material-icons { font-size: 18px; }

.priority-num {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: var(--accent-lt);
    color: var(--accent);
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.priority-guide-card {
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 16px;
    height: fit-content;
}

.priority-guide-card .guide-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.priority-guide-card .guide-title .material-icons { font-size: 15px; color: var(--muted); }

.priority-guide-card ol {
    padding-left: 16px;
    font-size: 12.5px;
    color: var(--muted);
    margin: 0 0 12px;
    line-height: 1.8;
}

.priority-guide-card .example-item {
    font-size: 12px;
    color: var(--muted);
    padding: 4px 0;
}

/* ── Feedback error ───────────────────────────────────── */
.field-error { font-size: 11.5px; color: var(--danger); margin-top: 3px; }

</style>

<div class="pitems-page">

    <div class="page-header">
        <div class="page-heading">
            <h1>Payroll Items</h1>
            <p>Manage payroll codes, deductions, and payment types.</p>
        </div>
        <button class="btn btn-primary-mod" onclick="document.getElementById('addModal').classList.add('open')">
            <span class="material-icons">add</span> New Item
        </button>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <!-- Table card -->
    <div class="table-card">
        <div class="table-toolbar">
            <div class="toolbar-left">
                <div class="toolbar-icon">
                    <span class="material-icons">receipt_long</span>
                </div>
                <div>
                    <div class="toolbar-title">All Payroll Items</div>
                    <div class="toolbar-subtitle">Codes, types and deduction settings</div>
                </div>
            </div>
        </div>

        <div class="table-wrap">
            <table class="pitems-table stripe hover nowrap" id="payrollCodesTable">
                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th>Code</th>
                        <th>Process Type</th>
                        <th>Trans Type</th>
                        <th>Pay Type</th>
                        <th>Category</th>
                        <th hidden>Relief T</th>
                        <th hidden>prossty</th>
                        <th hidden>rate</th>
                        <th hidden>incre</th>
                        <th hidden>prossty</th>
                        <th hidden>rate</th>
                        <th hidden>incre</th>
                        <th hidden>incre</th>
                        <th hidden>incre</th>
                        <th hidden>incre</th>
                        <th hidden>incre</th>
                        <th class="datatable-nosort">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrollItems as $row)
                    <tr>
                        <td hidden>{{ $row->ID }}</td>
                        <td>
                            <div class="code-cell">
                                <span class="code-primary">{{ $row->code }}</span>
                                <span class="code-desc">{{ $row->cname }}</span>
                            </div>
                        </td>
                        <td>{{ $row->procctype }}</td>
                        <td>
                            @php $vof = strtolower($row->varorfixed); @endphp
                            <span class="type-badge {{ $vof }}">{{ $row->varorfixed }}</span>
                        </td>
                        <td>
                            @php $tax = $row->taxaornon === 'Non-taxable' ? 'nontaxable' : 'taxable'; @endphp
                            <span class="type-badge {{ $tax }}">{{ $row->taxaornon }}</span>
                        </td>
                        <td>
                            @php $cat = strtolower($row->category); @endphp
                            <span class="type-badge {{ $cat }}">{{ $row->category }}</span>
                        </td>
                        <td hidden>{{ $row->relief }}</td>
                        <td hidden>{{ $row->prossty }}</td>
                        <td hidden>{{ $row->rate }}</td>
                        <td hidden>{{ $row->increREDU }}</td>
                        <td hidden>{{ $row->recintres }}</td>
                        <td hidden>{{ $row->formularinpu }}</td>
                        <td hidden>{{ $row->cumcas }}</td>
                        <td hidden>{{ $row->intrestcode }}</td>
                        <td hidden>{{ $row->codename }}</td>
                        <td hidden>{{ $row->issaccorel }}</td>
                        <td hidden>{{ $row->sposter }}</td>
                        <td>
                            <div class="action-wrap">
                                <button class="action-trigger" onclick="toggleMenu(this)">
                                    <span class="material-icons">more_horiz</span>
                                </button>
                                <div class="action-menu">
                                    <a href="#"
                                       data-id="{{ $row->ID }}"
                                       onclick="openEditModal(this); closeMenus();">
                                        <span class="material-icons">edit</span> Edit
                                    </a>
                                    <a href="#" class="danger"
                                       data-id="{{ $row->ID }}"
                                       data-code="{{ $row->code }}"
                                       onclick="deletePayrollCode(this); closeMenus();">
                                        <span class="material-icons">delete_outline</span> Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     ADD MODAL
══════════════════════════════════════════════════════ -->
<div class="modal-backdrop-custom" id="addModal">
    <div class="modal-card">

        <div class="modal-header">
            <div class="modal-header-icon">
                <span class="material-icons">add_circle_outline</span>
            </div>
            <div style="flex:1;">
                <div class="modal-header-title">New Payroll Item</div>
                <div class="modal-header-subtitle">Add a payroll code to the system</div>
            </div>
            <button class="modal-close-btn" onclick="document.getElementById('addModal').classList.remove('open')">
                <span class="material-icons">close</span>
            </button>
        </div>

        <div class="modal-body">
            <form id="payrollForm">
                @csrf
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                <!-- Basic info -->
                <div class="form-section">
                    <p class="form-section-label">Basic Information</p>
                    <div class="form-grid">
                        <div class="field fc-2">
                            <label>Code <span class="req">*</span></label>
                            <input type="text" id="code" name="code" placeholder="e.g. E01" required autocomplete="off">
                        </div>
                        <div class="field fc-6">
                            <label>Description <span class="req">*</span></label>
                            <input type="text" id="description" name="description" placeholder="e.g. Basic Salary" required autocomplete="off">
                        </div>
                        <div class="field fc-4">
                            <label>Category <span class="req">*</span></label>
                            <div class="select-wrap">
                                <select id="category" name="category" required>
                                    <option value="">Select category</option>
                                    <option value="normal">Normal</option>
                                    <option value="balance">Balance</option>
                                    <option value="loan">Loan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Process type + conditional -->
                <div class="form-section">
                    <p class="form-section-label">Process Type</p>
                    <div class="form-grid">
                        <div class="field fc-4">
                            <label>Process Type</label>
                            <div class="seg-toggle">
                                <input type="radio" id="amount" name="processt" value="Amount" checked>
                                <label for="amount">Amount</label>
                                <input type="radio" id="calculationRadio" name="processt" value="calculation">
                                <label for="calculationRadio">Calculation</label>
                            </div>
                        </div>
                        <div class="field fc-4" id="balanceOptions" style="display:none;">
                            <label>Balance Type</label>
                            <div class="seg-toggle">
                                <input type="radio" id="increasing" name="balanceType" value="Increasing">
                                <label for="increasing">Increasing</label>
                                <input type="radio" id="reducing" name="balanceType" value="Reducing">
                                <label for="reducing">Reducing</label>
                            </div>
                        </div>
                        <div class="field fc-2" id="loanRateField" style="display:none;">
                            <label>Rate</label>
                            <input type="text" id="rate" name="rate" placeholder="0.00" autocomplete="off">
                        </div>
                        <div class="field fc-4" id="loanRate" style="display:none;">
                            <label>Recovery &amp; Interest</label>
                            <div class="seg-toggle" id="recint-toggle">
                                <input type="radio" id="recintre" name="recintres" value="1" checked>
                                <label for="recintre">Recov &amp; Int</label>
                                <input type="radio" id="separate" name="recintres" value="0">
                                <label for="separate">Separate</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Code type + GL -->
                <div class="form-section">
                    <p class="form-section-label">Payroll Code Type &amp; GL Accounts</p>
                    <div class="form-grid">
                        <!-- Left: Code type -->
                        <div class="fc-6">
                            <div class="fieldset-box">
                                <span class="fieldset-legend">Code Type</span>
                                <div class="form-grid" style="margin-top:6px;">
                                    <div class="field fc-12">
                                        <label>Type <span class="req">*</span></label>
                                        <div class="select-wrap">
                                            <select id="prossty" name="prossty" required>
                                                <option value="">Select type</option>
                                                <option value="Payment">Payment</option>
                                                <option value="Deduction">Deduction</option>
                                                <option value="Benefit">Benefit</option>
                                                <option value="Relief">Relief</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>Variable / Fixed</label>
                                        <div class="seg-toggle" id="varorfixed-toggle">
                                            <input type="radio" id="variable" name="varorfixed" value="Variable" checked>
                                            <label for="variable">Variable</label>
                                            <input type="radio" id="fixed" name="varorfixed" value="Fixed">
                                            <label for="fixed">Fixed</label>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>Tax Status</label>
                                        <div class="seg-toggle" id="taxaornon-toggle">
                                            <input type="radio" id="taxable" name="taxaornon" value="Taxable" checked>
                                            <label for="taxable">Taxable</label>
                                            <input type="radio" id="nontax" name="taxaornon" value="Non-taxable">
                                            <label for="nontax">Non-taxable</label>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <div class="chip-row">
                                            <div class="chip-check">
                                                <input type="checkbox" id="saccocheck" name="saccocheck" value="Yes">
                                                <label for="saccocheck">
                                                    <span class="material-icons">savings</span> Sacco Related
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field fc-12" id="sacconames" style="display:none;">
                                        <label>Staff List</label>
                                        <div class="select-wrap">
                                            <select id="staffSelect7" name="staffSelect7">
                                                <option value="">Select Staff</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: GL -->
                        <div class="fc-6">
                            <div class="fieldset-box">
                                <span class="fieldset-legend">GL Accounts</span>
                                <div class="form-grid" style="margin-top:6px;">
                                    <div class="field fc-12">
                                        <div class="chip-row">
                                            <div class="chip-check">
                                                <input type="checkbox" id="pjornal">
                                                <label for="pjornal">
                                                    <span class="material-icons">link</span> Link to Payroll Journal
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>A/C Number</label>
                                        <input type="text" id="accountNumber" placeholder="Account number" autocomplete="off">
                                    </div>
                                    <div class="field fc-12">
                                        <label>Cost Centre</label>
                                        <input type="text" id="cc" placeholder="CC" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flags -->
                <div class="form-section">
                    <p class="form-section-label">Flags &amp; Relief</p>
                    <div class="form-grid">
                        <div class="field fc-6">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="exemptionBonuses">
                                    <label for="exemptionBonuses">Exemption / Bonuses / Overtime &amp; Retirement</label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-3">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="appearInP9">
                                    <label for="appearInP9">
                                        <span class="material-icons">description</span> Appear in P9
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-12">
                            <label>Relief Type</label>
                            <div class="seg-toggle seg-3" id="relief-toggle">
                                <input type="radio" id="none" name="relief" value="NONE" checked>
                                <label for="none">Not Relief</label>
                                <input type="radio" id="rnt" name="relief" value="RELIEF ON TAXABLE">
                                <label for="rnt">Relief on Taxable</label>
                                <input type="radio" id="rnp" name="relief" value="Relief on Paye">
                                <label for="rnp">Relief on PAYE</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calculation -->
                <div class="form-section">
                    <p class="form-section-label">Calculation</p>
                    <div class="form-grid">
                        <div class="field fc-12">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="calculation">
                                    <label for="calculation"><span class="material-icons">functions</span> Calculation</label>
                                </div>
                                <div class="chip-check">
                                    <input type="checkbox" id="cumulativeValue" name="calctype" value="cumulative">
                                    <label for="cumulativeValue"><span class="material-icons">stacked_line_chart</span> Cumulative Value</label>
                                </div>
                                <div class="chip-check">
                                    <input type="checkbox" id="casual" name="calctype" value="casual">
                                    <label for="casual"><span class="material-icons">person_outline</span> Casual</label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-12">
                            <label>Formula</label>
                            <input type="text" id="inputField" name="formularinpu" readonly placeholder="Formula will appear here" required>
                        </div>
                        <div class="field fc-3" id="loanhelper" style="display:none;">
                            <label>Interest Code</label>
                            <input type="text" id="interestcode" name="interestcode" autocomplete="off">
                        </div>
                        <div class="field fc-5" id="loanhelperDesc" style="display:none;">
                            <label>Interest Description</label>
                            <input type="text" id="interestdesc" name="interestdesc" autocomplete="off">
                        </div>
                        <div class="fc-12">
                            <div class="field-error" id="feedback"></div>
                        </div>
                    </div>
                </div>

                <!-- Priority section -->
                <div id="prioritySection" style="display:none;">
                    <div class="form-section-label" style="margin-bottom:14px;">Deduction Priority</div>
                    <div class="priority-info-banner">
                        <span class="material-icons">drag_indicator</span>
                        <span><strong>Drag and drop</strong> to set the deduction priority order.</span>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 220px;gap:16px;">
                        <div>
                            <div class="priority-current-card">
                                <div>
                                    <div class="label-tiny">Current Deduction</div>
                                    <div class="item-name" id="currentItemName">—</div>
                                    <div class="item-code" id="currentItemCode">Code will appear here</div>
                                </div>
                                <span class="priority-badge-pill" id="currentPriorityBadge">
                                    Priority <span id="currentPriorityNumber">—</span>
                                </span>
                            </div>
                            <div class="priority-list-card">
                                <div class="priority-list-header">
                                    <span class="material-icons">format_list_numbered</span>
                                    Existing Deductions — drag to reorder
                                </div>
                                <ul id="sortableDeductions"></ul>
                            </div>
                            <input type="hidden" name="priority" id="priorityInput">
                        </div>
                        <div class="priority-guide-card">
                            <div class="guide-title">
                                <span class="material-icons">help_outline</span> Priority Guide
                            </div>
                            <ol>
                                <li>Lower number = Higher priority</li>
                                <li>Priority 1 deducted first</li>
                                <li>Drag items to change order</li>
                            </ol>
                            <div class="form-section-label" style="font-size:10px;">Example Order</div>
                            <div class="example-item">1️⃣ Statutory</div>
                            <div class="example-item">2️⃣ Loans</div>
                            <div class="example-item">3️⃣ SACCO</div>
                            <div class="example-item">4️⃣ Welfare</div>
                            <div class="example-item">5️⃣ Other</div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="document.getElementById('addModal').classList.remove('open')">
                <span class="material-icons">close</span> Cancel
            </button>
            <button type="submit" form="payrollForm" class="btn btn-save">
                <span class="material-icons">save</span> Save Item
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     EDIT MODAL
══════════════════════════════════════════════════════ -->
<div class="modal-backdrop-custom" id="editModal">
    <div class="modal-card">

        <div class="modal-header">
            <div class="modal-header-icon">
                <span class="material-icons">edit</span>
            </div>
            <div style="flex:1;">
                <div class="modal-header-title">Edit Payroll Item</div>
                <div class="modal-header-subtitle" id="editModalSubtitle">Loading…</div>
            </div>
            <button class="modal-close-btn" onclick="document.getElementById('editModal').classList.remove('open')">
                <span class="material-icons">close</span>
            </button>
        </div>

        <div class="modal-body">
            <form id="editpayrollForm">
                @csrf
                <input type="hidden" id="editid" name="editid">

                <div class="form-section">
                    <p class="form-section-label">Basic Information</p>
                    <div class="form-grid">
                        <div class="field fc-2">
                            <label>Code</label>
                            <input type="text" id="editCode" name="editCode" autocomplete="off">
                        </div>
                        <div class="field fc-6">
                            <label>Description</label>
                            <input type="text" id="editDescription" name="editDescription" readonly>
                        </div>
                        <div class="field fc-4">
                            <label>Category</label>
                            <div class="select-wrap">
                                <select id="editCategory" name="editCategory">
                                    <option value="">Select category</option>
                                    <option value="normal">Normal</option>
                                    <option value="balance">Balance</option>
                                    <option value="loan">Loan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-label">Process Type</p>
                    <div class="form-grid">
                        <div class="field fc-4">
                            <label>Process Type</label>
                            <div class="seg-toggle">
                                <input type="radio" id="editAmount" name="editProcessType" value="Amount">
                                <label for="editAmount">Amount</label>
                                <input type="radio" id="editCalculation" name="editProcessType" value="Calculation">
                                <label for="editCalculation">Calculation</label>
                            </div>
                        </div>
                        <div class="field fc-4" id="editBalanceOptions" style="display:none;">
                            <label>Balance Type</label>
                            <div class="seg-toggle">
                                <input type="radio" id="editIncreasing" name="editBalanceType" value="Increasing">
                                <label for="editIncreasing">Increasing</label>
                                <input type="radio" id="editReducing" name="editBalanceType" value="Reducing">
                                <label for="editReducing">Reducing</label>
                            </div>
                        </div>
                        <div class="field fc-2" id="editLoanRateField" style="display:none;">
                            <label>Rate</label>
                            <input type="text" id="editRate" name="editRate" placeholder="0.00" autocomplete="off">
                        </div>
                        <div class="field fc-4" id="editLoanRate" style="display:none;">
                            <label>Recovery &amp; Interest</label>
                            <div class="seg-toggle" id="recint-toggleedit">
                                <input type="radio" id="recintredit" name="editrecintres" value="1" checked>
                                <label for="recintredit">Recov &amp; Int</label>
                                <input type="radio" id="separatedit" name="editrecintres" value="0">
                                <label for="separatedit">Separate</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-label">Payroll Code Type &amp; GL Accounts</p>
                    <div class="form-grid">
                        <div class="fc-6">
                            <div class="fieldset-box">
                                <span class="fieldset-legend">Code Type</span>
                                <div class="form-grid" style="margin-top:6px;">
                                    <div class="field fc-12">
                                        <label>Type</label>
                                        <div class="select-wrap">
                                            <select id="editProcessSty" name="editProcessSty">
                                                <option value="">Select type</option>
                                                <option value="Payment">Payment</option>
                                                <option value="Deduction">Deduction</option>
                                                <option value="Benefit">Benefit</option>
                                                <option value="Relief">Relief</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>Variable / Fixed</label>
                                        <div class="seg-toggle" id="editVarOrFixedToggle">
                                            <input type="radio" id="editVariable" name="editVarOrFixed" value="Variable" checked>
                                            <label for="editVariable">Variable</label>
                                            <input type="radio" id="editFixed" name="editVarOrFixed" value="Fixed">
                                            <label for="editFixed">Fixed</label>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>Tax Status</label>
                                        <div class="seg-toggle" id="editTaxableToggle">
                                            <input type="radio" id="editTaxable" name="editTaxOrNon" value="Taxable" checked>
                                            <label for="editTaxable">Taxable</label>
                                            <input type="radio" id="editNonTax" name="editTaxOrNon" value="Non-taxable">
                                            <label for="editNonTax">Non-taxable</label>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <div class="chip-row">
                                            <div class="chip-check">
                                                <input type="checkbox" id="saccoeditcheck" name="saccoeditcheck" value="Yes">
                                                <label for="saccoeditcheck">
                                                    <span class="material-icons">savings</span> Sacco Related
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field fc-12" id="saccoeditnames" style="display:none;">
                                        <label>Staff List</label>
                                        <div class="select-wrap">
                                            <select id="staffSelect8" name="staffSelect8">
                                                <option value="">Select Staff</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fc-6">
                            <div class="fieldset-box">
                                <span class="fieldset-legend">GL Accounts</span>
                                <div class="form-grid" style="margin-top:6px;">
                                    <div class="field fc-12">
                                        <div class="chip-row">
                                            <div class="chip-check">
                                                <input type="checkbox" id="editPjornal">
                                                <label for="editPjornal">
                                                    <span class="material-icons">link</span> Link to Payroll Journal
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field fc-12">
                                        <label>A/C Number</label>
                                        <input type="text" id="editAccountNumber" placeholder="Account number" autocomplete="off">
                                    </div>
                                    <div class="field fc-12">
                                        <label>Cost Centre</label>
                                        <input type="text" id="editCc" placeholder="CC" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-label">Flags &amp; Relief</p>
                    <div class="form-grid">
                        <div class="field fc-6">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="editExemptionBonuses">
                                    <label for="editExemptionBonuses">Exemption / Bonuses / Overtime &amp; Retirement</label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-3">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="editAppearInP9" name="editAppearInP9">
                                    <label for="editAppearInP9">
                                        <span class="material-icons">description</span> Appear in P9
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-12">
                            <label>Relief Type</label>
                            <div class="seg-toggle seg-3" id="editReliefToggle">
                                <input type="radio" id="editNone" name="editRelief" value="NONE" checked>
                                <label for="editNone">Not Relief</label>
                                <input type="radio" id="editRNT" name="editRelief" value="RELIEF ON TAXABLE">
                                <label for="editRNT">Relief on Taxable</label>
                                <input type="radio" id="editRNP" name="editRelief" value="Relief on Paye">
                                <label for="editRNP">Relief on PAYE</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-label">Calculation</p>
                    <div class="form-grid">
                        <div class="field fc-12">
                            <div class="chip-row">
                                <div class="chip-check">
                                    <input type="checkbox" id="editCalculationCheck">
                                    <label for="editCalculationCheck"><span class="material-icons">functions</span> Calculation</label>
                                </div>
                                <div class="chip-check">
                                    <input type="checkbox" id="editcumulative" name="editcalctype" value="cumulative">
                                    <label for="editcumulative"><span class="material-icons">stacked_line_chart</span> Cumulative Value</label>
                                </div>
                                <div class="chip-check">
                                    <input type="checkbox" id="editcasual" name="editcalctype" value="casual">
                                    <label for="editcasual"><span class="material-icons">person_outline</span> Casual</label>
                                </div>
                            </div>
                        </div>
                        <div class="field fc-12">
                            <label>Formula</label>
                            <input type="text" id="editinputField" readonly placeholder="Formula will appear here" required>
                        </div>
                        <div class="field fc-3" id="editloanhelper" style="display:none;">
                            <label>Interest Code</label>
                            <input type="text" id="editinterestcode" name="interestcode" autocomplete="off">
                        </div>
                        <div class="field fc-5" id="editloanhelperDesc" style="display:none;">
                            <label>Interest Description</label>
                            <input type="text" id="editinterestdesc" name="interestdesc" autocomplete="off">
                        </div>
                        <div class="fc-12">
                            <div class="field-error" id="editfeedback"></div>
                        </div>
                    </div>
                </div>

                <!-- Edit priority section -->
                <div id="prioreSection" style="display:none;">
                    <div class="form-section-label" style="margin-bottom:14px;">Deduction Priority</div>
                    <div class="priority-info-banner">
                        <span class="material-icons">drag_indicator</span>
                        <span><strong>Drag and drop</strong> to set the deduction priority order.</span>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 220px;gap:16px;">
                        <div>
                            <div class="priority-current-card">
                                <div>
                                    <div class="label-tiny">Current Deduction</div>
                                    <div class="item-name" id="editItemName">—</div>
                                    <div class="item-code" id="eItemCode">Code will appear here</div>
                                </div>
                                <span class="priority-badge-pill" id="editPriorityBadge">
                                    Priority <span id="editPriorityNumber">—</span>
                                </span>
                            </div>
                            <div class="priority-list-card">
                                <div class="priority-list-header">
                                    <span class="material-icons">format_list_numbered</span>
                                    Existing Deductions — drag to reorder
                                </div>
                                <ul id="editsortableDeductions"></ul>
                            </div>
                            <input type="hidden" name="priority" id="editpriorityInput">
                        </div>
                        <div class="priority-guide-card">
                            <div class="guide-title">
                                <span class="material-icons">help_outline</span> Priority Guide
                            </div>
                            <ol>
                                <li>Lower number = Higher priority</li>
                                <li>Priority 1 deducted first</li>
                                <li>Drag items to change order</li>
                            </ol>
                            <div class="form-section-label" style="font-size:10px;">Example Order</div>
                            <div class="example-item">1️⃣ Statutory</div>
                            <div class="example-item">2️⃣ Loans</div>
                            <div class="example-item">3️⃣ SACCO</div>
                            <div class="example-item">4️⃣ Welfare</div>
                            <div class="example-item">5️⃣ Other</div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="document.getElementById('editModal').classList.remove('open')">
                <span class="material-icons">close</span> Cancel
            </button>
            <button type="button" id="saveChangesButton" class="btn btn-save">
                <span class="material-icons">save</span> Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Keep all your original scripts exactly as-is -->
<script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script nonce="{{ $cspNonce }}">
    const amanage    = '{{ route("pitems.store") }}';
    const update     = '{{ route("pitems.update") }}';
    const updateorder= '{{ route("payroll.deductions.update-priorities") }}';
    const loadpriori = '{{ route("payroll.deductions.priorities") }}';
</script>

<script src="{{ asset('js/pitems.js') }}"></script>

<script nonce="{{ $cspNonce }}">
/* ── Action menu toggle ───────────────────────────────── */
function toggleMenu(btn) {
    const menu = btn.nextElementSibling;
    const isOpen = menu.classList.contains('open');
    closeMenus();
    if (!isOpen) menu.classList.add('open');
}

function closeMenus() {
    document.querySelectorAll('.action-menu.open').forEach(m => m.classList.remove('open'));
}

document.addEventListener('click', e => {
    if (!e.target.closest('.action-wrap')) closeMenus();
});

/* ── Wire openEditModal to new modal ─────────────────── */


/* ── Close modals on backdrop click ──────────────────── */
['addModal', 'editModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});

/* ── Toast helper (if not defined in pitems.js) ──────── */
if (typeof showToast === 'undefined') {
    window.showToast = function(type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success: 'check_circle', danger: 'error_outline', warning: 'warning_amber' };
        const t = document.createElement('div');
        t.className = `toast-msg ${type}`;
        t.innerHTML = `<span class="material-icons">${icons[type] || 'info'}</span>
                       <div><strong>${title}</strong> ${message ? ' — ' + message : ''}</div>`;
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    };
}
</script>

</x-custom-admin-layout>