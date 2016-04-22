<?php
/**
 * Created by PhpStorm.
 * User: groove
 * Date: 07.03.15
 * Time: 8:41
 */

namespace Larakit\Widget;


use Illuminate\Support\Arr;
use Larakit\Base\Map;
use Larakit\Base\Widget;
use Larakit\Manager\ManagerMenuSidebar;
use Larakit\Route\Route;

class WidgetSideBar extends Widget {
    /**
     * @var array
     */
    protected $maps;
    protected $name;
    protected $section = 'Larakit';

    function __construct($name) {
        $this->name = $name;
    }

    function addItemGroup($key, $title, $icon, $attrs = []) {
        return $this->addItem(
            [
                'title' => $title,
                'icon'  => $icon,
            ],
            $key,
            $attrs
        );
    }

    /**
     * @param       $access_name
     * @param       $text
     * @param       $url
     * @param array $attrs
     *
     * @return $this
     */
    function addItem($as, $key, $route_parameters = []) {
        if (is_string($as)) {
            if (!Route::isEnable($as)) {
                return $this;
            }
            if (Route::checkRouteFilters($as)) {
                return $this;
            }
            if (!count(Route::get($as))) {
                return $this;
            }
        }


        if (is_array($as)) {
            $text = Arr::get(
                $as,
                'title'
            );
            $icon = Arr::get(
                $as,
                'icon'
            );
            $url  = Arr::get(
                $as,
                'url',
                '#'
            );
            $as   = Arr::get(
                $as,
                'as'
            );
        } else {
            $keys = array_keys($route_parameters);
            $keys = array_map(
                function ($item) {
                    return '{' . $item . '}';
                },
                $keys
            );

            $text = str_replace($keys, array_values($route_parameters), Route::get_title($as));
            $icon = Route::get_icon($as);
            $url  = \URL::route($as, $route_parameters);
        }
        if ($icon) {
            $text = \HtmlI::setTitle($text)->addClass($icon) . ' &nbsp;' . \HtmlSpan::setContent($text);
        }
        if (!isset($this->maps[$this->section])) {
            $this->maps[$this->section] = Map::instance(get_called_class() . $this->name . $this->section);
        }
        if ($route_parameters) {
            $key .= '.'.str_replace(['&', '='], ['.', '-'], http_build_query($route_parameters));
        }
        $this->maps[$this->section]->add(
            $key,
            $as,
            $text,
            $url
        );

        return $this;
    }

    function setSection($section) {
        $this->section = $section;

        return $this;
    }

    function init() {
        $configs = ManagerMenuSidebar::get($this->name);

        $items = [];
        foreach ($configs as $config) {
            $items = array_merge_recursive(\Config::get($config), $items);
        }
        if (count($items)) {
            foreach ($items as $section => $section_data) {
                $this->setSection($section);
                $items = (array)Arr::get($section_data, 'items');
                foreach ($items as $as => $menu_key) {
                    //                        dump($name . '|' . $as . '|' . $menu_key);
                    //                        dump(Route::_('larakit_generator::admin.generator'));
                    //                        dd(\URL::route('larakit_generator::admin.generator'));
                    $this->addItem($as, $menu_key);
                }
                $groups = (array)Arr::get($section_data, 'groups');
                foreach ($groups as $k => $group) {
                    $title      = Arr::get($group, 'title', $k);
                    $icon       = Arr::get($group, 'icon', 'fa fa-gear');
                    $attributes = Arr::get($group, 'attributes', []);
                    $this->addItemGroup($k, $title, $icon, $attributes);
                }
            }
        }
    }

    function __toString() {
        $this->init();
        try {
            $sections            = [];
            $current_access_name = null;
            if (is_array($this->maps) && count($this->maps)) {
                foreach ($this->maps as $section => $map) {
                    $sections[$section] = $map->getItems();
                    $c                  = $map->getCurrent();
                    if ($c) {
                        $current_access_name = $c;
                    }
                }
            }
            $this->values['sections']            = $sections;
            $this->values['current_access_name'] = $current_access_name;

            return parent::__toString();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function tpl() {
        return 'larakit::!.widgets.side_bar';
    }


}