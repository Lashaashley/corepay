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
            <div class="flexone">
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
                <div class="skeleton-line width60" ></div>
                <div class="skeleton-line width80" ></div>
                <div class="skeleton-line width50" ></div>
            </div>
        </div>

        <!-- Rejection reason -->
        <div class="rejection-section">
            <div class="rejection-label">
                <span class="material-icons">comment</span>
                Rejection reason <span class="spanreg" >(required when rejecting)</span>
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



@vite(['resources/js/rapprove.js'])

</x-custom-admin-layout>