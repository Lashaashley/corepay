<x-custom-admin-layout>

{{-- Highcharts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/highcharts.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/exporting.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/accessibility.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/series-label.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/export-data.min.js"></script>

<style>
    /* ── Page ────────────────────────────────────────────────── */
    .dashboard-page {
        padding: 28px 24px;
        background: var(--bg);
        min-height: calc(100vh - 60px);
    }

    /* ── 2FA security banner ─────────────────────────────────── */
    .security-banner {
        display: flex; align-items: center; gap: 14px;
        padding: 13px 18px; margin-bottom: 22px;
        background: linear-gradient(90deg, #fff8e1, #fffde7);
        border: 1.5px solid #fde68a; border-left: 5px solid #f59e0b;
        border-radius: 14px; box-shadow: 0 2px 12px rgba(245,158,11,.12);
        animation: bannerPulse 3s ease-in-out infinite;
    }

    @keyframes bannerPulse {
        0%,100% { box-shadow: 0 2px 12px rgba(245,158,11,.12); }
        50%      { box-shadow: 0 2px 24px rgba(245,158,11,.28); }
    }

    .security-banner .banner-icon {
        width: 38px; height: 38px; border-radius: 10px;
        background: #fef3c7;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .security-banner .banner-icon .material-icons { font-size: 20px; color: #d97706; }
    .security-banner .banner-text { flex: 1; font-size: 13.5px; color: #92400e; }
    .security-banner .banner-text strong { font-weight: 700; color: #78350f; }

    .banner-btn {
        height: 34px; padding: 0 14px; border: none; border-radius: 8px;
        background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff;
        font-family: var(--font-body); font-size: 13px; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
        text-decoration: none; transition: transform .2s, box-shadow .2s; flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(245,158,11,.3);
    }

    .banner-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(245,158,11,.4); }
    .banner-btn .material-icons { font-size: 15px; }

    .banner-dismiss {
        width: 28px; height: 28px; border: none; background: none;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: #b45309; border-radius: 6px; transition: background .2s; flex-shrink: 0;
    }

    .banner-dismiss:hover { background: #fde68a; }
    .banner-dismiss .material-icons { font-size: 17px; }

    /* ── Page heading ────────────────────────────────────────── */
    .dash-heading {
        display: flex; align-items: flex-end; justify-content: space-between;
        margin-bottom: 24px; flex-wrap: wrap; gap: 10px;
    }

    .dash-heading h1 {
        font-family: var(--font-head);
        font-size: 22px; font-weight: 700; color: var(--ink); margin: 0 0 3px;
    }

    .dash-heading p { font-size: 13.5px; color: var(--muted); margin: 0; }

    .dash-date {
        font-size: 12.5px; color: var(--muted);
        display: flex; align-items: center; gap: 5px;
    }

    .dash-date .material-icons { font-size: 15px; }

    /* ── Stat cards row ──────────────────────────────────────── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    @media (max-width: 1100px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px)  { .stat-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px 18px;
        box-shadow: var(--shadow);
        display: flex; flex-direction: column; gap: 10px;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
        position: relative; overflow: hidden;
    }

    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        border-radius: 14px 14px 0 0;
    }

    .stat-card.blue::before   { background: linear-gradient(90deg, #1a56db, #4f46e5); }
    .stat-card.green::before  { background: linear-gradient(90deg, #059669, #10b981); }
    .stat-card.purple::before { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
    .stat-card.amber::before  { background: linear-gradient(90deg, #d97706, #f59e0b); }

    .stat-card:nth-child(1) { animation-delay: 0s; }
    .stat-card:nth-child(2) { animation-delay: .05s; }
    .stat-card:nth-child(3) { animation-delay: .10s; }
    .stat-card:nth-child(4) { animation-delay: .15s; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card-top {
        display: flex; align-items: flex-start; justify-content: space-between;
    }

    .stat-icon {
        width: 40px; height: 40px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .stat-icon .material-icons { font-size: 20px; }
    .stat-icon.blue   { background: var(--accent-lt);  }
    .stat-icon.blue   .material-icons { color: var(--accent); }
    .stat-icon.green  { background: var(--success-lt); }
    .stat-icon.green  .material-icons { color: var(--success); }
    .stat-icon.purple { background: #f3f0ff; }
    .stat-icon.purple .material-icons { color: #7c3aed; }
    .stat-icon.amber  { background: #fffbeb; }
    .stat-icon.amber  .material-icons { color: #d97706; }

    .stat-card-label { font-size: 12px; font-weight: 500; color: var(--muted); }
    .stat-card-value { font-family: var(--font-head); font-size: 26px; font-weight: 700; color: var(--ink); }
    .stat-card-sub   { font-size: 12px; color: var(--muted); }

    /* Gender sub-rows */
    .gender-rows { display: flex; flex-direction: column; gap: 5px; margin-top: 2px; }

    .gender-row {
        display: flex; align-items: center; justify-content: space-between;
        font-size: 12.5px;
    }

    .gender-row .gr-label { display: flex; align-items: center; gap: 5px; font-weight: 500; }
    .gender-row .gr-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .gender-row .gr-count { font-weight: 700; }
    .gender-row .gr-pct { color: var(--muted); font-size: 11.5px; }

    /* Branch rows */
    .branch-rows { display: flex; flex-direction: column; gap: 4px; }

    .branch-row {
        display: flex; align-items: center; justify-content: space-between;
        font-size: 12.5px; padding: 4px 0;
        border-bottom: 1px dashed var(--border);
    }

    .branch-row:last-child { border-bottom: none; }
    .branch-row .br-name { font-weight: 500; color: var(--ink); }
    .branch-row .br-count {
        font-size: 11px; font-weight: 700;
        background: var(--accent-lt); color: var(--accent);
        padding: 2px 8px; border-radius: 100px;
    }

    /* ── Charts row ──────────────────────────────────────────── */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    @media (max-width: 1100px) { .charts-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 700px)  { .charts-grid { grid-template-columns: 1fr; } }

    .chart-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
        animation: fadeUp .4s cubic-bezier(.22,.61,.36,1) both;
    }

    .chart-card:nth-child(1) { animation-delay: .2s; }
    .chart-card:nth-child(2) { animation-delay: .25s; }
    .chart-card:nth-child(3) { animation-delay: .3s; }

    .chart-card-head {
        display: flex; align-items: center; gap: 8px;
        padding: 12px 16px; border-bottom: 1px solid var(--border);
    }

    .chart-head-icon {
        width: 28px; height: 28px; border-radius: 8px; background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .chart-head-icon .material-icons { font-size: 14px; color: var(--accent); }
    .chart-head-icon.green  { background: var(--success-lt); }
    .chart-head-icon.green  .material-icons { color: var(--success); }
    .chart-head-icon.purple { background: #f3f0ff; }
    .chart-head-icon.purple .material-icons { color: #7c3aed; }

    .chart-title { font-family: var(--font-head); font-size: 13px; font-weight: 700; color: var(--ink); }

    .chart-body { padding: 4px; }

    @media (max-width: 768px) {
        .dashboard-page { padding: 18px 14px; }
    }
</style>

<div class="dashboard-page">

    {{-- ── 2FA Security Banner ──────────────────────────────── --}}
    @if(!Auth::user()->google2fa_secret)
    <div class="security-banner" id="securityBanner">
        <div class="banner-icon">
            <span class="material-icons">shield</span>
        </div>
        <div class="banner-text">
            <strong>Security Recommendation:</strong>
            Enable Two-Factor Authentication to protect your account.
        </div>
        <a href="{{ route('2fa.setup') }}" class="banner-btn">
            <span class="material-icons">lock</span> Enable Now
        </a>
        <button class="banner-dismiss" onclick="document.getElementById('securityBanner').style.display='none'">
            <span class="material-icons">close</span>
        </button>
    </div>
    @endif

    {{-- ── Page heading ─────────────────────────────────────── --}}
    <div class="dash-heading">
        <div>
            <h1>Dashboard</h1>
            <p>Welcome back, {{ Auth::user()->name }}. Here's an overview of your payroll.</p>
        </div>
        <div class="dash-date">
            <span class="material-icons">calendar_today</span>
            <span id="dashDate"></span>
        </div>
    </div>

    {{-- ── Stat cards ───────────────────────────────────────── --}}
    <div class="stat-grid">

        {{-- Head count --}}
        <div class="stat-card blue">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Total Head Count</div>
                    <div class="stat-card-value">{{ $totalEmployees }}</div>
                    <div class="stat-card-sub">100% of workforce</div>
                </div>
                <div class="stat-icon blue"><span class="material-icons">groups</span></div>
            </div>
            <div class="gender-rows">
                @foreach($genderStats as $gender)
                    @php
                        $isMale = strtolower(trim($gender->Gender)) === 'male';
                        $color  = $isMale ? '#1a56db' : '#e040fb';
                        $pct    = $totalEmployees > 0 ? ($gender->genderCount / $totalEmployees) * 100 : 0;
                    @endphp
                    <div class="gender-row">
                        <span class="gr-label">
                            <span class="gr-dot" style="background:{{ $color }};"></span>
                            {{ $gender->Gender }}
                        </span>
                        <span class="gr-count" style="color:{{ $color }};">{{ $gender->genderCount }}</span>
                        <span class="gr-pct">{{ number_format($pct, 1) }}%</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Branch stats --}}
        <div class="stat-card green">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Branches</div>
                    <div class="stat-card-value">{{ $branchStats->count() }}</div>
                    <div class="stat-card-sub">Active locations</div>
                </div>
                <div class="stat-icon green"><span class="material-icons">location_city</span></div>
            </div>
            <div class="branch-rows">
                @forelse($branchStats as $branch)
                    <div class="branch-row">
                        <span class="br-name">{{ $branch->branchname }}</span>
                        <span class="br-count">{{ $branch->staffCount }} agents</span>
                    </div>
                @empty
                    <div style="font-size:12.5px;color:var(--muted);text-align:center;padding:8px 0;">No branch data</div>
                @endforelse
            </div>
        </div>

        {{-- Placeholder card 3 — ready for future metric --}}
        <div class="stat-card purple">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Payroll Status</div>
                    <div class="stat-card-value" id="dashPayrollStatus">—</div>
                    <div class="stat-card-sub" id="dashPayrollSub">Current period</div>
                </div>
                <div class="stat-icon purple"><span class="material-icons">receipt_long</span></div>
            </div>
        </div>

        {{-- Placeholder card 4 — ready for future metric --}}
        <div class="stat-card amber">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Period</div>
                    <div class="stat-card-value" id="dashPeriodValue">—</div>
                    <div class="stat-card-sub">Active payroll period</div>
                </div>
                <div class="stat-icon amber"><span class="material-icons">event</span></div>
            </div>
        </div>

    </div>

    {{-- ── Charts ────────────────────────────────────────────── --}}
    <div class="charts-grid">

        <div class="chart-card">
            <div class="chart-card-head">
                <div class="chart-head-icon"><span class="material-icons">bar_chart</span></div>
                <span class="chart-title">Agent Earnings</span>
            </div>
            <div class="chart-body">
                <div id="attendanceChartContainer" style="min-height:260px;"></div>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-card-head">
                <div class="chart-head-icon green"><span class="material-icons">trending_up</span></div>
                <span class="chart-title">Turnover Trends</span>
            </div>
            <div class="chart-body">
                <div id="container" style="min-height:260px;"></div>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-card-head">
                <div class="chart-head-icon purple"><span class="material-icons">payments</span></div>
                <span class="chart-title">Net Pay</span>
            </div>
            <div class="chart-body">
                <div id="netpayChartContainer" style="min-height:260px;"></div>
            </div>
        </div>

    </div>

</div>{{-- /dashboard-page --}}

{{-- Data bridge for dash.js — unchanged --}}
<div id="chart-data"
     data-turnover="{{ json_encode($turnoverData) }}"
     data-payments="{{ json_encode($paymentsData) }}"
     data-netpay="{{ json_encode($netpayData) }}">
</div>

<script src="{{ asset('js/dash.js') }}"></script>

<script>
/* ── Live date ───────────────────────────────────────────── */
(function() {
    var days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var now    = new Date();
    var str    = days[now.getDay()] + ', ' + months[now.getMonth()] + ' ' + now.getDate() + ' ' + now.getFullYear();
    var el = document.getElementById('dashDate');
    if (el) el.textContent = str;
})();

/* ── Period stat card (reads from page if available) ─────── */
(function() {
    // Try to populate from the Blade variables if they're passed to the view
    @if(isset($currentPeriod))
        var periodEl = document.getElementById('dashPeriodValue');
        if (periodEl) periodEl.textContent = '{{ $currentPeriod }}';
    @endif
})();
</script>

</x-custom-admin-layout>