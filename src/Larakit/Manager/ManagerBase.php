<?php

namespace Larakit\Manager;

use Illuminate\Support\Arr;

class ManagerBase {
    static protected $items = [];

    static function key() {
        $v = explode('\\', get_called_class());
        return end($v);
    }

    static function set($v, $k = null) {
        static::$items[static::key()][$k] = $v;
    }

    static function add($k, $v) {
        static::$items[static::key()][$k][] = $v;
    }

    static function get($key = null, $default = []) {
        $k = static::key();
        if ($key) {
            $k .= '.' . $key;
        }
        return Arr::get(static::$items, $k, $default);
    }

}