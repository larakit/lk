<?php
namespace Larakit;

use Illuminate\Support\Arr;
use Larakit\Manager\ManagerPackage;
use Larakit\Manager\ManagerPackageMigration;
use Larakit\Page\PageTheme;
use Larakit\Page\Theme;

abstract class ServiceProvider extends \Illuminate\Support\ServiceProvider {


    function larapackage($package, $alias = null) {
        if (!$alias) {
            $alias = $package;
        }
//        Theme::set('bunkermedia');
        //если назначена кастомная тема оформления
        if ($theme = PageTheme::getCurrent()) {
            /// если переопределены шаблоны вьюх для указанной темы
            $theme_views_dir = base_path('vendor/' . $package . '/src/views/!/themes/' . $theme);
            if (file_exists($theme_views_dir)) {
                $this->loadViewsFrom($theme_views_dir, $alias);
            }
        }
        //базовые шаблоны пакета
        $view_dir = base_path('vendor/' . $package . '/src/views');
        if (file_exists($view_dir)) {
            $this->loadViewsFrom($view_dir, $alias);
        }


        //регистрируем миграции
        if (is_dir(base_path('vendor/' . $package . '/src/migrations'))) {
            ManagerPackageMigration::register($package);
        }
        $this->larakitRegisterLibs($package);
        $this->larakitRegisterCommands($alias, $alias);
        $this->larakitRegisterManagers($package);
        $this->larakitRegisterForm($package);
        $this->larakitRegisterWebconfig($package, $alias);
        $this->larakitRegisterRoutes($package);
        $this->larakitRegisterEvents($package);
        $this->larakitRegisterRouteFilters($package, $alias);
        ManagerPackage::register($package, $alias);
    }

    function larakitRegisterRouteFilters($package) {
        //автоматическая регистрация фильтров
        $filters = base_path('vendor/' . $package . '/src/boot/route_filters.php');
        if (file_exists($filters)) {
            include_once $filters;
        }
    }

    function larakitRegisterManagers($package) {
        //автоматическая регистрация фильтров
        $managers = rglob('*.php', 0, base_path('vendor/' . $package . '/src/boot/managers/'));
        foreach ($managers as $manager) {
            include_once $manager;
        }
    }

    function larakitRegisterForm($package) {
        //автоматическая регистрация элементов форм
        $laraform = base_path('vendor/' . $package . '/src/boot/laraform.php');
        if (file_exists($laraform)) {
            include_once $laraform;
        }
    }


    function larakitRegisterEvents($package) {
        //автоматическая регистрация Larakit-событий пакета
        $file = base_path('vendor/' . $package . '/src/boot/events_larakit.php');
        if (file_exists($file)) {
            include_once $file;
        }
        //автоматическая регистрация Laravel-событий пакета
        $file = base_path('vendor/' . $package . '/src/boot/events_laravel.php');
        if (file_exists($file)) {
            include_once $file;
        }
    }

    function larakitRegisterCommands($alias, $entity) {
        //автоматическая регистрация команд пакета
        $commands = \Config::get($alias . '::larakit/commands');
        if (count($commands)) {
            foreach ($commands as $command_name => $command_class) {
                $this->app->bind($command_name, function ($app) use ($command_class) {
                        return new $command_class;
                    });

            }
            $this->commands(array_keys($commands));
        }
    }

    function larakitRegisterRoutes($package) {
        //автоматическая регистрация роутов пакета
        $routes = rglob('*.php', 0, base_path('vendor/' . $package . '/src/boot/routes/'));
        foreach ($routes as $route) {
            include_once $route;
        }
    }

    function larakitRegisterLibs($package) {
        //автоматическая регистрация роутов пакета
        $libs = rglob('*.php', 0, base_path('vendor/' . $package . '/src/boot/libs/'));
        foreach ($libs as $lib) {
            include_once $lib;
        }
    }

    function larakitRegisterWebconfig($package, $entity) {
        //автоматическая регистрация элементов форм
        $webconfigs = rglob('*.php', 0, base_path('vendor/' . $package . '/src/boot/webconfig/'));
        foreach ($webconfigs as $webconfig) {
            include_once $webconfig;
        }
    }
}
