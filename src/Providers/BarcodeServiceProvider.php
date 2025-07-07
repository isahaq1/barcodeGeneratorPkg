<?php

namespace Isahaq\Barcode\Providers;

use Illuminate\Support\ServiceProvider;
use Isahaq\Barcode\Services\BarcodeService;

/**
 * Laravel Service Provider for Isahaq\Barcode
 */
class BarcodeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('barcode', function ($app) {
            return new BarcodeService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['barcode'];
    }
} 