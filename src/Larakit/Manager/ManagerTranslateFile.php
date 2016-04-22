<?php

namespace Larakit\Manager;

use Illuminate\Support\Arr;

class ManagerTranslateFile extends ManagerBase {

    /**
     * Регистрация компонента
     *
     * @param      $package
     * @param bool $dir
     */
    static function register($file, $name) {
        static::set($name, $file);
    }

}