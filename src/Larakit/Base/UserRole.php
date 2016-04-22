<?php
/**
 * Created by PhpStorm.
 * User: berdnikov_ay
 * Date: 16.10.2015
 * Time: 9:43
 */

namespace Larakit\Base;


use Illuminate\Support\Arr;
use Larakit\Model\User;

class UserRole {
    static protected $roles   = [];
    static protected $names   = [];
    static protected $aliases = [];
    static protected $tree    = [];

    static function register($code, $name, $aliases = []) {
        self::$roles[$code] = $name;
        ksort(self::$roles);
        $aliases = (array)$aliases;
        foreach ($aliases as $alias) {
            self::$aliases[$alias] = $code;
        }
        $code = str_replace('.', '.items.', $code);
        Arr::set(self::$tree, $code . '.name', $name);
        Arr::set(self::$tree, $code . '.code', $code);
    }

    static function getCodeByAlias($alias) {
        return Arr::get(self::$aliases, $alias);
    }

    static function get($code = null, $delimiter = ' / ') {
        if ($code) {
            $full_name = self::getName($code, $delimiter);
            $code      = str_replace('.', '.items.', $code);

            return $full_name;
            //            return Arr::get(self::$tree, $code . '.name', $code);
        }

        return self::$roles;
    }

    static function getName($code, $delimiter = ' / ') {
        $ret = [];
        $key = '';
        $ex  = explode('.', $code);
        while ($k = array_shift($ex)) {
            $key .= $k;
            $ret[] = Arr::get(self::$tree, $key . '.name');
            $key .= '.items.';
        }

        return implode($delimiter, $ret);
    }

    static function tree() {
        return self::$tree;
    }

    static function children($code) {
        return Arr::get(str_replace('.', '.items.', $code) . '.items', self::$tree);
    }

    static function assign($user_id, $roles) {
        $_roles = [];
        foreach ((array)$roles as $role) {
            if (isset(self::$roles[$role])) {
                $_roles[] = new \Larakit\Model\UserRole([
                    'user_id' => $user_id,
                    'role'    => $role,
                ]);
            }
        }
        if ($user_id instanceof Model) {
            $user = $user_id;
        } else {
            $user = User::findOrFail($user_id);
        }

        $user->roles()->delete();
        if (count($_roles)) {
            $user->roles()->saveMany($_roles);
        }
    }
}