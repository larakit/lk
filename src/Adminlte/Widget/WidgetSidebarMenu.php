<?php
namespace Adminlte\Widget;

use Adminlte\SidebarMenuGroup;
use Illuminate\Support\Arr;
use Larakit\Widget\Widget;

/**
 * Class WidgetSidebarMenu
 * @static  WidgetSidebarMenu instance($name = 'default')
 * @package Adminlte\Widget
 */
class WidgetSidebarMenu extends Widget {

    static protected $groups          = [];
    static protected $groups_priority = [];

    /**
     * @param      $name
     * @param null $priority
     *
     * @return SidebarMenuGroup
     */
    static function group($name, $priority = null) {
        if(!is_null($priority)) {
            static::$groups_priority[$name] = $priority;
        }
        if(!isset(static::$groups[$name])) {
            static::$groups[$name] = new SidebarMenuGroup();
        }

        return static::$groups[$name];
    }

    static function toArray() {
        $ret = [];
        foreach(static::$groups as $name => $group) {
            if(!isset(static::$groups_priority[$name])) {
                static::$groups_priority[$name] = 0;
            }
        }
        static::$groups_priority = Arr::sort(static::$groups_priority, function($v){
            return -$v;
        });
        foreach(static::$groups_priority as $name => $p) {
            $group      = Arr::get(static::$groups, $name);
            $ret[$name] = $group->toArray();
        }

        return $ret;
    }
    
    function toHtml() {
        return \View::make($this->tpl(), [
            'tree' => static::toArray(),
        ])->__toString();
    }

}