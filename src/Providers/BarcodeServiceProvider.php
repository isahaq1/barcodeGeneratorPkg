<?php

namespace Isahaq\Barcode\Providers;

use Illuminate\Support\ServiceProvider;
use Isahaq\Barcode\Services\BarcodeService;

class BarcodeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('barcode', function ($app) {
            return new BarcodeService();
        });
    }
} 