<?php

namespace Dialect\Gdpr;

use Dialect\Gdpr\Commands\AnonymizeInactiveUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class GdprServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerCommands();
        // Load standard issue migrations
        $timestamp = date('Y_m_d_His');
        $this->publishes([
            __DIR__.'/migrations/add_gdpr_to_users_table.php' => database_path('migrations/'.$timestamp.'_add_gdpr_to_users_table.php'),
            __DIR__.'/views/message.blade.php' => base_path('resources/views/gdpr/message.blade.php'),
            __DIR__.'/middleware/RedirectIfUnansweredTerms.php' => base_path('app/Http/Middleware/RedirectIfUnansweredTerms.php'),
            __DIR__.'/Http/Controllers/GdprController.php' => base_path('app/Http/Controllers/GdprController.php'),
        ], 'gdpr-consent');

        // add scheduled job without overriding any other scheduled jobs
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command(AnonymizeInactiveUsers::class)->daily();
        });
    }

    /**
     * Register the GDPR routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => config('gdpr.uri'),
            'namespace' => app()->getNamespace().'Http\Controllers',
            'middleware' => config('gdpr.middleware'),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        });
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AnonymizeInactiveUsers::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
        $this->offerPublishing();
    }

    /**
     * Setup the configuration for GDPR.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(__DIR__.'/config/gdpr.php', 'gdpr');
    }

    /**
     * Setup the resource publishing groups for GDPR.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/gdpr.php' => config_path('gdpr.php'),
            ], 'gdpr-config');
        }
    }
}
