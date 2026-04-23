<x-custom-admin-layout>
 @vite(['resources/css/pages/papprove.css']) 

 
<div class="approve-page">
 
    <div class="page-heading">
        <h1>Payroll Approvals</h1>
        <p>Review and approve earnings and net pay for the current payroll period.</p>
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
 
    {{-- ═══════════════════════════════
         SECTION 1 — APPROVE EARNINGS
    ═══════════════════════════════ --}}
    <div class="a-card">
        <div class="a-card-head">
            <div class="a-card-icon green"><span class="material-icons">payments</span></div>
            <span class="a-card-title">Approve Earnings</span>
        </div>
        <div class="a-card-body">
            <form id="forecastForm">
                <div class="filter-row">
                    <div class="field minwidth">
                        <label>Earnings Item</label>
                        <div class="select-wrap">
                            <select name="pname" id="pname" required autocomplete="off">
                                <option value="">Select Item</option>
                            </select>
                        </div>
                    </div>
                    <div class="field minwidht16">
                        <label>Agent (From)</label>
                        <div class="select-wrap">
                            <select name="staffSelect3" id="staffSelect3" autocomplete="off">
                                <option value="">Select Agent</option>
                            </select>
                        </div>
                    </div>
                    <div class="field minwidht16">
                        <label>Agent (To)</label>
                        <div class="select-wrap">
                            <select name="staffSelect4" id="staffSelect4" autocomplete="off">
                                <option value="">Select Agent</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-review" id="openitems">
                        <span class="material-icons">table_view</span> Review
                    </button>
                </div>
            </form>
        </div>
    </div>
 
    {{-- ═══════════════════════════════
         SECTION 2 — PERIOD + APPROVE EARNINGS
    ═══════════════════════════════ --}}
    <div class="a-card">
        <div class="a-card-head">
            <div class="a-card-icon green"><span class="material-icons">event_available</span></div>
            <span class="a-card-title">Current Payroll Period</span>
        </div>
        <div class="a-card-body">
            <div class="period-grid">
                <div class="period-cell">
                    <div class="lbl">Month</div>
                    <div class="val">{{ $month }}</div>
                    <input type="hidden" id="currentMonth" value="{{ $month }}">
                </div>
                <div class="period-cell">
                    <div class="lbl">Year</div>
                    <div class="val">{{ $year }}</div>
                    <input type="hidden" id="currentYear" value="{{ $year }}">
                </div>
            </div>
 
            <div class="callout warning">
                <span class="material-icons">warning_amber</span>
                <span>Approving earnings is <strong>final</strong> for this period. Ensure all data has been reviewed before proceeding.</span>
            </div>
 
            <button id="approve" type="button" class="btn btn-approve">
                <span class="material-icons">done_all</span> Approve Earnings
            </button>
        </div>
    </div>
 
    {{-- ═══════════════════════════════
         SECTION 3 — APPROVE NET PAY
    ═══════════════════════════════ --}}
    <div class="a-card">
        <div class="a-card-head">
            <div class="a-card-icon" id="netpayCardIcon">
                <span class="material-icons">verified</span>
            </div>
            <span class="a-card-title">Approve Net Pay</span>
        </div>
        <div class="a-card-body">
            <form id="netpayForm">
 
                <div class="filter-row margbottom">
                    <div class="field minwidht16">
                        <label>Agent (From)</label>
                        <div class="select-wrap">
                            <select name="staffSelect5" id="staffSelect5" autocomplete="off">
                                <option value="">Select Agent</option>
                            </select>
                        </div>
                    </div>
                    <div class="field minwidht16">
                        <label>Agent (To)</label>
                        <div class="select-wrap">
                            <select name="staffSelect6" id="staffSelect6" autocomplete="off">
                                <option value="">Select Agent</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-review" id="openitems2">
                        <span class="material-icons">table_view</span> Review
                    </button>
                </div>
 
                {{-- Approval toggle row --}}
                <div class="filter-row">
                    <div class="toggle-field">
                        <span class="lbl lblspan">Approval Mode</span>
                        <div class="approval-toggle">
                            <input type="checkbox" id="approvalToggle" checked>
                            <button type="button" class="toggle-track on" id="toggleTrack"
                                    onclick="toggleApproval()" aria-label="Toggle approval mode">
                            </button>
                            <span id="toggleText" class="toggle-text on">Approve</span>
                        </div>
                    </div>
 
                    <button type="submit" id="approveBtn" class="btn btn-approve">
                        <span class="material-icons">done_all</span> Approve
                    </button>
 
                    <button type="submit" id="rejectBtn" class="btn btn-reject">
                        <span class="material-icons">cancel</span> Reject
                    </button>
                </div>
 
                {{-- Feedback (shown when rejecting) --}}
                <div class="feedback-block" id="feedbackSection">
                    <label>Reason for Rejection</label>
                    <textarea id="rejection_reason" name="rejection_reason"
                              rows="3" placeholder="Enter reason…"></textarea>
                </div>
 
            </form>
        </div>
    </div>
 
</div>{{-- /approve-page --}}
 
{{-- ── PDF Report Modal ─────────────────────────────────────── --}}
<div class="pdf-modal-backdrop" id="staffreportModal">
    <div class="pdf-modal-card" id="staffreportModalCard">
        <div class="pdf-modal-header">
            <div class="pdf-modal-icon"><span class="material-icons">picture_as_pdf</span></div>
            <span class="pdf-modal-title" id="pdfModalTitle">Report Viewer</span>
            <div class="pdf-modal-actions">
                <button class="btn btn-download" id="pdfDownloadBtn">
                    <span class="material-icons font15">download</span> Download
                </button>
                <button class="btn-icon " id="pdfPrintBtn" title="Print">
                    <span class="material-icons">print</span>
                </button>
                <button class="btn-icon" id="closemodal">
                    <span class="material-icons">close</span>
                </button>
            </div>
        </div>
        <div class="pdf-modal-body" id="staffrpt-pdf-container">
            <div class="pdf-loading" id="pdfLoading">
                <span class="material-icons">sync</span>
                <span>Loading report…</span>
            </div>
        </div>
    </div>
</div>


    
     @vite(['resources/js/papprove.js'])
</x-custom-admin-layout>