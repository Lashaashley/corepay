<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Corepay') }}</title>
    
    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
   
    
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/core.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Select2 CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/select2/dist/css/select2.min.css') }}">
      
    
     @vite(['resources/css/app.scss', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        @include('layouts.partials.header')
        @include('layouts.partials.left-sidebar')
        
        <div class="main-container">
            @include('layouts.partials.navbar')
            
            <div class="pd-ltr-20">
                {{ $slot }}
            </div>
            
            @include('layouts.partials.right-sidebar')
        </div>
    </div>
    
    <!-- SCRIPTS - Properly ordered -->
    <!-- jQuery first (only include once) -->
    <script src="{{ asset('src/scripts/jquery.min.js') }}"></script>
    
    <!-- Core scripts -->
    <script src="{{ asset('vendors/scripts/core.js') }}"></script>
    <script src="{{ asset('vendors/scripts/process.js') }}"></script>
    <script src="{{ asset('vendors/scripts/layout-settings.js') }}"></script>
    
    <!-- Bootstrap -->
    <script src="{{ asset('src/plugins/bootstrap/bootstrap.min.js') }}"></script>
    
    <!-- Select2 -->
    <script src="{{ asset('src/plugins/select2/dist/js/select2.full.min.js') }}"></script>
    
    
    
    <!-- DataTables -->
    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>
    
    <!-- SweetAlert -->
    <script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
    
    @php
$routes = [
    "amanage"     => route("agents.data"),
    "branches"    => route("branches.getDropdown"),
    "depts"       => route("depts.getDropdown"),
    "getbanks"    => route("banks.getDropdown"),
    "getbranches" => route("brbranches.getDropdown"),
    "getuser"     => route("get.agent", ["id" => "__id__"]),
    "getptypes"   => route("paytypes.getDropdown"),
    "getbybank"   => route("branches.getByBank"),
    "codebybank"  => route("codes.getByBank"),
    "analcompperiod"  => route("analytics.compare.periods"),
    "analdaterange"  => route("analytics.date.range"),
    "analdash"  => route("analytics.dashboard.data"),
    "getbycamp"  => route("classes.getByCampus"),
    "getbycamp"  => route("classes.getByCampus"),
    "allstaffreport"  => route("reports.full-staff"),
    "autocalc"  => route("autocalc.process"),
     "getwuth"  => route("payroll.deductions.data"),
     "tstatus"  => route("toggle.status"),
     "getcodes"  => route("mngprol.getcodes"),
     "staffsearch"  => route("payroll.staff.search"),
     "staffdet"  => route("staff.search.details"),
     "fetchitems"  => route("fetch.items"),
     "paysubmit"  => route("payroll.submit"),
     "payslipProgress" => route("bulk.payslips.progress", ["jobId" => "__id__"]),
     "downloadzip" => route("bulk.payslips.download.zip", ["jobId" => "__id__"]),
     "downloadindivi" => route("bulk.payslips.list", ["jobId" => "__id__"]),
     "summarydata"  => route("summary.data"),
     "login"  => route("login"),
     "periodclose"  => route("period.close"),
     "bulkgenerate"  => route("bulk.payslips.generate"),
     "modulesass"  => route("modules.assign"),
     "newuser"  => route("newuser.store"),
     
     "manageusers"     => route("musers.data"),
    "getpayroll"  => route("getPayroll.types"),
    "getuserman"     => route("get.user", ["id" => "__id__"]),
    "updateuser"  => route("update.user", ["id" => "__id__"]),
    "app_url"     => url('/'),
    "storage_url" => asset('storage'),
    "uploads_url" => asset('uploads'),
];
@endphp

<div id="appConfig" data-routes='@json($routes)'></div>
    
</body>
</html>