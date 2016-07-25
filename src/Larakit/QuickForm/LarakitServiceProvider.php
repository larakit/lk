<?php
namespace Larakit\QuickForm;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Larakit\QuickForm\Command\Ide;

class LarakitServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot() {
        $this->loadViewsFrom(__DIR__.'/../views/', 'quickform');
        $this->loadTranslationsFrom(__DIR__.'/../lang/', 'quickform');
        foreach(Register::$elements as $el){
            $namespace = Arr::get($el, 'namespace');
            $view_dir = Arr::get($el, 'view_dir');
            $this->loadViewsFrom($view_dir, $namespace);
            $this->publishes([
                $view_dir => resource_path('views/vendor/'.$namespace),
            ], 'quickform');
        }
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