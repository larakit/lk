<?php
namespace Larakit\Helper;

class HelperUrl {
    static function is_local($url) {
        return ((false === mb_strpos($url, ':')) || ('//' == mb_substr($url, 0, 2)));
    }

    static function prepare($url) {
        if (self::is_local($url)) {
            $url = '/' . trim($url, '/');
        }
        return $url;
    }
}