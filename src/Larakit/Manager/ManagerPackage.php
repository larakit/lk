<?php

namespace Larakit\Manager;

use Illuminate\Support\Arr;
use Larakit\Helper\HelperFile;

class ManagerPackage extends ManagerBase {

    /**
     * Регистрация компонента
     *
     * @param      $package
     * @param bool $dir
     */
    static function register($package, $alias) {
        static::set($alias, $package);
    }

    /**
     * Получить все типы зарегистрированных миграторов
     * @return array
     */
    static function apply() {
        foreach (static::get() as $package => $alias) {
            self::larakitRegisterComposers($package, $alias);
            self::larakitRegisterMenuSidebar($package, $alias, $alias);
            self::larakitRegisterMenuSubpages($package, $alias);
            $entities = \Config::get($alias . '::larakit/entities', []);
            foreach ($entities as $entity => $data) {
                self::larakitRegisterComposers($package, $entity);
                self::larakitRegisterMenuSidebar($alias, $alias, $entity);
                self::larakitRegisterMenuSubpages($package, $alias);
            }
        }
    }

    static function larakitRegisterComposers($package, $entity) {
        //автоматическая регистрация роутов пакета
        $routes = base_path('vendor/' . $package . '/src/boot/composers/' . $entity . '.php');
        if (file_exists($routes)) {
            include_once $routes;
        }
    }


    static function larakitRegisterMenuSidebar($package, $alias, $entity) {
        //автоматическая регистрация пунктов бокового меню SideBar
        foreach (ManagerSection::get() as $code => $name) {
            $dir = base_path('vendor/' . $package . '/src/config/larakit/sidebar/' . $code);
            $dir = HelperFile::normalizeFilePath($dir);
            if (file_exists($dir)) {
                $dirs = rglob('*.php', 0, $dir);
                foreach ($dirs as $d) {
                    $d = str_replace($dir, '', $d);;
                    $d = str_replace('.php', '', $d);;
                    $d = trim($d, '/');
                    ManagerMenuSidebar::register($alias . '::larakit/sidebar/' . $code . '/' . $d, $code);
                }
            }
        }
    }

    static function larakitRegisterMenuSubpages($package, $alias) {
        //автоматическая регистрация дочерних страниц Subpages
        $dir = base_path('vendor/' . $package . '/src/config/larakit/subpages/');
        $dir = HelperFile::normalizeFilePath($dir);
        if (file_exists($dir)) {
            $dirs = rglob('*.php', 0, $dir);
            foreach ($dirs as $d) {
                $d = str_replace($dir, '', $d);;
                $d = str_replace('.php', '', $d);;
                $d              = trim($d, '/');
                $menus_subpages = (array)\Config::get($alias . '::larakit/subpages/' . $d);
                if (count($menus_subpages)) {
                    foreach ($menus_subpages as $page => $items) {
                        $manager = \Larakit\Widget\WidgetSubpages::factory($page);
                        foreach ($items as $as => $props) {
                            $style      = Arr::get($props, 'style', 'bg-aqua');
                            $is_curtain = Arr::get($props, 'is_curtain', false);
                            $manager->add($as, $style, $is_curtain);
                        }
                    }
                }
            }
        }
    }

}