<?php
namespace Adminlte;

use Illuminate\Support\Arr;
use Larakit\Route\Route;

class SidebarMenuGroup {

    protected $items   = [];
    protected $childs = [];
    protected $tree = [];

    function branch($path = '') {
        $priorities = (array) Arr::get($this->childs, $path);
        if(count($priorities)) {
            krsort($priorities);
            foreach($priorities as $priority => $elements) {
                foreach($elements as $element) {
                    $element_path = str_replace('.', '._items_.', $element);
                    $e            = Arr::get($this->items, $element);
                    $a = Arr::get($this->tree, $element_path , []);
                    Arr::set($this->tree, $element_path , array_merge($a,$e));
                    $this->branch($element);
                }
            }
        }
        return $this->tree;
    }

    function toArray(){
        $this->branch('');
        return $this->tree;
    }

    function addItem($code, $title, $route, $priority = 0, $badge_text=null, $badge_class='bg-green') {
        $url = route($route, [], false);
        //запишем информаци о ноде
        $this->items[$code] = compact('route','title', 'url', 'is_active', 'badge_text', 'badge_class');
        //получим родительскую ноду
        $a = explode('.', $code);
        array_pop($a);
        $parent_path                                = trim(implode('.', (array) $a), '.');
        $this->childs[$parent_path][$priority][] = $code;
        return $this;
    }

}