<x-custom-admin-layout>
@vite(['resources/css/pages/closep.css']) 
<div class="close-period-page">
 
    <div class="page-heading">
        <h1>Close Period</h1>
        <p>Finalise the current payroll period and distribute agent payslips.</p>
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
    <div id="successMessage"></div>
 
    <div class="cp-layout">
 
        {{-- ── Left: Close period ──────────────────────────── --}}
        <div class="cp-card">
            <div class="cp-card-head">
                <div class="cp-icon red"><span class="material-icons">event_busy</span></div>
                <div>
                    <p class="cp-card-title">Current Period</p>
                    <p class="cp-card-subtitle">Close when payroll is finalised</p>
                </div>
            </div>
 
            <div class="cp-card-body">
                <div class="period-grid">
                    <div class="period-cell">
                        <div class="lbl">Month</div>
                        <div class="val">{{ $month }}</div>
                    </div>
                    <div class="period-cell">
                        <div class="lbl">Year</div>
                        <div class="val">{{ $year }}</div>
                    </div>
                </div>
 
                {{-- Hidden fields for JS compatibility --}}
                <input type="hidden" id="currentMonth" value="{{ $month }}">
                <input type="hidden" id="currentYear"  value="{{ $year }}">
 
                <div class="cp-alert">
                    <span class="material-icons">warning_amber</span>
                    <span>Closing a period is <strong>irreversible</strong>. Ensure all data is reviewed and approved before proceeding.</span>
                </div>
 
                <button id="load" type="button" class="btn btn-danger-action">
                    <span class="material-icons">lock</span>
                    Close Period
                </button>
            </div>
        </div>
 
        {{-- ── Right: Bulk payslip generation ──────────────── --}}
        <div class="cp-card">
            <div class="cp-card-head">
                <div class="cp-icon green"><span class="material-icons">mail</span></div>
                <div>
                    <p class="cp-card-title">Bulk Payslip Generation</p>
                    <p class="cp-card-subtitle">Generate &amp; optionally email payslips to agents</p>
                </div>
            </div>
 
            <div class="cp-card-body">
                <form id="bulkPayslipForm">
                    @csrf
 
                    <div class="dispgrid">
 
                        <div class="cp-field">
                            <label>Period <span class="spancolor">*</span></label>
                            <div class="select-wrap">
                                <select id="period" name="period" required>
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
 
                        <div class="cp-field">
                            <label>Download Method <span class="spancolor">*</span></label>
                            <div class="select-wrap">
                                <select id="download_method" name="download_method" required>
                                    <option value="nogeneration">No Download</option>
                                    <option value="zip">ZIP — file</option>
                                    
                                </select>
                            </div>
                            <span class="field-hint">ZIP: all payslips in one archive · Individual: separate PDFs per agent</span>
                        </div>
 
                    </div>
 
                    {{-- Email chip --}}
                    <div class="cp-field">
                        <label>Email Delivery</label>
                        <label class="email-chip" id="emailChip">
                            <input type="checkbox" id="send_email" name="send_email" value="1">
                            <div class="ec-check">
                                <span class="material-icons">check</span>
                            </div>
                            <span class="material-icons mail-icon">email</span>
                            Send payslips via email
                        </label>
                        <span class="pwd-badge">
                            <span class="material-icons">lock</span>
                            Payslip password: Employee's KRA PIN
                        </span>
                    </div>
 
                    <button type="submit" class="btn btn-generate" id="generateBtn">
                        <span class="material-icons">download</span>
                        Generate &amp; Download Payslips
                    </button>
 
                </form>
 
                {{-- Inline progress — shown by JS --}}
                <div id="progressSection">
                    <div class="progress-inset">
                        <div class="progress-inset-title">
                            <span class="material-icons">sync</span>
                            Generation Progress
                        </div>
                        <div class="progress-track">
                            <div id="progressBar"></div>
                        </div>
                        <p id="progressMessage">Starting…</p>
                        <p id="progressStats"></p>
                    </div>
 
                    <div id="downloadLinksSection">
                        <h6>Download Files</h6>
                        <div id="downloadLinks"></div>
                    </div>
                </div>
 
            </div>
        </div>
 
    </div>
</div>
 
{{-- ── Sending progress modal (#progress-modal — ID preserved) ── --}}
<div class="pm-backdrop" id="progress-modal">
    <div class="pm-card">
        <div class="pm-spin-icon">
            <span class="material-icons">sync</span>
        </div>
        <h3>Sending</h3>
        <p id="progress-message">Preparing payslips…</p>
        <div class="pm-track">
            <div id="progress-bar"></div>
        </div>
    </div>
</div>


    
   

    @vite(['resources/js/closep.js'])
    
  
</x-custom-admin-layout>    