<?php

namespace Larakit\Manager;

use Illuminate\Support\Arr;

class ManagerSection extends ManagerBase {

    /**
     * Регистрация компонента
     *
     * @param      $package
     * @param bool $dir
     */
    static function register($section, $name) {
        static::set($name, $section);
    }

}