<?php
/**
 * Created by Larakit.
 * Link: http://github.com/larakit
 * User: Alexey Berdnikov
 * Date: 19.07.16
 * Time: 9:54
 */

namespace Larakit\CRUD;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait TraitEntity {

    static function entityExplode() {
        $delimitters = [
            '\Models\\',
            '\Http\Controllers\\',
            '\Controllers\\',
            '\FormFilters\\',
        ];
        $o           = get_called_class();
        foreach($delimitters as $delimitter) {
            if((false !== mb_strpos($o, $delimitter))) {
                $e = explode($delimitter, $o);

                return [
                    'vendor' => Arr::get($e, 0),
                    'entity' => Arr::get($e, 1),
                ];
            }
        }
        throw new \Exception('Не известный науке тип!');
    }

    static function getEntityPrefix() {
        return '';
    }

    static function getEntitySuffix() {
        return '';
    }

    static function getVendorStudly() {
        $v = Arr::get(self::entityExplode(), 'vendor');

        return Str::studly($v);
    }

    static function getVendorSnake($suffix = '::') {
        $v      = Arr::get(self::entityExplode(), 'vendor');
        $v      = str_replace('\\', '-', $v);
        $v      = mb_strtolower($v);
        $app_ns = trim(Str::snake(\App::getNamespace()), '\\');
        $app_ns = str_replace('\\', '-', $app_ns);
        if($app_ns == $v) {
            return '';
        }

        return mb_strtolower($v) . $suffix;
    }

    static function getEntityStudly() {
        return Str::studly(static::getEntitySnake());
    }

    static function getEntitySnake() {
        $str = Str::snake(Arr::get(self::entityExplode(), 'entity'));
        if(static::getEntityPrefix()) {
            $str = mb_substr($str, mb_strlen(Str::snake(static::getEntityPrefix())) + 1);
        }
        if(static::getEntitySuffix()) {
            $str = mb_substr($str, 0, 0 - mb_strlen(Str::snake(static::getEntitySuffix())) - 1);
        }

        return $str;
    }

    static function tableName() {
        return static::getVendorSnake('__') . Str::plural(static::getEntitySnake());
    }

    static function classModel() {
        return static::getVendorStudly() . '\Models\\' . self::getEntityStudly();
    }

    static function classFormFilter() {
        return static::getVendorStudly() . '\FormFilters\\' . self::getEntityStudly() . Str::studly(self::getEntityPrefix());
    }

    static function makeFormFilter() {
        $class = static::classFormFilter();

        return new $class(static::classModel());
    }

    static function classForm() {
        return static::getVendorStudly() . '\Forms\\' . self::getEntityStudly();
    }

    static function classAccessor() {
        return static::getVendorStudly() . '\Accessors\\' . self::getEntityStudly();
    }

    static function classAcl() {
        return static::getVendorStudly() . '\Acls\\' . self::getEntityStudly();
    }

    static function classValidator() {
        return static::getVendorStudly() . '\Validators\\' . self::getEntityStudly();
    }

    static function config($path, $default = null) {
        return config(static::getVendorSnake() . 'models/' . static::getEntitySnake() . '/' . $path, $default);
    }

    static function translate($context, $params = []) {
        return laralang(static::getVendorSnake() . 'models/' . static::getEntitySnake() . '/' . $context, $params);
    }

}