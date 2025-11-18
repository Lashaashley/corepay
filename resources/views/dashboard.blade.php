<x-custom-admin-layout>
    <div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Dashboard</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Your dashboard content -->
        <div class="row">
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <!-- Dashboard cards or widgets -->
            </div>
        </div>
    </div>
    
</x-custom-admin-layout>