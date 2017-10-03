<?php

namespace Magnetar\Tariffs;

use Illuminate\Support\ServiceProvider;

class TariffsServiceProvider extends ServiceProvider
{
    protected $commands = [
        'Magnetar\Tariffs\Commands\TariffExpired',
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if(config('tariffs.controllers.enabled') == true)
            include __DIR__.'/routes.php';

        $this->publishes([
            __DIR__.'/config/tariffs.php' => config_path('tariffs.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/migrations' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Magnetar\Tariffs\Controllers\ObjectCrudController');
        $this->app->make('Magnetar\Tariffs\Controllers\ModuleCrudController');
        $this->app->make('Magnetar\Tariffs\Controllers\CardCrudController');
        $this->app->make('Magnetar\Tariffs\Controllers\ObjectTypeCrudController');
        $this->app->make('Magnetar\Tariffs\Controllers\PaymentController');
        $this->commands($this->commands);

        $this->loadViewsFrom(__DIR__.'/views', 'magnetar_tariffs');
    }
}
