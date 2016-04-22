<?php
namespace Larakit;

use Larakit\Command\AppDb;
use Larakit\Command\Backup;
use Larakit\Command\Readiness;

class LarakitServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot() {
        $this->larapackage('larakit/lk', 'larakit');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->commands([
            AppDb::class,
            Readiness::class,
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