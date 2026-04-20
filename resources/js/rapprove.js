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

            const showurl = window.App.routes.showfields.replace('__id__', id);

            fetch(showurl, {
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
                            <td colspan="3" class="muted" >
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
                    <div class="paddang" >
                        <span class="material-icons font32" >error_outline</span>
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
        this.innerHTML = '<span class="material-icons anime" >sync</span> Approving…';

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

    const approveurl = window.App.routes.regapprove.replace('__id__', id);

    $.ajax({
        url: approveurl,
        type: "POST",
        data: {
            
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

    const rejecturl = window.App.routes.regapprovrej.replace('__id__', id);

    $.ajax({
        url: rejecturl,
        type: "POST",
        data: {
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