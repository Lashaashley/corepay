<x-custom-admin-layout>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
	<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.3.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
.wrapper{
 width: 100%;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.12);
}
.wrapper header{
  display: flex;
  align-items: center;
  padding: 25px 30px 10px;
  justify-content: space-between;
}
header .icons{
  display: flex;
}
header .icons span{
  height: 38px;
  width: 38px;
  margin: 0 1px;
  cursor: pointer;
  color: #878787;
  text-align: center;
  line-height: 38px;
  font-size: 1.9rem;
  user-select: none;
  border-radius: 50%;
}
.icons span:last-child{
  margin-right: -10px;
}
header .icons span:hover{
  background: #f2f2f2;
}
header .current-date{
  font-size: 1.45rem;
  font-weight: 500;
}
.calendar{
  padding: 0px;
  
}
.calendar ul{
  display: flex;
  flex-wrap: wrap;
  list-style: none;
  text-align: center;
}
.calendar .days{
  margin-bottom: 20px;
}
.calendar li{
  color: #333;
  width: calc(100% / 7);
  font-size: 1.07rem;
}
.calendar .weeks li{
  font-weight: 500;
  cursor: default;
}
.calendar .days li{
  z-index: 1;
  cursor: pointer;
  position: relative;
  margin-top: 10px;
}
.days li.inactive{
  color: #aaa;
}
.days li.active{
  color: #fff;
}
.days li::before{
  position: absolute;
  content: "";
  left: 50%;
  top: 50%;
  height: 40px;
  width: 40px;
  z-index: -1;
  border-radius: 50%;
  transform: translate(-50%, -50%);
}
.days li.active::before{
  background: #9B59B6;
}
.days li:not(.active):hover::before{
  background: #f2f2f2;
}
.fetched-date{
    background: #5AB2FF; /* Example color */
    color: black; /* Text color */
}
	input[type="number"] {
    min-width: 50px;
}
.hidden {
    display: none;
}
.highcharts-figure,
.highcharts-data-table table {
    min-width: 360px;
    max-width: 800px;
    margin: 1em auto;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
.widget-data {
    padding-top: 0!important; /* Reduces top padding */
    padding-bottom: 0!important; /* Reduces bottom padding */
}
.dept-perf-item .progress {
    background-color: #f0f0f0;
    border-radius: 5px;
    overflow: hidden;
}

.dept-perf-item .progress-bar {
    transition: width 0.5s ease-in-out;
}
.dept-perf-item {
    margin-bottom: 0.5rem;
}

.dept-name {
    font-weight: 600;
    color: #333;
}

.progress {
    background-color: #e9ecef;
}
	</style>
    <div class="min-height-200px">
        

      <div class="pd-ltr-20">
    <div class="row pb-10">
        {{-- Gender Statistics Card --}}
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <div class="d-flex flex-wrap">
                    <div class="widget-data w-100">
                        <div class="d-flex justify-content-between align-items-center border-bottom py-0 mb-0">
                            <div class="font-13 weight-500">
                                <i class="fa fa-users mr-2"></i>Total Head Count
                            </div>
                            <div class="text-right">
                                <span class="font-13 weight-600">{{ $totalEmployees }}</span>
                                <div class="font-12 weight-500">100%</div>
                            </div>
                        </div>
                        
                        @foreach($genderStats as $gender)
                            @php
                                $colorClass = strtolower(trim($gender->Gender)) == 'male' ? '#0275d8' : '#FF69B4';
                                $icon = strtolower(trim($gender->Gender)) == 'male' ? 'fa-male' : 'fa-female';
                                $percentage = ($gender->genderCount / $totalEmployees) * 100;
                            @endphp
                            
                            <div class="d-flex justify-content-between align-items-center border-bottom py-0">
                                <div class="font-13 weight-500" style="color: {{ $colorClass }};">
                                    <i class="fa {{ $icon }} mr-2"></i>{{ $gender->Gender }}
                                </div>
                                <div class="text-right">
                                    <span class="font-13 weight-600" style="color: {{ $colorClass }};">
                                        {{ $gender->genderCount }}
                                    </span>
                                    <div class="font-12 weight-500" style="color: {{ $colorClass }};">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Branch Statistics Card --}}
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <div class="d-flex flex-wrap">
                    <div class="widget-data w-100">
                        @if($branchStats->isNotEmpty())
                            @foreach($branchStats as $branch)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <span class="font-14 text-secondary weight-500">
                                        {{ $branch->branchname }}
                                    </span>
                                    <span class="font-14 text-secondary weight-500 ml-auto">
                                        {{ $branch->staffCount }} Agents
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <div class="d-flex justify-content-center align-items-center py-3">
                                <span class="font-14 text-secondary weight-500">No branch data found</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Empty Cards for Future Use --}}
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <div class="d-flex flex-wrap">
                    <div class="widget-data">
                        <div class="d-flex align-items-center">
                            <div class="weight-700 font-24 text-dark"></div>
                            <div class="font-14 text-secondary weight-500 ml-2" style="font-size: 14px;"></div>
                        </div>
                        <div class="d-flex align-items-center"></div> 
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <div class="d-flex justify-content-between align-items-center mb-3"></div>
                <div class="department-performance-horizontal"></div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row">
        {{-- Agents Earnings Chart --}}
        <div class="col-lg-4 col-md-6 mb-20">
            <div class="card-box height-100-p pd-20 min-height-200px">
                <div id="attendanceChartContainer"></div>
            </div>
        </div>

        {{-- Turnover Trends Chart --}}
        <div class="col-lg-4 col-md-6 mb-20">
            <figure class="highcharts-figure">
                <div id="container" style="width:100%; height:400px;"></div>
            </figure>
        </div>

        {{-- Net Pay Chart --}}
        <div class="col-lg-4 col-md-6 mb-20">
            <div class="card-box height-100-p pd-20 min-height-100px">
                <div class="d-flex justify-content-between"></div>
                <div class="user-list">
                    <div id="netpayChartContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-box mb-30"></div>
</div>
        
        
    </div> 
    <div id="chart-data" 
         data-turnover="{{ json_encode($turnoverData) }}"
         data-payments="{{ json_encode($paymentsData) }}"
         data-netpay="{{ json_encode($netpayData) }}">
    </div>
     <script src="{{ asset('js/dash.js') }}"></script>
    <script>
   
        </script>
    
</x-custom-admin-layout>