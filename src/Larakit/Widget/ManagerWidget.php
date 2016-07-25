<?php
/**
 * Created by Larakit.
 * Link: http://github.com/larakit
 * User: Alexey Berdnikov
 * Date: 24.05.16
 * Time: 16:45
 */

namespace Larakit\Widget;

use Larakit\Boot;
use Larakit\Twig;

class ManagerWidget {

    static $widgets = [];

    static function register($class, $view_path) {
        if(!is_a($class, Widget::class, true)) {
            throw new \Exception('Не является наследником класса ' . Widget::class);
        }
        $widget_name = $class::widget_name();
        if($view_path) {
            Boot::register_view_path($view_path, $widget_name);
        }
        Twig::register_function($widget_name, function ($instance = null) use ($class) {
            return $class::instance($instance);
        });
    }

}