<x-custom-admin-layout>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/pages/preports.css'])
</head>
 
<div class="reports-page">
 
    <div class="page-heading">
        <h1>Payroll Reports</h1>
        
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
 
    @if(session('success'))
        <div class="locked-banner backver">
            <span class="material-icons versuccess">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
 
    {{-- ── Locked banner (netpay not approved) ─────────────── --}}
    @if(!$netpayApproved)
    <div class="locked-banner" id="netpay-warning-alert">
        <span class="material-icons">warning_amber</span>
        <div>
            <strong>Bank Interface Unavailable</strong>
            Netpay for {{ $month }} {{ $year }} is currently
            <span class="spanfont">{{ $netpayStatus }}</span>.
            The Bank Interface tab will be enabled once netpay is approved.
        </div>
    </div>
    @endif
 
    {{-- ── Tab bar ──────────────────────────────────────────── --}}
    <div class="tab-bar" id="tabBar">
        <button class="tab-btn active" data-tab="deductions">
            <span class="material-icons">receipt</span> Agent Payslips
        </button>
        <button class="tab-btn" data-tab="summaries" id="summaries-tab">
            <span class="material-icons">summarize</span> Summaries
        </button>
        <button class="tab-btn" data-tab="overview" id="overview-tab">
            <span class="material-icons">bar_chart</span> Overview
        </button>
        <button class="tab-btn" data-tab="variance" id="variance-tab">
            <span class="material-icons">compare_arrows</span> Variance Reports
        </button>
        <button class="tab-btn {{ !$netpayApproved ? 'disabled' : '' }}"
                data-tab="binterface"
                id="binterface-tab"
                data-netpay-approved="{{ $netpayApproved ? 'true' : 'false' }}"
                data-netpay-status="{{ $netpayStatus }}">
            @if(!$netpayApproved)
                <span class="material-icons lock-icon">lock</span>
            @else
                <span class="material-icons">account_balance</span>
            @endif
            Bank Interface
        </button>
    </div>
 
    {{-- ── Tab body ─────────────────────────────────────────── --}}
    <div class="tab-body">
 
        {{-- ═══════════════════════════════════════
             AGENT PAYSLIPS
        ═══════════════════════════════════════ --}}
        <div class="tab-panel active" id="panel-deductions">
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">person</span></div>
                    Agent Payslip Viewer
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field minwidth20">
                            <label>Agent</label>
                            <div class="select-wrap">
                                <select name="staffid" id="staffid" required autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="period" id="period" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view view-slip" id="vpslip">
                            <span class="material-icons">visibility</span> View
                        </button>
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ═══════════════════════════════════════
             SUMMARIES
        ═══════════════════════════════════════ --}}
        <div class="tab-panel" id="panel-summaries">
 
            {{-- Overall summary --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">summarize</span></div>
                    Overall Summary
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto" id="periodoveral" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="openovral">
                            <span class="material-icons">visibility</span> View
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- Items listing --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">list_alt</span></div>
                    Items Listing
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field minwidth18" >
                            <label>Payroll Item</label>
                            <div class="select-wrap">
                                <select name="pname" id="pname" required autocomplete="off">
                                    <option value="">Select Item</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field minwidth16">
                            <label>Agent (From)</label>
                            <div class="select-wrap">
                                <select name="staffSelect3" id="staffSelect3" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field minwidth16">
                            <label>Agent (To)</label>
                            <div class="select-wrap">
                                <select name="staffSelect4" id="staffSelect4" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto2" id="periodoveral2" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="openitems">
                            <span class="material-icons">table_view</span> Open
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- Statutory returns --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon successlt" >
                        <span class="material-icons versuccess">account_balance</span>
                    </div>
                    Statutory Returns
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Statutory Item</label>
                            <div class="select-wrap">
                                <select name="statutory" id="statutory" required autocomplete="off">
                                    <option value="">Select Item</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field minwidth16">
                            <label>Agent (From)</label>
                            <div class="select-wrap">
                                <select name="staffSelect5" id="staffSelect5" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field minwidth16">
                            <label>Agent (To)</label>
                            <div class="select-wrap">
                                <select name="staffSelect6" id="staffSelect6" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto3" id="periodoveral3" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="openstatutory" disabled>
                            <span class="material-icons">visibility</span> Open
                        </button>
                        <button class="btn btn-download" id="downstatutory" disabled>
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
        </div>
 
        {{-- ═══════════════════════════════════════
             OVERVIEW
        ═══════════════════════════════════════ --}}
        <div class="tab-panel" id="panel-overview">
 
            {{-- Payroll summary --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">bar_chart</span></div>
                    Payroll Summary
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field minwidth16">
                            <label>Agent (From)</label>
                            <div class="select-wrap">
                                <select name="staffSelect7" id="staffSelect7" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field minwidth16">
                            <label>Agent (To)</label>
                            <div class="select-wrap">
                                <select name="staffSelect8" id="staffSelect8" autocomplete="off">
                                    <option value="">Select Agent</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto4" id="periodoveral4" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="prolsum">
                            <span class="material-icons">table_view</span> Open
                        </button>
                        <button class="btn btn-download" id="excelsum">
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- P10 KRA --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon backfe">
                        <span class="material-icons colord9">description</span>
                    </div>
                    P10 KRA
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto5" id="periodoveral5" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-download" id="p10kra" disabled>
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- Payment Advices --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon backf0">
                        <span class="material-icons color05">payment</span>
                    </div>
                    Payment Advices
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto6" id="periodoveral6" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>Payment Method</label>
                            <div class="chip-group">
                                <div class="chip">
                                    <input type="radio" id="recintre" name="recintres" value="Etransfer" checked>
                                    <label for="recintre">Bank</label>
                                </div>
                                <div class="chip">
                                    <input type="radio" id="separate" name="recintres" value="cheque">
                                    <label for="separate">Cheque</label>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-view" id="banktrans">
                            <span class="material-icons">visibility</span> View
                        </button>
                        <button class="btn btn-download" id="banktransexce" disabled>
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
        </div>
 
        {{-- ═══════════════════════════════════════
             VARIANCE REPORTS
        ═══════════════════════════════════════ --}}
        <div class="tab-panel" id="panel-variance">
 
            {{-- Payroll items variance --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">compare_arrows</span></div>
                    Payroll Items Variance
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field minwidth18" >
                            <label>Payroll Item</label>
                            <div class="select-wrap">
                                <select name="p2name" id="p2name" required autocomplete="off">
                                    <option value="">Select Item</option>
                                </select>
                            </div>
                        </div>
                        {{-- Hidden agent selects — required by JS --}}
                        <select name="staffSelectst" id="staffSelectst" hidden autocomplete="off">
                            <option value="">Select Agent</option>
                        </select>
                        <select name="staffSelectnd" id="staffSelectnd" hidden autocomplete="off">
                            <option value="">Select Agent</option>
                        </select>
                        <div class="filter-field">
                            <label>1st Period</label>
                            <div class="select-wrap">
                                <select name="1stperiod" id="1stperiod" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>2nd Period</label>
                            <div class="select-wrap">
                                <select name="2ndperiod" id="2ndperiod" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="varitem">
                            <span class="material-icons">compare_arrows</span> Compare
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- Summary variance --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon"><span class="material-icons">trending_up</span></div>
                    Summary Variance
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>1st Period</label>
                            <div class="select-wrap">
                                <select name="s1stperiod" id="s1stperiod" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label>2nd Period</label>
                            <div class="select-wrap">
                                <select name="s2ndperiod" id="s2ndperiod" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-view" id="varsitem">
                            <span class="material-icons">compare_arrows</span> Compare
                        </button>
                    </div>
                </div>
            </div>
 
        </div>
 
        {{-- ═══════════════════════════════════════
             BANK INTERFACE
        ═══════════════════════════════════════ --}}
        <div class="tab-panel" id="panel-binterface">
 
            @if(!$netpayApproved)
            <div class="locked-banner">
                <span class="material-icons">lock</span>
                <div>
                    <strong>Access Restricted</strong>
                    Bank interface files cannot be generated until netpay for {{ $month }} {{ $year }} is approved.
                    Current status: <strong>{{ $netpayStatus }}</strong>
                </div>
            </div>
            @endif
 
            {{-- IFT --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon backef" >
                        <span class="material-icons color1a" >swap_horiz</span>
                    </div>
                    Immediate Fund Transfer (IFT)
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto7" id="periodoveral7" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-download" id="iftgen">
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- EFT --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon backef">
                        <span class="material-icons color1a">account_balance</span>
                    </div>
                    Electronic Fund Transfer (EFT)
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto8" id="periodoveral8" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-download" id="eftgen">
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
            {{-- RTGS --}}
            <div class="report-section">
                <div class="report-section-head">
                    <div class="rs-icon backef">
                        <span class="material-icons color1a">currency_exchange</span>
                    </div>
                    Real-Time Gross Settlement (RTGS)
                </div>
                <div class="report-section-body">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label>Period</label>
                            <div class="select-wrap">
                                <select name="periodto9" id="periodoveral9" required autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-download" id="rtgsgen">
                            <span class="material-icons">download</span> Download
                        </button>
                    </div>
                </div>
            </div>
 
        </div>
 
    </div>{{-- /tab-body --}}
</div>{{-- /reports-page --}}
 
{{-- ── PDF Viewer modal ─────────────────────────────────────── --}}
{{-- Replaces Bootstrap #staffreportModal --}}
<div class="pdf-modal-backdrop" id="staffreportModal">
    <div class="pdf-modal-card" id="staffreportModalCard">
        <div class="pdf-modal-header">
            <div class="pdf-modal-icon"><span class="material-icons">picture_as_pdf</span></div>
            <span class="pdf-modal-title" id="pdfModalTitle">Report Viewer</span>
            <div class="pdf-modal-actions">
                <button class="btn btn-download height34" id="pdfDownloadBtn" >
                    <span class="material-icons font15" >download</span> Download
                </button>
                <button class="btn-icon hidden" id="pdfPrintBtn"  title="Print">
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
 
{{-- ── Progress modal (download) ────────────────────────────── --}}
<div class="progress-modal-backdrop" id="progress-modal">
    <div class="progress-modal-card">
        <h3>Downloading…</h3>
        <p id="progress-message">Preparing your file.</p>
        <div class="progress-track">
            <div class="progress-fill" id="progress-bar"></div>
        </div>
    </div>
</div>
    
                

    






@vite(['resources/js/preports.js'])
</x-custom-admin-layout>
