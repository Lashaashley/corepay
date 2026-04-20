<x-custom-admin-layout>

{{-- Highcharts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/highcharts.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/exporting.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/accessibility.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/series-label.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.1/modules/export-data.min.js"></script>
<script src="https://code.highcharts.com/11.4.1/modules/csp.js"></script>

@vite(['resources/css/pages/dashboard.css']) 

<div class="dashboard-page">

    {{-- ── 2FA Security Banner ──────────────────────────────── --}}
    @if(!Auth::user()->google2fa_secret)

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
                            <span class="gr-dot grlebel"></span>
                            {{ $gender->Gender }}
                        </span>
                        <span class="gr-count grcount">{{ $gender->genderCount }}</span>
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
                    <div class="nobranch">No branch data</div>
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
                <div id="attendanceChartContainer" class="attendanceChartContainer"></div>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-card-head">
                <div class="chart-head-icon green"><span class="material-icons">trending_up</span></div>
                <span class="chart-title">Turnover Trends</span>
            </div>
            <div class="chart-body">
                <div id="container"></div>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-card-head">
                <div class="chart-head-icon purple"><span class="material-icons">payments</span></div>
                <span class="chart-title">Net Pay</span>
            </div>
            <div class="chart-body">
                <div id="netpayChartContainer"></div>
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


@vite(['resources/js/dash.js'])

</x-custom-admin-layout>