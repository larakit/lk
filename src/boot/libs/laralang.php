<?php
if (!function_exists('laralang')) {
    function laralang($key, $replace = [], $locale = null) {
        $ret = Lang::get($key, $replace, $locale);
        return ($ret != $key) ? $ret : (\Config::get('app.debug') ? $key : '');
    }
}
\Larakit\Twig::register_function('laralang',
    function ($key, $replace = [], $locale = null) {
        return Lang::get($key, $replace, $locale);
    });