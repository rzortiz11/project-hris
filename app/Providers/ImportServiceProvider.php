<?php

namespace App\Providers;

use App\Models\Import;
use Illuminate\Support\ServiceProvider;
use Filament\Actions\Imports\Models\Import as VendorImport;

class ImportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(VendorImport::class, function ($app) {
            return new Import();
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
