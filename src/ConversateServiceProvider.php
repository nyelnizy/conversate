<?php

namespace Amot\Conversate;

use Illuminate\Support\ServiceProvider;

class ConversateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/conversate.php', 'conversate');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/conversate.php' => config_path('conversate.php'),
            ], 'conversate');
            $this->publishes([
                __DIR__ . '/../src/routes/actions.php' => app_path('routes/actions.php')
            ], 'conversate');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register facade
        $this->app->singleton('conversate', function () {
            return new Conversate;
        });
    }
}
