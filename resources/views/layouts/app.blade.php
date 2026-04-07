<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Core Pay') }}</title>
        

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        @vite('resources/src/plugins/jquery-steps/jquery.steps.css')
    @vite('resources/src/plugins/datatables/css/dataTables.bootstrap4.min.css')
    @vite('resources/src/plugins/datatables/css/responsive.bootstrap4.min.css')
    @vite('resources/css/style.css')
        <script src="https://unpkg.com/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
        <script type="module" src="{{ mix('js/app.js') }}"></script>
        <link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/core.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/style.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/datatables/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/datatables/css/responsive.bootstrap4.min.css') }}">


        <!-- Scripts -->
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
        
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
<script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/11.4.1/modules/csp.js"></script>

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
    </body>
    <script nonce="{{ $cspNonce }}">

window.routes = {
            fullStaffReport: '{{ route("reports.full-staff") }}',
            // Add other routes as needed
        };
</script>

</html>
