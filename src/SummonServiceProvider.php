<?php

namespace Quezler\Laravel_Summon;

use Illuminate\Support\ServiceProvider;

class SummonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->commands([
                Console\SummonConsole::class,
            ]);

        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
