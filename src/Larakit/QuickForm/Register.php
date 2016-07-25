<?php
/**
 * Created by PhpStorm.
 * User: berdnikov_ay
 * Date: 10.07.2015
 * Time: 13:32
 */

namespace Larakit\QuickForm;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Register {

    static $elements   = [];
    static $containers = [
        'HTML_QuickForm2_Container',
        'HTML_QuickForm2',
    ];

    static function callback($name) {
        return [
            self::getClass($name),
            'laraform',
        ];
    }

    static protected function get($type, $prop) {
        $prop = Str::snake(mb_substr($prop, 3));

        return Arr::get(self::$elements, $type . '.' . $prop);
    }

    static function getClass($type) {
        return self::get($type, __FUNCTION__);
    }

    static function getPackage($type) {
        return self::get($type, __FUNCTION__);
    }

    static function getNamespace($type) {
        return self::get($type, __FUNCTION__);
    }

    static function container($class) {
        self::$containers[] = $class;
    }

    static function register($name, $namespace, $view_dir) {
        $class = '\Larakit\QuickForm\Element' . Str::studly($name);
        $cont = '\HTML_QuickForm2_Container';
        if(trim($class, '\\') instanceof $cont) {
            self::$containers[] = $class;
        }
        $path = 'QuickForm/Element' . Str::studly($name) . '.php';
        \HTML_QuickForm2_Factory::registerElement(
            $name, $class, $path
        );
        if((is_subclass_of($class, '\HTML_QuickForm2_Container'))) {
            self::container($class);
        }
        self::$elements[$name] = compact('class', 'namespace', 'view_dir');
    }

} 