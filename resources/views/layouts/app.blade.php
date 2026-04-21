<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Core Pay') }}</title>
        

        <!-- Fonts -->
        
       
        @vite('resources/src/plugins/jquery-steps/jquery.steps.css')
   
    
        <script src="https://unpkg.com/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
        
        <link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/core.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/style.css') }}">



       
        
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

           

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <script src="{{ asset('vendors/scripts/jquery.min.js') }}"></script>
<script src="{{ asset('vendors/scripts/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendors/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendors/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendors/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('vendors/datatables/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>


    <div id="sessionWarning"
     class="hidden fixed bottom-0 inset-x-0 z-50 bg-amber-50 border-t-2 border-amber-400
            flex items-center justify-between px-6 py-3 shadow-lg"
     role="alert"
     aria-live="assertive">
    <span id="sessionWarningText" class="text-sm text-amber-800 font-medium"></span>
    <button id="sessionExtendBtn"
            type="button"
            class="ml-6 shrink-0 rounded-md bg-amber-500 px-4 py-1.5 text-sm font-semibold
                   text-white hover:bg-amber-600 focus:outline-none focus:ring-2
                   focus:ring-amber-400 focus:ring-offset-1 transition">
        Stay signed in
    </button>
</div>
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
];
@endphp

<div id="appConfig" data-routes='@json($routes)'></div>

@vite(['resources/css/app.scss', 'resources/js/app.js'])
    </body>
 


</html>
