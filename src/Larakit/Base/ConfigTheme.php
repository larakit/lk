<?php
namespace Larakit\Base;

use Larakit\Webconfig;

class ConfigTheme {
    static function get($key, $default = null) {
        $theme = Webconfig::get('app.theme');
        if (mb_strpos($key, '::')) {
            $theme_key = str_replace('::', '::!/' . $theme, $key);
        } else {
            $theme_key = '!/' . $theme . '/' . $key;
        }
        return \Config::get($theme_key, \Config::get($key, $default));
    }
}