<?php

namespace Larakit\Base;

use Larakit\ServiceProvider;

class LarakitServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot() {
        include __DIR__ . '/../../libs/formatters.php';
        include __DIR__ . '/../../libs/database.php';
        include __DIR__ . '/../../libs/debug.php';
        include __DIR__ . '/../../libs/hashids.php';
        include __DIR__ . '/../../routes.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->larapackage('larakit/laravel-larakit', 'larakit', 'public');
        $this->app->bind('larakit:env',
            function ($app) {
                return new \Larakit\Command\Env();
            });
        $this->app->bind('larakit:readiness',
            function ($app) {
                return new \Larakit\Command\Readiness();
            });
        $this->app->bind('larakit:app-provider',
            function ($app) {
                return new \Larakit\Command\AppProvider();
            });
        $this->app->bind('larakit:app-alias',
            function ($app) {
                return new \Larakit\Command\AppAlias();
            });
        $this->app->bind('larakit:app-db',
            function ($app) {
                return new \Larakit\Command\AppDb();
            });
        $this->app->bind('larakit:app-migrate',
            function ($app) {
                return new \Larakit\Command\AppMigrate();
            });
        $this->commands([
            'larakit:env',
            'larakit:app-provider',
            'larakit:app-alias',
            'larakit:app-db',
            'larakit:readiness',
            'larakit:app-migrate',
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

}
