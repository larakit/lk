<?php
namespace Larakit\User;

use Larakit\Model\UserAuthToken;
use Larakit\Webconfig;

class LaravelMe {
    static function _($k) {
        if (\Auth::guest()) {
            return null;
        }
        $user = \Auth::getUser();

        return ($user && $user->{$k}) ? $user->{$k} : null;
    }

    static function id() {
        return static::_(__FUNCTION__);
    }

    static function username() {
        return static::_(__FUNCTION__);
    }

    static function gender() {
        return static::_(__FUNCTION__);
    }

    static function is_admin() {
        return static::_(__FUNCTION__);
    }

    static function is_translator() {
        return static::is_admin() || static::_(__FUNCTION__);
    }

    static function login() {
        return static::_(__FUNCTION__);
    }

    static function about() {
        return static::_(__FUNCTION__);
    }

    static function sign() {
        return static::_(__FUNCTION__);
    }

    static function birthday() {
        return static::_(__FUNCTION__);
    }

    static function password() {
        return static::_(__FUNCTION__);
    }

    static function email() {
        return static::_(__FUNCTION__);
    }

    static function code_email() {
        return static::_(__FUNCTION__);
    }

    static function code_phone() {
        return static::_(__FUNCTION__);
    }

    static function is_show_email() {
        return static::_(__FUNCTION__);
    }

    static function is_show_phone() {
        return static::_(__FUNCTION__);
    }

    static function phone() {
        return static::_(__FUNCTION__);
    }

    static function is_show_age() {
        return static::_(__FUNCTION__);
    }

    static function is_show_birthday() {
        return static::_(__FUNCTION__);
    }

    static function hasRole($role) {
        foreach (static::roles() as $r) {
            if (\Str::is($role, $r)) {
                return true;
            }
        }

        return false;
    }

    static function roles() {
        if (!\Auth::getUser()) {
            return [];
        }

        return (array)\Auth::getUser()->roles->lists('role');
    }

    static function authToken($url = '/') {
        if (!\Auth::getUser()) {
            return false;
        }
        if (true !== Webconfig::get('larakit:auth_token')) {
            return false;
        }
        $token = md5($url . microtime(true) . hashids_encode(Me::id()));
        UserAuthToken::create(
            [
                'user_id' => Me::id(),
                'url'     => $url,
                'token'   => $token
            ]
        );

        return $token;
    }

    static function getTokenLink($route, $params = []) {
        $params = [];
        $link   = route($route, $params);
        $token  = static::authToken($link);
        if ($token) {
            $params['auth_token'] = $token;

            return route($route, $params);
        }

        return false;
    }

}