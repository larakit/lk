<?php
namespace Larakit\Widget;

use Illuminate\Support\Arr;
use Larakit\Route\Route;

class WidgetSubpages extends \Larakit\Base\Widget {
    protected $children = [];

    static function factory($name = null) {
        if (!$name) {
            $name = \Route::currentRouteName();
        }
        return parent::factory($name);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function add($as, $style = 'bg-aqua', $is_curtain = false, $params = []) {
        //        debug_print_backtrace();
        //        dd(1);
        if (!Route::isEnable($as)) {
            return $this;
        }

        if(is_string($as) && !count(Route::get($as))){
//            dd($as, Route::get($as));
            return $this;
        }

        $this->children[$as] = [
            'class'   => $style,
            'curtain' => $is_curtain,
            'params'  => $params,
        ];
        return $this;
    }

    function getChilds() {
        return $this->children;
    }

    function tpl() {
        return 'larakit::!.widgets.subpages';
    }


    function toHtml() {
        $children = array_merge($this->children,
            WidgetSubpages::factory('*')->getChilds());
        return \View::make($this->tpl(),
            ['children' => $children])->__toString();

    }

}