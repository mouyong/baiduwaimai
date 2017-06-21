<?php

namespace App\Providers;

use App\Redis\Rcache;
use Illuminate\Support\ServiceProvider;

class RcacheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Rcache', function () {
            return Rcache::getInstance();
        });
    }
}
