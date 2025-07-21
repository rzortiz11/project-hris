<?php

namespace App\Providers;

use App\Models\Export;
use Illuminate\Support\ServiceProvider;
use Filament\Actions\Exports\Models\Export as VendorExport;

class ExportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(VendorExport::class, function ($app) {
            return new Export();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
