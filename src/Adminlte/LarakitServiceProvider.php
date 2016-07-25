<?php namespace Adminlte;

class LarakitServiceProvider extends \Larakit\ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot() {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        include __DIR__.'/../routes.php';
        $config = __DIR__ . '/../config/config.php';
        $this->mergeConfigFrom($config, 'larakit.lk-adminlte');
        $this->publishes([
            $config => config_path('larakit/lk-adminlte/config.php'),
        ]);
        $this->loadViewsFrom(__DIR__.'/../views', 'lk-adminlte');
        if(\Request::is('admin*')){
            \LaraPage::body()->addClass(config('larakit.lk-adminlte.body_class'));
        }
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
