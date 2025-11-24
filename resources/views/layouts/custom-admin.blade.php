<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'School Asist') }}</title>
    
    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/core.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Select2 CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/select2/dist/css/select2.min.css') }}">
    
    
     @vite(['resources/css/app.scss', 'resources/css/icon-font.min.css', 'resources/css/style.css'])
    
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
    
    <!-- Your custom scripts (should be last) -->
    <script src="{{ asset('vendors/scripts/script.min.js') }}"></script>
    
    <!-- Script to verify libraries are loaded -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('jQuery loaded:', typeof jQuery !== 'undefined');
        console.log('jQuery version:', jQuery.fn.jquery);
        console.log('DataTables loaded:', typeof jQuery.fn.DataTable !== 'undefined');
        console.log('Select2 loaded:', typeof jQuery.fn.select2 !== 'undefined');
        
    });
    </script>
</body>
</html>