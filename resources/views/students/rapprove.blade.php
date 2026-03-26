<x-custom-admin-layout>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<style nonce="{{ $cspNonce }}">
    /* ── Page-specific — tokens from corepay.css ─────────────── */

    .kyc-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }

    /* ── Page header ─────────────────────────────────────────── */
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

    /* ── Table card ──────────────────────────────────────────── */
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
        gap: 10px;
        padding: 18px 24px;
        border-bottom: 1px solid var(--border);
    }

    .toolbar-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .toolbar-icon .material-icons { font-size: 18px; color: var(--accent); }

    .toolbar-title {
        font-family: var(--font-head);
        font-size: 15px; font-weight: 700; color: var(--ink);
    }

    .toolbar-subtitle { font-size: 12px; color: var(--muted); }

    /* Table */
    .table-wrap { overflow-x: auto; }

    table.kyc-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
        font-family: var(--font-body);
    }

    table.kyc-table thead th {
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

    table.kyc-table thead th:first-child { padding-left: 24px; }
    table.kyc-table thead th:last-child  { padding-right: 24px; }

    table.kyc-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f3f4f8;
        vertical-align: middle;
        color: var(--ink);
    }

    table.kyc-table tbody td:first-child { padding-left: 24px; }
    table.kyc-table tbody td:last-child  { padding-right: 24px; }

    table.kyc-table tbody tr:last-child td { border-bottom: none; }
    table.kyc-table tbody tr { transition: background .15s; }
    table.kyc-table tbody tr:hover td { background: #f8faff; }

    /* Agent cell */
    .agent-cell { display: flex; flex-direction: column; gap: 2px; }
    .agent-name { font-weight: 600; color: var(--ink); }
    .agent-id   { font-size: 12px; color: var(--muted); }

    /* Submitter cell */
    .submitter-cell { display: flex; flex-direction: column; gap: 2px; }
    .submitter-name { font-weight: 500; }
    .submitter-time { font-size: 12px; color: var(--muted); }

    /* Fields badge */
    .fields-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: var(--accent-lt);
        border-radius: 100px;
        font-size: 12px;
        font-weight: 600;
        color: var(--accent);
    }

    .fields-badge .material-icons { font-size: 13px; }

    /* Review button */
    .btn-review {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 34px;
        padding: 0 14px;
        background: var(--accent-lt);
        border: 1.5px solid #bfdbfe;
        border-radius: var(--radius-sm);
        font-family: var(--font-body);
        font-size: 13px;
        font-weight: 600;
        color: var(--accent);
        cursor: pointer;
        transition: all .2s;
    }

    .btn-review:hover {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(26,86,219,.25);
    }

    .btn-review .material-icons { font-size: 15px; }

    /* Empty state */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 24px;
        text-align: center;
        color: var(--muted);
    }

    .empty-state .material-icons { font-size: 44px; color: #d1d5db; margin-bottom: 12px; }
    .empty-state p { font-size: 14px; margin: 0; }

    /* ── Review modal ────────────────────────────────────────── */
    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        backdrop-filter: blur(4px);
        z-index: 8000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-backdrop-custom.open { display: flex; }

    .modal-card {
        background: var(--surface);
        border-radius: 20px;
        width: 100%;
        max-width: 640px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        animation: fadeUp .3s cubic-bezier(.22,.61,.36,1) both;
        overflow: hidden;
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

    /* Modal body */
    .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 24px 26px;
    }

    /* Meta info strip */
    .meta-strip {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 22px;
    }

    .meta-item {
        background: var(--bg);
        border-radius: var(--radius-sm);
        padding: 12px 14px;
    }

    .meta-item .meta-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--muted);
        margin-bottom: 4px;
    }

    .meta-item .meta-value {
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
    }

    /* Changes table */
    .changes-section-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--muted);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .changes-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    .changes-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .changes-table thead th {
        background: #f9fafb;
        color: var(--muted);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: 9px 14px;
        border-bottom: 1px solid var(--border);
    }

    .changes-table tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #f3f4f8;
        vertical-align: middle;
    }

    .changes-table tbody tr:last-child td { border-bottom: none; }

    .changes-table .field-name { font-weight: 600; color: var(--ink); }

    .changes-table .old-val {
        color: var(--danger);
        text-decoration: line-through;
        font-size: 12.5px;
    }

    .changes-table .new-val {
        color: var(--success);
        font-weight: 600;
    }

    /* Rejection textarea */
    .rejection-section {
        padding: 0 26px 22px;
        flex-shrink: 0;
        border-top: 1px solid var(--border);
        padding-top: 18px;
    }

    .rejection-label {
        font-size: 12.5px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .rejection-label .material-icons { font-size: 15px; color: var(--muted); }

    .rejection-textarea {
        width: 100%;
        height: 80px;
        padding: 10px 13px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: var(--font-body);
        font-size: 13.5px;
        color: var(--ink);
        background: #fafafa;
        resize: vertical;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .rejection-textarea:focus {
        border-color: var(--danger);
        background: var(--surface);
        box-shadow: 0 0 0 3.5px rgba(220,38,38,.08);
    }

    /* Modal footer */
    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 26px;
        border-top: 1px solid var(--border);
        flex-shrink: 0;
        background: #fafafa;
    }

    /* Shared button base */
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
    }

    .btn .material-icons { font-size: 16px; }
    .btn:hover { transform: translateY(-1px); }
    .btn:active { transform: translateY(0); }

    .btn-approve {
        background: linear-gradient(135deg, #059669, #10b981);
        color: #fff;
        box-shadow: 0 4px 14px rgba(5,150,105,.28);
    }

    .btn-approve:hover { box-shadow: 0 7px 18px rgba(5,150,105,.38); filter: brightness(1.05); }

    .btn-reject {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: #fff;
        box-shadow: 0 4px 14px rgba(220,38,38,.25);
    }

    .btn-reject:hover { box-shadow: 0 7px 18px rgba(220,38,38,.35); filter: brightness(1.05); }

    .btn-ghost-modal {
        background: var(--surface);
        color: var(--muted);
        border: 1.5px solid var(--border);
    }

    .btn-ghost-modal:hover { color: var(--ink); border-color: #9ca3af; }

    /* Loading skeleton */
    .skeleton-line {
        height: 14px;
        background: linear-gradient(90deg, #f3f4f8 25%, #e9ecef 50%, #f3f4f8 75%);
        background-size: 200% 100%;
        border-radius: 6px;
        animation: shimmer 1.5s infinite;
        margin-bottom: 10px;
    }

    @keyframes shimmer { to { background-position: -200% 0; } }

    /* ── Toast ───────────────────────────────────────────────── */
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

    @media (max-width: 640px) {
        .kyc-page { padding: 18px 14px; }
        .meta-strip { grid-template-columns: 1fr; }
        .modal-card { max-height: 95vh; }
    }
</style>

<div class="kyc-page">

    <div class="page-header">
        <div class="page-heading">
            <h1>Pending KYC Updates</h1>
            <p>Review and approve or reject agent KYC change requests.</p>
        </div>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <div class="table-card">

        <div class="table-toolbar">
            <div class="toolbar-icon">
                <span class="material-icons">manage_accounts</span>
            </div>
            <div>
                <div class="toolbar-title">KYC Review Queue</div>
                <div class="toolbar-subtitle">
                    {{ $pendingUpdates->count() }} pending {{ Str::plural('request', $pendingUpdates->count()) }}
                </div>
            </div>
        </div>

        <div class="table-wrap">
            @if($pendingUpdates->count() > 0)
            <table class="kyc-table">
                <thead>
                    <tr>
                        <th>Agent</th>
                        <th>Submitted By</th>
                        <th>Changes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingUpdates as $update)
                    <tr>
                        <td>
                            <div class="agent-cell">
                                <span class="agent-name">
                                    {{ $update->employee->FirstName ?? '' }} {{ $update->employee->LastName ?? '' }}
                                </span>
                                <span class="agent-id">{{ $update->empid }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="submitter-cell">
                                <span class="submitter-name">{{ $update->submitter->name ?? 'Unknown' }}</span>
                                <span class="submitter-time">{{ $update->submitted_at->format('d M Y, H:i') }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="fields-badge">
                                <span class="material-icons">edit_note</span>
                                {{ count(array_filter($update->pending_data, function($value, $key) use ($update) {
                                    return ($update->original_data[$key] ?? null) != $value;
                                }, ARRAY_FILTER_USE_BOTH)) }} fields changed
                            </span>
                        </td>
                        <td>
                            <button class="btn-review reviewBtn" data-id="{{ $update->id }}">
                                <span class="material-icons">rate_review</span> Review
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <span class="material-icons">task_alt</span>
                <p>No pending KYC updates — all clear!</p>
            </div>
            @endif
        </div>

    </div>
</div>

<!-- ── Review modal ───────────────────────────────────────── -->
<input type="hidden" id="pending_update_id">

<div class="modal-backdrop-custom" id="reviewModal">
    <div class="modal-card">

        <!-- Header -->
        <div class="modal-header">
            <div class="modal-header-icon">
                <span class="material-icons">rate_review</span>
            </div>
            <div style="flex:1;">
                <div class="modal-header-title">Review KYC Changes</div>
                <div class="modal-header-subtitle" id="modalAgentLabel">Loading…</div>
            </div>
            <button class="modal-close-btn" id="modalCloseBtn">
                <span class="material-icons">close</span>
            </button>
        </div>

        <!-- Scrollable body -->
        <div class="modal-body" id="reviewModalBody">
            <!-- Skeleton while loading -->
            <div id="modalSkeleton">
                <div class="skeleton-line" style="width:60%"></div>
                <div class="skeleton-line" style="width:80%"></div>
                <div class="skeleton-line" style="width:50%"></div>
            </div>
        </div>

        <!-- Rejection reason -->
        <div class="rejection-section">
            <div class="rejection-label">
                <span class="material-icons">comment</span>
                Rejection reason <span style="font-size:11px;color:var(--muted);margin-left:4px;">(required when rejecting)</span>
            </div>
            <textarea class="rejection-textarea" id="rejection_reason"
                      placeholder="Enter reason for rejection…"></textarea>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
            <button class="btn btn-ghost-modal" id="modalCancelBtn">
                <span class="material-icons">close</span> Cancel
            </button>
            <button class="btn btn-reject" id="rejectbtn">
                <span class="material-icons">cancel</span> Reject
            </button>
            <button class="btn btn-approve" id="approvebtn">
                <span class="material-icons">check_circle</span> Approve
            </button>
        </div>

    </div>
</div>

<script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function () {

    const modal        = document.getElementById('reviewModal');
    const modalBody    = document.getElementById('reviewModalBody');
    const modalAgent   = document.getElementById('modalAgentLabel');
    const skeleton     = document.getElementById('modalSkeleton');
    const hiddenId     = document.getElementById('pending_update_id');
    const rejectReason = document.getElementById('rejection_reason');

    /* ── Open modal ──────────────────────────────────── */
    document.querySelectorAll('.reviewBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            hiddenId.value = id;
            rejectReason.value = '';

            // Show modal with skeleton
            modalBody.innerHTML = '';
            modalBody.appendChild(skeleton);
            skeleton.style.display = 'block';
            modalAgent.textContent = 'Loading…';
            modal.classList.add('open');

            fetch("{{ route('registration.approvals.show', ':id') }}".replace(':id', id), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(response => {
                if (response.status !== 'success') throw new Error('Bad response');

                const pending = response.pendingUpdate;
                const changes = response.changes;

                modalAgent.textContent = `Agent ${pending.empid} · ${pending.status}`;

                // Meta strip
                let html = `
                    <div class="meta-strip">
                        <div class="meta-item">
                            <div class="meta-label">Agent ID</div>
                            <div class="meta-value">${pending.empid}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Status</div>
                            <div class="meta-value">${pending.status}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Submitted By</div>
                            <div class="meta-value">${pending.submitter ? pending.submitter.name : 'Unknown'}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Submitted At</div>
                            <div class="meta-value">${pending.submitted_at}</div>
                        </div>
                    </div>
                `;

                // Changes table
                html += `<p class="changes-section-label">Changed Fields</p>`;
                html += `
                    <table class="changes-table">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Current Value</th>
                                <th>Proposed Value</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                const keys = Object.keys(changes);
                if (keys.length > 0) {
                    keys.forEach(field => {
                        const oldVal = changes[field].old ?? '—';
                        const newVal = changes[field].new ?? '—';
                        html += `
                            <tr>
                                <td class="field-name">${field}</td>
                                <td><span class="old-val">${oldVal}</span></td>
                                <td><span class="new-val">${newVal}</span></td>
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="3" style="text-align:center;padding:20px;color:var(--muted);">
                                No changes found
                            </td>
                        </tr>
                    `;
                }

                html += '</tbody></table>';

                skeleton.style.display = 'none';
                modalBody.innerHTML = html;
            })
            .catch(() => {
                skeleton.style.display = 'none';
                modalBody.innerHTML = `
                    <div style="text-align:center;padding:32px;color:var(--danger);">
                        <span class="material-icons" style="font-size:32px;display:block;margin-bottom:8px;">error_outline</span>
                        Failed to load details. Please try again.
                    </div>`;
            });
        });
    });

    /* ── Close modal ─────────────────────────────────── */
    function closeModal() { modal.classList.remove('open'); }

    document.getElementById('modalCloseBtn').addEventListener('click', closeModal);
    document.getElementById('modalCancelBtn').addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    /* ── Approve ─────────────────────────────────────── */
    document.getElementById('approvebtn').addEventListener('click', function () {
        const id = hiddenId.value;
        if (!id) return;

        this.disabled = true;
        this.innerHTML = '<span class="material-icons" style="animation:spin 1s linear infinite">sync</span> Approving…';

        submitAction(id, 'approve', '', this, '<span class="material-icons">check_circle</span> Approve');
    });

    /* ── Reject ──────────────────────────────────────── */
    $(document).on('click', '#approvebtn', function(e){
    e.preventDefault();

    let id = $('#pending_update_id').val();

    if(!id){
        showToast("danger", "Error", "No pending update selected.");
        return;
    }

    $('#approvebtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Approving...');

    $.ajax({
        url: "{{ route('registration.approvals.approve', ':id') }}".replace(':id', id),
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function(response){
            if(response.success){
                showToast("success", "Approved", response.message);

                $('#reviewModal').modal('hide');

                // Optional: remove row from table
                $('button.reviewBtn[data-id="'+id+'"]').closest('tr').remove();
            }else{
                showToast("danger", "Error", response.message);
            }
        },
        error: function(xhr){
            console.log(xhr.responseText);
            showToast("danger", "Error", "Failed to approve update.");
        },
        complete: function(){
            $('#approvebtn').prop('disabled', false).html('<i class="fas fa-check-double"></i> Approve');
        }
    });
});
$(document).on('click', '#rejectbtn', function(e){
    e.preventDefault();

    let id = $('#pending_update_id').val();
    let reason = $('#rejection_reason').val();

    if(!id){
        showToast("danger", "Error", "No pending update selected.");
        return;
    }

    if(!reason || reason.trim() === ""){
        showToast("danger", "Rejected", "Rejection reason is required.");
        return;
    }

    $('#rejectbtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Rejecting...');

    $.ajax({
        url: "{{ route('registration.approvals.reject', ':id') }}".replace(':id', id),
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            rejection_reason: reason
        },
        success: function(response){
            if(response.success){
                showToast("success", "Rejected", response.message);

                $('#reviewModal').modal('hide');

                // Optional: remove row from table
                $('button.reviewBtn[data-id="'+id+'"]').closest('tr').remove();
            }else{
                showToast("danger", "Error", response.message);
            }
        },
        error: function(xhr){
            console.log(xhr.responseText);

            if(xhr.status === 422){
                showToast("danger", "Validation Error", "Rejection reason is required.");
            }else{
                showToast("danger", "Error", "Failed to reject update.");
            }
        },
        complete: function(){
            $('#rejectbtn').prop('disabled', false).html('<i class="fas fa-window-close"></i> Cancel');
        }
    });
});


    /* ── Toast ───────────────────────────────────────── */
    function showToast(type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success: 'check_circle', danger: 'error_outline' };
        const t = document.createElement('div');
        t.className = `toast-msg ${type}`;
        t.innerHTML = `<span class="material-icons">${icons[type]}</span>
                       <div><strong>${title}</strong> ${message}</div>`;
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }

});
</script>

</x-custom-admin-layout>