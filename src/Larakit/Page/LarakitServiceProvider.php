<?php
namespace Larakit\Page;



use Larakit\ServiceProvider;

class LarakitServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot() {
        $this->larapackage('larakit/lk-page', 'lk-page');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
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