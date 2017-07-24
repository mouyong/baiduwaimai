<?php

namespace App\Providers;

use Baidu\Baidu;
use Illuminate\Support\ServiceProvider;

class BaiduServiceProvider extends ServiceProvider
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
        $this->app->bind('baidu', function ($app, $source) {
            return new Baidu($source);
        });
    }
}
