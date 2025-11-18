<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Http\View\Composers\PayrollComposer;
use App\Http\View\Composers\MenuComposer;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Log::info('ViewServiceProvider: register() called');
    }

    public function boot(): void
    {
        Log::info('ViewServiceProvider: boot() called');
        
        // Share payroll data
        View::composer([
            'components.navbar',
            'layouts.app',
        ], PayrollComposer::class);

        // Share menu data with sidebar
        View::composer([
            'components.left-sidebar', // ✅ Check this matches your blade file name exactly
            'layouts.app',
        ], MenuComposer::class);
        
        Log::info('ViewServiceProvider: Composers registered');
    }
}