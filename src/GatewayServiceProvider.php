<?php

namespace Ateros\Pay;

use Illuminate\Support\ServiceProvider;

class GatewayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register()
    {
        $this->app->make('Ateros\Pay\Gateway');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/pay.php' => config_path('pay.php'),
        ], 'ateros-pay');
    }
}
