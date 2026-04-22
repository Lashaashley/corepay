<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Corepay') }}</title>
    
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- CSS only in head -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/core.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/style.css') }}">

   
    <link rel="stylesheet" href="{{ asset('src/plugins/select2/dist/css/select2.min.css') }}">
    
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

    <!-- SCRIPTS at bottom of body, in dependency order -->

    <!-- ❌ REMOVED: jquery.min.js — Vite bundle already provides jQuery on window -->

    <!-- Core scripts (depend on jQuery from Vite bundle above) -->
    <script src="{{ asset('vendors/scripts/core.js') }}"></script>
    <script src="{{ asset('vendors/scripts/process.js') }}"></script>
    <script src="{{ asset('vendors/scripts/layout-settings.js') }}"></script>

   

    <!-- ✅ Select2 HERE — after Vite bundle has set window.jQuery -->
    <script src="{{ asset('src/plugins/select2/dist/js/select2.full.min.js') }}"></script>

   

    
    
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
    "netnofityapp"  => route("netpay.notify.approver"),
    "payapprove"  => route("payroll.approve"),
    "netapprove"  => route("netpay.approve"),
    "netreject"  => route("netpay.reject"),
    "netreports"  => route("reports.netpay"),
    "netreportsexcel"  => route("reports.netpay.excel"),
    "earnreports"  => route("reports.earnings"),
    "earnreportsexcel"  => route("reports.earnings.excel"),
    "payimport"  => route("deductions.import.process"),
    "payimport"  => route("deductions.import.process"),
    "pitemsupdate"  => route("pitems.update"),
    "pitemsupdatepriorities"  => route("payroll.deductions.update-priorities"),
    "loadpriori"  => route("payroll.deductions.priorities"),
    "searchagent"  => route("preports.search"),
    "overalsumm"  => route("reports.overall-summary"),
    "paysummary"  => route("reports.payroll-summary"),
    "excelsummary"  => route("payroll.summary.excel"),
    "bankadvice"  => route("reports.bank-advice"),
    "reportpayitems"  => route("reports.payroll-items"),
    "variancereport"  => route("reports.variance"),
    "payrolvariance"  => route("reports.payroll-variance"),
    "eftreport"  => route("generate.eft.report"),
    "rtgsreport"  => route("generate.rtgs.report"),
    "iftreport"  => route("generate.ift.report"),
    "genpayslip"  => route("payslip.generate"),
    "regapprovrej"     => route("registration.approvals.reject", ["id" => "__id__"]),
    "showfields"     => route("registration.approvals.show", ["id" => "__id__"]),
    "regapprove"     => route("registration.approvals.approve", ["id" => "__id__"]),
    "ritemsupdate"  => route("ritems.update"),
    "getwith"  => route("ritems.getwithholding"),
    "wthgroups"  => route("whgroups.store"),
    "wthgroupsdelete"  => route("whgroups.delete"),
    "ritemsgetcode"  => route("ritems.getcodes"),
    "rolesdrop"  => route("roles.getDropdown"),
    "rolesgetall"  => route("roles.getall"),
    "savemodules"  => route("modules.save"),
    "getrmodule"  => route("modules.getRoleModules"),
    "getallbranches"  => route("branches.getall"),
    "getallstaticinfo"  => route("staticinfo.getall"),
    "paytypesgetall" => route("paytypes.getall"),
    "branchesgetall" => route("branches.getall"),
    "deptsgetall" => route("depts.getall"),
    "banksgetall" => route("banks.getall"),
    "econfiggetall" => route("econfig.getall"),
    "compbgetall" => route("compb.getall"),
    "auditexcel" => route("audit.exportExcel"),
    "auditviewPdf" => route("audit.viewPdf"),
    "auditexportPdf" => route("audit.exportPdf"),
    "auditgetData" => route("audit.getData"),
    "static"  => url("static", ["id" => "__id__"]),
    "econfig"  => url("econfig", ["id" => "__id__"]),
    "pmodes"  => url("pmodes", ["id" => "__id__"]),
    "deptsup"  => url("depts", ["id" => "__id__"]),
    "branchesup"  => url("branches", ["id" => "__id__"]),
    "banksup"  => url("banks", ["id" => "__id__"]),
    "compbup"  => url("compb", ["id" => "__id__"]),
];
@endphp

<div id="appConfig" data-routes='@json($routes)'></div>
    
</body>
</html>