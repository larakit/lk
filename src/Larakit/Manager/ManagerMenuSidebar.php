<?php

namespace Larakit\Manager;

use Illuminate\Support\Arr;

class ManagerMenuSidebar extends ManagerBase {

    /**
     * Регистрация компонента
     *
     * @param      $package
     * @param bool $dir
     */
    static function register($path, $section) {
        static::add($section, $path);
    }

    /**
     * Получить все типы зарегистрированных миграторов
     * @return array
     */
    static function apply() {
        foreach (static::get() as $package => $alias) {
            self::larakitRegisterComposers($package, $alias);
            self::larakitRegisterMenuNavbar($alias, $alias);
//            self::larakitRegisterMenuSidebar($alias, $alias);
            self::larakitRegisterMenuSubpages($alias, $alias);

            $entities = \Config::get($alias . '::larakit/entities', []);
            foreach ($entities as $entity => $data) {
                self::larakitRegisterComposers($package, $entity);
                self::larakitRegisterMenuNavbar($alias, $entity);
//                self::larakitRegisterMenuSidebar($alias, $entity);
                self::larakitRegisterMenuSubpages($alias, $entity);
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

    static function larakitRegisterMenuNavbar($alias, $entity) {
        //автоматическая регистрация пунктов верхнего меню NavBar
        $menus_navbar = (array)\Config::get($alias . '::models/' . $entity . '/menus_navbar');
        if (count($menus_navbar)) {
            foreach ($menus_navbar as $name => $items) {
                $manager = \Larakit\Widget\WidgetNavBar::factory($name);
                foreach ($items as $as => $key) {
                    $params   = [];
                    $params[] = $as;
                    if (is_string($key)) {
                        $params[] = $key;
                    } else {
                        $params[] = Arr::get($key, 'key');
                        $params[] = Arr::get($key, 'attributes');
                    }
                    call_user_func_array([
                            $manager,
                            'addItem'
                        ],
                        $params);
                }
            }
        }

    }

    static function larakitRegisterMenuSidebar($alias, $entity) {
        //автоматическая регистрация пунктов бокового меню SideBar
        $menus_sidebar = (array)\Config::get($alias . '::models/' . $entity . '/menus_sidebar');
        if (count($menus_sidebar)) {
            foreach ($menus_sidebar as $name => $sections) {
                $manager = \Larakit\Widget\WidgetSideBar::factory($name);
                foreach ($sections as $section => $section_data) {
                    $manager->setSection($section);
                    $items = (array)Arr::get($section_data, 'items');
                    foreach ($items as $as => $menu_key) {
                        //                        dump($name . '|' . $as . '|' . $menu_key);
                        //                        dump(Route::_('larakit_generator::admin.generator'));
                        //                        dd(\URL::route('larakit_generator::admin.generator'));
                        $manager->addItem($as, $menu_key);
                    }
                    $groups = (array)Arr::get($section_data, 'groups');
                    foreach ($groups as $k => $group) {
                        $title      = Arr::get($group, 'title', $k);
                        $icon       = Arr::get($group, 'icon', 'fa fa-gear');
                        $attributes = Arr::get($group, 'attributes', []);
                        $manager->addItemGroup($k, $title, $icon, $attributes);
                    }
                }
            }
        }

    }

    static function larakitRegisterMenuSubpages($alias, $entity) {
        //автоматическая регистрация дочерних страниц Subpages
        $menus_subpages = (array)\Config::get($alias . '::models/' . $entity . '/menus_subpages');
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