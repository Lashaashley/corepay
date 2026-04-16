<x-custom-admin-layout>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
 @vite(['resources/css/pages/rapprove.css']) 

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