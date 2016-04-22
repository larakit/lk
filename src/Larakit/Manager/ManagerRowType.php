<?php

namespace Larakit\Manager;

use Illuminate\Support\Arr;

class ManagerRowType extends ManagerBase {

    /**
     * Регистрация компонента
     *
     * @param      $package
     * @param bool $dir
     */
    static function register($vendor, $entity, $row_name) {
        static::add(self::makeKey($vendor, $entity), $row_name);
    }

    static function makeKey($vendor, $entity) {
        return $vendor . '::' . $entity;;
    }

}